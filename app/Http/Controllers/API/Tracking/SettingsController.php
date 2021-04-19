<?php


namespace App\Http\Controllers\API\Tracking;


use App\Company;
use App\Currency;
use App\TrackingSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends BaseController
{
    public function get() {
        $company = Auth::user()->employee->companyData;
        return self::showResponse(true, [
            'company' => $company,
            'currency' => $company->currency,
            'email' => Auth::user()->contact_email,
            'settings' => TrackingSettings::where('entity_id', '=', $company->id)
                ->where('entity_type', '=', Company::class)
                ->pluck('data')
                ->first()
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
        if ($request->has('settings')) {
            TrackingSettings::updateOrCreate([
                    'entity_id' => Auth::user()->employee->companyData->id,
                    'entity_type' => Company::class
                ], [
                    'data' => $request->get('settings')
                ]
            );
        }
        return self::showResponse(true, '');
    }
}
