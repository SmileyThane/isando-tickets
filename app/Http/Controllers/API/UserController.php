<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    public function find($id = null)
    {
        $id = $id ?? Auth::id();
        $user = $this->userRepo->find($id);
        return self::showResponse(true, $user);
    }

    public function update(Request $request)
    {
        $id = Auth::id();
        $success = false;
        $result = null;
        $isValid = $this->userRepo->validate($request, false);
        if ($isValid === true) {
            $result = $this->userRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result ?? $isValid);
    }
}
