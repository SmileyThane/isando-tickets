<?php


namespace App\Repositories;


use App\ModelHasRole;
use App\Permission;
use App\Role;
use App\RoleHasPermission;
use Illuminate\Http\Request;
use stdClass;

class RoleRepository
{
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

    public function getRolesWithPermissions(): array
    {
        $result = [];
        $roles = Role::all();
        $permissions = Permission::all();

        foreach ($permissions as $key => $permission) {
            $result[$key] = new stdClass();
            $result[$key]->{0} = ['id' => $permission->id, 'name' => $permission->name];
            foreach ($roles as $role) {
                $result[$key]->{$role->id} = in_array($permission->id, $role->permissionIds(), true);
            }
        }
        return $result;
    }

}
