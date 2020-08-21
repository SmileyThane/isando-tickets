<?php


namespace App\Repository;


use App\Http\Controllers\Controller;
use App\Notifications\RegularInviteEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRepository
{
    public function validate($request, $new = true)
    {
        $params = [
            'name' => 'required',
            'password' => 'sometimes|min:8',
        ];
        if ($new === true) {
            $params['email'] = [
                'required',
                Rule::unique('users')->ignore(
                    User::where('is_active', false)->where('email', $params['email'])->first()
                ),
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

        return User::where('id', $id)->with(array_merge(['phones.type', 'addresses.type'], $with))->first();
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
        try {
            $user = User::find($request->user_id);
            $user->is_active = $request->is_active;
            $user->save();
            $result = true;
        } catch (\Throwable $th) {
            $result = false;
        }
        return $result;
    }

    public function sendInvite($user, $role, $password = null): bool
    {
        if ($password === null) {
            $password = Controller::getRandomString();
            $user->password = bcrypt($password);
            $user->save();
        }
        $user->notify(new RegularInviteEmail($user->name, $role, $user->email, $password));
        return true;
    }
}
