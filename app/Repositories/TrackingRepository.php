<?php

namespace App\Repositories;

use App\Http\Controllers\API\Tracking\Filters\Trackers\EntityId;
use App\Http\Controllers\API\Tracking\Filters\Trackers\EntityType;
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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TrackingRepository
{

    protected $rules = [
        'create' => [
            'product.id' => 'nullable|exists:App\Product,id',
            'description' => 'nullable|string',
            'date_from' => 'required|string',
            'date_to' => 'nullable|string',
            'status' => 'required|integer|in:0,1,2,3',
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
            'status' => 'nullable|integer|in:0,1,2,3',
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
        $permissionIds = Auth::user()->employee->getPermissionIds();
//        DB::enableQueryLog();

        if (!in_array(Permission::TRACKER_VIEW_OWN_TIME_ACCESS, $permissionIds) &&
            !in_array(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS, $permissionIds) &&
            !in_array(Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS, $permissionIds)
        ) {
            throw new Exception('Access denied');
        }

        // User
        $tracking = Tracking::SimpleUser();

        if (in_array(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS, $permissionIds)) {
            // Manager
            $tracking = Tracking::TeamManager();
        }
        if (in_array(Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS, $permissionIds)) {
            // Company Admin
            $tracking = Tracking::CompanyAdmin();
        }

        if (!empty($request->get('team'))) {
            $tracking->whereIn('user_id', explode(',', $request->get('team')));
        }

        if ($request->has('users') && $request->users) {
            $users = explode(',', $request->users);
            $users = collect($users);
            $tracking->whereIn('user_id', $users);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $tracking
//            ->where('status', '!=', Tracking::$STATUS_ARCHIVED)
                ->where('date_from', '>=', Carbon::parse($request->date_from)->startOfDay()->format(Tracking::$DATETIME_FORMAT))
                ->where(function ($query) use ($request) {
                    $query->where('date_to', '<=', Carbon::parse($request->date_to)->endOfDay()->format(Tracking::$DATETIME_FORMAT))
                        ->orWhereNull('date_to');
                });
        }
        $tracking
//            ->with('Timesheet')
            ->with('Tags.Translates:name,lang,color')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->orderBy('date_from', 'desc')
            ->limit(15)
            ->offset($request->get('offset', 0));

        return app(Pipeline::class)
            ->send($tracking)
            ->through([
                EntityId::class,
                EntityType::class,
            ])
            ->thenReturn()
            ->get();
//        dd(DB::getQueryLog());
    }

    public function find($id)
    {
        return TrackingProject::where('id', '=', $id)
            ->with('Tags.Translates:name,lang,color')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->orderBy('date_from', 'desc')
            ->first();
    }

    public function create(Request $request)
    {
        if (!Auth::user()->employee->hasPermissionId(40)) {
            throw new Exception('Access denied');
        }
        $tracking = new Tracking();
        $tracking->is_manual = true;
        $tracking->user_id = Auth::user()->id;
        if ($request->has('description')) {
            $tracking->description = $request->description;
        }
        $tracking->date_from = Carbon::parse($request->date_from)->format(Tracking::$DATETIME_FORMAT);
        if ($request->has('date_from') && $request->has('date_to') && !is_null($request->date_to)) {
            if (Carbon::parse($request->date_from)->gt(Carbon::parse($request->date_to))) {
                throw new Exception('The date from must be a date before date to.');
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
        if ($request->has('billed')) {
            $tracking->billed = $request->billed;
        }
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $tracking->entity_id = $request->entity['id'];
            $tracking->entity_type = $request->entity_type ?? TrackingProject::class;
        }
        $tracking->company_id = Auth::user()->employee->companyData->id;
//        $team = Team::whereHas('employees', function ($query) {
//            return $query->where('company_user_id', '=', Auth::user()->employee->id);
//        })->first();
//        $tracking->team_id = $team ? $team->id : null;
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
            $tracking->team_id = $tracking->entity->team_id;
            $tracking->save();
        }

        if ($tracking->entity_type === Ticket::class) {
            $tracking->rate = $tracking->entity->billedBy ? $tracking->entity->billedBy->cost : 0;
            $tracking->team_id = $tracking->entity->to_team_id;
            $tracking->save();
        }

        if (is_null($tracking->entity_id)) {
            $tracking->team_id = null;
            $tracking->save();
        }

        TrackingTimesheetRepository::recalculate($tracking);
        return Tracking::where('id', '=', $tracking->id)
            ->with('Tags.Translates:name,lang,color')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->first();
    }

    protected function logTracking($trackingId, $action, $from = null, $to = null)
    {
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
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function update(Request $request, Tracking $tracking)
    {
        $trackingDiff = Carbon::parse($tracking->date_from)->diffInSeconds(Carbon::now());
        if (
            !Auth::user()->employee->hasPermissionId([Permission::TRACKER_EDIT_DELETE_OWN_TIME_UNLIMITED_ACCESS])
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS)
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS)
        ) {
            throw new Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 14) {
            throw new Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 7) {
            throw new Exception('Access denied');
        }
        if ($tracking->user_id !== Auth::user()->id && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_TEAM_TIME_ACCESS)) {
            throw new Exception('Access denied');
        }

        $oldTracking = $tracking;
        $oldService = $tracking->service;
        $oldEntityId = $tracking->entity_id;
        $oldEntityType = $tracking->entity_type;
        $oldTeamId = $tracking->team_id;
        $oldCompanyId = $tracking->company_id;
        $oldIsManual = $tracking->is_manual;
        if ($request->has('description')) {
            $tracking->description = $request->description;
        }
        if ($request->has('date_from')) {
            if (!$request->has('date_to') && Carbon::parse($request->date_from)->gt(Carbon::parse($tracking->date_to))) {
                throw new Exception('The date from must be a date before date to.');
            }
            $tracking->date_from = Carbon::parse($request->date_from)->format(Tracking::$DATETIME_FORMAT);
        }
        if ($request->has('date_to')) {
            if (!is_null($request->date_to) && Carbon::parse($tracking->date_from)->gt(Carbon::parse($request->date_to))) {
                throw new Exception('The date to must be a date after date from.');
            }
            $tracking->date_to = $request->has('date_to') && !is_null($request->date_to) ? Carbon::parse($request->date_to)->format(Tracking::$DATETIME_FORMAT) : null;
        }
        if ($request->has('status')) {
            $tracking->status = $request->status;
        }
        if ($request->has('billable') && Auth::user()->employee->hasPermissionId(46)) {
            $tracking->billable = $request->billable;
        }
        if ($request->has('billed')) {
            $tracking->billed = $request->billed;
        }
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $tracking->entity_id = $request->entity['id'];
            if (is_null($tracking->entity_id)) {
                $tracking->team_id = null;
                $tracking->rate = 0;
            }
            $tracking->entity_type = $request->entity_type ?? TrackingProject::class;
            if ($tracking->entity_type === TrackingProject::class) {
                $project = TrackingProject::find($request->entity['id']);
                $tracking->rate = $project->rate;
                $tracking->team_id = $tracking->entity->team_id;
            }
            if ($tracking->entity_type === Ticket::class) {
                $tracking->rate = $tracking->entity->billedBy ? $tracking->entity->billedBy->cost : 0;
                $tracking->team_id = $tracking->entity->to_team_id;
            }
            if (is_null($tracking->entity_id)) {
                $tracking->team_id = null;
                $tracking->save();
            }
        }
        if (is_null($tracking->entity_id)) {
            $tracking->team_id = null;
            $tracking->save();
        }
        if ($request->has('service')) {
            if (!is_null($request->service)) {
                $service = Service::with('Company')->find($request->service['id']);
                $tracking->Services()->sync([$service->id]);
            } else {
                $tracking->Services()->sync([]);
            }
        }
        $tracking->is_manual = true;
        $tracking->save();
        $this->logTracking($tracking->id, TrackingLogger::UPDATE, $oldTracking, $tracking);
        if ($request->has('tags')) {
            $this->logTracking($tracking->id, TrackingLogger::UPDATE_TAGS, $tracking->Tags()->get(), $request->tags);
            $tracking->Tags()->detach();
            foreach ($request->tags as $tag) {
                $tracking->Tags()->attach($tag['id']);
            }
        }
        $tracking->refresh();
        $timesheetId = $tracking->timesheet_id;
//        dd($oldTracking, $oldTracking->service, $tracking, $tracking->service);
        Log::debug('==========================================================================================');
        Log::debug('Recalculate new track');
        TrackingTimesheetRepository::recalculate($tracking);
        if (!is_null($timesheetId) && $timesheetId !== $tracking->timesheet_id) {
            $track = Tracking::where('timesheet_id', '=', $timesheetId)->first();
            TrackingTimesheetRepository::recalculate($track, false);
        }
        Log::debug('Recalculate old track');
        TrackingTimesheetRepository::recalculate($oldTracking, false, $oldService,
            $oldEntityId, $oldEntityType, $oldTeamId, $oldCompanyId, $oldIsManual);
        return Tracking::where('id', '=', $tracking->id)
            ->with('Tags.Translates')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->first();
    }

    public function delete(Tracking $tracking)
    {
        $trackingDiff = Carbon::parse($tracking->date_from)->diffInSeconds(Carbon::now());
        if (
            !Auth::user()->employee->hasPermissionId([Permission::TRACKER_EDIT_DELETE_OWN_TIME_UNLIMITED_ACCESS])
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS)
            && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS)
        ) {
            throw new Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 14) {
            throw new Exception('Access denied');
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS) && $trackingDiff > 60 * 60 * 24 * 7) {
            throw new Exception('Access denied');
        }

        if ($tracking->user_id !== Auth::user()->id && !Auth::user()->employee->hasPermissionId(Permission::TRACKER_DELETE_TEAM_TIME_ACCESS)) {
            throw new Exception('Access denied');
        }
        $oldTracking = $tracking;
        $oldService = $oldTracking->service;
        $this->logTracking($tracking->id, TrackingLogger::DELETE, $tracking, null);
        $tracking->delete();
        TrackingTimesheetRepository::recalculate($oldTracking, false, $oldService);
        return true;
    }

    public function duplicate(Tracking $tracking)
    {
        if ($tracking->user_id === Auth::user()->id) {
            $newTracking = $tracking->replicate();
            $newTracking->date_from = Carbon::parse($newTracking->date_from)->setSeconds(0)->format(Tracking::$DATETIME_FORMAT);
            $newTracking->date_to = Carbon::parse($newTracking->date_to)->setSeconds(0)->format(Tracking::$DATETIME_FORMAT);
            $newTracking->is_manual = true;
            if ($newTracking->entity_type === TrackingProject::class) {
                $newTracking->team_id = $newTracking->entity->team_id;
            }
            if ($newTracking->entity_type === Ticket::class) {
                $newTracking->team_id = $newTracking->entity->to_team_id;
            }
            $newTracking->save();
            TrackingTimesheetRepository::recalculate($newTracking);
            if ($tracking->service) {
                $newTracking->Services()->attach($tracking->service->id);
            }
            TrackingTimesheetRepository::recalculate($tracking);
            $this->logTracking($tracking->id, TrackingLogger::DUPLICATE, $tracking, $newTracking);
            return Tracking::where('id', '=', $newTracking->id)
                ->with('Tags.Translates:name,lang,color')
                ->with('User:id,name,surname,middle_name,number,avatar_url')
                ->first();
        }
        return false;
    }

    public function getCurrentUserTracking()
    {
        $user = Auth::user();
        return $user->tracking()->where('status', '=', Tracking::$STATUS_STARTED)->get();
    }

    private function acl()
    {
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
}
