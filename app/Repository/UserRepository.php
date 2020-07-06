<?php


namespace App\Repository;


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
            $params['email'] = 'required|unique:users';
        }
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
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

    public function delete($id)
    {
        $result = false;
        $user = User::find($id);
        if ($user) {
            $user->delete();
            $result = true;
        }
        return $result;
    }

}
