<?php


namespace App\Repository;


use App\Company;
use App\CompanyUserNotification;
use App\Email;
use App\EmailType;
use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Notifications\ResetPasswordEmail;
use App\UserNotificationStatus;
use App\Role;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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
            'password' => 'sometimes|min:8',
        ];
        if ($new === true && $request['email'] !== '[no_email]') {
            $email = Email::where([
                'entity_type' => User::class,
                'email' => $request['email'],
            ])->first();
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
        return  User::where('id', $id)->with(array_merge(['phones.type', 'addresses.type', 'addresses.country', 'socials.type', 'emails', 'emails.type', 'emailSignatures', 'notificationStatuses'], $with))->first();
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
            Email::where('entity_type', User::class)->where('entity_id', $id)->delete();

            $user->delete();
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

                $this->sendInvite($user->fresh(), Role::COMPANY_CLIENT);
            }

            $result = true;
        } catch (Throwable $throwable) {
            Log::error($throwable);
        }
        return $result;
    }

    public function sendInvite($user, $role, $password = null): bool
    {
        $from = $role === Role::LICENSE_OWNER ? Config::get('mail.from.name') : $user->employee->companyData->name;
        try {
            if ($password === null) {
                $password = Controller::getRandomString();
                $user->password = bcrypt($password);
                $user->save();
            }
            $user->notify(new RegularInviteEmail($from, $user->title, $user->full_name, $role, $user->email, $password, $user->language->short_code));
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

        if ($request->has('theme_color')) {
            $data['theme_color'] = $request->theme_color;
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

        $from =  $user->employee->companyData->name;
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
}
