<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientRepository
{

    public function validate($request, $new = true)
    {
        $params = [
            'client_name' => 'required',
            'client_description' => 'required',
        ];
        if ($new === true) {
            $params['supplier_id'] = 'required';
            $params['supplier_type'] = 'required';
        }
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $clients = ClientCompanyUser::where('company_user_id')->clients()->paginate();
        } else {
            $clients = Company::find($companyId)->clients()->paginate();
        }
        return $clients;
    }

    public function find($id)
    {
        return Client::where('id', $id)->with('teams', 'employees')->first();
    }

    public function create(Request $request)
    {
        $client = new Client();
        $client->name = $request->client_name;
        $client->description = $request->client_description;
        $client->photo = $request->photo;
        $client->supplier_id = $request->supplier_id;
        $client->supplier_type = $request->supplier_type;
        $client->save();
        return $client;
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);
        $client->name = $request->client_name;
        $client->description = $request->client_description;
        $client->photo = $request->photo;
        $client->save();
        return $client;
    }

    public function delete($id)
    {
        $result = false;
        $client = Client::find($id);
        if ($client) {
            $client->delete();
            $result = true;
        }
        return $result;
    }

    public function attach(Request $request)
    {
        $clientCompanyUser = ClientCompanyUser::firstOrCreate(
            ['client_id' => $request->client_id],
            ['company_user_id' => $request->company_user_id]
        );
        return true;
    }


}
