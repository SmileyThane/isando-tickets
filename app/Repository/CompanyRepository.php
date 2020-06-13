<?php


namespace App\Repository;


use App\Company;
use App\CompanyProduct;
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
            $company = Company::where('id', $id ?? Auth::user()->employee->company_id)->with('employees.userData', 'clients', 'teams')->first();
        }
        return $company;
    }

    public function create(Request $request)
    {
        $company = new Company();
        $company->name = $request->company_name;
        $company->company_number = $request->company_number;
        $company->domain_hash = random_int(0, 99999999);
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
        //modify it
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

    public function attachProduct(Request $request)
    {
        CompanyProduct::firstOrCreate(
            ['company_id' => $request->company_id,
            'product_id' => $request->product_id]
        );
        return true;
    }

    public function detachProduct($id)
    {
        $result = false;
        $client = CompanyProduct::find($id);
        if ($client) {
            $client->delete();
            $result = true;
        }
        return $result;
    }


}
