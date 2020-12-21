<?php


namespace App\Repository;


use App\Company;
use App\EmailType;
use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Notifications\ResetPasswordEmail;
use App\Role;
use App\Settings;
use App\User;
use App\Email;
use App\Repository\EmailRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        return User::where('id', $id)->with(array_merge(['phones.type', 'addresses.type', 'addresses.country', 'socials.type', 'emails', 'emails.type', 'emailSignatures'], $with))->first();
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->middle_name = $request->middle_name;
        $user->surname = $request->surname;
        $user->password = bcrypt($request->password);
        $user->is_active = $request->is_active;
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
            $user->save();
//            Email::where(['entity_id' => $user->id, 'entity_type' => User::class])
                $email = Email::find($request->email_id);

                if ($user->is_active === true) {
                    $email->email_type = 1;
                } else {
                    $secondaryType = EmailType::where('entity_type', Company::class)->where('entity_id', $user->employee->companyData->id)->first();
                    $email->email_type = $secondaryType ? $secondaryType->id : 1;
                }
                $email->save();
            $result = true;
        } catch (\Throwable $th) {
            dd($th);
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
                $user->notify(new ResetPasswordEmail($from, $user->name, $role, $user->email, $password));
            } else {
                $user->notify(new RegularInviteEmail($from, $user->name, $role, $user->email, $password));
            }
        } catch (\Throwable $throwable) {
            Log::error($throwable);
            //hack for broken notification system
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

}
