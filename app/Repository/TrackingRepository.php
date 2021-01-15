<?php


namespace App\Repository;

use App\Client;
use App\Company;
use App\Product;
use App\Team;
use App\TeamCompanyUser;
use App\Tracking;
use App\TrackingProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrackingRepository
{

    public function validate($request, $new = true)
    {
        $params = [
            'productId' => 'nullable|exists:App\Product,id',
            'description' => 'nullable|string',
            'dateFrom' => 'required|string',
            'dateTo' => 'required|string',
            'status' => 'required|string|in:started,paused,stopped',
            'billable' => 'boolean',
            'billed' => 'boolean',
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function all(Request $request)
    {
        return Auth::user()
            ->tracking()
            ->whereBetween('date_from', [
                Carbon::parse($request->dateFrom)->startOfDay(),
                Carbon::parse($request->dateTo)->endOfDay()
            ])
            ->with('Project.Product')
            ->with('Project.Client')
            ->with('User:id,name,surname,middle_name')
            ->orderBy('id', 'desc')
            ->get();
    }


    public function find($id)
    {
        return TrackingProject::where('id', $id)->with('client')->first();
    }

    public function create(Request $request)
    {
        $tracking = new Tracking();
        $tracking->user_id = Auth::user()->id;
        if ($request->has('productId')) { $tracking->product_id = $request->productId; }
        if ($request->has('description')) { $tracking->description = $request->description; }
        $tracking->date_from = Carbon::parseFromLocale($request->dateFrom);
        $tracking->date_to = Carbon::parseFromLocale($request->dateTo);
        $tracking->status = $request->status;
        if ($request->has('billable')) { $tracking->billable = $request->billable; }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        $tracking->save();
        return $tracking;
    }

    public function update(Request $request, $id)
    {
        //TODO
    }

    public function delete($id)
    {
        //TODO
    }

}
