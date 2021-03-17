<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\FileRepository;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $fileRepo;

    /**
     * Instantiate a new controller instance.
     * @param FileRepository $fileRepo
     */
    public function __construct(FileRepository $fileRepo)
    {
        $this->fileRepo = $fileRepo;
    }

    public function find($id)
    {
        $file = $this->fileRepo->find($id);
        return self::showResponse(true, $file);
    }

    public function add(Request $request)
    {
        $filepath = $this->fileRepo->store($request->file, $request->model_id, $request->model_type);
        return self::showResponse(true, $filepath);
    }

    public function delete($id)
    {
        $this->fileRepo->delete($id);
        return self::showResponse(true);
    }

}
