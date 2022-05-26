<?php


namespace App\Repositories;

use App\Client;
use App\EmployeeLimitationGroup;
use App\LimitationGroup;
use App\LimitationGroupHasModel;
use App\LimitationType;
use Illuminate\Support\Facades\Auth;

class LimitationGroupRepository
{
    public function create($name, $companyId, $limitationTypeId, $autoAssign): bool
    {
        LimitationGroup::query()->create([
            'name' => $name,
            'company_id' => $companyId,
            'limitation_type_id' => $limitationTypeId,
            'is_auto_assign_new_entities' => $autoAssign
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

    public function getAssignedLimitationGroupByModel($model)
    {
        return LimitationGroup::query()->whereHas(
            'employees',
            static function ($query) {
                $query->where('company_user_id', Auth::user()->employee->id);
            }
        )->whereHas(
            'type',
            static function ($query) use ($model) {
                $query->where('model', $model);
            }
        )->get();
    }

    public function limitationAutoAssignProcess($entity): bool
    {
        $type = LimitationType::query()->where('model', 'App\\' . class_basename($entity))->first();
        if ($type) {
            $limitationGroups = LimitationGroup::query()
                ->where('is_auto_assign_new_entities', true)
                ->where('limitation_type_id', $type->id)
                ->where('company_id', Auth::user()->employee->company_id)
                ->get();
            $insertion = [];
            foreach ($limitationGroups as $key => $limitationGroup) {
                $insertion[$key]['model_id'] = $entity->id;
                $insertion[$key]['model_type'] = $type->model;
                $insertion[$key]['limitation_group_id'] = $limitationGroup->id;

            }
            LimitationGroupHasModel::query()->insert($insertion);
        }

        return true;
    }
}
