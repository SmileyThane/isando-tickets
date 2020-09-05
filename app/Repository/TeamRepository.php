<?php


namespace App\Repository;


use App\Company;
use App\Team;
use App\TeamCompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamRepository
{

    public function validate($request, $new = true)
    {
        $params = [
            'team_name' => 'required',
            'team_description' => 'required',
        ];
        if ($new === true) {
            $params['owner_id'] = 'required';
            $params['owner_type'] = 'required';
        }
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        $companyId = Auth::user()->employee->company_id;
        $teamsIds = Company::find($companyId)->teams->pluck('id')->toArray();
        $teams = Team::whereIn('id', $teamsIds);
        if ($request->search !== '') {
            $teams->where(static function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        return $teams->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')
            ->paginate($request->per_page ?? $teams->count());
    }


    public function find($id)
    {
        return Team::where('id', $id)->with('employees.employee.userData')->first();
    }

    public function create(Request $request)
    {
        $team = new Team();
        $team->name = $request->team_name;
        $team->description = $request->team_description;
        $team->team_owner_id = $request->owner_id;
        $team->team_owner_type = $request->owner_type;
        $team->save();
        return $team;
    }

    public function update(Request $request, $id)
    {
        $team = Team::find($id);
        $team->name = $request->team_name;
        $team->description = $request->team_description;
        $team->save();
        return $team;
    }

    public function delete($id)
    {
        $result = false;
        $team = Team::find($id);
        if ($team) {
            $team->delete();
            $result = true;
        }
        return $result;
    }

    public function attach(Request $request)
    {
        $teamCompanyUser = TeamCompanyUser::firstOrCreate(
            ['team_id' => $request->team_id,
                'company_user_id' => $request->company_user_id]
        );
        return true;
    }

    public function detach(Request $request, $id)
    {
        $result = false;
        $teamCompanyUser = TeamCompanyUser::find($id);
        if ($teamCompanyUser) {
            $teamCompanyUser->delete();
            $result = true;
        }
        return $result;
    }

}
