<?php


namespace App\Repository;


use App\ClientCompanyUser;
use App\Company;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyRepository
{

    public function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'company_number' => 'required|unique:companies',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasRole(Role::COMPANY_CLIENT)) {
            $company = null;
        } else {
            $company = Company::find($id ?? Auth::user()->employee->company_id);
        }
        return $company;
    }

    public function create(Request $request)
    {
        $company = new Company();
        $company->name = $request->company_name;
        $company->company_number = $request->company_number;
        $company->domain_hash = rand(0, 99999999);
        $company->photo = $request->photo;
        $company->description = $request->description;
        $company->registration_date = $request->registration_date ?? now();
        $company->is_validated = $request->is_validated;
        $company->save();
        return $company;
    }

    public function update(Request $request, $id)
    {
        $company = Company::find($id);
        foreach ($request->all() as $param => $value) {
            $company->$param = $value;
        }
        $company->save();
        return $company;
    }

    public function delete($id)
    {
        $result = false;
        $company = Company::find($id);
        if ($company) {
            $company->delete();
            $result = true;
        }
        return $result;
    }


}
