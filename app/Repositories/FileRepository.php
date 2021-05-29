<?php

namespace App\Repositories;

use App\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileRepository
{

    public function find($id)
    {
        $file = File::find($id);
        return $file;
    }

    public function store($file, $model_id, $model_type, $service_info = null)
    {
        if (is_string($file)) {
            $name = basename('/public/' . $file);
            $originalPath = str_replace($name, '', $file);
        } else {
            $name = $file->getClientOriginalName();
            $originalPath = 'files/original/' . Controller::getRandomString() . '/';
            $file->storeAs('/public/' . $originalPath, $name);
        }
        $newFile = new File();
        $newFile->name = $name;
        $newFile->filepath = $originalPath;
        $newFile->model_id = $model_id;
        $newFile->model_type = $model_type;
        $newFile->service_info = $service_info;
        $newFile->save();
        return $originalPath;
    }

    public function delete($id)
    {
        $file = File::find($id);
        $res = '/public/' . $file->filepath . $file->name;
        Storage::delete($res);
        $file->delete();
        return true;
    }


}
