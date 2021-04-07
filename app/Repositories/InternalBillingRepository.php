<?php

namespace App\Repositories;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\InternalBilling;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternalBillingRepository
{
    public function index()
    {
        $employee = Auth::user()->employee;
        $entities = [];
        if ($employee->hasRoleId(Role::COMPANY_CLIENT)) {
            $clients = ClientCompanyUser::query()->where('company_user_id', $employee->id)->get();
            if ($clients) {
                foreach ($clients->pluck('client_id')->toArray() as $clientId) {
                    $entities[] = ['entity_type' => Client::class , 'entity_id' => $clientId];
                }
            }
        }
        if ($employee->hasRoleId(Role::HIGH_PRIVIGIES)) {
            $company = Company::find($employee->company_id);
            $entities[] = ['entity_type' => Company::class , 'entity_id' => $employee->company_id];
            $clientsIds = (new ClientRepository())->getRecursiveChildClientIds($company->clients);
            if ($clientsIds) {
                foreach ($clientsIds as $clientId) {
                    $entities[] = ['entity_type' => Client::class , 'entity_id' => $clientId];
                }
            }
        }
        $entities[] = ['entity_type' => User::class , 'entity_id' => Auth::id()];

        $internalBilling = InternalBilling::query();
        if ($entities) {
            foreach ($entities as $entity) {
                $internalBilling->orwhere(function ($query) use ($entity) {
                    $query->where($entity);
                });
            }
            return $internalBilling->get();
        }
        return [];
    }

    public function find(int $id)
    {
        return InternalBilling::find($id);
    }

    public function create($name, $cost, $currencyId = null, $entityId = null, $entityType = null)
    {
        return InternalBilling::create([
            'name' => $name,
            'entity_id' => $entityId ?? Auth::id(),
            'entity_type' => $entityType ?? User::class,
            'cost' => $cost,
            'currency_id' => $currencyId ?? 1
        ]);
    }

    public function update($id, $name, $cost, $currencyId)
    {
        $internalBilling = InternalBilling::find($id);
        if ($internalBilling) {
            $internalBilling->update([
                'name' => $name,
                'cost' => $cost,
                'currency_id' => $currencyId ?? 1
            ]);
        }

        return $internalBilling;
    }

    public function delete($id): bool
    {
        $internalBilling = InternalBilling::find($id);

        if ($internalBilling) {
            $internalBilling->delete();
            return true;
        }

        return false;
    }
}
