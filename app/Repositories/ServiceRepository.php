<?php

namespace App\Repositories;


use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceRepository
{

    protected $rules = [
        'name' => 'string|required'
    ];

    public function validate($request, $new = true)
    {
        $params = $this->rules;
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        return Service::MyCompany()->get();
    }

    public function find($service_id)
    {
        return Service::find($service_id);
    }

    public function create(Request $request, $company_id)
    {
        $service = new Service();
        $service->name = $request->name;
        $service->company_id = $company_id;
        $service->save();
        return $service;
    }

    public function update(Request $request, $service_id)
    {
        $service = Service::findOrFail($service_id);
        if ($request->has('name')) {
            $service->name = $request->name;
        }
        $service->save();
        return $service;
    }

    public function delete($service_id)
    {
        $service = Service::findOrFail($service_id);
        $service->delete();
        return true;
    }

}
