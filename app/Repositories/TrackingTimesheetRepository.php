<?php


namespace App\Repositories;

use App\Http\Controllers\API\Tracking\PDF;
use App\Notifications\TimesheetAppovalRequest;
use App\Notifications\TimesheetApproved;
use App\Notifications\TimesheetRejected;
use App\Permission;
use App\Service;
use App\Team;
use App\Tracking;
use App\TrackingProject;
use App\TrackingTimesheet;
use App\TrackingTimesheetTemplate;
use App\TrackingTimesheetTime;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            }])
            ->whereIn('status', [
                TrackingTimesheet::STATUS_TRACKED,
                TrackingTimesheet::STATUS_PENDING,
                TrackingTimesheet::STATUS_REJECTED,
                TrackingTimesheet::STATUS_APPROVED,
                TrackingTimesheet::STATUS_ARCHIVED,
                TrackingTimesheet::STATUS_UNSUBMITTED,
            ])
            ->where(function ($q) use ($request) {
                $q->where('from', '<=', Carbon::parse($request->end))
                    ->where('to', '>=', Carbon::parse($request->start));
            });

        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($q) {
                return $q
                    ->where('company_user_id', '=', Auth::user()->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $query->where(function ($query) use ($teams) {
                    $query->whereIn('team_id', $teams)
                        ->where(function($q) {
                           $q->whereNull('approver_id')
                               ->orWhere('approver_id', '=', Auth::user()->id);
                        })
                        ->orWhere('user_id', '=', Auth::user()->id);
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
        $query->orderBy('id', 'desc');
//        dd($query->toSql(), $query->getBindings(), $query->get());
        return $query->get();
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
        $query->orderBy('id', 'desc');
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
        if ($request->has('entity_id') && $request->entity_id && $request->entity_type) {
            $timesheet->entity_id = $request->entity_id;
            $timesheet->entity_type = $request->entity_type ?? TrackingProject::class;
            Tracking::where('timesheet_id', '=', $timesheet->id)
                ->update(['entity_id' => $timesheet->entity_id, 'entity_type' => $timesheet->entity_type]);
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
        $timesheet = TrackingTimesheet::find($id);
        $timesheet->delete();
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

    public static function calcTimeByTrackers($trackers) {
        $time = [0,0,0,0,0,0,0];

        foreach ($trackers as $tracker) {
            // dayOfWeekIso returns a number between 1 (monday) and 7 (sunday)
            $dayOfWeek = Carbon::parse($tracker->date_from)->dayOfWeekIso;
            $time[$dayOfWeek - 1] += $tracker->passed;
        }
        return $time;
    }

    public static function setTimesheetTime($timesheet_id, $timeByDay) {
        $trackingTimesheetTime = TrackingTimesheetTime::where('timesheet_id', '=', $timesheet_id)
            ->orderBy('date')->get();
        foreach ($trackingTimesheetTime as $time) {
            // dayOfWeekIso returns a number between 1 (monday) and 7 (sunday)
            $dayOfWeek = Carbon::parse($time->date)->dayOfWeekIso;
            if (isset($timeByDay[$dayOfWeek-1])) {
                $time->time = self::convertSecondsToTimeFormat($timeByDay[$dayOfWeek - 1], true);
            } else {
                $time->time = self::convertSecondsToTimeFormat(0, true);
            }
            $time->save();
        }
    }

    public static function recalculate($tracker) {

        /*
         * TODO Two points for solve that problem
         * 1) if tracker has timesheet then recalculate this timesheet with all available trackers
         * 2) if tracker hasn't timesheet then search the same tracker, calculate and create timesheet
         */
        if ($tracker->timesheet_id) {
            $sameTrackers = Tracking::where([
                ['timesheet_id', '=', $tracker->timesheet_id],
                ['user_id', '=', $tracker->user_id]
            ])->get();
            $timeByDay = self::calcTimeByTrackers($sameTrackers);
            self::setTimesheetTime($tracker->timesheet_id, $timeByDay);
        } else {
            $sameTrackers = Tracking::where([
                ['status', '<>', Tracking::$STATUS_ARCHIVED],
                ['user_id', '=', $tracker->user_id],
                ['team_id', '=', $tracker->team_id],
                ['company_id', '=', $tracker->company_id],
//                ['is_manual', '=', $tracker->is_manual],
                ['entity_id', '=', $tracker->entity_id],
                ['entity_type', '=', $tracker->entity_type],
                ['date_from', '>=', Carbon::parse($tracker->date_from)->startOf('weeks')->format(Tracking::$DATE_FORMAT)],
                ['date_to', '<=', Carbon::parse($tracker->date_to)->endOf('weeks')->format(Tracking::$DATE_FORMAT)],
            ])->get();
            $timeByDay = self::calcTimeByTrackers($sameTrackers);
            // search timesheet
            $timesheet = TrackingTimesheet::where([
                ['user_id', '=', $tracker->user_id],
                ['company_id', '=', $tracker->company_id],
                ['team_id', '=', $tracker->team_id],
                ['entity_id', '=', $tracker->entity_id],
                ['entity_type', '=', $tracker->entity_type],
                ['is_manually', '<>', $tracker->is_manual],
                ['from', '=', Carbon::parse($tracker->date_from)->startOf('weeks')->format(Tracking::$DATE_FORMAT)],
                ['to', '=', Carbon::parse($tracker->date_to)->endOf('weeks')->format(Tracking::$DATE_FORMAT)]
            ])->first();
            if (!$timesheet) {
                // create new timesheet
                $timesheet = new TrackingTimesheet();
                $timesheet->user_id = $tracker->user_id;
                $timesheet->company_id = $tracker->company_id;
                $timesheet->team_id = $tracker->team_id;
                $timesheet->entity_id = $tracker->entity_id;
                $timesheet->entity_type = $tracker->entity_type;
                $timesheet->is_manually = false;
                $timesheet->service_id = $tracker->service ? $tracker->service->id : null;
                $timesheet->from = Carbon::parse($tracker->date_from)->startOf('weeks')->format(Tracking::$DATE_FORMAT);
                $timesheet->to = Carbon::parse($tracker->date_to)->endOf('weeks')->format(Tracking::$DATE_FORMAT);
                $timesheet->save();
                $timesheet->genTimes(); // to generating empty time fields
                foreach ($sameTrackers as $track) {
                    $track->timesheet_id = $timesheet->id;
                    $track->save();
                }
                self::setTimesheetTime($timesheet->id, $timeByDay);
            } else {
                // update exists timesheet
                foreach ($sameTrackers as $track) {
                    $track->timesheet_id = $timesheet->id;
                    $track->save();
                }
                self::setTimesheetTime($timesheet->id, $timeByDay);
            }
        }
        return true;
//        $tracking = $tracker;
//        $trackers = Tracking::where([
//            ['status', '<>', Tracking::$STATUS_ARCHIVED],
//            ['user_id', '=', $tracking->user_id],
//            ['team_id', '=', $tracking->team_id],
//            ['company_id', '=', $tracking->company_id],
//            ['is_manual', '=', $tracking->is_manual],
//            ['entity_id', '=', $tracking->entity_id],
//            ['entity_type', '=', $tracking->entity_type],
//            ['date_from', '>=', Carbon::parse($tracking->date_from)->startOf('weeks')->format('Y-m-d')],
//            ['date_to', '<=', Carbon::parse($tracking->date_to)->endOf('weeks')->format('Y-m-d')],
//        ])->get();
//
//        $items = [];
//        if ($trackers->count()) {
//            foreach ($trackers as $tracker) {
//                $timesheet = TrackingTimesheet::where([
//                    ['user_id', '=', $tracker->user_id],
//                    ['company_id', '=', $tracker->company_id],
//                    ['team_id', '=', $tracker->team_id],
//                    ['entity_id', '=', $tracker->entity_id],
//                    ['entity_type', '=', $tracker->entity_type],
//                    ['is_manually', '=', !$tracker->is_manual],
//                    ['from', '>=', Carbon::parse($tracker->date_from)->startOf('weeks')],
//                    ['to', '<=', Carbon::parse($tracker->date_to)->endOf('weeks')]
//                ])->first();
//                if (!$timesheet) {
//                    $timesheet = new TrackingTimesheet();
//                    $timesheet->user_id = $tracker->user_id;
//                    $timesheet->company_id = $tracker->company_id;
//                    $timesheet->team_id = $tracker->team_id;
//                    $timesheet->entity_id = $tracker->entity_id;
//                    $timesheet->entity_type = $tracker->entity_type;
//                    $timesheet->is_manually = false;
//                    $timesheet->from = Carbon::parse($tracker->date_from)->startOf('weeks')->format('Y-m-d');
//                    $timesheet->to = Carbon::parse($tracker->date_to)->endOf('weeks')->format('Y-m-d');
//                    $timesheet->save();
//                    for ($i = 0; $i <= 6; $i++) {
//                        $trackingTimesheetTime = new TrackingTimesheetTime();
//                        $trackingTimesheetTime->timesheet_id = $timesheet->id;
//                        $trackingTimesheetTime->type = TrackingTimesheetTime::TYPE_WORK;
//                        $trackingTimesheetTime->date = Carbon::parse($timesheet->from)->addDays($i)->format('Y-m-d');
//                        $trackingTimesheetTime->time = self::convertSecondsToTimeFormat(0, true);
//                        $trackingTimesheetTime->save();
//                    }
//                }
//                TrackingTimesheetTime::where('timesheet_id', '=', $timesheet->id)->update(['time' => '00:00:00']);
//                Tracking::where('id', '=', $tracker->id)->update(['timesheet_id' => $timesheet->id]);
//                $date = Carbon::parse($tracker->date_from)->format('Y-m-d');
//                $items[$timesheet->id][$date][] = $tracker;
//            }
//        } else {
//            if ($tracking->timesheet_id) {
//                TrackingTimesheetTime::where([
//                    ['timesheet_id', '=', $tracking->timesheet_id],
//                    ['date', '=', Carbon::parse($tracking->date_from)->format('Y-m-d')]
//                ])->update(['time' => '00:00:00']);
//            }
//        }
//
//        foreach ($items as $timesheet_id => $timesheetItems) {
//            foreach ($timesheetItems as $date => $trackers) {
//                $seconds = 0;
//                foreach ($trackers as $tracker) {
//                    $seconds += $tracker->passed;
//                }
//                TrackingTimesheetTime::updateOrCreate([
//                    'timesheet_id' => $timesheet_id,
//                    'type' => TrackingTimesheetTime::TYPE_WORK,
//                    'date' => $date
//                ], [
//                    'time' => self::convertSecondsToTimeFormat($seconds, true),
//                    'timesheet_id' => $timesheet_id,
//                    'type' => TrackingTimesheetTime::TYPE_WORK,
//                    'date' => $date
//                ]);
//            }
//        }
//        return true;
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
        try {
            $time = explode(':', $time);
            return $time[0] * 60 * 60 + $time[1] * 60 + (isset($time[2]) ? $time[2] : 0);
        } catch (\Exception $exception) {
            return 0;
        }
    }

    static function convertSecondsToTimeFormat($value, $withSeconds = true) {
        try {
            $h = floor($value / 60 / 60);
            $m = floor(($value - ($h * 60 * 60)) / 60);
            if ($withSeconds) {
                return sprintf("%02d", $h) . ":" . sprintf("%02d", $m) . ":" . sprintf("%02d", $value % 60);
            }
            return sprintf("%02d", $h) . ":" . sprintf("%02d", $m);
        } catch (\Exception $exception) {
            dd($exception);
        }

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
//            dd($timesheet, $from, $to, $trackers);
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
            return 0;
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

    public function copyPreviousWeek(User $user, $from, $to) {
        $timesheets = TrackingTimesheet::where([
            ['user_id', '=', $user->id],
            ['from', '<=', $from],
            ['to', '>=', $to],
            ['number', '=', null],
        ])->get();
        foreach ($timesheets as $timesheet) {
            $newTimesheet = $timesheet->duplicate();
            $newTimesheet->is_manually = true;
            $newTimesheet->from = Carbon::now()->startOf('week');
            $newTimesheet->to = Carbon::now()->endOf('week');
            $newTimesheet->save();
            $this->genTrackersByTimesheet($newTimesheet);
        }
    }

    public function getUserTemplates() {
        return TrackingTimesheetTemplate::where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')->get();
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

    public function loadTemplate($template_id, $dateStart, $dateEnd) {
        $template = TrackingTimesheetTemplate::where([
            ['id', '=', $template_id],
            ['user_id', '=', Auth::user()->id]
        ])->first();
        if ($template) {
            foreach ($template->data as $item) {
                $parent = $item['parent'];
                $service = $item['service'] ?? null;
                $hours = $item['hours'] ?? null;
                $entity = $item['entity'] ?? null;
                $timesheet = new TrackingTimesheet();
                $timesheet->from = $dateStart;
                $timesheet->to = $dateEnd;
                $timesheet->user_id = $parent['user_id'];
                $timesheet->team_id = $parent['team_id'];
                $timesheet->company_id = $parent['company_id'];
                $timesheet->is_manually = $parent['is_manually'];
                $timesheet->billable = $parent['billable'];
                $timesheet->status = TrackingTimesheet::STATUS_TRACKED;
                $timesheet->approver_id = null;
                if ($entity) {
                    $timesheet->entity_id = $parent['entity_id'];
                    $timesheet->entity_type = $parent['entity_type'];
                } else {
                    $timesheet->entity_id = null;
                    $timesheet->entity_type = null;
                }
                if ($service) {
                    $timesheet->service_id = $parent['service_id'];
                }
                $timesheet->save();
                if ($hours) {
                    foreach (collect($hours)->sortBy('dayOfWeek') as $hour) {
                        $time = new TrackingTimesheetTime();
                        $time->timesheet_id = $timesheet->id;
                        $time->date = Carbon::parse($dateStart)->addDays($hour['dayOfWeek'] - 1)->format('Y-m-d');
                        $time->time = $hour['time'];
                        $time->type = $hour['type'];
                        $time->description = $hour['description'];
                        $time->save();
                    }
                } else {
                    for ($i = 0; $i < 7; $i++) {
                        $time = new TrackingTimesheetTime();
                        $time->timesheet_id = $timesheet->id;
                        $time->date = Carbon::parse($dateStart)->addDays($i)->format('Y-m-d');
                        $time->time = '00:00:00';
                        $time->type = 'work';
                        $time->description = null;
                        $time->save();
                    }
                }
                $this->genTrackersByTimesheet($timesheet);
            }
            return true;
        }
        return false;
    }

    public function removeTemplate($template_id) {
        return TrackingTimesheetTemplate::where([
            ['id', '=', $template_id],
            ['user_id', '=', Auth::user()->id]
        ])->delete();
    }

    private function getData(Request $request) {
        $selected = $request->selected;
        if (count($selected)) {
            $query = TrackingTimesheet::with('User')
                ->with('Service')
                ->with('Approver')
                ->with(['Times' => function ($q) {
                    $q->orderBy('date', 'asc');
                }]);
            if ($request->status > 0) {
                $query->whereIn('number', TrackingTimesheet::whereIn('id', $selected)->pluck('number')->all());
            } else {
                $query->whereIn('id', TrackingTimesheet::whereIn('id', $selected)->pluck('id')->all());
            }
            return $query->get();
        }
        return [];
//        $statusesForFilter = [];
//        if ($request->has('pending') && !is_null($request->pending)) {
//            $statusesForFilter[] = TrackingTimesheet::STATUS_PENDING;
//        }
//        if ($request->has('rejected') && !is_null($request->rejected)) {
//            $statusesForFilter[] = TrackingTimesheet::STATUS_REJECTED;
//        }
//        if ($request->has('archived') && !is_null($request->archived)) {
//            $statusesForFilter[] = TrackingTimesheet::STATUS_ARCHIVED;
//        }
//        if ($request->has('tracked') && !is_null($request->tracked)) {
//            $statusesForFilter[] = TrackingTimesheet::STATUS_TRACKED;
//        }
//        $query = TrackingTimesheet::with('User')
//            ->with('Service')
//            ->with('Approver')
//            ->with(['Times' => function ($q) {
//                $q->orderBy('date', 'asc');
//            }])
//            ->whereIn('status', $statusesForFilter);
//
//        if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_TEAM_TIME_ACCESS)) {
//            // Manager
//            $teams = $teams = Team::whereHas('employees', function ($q) {
//                return $q
//                    ->where('company_user_id', '=', Auth::user()->employee->id)
//                    ->where('is_manager', '=', true);
//            })->get()->pluck('id')->toArray();
//            $query->where(function ($query) use ($teams) {
//                    $query
//                        ->whereIn('team_id', $teams)
//                        ->whereNull('approver_id')
//                        ->orWhere('approver_id', '=', Auth::user()->id);
//                });
//        } else if (Auth::user()->employee->hasPermissionId(Permission::TRACKER_VIEW_COMPANY_TIME_ACCESS)) {
//            // Company Admin
//            $company = Auth::user()->employee()
//                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
//                ->with('userData')->first();
//            $query->where('company_id', '=', $company->company_id)
//                ->where(function ($query) {
//                    $query->whereNull('approver_id')
//                        ->orWhere('approver_id', '=', Auth::user()->id);
//                });
//        } else {
//            $query->where(function($q) {
//                $q->where('user_id', '=', Auth::user()->id)
//                    ->orWhere('approver_id', '=', Auth::user()->id);
//            });
//        }
//        $query->where(function($q) use ($request) {
//            $q->where('from', '<=', $request->to)
//                ->where('to', '>=', $request->from);
//        });
//        $query->orderBy('id', 'desc');
////        dd($query->toSql(), $query->getBindings());
//        return $query->get();
    }

    protected function prepareDataForPDF($entities) {
        $items = [];
        $dayOfWeekTime = [];
        foreach ($entities as $key => $entity) {
            $item = [
                'number' => $entity->number ?? '',
                'name' => $entity->entity ? $entity->entity->name : '',
                'start' => Carbon::parse($entity['from'])->format('d.m.Y'),
                'end' => Carbon::parse($entity['to'])->format('d.m.Y'),
                'service' => $entity->service ? $entity->service->name : '',
                'status' => $entity->status,
            ];
            foreach ($entity->times as $time) {
                if (!isset($dayOfWeekTime[Carbon::parse($time->date)->shortEnglishDayOfWeek])) {
                    $dayOfWeekTime[Carbon::parse($time->date)->shortEnglishDayOfWeek] = "00:00:00";
                }
                $dayOfWeekTime[Carbon::parse($time->date)->shortEnglishDayOfWeek] =
                    self::convertSecondsToTimeFormat(
                        self::convertTimeToSeconds($dayOfWeekTime[Carbon::parse($time->date)->shortEnglishDayOfWeek])
                        + self::convertTimeToSeconds($time->time), false
                    );
                $item[Carbon::parse($time->date)->shortEnglishDayOfWeek] = Carbon::parse($time->time)->format('H:i');
            }
            array_push($items, $item);
        }
        $items[] = array_merge([
            'number' => '',
            'name' => 'Totals',
            'start' => '',
            'end' => '',
            'service' => '',
            'status' => '',
        ], $dayOfWeekTime);
        $items[] = [
            'number' => '',
            'name' => 'Total time per timesheet',
            'start' => '',
            'end' => '',
            'service' => '',
            'status' => '',
            'mon' => '',
            'tue' => '',
            'wed' => '',
            'thu' => '',
            'fri' => '',
            'sat' => '',
            'sun' => self::convertSecondsToTimeFormat(collect($dayOfWeekTime)->map(function($item) { return self::convertTimeToSeconds($item); })->sum(), false)
        ];
        return $items;
    }

    public function exportPdf(Request $request) {
        $headers = [
            ['slug' => 'number', 'text' => 'Number', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => false],
            ['slug' => 'name', 'text' => 'Name', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 20, 'resizable' => true],
            ['slug' => 'start', 'text' => 'Start', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 10, 'resizable' => false],
            ['slug' => 'end', 'text' => 'End', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 10, 'resizable' => false],
            ['slug' => 'service', 'text' => 'Service', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 10, 'resizable' => true],
            ['slug' => 'status', 'text' => 'Status', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 10, 'resizable' => true],
            ['slug' => 'mon', 'text' => 'Mon', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
            ['slug' => 'tue', 'text' => 'Tue', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
            ['slug' => 'wed', 'text' => 'Wed', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
            ['slug' => 'thu', 'text' => 'Thu', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
            ['slug' => 'fri', 'text' => 'Fri', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
            ['slug' => 'sat', 'text' => 'Sat', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
            ['slug' => 'sun', 'text' => 'Sun', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => true],
        ];
        $entities = $this->getData($request);
        $data = $this->prepareDataForPDF($entities);

        $ts = $entities[0];

        $pdf = new PDF();
        $pdf->setFirstPage(0);
        $pdf->SetOptions([
            'title' => 'Timesheet report #' . $ts->number,
            'user' => $ts->user->full_name,
            'period' => Carbon::parse($ts->from)->format('d.m.Y') . ' - ' . Carbon::parse($ts->to)->format('d.m.Y'),
            'status' => 'Status of timesheet: ' . $ts->status,
            'approver' => "Approver: " . ($ts->approver ? $ts->approver->full_name : ''),
        ]);
        $pdf->AliasNbPages();

        $pdf->AddPage('L', 'A4');
        $pdf->SetFont('Arial', '', 10);
        $pdf->EasyTable($headers, $data);

        // GENERATE FILE
        try {
            $tmpFileName = storage_path('app') . Auth::id() . '-' . time() . '.pdf';
            File::put($tmpFileName, $pdf->Output('S', $tmpFileName, true));
            if (File::exists($tmpFileName)) {
                return response()->file($tmpFileName)->deleteFileAfterSend();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
        throw new \Exception('Error generating file');
    }
}
