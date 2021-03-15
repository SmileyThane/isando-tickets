<?php


namespace App\Repository;


use App\Tracking;
use Carbon\Carbon;
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
                if (is_array($item['selected']) && count($item['selected'])) {
                    return true;
                } elseif (is_numeric($item['selected'])) {
                    return true;
                }
                return false;
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

//        if ($grouping->isNotEmpty()) {
//            $fields = collect(['id', 'description', 'user_id', 'project_id', 'date_from', 'date_to', 'status', 'billable', 'billed', 'created_at', 'updated_at']);
//            foreach ($grouping as $group) {
//                switch ($group) {
//                    case 'day':
//                        // TODO
//                        break;
//                    case 'week':
//                        // TODO
//                        break;
//                    case 'month':
//                        // TODO
//                        break;
//                    case 'description':
//                        $tracking->groupBy('tracking.description');
//                        $fields = $fields->filter(function ($item) use ($group) {
//                            return $item !== $group;
//                        });
//                        break;
//                    case 'billability':
//                        $tracking->groupBy('tracking.billable');
//                        $fields = $fields->filter(function ($item) use ($group) {
//                            return $item !== 'billable';
//                        });
//                        break;
//                }
//            }
//            foreach ($fields as $field) {
//                $tracking->groupBy("tracking.{$field}");
//            }
//        }

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
//                        dd($filter['selected']);
                        $tracking->where('tracking.billable', '=', (int)$filter['selected']);
                        break;
                }
            }
        }

        $tracks = $tracking->get();
//            ->map(function ($item) {
//                $obj = new \stdClass();
//                $obj->id = $item->id;
//                $obj->description = $item->description;
//                $obj->date_from = $item->date_from;
//                $obj->date_to = $item->date_to;
//                $obj->billable = $item->billable;
//                return $obj;
//            });

        if ($grouping->isEmpty()) return $tracks;

        $group = $grouping->shift();
        $items = $this->makeList($group, $tracks);

        return array_values($this->makeStructure($grouping->toArray(), 0, $items));

    }

    protected function getData($tracking, $field = 'description') {
        switch ($field) {
            case 'month':
                return Carbon::parse($tracking->date_from)->format('F Y');
            case 'week':
                $startWeek = Carbon::parse($tracking->date_from)->startOfWeek(Carbon::MONDAY);
                $endWeek = Carbon::parse($tracking->date_from)->endOfWeek(Carbon::SUNDAY);
                return 'Week of '
                    . $startWeek->format('j M')
                    . ' ('
                        . $startWeek->format('D j M Y')
                        . ' - '
                        . $endWeek->format('D j M Y')
                    . ')';
            case 'day':
                return Carbon::parse($tracking->date_from)->format('l, j M');
            case 'description':
                return $tracking->description ?? 'None';
            case 'billability':
                return $tracking->billable ? 'Billable' : 'Non-billable';
        }
        return null;
    }

    protected function makeList($group, $trackings) {
        $items = [];
        foreach ($trackings as $tracking) {
            $data = $this->getData($tracking, $group);
            $items[$data]['name'] = $data;
            $items[$data]['children'][] = $tracking;
        }
        return $items;
    }

    protected function makeStructure($grouping, $groupIndex, $tracks) {
        if (!isset($grouping[$groupIndex])) return $tracks;
        foreach ($tracks as $key => $track) {
            $list = $this->makeList($grouping[$groupIndex], $track['children']);
            $tracks[$key]['name'] = $key;
            $tracks[$key]['children'] = array_values($this->makeStructure($grouping, $groupIndex+1, $list));
        }
        return array_values($tracks);
    }

    public function genPDF($request) {
        return '';
    }

    public function genCSV($request) {
        return '';
    }
}
