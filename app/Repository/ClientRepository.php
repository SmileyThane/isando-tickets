<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientRepository
{

    protected $companyUserRepo;

    public function __construct(CompanyUserRepository $companyUserRepository)
    {
        $this->companyUserRepo = $companyUserRepository;
    }

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

    public function clients($request, $forceReturnAll = false)
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
            $clients->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', $request->search . '%')
                        ->orWhere('description', 'like', $request->search . '%');
                }
            );
        }
        $clients = $clients->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');
        if ($forceReturnAll) {
            return $clients->get();
        } else {
            return $clients->paginate($request->per_page ?? $clients->count());
        }
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
            ->with('teams', 'employees.employee.userData.phones.type', 'employees.employee.userData.addresses.type', 'clients', 'products.productData', 'phones.type', 'addresses.type', 'socials.type')
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
                'company_user_id' => $request->company_user_id]
        );
        return true;
    }

    public function detach($id): bool
    {
        $result = false;
        $client = ClientCompanyUser::find($id);
        if ($client) {
            if (ClientCompanyUser::where('company_user_id', $client->company_user_id)->count() === 1) {
                CompanyUser::where('id', $client->company_user_id)->delete();
            }
            $client->delete();
            $result = true;
        }
        return $result;
    }

    public function all($request)
    {
        $clients = $this->clients($request, true);
        $employee = $this->companyUserRepo->all($request, true);

        $result = $clients->merge($employee);

        $sort = function ($item, $key) use ($request) {
            $field = $request->sort_by ?? 'name';
            if ($item instanceof Client) {
                switch ($field) {
                    case 'id':
                        return $item->id;
                    case 'name':
                        return $item->name;
                    case 'description':
                        return $item->description;
                    case 'email':
                        return '';
                }
            } else {
                switch ($field) {
                    case 'id':
                        return $item->employee->userData->id;
                    case 'name':
                        return $item->employee->userData->full_name;
                    case 'description':
                        return $item->clients->name;
                    case 'email':
                        return $item->employee->userData->email;
                }
            }
        };

        if ($request->sort_val === 'false') {
            $result = $result->sortBy($sort);
        } else {
            $result = $result->sortByDesc($sort);
        }

        return $result->paginate($request->per_page ?? $result->count());
    }

}
