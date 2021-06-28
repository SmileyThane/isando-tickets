<?php


namespace App\Repositories;

use App\Notifications\TimesheetAppovalRequest;
use App\Notifications\TimesheetApproved;
use App\Notifications\TimesheetRejected;
use App\Permission;
use App\Service;
use App\Team;
use App\Ticket;
use App\Tracking;
use App\TrackingProject;
use App\TrackingTimesheet;
use App\TrackingTimesheetTemplate;
use App\TrackingTimesheetTime;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackingTimesheetRepository
{
    public function validate(Request $request) {
        return true;
    }

    public function all(Request $request) {
        $query = TrackingTimesheet::with('User')
            ->with('Service')
            ->with('Approver')
            ->with(['Times' => function ($q) {
                $q->orderBy('date', 'asc');
            }]);
        $teams = \App\Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->pluck('id');
        if ($teams->count()) {
            $query->where(function($q) use ($teams) {
                $q->where('user_id', '=', Auth::user()->id)
                    ->orWhereIn('team_id', $teams)
                    ->orWhere('approver_id', Auth::user()->id);
            });
        } else {
            $query->where('user_id', '=', Auth::user()->id)
                ->orWhere('approver_id', Auth::user()->id);
        }
        return $query
            ->where(function ($q) use ($request) {
                $q->whereBetween('from', [Carbon::parse($request->start), Carbon::parse($request->end)])
                    ->orWhereBetween('to', [Carbon::parse($request->start), Carbon::parse($request->end)]);
            })
            ->get();
    }

    public function getAllGroupedByStatus(Request $request) {
        $query = TrackingTimesheet::with('User')
            ->with('Service')
            ->with('Approver')
            ->with(['Times' => function ($q) {
                $q->orderBy('date', 'asc');
            }])
            ->whereIn('status', [
                TrackingTimesheet::STATUS_PENDING,
                TrackingTimesheet::STATUS_REJECTED,
                TrackingTimesheet::STATUS_APPROVED,
                TrackingTimesheet::STATUS_ARCHIVED,
            ]);

        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($q) {
                return $q
                    ->where('company_user_id', '=', Auth::user()->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $query->orWhereIn('team_id', $teams)
                ->where(function ($query) {
                    $query->whereNull('approver_id')
                        ->orWhere('approver_id', '=', Auth::user()->id);
                });
        } else if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $company = Auth::user()->employee()
                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
                ->with('userData')->first();
            $query->where('company_id', '=', $company->company_id)
                ->where(function ($query) {
                    $query->whereNull('approver_id')
                        ->orWhere('approver_id', '=', Auth::user()->id);
                });
        } else {
            $query->where(function($q) {
                $q->where('user_id', '=', Auth::user()->id)
                    ->orWhere('approver_id', '=', Auth::user()->id);
            });
        }
        return $query->get();
    }

    public function find($id) {
        return null;
    }

    public function create(Request $request) {
        $timesheet = new TrackingTimesheet();
        $timesheet->user_id = Auth::user()->id;
        $team = Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id);
        })->first();
        $timesheet->team_id = $team ? $team->id : null;
        $timesheet->company_id = Auth::user()->employee->companyData->id;
        $timesheet->service_id = $request->get('service', null);
        if ($request->has('entity_id') && $request->has('entity_type')) {
            $timesheet->entity_id = $request->entity_id;
            $timesheet->entity_type = $request->entity_type ?? TrackingProject::class;
        }
        $timesheet->is_manually = true;
        $timesheet->from = Carbon::parse($request->get('mon'))->format('Y-m-d');
        $timesheet->to = Carbon::parse($request->get('sun'))->format('Y-m-d');
        $timesheet->status = TrackingTimesheet::STATUS_TRACKED;
        $timesheet->save();

        $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($daysOfWeek as $dayOfWeek) {
            if ($request->has($dayOfWeek)) {
                $time = new TrackingTimesheetTime();
                $time->type = TrackingTimesheetTime::TYPE_WORK;
                $time->date = Carbon::parse($request->get($dayOfWeek))->format('Y-m-d');
                $time->time = Carbon::parse($request->get($dayOfWeek))->format('H:i:s');
                $time->description = '';
                $time->timesheet_id = $timesheet->id;
                $time->save();
            }
        }

        $this->genTrackersByTimesheet($timesheet);

        return TrackingTimesheet::with('User')
            ->with('Approver')
            ->with('Service')
            ->where('id', '=', $timesheet->id)
            ->with(['Times' => function ($q) {
                $q->orderBy('date', 'asc');
            }])->first();
    }

    public function update(Request $request, $id) {
        $timesheet = TrackingTimesheet::findOrFail($id);
        if ($request->has('entity') && $request->entity && $request->entity_type) {
            $timesheet->entity_id = $request->entity['id'];
            $timesheet->entity_type = $request->entity_type ?? TrackingProject::class;
        }
        if ($request->has('billable')) {
            $timesheet->billable = $request->get('billable', false);
        }
        if ($request->has('service')) {
            $service = $request->get('service');
            $timesheet->service_id = $service && isset($service['id']) ? $service['id'] : $service;
        }
        $timesheet->save();
        if ($request->has('times')) {
            $items = $request->get('times');
            foreach ($items as $item) {
                TrackingTimesheetTime::updateOrCreate(
                    ['id' => $item['id'], 'timesheet_id' => $item['timesheet_id']],
                    ['date' => $item['date'], 'time' => $item['time']]
                );
            }
        }
        $this->genTrackersByTimesheet($timesheet);

        return TrackingTimesheet::with('User')
            ->with('Approver')
            ->with('Service')
            ->where('id', '=', $id)
            ->with(['Times' => function ($q) {
                $q->orderBy('date', 'asc');
            }])->first();
    }

    public function delete($id) {
        return TrackingTimesheet::where('id', '=', $id)->delete();
    }

    public function remind(Request $request)
    {
        $ids = $request->get('ids');
        $timesheets = TrackingTimesheet::whereIn('id', $ids)->get();
        try {
            foreach ($timesheets as $timesheet) {
                if ($timesheet->approver && (
                    is_null($timesheet->notification_date)
                    || Carbon::parse($timesheet->notification_date)->diffInHours(Carbon::now()) > 6
                )) {
                    $timesheet->notification_date = Carbon::now();
                    $timesheet->save();
                    $timesheet->approver->notify(new TimesheetAppovalRequest());
                }
            }
        } catch (\Exception $e) {
            Log::debug($e);
        }
        return true;
    }

    public function submit(Request $request)
    {
        $ids = $request->get('ids');
        $updatedTimesheet = [];
        $number = null;
        foreach ($ids as $id) {
            $trackingTimesheet = TrackingTimesheet::where('id', '=', $id)->first();
            $approverId = null;

            if (in_array($request->get('status'), [TrackingTimesheet::STATUS_TRACKED])) {
                $trackingTimesheet->number = null;
                $trackingTimesheet->save();
            }

            if (in_array($request->get('status'), [TrackingTimesheet::STATUS_PENDING])) {
                $approverId = $request->get('approver_id', null);
                if (!$number && !$trackingTimesheet->number) {
                    $number = $trackingTimesheet->genNumber();
                }
                if ($number && !$trackingTimesheet->number) {
                    $trackingTimesheet->number = $number;
                    $trackingTimesheet->save();
                }
            } else {
                $approverId = null;
            }
            if (in_array($request->get('status'), [
                TrackingTimesheet::STATUS_ARCHIVED, TrackingTimesheet::STATUS_APPROVED,
                TrackingTimesheet::STATUS_REJECTED
            ])) {
                $approverId = Auth::user()->id;
            }

            TrackingTimesheet::where('id', '=', $id)->update([
                'status' => $request->get('status'),
                'approver_id' => $approverId,
                'submitted_on' => in_array($request->get('status'), [
                    TrackingTimesheet::STATUS_PENDING, TrackingTimesheet::STATUS_ARCHIVED,
                    TrackingTimesheet::STATUS_APPROVED, TrackingTimesheet::STATUS_REJECTED
                ])
                    ? Carbon::now()
                    : null,
                'note' => $request->get('note', null),
            ]);
            $tt = TrackingTimesheet::where('id', '=', $id)
                ->with('User')
                ->with('Approver')
                ->with('Service')
                ->with(['Times' => function ($q) {
                    $q->orderBy('date', 'asc');
                }])->first();
            $tt->notification_date = null;
            $tt->save();
            try {
                if ($tt->status === TrackingTimesheet::STATUS_REJECTED && $tt->approver) {
                    $tt->user->notify(new TimesheetRejected($tt));
                }
                if ($tt->status === TrackingTimesheet::STATUS_ARCHIVED && $tt->approver) {
                    $tt->user->notify(new TimesheetApproved($tt));
                }
                if ($tt->status === TrackingTimesheet::STATUS_PENDING && $tt->approver) {
                    $tt->notification_date = Carbon::now();
                    $tt->save();
                    $tt->approver->notify(new TimesheetAppovalRequest());
                }
            } catch (\Exception $e) {
                Log::debug($e);
            }
            $updatedTimesheet[] = $tt;
        }
        return $updatedTimesheet;
    }

    public static function recalculate($tracking) {

        $trackers = Tracking::where([
            ['status', '<>', Tracking::$STATUS_ARCHIVED],
            ['user_id', '=', $tracking->user_id],
            ['team_id', '=', $tracking->team_id],
            ['company_id', '=', $tracking->company_id],
            ['is_manual', '=', $tracking->is_manual],
            ['entity_id', '=', $tracking->entity_id],
            ['entity_type', '=', $tracking->entity_type],
            ['date_from', '>=', Carbon::parse($tracking->date_from)->startOf('weeks')->format('Y-m-d')],
            ['date_to', '<=', Carbon::parse($tracking->date_to)->endOf('weeks')->format('Y-m-d')],
        ])->get();

        $items = [];
        if ($trackers->count()) {
            foreach ($trackers as $tracker) {
                $timesheet = TrackingTimesheet::where([
                    ['user_id', '=', $tracker->user_id],
                    ['company_id', '=', $tracker->company_id],
                    ['team_id', '=', $tracker->team_id],
                    ['entity_id', '=', $tracker->entity_id],
                    ['entity_type', '=', $tracker->entity_type],
                    ['is_manually', '=', !$tracker->is_manual],
                    ['from', '>=', Carbon::parse($tracker->date_from)->startOf('weeks')],
                    ['to', '<=', Carbon::parse($tracker->date_to)->endOf('weeks')]
                ])->first();
                if (!$timesheet) {
                    $timesheet = new TrackingTimesheet();
                    $timesheet->user_id = $tracker->user_id;
                    $timesheet->company_id = $tracker->company_id;
                    $timesheet->team_id = $tracker->team_id;
                    $timesheet->entity_id = $tracker->entity_id;
                    $timesheet->entity_type = $tracker->entity_type;
                    $timesheet->is_manually = false;
                    $timesheet->from = Carbon::parse($tracker->date_from)->startOf('weeks')->format('Y-m-d');
                    $timesheet->to = Carbon::parse($tracker->date_to)->endOf('weeks')->format('Y-m-d');
                    $timesheet->save();
                    for ($i = 0; $i <= 6; $i++) {
                        $trackingTimesheetTime = new TrackingTimesheetTime();
                        $trackingTimesheetTime->timesheet_id = $timesheet->id;
                        $trackingTimesheetTime->type = TrackingTimesheetTime::TYPE_WORK;
                        $trackingTimesheetTime->date = Carbon::parse($timesheet->from)->addDays($i)->format('Y-m-d');
                        $trackingTimesheetTime->time = self::convertSecondsToTimeFormat(0, true);
                        $trackingTimesheetTime->save();
                    }
                }
                TrackingTimesheetTime::where('timesheet_id', '=', $timesheet->id)->update(['time' => '00:00:00']);
                Tracking::where('id', '=', $tracker->id)->update(['timesheet_id' => $timesheet->id]);
                $date = Carbon::parse($tracker->date_from)->format('Y-m-d');
                $items[$timesheet->id][$date][] = $tracker;
            }
        } else {
            if ($tracking->timesheet_id) {
                TrackingTimesheetTime::where([
                    ['timesheet_id', '=', $tracking->timesheet_id],
                    ['date', '=', Carbon::parse($tracking->date_from)->format('Y-m-d')]
                ])->update(['time' => '00:00:00']);
            }
        }

        foreach ($items as $timesheet_id => $timesheetItems) {
            foreach ($timesheetItems as $date => $trackers) {
                $seconds = 0;
                foreach ($trackers as $tracker) {
                    $seconds += $tracker->passed;
                }
                TrackingTimesheetTime::updateOrCreate([
                    'timesheet_id' => $timesheet_id,
                    'type' => TrackingTimesheetTime::TYPE_WORK,
                    'date' => $date
                ], [
                    'time' => self::convertSecondsToTimeFormat($seconds, true),
                    'timesheet_id' => $timesheet_id,
                    'type' => TrackingTimesheetTime::TYPE_WORK,
                    'date' => $date
                ]);
            }
        }
        return true;
    }

//    public static function recalculateTimesheet(int $timesheetId) {
//        $timesheet = TrackingTimesheet::where('id', '=', $timesheetId)->with('Times')->first();
//        if ($timesheet) {
//            foreach ($timesheet->times as $time) {
//                $tracking = Tracking::where([
//                    ['timesheet_id', '=', $timesheet->id],
//                    ['date_from', '<=', Carbon::parse($time->date)->endOfDay()->format(Tracking::$DATETIME_FORMAT)],
//                    ['date_to', '>=', Carbon::parse($time->date)->startOfDay()->format(Tracking::$DATETIME_FORMAT)],
//                ])->pluck('time')->get();
//                $passedTime = $tracking->map(function($item) {
//                    return self::convertTimeToSeconds($item);
//                })->sum();
//                dd($tracking, $passedTime);
//                $time->time = self::convertTimeToSeconds($passedTime);
//                $time->save();
//            }
//        }
//    }

    static function convertTimeToSeconds($time) {
        $time = explode(':', $time);
        return $time[0] * 60 * 60 + $time[1] * 60 + $time[2];
    }

    static function convertSecondsToTimeFormat($value, $withSeconds = true) {
        $h = floor($value / 60 / 60);
        $m = floor(($value - ($h * 60 * 60)) / 60);
        if ($withSeconds) {
            return sprintf("%02d", $h) . ":" . sprintf("%02d", $m) . ":" . sprintf("%02d", $value % 60);
        }
        return sprintf("%02d", $h) . ":" . sprintf("%02d", $m);
    }

    public function increaseTimes(TrackingTimesheet $timesheet, TrackingTimesheetTime $time, int $timeToInc) {
        $trackingProject = null;
        if ($timesheet->entity_type === TrackingProject::class) {
            $trackingProject = TrackingProject::where('id', '=', $timesheet->entity_id)->first();
        }

        $tracker = new Tracking();
        $tracker->timesheet_id = $timesheet->id;
        $tracker->user_id = $timesheet->user_id;
        $tracker->team_id = $timesheet->team_id;
        $tracker->company_id = $timesheet->company_id;
        $tracker->entity_id = $timesheet->entity_id;
        $tracker->entity_type = $timesheet->entity_type;
        $tracker->is_manual = false;
        $tracker->date_from = Carbon::parse($time->date)->hours(8)->format(Tracking::$DATETIME_FORMAT);
        $tracker->date_to = Carbon::parse($tracker->date_from)->addSeconds($timeToInc)->format(Tracking::$DATETIME_FORMAT);
        $tracker->status = Tracking::$STATUS_STOPPED;
        $tracker->billable = $trackingProject ? $trackingProject->billable_by_default : false;
        $tracker->rate = $trackingProject ? $trackingProject->rate : 0;
        $tracker->save();

        $service = Service::where('id', '=', $timesheet->service_id)->first();
        if ($service) {
            $tracker->Services()->sync([$service->id]);
        }
    }

    public function decreaseTimes($trackers, $timeToDec = 0) {
        if ($timeToDec <= 0 || empty($trackers)) return;
        $tracker = $trackers->shift();
        if ($tracker->passed > $timeToDec) {
            $tracker->date_to = Carbon::parse($tracker->date_to)->subSeconds($timeToDec)->format(Tracking::$DATETIME_FORMAT);
            $tracker->save();
        } elseif ($tracker->passed <= $timeToDec) {
            $timeToDec = $timeToDec - $tracker->passed;
            $tracker->delete();
            $this->decreaseTimes($trackers, $timeToDec);
        }
        return;
    }

    public function genTrackersByTimesheet(TrackingTimesheet $timesheet) {
        $entityId = $timesheet->entity_id;
        $entityType = $timesheet->entity_type;
        $userId = $timesheet->user_id;
        $teamId = $timesheet->team_id;
        $companyId = $timesheet->company_id;
        foreach ($timesheet->times as $time) {
            $from = Carbon::parse($time->date)->startOfDay()->format(Tracking::$DATETIME_FORMAT);
            $to = Carbon::parse($time->date)->endOfDay()->format(Tracking::$DATETIME_FORMAT);
            $trackers = Tracking::where([
                ['team_id', '=', $teamId],
                ['company_id', '=', $companyId],
                ['user_id', '=', $userId],
                ['entity_id', '=', $entityId],
                ['entity_type', '=', $entityType],
                ['timesheet_id', '=', $timesheet->id],
                ['is_manual', '=', 'false'],
            ])
                ->where(function ($query) use ($from, $to) {
                    $query->where('date_from', '<=', $to)
                        ->where('date_to', '>=', $from);
                })
                ->orderBy('id', 'desc')
                ->get();
            $totalTime = $time->timeInSec;
            $trackersTotalPassed = $trackers->sum('passed');
            if ($trackersTotalPassed > $totalTime) {
                $this->decreaseTimes($trackers, $trackersTotalPassed - $totalTime);
            } elseif ($trackersTotalPassed < $totalTime) {
                $this->increaseTimes($timesheet, $time, $totalTime - $trackersTotalPassed);
            }
        }
    }

    public function getCountTimesheetForApproval() {
        if (!Auth::user()->employee->hasPermissionId([
            Permission::TRACKER_VIEW_OWN_TIME_ACCESS,
            Permission::TRACKER_VIEW_TEAM_TIME_ACCESS,
            Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS,
        ])) {
            throw new \Exception('Access denied');
        }
        $timesheet = 0;
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($query) {
                return $query
                    ->where('company_user_id', '=', Auth::user()->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $timesheet = TrackingTimesheet::where('status', '=', TrackingTimesheet::STATUS_PENDING)
                ->whereIn('team_id', $teams)
                ->where(function ($query) {
                    $query->whereNull('approver_id')
                        ->orWhere('approver_id', '=', Auth::user()->id);
                })
                ->groupBy('number')
                ->select('number')
                ->get()->count();
        }
        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $company = Auth::user()->employee()
                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
                ->with('userData')->first();
            $timesheet = TrackingTimesheet::where('status', '=', TrackingTimesheet::STATUS_PENDING)
                ->where('company_id', '=', $company->company_id)
                ->where(function ($query) {
                    $query->whereNull('approver_id')
                        ->orWhere('approver_id', '=', Auth::user()->id);
                })
                ->groupBy('number')
                ->select('number')
                ->get()->count();
        }
        return $timesheet;
    }

    public function copyLastWeek(User $user) {
        $timesheets = TrackingTimesheet::where([
            ['user_id', '=', $user->id],
            ['from', '<=', Carbon::now()->subWeek()->endOf('week')],
            ['to', '>=', Carbon::now()->subWeek()->startOf('week')],
            ['is_manually', '=', true],
        ])->get();
        foreach ($timesheets as $timesheet) {
            $newTimesheet = $timesheet->duplicate();
            $this->genTrackersByTimesheet($newTimesheet);
        }
    }

    public function getUserTemplates() {
        return TrackingTimesheetTemplate::where('user_id', '=', Auth::user()->id)->get();
    }

    public function saveAsTemplate($items, $config) {
        $data = [];
        foreach ($items as $item) {
            $timesheet = TrackingTimesheet::find($item);
            $dataItem = [];
            $dataItem['parent'] = $timesheet;
            if (in_array('projects', $config['components'])) {
                $dataItem['entity'] = $timesheet->entity;
            }
            if (in_array('services', $config['components'])) {
                $dataItem['service'] = null;
                if ($timesheet->Service()->first()) {
                    $dataItem['service'] = $timesheet->Service()->first();
                }
            }
            if (in_array('hours', $config['components'])) {
                $dataItem['hours'] = $timesheet->Times;
            }
            array_push($data, $dataItem);
        }
        $template = new TrackingTimesheetTemplate();
        $template->name = $config['name'];
        $template->user_id = Auth::user()->id;
        $template->data = $data;
        $template->save();
        return $template;
    }

}
