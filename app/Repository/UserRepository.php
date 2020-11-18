<?php


namespace App\Repository;


use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\Notifications\ResetPasswordEmail;
use App\Role;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserRepository
{
    public function validate($request, $new = true)
    {
        $params = [
            'name' => 'required',
            'password' => 'sometimes|min:8',
        ];
        if ($new === true && $request['email'] !== '[no_email]') {
            $user = User::where(['email' => $request['email'], 'is_active' => 1])->first();
            $params['email'] = [
                'required',
                Rule::unique('users')->ignore($user ? $user->id : null),
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
        return User::where('id', $id)->with(array_merge(['phones.type', 'addresses.type', 'socials.type'], $with))->first();
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->is_active = $request->is_active;
        $user->save();
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->toArray());
        if (isset($request->password) && $request->password !== '') {
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
            if (!($request->is_active === true && User::where([['email', $user->email], ['is_active', true]])->exists())) {
                $user->is_active = $request->is_active;
                $user->save();
                $result = true;
            }
        } catch (\Throwable $th) {
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
