<?php


namespace App\Repository;


use App\ClientCompanyUser;
use App\Company;
use App\CompanyProduct;
use App\CompanySettings;
use App\ProductCategory;
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

    public function find($request, $id)
    {
        $employee = Auth::user()->employee;
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $company = $clientCompanyUser->clients()->with('employees.employee.userData');
        } else {
            $company = Company::where('id', $id ?? $employee->company_id);
        }
        if ($request->search !== '') {
            $company->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );
        }
        return $id ? $company->with(['employees' => function ($query) {
            $result = $query->whereDoesntHave('assignedToClients');
            if (Auth::user()->employee->hasAnyRole(Role::COMPANY_CLIENT, Role::USER)) {
                $result->where('user_id', Auth::id());
            }
            return $result->get();
        }, 'employees.userData', 'employees.userData.phones.type', 'employees.userData.addresses.type', 'clients',
            'teams', 'phones.type', 'addresses.type', 'socials.type'])
            ->first() : $company->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')->paginate($request->per_page ?? $company->count());
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
        $company = Company::where('id', $id)->with('employees.userData', 'clients', 'teams')->first();
        $company->name = $request->name;
        $company->company_number = $request->company_number;
        $company->description = $request->description;
        $company->registration_date = $request->registration_date ?? now();
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


    public function attachProductCategory(Request $request)
    {
        ProductCategory::firstOrCreate([
            'name' => $request->name,
            'company_id' => $request->company_id,
            'parent_id' => $request->parent_id ?? null,
        ]);

        return true;
    }

    public function detachProductCategory($id)
    {
        $result = false;
        $category = ProductCategory::find($id);
        if ($category) {
            $category->delete();
            $result = true;
        }
        return $result;
    }

    public function getProductCategoriesTree($id, $showFullNames = false)
    {
        $result = [];
        $company = Company::find($id);
        if ($company) {
            $result = $company->productCategories()->orderBy('name', 'ASC')->get()->toTree();
        }
        return $result;
    }

    public function getProductCategoriesFlat($id)
    {
        $result = [];
        $company = Company::find($id);
        if ($company) {
            $result = $company->productCategories()->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

            foreach ($result as &$category) {
                $category->name = $category->getFullName();
            }
        }
        return $result;
    }

    public function getSettings($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $settings = CompanySettings::where('company_id', $companyId)->firstOrCreate();
        return $settings->data;
    }

    public function updateSettings($companyId = null, $newData)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $settings = CompanySettings::where('company_id', $companyId)->firstOrCreate();
        $data = $settings->data;
        if ($newData->timezone) {
            $data['timezone'] = $newData->timezone;
        }
        if ($newData->navbar_type) {
            $data['navbar_type'] = $newData->navbar_type;
        }
        $settings->data = $data;
        $settings->save();
        return true;
    }

}
