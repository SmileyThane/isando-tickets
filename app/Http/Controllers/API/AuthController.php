<?php

namespace App\Http\Controllers\API;

use App\CompanyUser;
use App\Email;
use App\Http\Controllers\Controller;
use App\Plan;
use App\Repository\CompanyRepository;
use App\Repository\CompanyUserRepository;
use App\Repository\LicenceRepository;
use App\Repository\PlanRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $companyRepo;
    protected $userRepo;
    protected $companyUserRepo;
    protected $planRepo;
    protected $licenseRepo;
    protected $roleRepo;

    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        CompanyUserRepository $companyUserRepository,
        LicenceRepository $licenceRepository,
        RoleRepository $roleRepository,
        PlanRepository $planRepository

    )
    {
        $this->companyRepo = $companyRepository;
        $this->userRepo = $userRepository;
        $this->companyUserRepo = $companyUserRepository;
        $this->planRepo = $planRepository;
        $this->licenseRepo = $licenceRepository;
        $this->roleRepo = $roleRepository;
    }

    public function login(Request $request)
    {
        $result = false;
        $tokenData = null;
        $email = Email::where('entity_type', User::class)->where('email', $request->email)->first();
        if (Auth::guard('web')->attempt(['id' => $email ? $email->entity_id : null, 'password' => $request->password, 'is_active' => true])) {
            $user = Auth::guard('web')->user();
            if ($user) {
                $tokenResult = $user->createToken('web');
                $tokenData['token'] = $tokenResult->accessToken;
                $tokenData['client_name'] = $tokenResult->token->name;
                $tokenData['expires_at'] = Carbon::parse($tokenResult->token['expires_at'])->format('U');
                $result = true;
            }

        }
        return self::showResponse($result, $tokenData);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return self::showResponse(true);
    }

    public function register(Request $request)
    {
        $errors = $this->getRegistrationErrors($request);
        if ($errors === null) {
            $request->is_validated = true;
            $request->is_active = true;
            $company = $this->companyRepo->create($request);
            $request->company_id = $company->id;
            $license = $this->licenseRepo->create($request);
            $request->password = Controller::getRandomString();
            $user = $this->userRepo->create($request);
            $companyUser = $this->companyUserRepo->create($company->id, $user->id, false);
            $this->roleRepo->attach($companyUser->id, CompanyUser::class, Role::LICENSE_OWNER);
            $this->userRepo->sendInvite($user, Role::LICENSE_OWNER, $request->password);
            return self::showResponse(true);
        }
        return self::showResponse(false, $errors);
    }

    private function getRegistrationErrors($request)
    {
        $isCompanyValid = $this->companyRepo->validate($request);
        $errors['company'] = $isCompanyValid !== true ? $isCompanyValid : null;
        $isUserValid = $this->userRepo->validate($request);
        $errors['user'] = $isUserValid !== true ? $isUserValid : null;
        $isLicenseValid = $this->licenseRepo->validate($request);
        $errors['license'] = $isLicenseValid !== true ? $isLicenseValid : null;
        return $isCompanyValid === true && $isUserValid === true && $isLicenseValid === true ? null : $errors;
    }

    public function plans()
    {
        return self::showResponse(true, Plan::all());
    }
}
