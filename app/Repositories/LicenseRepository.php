<?php


namespace App\Repositories;


use App\License;
use App\Plan;
use App\PlanPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseRepository
{
    public function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'plan_price_id' => 'required',
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

    public function create(Request $request)
    {
        $plan = $this->processPayment($request->plan_price_id, $request->card_hash);
        if ($plan) {
            $license = new License();
            $license->plan_id = $plan->id;
            $license->company_id = $request->company_id;
            $license->payment_description = $request->payment_description;
            $license->save();

            return $license;
        }
        return null;
    }

    public function update(Request $request, $id)
    {
        $license = License::find($id);
        foreach ($request->all() as $param => $value) {
            $license->$param = $value;
        }
        $license->save();

        return $license;
    }

    public function delete($id)
    {
        $result = false;
        $license = License::find($id);
        if ($license) {
            $license->delete();
            $result = true;
        }

        return $result;
    }

    private function processPayment($planPriceId, $cardHash)
    {
        $planPrice = PlanPrice::find($planPriceId);
        $plan = Plan::find($planPrice->plan_id);
        if ($plan->is_default === 1) {
            return $plan;
        }

        return null;
    }
}
