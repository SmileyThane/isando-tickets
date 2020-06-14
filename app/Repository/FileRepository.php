<?php

namespace App\Repository;

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

    public function store($file, $model_id, $model_type)
    {

        try {
            $name = $file->getClientOriginalName();
        } catch (\Throwable $th) {
            $name = 'null';
        }

        $originalPath = 'files/original/' . Controller::getRandomString() .'/';
        $file->storeAs('/public/' . $originalPath, $name);
        $newFile = new File();
        $newFile->name = $name;
        $newFile->filepath = $originalPath;
        $newFile->model_id = $model_id;
        $newFile->model_type = $model_type;
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
