<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $userRepo;
    protected $teamRepo;

    public function __construct(UserRepository $userRepository, TeamRepository $teamRepository)
    {
        $this->userRepo = $userRepository;
        $this->teamRepo = $teamRepository;
    }

    public function get(Request $request)
    {
        $teams = $this->teamRepo->all($request);
        return self::showResponse(true, $teams);
    }

    public function find($id)
    {
        $team = $this->teamRepo->find($id);
        return self::showResponse(true, $team);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->teamRepo->validate($request);
        if ($result === true) {
            $result = $this->teamRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->teamRepo->validate($request, false);
        if ($result === true) {
            $result = $this->teamRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete(Request $request, $id)
    {
        $result = $this->teamRepo->delete($id);
        return self::showResponse($result);
    }

    public function attach(Request $request)
    {
        $result = $this->teamRepo->attach($request);
        return self::showResponse($result);
    }

    public function detach(Request $request, $id)
    {
        $result = $this->teamRepo->detach($request, $id);
        return self::showResponse($result);
    }
}
