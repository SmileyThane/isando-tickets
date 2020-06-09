<?php


namespace App\Repository;


use App\CompanyUser;
use Illuminate\Support\Facades\Validator;

class CompanyUserRepository
{

    public function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function find($id)
    {
        return CompanyUser::find($id);
    }

    public function create($companyId, $userId)
    {
        $companyUser = new CompanyUser();
        $companyUser->user_id = $userId;
        $companyUser->company_id = $companyId;
        $companyUser->save();
        return $companyUser;
    }

    public function delete($id)
    {
        $result = false;
        $companyUser = CompanyUser::find($id);
        if ($companyUser) {
            $companyUser->delete();
            $result = true;
        }
        return $result;
    }


}
