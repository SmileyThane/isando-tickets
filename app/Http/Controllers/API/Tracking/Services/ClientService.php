<?php

namespace App\Http\Controllers\API\Tracking\Services;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\Http\Controllers\API\Tracking\Filters\Clients\Projects;
use App\LimitationGroupHasModel;
use App\Permission;
use App\Repositories\LimitationGroupRepository;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;

class ClientService
{

    public function clients($request)
    {
        if ($request->search) {
            $request['page'] = 1;
        }
        $clients = $this->getClients($request);
        $clients = app(Pipeline::class)
            ->send($clients)
            ->through([
                Projects::class,
            ])
            ->thenReturn();
//        dd($clients->toSql(), $clients->getBindings());
        return $clients->paginate($request->per_page ?? $clients->count());
    }

    public function getClients($request)
    {
        $employee = Auth::user()->employee;
        $companyId = $employee->company_id;
        $clients = Client::query();
        $clients->where(function ($clients) use ($request, $employee, $companyId) {
            if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
                $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
                $clients->where('id', $clientCompanyUser->client_id);
            } elseif ($employee->hasPermissionId([Permission::CLIENT_GROUPS_DEPENDENCY])) {
                $assignedClients = [];
                $clientGroups = (new LimitationGroupRepository())->getAssignedLimitationGroupByModel(Client::class);
                if ($clientGroups) {
                    $assignedClients = LimitationGroupHasModel::query()
                        ->whereIn('limitation_group_id', $clientGroups->pluck('id')->toArray())
                        ->get();
                }
                $clients->whereIn('id', $assignedClients->pluck('model_id')->unique()->values()->all());
            } else {
                $clients->where(['supplier_type' => Company::class, 'supplier_id' => $companyId]);
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
            $childClientIds = $this->getRecursiveChildClientIds($clients->get());
            $clients->orWhereIn('id', $childClientIds);
        });
        return $clients->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc');
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
        return array_unique($clientIds);
    }
}
