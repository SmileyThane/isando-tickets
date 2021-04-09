<?php


namespace App\Repositories;

use App\Client;
use App\Http\Controllers\API\Tracking\PDF;
use App\Notifications\SendTrackingReportByEmail;
use App\Tag;
use App\Ticket;
use App\Tracking;
use App\TrackingProject;
use App\TrackingReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class TrackingReportRepository
{
    protected $timeInDecimal = false;
    protected $round = 0;
    protected $settings = [];
    protected $company;
    protected $currency = 'EUR';

    public function all(Request $request) {
        return Auth::user()->trackingReports()->orderBy('id', 'desc')->get();
    }

    public function find($reportId) {
        return Auth::user()->trackingReports()->find($reportId);
    }

    public function create(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'configuration' => 'required'
        ]);

        $report = new TrackingReport();
        $report->name = $request->get('name');
        $report->configuration = $request->get('configuration');
        $user = Auth::user();
        $user->trackingReports()->save($report);
        return $report;
    }

    public function delete($id) {
        $report = Auth::user()->trackingReports()->findOrFail($id);
        $report->delete();
    }

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
        $this->company = Auth::user()->employee->companyData;
        $this->currency = $this->company && $this->company->currency ? $this->company->currency->slug : $this->currency;
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

        $tracking = Tracking::with('User.employee.assignedToTeams')
            ->with('tags');

        if ($request->has('period') && isset($request->period['start'])) {
            $tracking->where('tracking.date_from', '>=', $request->period['start']);
        }
        if ($request->has('period') && isset($request->period['end'])) {
            $tracking->where(function($query) use ($request) {
                $query
                    ->where('tracking.date_to', '<=', $request->period['end'])
                    ->orWhereNull('tracking.date_to');
            });
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
        }

        if ($filtering->isNotEmpty()) {
            foreach ($filtering as $filter) {
                switch ($filter['value']) {
                    case 'coworkers':
                        $tracking->whereIn('user_id', $filter['selected']);
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

                        $tracking->where(function($query) use ($projectIds, $ticketIds) {
                            // project
                            if (count($projectIds)) {
                                $query->where(function($q) use ($projectIds) {
                                    return $q
                                        ->where('entity_type', '=', TrackingProject::class)
                                        ->whereIn('entity_id', $projectIds);
                                });
                            }
                            // ticket
                            if (count($ticketIds)) {
                                if (count($projectIds)) {
                                    $query->where(function($q) use ($ticketIds) {
                                        return $q
                                            ->where('entity_type', '=', Ticket::class)
                                            ->whereIn('entity_id', $ticketIds);
                                    });
                                } else {
                                    $query->orWhere(function($q) use ($ticketIds) {
                                        return $q
                                            ->where('entity_type', '=', Ticket::class)
                                            ->whereIn('entity_id', $ticketIds);
                                    });
                                }
                            }
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
                        $tracking->where('tracking.billable', '=', (int)$filter['selected']);
                        break;
                    case 'tag':
                        $tracking->whereHas('Tags', function($query) use ($filter) {
                            $query->whereIn('tags.id', $filter['selected']);
                        });
                        break;
                }
            }
        }

        $tracks = $tracking->get()->toArray();

        $this->round = $request->get('round', 0);
        $round = $this->round;

        if ($round > 0) {
            $tracks = collect($tracks)->map(function ($track) use ($round) {
                $time = $this->convertSecondsToTimeFormat($track['passed']);
                $roundedTime = $this->getRoundTime($time, $round);
                $passed = $this->convertTimeToSeconds($roundedTime);
                $track['passed'] = $passed;
                if ($track['billable']) {
                    $track['revenue'] = number_format((float)$track['rate'] * (float)$this->convertSecondsToDecimal((float)$passed), 2, '.', '');
                }
                return $track;
            });
        }

        return [
            'tracks' => $tracks,
            'grouping' => $grouping
        ];
    }

    function getRoundTime($time, $minutes = '5') {
        if ($minutes === 0) return $time;
        $time = explode(':', $time);
        $seconds = (int)$time[0]*60*60 + (int)$time[1]*60;
        if (isset($time[2])) $seconds += (int)$time[2];
        $rounded = round((int)$seconds / ((int)$minutes * 60)) * ((int)$minutes * 60);
//        dd($time, $minutes, $rounded, $this->convertSecondsToTimeFormat($rounded));
        return $this->convertSecondsToTimeFormat($rounded);
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
                return Carbon::parse($tracking['date_from'])->format('F Y');
            case 'week':
                $startWeek = Carbon::parse($tracking['date_from'])->startOfWeek(Carbon::MONDAY);
                $endWeek = Carbon::parse($tracking['date_from'])->endOfWeek(Carbon::SUNDAY);
                return 'Week of '
                    . $startWeek->format('j M')
                    . ' ('
                        . $startWeek->format('D j M Y')
                        . ' - '
                        . $endWeek->format('D j M Y')
                    . ')';
            case 'day':
                return Carbon::parse($tracking['date_from'])->format('l, j M');
            case 'description':
                return $tracking['description'] ?? 'None';
            case 'billability':
                return $tracking['billable'] ? 'Billable' : 'Non-billable';
            case 'service':
                return $tracking['service'] ? $tracking['service']['name'] : 'None';
            case 'project':
                return $tracking['entity'] ? $tracking['entity']['name'] : 'None';
            case 'client':
                return isset($tracking) && isset($tracking['entity']) && isset($tracking['entity']['client']) ? $tracking['entity']['client']['name'] : 'None';
            case 'coworker':
                return $tracking['user']['full_name'];
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
        $currency = $company = Auth::user()->employee->companyData->currency;
        foreach ($entities as $key => $entity) {
            $start = Carbon::parse($entity['date_from'])->format('H:i');
            $end = Carbon::parse($entity['date_to'])->format('H:i');
            $total = $this->convertSecondsToTimeFormat($entity['passed'], false);
            $item = [
                'date' => Carbon::parse($entity['date_from'])->format('d M Y'),
                'start' => $this->timeInDecimal ? $this->convertTimeToDecimal($start) : $start,
                'end' => $this->timeInDecimal ? $this->convertTimeToDecimal($end) : $end,
                'total' => $this->timeInDecimal ? $entity['passed_decimal'] : $total,
                'coworker' => $entity['user']['full_name'],
                'customer' => isset($entity['entity']) && isset($entity['entity']['client']) ? $entity['entity']['client']['name'] : '',
                'project' => isset($entity['entity']) ? $entity['entity']['name'] : '',
                'service' => isset($entity['service']) ? $entity['service']['name'] : '',
                'description' => isset($entity['description']) ? $entity['description'] : '',
                'billable' => $entity['billable'] ? 'Yes' : 'No',
                'revenue' => $entity['revenue']
                    ?
                        (
                            $currency
                            ? $currency->slug . ' '
                            : ''
                        ) . $entity['revenue']
                    : ''
            ];
            array_push($items, $item);
        }
        return $items;
    }

    function convertTimeToDecimal($time) {
        $hms = explode(":", $time);
        return number_format(($hms[0] + ($hms[1]/60)), 2);
    }

    function convertSecondsToDecimal(int $seconds) {
        return number_format($seconds / 60 / 60, 2);
    }

    function convertSecondsToTimeFormat($value, $withSeconds = true) {
//        $M = floor($value /2592000);
//        if (!empty($M)) {
//            $result[] = $M . ' months,';
//        }
//        $d = floor(($value %2592000)/86400);
//        if (!empty($d)) {
//            $result[] = $d . ' days,';
//        }
        $h = floor($value / 60 / 60);
        $m = floor(($value - ($h * 60 * 60)) / 60);
        if ($withSeconds) {
            return sprintf("%02d", $h) . ":" . sprintf("%02d", $m) . ":" . sprintf("%02d", $value % 60);
        }
        return sprintf("%02d", $h) . ":" . sprintf("%02d", $m);
    }

    function convertTimeToSeconds($time) {
        $time = explode(':', $time);
        $seconds = 0;
        if (isset($time[0])) $seconds += $time[0] * 60 * 60;
        if (isset($time[1])) $seconds += $time[1] * 60;
        if (isset($time[2])) $seconds += $time[2];
        return $seconds;
    }

    private function recalculateHeaderWidths($headers, $exclude = []) {
        $headers = collect($headers);
        $exclude = $headers->filter(function ($item) use ($exclude) {
            return in_array($item['slug'], $exclude);
        });
        $headers = $headers->filter(function ($item) use ($exclude) {
            return !in_array($item['slug'], $exclude->map(function($i) { return $i['slug']; })->toArray());
        });
        $sumWidth = $exclude->sum(function($item) { return $item['width']; });
        $countResizableColumns = $headers
            ->filter(function($item) { return $item['resizable']; })
            ->sum(function () { return 1; });
        $maxStep = (int)ceil($sumWidth / $countResizableColumns);
        $headers = $headers->toArray();
        foreach ($headers as $key => $header) {
            if ($sumWidth <= 0) break;
            if ($header['resizable']) {
                if ($sumWidth < $maxStep && $sumWidth > 0) {
                    $headers[$key]['width'] += $sumWidth;
                    $sumWidth -= $sumWidth;
                } else {
                    $headers[$key]['width'] += $maxStep;
                    $sumWidth -= $maxStep;
                }
            }
        }
        return $headers;
    }

    public function genPDF($request) {

        // Pre-define some variables
        $reportName = $request->get('name', 'Report');
        $headers = [
            ['slug' => 'date', 'text' => 'Date', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 7, 'resizable' => false],
            ['slug' => 'start', 'text' => 'Start', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 4, 'resizable' => false],
            ['slug' => 'end', 'text' => 'End', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 4, 'resizable' => false],
            ['slug' => 'total', 'text' => 'Total', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 4, 'resizable' => false],
            ['slug' => 'coworker', 'text' => 'Co-worker', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 11, 'resizable' => true],
            ['slug' => 'client', 'text' => 'Customer', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 16, 'resizable' => true],
            ['slug' => 'project', 'text' => 'Project', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 17, 'resizable' => true],
            ['slug' => 'service', 'text' => 'Service', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 9, 'resizable' => true],
            ['slug' => 'description', 'text' => 'Description', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 15, 'resizable' => true],
            ['slug' => 'billable', 'text' => 'Billable', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 5, 'resizable' => false],
            ['slug' => 'revenue', 'text' => 'Revenue', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 8, 'resizable' => false]
        ];

        if ($request->has('timeInDecimal') && $request->get('timeInDecimal') === true) {
            $this->timeInDecimal = $request->get('timeInDecimal');
        }

        $tracks = $this->getData($request)['tracks'];
        $fullTime = collect($tracks)->sum(function ($item) {
            return $item['passed'];
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

        if ($request->has('showCover') && $request->get('showCover') === true) {
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
            $totalTime = $this->convertSecondsToTimeFormat($fullTime, false);
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
        }

        if ($request->has('showRevenue') && $request->get('showRevenue') === false) {
            $headers = $this->recalculateHeaderWidths($headers, ['revenue']);
            $data = collect($data)->map(function($item) {
                unset($item['revenue']);
                return $item;
            })->toArray();
        }

        if ($request->has('hideColumns') && !empty($request->get('hideColumns')) && is_array($request->get('hideColumns'))) {
            $hideColumns = collect($request->get('hideColumns'))
                ->map(function($item) {
                    return $item['value'];
                })
                ->toArray();
            $headers = $this->recalculateHeaderWidths($headers, $hideColumns);
            $data = collect($data)->map(function($item) use ($hideColumns) {
                foreach ($hideColumns as $key) {
                    if (isset($item[strtolower($key)])) {
                        unset($item[$key]);
                    }
                }
                return $item;
            })->toArray();
//            dd($headers, $data);
        }

        // PAGE 2 and next
        $pdf->AddPage('L', 'A4');
        $pdf->SetFont('Arial', '', 10);
        $pdf->EasyTable($headers, $data);

        $html = '';
        // GENERATE FILE
        try {
            $tmpFileName = storage_path('app') . Auth::id() . '-' . time() . '.pdf';
            File::put($tmpFileName, $pdf->Output('S', $tmpFileName, true));
            if (File::exists($tmpFileName)) {
                $email = $request->get('email', Auth::user()->contact_email->email);
                if (
                        $request->has('sendByEmail') && $request->get('sendByEmail') === true
                    &&  $request->has('email') && $email
                ) {
                    Notification::route('mail', $email)
                        ->notify(new SendTrackingReportByEmail($tmpFileName));
                }
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
                $row[] = isset($tracking['entity']) && isset($tracking['entity']['client']) ? $tracking['entity']['client']['name'] : '';
                $row[] = isset($tracking['entity']) && isset($tracking['entity']['client']) ? $tracking['entity']['client']['id'] : '';
            }
            $row[] = $tracking['entity'] ? $tracking['entity']['name'] : '';
            $row[] = $tracking['entity'] ? $tracking['entity']['id'] : '';
            $row[] = $tracking['service'] ? $tracking['service']['name'] : '';
            $row[] = $tracking['service'] ? $tracking['service']['id'] : '';
            $row[] = $tracking['description'];
            $row[] = $tracking['billable'] ? 1 : 0;
            $row[] = round($tracking['rate'], 2);
            $row[] = Carbon::parse($tracking['date_from'])->format('d/m/Y');
            $row[] = $this->timeInDecimal ? $this->convertTimeToDecimal($tracking['date_from']) : Carbon::parse($tracking['date_from'])->format('H:i');
            $row[] = $this->timeInDecimal ? $this->convertTimeToDecimal($tracking['date_to']) : Carbon::parse($tracking['date_to'])->format('H:i');
            $row[] = $this->timeInDecimal ? $this->convertSecondsToDecimal($tracking['passed']) : $this->convertSecondsToTimeFormat($tracking['passed'], false);
            $row[] = $tracking['revenue'];
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
            'Service',
            'Service number',
            'Description',
            'Billable',
            'Hourly rate in ' . $this->currency,
            'Date',
            'Start',
            'End',
            'Total time',
            'Revenue in ' . $this->currency
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
