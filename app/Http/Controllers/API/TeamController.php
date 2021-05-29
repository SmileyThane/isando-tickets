<?php


namespace App\Http\Controllers\API;

use App\Company;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::user()->employee->hasPermissionId(Permission::TEAM_READ_ACCESS)) {
            return self::showResponse(true, $this->teamRepo->all($request));
        }

        return self::showResponse(false);
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TEAM_READ_ACCESS)) {
            return self::showResponse(true, $this->teamRepo->find($id));
        }

        return self::showResponse(false);
    }

    public function create(Request $request)
    {
        if (!array_key_exists('owner_id', $request->all())) {
            $request['owner_id'] = Auth::user()->employee->company_id;
            $request['owner_type'] = Company::class;
        }

        $isValid = $this->teamRepo->validate($request);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::TEAM_WRITE_ACCESS);

        if ($isValid === true && $hasAccess) {
            return self::showResponse(true, $this->teamRepo->create($request));
        }

        return self::showResponse(false);
    }

    public function update(Request $request, $id)
    {
        $isValid = $this->teamRepo->validate($request, false);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::TEAM_WRITE_ACCESS);

        if ($isValid === true && $hasAccess) {
            return self::showResponse(true, $this->teamRepo->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function delete($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TEAM_DELETE_ACCESS)) {
            return self::showResponse($this->teamRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function attach(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TEAM_WRITE_ACCESS)) {
            return self::showResponse($this->teamRepo->attach($request));
        }

        return self::showResponse(false);
    }

    public function detach(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TEAM_WRITE_ACCESS)) {
            return self::showResponse($this->teamRepo->detach($request, $id));
        }

        return self::showResponse(false);
    }

    public function toggleAsManager(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::EDIT_TEAM_MANAGER_FLAG)) {
            return self::showResponse($this->teamRepo->toggleAsManager($request));
        }

        return self::showResponse(false);
    }
}
