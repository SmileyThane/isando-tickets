<?php


namespace App\Http\Controllers\API;

use App\Company;
use App\Http\Controllers\Controller;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $companyRepo;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepo = $companyRepository;
    }

    public function find($id = null)
    {
        $company = $this->companyRepo->find($id);
        return self::showResponse(true, $company);
    }
}
