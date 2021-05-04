<?php


namespace App\Repositories;

use App\Client;
use App\LimitationGroup;
use App\LimitationGroupHasModel;
use App\EmployeeLimitationGroup;

class LimitationGroupRepository
{
    public function create($name, $companyId, $limitationTypeId): bool
    {
        LimitationGroup::query()->create([
            'name' => $name,
            'company_id' => $companyId,
            'limitation_type_id' => $limitationTypeId
        ]);

        return true;
    }

    public function delete($id): bool
    {
        LimitationGroupHasModel::query()->where('limitation_group_id', $id)->delete();
        EmployeeLimitationGroup::query()->where('limitation_group_id', $id)->delete();
        LimitationGroup::query()->where('id', $id)->delete();

        return true;
    }

    public function addCompanyUser($companyUserIds, $limitationGroupId): bool
    {
        if ($limitationGroupId && $companyUserIds) {
            EmployeeLimitationGroup::where('limitation_group_id', $limitationGroupId)->delete();
            $companyUserData = [];
            foreach ($companyUserIds as $key => $companyUserId) {
                $companyUserData[$key] = ['company_user_id' => $companyUserId, 'limitation_group_id' => $limitationGroupId];
            }
            EmployeeLimitationGroup::query()->insert($companyUserData);
        }

        return true;
    }

    public function removeCompanyUser($id): bool
    {
        EmployeeLimitationGroup::query()->where('id', $id)->delete();

        return true;
    }

    public function addLimitationModel($modelIds, $limitationGroupId, $model = Client::class): bool
    {
        if ($modelIds && $limitationGroupId) {
            LimitationGroupHasModel::where(['limitation_group_id' => $limitationGroupId, 'model_type' => $model])->delete();
            $limitationData = [];
            foreach ($modelIds as $key => $limitationId) {
                $limitationData[$key] = [
                    'model_id' => $limitationId,
                    'limitation_group_id' => $limitationGroupId,
                    'model_type' => $model
                ];
            }
            LimitationGroupHasModel::query()->insert($limitationData);
        }

        return true;
    }

    public function removeModel($id): bool
    {
        LimitationGroupHasModel::query()->where('id', $id)->delete();

        return true;
    }
}
