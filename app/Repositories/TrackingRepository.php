<?php


namespace App\Repositories;

use App\Service;
use App\Serviceable;
use App\Tracking;
use App\TrackingProject;
use App\TrackingLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrackingRepository
{

    protected $rules = [
        'create' => [
            'product.id' => 'nullable|exists:App\Product,id',
            'description' => 'nullable|string',
            'date_from' => 'required|string',
            'date_to' => 'nullable|string',
            'status' => 'required|string|in:started,paused,stopped',
            'billable' => 'boolean',
            'billed' => 'boolean',
            'tags' => 'array|nullable',
            'tags.*.id' => 'integer|nullable',
            'tags.*.name' => 'string',
        ],
        'update' => [
            'product.id' => 'nullable|exists:App\Product,id',
            'description' => 'nullable|string',
            'date_from' => 'nullable|string',
            'date_to' => 'nullable|string',
            'status' => 'nullable|string|in:started,paused,stopped',
            'billable' => 'boolean',
            'billed' => 'boolean',
            'tags' => 'array|nullable',
            'tags.*.id' => 'integer|nullable',
            'tags.*.name' => 'string',
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
            ->with('Tags')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function find($id)
    {
        return TrackingProject::where('id', $id)
            ->with('client')
            ->first();
    }

    public function create(Request $request)
    {
        $tracking = new Tracking();
        $tracking->user_id = Auth::user()->id;
        if ($request->has('description')) { $tracking->description = $request->description; }
        $tracking->date_from = Carbon::parse($request->date_from)->utc();
        if ($request->has('date_from') && $request->has('date_to') && !is_null($request->date_to)) {
            if (Carbon::parse($request->date_from)->gt(Carbon::parse($request->date_to))) {
                throw new \Exception('The date from must be a date before date to.');
            }
        }
        $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parse($request->date_to)->utc() : null;
        $tracking->status = $request->status;
        if ($request->has('billable')) { $tracking->billable = $request->billable; }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $tracking->entity_id = $request->entity['id'];
            $tracking->entity_type = $request->entity_type ?? TrackingProject::class;
        }
        $tracking->save();
        if ($request->has('service') && !is_null($request->service)) {
            if (!is_null($request->service)) {
                $service = Service::with('Company')->find($request->service['id']);
                $tracking->Services()->sync([$service->id]);
            } else {
                $tracking->Services()->sync([]);
            }
        }
        $this->logTracking($tracking->id, TrackingLogger::CREATE, null, $tracking);
        if ($request->has('tags')) {
            foreach ($request->tags as $tag) {
                $tracking->Tags()->attach($tag['id']);
            }
            $this->logTracking($tracking->id, TrackingLogger::ATTACH_TAGS, null, $request->tags);
        }

        return $tracking;
    }

    public function update(Request $request, Tracking $tracking)
    {
        $oldTracking = $tracking;
        $tracking->user_id = Auth::user()->id;
        if ($request->has('description')) { $tracking->description = $request->description; }
        if ($request->has('date_from')) {
            if (!$request->has('date_to') && Carbon::parse($request->date_from)->gt(Carbon::parse($tracking->date_to))) {
                throw new \Exception('The date from must be a date before date to.');
            }
            $tracking->date_from = Carbon::parse($request->date_from)->utc();
        }
        if ($request->has('date_to')) {
            if (!is_null($request->date_to) && Carbon::parse($tracking->date_from)->gt(Carbon::parse($request->date_to))) {
                throw new \Exception('The date to must be a date after date from.');
            }
            $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parse($request->date_to)->utc() : null;
        }
        if ($request->has('status')) {
            $tracking->status = $request->status;
        }
        if ($request->has('billable')) { $tracking->billable = $request->billable; }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $tracking->entity_id = $request->entity['id'];
            $tracking->entity_type = $request->entity_type ?? TrackingProject::class;
        }
        if ($request->has('service')) {
            if (!is_null($request->service)) {
                $service = Service::with('Company')->find($request->service['id']);
                $tracking->Services()->sync([$service->id]);
            } else {
                $tracking->Services()->sync([]);
            }
        }
        $tracking->save();
        $this->logTracking($tracking->id, TrackingLogger::UPDATE, $oldTracking, $tracking);
        if ($request->has('tags')) {
            $this->logTracking($tracking->id, TrackingLogger::UPDATE_TAGS, $tracking->Tags()->get(), $request->tags);
            $tracking->Tags()->detach();
            foreach ($request->tags as $tag) {
                $tracking->Tags()->attach($tag['id']);
            }
        }
        return $tracking;
    }

    public function delete(Tracking $tracking)
    {
//        if ($tracking->user_id === Auth::user()->id) {
            $this->logTracking($tracking->id, TrackingLogger::DELETE, $tracking, null);
            $tracking->delete();
            return true;
//        }
        return false;
    }

    public function duplicate(Tracking $tracking)
    {
        if ($tracking->user_id === Auth::user()->id) {
            $newTracking = $tracking->replicate();
            $newTracking->save();
            $this->logTracking($tracking->id, TrackingLogger::DUPLICATE, $tracking, $newTracking);
            return $newTracking;
        }
        return false;
    }

    protected function logTracking($trackingId, $action, $from = null, $to = null) {
        if ($action === TrackingLogger::UPDATE_TAGS && empty($from) && empty($to)) {
            return false;
        }
        $trackingLog = new TrackingLogger();
        $trackingLog->user_id = Auth::user()->id;
        $trackingLog->tracking_id = $trackingId;
        $trackingLog->action = $action;
        $trackingLog->from = $from;
        $trackingLog->to = $to;
        try {
            $trackingLog->save();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
