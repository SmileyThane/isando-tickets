<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
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
        return Company::find($companyId)->teams()->paginate();
    }


    public function find($id)
    {
        return Team::where('id', $id)->with('employees')->first();
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
        $clientCompanyUser = TeamCompanyUser::firstOrCreate(
            ['team_id' => $request->team_id],
            ['company_user_id' => $request->company_user_id]
        );
        return true;
    }

}
