<?php


namespace App\Repository;


use App\ClientCompanyUser;
use App\Company;
use App\CompanyProduct;
use App\CompanyUser;
use App\CompanyUserNotification;
use App\ProductCategory;
use App\Role;
use App\Settings;
use App\TicketNotificationType;
use Illuminate\Database\Eloquent\Collection;
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
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $employee->id)->first();
            $company = $clientCompanyUser->clients()->with('employees.employee.userData');
        } else {
            $company = Company::where('id', $id ?? $employee->company_id);
        }
        if ($request->search !== '') {
            $company->where(
                function ($query) use ($request) {
                    $query->where('name', 'like', $request->search . '%')
                        ->orWhere('name', 'like', ' %' . $request->search . '%');
                    $query->orWhere('description', 'like', '%' . $request->search . '%');
                }
            );
        }
        return $id ? $company->with(['employees' => function ($query) {
            $result = $query->whereDoesntHave('assignedToClients')->where('is_clientable', false);
            if (Auth::user()->employee->hasAnyRole(Role::COMPANY_CLIENT, Role::USER)) {
                $result->where('user_id', Auth::id());
            }
            return $result->get();
        }, 'employees.userData', 'employees.userData.phones.type', 'employees.userData.addresses.type', 'employees.userData.emails.type', 'clients',
            'products.productData', 'teams', 'phones.type', 'addresses.type', 'addresses.country', 'socials.type', 'emails', 'emails.type'])
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

        if ($request->has('theme_color')) {
            $data['theme_color'] = $request->theme_color;
        }

        if ($request->has('override_user_theme')) {
            $data['override_user_theme'] = $request->override_user_theme;
        }

        $settings->data = $data;
        $settings->save();

        return $settings->data;
    }

    public function updatelogo(Request $request, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $company = Company::findOrFail($companyId);

        if (!Storage::exists('public/logos')) {
            Storage::makeDirectory('public/logos');
        }
        $file = $request->file('logo')->storeAs('public/logos', $companyId . '-' . time() . '.' . $extension = $request->file('logo')->extension());
        $company->logo_url = Storage::url($file);
        $company->save();
        return $company;
    }

    public function getNotifiedUsers(Request $request, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $employees = CompanyUser::where('company_id', $companyId);

        if (!empty($request->search)) {
            $request['page'] = 1;
            $employees->whereHas(
                'userData',
                function ($query) use ($request) {
                    $query->where('name', 'like', $request->search . '%')
                        ->orWhere('surname', 'like', $request->search . '%');
                }
            );
        }

        $employees = $employees->with('userData.emails.type')->get();
        $notifiedUsers = CompanyUserNotification::where('company_id', $companyId)->get();

        $result = new Collection();
        foreach ($employees as $employee) {
            $item = $employee->userData;
            $notifications_status = [];

            $notifications = $notifiedUsers->where('user_id', $employee->userData->id);
            foreach ($notifications as $notification) {
                array_push($notifications_status, $notification->ticket_notification_type_id);
            }
            $item->notifications_status = $notifications_status;

            $result->push($item);
        }

        $orderFunc = function ($item, $key) use ($request) {
            switch ($request->sort_by) {
                case 'id':
                    return $item->id;
                case 'name':
                    return mb_strtolower($item->name);
                case 'surname':
                    return mb_strtolower($item->surname);
                case 'contact_email':
                    $email = $item->contact_email;
                    return $email ? mb_strtolower($email->email) : '';
                case 'notifications_status':
                    if (empty($item->notifications_status)) {
                        return 0;
                    } elseif (in_array(TicketNotificationType::ALL, $item->notifications_status)) {
                        return 2;
                    } else {
                        return 1;
                    }
            }
        };

        if ($request->sort_val === 'false') {
            $result = $result->sortBy($orderFunc);
        } else {
            $result = $result->sortByDesc($orderFunc);
        }

        return $result->paginate($request->per_page ?? $result->count());
    }

    public function setNotifiedUser(Request $request, $companyId = null, $userId, $notificationTypes)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        CompanyUserNotification::where('company_id', $companyId)->where('user_id', $userId)->whereNotIn('ticket_notification_type_id', $notificationTypes)->delete();

        foreach ($notificationTypes as $notificationType) {
            CompanyUserNotification::firstOrCreate([
                'company_id' => $companyId,
                'user_id' => $userId,
                'ticket_notification_type_id' => $notificationType
            ]);
        }

        return true;
    }
}
