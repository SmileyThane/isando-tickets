<?php

namespace App\Repositories;

use App\Permission;
use App\Role;
use App\Service;
use App\Team;
use App\Ticket;
use App\Tracking;
use App\TrackingProject;
use App\TrackingLogger;
use App\TrackingTimesheet;
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

    private function acl() {
        $isLicensed = false;
        $roleId = null;
        $teams = [];

//        Auth::user()->employee->userData->company_id
//        dd(Auth::user()->employee->role_names); // all roles in company

        // USER ROLE
//→ we need to have one role that has access to time tracking (just my own time tracking entries)
//based on license purchase by the license company - when license company purchases license for time tracking,
// everyone from the license company has access to the time tracking (their own entries)
        $hasLicensed = Auth::user()->employee->companyData->license;
        $company = Auth::user()->employee->companyData;
//        dd($company->id);
        // TEAM MANAGER
//→ we need to be able to select who is manager in each team on the team page, possibly as a tick box in the list of members.
// The tickbox header should have hover text "Team manager role allows view, edit and approve time tracking of the team members"
//license company purchased the license for time tracking , AND
//they are managers role and linked to a specific team
        $isManager = Auth::user()->employee->hasRoleId(Role::MANAGER);
//        $teams = Auth::user()->employee->assignedToTeams()->with('teamData')->get();
        $teams = Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id);
        })->with('employees.employee.userData')->first();
        $teamEmployees = $teams;
        dd($teams);

        // COMPANY ADMIN
//→ additionally, there should be a role that can view & edit time tracking of all license company users TIME TRACKING ADMIN
//license company purchased the license for time tracking , AND
//has got role Time tracking Admin
        $isAdmin = Auth::user()->employee->hasRoleId(Role::ADMIN);
        $company = Auth::user()->employee->companyData()->with('employees.userData')->first();
        $employees = $company;

//        $employee = Auth::user()->employee->assignedToClients;//->clients->customLicense;
        dd($company->employees->first());
    }

    public function all(Request $request)
    {
        if (!Auth::user()->employee->hasPermissionId([
            Permission::TRACKER_VIEW_OWN_TIME_ACCESS,
            Permission::TRACKER_VIEW_TEAM_TIME_ACCESS,
            Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS,
        ])) {
            throw new \Exception('Access denied');
        }

        // User
        $tracking = Tracking::SimpleUser();
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $tracking = Tracking::TeamManager();
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $tracking = Tracking::CompanyAdmin();
        }

        $tracking
            ->where(function($query) {
                $query->where('status', '!=', Tracking::$STATUS_ARCHIVED)
                    ->orWhereNull('status');
            })
            ->whereBetween('date_from', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ])
            ->with('Timesheet')
            ->with('Tags.Translates')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->orderBy('id', 'desc');

        return $tracking->get();
    }

    public function find($id)
    {
        return TrackingProject::where('id', $id)
            ->with('client')
            ->first();
    }

    public function create(Request $request)
    {
        if (!Auth::user()->employee->hasPermissionId(40)) {
            throw new \Exception('Access denied');
        }
        $tracking = new Tracking();
        $tracking->user_id = Auth::user()->id;
        if ($request->has('description')) { $tracking->description = $request->description; }
        $tracking->date_from = Carbon::parse($request->date_from)->format(Tracking::$DATETIME_FORMAT);
        if ($request->has('date_from') && $request->has('date_to') && !is_null($request->date_to)) {
            if (Carbon::parse($request->date_from)->gt(Carbon::parse($request->date_to))) {
                throw new \Exception('The date from must be a date before date to.');
            }
        }
        $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parse($request->date_to)->format(Tracking::$DATETIME_FORMAT) : null;
        $tracking->status = $request->status;
        if ($request->has('billable')) {
            $tracking->billable = $request->billable;
        } else {
            if ($request->has('entity') && $request->entity_type && $request->entity_type === TrackingProject::class) {
                $trackingProject = TrackingProject::find($request->entity['id']);
                if ($trackingProject) {
                    $tracking->billable = $trackingProject->billable_by_default;
                }
            }
        }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $tracking->entity_id = $request->entity['id'];
            $tracking->entity_type = $request->entity_type ?? TrackingProject::class;
        }
        $tracking->company_id = Auth::user()->employee->companyData->id;
        $team = Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id);
        })->first();
        $tracking->team_id = $team ? $team->id : null;
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

        if ($tracking->entity_type === TrackingProject::class) {
            $tracking->rate = $tracking->entity->rate;
            $tracking->save();
        }

        if ($tracking->entity_type === Ticket::class) {
            $tracking->rate = $tracking->entity->billedBy ? $tracking->entity->billedBy->cost : 0;
            $tracking->save();
        }

        TrackingTimesheetRepository::recalculate($tracking);
        return Tracking::where('id', '=', $tracking->id)->with('Tags.Translates')
            ->with('User:id,name,surname,middle_name,number,avatar_url')->first();
    }

    public function update(Request $request, Tracking $tracking)
    {
        $trackingDiff = Carbon::parse($tracking->date_from)->diffInSeconds(Carbon::now());

        if (
            !Auth::user()->employee->hasPermissionId([Permission::TRACKER_EDIT_DELETE_OWN_TIME_UNLIMITED_ACCESS])
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS)
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS)
        ) {
            throw new \Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 14) {
            throw new \Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 7) {
            throw new \Exception('Access denied');
        }
        if ($tracking->user_id !== Auth::user()->id  && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_TEAM_TIME_ACCESS)) {
            throw new \Exception('Access denied');
        }

        $oldTracking = $tracking;
        if ($request->has('description')) { $tracking->description = $request->description; }
        if ($request->has('date_from')) {
            if (!$request->has('date_to') && Carbon::parse($request->date_from)->gt(Carbon::parse($tracking->date_to))) {
                throw new \Exception('The date from must be a date before date to.');
            }
            $tracking->date_from = Carbon::parse($request->date_from)->format(Tracking::$DATETIME_FORMAT);
        }
        if ($request->has('date_to')) {
            if (!is_null($request->date_to) && Carbon::parse($tracking->date_from)->gt(Carbon::parse($request->date_to))) {
                throw new \Exception('The date to must be a date after date from.');
            }
            $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parse($request->date_to)->format(Tracking::$DATETIME_FORMAT) : null;
        }
        if ($request->has('status')) {
            $tracking->status = $request->status;
        }
        if ($request->has('billable') && Auth::user()->employee->hasPermissionId(46)) {
            $tracking->billable = $request->billable;
        }
        if ($request->has('billed')) { $tracking->billed = $request->billed; }
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $tracking->entity_id = $request->entity['id'];
            $tracking->entity_type = $request->entity_type ?? TrackingProject::class;
            if ($tracking->entity_type === TrackingProject::class) {
                $project = TrackingProject::find($request->entity['id']);
                $tracking->rate = $project->rate;
            }
            if ($tracking->entity_type === Ticket::class) {
                $tracking->rate = $tracking->entity->billedBy ? $tracking->entity->billedBy->cost : 0;
            }
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
        TrackingTimesheetRepository::recalculate($oldTracking);
        TrackingTimesheetRepository::recalculate($tracking);
        return Tracking::where('id', '=', $tracking->id)->with('Tags.Translates')
            ->with('User:id,name,surname,middle_name,number,avatar_url')->first();;
    }

    public function delete(Tracking $tracking)
    {
        $trackingDiff = Carbon::parse($tracking->date_from)->diffInSeconds(Carbon::now());
        if (
            !Auth::user()->employee->hasPermissionId([Permission::TRACKER_EDIT_DELETE_OWN_TIME_UNLIMITED_ACCESS])
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS)
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS)
        ) {
            throw new \Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 14) {
            throw new \Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 7) {
            throw new \Exception('Access denied');
        }

        if ($tracking->user_id !== Auth::user()->id  && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_DELETE_TEAM_TIME_ACCESS)) {
            throw new \Exception('Access denied');
        }
        $oldTracking = $tracking;
        $this->logTracking($tracking->id, TrackingLogger::DELETE, $tracking, null);
        $tracking->delete();
        TrackingTimesheetRepository::recalculate($oldTracking);
        return true;
    }

    public function duplicate(Tracking $tracking)
    {
        if ($tracking->user_id === Auth::user()->id) {
            $newTracking = $tracking->replicate();
            $newTracking->date_from = Carbon::parse($newTracking->date_from)->setSeconds(0)->format(Tracking::$DATETIME_FORMAT);
            $newTracking->date_to = Carbon::parse($newTracking->date_to)->setSeconds(0)->format(Tracking::$DATETIME_FORMAT);
            $newTracking->is_manual = true;
            $timesheet = TrackingTimesheet::find($tracking->timesheet_id);
            $newTimesheet = TrackingTimesheet::where([
                ['entity_id', '=', $timesheet->entity_id],
                ['entity_type', '=', $timesheet->entity_type],
                ['user_id', '=', $timesheet->user_id],
                ['team_id', '=', $timesheet->team_id],
                ['company_id', '=', $timesheet->company_id],
                ['is_manually', '=', !$timesheet->is_manually],
            ])->first();
            $newTracking->timesheet_id = $newTimesheet ? $newTimesheet->id : null;
            $newTracking->save();
            if ($tracking->service) {
                $newTracking->Services()->attach($tracking->service->id);
            }
            TrackingTimesheetRepository::recalculate($tracking);
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

    public function getCurrentUserTracking() {
        $user = Auth::user();
        return $user->tracking()->where('status', '=', 'started')->get();
    }
}
