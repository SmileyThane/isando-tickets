<?php


namespace App\Repositories;

use App\ModelHasRole;
use App\Permission;
use App\Role;
use App\RoleHasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class RoleRepository
{
    public function replicate($roleId, $companyId): bool
    {
        $role = Role::find($roleId);
        if ($role->is_public === 1 &&
            !Role::query()->where(['name' => $role->name, 'company_id' => $companyId])->exists()
        ) {
            $newRole = $role->replicate()->fill([
                'company_id' => $companyId
            ]);
            $newRole->save();

            $roleHasPermission = RoleHasPermission::query()->where(['role_id' => $role->id])->get();

            $roleHasPermission->each(static function ($item, $key) use ($newRole) {
                $copy = $item->replicate();
                $copy->role_id = $newRole->id;
                $copy->save();
            });

            return true;
        }

        return false;
    }

    public function attach($modelId, $class, $roleId): bool
    {
        $newRole = new ModelHasRole();
        $newRole->model_id = $modelId;
        $newRole->model_type = $class;
        $newRole->role_id = $roleId;
        $newRole->save();
        return true;
    }

    public function detach($modelId, $class, $roleId): bool
    {
        $attachedRole = ModelHasRole::where([
            'model_id' => $modelId,
            'model_type' => $class,
            'role_id' => $roleId
        ])->first();
        $attachedRole->delete();
        return true;
    }

    public function updateRoles(Request $request): bool
    {
        ModelHasRole::where(['model_id' => $request->company_user_id,
            'model_type' => $request->model_type])->delete();
        foreach ($request->role_ids as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && !$role->company_id) {
                $correctRole = Role::query()
                    ->where('name', '=', $role->name)
                    ->where('company_id', '=', Auth::user()->employee->company_id)
                    ->first();

                if ($correctRole) {
                    $roleId = $correctRole->id;
                }
            }
            ModelHasRole::firstOrCreate(
                ['model_id' => $request->company_user_id,
                    'model_type' => $request->model_type,
                    'role_id' => $roleId]
            );
        }
        return true;
    }

    public function mergeRolesWithPermissions(array $data): array
    {
        $result = [];
        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                if ($value === true) {
                    $result[] = [
                        'permission_id' => $item['0']['id'],
                        'role_id' => $key
                    ];
                }
            }
        }

        RoleHasPermission::query()->truncate();
        RoleHasPermission::query()->insert($result);
        return $this->getRolesWithPermissions();
    }

    public function getRolesWithPermissions(array $data): array
    {
        $result = [];
        $roles = Role::query()->where(['company_id' => Auth::user()->employee->company_id])->get();

        if (isset($data['search'])) {
            $permissions = Permission::query()
                ->where('name', 'like', '%' . $data['search'] . '%')
                ->get();
        } else {
            $permissions = Permission::all();
        }

        foreach ($permissions as $key => $permission) {
            $result[$key] = new stdClass();
            $result[$key]->{0} = ['id' => $permission->id, 'name' => $permission->name];
            foreach ($roles as $role) {
                $result[$key]->{$role->id} = in_array($permission->id, $role->permissionIds(), true);
            }
        }
        return $result;
    }

    /**
     * Return all roles by user company id
     *
     * @param array $data
     * @param int $companyId
     * @return array
     */
    public function getAllByCompanyId(array $data, int $companyId): array
    {
        $roles = Role::query()->where('company_id', $companyId);

        if (isset($data['search'])) {
            $roles->where('name', 'like', '%' . $data['search'] . '%');
        }

        return $roles->get()->toArray();
    }
}
