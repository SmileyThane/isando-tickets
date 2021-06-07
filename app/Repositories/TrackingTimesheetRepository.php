<?php


namespace App\Repositories;

use App\Notifications\TimesheetAppovalRequest;
use App\Notifications\TimesheetApproved;
use App\Notifications\TimesheetRejected;
use App\Team;
use App\Tracking;
use App\TrackingProject;
use App\TrackingTimesheet;
use App\TrackingTimesheetTime;
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
        $query = TrackingTimesheet::with('Project.Client')
            ->with('User')
            ->with('Approver')
            ->with(['Times' => function ($q) {
                $q->orderBy('date', 'asc');
            }]);
//        if ($request->has('project') && !in_array($request->get('project', "0"), ["0", "null"])) {
//            $query->where('project_id', '=', $request->get('project', null));
//        }
//        if ($request->has('type')) {
//            switch ($request->get('type', '0')) {
//                case '0': $query->where('status', '=', TrackingTimesheet::STATUS_TRACKED); break;
//                case '1': $query->where('status', '=', TrackingTimesheet::STATUS_PENDING); break;
//                case '2': $query->where('status', '=', TrackingTimesheet::STATUS_UNSUBMITTED); break;
//                case '3': $query->where('status', '=', TrackingTimesheet::STATUS_ARCHIVED); break;
//            }
//        }
//        dd($query->toSql(), $query->getBindings());
        $teams = \App\Team::whereHas('employees', function ($query) {
            return $query->where('company_user_id', '=', Auth::user()->employee->id)
                ->where('is_manager', '=', true);
        })->pluck('id');
        if ($teams->count()) {
            $query->where(function($q) use ($teams) {
                $q->where('user_id', '=', Auth::user()->id)
                    ->orWhereIn('team_id', $teams);
            });
        } else {
            $query->where('user_id', '=', Auth::user()->id);
        }
        return $query
            ->where(function ($q) use ($request) {
                $q->whereBetween('from', [Carbon::parse($request->start), Carbon::parse($request->end)])
                    ->orWhereBetween('to', [Carbon::parse($request->start), Carbon::parse($request->end)]);
            })
            ->get();
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
        $project = $request->get('project');
        $timesheet->project_id = $project['id'];
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

        return TrackingTimesheet::with('User')
            ->with('Approver')
            ->where('id', '=', $timesheet->id)->with('Project.Client')
            ->with(['Times' => function ($q) {
                $q->orderBy('date', 'asc');
            }])->first();
    }

    public function update(Request $request, $id) {
        $timesheet = TrackingTimesheet::findOrFail($id);
        $timesheet->project_id = $request->get('project_id', null);
        $timesheet->billable = $request->get('billable', false);
        $timesheet->save();
        $items = $request->get('times');
        foreach ($items as $item) {
            TrackingTimesheetTime::updateOrCreate(
                ['id' => $item['id'], 'timesheet_id' => $item['timesheet_id']],
                ['date' => $item['date'], 'time' => $item['time']]
            );
        }
        return TrackingTimesheet::with('User')
            ->with('Approver')
            ->where('id', '=', $id)->with('Project.Client')
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
        foreach ($ids as $id) {

            $approverId = null;
            if (in_array($request->get('status'), [TrackingTimesheet::STATUS_PENDING])) {
                $approverId = $request->get('approver_id', null);
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
                ->with('Project.Client')
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
        if ($tracking->entity_type !== TrackingProject::class || is_null($tracking->entity_id))
            return false;

        $trackers = Tracking::where([
            ['status', '<>', Tracking::$STATUS_ARCHIVED],
            ['user_id', '=', $tracking->user_id],
            ['team_id', '=', $tracking->team_id],
            ['company_id', '=', $tracking->company_id],
            ['entity_id', '=', $tracking->entity_id],
            ['entity_type', '=', $tracking->entity_type],
            ['date_from', '>=', Carbon::parse($tracking->date_from)->startOf('weeks')->format('Y-m-d')],
            ['date_to', '<=', Carbon::parse($tracking->date_to)->endOf('weeks')->format('Y-m-d')],
        ])->get();

        $items = [];
        foreach ($trackers as $tracker) {
            $timesheet = TrackingTimesheet::where([
                ['user_id', '=', $tracker->user_id],
                ['company_id', '=', $tracker->company_id],
                ['team_id', '=', $tracker->team_id],
                ['project_id', '=', $tracker->entity_id],
                ['is_manually', '=', false],
                ['from', '>=', Carbon::parse($tracker->date_from)->startOf('weeks')],
                ['to', '<=', Carbon::parse($tracker->date_to)->endOf('weeks')]
            ])->first();
            if (!$timesheet) {
                $timesheet = new TrackingTimesheet();
                $timesheet->user_id = $tracker->user_id;
                $timesheet->company_id = $tracker->company_id;
                $timesheet->team_id = $tracker->team_id;
                $timesheet->project_id = $tracker->entity_id;
                $timesheet->is_manually = false;
                $timesheet->from = Carbon::parse($tracker->date_from)->startOf('weeks')->format('Y-m-d');
                $timesheet->to = Carbon::parse($tracker->date_to)->endOf('weeks')->format('Y-m-d');
                $timesheet->save();
                for ($i = 0; $i <= 6; $i++) {
                    $trackingTimesheetTime = new TrackingTimesheetTime();
                    $trackingTimesheetTime->timesheet_id = $timesheet->id;
                    $trackingTimesheetTime->type = TrackingTimesheetTime::TYPE_WORK;
                    $trackingTimesheetTime->date = Carbon::parse($timesheet->from)->add($i, 'days')->format('Y-m-d');
                    $trackingTimesheetTime->time = self::convertSecondsToTimeFormat(0, true);
                    $trackingTimesheetTime->save();
                }
            }
            TrackingTimesheetTime::where('timesheet_id', '=', $timesheet->id)->update(['time' => '00:00:00']);
            Tracking::where('id', '=', $tracker->id)->update(['timesheet_id' => $timesheet->id]);
            $date = Carbon::parse($tracker->date_from)->format('Y-m-d');
            $items[$timesheet->id][$date][] = $tracker;
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

    static function convertSecondsToTimeFormat($value, $withSeconds = true) {
        $h = floor($value / 60 / 60);
        $m = floor(($value - ($h * 60 * 60)) / 60);
        if ($withSeconds) {
            return sprintf("%02d", $h) . ":" . sprintf("%02d", $m) . ":" . sprintf("%02d", $value % 60);
        }
        return sprintf("%02d", $h) . ":" . sprintf("%02d", $m);
    }

}
