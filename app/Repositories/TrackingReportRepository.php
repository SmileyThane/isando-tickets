<?php


namespace App\Repositories;

use App\Client;
use App\Http\Controllers\API\Tracking\PDF;
use App\Ticket;
use App\Tracking;
use App\TrackingProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

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
            'filters.*.value'       => 'string|in:coworkers,projects,clients,services,billable'
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    protected function getData(Request $request) {
        $sorting        = $request->sort['value'];
        $grouping       = $request->group;
        if (isset($request->group) && isset($request->group['value']) && $request->group['value'] === 'custom') {
            $grouping = $request->group['items'];
        }
        if (isset($request->group) && isset($request->group['value']) && in_array($request->group['value'], ['all_no_group', 'all_chron'])) {
            $grouping = [];
        }
        $grouping       = collect($grouping)->map(function ($item) { return $item['value']; });
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

        $tracking = Tracking::with('User.employee.assignedToTeams');

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
                    case 'projects':
                        $projectIds = TrackingProject::whereIn('id', $filter['selected'])->pluck('id')->all();
                        $tracking->whereIn('entity_id', $projectIds)
                                 ->where('entity_type', TrackingProject::class);
                        break;
                    case 'clients':
                        $clients = Client::whereIn('id', $filter['selected'])->pluck('id')->all();
                        $projectIds = TrackingProject::whereIn('client_id', $clients)->pluck('id')->all();
                        $ticketIds = Ticket::where('to_entity_type', Client::class)
                            ->whereIn('to_entity_id', $clients)
                            ->pluck('id')
                            ->all();

//                        dd($clients, $projectIds, $ticketIds);
                        $tracking
                            // project
                            ->where(function($query) use ($projectIds) {
                                return $query
                                    ->where('entity_type', TrackingProject::class)
                                    ->whereIn('entity_id', $projectIds);
                            })
                            // ticket
                            ->orWhere(function($query) use ($ticketIds) {
                                return $query
                                    ->where('entity_type', Ticket::class)
                                    ->whereIn('entity_id', $ticketIds);
                            });

//                        $tracking->whereHas('Entity.Client', function ($query) use ($filter) {
//                            $query->whereIn('clients.id', $filter['selected']);
//                        });
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

        return [
            'tracks' => $tracks,
            'grouping' => $grouping
        ];
    }

    public function generate(Request $request) {
        $result = $this->getData($request);
        $grouping = $result['grouping'];
        $tracks = $result['tracks'];

        if ($grouping->isEmpty()) return $tracks;

        $group = $grouping->shift();
        $items = $this->makeList($group, $tracks);

        return array_values($this->makeStructure($grouping->toArray(), 0, $items));

    }

    protected function getFieldData($tracking, $field = 'description') {
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
            case 'service':
                return $tracking->service ? $tracking->service->name : 'None';
            case 'project':
                return $tracking->entity ? $tracking->entity->name : 'None';
            case 'client':
                return $tracking && $tracking->entity && $tracking->entity->client ? $tracking->entity->client->name : 'None';
            case 'coworker':
                return $tracking->user->full_name;
        }
        return null;
    }

    protected function makeList($group, $trackings) {
        $items = [];
        foreach ($trackings as $tracking) {
            $data = $this->getFieldData($tracking, $group);
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

    protected function prepareDataForPDF($entities) {
        $items = [];
        foreach ($entities as $entity) {
//            dd($entity->entity);
            $item = [
                'date' => Carbon::parse($entity->date_from)->format('d M Y'),
                'start' => Carbon::parse($entity->date_from)->format('H:i'),
                'end' => Carbon::parse($entity->date_to)->format('H:i'),
                'total' => $this->convertSecondsToTimeFormat(Carbon::parse($entity->date_to)->diffInSeconds($entity->date_from)),
                'coworker' => $entity->user->full_name,
                'customer' => isset($entity->entity) && isset($entity->entity->client) ? $entity->entity->client->name : '',
                'project' => isset($entity->entity) ? $entity->entity->name : '',
                'service' => isset($entity->service) ? $entity->service->name : '',
                'billable' => $entity->billable ? 'Yes' : 'No',
                'amount' => $entity->amount
            ];
            array_push($items, $item);
        }
        return $items;
    }

    function convertSecondsToTimeFormat($value) {
        $result = [];
//        $M = floor($value /2592000);
//        if (!empty($M)) {
//            $result[] = $M . ' months,';
//        }
//        $d = floor(($value %2592000)/86400);
//        if (!empty($d)) {
//            $result[] = $d . ' days,';
//        }
        $h = floor(($value %86400)/3600);
        $m = floor(($value %3600)/60);
        $result[] = sprintf("%02d", $h) . ":" . sprintf("%02d", $m); // . ":" . sprintf("%02d", $value % 60);

        return implode(', ', $result);
    }

    public function genPDF($request, $htmlFormat = false) {

        // Pre-define some variables
        $reportName = $request->get('name', 'Report');
        $headers = [
            ['text' => 'Date', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Start', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'End', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Total', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Co-worker', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Customer', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Project', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Service', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Billable', 'style' => 'border:B;border-width:1;font-style:B'],
            ['text' => 'Amount', 'style' => 'border:B;border-width:1;font-style:B']
        ];
        $tracks = $this->getData($request)['tracks'];
        $fullTime = collect($tracks)->sum(function ($item) {
            return Carbon::parse($item->date_from)->diffInSeconds(Carbon::parse($item->date_to));
        });
        $data = $this->prepareDataForPDF($tracks);
        $user = Auth::user();
        $period = $request->get('periodText');
        $coworkers = $request->get('coworkers');
        if (empty($coworkers)) $coworkers = $user->full_name;

        $pdf = new PDF();
        $pdf->SetOptions([
            'title' => $reportName,
            'user' => $user->full_name,
            'period' => $period,
        ]);
        $pdf->AliasNbPages();

        $y = 0;
        // PAGE 1. TITLE
        $pdf->AddPage('P', 'A4');
        $y += 60;
        // Report name
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($pdf->GetCenterX($reportName, $pdf->GetPageWidth()) , $y);
        $pdf->Write(3, $reportName);

        // Co-workers
        $y += 10;
        $pdf->SetFont('Arial', 'B', 32);
        $lines = $pdf->GetCountLines($coworkers, $pdf->GetPageWidth());
        if ($lines > 1) {
            $pdf->SetXY(30, $y);
        } else {
            $pdf->SetXY($pdf->GetCenterX($coworkers, $pdf->GetPageWidth()), $y);
        }
        $pdf->Write(10, $coworkers);
        $y += 10 * $lines;

        // Period
        $y += 10;
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($pdf->GetCenterX($period, $pdf->GetPageWidth()), $y);
        $pdf->Write(3, $period);

        // Total time
        $y += 30;
        $totalTimeTitle = 'Total time';
        $totalTime = $this->convertSecondsToTimeFormat($fullTime);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($pdf->GetCenterX($totalTimeTitle, $pdf->GetPageWidth()), $y);
        $pdf->Write(3, $totalTimeTitle);
        $x = $pdf->GetX();
        $y += 5;
        $pdf->Line($x - 20, $y, $x + 5, $y);
        $y += 5;
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($pdf->GetCenterX($totalTime, $pdf->GetPageWidth()), $y);
        $pdf->Write(3, $totalTime);

        // Created At
        $y += 20;
        $createdAt = 'Created at ' . Carbon::now()->format('d F Y H:i') . ' o\' clock';
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($pdf->GetCenterX($createdAt, $pdf->GetPageWidth()), $y);
        $pdf->Write(3, $createdAt);

        // PAGE 2 and next
        $pdf->AddPage('L', 'A4');
        $pdf->SetFont('Arial', '', 10);
        $pdf->EasyTable($headers, $data);

        $html = '';
        // GENERATE FILE
        try {
            $tmpFileName = storage_path('app') . Auth::id() . '-' . time() . '.pdf';
            File::put($tmpFileName, $pdf->Output('', '', true));
            if (File::exists($tmpFileName)) {
                return response()->file($tmpFileName)->deleteFileAfterSend();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
        throw new \Exception('Error generating file');
    }


    protected function prepareDataForCSV($entities, $items = []) {
        $entities = (array)$entities;
        foreach ($entities as $entity) {
            if (isset($entity['children'])) {
                Log::debug($entity['name']);
                $items = array_merge($items, $this->prepareDataForCSV($entity['children']));
            } else {
                Log::debug($entity['id']);
                array_push($items, $entity);
            }
        }
        return $items;
    }

    protected function getDataCSV($tracking) {
        try {
            $row = [];
            $row[] = $tracking['user'] ? $tracking['user']['full_name'] : '';
            $row[] = $tracking['user'] ? $tracking['user']['id'] : '';
            if ($tracking['entity'] && isset($tracking['entity']['from'])) {
                $row[] = $tracking['entity'] && $tracking['entity']['from_company_name'] ? $tracking['entity']['from_company_name'] : '';
                $row[] = $tracking['entity'] && $tracking['entity']['from']['id'] ? $tracking['entity']['from']['id'] : '';
            } else {
                $row[] = $tracking['entity'] && $tracking['entity']['client'] ? $tracking['entity']['client']['name'] : '';
                $row[] = $tracking['entity'] && $tracking['entity']['client'] ? $tracking['entity']['client']['id'] : '';
            }
            $row[] = $tracking['entity'] ? $tracking['entity']['name'] : '';
            $row[] = $tracking['entity'] ? $tracking['entity']['id'] : '';
            $row[] = $tracking['service'] ? $tracking['service']['name'] : '';
            $row[] = $tracking['service'] ? $tracking['service']['id'] : '';
            $row[] = 0;
            $row[] = '';
            $row[] = $tracking['description'];
            $row[] = $tracking['billable'] ? 1 : 0;
            $row[] = 0;
            $row[] = round(0, 2);
            $row[] = Carbon::parse($tracking['date_from'])->format('Y-m-d H:i:s');
            $row[] = Carbon::parse($tracking['date_to'])->format('Y-m-d H:i:s');
            $row[] = 0;
            $row[] = 0;
            $row[] = 0;
            $row[] = 0;
            $row[] = 0;
            return implode(';', $row);
        } catch (\Exception $exception) {
            dd($exception->getMessage(), $exception->getLine(), $tracking);
        }
    }

    protected function getHeaderCSV() {
        return implode(';', [
            'Co-worker',
            'Personnel number',
            'Customer',
            'Customer number',
            'Project',
            'Project number',
            'Service/Lump sum',
            'Service/Lump sum number',
            'Amount (Lump sums)',
            'Unit (Lump sums)',
            'Description',
            'Billable',
            'Billed',
            'Hourly rate in CHF',
            'Start',
            'End',
            'Amount',
            'Correction of time in seconds',
            'Time clocked',
            'Clocked offline',
            'Revenue in CHF'
        ]) . "\n";
    }

    public function genCSV($request) {
        if (isset($request->group) && $request->group['value'] === 'custom') {
            $data = $this->generate($request);
        } else {
            $data = $this->getData($request)['tracks']->toArray();
        }

        $result = $this->prepareDataForCSV($data);
        foreach ($result as $key => $item) {
            $result[$key] = $this->getDataCSV($item);
        }

        return $this->getHeaderCSV() . implode("\n", $result);
    }
}
