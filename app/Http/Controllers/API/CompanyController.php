<?php


namespace App\Http\Controllers\API;

use App\Company;
use App\Http\Controllers\Controller;
use App\Repository\CompanyRepository;
use App\Repository\CompanyUserRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $companyRepo;
    protected $companyUserRepo;

    public function __construct(CompanyRepository $companyRepository, CompanyUserRepository $companyUserRepository)
    {
        $this->companyRepo = $companyRepository;
        $this->companyUserRepo = $companyUserRepository;
    }

    public function find($id = null)
    {
        $company = $this->companyRepo->find($id);
        return self::showResponse(true, $company);
    }

    public function invite(Request $request)
    {
        return $this->companyUserRepo->invite($request);
    }

}
