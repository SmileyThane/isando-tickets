<?php

namespace App\Repositories;

use App\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenceRepository
{
    public function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required',
            'payment_description' => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function find($id)
    {
        return License::find($id);
    }

    public function create(Request $request): License
    {
        $license = new License();
        $license->plan_id = $request->plan_id;
        $license->company_id = $request->company_id;
        $license->payment_description = $request->payment_description;
        $license->save();
        return $license;
    }

    public function update(Request $request, $id): License
    {
        $license = License::find($id);
        foreach ($request->all() as $param => $value) {
            $license->$param = $value;
        }
        $license->save();
        return $license;
    }

    public function delete($id): bool
    {
        $result = false;
        $license = License::find($id);
        if ($license) {
            $license->delete();
            $result = true;
        }
        return $result;
    }
}
