<?php


namespace App\Repository;


use App\ModelHasRole;

class RoleRepository
{
    public function attach($modelId, $class, $roleId)
    {
        $newRole = new ModelHasRole();
        $newRole->model_id = $modelId;
        $newRole->model_type = $class;
        $newRole->role_id = $roleId;
        $newRole->save();
        return true;
    }

    public function detach($modelId, $class, $roleId)
    {
        $attachedRole = ModelHasRole::where([
            'model_id' => $modelId,
            'model_type' => $class,
            'role_id' => $roleId
        ])->first();
        $attachedRole->delete();
        return true;
    }

}
