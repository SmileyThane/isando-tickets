<?php


namespace App\Http\Controllers\API\Tracking;


use App\Company;
use App\Currency;
use App\TrackingSettings;
use App\User;
use Carbon\Carbon;
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
            'settings' => TrackingSettings::where('entity_id', '=', Auth::user()->id)
                ->where('entity_type', '=', User::class)
                ->pluck('data')
                ->first()
        ]);
    }

    public function update(Request $request) {
        if ($request->has('currency') && $request->get('currency') !== null) {
            try {
                $currency = Currency::find((int)$request->get('currency'));
                $company = Company::find(Auth::user()->employee->companyData->id);
                $company->currency_id = $currency->id;
                $company->save();
            } catch (\Exception $exception) {
                return self::showResponse(false, $exception->getMessage());
            }
        }
        if ($request->has('settings') && Auth::user()->employee->getPermissionIds([80,81])) {
            TrackingSettings::updateOrCreate([
                    'entity_id' => Auth::user()->id,
                    'entity_type' => User::class
                ], [
                    'data' => $request->get('settings')
                ]
            );
        }
        return self::showResponse(true, '');
    }

    public function genReport(Request $request, $source = 'tracker') {
        $periodStart = $request->get('start', Carbon::now()->startOf('week'));
        $periodEnd = $request->get('end', Carbon::now()->endOf('week'));
        $clients = $request->get('clients', []);
        $coworkers = $request->get('coworkers', []);

        return $this->trackingReportRepo->getReportDetail($source, $periodStart, $periodEnd, $clients, $coworkers);
    }

    public function getReconciliationReport(Request $request) {
        $periodStart = $request->get('start', Carbon::now()->startOf('week'));
        $periodEnd = $request->get('end', Carbon::now()->endOf('week'));
        $groupBy = $request->get('group', 'client');
        return $this->trackingReportRepo->getReportReconciliationDetail($periodStart, $periodEnd, $groupBy);
    }
}
