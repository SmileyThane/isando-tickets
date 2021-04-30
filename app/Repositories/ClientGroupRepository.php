<?php


namespace App\Repositories;

use App\ClientGroup;
use App\ClientGroupHasClient;
use App\EmployeeClientGroup;

class ClientGroupRepository
{
    public function create($name, $companyId): bool
    {
        ClientGroup::query()->create([
            'name' => $name,
            'company_id' => $companyId
        ]);

        return true;
    }

    public function delete($id): bool
    {
        ClientGroupHasClient::query()->where('client_group_id', $id)->delete();
        EmployeeClientGroup::query()->where('client_group_id', $id)->delete();
        ClientGroup::query()->where('id', $id)->delete();

        return true;
    }

    public function addCompanyUser($companyUserIds, $clientGroupId): bool
    {
        if ($clientGroupId && $companyUserIds) {
            EmployeeClientGroup::where('client_group_id', $clientGroupId)->delete();
            $companyUserData = [];
            foreach ($companyUserIds as $key => $companyUserId) {
                $companyUserData[$key] = ['company_user_id' => $companyUserId, 'client_group_id' => $clientGroupId];
            }
            EmployeeClientGroup::query()->insert($companyUserData);
        }

        return true;

    }

    public function removeCompanyUser($id): bool
    {
        EmployeeClientGroup::query()->where('id', $id)->delete();

        return true;
    }

    public function addClient($clientIds, $clientGroupId): bool
    {
        if ($clientIds && $clientGroupId) {
            ClientGroupHasClient::where('client_group_id', $clientGroupId)->delete();
            $clientData = [];
            foreach ($clientIds as $key => $clientId) {
                $clientData[$key] = ['client_id' => $clientId, 'client_group_id' => $clientGroupId];
            }
            ClientGroupHasClient::query()->insert($clientData);
        }

        return true;
    }

    public function removeClient($id)
    {
        ClientGroupHasClient::query()->where('id', $id)->delete();

        return true;
    }
}
