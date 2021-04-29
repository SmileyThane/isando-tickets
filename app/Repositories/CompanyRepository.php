<?php

namespace App\Repositories;

use App\ClientCompanyUser;
use App\Company;
use App\CompanyProduct;
use App\CompanyUserNotification;
use App\Permission;
use App\ProductCategory;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $company = $clientCompanyUser->clients()->with('employees.employee.userData');
        } else {
            $company = Company::where('id', $id ?? $employee->company_id);
            $company->with('currency');
        }
        if ($request->search !== '') {
            $company->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('name', 'like', ' %' . $request->search . '%');
                    $query->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );
        }

        return $id ? $company->with(['employees' => function ($query) {
            $result = $query->whereDoesntHave('assignedToClients')->where('is_clientable', false);
            if (Auth::user()->employee->hasPermissionId([Permission::EMPLOYEE_CLIENT_ACCESS, Permission::EMPLOYEE_USER_ACCESS])) {
                $result->where('user_id', Auth::id());
            }
            return $result->get();
        }, 'employees.userData', 'employees.userData.phones.type', 'employees.userData.addresses.type',
            'employees.userData.emails.type', 'clients',
            'products.productData', 'teams', 'phones.type', 'addresses.type', 'addresses.country', 'socials.type',
            'emails', 'billing', 'emails.type'])
            ->first() :
            $company->orderBy($request->sort_by ?? 'id', $request->sort_val === 'false' ? 'asc' : 'desc')
                ->paginate($request->per_page ?? $company->count());
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

        if ($request->hasFile('logo')) {
            if (!Storage::exists('public/logos')) {
                Storage::makeDirectory('public/logos');
            }

            $file = $request->file('logo')->storeAs('public/logos', $company->id . '-' . time() . '.' . $extension = $request->file('logo')->extension());
            $company->logo_url = Storage::url($file);
            $company->save();
        }
        return $company;
    }

    public function update(Request $request, $id)
    {
        $company = Company::where('id', $id)->with('employees.userData', 'clients', 'teams')->first();
        $company->name = $request->name ?? $company->name;
        $company->company_number = $request->company_number ?? $company->company_number;
        $company->description = $request->description ?? $company->description;
        $company->registration_date = $request->registration_date ?? now();

        if ($request->has('currency_id')) {
            $company->currency_id = $request->get('currency_id');
        }

        //aliases
        $company->first_alias = $request->first_alias ?? $company->first_alias;
        $company->second_alias = $request->second_alias ?? $company->second_alias;

        if ($request->hasFile('logo')) {
            if (!Storage::exists('public/logos')) {
                Storage::makeDirectory('public/logos');
            }

            $file = $request->file('logo')->storeAs('public/logos', $company->id . '-' . time() . '.' . $extension = $request->file('logo')->extension());
            $company->logo_url = Storage::url($file);
        }

        $company->save();
        return $company;
    }

    public function delete($id)
    {
        $result = false;
        $company = Company::find($id);
        if ($company) {
            if ($company->logo_url) {
                Storage::delete('logos', $company->logo_url);
            }
            $company->delete();
            $result = true;
        }
        return $result;
    }

    public function attachProduct(Request $request): bool
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

    public function getCompanyLicense($companyId)
    {
        return Company::with('license')->where('id', '=', $companyId)->first();
    }

    public function attachProductCategory($name, $companyId = null, $parentId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        ProductCategory::firstOrCreate([
            'name' => $name,
            'company_id' => $companyId,
            'parent_id' => $parentId,
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

    public function getProductCategoriesTree($companyId = null, $showFullNames = false)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company->productCategories()->orderBy('name', 'ASC')->get()->toTree();
        }
        return $result;
    }

    public function getProductCategoriesFlat($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            return $result = $company->productCategories()->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();
        }
        return $result;
    }

    public function getSettings($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $settings = Settings::firstOrCreate([
            'entity_id' => $companyId,
            'entity_type' => Company::class
        ], [
            'data' => []
        ]);
        return $settings->data;
    }

    public function updateSettings(Request $request, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $settings = Settings::firstOrCreate([
            'entity_id' => $companyId,
            'entity_type' => Company::class
        ], [
            'data' => []
        ]);
        $data = $settings->data;

        if ($request->has('imezone')) {
            $data['timezone'] = $request->timezone;
        }
        if ($request->has('navbar_style')) {
            $data['navbar_style'] = $request->navbar_style;
        }

        if ($request->has('ticket_number_format')) {
            $data['ticket_number_format'] = $request->ticket_number_format;
        }

        if ($request->has('theme_fg_color')) {
            $data['theme_fg_color'] = $request->theme_fg_color;
        }

        if ($request->has('theme_bg_color')) {
            $data['theme_bg_color'] = $request->theme_bg_color;
        }

        if ($request->has('override_user_theme')) {
            $data['override_user_theme'] = $request->override_user_theme;
        }

        if ($request->has('employee_number_format')) {
            $data['employee_number_format'] = $request->employee_number_format;
        }

        $settings->data = $data;
        $settings->save();

        return $settings->data;
    }

    public function updateLogo(Request $request, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $company = Company::findOrFail($companyId);

        if (!Storage::exists('public/logos')) {
            Storage::makeDirectory('public/logos');
        }
        $file = $request->file('logo')->storeAs('public/logos', 'company-' . $companyId . '-' . time() . '.' . $request->file('logo')->extension());
        $company->logo_url = Storage::url($file);
        $company->save();
        return $company;
    }
}
