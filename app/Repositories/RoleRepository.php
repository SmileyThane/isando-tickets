<?php


namespace App\Repositories;


use App\ModelHasRole;
use Illuminate\Http\Request;

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


    public function updateRoles(Request $request)
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

}
