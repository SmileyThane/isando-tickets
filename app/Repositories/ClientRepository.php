<?php

namespace App\Repositories;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\Email;
use App\LimitationGroup;
use App\LimitationGroupHasModel;
use App\Permission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class ClientRepository
{
    public function validate($request, $new = true)
    {
        $params = [
            'client_name' => 'required',
            'number' => 'unique:clients',
            // 'client_description' => 'required',
        ];
        if ($new === true) {
            $params['supplier_type'] = 'required';
            $params['supplier_id'] = [
                'required',
//                Rule::unique('clients')->where(function ($query) use ($request) {
//                    return $query->where('name', $request['client_name'])
//                        ->where('supplier_type', $request['supplier_type']);
//                }),
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
        if ($request->search) {
            $request['page'] = 1;
        }
        $clients = $this->getClients($request);
        $childClientIds = $this->getRecursiveChildClientIds($clients->get());
        $clients = $clients->orWhereIn('id', $childClientIds)
            ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');
        return $clients->paginate($request->per_page ?? $clients->count());
    }

    public function getClients($request)
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $clients = Client::where('id', $clientCompanyUser->client_id);
        } elseif ($employee->hasPermissionId([Permission::CLIENT_GROUPS_DEPENDENCY])) {
            $assignedClients = [];
            $clientGroups = (new LimitationGroupRepository())->getAssignedLimitationGroupByModel(Client::class);
            if ($clientGroups) {
                $assignedClients = LimitationGroupHasModel::query()
                    ->whereIn('limitation_group_id', $clientGroups->pluck('id')->toArray())
                    ->get();
            }
            $clients = Client::whereIn('id', $assignedClients->pluck('model_id'));
        } else {
            $clients = Client::where(['supplier_type' => Company::class, 'supplier_id' => $companyId]);
        }

        if ($request->search) {
            $clients->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('short_name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );
        }
        return $clients;
    }

    public function getRecursiveChildClientIds($clientsArray): array
    {
        $clientIds = [];
        foreach ($clientsArray as $client) {
            if ($client->has('clients') && count($client->clients) > 0) {
                $clientIds[] = $client->id;
                $childClientIds = $this->getRecursiveChildClientIds($client->clients);
                $clientIds = array_merge($clientIds, $childClientIds);
            } else {
                $clientIds[] = $client->id;
            }
        }
        return $clientIds;
    }

    public function relatedClients($request, int $id)
    {
        $client = Client::where('id', $id)->with('clients')->first();
        if ($client->clients) {
            $childClientIds = $this->getRecursiveChildClientIds($client->clients);

            $clients = Client::whereIn('id', $childClientIds)->with('customLicense')
                ->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');

            return $clients->get();
        }
        return [];
    }

    public function suppliers()
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        $company = Company::find($companyId);
        $suppliers[] = ['name' => $company->name, 'item' => [Company::class => $companyId]];
        if (!$employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $childClientIds = $this->getRecursiveChildClientIds($company->clients);
            $clients = Client::query()->whereIn('id', $childClientIds)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $clientsArray = ClientCompanyUser::query()->where('company_user_id', $employee->id)->first()->clients();
            $clients = $clientsArray
                ->orderBy('name', 'asc')
                ->paginate($clientsArray->count());

        }
        foreach ($clients as $client) {
            $suppliers[] = ['name' => $client->name, 'item' => [Client::class => $client->id]];
        }

        return $suppliers;
    }

    public function find($id)
    {
        return Client::where('id', $id)
            ->with(
                'teams',
                'employees.employee.userData.phones.type',
                'employees.employee.userData.addresses.type',
                'employees.employee.userData.addresses.country',
                'employees.employee.userData.emails.type',
                'clients',
                'products.productData',
                'phones.type',
                'addresses.type',
                'billing',
                'addresses.country',
                'socials.type',
                'emails.type'
            )
            ->first();
    }

    public function create(Request $request): Client
    {
        $client = new Client();
        $client->name = $request->client_name;
        $client->description = $request->client_description;
        $client->photo = $request->photo;
        $client->number = $request->number;
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
        $client->number = $request->number;
        $client->short_name = $request->short_name;
        $client->photo = $request->photo;
        $client->save();
        return $client;
    }

    public function delete($id): bool
    {
        $result = false;
        $client = Client::find($id);
        if ($client) {
            ClientCompanyUser::where('client_id', $id)->delete();
            $client->delete();
            $result = true;
            if ($client->customLicense !== null) {
                $users = (new CustomLicenseRepository())->getUsers($client->customLicense->remote_client_id);
                foreach ($users->entities as $user) {
                    (new CustomLicenseRepository())
                        ->unassignFromIxarmaCompany($user->id);
                }
            }
        }

        return $result;
    }

    public function attach(Request $request)
    {
        Log::info('attached_c_cu_d:' . $request->client_id . '_' . $request->company_user_id . '_' . $request->description);
        return ClientCompanyUser::firstOrCreate(
            [
                'client_id' => $request->client_id,
                'company_user_id' => $request->company_user_id
            ],
            ['description' => $request->description]
        );
    }

    public function updateDescription(Request $request)
    {
        $clientUser = ClientCompanyUser::where([
            'client_id' => $request->client_id,
            'company_user_id' => $request->company_user_id
        ])->first();
        if ($clientUser) {
            $clientUser->description = $request->description;
            $clientUser->save();
        }
        return $clientUser;
    }

    public function detach($id): bool
    {
        $result = false;
        $client = ClientCompanyUser::find($id);
        if ($client) {
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
        if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $clients = Client::where('id', $clientCompanyUser->client_id);
        } elseif ($employee->hasPermissionId([Permission::CLIENT_GROUPS_DEPENDENCY])) {
            $assignedClients = [];
            $clientGroups = LimitationGroup::query()
                ->whereHas('employees', static function ($query) {
                    $query->where('company_user_id', Auth::user()->employee->id);
                })->whereHas('type', static function ($query) {
                    $query->where('model', Client::class);
                })->get();
            if ($clientGroups) {
                $assignedClients = LimitationGroupHasModel::query()
                    ->whereIn('limitation_group_id', $clientGroups->pluck('id')->toArray())
                    ->get();
            }
            $clients = Client::whereIn('id', $assignedClients->pluck('model_id'));
            $clientIds = $assignedClients->pluck('model_id')->toArray();
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
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );

            $clientCompanyUsers->whereHas(
                'employee.userData',
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('surname', 'like', '%' . $request->search . '%');
                }
            );
        }
        $clients = $clients->with(['phones', 'phones.type', 'addresses', 'addresses.type', 'addresses.country'])->get();

        $clientCompanyUsers = $clientCompanyUsers->with(['employee.userData' => function ($query) {
            $query->with(['phones', 'phones.type', 'addresses', 'addresses.type', 'addresses.country']);
        }])->whereHas('employee.userData')->get();

        $result = $clients->merge($clientCompanyUsers);
        $result = $result->unique(function ($item) {
            if ($item instanceof Client) {
                return 'C' . $item->id;
            } else {
                return 'U' . $item->employee->userData->id;
            }
        });

        $orderFunc = static function ($item) use ($orderBy) {
            if ($item instanceof Client) {
                switch ($orderBy) {
                    case 'id':
                        return $item->id;
                    case 'name':
                        return mb_strtolower($item->name);
                    case 'email':
                    case 'emails':
                        $email = $item->contact_email;
                        return $email ? mb_strtolower($email->email) : '';
                    case 'phone':
                    case 'phones':
                        $phone = $item->contact_phone;
                        return $phone ? mb_strtolower($phone->phone) : '';
                    case 'city':
                        if ($item->addresses->isNotEmpty()) {
                            return mb_strtolower($item->addresses->first()->city);
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
                        return mb_strtolower($item->employee->userData->full_name);
                    case 'email':
                    case 'emails':
                        $email = $item->employee->userData->contact_email;
                        return $email ? mb_strtolower($email->email) : '';
                    case 'phone':
                    case 'phones':
                        $phone = $item->employee->userData->contact_phone;
                        return $phone ? mb_strtolower($phone->phone) : '';
                    case 'city':
                        if ($item->employee->userData->addresses->isNotEmpty()) {
                            return mb_strtolower($item->employee->userData->addresses->first()->city);
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
        } catch (Throwable $th) {
            Log::error($th);
        }
        return $result;
    }

    public function getClientsAsRecipientsTree(): array
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $clients = Client::where('id', $clientCompanyUser->client_id);
            $company = $clientCompanyUser->clients()->with('employees.employee.userData');
        } else {
            $clients = Client::where(['supplier_type' => Company::class, 'supplier_id' => $companyId]);
            $company = Company::where('id', $companyId);
        }

        $clients = $clients->with('employees.employee.userData.emails.type', 'clients', 'emails.type')->orderBy('name', 'asc')->get();

        $company = $company->with(['employees' => function ($query) {
            $result = $query->whereDoesntHave('assignedToClients')->where('is_clientable', false);
            if (Auth::user()->employee->hasPermissionId(
                [
                    Permission::EMPLOYEE_CLIENT_ACCESS,
                    Permission::EMPLOYEE_USER_ACCESS
                ]
            )) {
                $result->where('user_id', Auth::id());
            }
            return $result->get();
        }, 'employees.userData.emails.type', 'emails.type'])->first();

        $clients = $clients->prepend($company);

        $results = [];
        foreach ($clients as $client) {
            $clientData = [
                'id' => $client->id,
                'entity_type' => Client::class,
                'entity_id' => $client->id,
                'name' => $client->name,
                'children' => []
            ];
            foreach ($client->emails as $email) {
                if (filter_var($email->email, FILTER_VALIDATE_EMAIL)) {
                    array_push($clientData['children'], [
                        'id' => $clientData['id'] . '-0-' . $email->id,
                        'entity_type' => Email::class,
                        'entity_id' => $email->id,
                        'name' => $email->email,
                        'type' => $email->type
                    ]);
                }
            }

            foreach ($client->employees as $employee) {
                if ($employee->employee) {
                    $employee = $employee->employee;
                }
                if ($employee->userData !== null) {
                    $employeeData = [
                        'id' => $clientData['id'] . '-' . $employee->userData->id,
                        'entity_type' => User::class,
                        'entity_id' => $employee->userData->id,
                        'name' => $employee->userData->full_name,
                        'children' => []
                    ];
                    foreach ($employee->userData->emails as $email) {
                        if (filter_var($email->email, FILTER_VALIDATE_EMAIL)) {
                            array_push($employeeData['children'], [
                                'id' => $employeeData['id'] . '-' . $email->id,
                                'entity_type' => Email::class,
                                'entity_id' => $email->id,
                                'name' => $email->email,
                                'type' => $email->type
                            ]);
                        }
                    }
                    if ($employeeData['children']) {
                        array_push($clientData['children'], $employeeData);
                    }
                }

            }

            if ($clientData['children']) {
                array_push($results, $clientData);
            }
        }
        return $results;
    }

    public function updateLogo(Request $request, $clientId)
    {
        $client = Client::findOrFail($clientId);

        if (!Storage::exists('public/logos')) {
            Storage::makeDirectory('public/logos');
        }
        $file = $request->file('logo')->storeAs('public/logos', 'client-' . $clientId . '-' . time() . '.' . $request->file('logo')->extension());
        $client->logo_url = Storage::url($file);
        $client->save();
        return $client;
    }
}
