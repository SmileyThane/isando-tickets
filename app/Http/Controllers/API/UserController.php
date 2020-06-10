<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    public function find($id = null) {
        $id = $id ?? Auth::id();
        $user = $this->userRepo->find($id);
        return self::showResponse(true, $user);
    }

    public function update() {
        $id = Auth::id();
        $user = $this->userRepo->update($id);
        return self::showResponse(true, $user);
    }
}
