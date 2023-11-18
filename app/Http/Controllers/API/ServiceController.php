<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{

    protected $serviceRepo;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepo = $serviceRepository;
    }

    public function create(Request $request) {
        $success = false;
        $result = $this->serviceRepo->validate($request, true);
        if ($result === true) {
            $company = Auth::user()->employee()->first()->companyData()->first();
            $result = $this->serviceRepo->create($request, $company->id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function get(Request $request)
    {
        $services = $this->serviceRepo->all($request);
        return self::showResponse(true, $services);
    }

    public function find($id)
    {
        $service = $this->serviceRepo->find($id);
        return self::showResponse(true, $service);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->serviceRepo->validate($request, false);
        if ($result === true) {
            $result = $this->serviceRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete(Request $request, $id)
    {
        $result = $this->serviceRepo->delete($id);
        return self::showResponse($result);
    }

}
