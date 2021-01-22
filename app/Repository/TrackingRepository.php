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

    protected $rules = [
        'create' => [
            'product_id' => 'nullable|exists:App\Product,id',
            'description' => 'nullable|string',
            'date_from' => 'required|string',
            'date_to' => 'nullable|string',
            'status' => 'required|string|in:started,paused,stopped',
            'billable' => 'boolean',
            'billed' => 'boolean',
        ],
        'update' => [
            'product_id' => 'nullable|exists:App\Product,id',
            'description' => 'nullable|string',
            'date_from' => 'nullable|string',
            'date_to' => 'nullable|string',
            'status' => 'nullable|string|in:started,paused,stopped',
            'billable' => 'boolean',
            'billed' => 'boolean',
        ]
    ];

    public function validate($request, $new = true)
    {
        $params = $this->rules['update'];
        if ($new) {
            $params = $this->rules['create'];
        }
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
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
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
        if ($request->has('description')) { $tracking->description = $request->description; }
        $tracking->date_from = Carbon::parseFromLocale($request->date_from);
        $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parseFromLocale($request->date_to) : null;
        $tracking->status = $request->status;
        if ($request->has('billable')) { $tracking->billable = $request->billable; }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        if ($request->has('project')) { $tracking->project_id = $request->project['id']; }
        $tracking->save();
        return $tracking;
    }

    public function update(Request $request, Tracking $tracking)
    {
        $tracking->user_id = Auth::user()->id;
        if ($request->has('description')) { $tracking->description = $request->description; }
        if ($request->has('date_from')) {
            $tracking->date_from = Carbon::parseFromLocale($request->date_rom);
        }
        if ($request->has('date_to')) {
            $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parseFromLocale($request->date_to) : null;
        }
        if ($request->has('status')) {
            $tracking->status = $request->status;
        }
        if ($request->has('billable')) { $tracking->billable = $request->billable; }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        if ($request->has('project')) { $tracking->project_id = $request->project['id']; }
        $tracking->save();
        return $tracking;
    }

    public function delete(Tracking $tracking)
    {
        if ($tracking->user_id === Auth::user()->id) {
            $tracking->delete();
            return true;
        }
        return false;
    }

    public function duplicate(Tracking $tracking)
    {
        if ($tracking->user_id === Auth::user()->id) {
            $newTracking = $tracking->replicate();
            return $newTracking;
        }
        return false;
    }
}
