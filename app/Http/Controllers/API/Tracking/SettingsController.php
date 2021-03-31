<?php


namespace App\Http\Controllers\API\Tracking;


use App\Company;
use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends BaseController
{
    public function get() {
        $company = Auth::user()->employee->companyData;
        return self::showResponse(true, [
            'company' => $company,
            'currency' => $company->currency,
        ]);
    }

    public function update(Request $request) {
        if ($request->has('currency')) {
            try {
                $currency = Currency::find((int)$request->get('currency'));
                $company = Company::find(Auth::user()->employee->companyData->id);
                $company->currency_id = $currency->id;
                $company->save();
            } catch (\Exception $exception) {
                return self::showResponse(false, $exception->getMessage());
            }
        }
        return self::showResponse(true, '');
    }
}
