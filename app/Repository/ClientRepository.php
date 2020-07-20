<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientRepository
{

    public function validate($request, $new = true)
    {
        $params = [
            'client_name' => 'required',
            'client_description' => 'required',
        ];
        if ($new === true) {
            $params['supplier_type'] = 'required';
            $params['supplier_id'] = [
                'required',
                Rule::unique('clients')->where(function ($query) use ($request) {
                    return $query->where('name', $request['client_name'])
                        ->where('supplier_type', $request['supplier_type']);
                }),
            ];
        }
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all()
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $clients = ClientCompanyUser::where('company_user_id', $employee->id)->first()->clients()->paginate(1000);
        } else {
            $clients = Company::find($companyId)->clients()->paginate(1000);
        }
        return $clients;
    }

    public function suppliers()
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        $company = Company::find($companyId);
        $suppliers[] = ['name' => $company->name, 'item' => [Company::class => $companyId]];
        if (!$employee->hasRole(Role::COMPANY_CLIENT)) {
            $clients = $company->clients;
        } else {
            $clients = ClientCompanyUser::where('company_user_id', $employee->id)->first()->clients()->paginate(1000);
        }
        foreach ($clients as $client) {
            $suppliers[] = ['name' => $client->name, 'item' => [Client::class => $client->id]];
        }
        return $suppliers;
    }

    public function find($id)
    {
        return Client::where('id', $id)->with('teams', 'employees.employee.userData', 'clients', 'phones.type', 'addresses.type', 'socials.type')->first();
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
        ClientCompanyUser::firstOrCreate(
            ['client_id' => $request->client_id,
                'company_user_id' => $request->company_user_id]
        );
        return true;
    }

    public function detach($id)
    {
        $result = false;
        $client = ClientCompanyUser::find($id);
        if ($client) {
            $client->delete();
            $result = true;
        }
        return $result;
    }


}
