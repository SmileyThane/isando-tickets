<?php


namespace App\Repositories;

use App\Address;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\CompanyUserNotification;
use App\Email;
use App\EmailType;
use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Notifications\ResetPasswordEmail;
use App\Permission;
use App\Phone;
use App\Settings;
use App\Social;
use App\User;
use App\UserNotificationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class UserRepository
{
    protected $emailRepo;

    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepo = $emailRepository;
    }

    public function validate($request, $new = true)
    {
        $params = [
            'name' => 'required',
            'password' => 'sometimes|min:8|confirmed',
        ];
        if ($new === true && $request['email'] !== '[no_email]') {
            $email = Email::where([
                'entity_type' => User::class,
                'email' => $request['email'],
            ])->first();
            $params['password'] = 'required|min:8|confirmed';
            $params['email'] = [
                'required',
                Rule::unique('emails')->ignore($email ? $email->entity_id : null)->where(function ($query) {
                    return $query->where('entity_type', User::class);
                }),
            ];
        }
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function find($id, $with = [])
    {
        if (Auth::user()->employee->hasPermissionId(Permission::ACTIVITY_READ_ACCESS)) {
            $with = array_merge(['employee.activities.type', 'employee.activities.client'], $with);
        }

        return User::withTrashed()->where('id', $id)
            ->with(array_merge(['phones.type', 'addresses.type', 'addresses.country', 'socials.type', 'emails', 'emails.type', 'emailSignatures', 'notificationStatuses', 'billing', 'ixarmaLink'], $with))
            ->first();
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->middle_name = $request->middle_name;
        $user->surname = $request->surname;
        $user->password = bcrypt($request->password);
        $user->is_active = $request->is_active;
        $user->language_id = $request->language_id ?? 1;
        $user->save();

        if ($request['email'] && $request['email'] !== '[no_email]') {
            $this->emailRepo->create($user->id, User::class, $request['email'], 1);
        }

        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->toArray());
        if (isset($request->password) && !empty($request->password)) {
            $user->password = bcrypt($request->password);
            $user->save();
        }
        return $user;
    }

    public function delete($id): bool
    {
        $result = false;
        $user = User::find($id);
        if ($user) {
            Address::where('entity_type', User::class)->where('entity_id', $id)->delete();
            Phone::where('entity_type', User::class)->where('entity_id', $id)->delete();
            Social::where('entity_type', User::class)->where('entity_id', $id)->delete();
            Email::where('entity_type', User::class)->where('entity_id', $id)->delete();
//            Settings::where('entity_type', User::class)->where('entity_id', $id)->delete();
//            EmailSignature::where('entity_type', User::class)->where('entity_id', $id)->delete();
//            UserNotificationStatus::where('user_id', $id)->delete();

            ClientCompanyUser::whereIn('company_user_id', CompanyUser::where('user_id', $id)->pluck('id')->toArray())->delete();
            CompanyUser::where('user_id', $id)->delete();

            $user->delete();
            $result = true;
        }
        return $result;
    }

    public function restoreDeleted($id): bool
    {
        $result = false;

        $user = User::withTrashed()->find($id);
        if ($user) {
            Address::withTrashed()->where('entity_type', User::class)->where('entity_id', $id)->restore();
            Phone::withTrashed()->where('entity_type', User::class)->where('entity_id', $id)->restore();
            Social::withTrashed()->where('entity_type', User::class)->where('entity_id', $id)->restore();
            Email::withTrashed()->where('entity_type', User::class)->where('entity_id', $id)->restore();
//            Settings::withTrashed()->where('entity_type', User::class)->where('entity_id', $id)->restore();
//            EmailSignature::withTrashed()->where('entity_type', User::class)->where('entity_id', $id)->restore();
//            UserNotificationStatus::withTrashed()->where('user_id', $id)->restore();

            ClientCompanyUser::withTrashed()->whereIn('company_user_id', CompanyUser::where('user_id', $id)->pluck('id')->toArray())->restore();
            CompanyUser::withTrashed()->where('user_id', $id)->restore();

            $user->restore();
            $result = true;
        }
        return $result;
    }

    public function changeIsActive(Request $request): bool
    {
        $result = false;
        try {
            $user = User::find($request->user_id);
            $user->is_active = $request->is_active;
            $user->language_id = $request->language_id ?? $user->language_id;
            $user->save();
            $email = Email::find($request->email_id);

            if ($user->is_active === true) {
                $email->email_type = 1;
                $email->save();

                $companyId = $user->employee->companyData->id;
                $secondaryType = EmailType::where('entity_type', Company::class)->where('entity_id', $companyId)->first();
                if (!$secondaryType) {
                    $companyId = Auth::user()->employee->companyData->id;
                    $secondaryType = EmailType::where('entity_type', Company::class)->where('entity_id', $companyId)->first();
                }
                Email::where('id', '<>', $email->id)->where('email_type', 1)->where('entity_type', $email->entity_type)->where('entity_id', $email->entity_id)->update(['email_type' => $secondaryType ? $secondaryType->id : null]);

                $this->sendInvite($user->fresh());
            }

            $result = true;
        } catch (Throwable $throwable) {
            Log::error($throwable);
        }
        return $result;
    }

    public function sendInvite($user, $originalSender = false, $password = null): bool
    {
        $from = $originalSender ? Config::get('mail.from.name') : $user->employee->companyData->name;
        try {
            if ($password === null) {
                $password = Controller::getRandomString();
                $user->password = bcrypt($password);
                $user->save();
            }
            $user->notify(new RegularInviteEmail(
                $from,
                $user->title,
                $user->full_name,
                $user->email,
                $password,
                $user->language->short_code
            ));
        } catch (Throwable $throwable) {
            Log::error($throwable);
            //hack for broken notification system
            return false;
        }
        return true;
    }

    public function getSettings($userId = null)
    {
        $userId = $userId ?? Auth::user()->id;
        $settings = Settings::firstOrCreate([
            'entity_id' => $userId,
            'entity_type' => User::class
        ], [
            'data' => []
        ]);
        return $settings->data;
    }

    public function updateSettings(Request $request, $userId = null)
    {
        $userId = $userId ?? Auth::user()->id;
        $settings = Settings::firstOrCreate([
            'entity_id' => $userId,
            'entity_type' => User::class
        ], [
            'data' => []
        ]);
        $data = $settings->data;

        if ($request->has('theme_fg_color')) {
            $data['theme_fg_color'] = $request->theme_fg_color;
        }

        if ($request->has('theme_bg_color')) {
            $data['theme_bg_color'] = $request->theme_bg_color;
        }

        $settings->data = $data;
        $settings->save();

        return $settings->data;
    }

    public function resetPassword($email)
    {
        $email = Email::where('entity_type', User::class)->where('email', $email)->first();
        if (!$email) {
            return false;
        }

        $user = User::find($email->entity_id);
        if (!$user) {
            return false;
        }

        $from = $user->employee->companyData->name;
        try {
            $password = Controller::getRandomString();
            $user->password = bcrypt($password);
            $user->save();
            $user->notify(new ResetPasswordEmail($from, $user->title, $user->full_name, null, $user->email, $password, $user->language->short_code));
        } catch (Throwable $throwable) {
            Log::error($throwable);
            //hack for broken notification system
            return false;
        }
        return true;
    }

    public function setNotificationStatuses($id, $notificationStatuses)
    {
        UserNotificationStatus::where('user_id', $id)->whereNotIn('status', $notificationStatuses)->delete();

        foreach ($notificationStatuses as $status) {
            UserNotificationStatus::firstOrCreate([
                'user_id' => $id,
                'status' => $status
            ]);
        }

        return true;
    }

    public function updateAvatar(Request $request, $userId = null)
    {
        $userId = $request->user_id ?? Auth::user()->id;
        $user = User::findOrFail($userId);

        if (!Storage::exists('public/avatars')) {
            Storage::makeDirectory('public/avatars');
        }
        $file = $request->file('avatar')->storeAs('public/avatars', $userId . '-' . time() . '.' . $request->file('avatar')->extension());
        $user->avatar_url = Storage::url($file);
        $user->save();
        return $user;
    }
}
