<?php


namespace App\Repository;


use App\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackingReportRepository
{
    public function validate($request, $new = true)
    {
        $params = [
            'period'                => 'required|array',
            'period.start'          => 'required_without:period.end|date|nullable',
            'period.end'            => 'required|date',
            'sort'                  => 'required|array',
            'sort.value'            => 'required|string|in:alph-asc,chron-desc,duration-desc,duration-asc,revenue-desc,revenue-asc',
            'round'                 => 'required|integer',
            'group'                 => 'array',
            'group.*.value'         => 'string|in:day,description,week,billability,month',
            'filters'               => 'array',
            'filters.*.value'       => 'string|in:coworkers,clients&projects,clients,services,billable'
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function generate(Request $request) {
        $periodStart    = $request->period['start'];
        $periodEnd      = $request->period['end'];
        $round          = $request->round;
        $sorting        = $request->sort['value'];
        $grouping       = collect($request->group)->map(function ($item) { return $item['value']; });
        $filtering      = collect($request->filters)
            ->map(function($item) {
                return [
                    'value' => $item['value'],
                    'selected' => $item['selected']
                ];
            })
            ->filter(function ($item) {
                return $item['selected'];
            });

        $tracking = Tracking::with('Project.Client')
            ->with('User.employee.assignedToTeams');

        if ($request->has('period') && isset($request->period['start'])) {
            $tracking->where('tracking.date_from', '>=', $request->period['start']);
        }
        if ($request->has('period') && isset($request->period['end'])) {
            $tracking->where('tracking.date_to', '<=', $request->period['end']);
        }

        switch ($sorting) {
            case 'alph-asc':
                $tracking
                    ->orderBy('tracking.date_from', 'asc')
                    ->orderBy('tracking.date_to', 'asc');
                break;
            case 'chron-desc':
                $tracking
                    ->orderBy('tracking.date_to', 'desc')
                    ->orderBy('tracking.date_from', 'desc');
                break;
            case 'duration-desc':
                // TODO
                break;
            case 'duration-asc':
                // TODO
                break;
            case 'revenue-asc':
                // TODO
                break;
            case 'revenue-desc':
                // TODO
                //break;
        }

        if ($grouping->isNotEmpty()) {
            $fields = collect(['id', 'description', 'user_id', 'project_id', 'date_from', 'date_to', 'status', 'billable', 'billed', 'created_at', 'updated_at']);
            foreach ($grouping as $group) {
                switch ($group) {
                    case 'day':
                        // TODO
                        break;
                    case 'week':
                        // TODO
                        break;
                    case 'month':
                        // TODO
                        break;
                    case 'description':
                        $tracking->groupBy('tracking.description');
                        $fields = $fields->filter(function ($item) use ($group) {
                            return $item !== $group;
                        });
                        break;
                    case 'billability':
                        $tracking->groupBy('tracking.billable');
                        $fields = $fields->filter(function ($item) use ($group) {
                            return $item !== 'billable';
                        });
                        break;
                }
            }
            foreach ($fields as $field) {
                $tracking->groupBy("tracking.{$field}");
            }
        }

        if ($filtering->isNotEmpty()) {
            foreach ($filtering as $filter) {
                switch ($filter['value']) {
                    case 'coworkers':
                        $tracking->whereIn('tracking.user_id', $filter['selected']);
                        break;
                    case 'clients&projects':
                        $tracking->whereHas('Project', function ($query) use ($filter) {
                            $query->whereIn('tracking.project_id', $filter['selected']);
                        });
                        break;
                    case 'clients':
                        $tracking->whereHas('Project.Client', function ($query) use ($filter) {
                            $query->whereIn('clients.id', $filter['selected']);
                        });
                        break;
                    case 'services':
                        $tracking->whereHas(
                            'Services',
                            function ($query) use ($filter) {
                                $query->whereIn('serviceable.service_id', $filter['selected']);
                            }
                        );
                        break;
                    case 'billable':
                        $tracking->where('tracking.billable', '=', $filter['selected']);
                        break;
                }
            }
        }

        return $tracking->get();
    }

}
