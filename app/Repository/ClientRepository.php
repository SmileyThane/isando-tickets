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

    public function clients($request)
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $clients = Client::where('id', $clientCompanyUser->client_id);
        } else {
            $clients = Client::where(['supplier_type' => Company::class, 'supplier_id' => $companyId]);
        }
        if ($request->search) {
            $request['page'] = 1;
            $clients->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );
        }
        $clients = $clients->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');
        return $clients->paginate($request->per_page ?? $clients->count());
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
        return Client::where('id', $id)
            ->with('teams', 'employees.employee.userData.phones.type', 'employees.employee.userData.addresses.type', 'employees.employee.userData.addresses.country', 'employees.employee.userData.emails.type', 'clients', 'products.productData', 'phones.type', 'addresses.type', 'addresses.country', 'socials.type', 'emails.type')
            ->first();
    }

    public function create(Request $request): Client
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

    public function delete($id): bool
    {
        $result = false;
        $client = Client::find($id);
        if ($client) {
            $client->delete();
            $result = true;
        }
        return $result;
    }

    public function attach(Request $request): bool
    {
        ClientCompanyUser::firstOrCreate(
            ['client_id' => $request->client_id,
                'company_user_id' => $request->company_user_id
            ],
            ['description' => $request->description]
        );
        return true;
    }

    public function detach($id): bool
    {
        $result = false;
        $client = ClientCompanyUser::find($id);
        if ($client) {
//            if (ClientCompanyUser::where('company_user_id', $client->company_user_id)->count() === 1) {
//                (new CompanyUserRepository(new UserRepository(new EmailRepository()),new RoleRepository()))->delete($client->company_user_id);
//            }
            $client->delete();
            $result = true;
        }
        return $result;
    }

    public function all($request)
    {
        $orderBy = $request->sort_by ?? 'name';
        $request->sort_by = null;

        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        $clientIds = [];
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $clients = Client::where('id', $clientCompanyUser->client_id);
        } else {
            $clients = Client::where(['supplier_type' => Company::class, 'supplier_id' => $companyId]);
            $clientIds = Client::where([
                'supplier_type' => Company::class,
                'supplier_id' => $employee->company_id
            ])->get()->pluck('id')->toArray();
        }

        $clientCompanyUsers = ClientCompanyUser::whereIn('client_id', $clientIds);
        if ($request->search) {
            $clients->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', $request->search . '%')
                        ->orWhere('description', 'like', $request->search . '%');
                }
            );

            $clientCompanyUsers->whereHas(
                'employee.userData',
                function ($query) use ($request) {
                    $query->where('name', 'like', $request->search . '%')
                        ->orWhere('surname', 'like', $request->search . '%')
                        ->orWhere('email', 'like', $request->search . '%');
                }
            );
        }
        $clients = $clients->with(['phones', 'phones.type', 'addresses', 'addresses.type', 'addresses.country'])->get();

        $clientCompanyUsers = $clientCompanyUsers->with(['employee.userData' => function ($query) use ($request) {
            $query->with(['phones', 'phones.type', 'addresses', 'addresses.type', 'addresses.country']);
        }])->get();

        $result = $clients->merge($clientCompanyUsers);
        $result = $result->unique(function ($item) {
            if ($item instanceof Client) {
                return 'C' . $item->id;
            } else {
                return 'U' . $item->employee->userData->id;
            }
        });

        $orderFunc = function ($item, $key) use ($orderBy) {
            if ($item instanceof Client) {
                switch ($orderBy) {
                    case 'id':
                        return $item->id;
                    case 'name':
                        return $item->name;
                    case 'email':
                        $email = $item->contact_email;
                        return $email ? $email->email : '';
                    case 'phone':
                        $phone = $item->contact_phone;
                        return $phone ? $phone->phone : '';
                    case 'city':
                        if ($item->addresses->isNotEmpty()) {
                            return $item->addresses->first()->city;
                        } else {
                            return '';
                        }
                    case 'country':
                        if ($item->addresses->isNotEmpty()) {
                            return $item->addresses->first()->country ? $item->addresses->first()->country->iso_3166_2 : '';
                        } else {
                            return '';
                        }
                    case 'is_active':
                        return $item->is_active ? 1 : 0;
                }
            } else {
                switch ($orderBy) {
                    case 'id':
                        return $item->employee->userData->id;
                    case 'name':
                        return $item->employee->userData->full_name;
                    case 'email':
                        $email = $item->employee->userData->contact_email;
                        return $email ? $email->email : '';
                    case 'phone':
                        $phone = $item->employee->userData->contact_phone;
                        return $phone ? $phone->phone : '';
                    case 'city':
                        if ($item->employee->userData->addresses->isNotEmpty()) {
                            return $item->employee->userData->addresses->first()->city;
                        } else {
                            return '';
                        }
                    case 'country':
                        if ($item->employee->userData->addresses->isNotEmpty()) {
                            return $item->employee->userData->addresses->first()->country ? $item->employee->userData->addresses->first()->country->iso_3166_2 : '';
                        } else {
                            return '';
                        }
                    case 'is_active':
                        return $item->employee->userData->is_active ? 1 : 0;
                }
            }
        };

        if ($request->sort_val === 'false') {
            $result = $result->sortBy($orderFunc);
        } else {
            $result = $result->sortByDesc($orderFunc);
        }

        return $result->paginate($request->per_page ?? $result->count());
    }


    public function changeIsActive(Request $request): bool
    {
        $result = false;
        try {
            $client = Client::find($request->id);
            $client->is_active = $request->is_active;
            $client->save();
            $result = true;
        } catch (\Throwable $th) {
        }
        return $result;
    }
}
