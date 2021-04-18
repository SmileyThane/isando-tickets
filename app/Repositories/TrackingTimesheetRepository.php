<?php


namespace App\Repositories;

use App\Team;
use App\TrackingTimesheet;
use App\TrackingTimesheetTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingTimesheetRepository
{
    public function validate(Request $request) {
        return true;
    }

    public function all(Request $request) {
        $query = TrackingTimesheet::with('Project.Client')
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
        return $query
            ->where('user_id', '=', Auth::user()->id)
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

        return $timesheet;
    }

    public function update(Request $request, $id) {
        return null;
    }

    public function delete($id) {
        return null;
    }

}
