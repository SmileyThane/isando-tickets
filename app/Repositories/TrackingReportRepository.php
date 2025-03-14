<?php


namespace App\Repositories;

use App\Client;
use App\Http\Controllers\API\Tracking\PDF;
use App\Notifications\SendTrackingReportByEmail;
use App\Permission;
use App\Team;
use App\Ticket;
use App\Tracking;
use App\TrackingProject;
use App\TrackingReport;
use App\TrackingSettings;
use App\TrackingTimesheet;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class TrackingReportRepository
{
    protected $timeInDecimal = false;
    protected $round = 0;
    protected $settings = [];
    protected $company;
    protected $currency = 'EUR';

    public function all(Request $request)
    {
        if (!Auth::user()->employee->hasPermissionId(61)) {
            throw new Exception('Access denied');
        }
        return $this->getUserReports();
    }

    public function getUserReports()
    {
        return Auth::user()->trackingReports()->orderBy('id', 'desc')->get();
    }

    public function find($reportId)
    {
        return Auth::user()->trackingReports()->find($reportId);
    }

    public function create(Request $request)
    {
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

    public function delete($id)
    {
        $report = Auth::user()->trackingReports()->findOrFail($id);
        $report->delete();
    }

    public function validate($request, $new = true)
    {
        $params = [
            'period' => 'required|array',
            'period.start' => 'required_without:period.end|date|nullable',
            'period.end' => 'required|date',
            'sort' => 'required|array',
            'sort.value' => 'required|string|in:alph-asc,chron-desc,duration-desc,duration-asc,revenue-desc,revenue-asc',
            'round' => 'required|string',
            'group' => 'array',
            'group.*.value' => 'string|in:day,description,week,billability,month',
            'filters' => 'array',
            'filters.*.value' => 'string|in:coworkers,projects,clients,services,billable'
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    public function genPDF($request)
    {

        // Pre-define some variables
        $reportName = $request->get('name', 'Report');
        $headers = [
            ['slug' => 'date', 'text' => 'Date', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 7, 'resizable' => false],
            ['slug' => 'start', 'text' => 'Start', 'style' => 'align:R;border:B;border-width:1;font-style:B', 'width' => 4, 'resizable' => false],
            ['slug' => 'end', 'text' => 'End', 'style' => 'align:R;border:B;border-width:1;font-style:B', 'width' => 4, 'resizable' => false],
            ['slug' => 'total', 'text' => 'Total', 'style' => 'align:R;border:B;border-width:1;font-style:B', 'width' => 4, 'resizable' => false],
            ['slug' => 'coworker', 'text' => 'Co-worker', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 11, 'resizable' => true],
            ['slug' => 'client', 'text' => 'Customer', 'style' => 'border:B;border-width:1;font-style:B', 'width' => 16, 'resizable' => true],
            ['slug' => 'project', 'text' => $this->getProjectLabel(), 'style' => 'border:B;border-width:1;font-style:B', 'width' => 17, 'resizable' => true],
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
            $pdf->SetXY($pdf->GetCenterX($reportName, $pdf->GetPageWidth()), $y);
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
            $data = collect($data)->map(function ($item) {
                unset($item['revenue']);
                return $item;
            })->toArray();
        }

        if ($request->has('hideColumns') && !empty($request->get('hideColumns')) && is_array($request->get('hideColumns'))) {
            $hideColumns = collect($request->get('hideColumns'))
                ->map(function ($item) {
                    return $item['value'];
                })
                ->toArray();
            $headers = $this->recalculateHeaderWidths($headers, $hideColumns);
            $data = collect($data)->map(function ($item) use ($hideColumns) {
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
                    && $request->has('email') && $email
                ) {
                    Notification::route('mail', $email)
                        ->notify(new SendTrackingReportByEmail($tmpFileName));
                }
                return response()->file($tmpFileName)->deleteFileAfterSend();
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
        throw new Exception('Error generating file');
    }

    private function getProjectLabel()
    {
        $settings = TrackingSettings::where([
            ['entity_id', '=', Auth::user()->id],
            ['entity_type', '=', User::class]
        ])->first();

        $projectLabel = 'Project';
        if (isset($settings->data['projectType'])) {
            switch ($settings->data['projectType']) {
                case 1:
                    $projectLabel = 'Department';
                    break;
                case 2:
                    $projectLabel = 'Profit center';
                    break;
                default:
                    $projectLabel = 'Project';
                    break;
            }
        }
        return $projectLabel;
    }

    protected function getData(Request $request)
    {

        $permissionIds = Auth::user()->employee->getPermissionIds();

        if (!in_array(Permission::TRACKER_REPORT_VIEW_OWN_TIME_ACCESS, $permissionIds) &&
            !in_array(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS, $permissionIds) &&
            !in_array(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS, $permissionIds)
        ) {
            throw new Exception('Access denied');
        }

        $tracking = Tracking::with('User.employee.assignedToTeams')
            ->with('Tags.Translates');
//            ->where(function($query) {
//                $query
//                    ->where('status', '!=', Tracking::$STATUS_ARCHIVED)
//                    ->orWhereNull('status');
//            });

        if (in_array(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS, $permissionIds)) {
            // Company Admin
            $tracking = $tracking->CompanyAdmin();
        } elseif (in_array(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS, $permissionIds)) {
            // Manager
            $tracking = $tracking->TeamManager();
        } else {
            // User
            $tracking = $tracking->SimpleUser();
        }

        $this->company = Auth::user()->employee->companyData;
        $this->currency = $this->company && $this->company->currency ? $this->company->currency->slug : $this->currency;
        $sorting = $request->sort['value'];
        $grouping = $request->group;
        if (isset($request->group) && isset($request->group['value']) && $request->group['value'] === 'custom') {
            $grouping = $request->group['items'];
        }
        if (isset($request->group) && isset($request->group['value']) && in_array($request->group['value'], ['all_no_group', 'all_chron'])) {
            $grouping = [];
        }
        $grouping = collect($grouping)->map(function ($item) {
            return $item['value'];
        });
        $filtering = collect($request->filters)
            ->map(function ($item) {
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

        if ($request->has('period') && isset($request->period['start'])) {
            $tracking->where('tracking.date_from', '>=', Carbon::parse($request->period['start'])->startOf('day'));
        }
        if ($request->has('period') && isset($request->period['end'])) {
            $tracking->where(function ($query) use ($request) {
                $query
                    ->where('tracking.date_to', '<=', Carbon::parse($request->period['end'])->endOf('day'))
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

                        $tracking->where(function ($query) use ($projectIds, $ticketIds) {
                            // project
                            if (count($projectIds)) {
                                $query->where(function ($q) use ($projectIds) {
                                    return $q
                                        ->where('entity_type', '=', TrackingProject::class)
                                        ->whereIn('entity_id', $projectIds);
                                });
                            }
                            // ticket
                            if (count($ticketIds)) {
                                if (count($projectIds)) {
                                    $query->where(function ($q) use ($ticketIds) {
                                        return $q
                                            ->where('entity_type', '=', Ticket::class)
                                            ->whereIn('entity_id', $ticketIds);
                                    });
                                } else {
                                    $query->orWhere(function ($q) use ($ticketIds) {
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
                        if (Auth::user()->employee->hasPermissionId(65)) {
                            $tracking->where('tracking.billable', '=', (int)$filter['selected']);
                        }
                        break;
                    case 'tag':
                        $tracking->whereHas('Tags', function ($query) use ($filter) {
                            $query->whereIn('tags.id', $filter['selected']);
                        });
                        break;
                }
            }
        }

        $tracks = $tracking->get()->toArray();

        $this->round = $request->get('round', 0);
        $round = $this->round;

        if (!empty($round)) {
            $tracks = collect($tracks)->map(function ($track) use ($round) {
                $time = $this->convertSecondsToTimeFormat($track['passed']);
                $roundedTime = $this->getRoundTime($time, $round);
                $passed = $this->convertTimeToSeconds($roundedTime);
                $track['passed'] = $passed;
                if ($track['billable'] && Auth::user()->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_REVENUE_PREVIEW_ACCESS)) {
                    $track['revenue'] = number_format((float)$track['rate'] * (float)$this->convertSecondsToDecimal((float)$passed), 2, '.', '');
                } else {
                    $track['revenue'] = 0;
                }
                return $track;
            });
        }

        return [
            'tracks' => $tracks,
            'grouping' => $grouping
        ];
    }

    function convertSecondsToTimeFormat($value, $withSeconds = true)
    {
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

    function getRoundTime($time, $round = '5')
    {
        if ($round === 0) return $time;
        $minutes = $round;
        $direction = 'nearest';
        if (!is_numeric($round)) {
            try {
                $round = explode('_', $round);
                if ($round[0] !== 'custom' || count($round) !== 4) return $time;
                $minutes = $round[1];
                $direction = $round[2];
            } catch (Exception $exception) {
                return $time;
            }
        }
        $time = explode(':', $time);
        $seconds = (int)$time[0] * 60 * 60 + (int)$time[1] * 60;
        if (isset($time[2])) $seconds += (int)$time[2];
        switch ($direction) {
            case 'up':
                $rounded = ceil((int)$seconds / ((int)$minutes * 60)) * ((int)$minutes * 60);
                break;
            case 'down':
                $rounded = floor((int)$seconds / ((int)$minutes * 60)) * ((int)$minutes * 60);
                break;
            default:
                $rounded = round((int)$seconds / ((int)$minutes * 60)) * ((int)$minutes * 60);
        }
//        dd($time, $minutes, $rounded, $this->convertSecondsToTimeFormat($rounded));
        return $this->convertSecondsToTimeFormat($rounded);
    }

    function convertTimeToSeconds($time)
    {
        $time = explode(':', $time);
        $seconds = 0;
        if (isset($time[0])) $seconds += $time[0] * 60 * 60;
        if (isset($time[1])) $seconds += $time[1] * 60;
        if (isset($time[2])) $seconds += $time[2];
        return $seconds;
    }

    function convertSecondsToDecimal(int $seconds)
    {
        return number_format($seconds / 60 / 60, 2);
    }

    protected function prepareDataForPDF($entities)
    {
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
                'total' => $this->timeInDecimal ? number_format($entity['passed_decimal'], 2) : $total,
                'coworker' => $entity['user']['full_name'],
                'customer' => isset($entity['entity']) && isset($entity['entity']['client'])
                    ? $entity['entity']['client']['name']
                    : (
                    isset($entity['entity']['from_entity_name'])
                        ? $entity['entity']['from_entity_name']
                        : ''
                    ),
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

    function convertTimeToDecimal($time)
    {
        $hms = explode(":", $time);
        return number_format(($hms[0] + ($hms[1] / 60)), 2);
    }

    private function recalculateHeaderWidths($headers, $exclude = [])
    {
        $headers = collect($headers);
        $exclude = $headers->filter(function ($item) use ($exclude) {
            return in_array($item['slug'], $exclude);
        });
        $headers = $headers->filter(function ($item) use ($exclude) {
            return !in_array($item['slug'], $exclude->map(function ($i) {
                return $i['slug'];
            })->toArray());
        });
        $sumWidth = $exclude->sum(function ($item) {
            return $item['width'];
        });
        $countResizableColumns = $headers
            ->filter(function ($item) {
                return $item['resizable'];
            })
            ->sum(function () {
                return 1;
            });
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

    public function genCSV($request)
    {
        if (isset($request->group) && $request->group['value'] === 'custom') {
            $data = $this->generate($request);
        } else {
            if (is_array($this->getData($request)['tracks'])) {
                $data = $this->getData($request)['tracks'];
            } else {
                $data = $this->getData($request)['tracks']->toArray();
            }
        }

        if ($request->has('timeInDecimal') && $request->get('timeInDecimal') === true) {
            $this->timeInDecimal = $request->get('timeInDecimal');
        }

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, $this->getHeaderCSV(), ';');

        $result = $this->prepareDataForCSV($data);
        foreach ($result as $key => $item) {
//            $result[$key] = $this->getDataCSV($item);
            fputcsv($out, $this->getDataCSV($item), ';');
        }
        return stream_get_contents($out);
    }

    public function generate(Request $request)
    {
        $result = $this->getData($request);
        $grouping = $result['grouping'];
        $tracks = $result['tracks'];

        if ($grouping->isEmpty()) return [
            'g1' => $tracks,
            'g2' => $tracks,
        ];

        $group = $grouping->shift();
        $items = $this->makeList($group, $tracks);

        $items2 = $this->makeList($group === 'project' ? 'service' : 'project', $tracks);
        return [
            'g1' => array_values($this->makeStructure($grouping->toArray(), 0, $items)),
            'g2' => array_values($this->makeStructure([], 0, $items2)),
        ];

    }

    protected function makeList($group, $trackings)
    {
        $items = [];
        foreach ($trackings as $tracking) {
            $data = $this->getFieldData($tracking, $group);
            $items[$data]['name'] = $data;
            if ($group === 'project') {
                $items[$data]['client'] = $this->getFieldData($tracking, 'client');
            }
            $items[$data]['children'][] = $tracking;
        }
        return $items;
    }

    protected function getFieldData($tracking, $field = 'description')
    {
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

    protected function makeStructure($grouping, $groupIndex, $tracks)
    {
        if (!isset($grouping[$groupIndex])) return $tracks;
        foreach ($tracks as $key => $track) {
            $list = $this->makeList($grouping[$groupIndex], $track['children']);
            $tracks[$key]['name'] = $key;
            $tracks[$key]['children'] = array_values($this->makeStructure($grouping, $groupIndex + 1, $list));
        }
        return array_values($tracks);
    }

    protected function getHeaderCSV()
    {
        return [
            'Co-worker',
            'Personnel number',
            'Customer',
            'Customer number',
            $this->getProjectLabel(),
            $this->getProjectLabel() . " number",
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
        ];
    }

    protected function prepareDataForCSV($entities, $items = [])
    {
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

    protected function getDataCSV($tracking)
    {
        try {
            $row = [];
            $row[] = $tracking['user'] ? $this->fixCharacters($tracking['user']['full_name']) : '';
            $row[] = $tracking['user'] ? $tracking['user']['id'] : '';
            if ($tracking['entity'] && isset($tracking['entity']['from_entity_id'])) {
                $row[] = $tracking['entity'] && $tracking['entity']['from_entity_name'] ? $this->fixCharacters($tracking['entity']['from_entity_name']) : '';
                $row[] = $tracking['entity'] && $tracking['entity']['from_entity_id'] ? $tracking['entity']['from_entity_id'] : '';
            } else {
                $row[] = isset($tracking['entity']) && isset($tracking['entity']['client']) ? $this->fixCharacters($tracking['entity']['client']['name']) : '';
                $row[] = isset($tracking['entity']) && isset($tracking['entity']['client']) ? $tracking['entity']['client']['id'] : '';
            }
            $row[] = $tracking['entity'] ? $this->fixCharacters($tracking['entity']['name']) : '';
            $row[] = $tracking['entity'] ? $tracking['entity']['id'] : '';
            $row[] = $tracking['service'] ? $this->fixCharacters($tracking['service']['name']) : '';
            $row[] = $tracking['service'] ? $tracking['service']['id'] : '';
            $row[] = $this->fixCharacters($tracking['description']);
            $row[] = $tracking['billable'] ? 1 : 0;
            $row[] = round($tracking['rate'], 2);
            $row[] = Carbon::parse($tracking['date_from'])->format('d/m/Y');
            $row[] = Carbon::parse($tracking['date_from'])->format('H:i');
            $row[] = Carbon::parse($tracking['date_to'])->format('H:i');
            $row[] = $this->timeInDecimal ? $this->convertSecondsToDecimal($tracking['passed']) : $this->convertSecondsToTimeFormat($tracking['passed'], false);
            $row[] = $tracking['revenue'];
            return $row;
        } catch (Exception $exception) {
            dd($exception->getMessage(), $exception->getLine(), $tracking);
        }
    }

    private function fixCharacters($text)
    {
        return $text;
//        return iconv('utf-8', 'windows-1252', $text);
    }

    public function getTotalTimeByServices($from, $to, $team = null)
    {
        $user = Auth::user();

        if (!Auth::user()->employee->hasPermissionId([
            Permission::TRACKER_REPORT_VIEW_OWN_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS
        ])) {
            throw new Exception('Access denied');
        }

        $tracking = DB::table('tracking');

        if ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $company = $user->employee()
                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
                ->with('userData')
                ->first();
            $tracking->where(function ($query) use ($company, $user) {
                $query->where('tracking.user_id', '=', $user->id)
                    ->orWhere('tracking.company_id', '=', $company->company_id);
            });
        } elseif ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($query) use ($user) {
                return $query
                    ->where('company_user_id', '=', $user->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $tracking->whereIn('tracking.team_id', $teams);
        } else {
            // User
            $tracking->where('tracking.user_id', '=', $user->id);
        }

        if ($team !== 'null') {
            $tracking->where('tracking.team_id', '=', $team);
        }

        $tracking
            ->leftJoin('serviceable', function ($join) {
                $join->on('tracking.id', '=', 'serviceable.serviceable_id')
                    ->where('serviceable.serviceable_type', '=', Tracking::class);
            })
            ->leftJoin('services', 'services.id', '=', 'serviceable.service_id')
            ->where(function ($query) use ($from, $to) {
                $query->where('date_to', '>=', $from)
                    ->where('date_from', '<=', $to);
            })
            ->groupBy('services.id', 'services.name')
            ->select(['services.name', 'services.id', DB::raw("SUM(TIMESTAMPDIFF(SECOND, date_from, date_to)) as duration")]);
//        dd($tracking->toSql(), $tracking->getBindings(), $tracking->get());
        return $tracking->get();
    }

    public function getTotalTimeByProjects($from, $to, $team = null)
    {
        $user = Auth::user();

        if (!Auth::user()->employee->hasPermissionId([
            Permission::TRACKER_REPORT_VIEW_OWN_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS
        ])) {
            throw new Exception('Access denied');
        }

        $tracking = DB::table('tracking');

        if ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $company = $user->employee()
                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
                ->with('userData')
                ->first();
            $tracking->where(function ($query) use ($company, $user) {
                $query->where('tracking.user_id', '=', $user->id)
                    ->orWhere('tracking.company_id', '=', $company->company_id);
            });
        } elseif ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($query) use ($user) {
                return $query
                    ->where('company_user_id', '=', $user->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $tracking->whereIn('tracking.team_id', $teams);
        } else {
            // User
            $tracking->where('tracking.user_id', '=', $user->id);
        }

        if ($team !== 'null') {
            $tracking->where('tracking.team_id', '=', $team);
        }

        $tracking
            ->leftJoin('tracking_projects', function ($join) {
                $join->on('tracking.entity_id', '=', 'tracking_projects.id')
                    ->where('tracking.entity_type', '=', TrackingProject::class);
            })
            ->leftJoin('clients', 'clients.id', '=', 'tracking_projects.client_id')
            ->where(function ($query) use ($from, $to) {
                $query->where('date_to', '>=', $from)
                    ->where('date_from', '<=', $to);
            })
            ->groupBy('tracking_projects.client_id', 'clients.name', 'tracking_projects.id', 'tracking_projects.project')
            ->select(['tracking_projects.client_id', 'clients.name as client_name', 'tracking_projects.project', 'tracking_projects.id',
                DB::raw("SUM(TIMESTAMPDIFF(SECOND, tracking.date_from, tracking.date_to)) as duration")]);
//        dd($tracking->toSql(), $tracking->getBindings(), $tracking->get());
        return $tracking->get();
    }

    public function getTopProjects($from, $to, int $numberOfProjects = 10, $team = null)
    {
        $user = Auth::user();

        if (!Auth::user()->employee->hasPermissionId([
            Permission::TRACKER_REPORT_VIEW_OWN_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS
        ])) {
            throw new Exception('Access denied');
        }

        $tracking = DB::table('tracking');

        if ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $company = $user->employee()
                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
                ->with('userData')
                ->first();
            $tracking->where(function ($query) use ($company, $user) {
                $query->where('tracking.user_id', '=', $user->id)
                    ->orWhere('tracking.company_id', '=', $company->company_id);
            });
        } elseif ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($query) use ($user) {
                return $query
                    ->where('company_user_id', '=', $user->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $tracking->whereIn('tracking.team_id', $teams);
        } else {
            // User
            $tracking->where('tracking.user_id', '=', $user->id);
        }

        if ($team !== 'null') {
            $tracking->where('tracking.team_id', '=', $team);
        }

        $tracking
            ->leftJoin('tracking_projects', function ($join) {
                $join->on('tracking.entity_id', '=', 'tracking_projects.id')
                    ->where('tracking.entity_type', '=', TrackingProject::class);
            })
            ->leftJoin('clients', 'clients.id', '=', 'tracking_projects.client_id')
            ->where(function ($query) use ($from, $to) {
                $query->where('date_to', '>=', $from)
                    ->where('date_from', '<=', $to);
            })
            ->where('tracking.billable', '=', true)
            ->groupBy(
                'tracking_projects.client_id',
                'clients.name',
                'tracking_projects.id',
                'tracking_projects.project',
                'tracking_projects.rate'
            )
            ->select([
                'tracking_projects.client_id',
                'clients.name as client_name',
                'tracking_projects.project as project_name',
                'tracking_projects.id',
                DB::raw("SUM(TIMESTAMPDIFF(SECOND, tracking.date_from, tracking.date_to)) as duration"),
                DB::raw("(`tracking_projects`.rate * (SUM(TIMESTAMPDIFF(SECOND, tracking.date_from, tracking.date_to)) / 60 / 60)) as revenue")
            ])
            ->orderBy('revenue', 'desc')
            ->limit($numberOfProjects);
//        dd($tracking->toSql(), $tracking->getBindings(), $tracking->get());
        return $tracking->get();
    }

    public function getLastActivity($from, $to, $team = null)
    {
        $user = Auth::user();

        if (!Auth::user()->employee->hasPermissionId([
            Permission::TRACKER_REPORT_VIEW_OWN_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS,
            Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS
        ])) {
            throw new Exception('Access denied');
        }

        $tracking = DB::table('tracking');

        if ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS)) {
            // Company Admin
            $company = $user->employee()
                ->whereDoesntHave('assignedToClients')->where('is_clientable', false)
                ->with('userData')
                ->first();
            $tracking->where(function ($query) use ($company, $user) {
                $query->where('tracking.company_id', '=', $company->company_id);
            });
        } elseif ($user->employee->hasPermissionId(Permission::TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS)) {
            // Manager
            $teams = $teams = Team::whereHas('employees', function ($query) use ($user) {
                return $query
                    ->where('company_user_id', '=', $user->employee->id)
                    ->where('is_manager', '=', true);
            })->get()->pluck('id')->toArray();
            $tracking->whereIn('tracking.team_id', $teams);
        } else {
            // User
            $tracking->where('tracking.user_id', '=', $user->id);
        }

        if ($team !== 'null') {
            $tracking->where('tracking.team_id', '=', $team);
        }

        $tracking
            ->leftJoin('teams', 'teams.id', '=', 'tracking.team_id')
            ->leftJoin('users', 'users.id', '=', 'tracking.user_id')
            ->where(function ($query) use ($from, $to) {
                $query->where('date_to', '>=', $from)
                    ->where('date_from', '<=', $to);
            })
            ->groupBy(
                'tracking.team_id', 'teams.name',
                'tracking.user_id', 'users.name', 'users.surname',
                'tracking.entity_type', 'tracking.entity_id'
            )
            ->select([
                'tracking.team_id',
                'teams.name as team_name',
                'tracking.user_id', 'users.name', 'users.surname',
                'tracking.entity_type',
                'tracking.entity_id',
                DB::raw("CASE
                    WHEN `tracking`.`entity_type` LIKE 'App%TrackingProject' THEN (SELECT tracking_projects.project as name FROM tracking_projects WHERE tracking_projects.id = tracking.entity_id)
                    WHEN `tracking`.`entity_type` LIKE 'App%Ticket' THEN (SELECT tickets.name FROM tickets WHERE tickets.id = tracking.entity_id)
                    ELSE 'None' END entity"),
                DB::raw("SUM(TIMESTAMPDIFF(SECOND, TIMESTAMP(`tracking`.`date_from`), TIMESTAMP(`tracking`.`date_to`))) as seconds"),
            ])
            ->having('seconds', '>', 0)
            ->orderBy('tracking.date_from', 'desc');
//        dd($tracking->toSql(), $tracking->getBindings(), $tracking->get());
        $teamsData = $tracking->get();

        $teams = [];
        foreach ($teamsData as $team) {
            $tracker = Tracking::where(function ($query) use ($from, $to) {
                $query->where('date_to', '>=', $from)
                    ->where('date_from', '<=', $to);
            })
                ->where('team_id', '=', $team->team_id)
                ->orderBy('date_from', 'desc')
                ->first();

            $teams[$team->team_id ?? 0]['name'] = $team->team_name;
            if (!isset($teams[$team->team_id ?? 0]['seconds'])) $teams[$team->team_id ?? 0]['seconds'] = 0;
            $teams[$team->team_id ?? 0]['seconds'] += $team->seconds;
            $teams[$team->team_id ?? 0]['users'][] = $team;
            $teams[$team->team_id ?? 0]['tracker'] = $tracker;
        }

        return (array)array_values($teams);
    }

    public function getReportDetail($source, $from, $to, $clients = [], $coworkers = [])
    {
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        switch ($source) {
            case 'tracker':
                $result = $this->prepareTrackerDataToReportDetail($this->getTrackerDataToReportDetail($from, $to, $clients, $coworkers));
                break;
            case 'timesheet':
                $result = $this->prepareTimesheetDataToReportDetail($this->getTimesheetDataToReportDetail($from, $to, $clients, $coworkers));
                break;
            default:
                $result = [];
        }
        foreach ($result as $key => $row) {
            fputcsv($out, $row, ';');
        }
        return stream_get_contents($out);
    }

    protected function prepareTrackerDataToReportDetail($trackers)
    {
        $trackers = $trackers->get();
        $rows = [
            [
                'id', 'company_id', 'team_id', 'timesheet_id', 'status', 'user_id', 'user_name', 'entity_id',
                'entity_type', 'entity_name', 'client_id', 'client_name', 'service_id', 'service_name', 'description',
                'tags', 'billable', 'date_from', 'date_to', 'passed', 'passed_decimal', 'revenue'
            ]
        ];
        foreach ($trackers as $tracker) {
            switch ($tracker->status) {
                case Tracking::$STATUS_STARTED:
                    $status = 'started';
                    break;
                case Tracking::$STATUS_STOPPED:
                    $status = 'stopped';
                    break;
                case Tracking::$STATUS_PAUSED:
                    $status = 'paused';
                    break;
                case Tracking::$STATUS_ARCHIVED:
                    $status = 'archived';
                    break;
                default:
                    $status = '';
            }
            $row = [
                'id' => $tracker->id,
                'company_id' => $tracker->company_id,
                'team_id' => $tracker->team_id,
                'timesheet_id' => $tracker->timesheet_id,
                'status' => $status,
                'user_id' => $tracker->user->id,
                'user_name' => $tracker->user->name . ' ' . $tracker->user->surname,
                'entity_id' => $tracker->entity ? $tracker->entity->id : '',
                'entity_type' => $tracker->entity ? ($tracker->entity->from ? 'Ticket' : 'Project') : '',
                'entity_name' => $tracker->entity ? $tracker->entity->name : '',
                'client_id' => $tracker->entity && $tracker->entity->client ? $tracker->entity->client->id : '',
                'client_name' => $tracker->entity && $tracker->entity->client ? $tracker->entity->client->name : '',
                'service_id' => $tracker->service ? $tracker->service->id : '',
                'service_name' => $tracker->service ? $tracker->service->name : '',
                'description' => $tracker->description,
                'tags' => $tracker->tags->pluck('name')->join(', '),
                'billable' => $tracker->billable,
                'date_from' => $tracker->date_from,
                'date_to' => $tracker->date_to,
                'passed' => $tracker->passed,
                'passed_decimal' => number_format($tracker->passed_decimal, 2),
                'revenue' => $tracker->revenue,
            ];
            array_push($rows, $row);
        }
        return $rows;
    }

    public function getTrackerDataToReportDetail($from, $to, $clients = [], $coworkers = [])
    {
        $trackers = Tracking::where('date_from', '<=', Carbon::parse($to)->endOfDay()->format(Tracking::$DATETIME_FORMAT))
            ->where(function ($query) use ($from, $to) {
                $query
                    ->where('date_to', '>=', Carbon::parse($from)->startOfDay()->format(Tracking::$DATETIME_FORMAT))
                    ->orWhereNull('date_to');
            });
        if (!empty($coworkers)) {
            $trackers->whereIn('user_id', $coworkers);
        }
        if (!empty($clients)) {
            $projectIds = TrackingProject::whereIn('client_id', $clients)->pluck('id')->all();
            $trackers->where('entity_type', '=', TrackingProject::class)
                ->whereIn('entity_id', $projectIds);
        }

//        dd($trackers->toSql(), $trackers->get());
        $trackers->with('Tags.Translates:name,lang,color')
            ->with('User:id,name,surname,middle_name,number,avatar_url')
            ->orderBy('date_from', 'desc');
        return $trackers;
    }

    protected function prepareTimesheetDataToReportDetail($timesheets)
    {
        $timesheets = $timesheets->get();
        $rows = [
            [
                'id', 'company_id', 'team_id', 'user_id', 'user_name', 'number', 'status', 'entity_id', 'entity_type',
                'entity_name', 'client_id', 'client_name', 'is_manually', 'billable', 'date_from', 'date_to', 'total_time', 'service_id', 'service_name',
                'mon', 'mon_decimal', 'tue', 'tue_decimal', 'wed', 'wed_decimal', 'thu', 'thu_decimal', 'fri', 'fri_decimal',
                'sat', 'sat_decimal', 'sun', 'sun_decimal', 'approver_id', 'approver_name'
            ]
        ];
        foreach ($timesheets as $timesheet) {
            switch ($timesheet->status) {
                case TrackingTimesheet::STATUS_TRACKED:
                    $status = 'tracked';
                    break;
                case TrackingTimesheet::STATUS_PENDING:
                    $status = 'pending';
                    break;
                case TrackingTimesheet::STATUS_REJECTED:
                    $status = 'rejected';
                    break;
                case TrackingTimesheet::STATUS_ARCHIVED:
                    $status = 'archived';
                    break;
                case TrackingTimesheet::STATUS_APPROVED:
                    $status = 'approved';
                    break;
                case TrackingTimesheet::STATUS_UNSUBMITTED:
                    $status = 'unsubmitted';
                    break;
                default:
                    $status = '';
            }
            $row = [
                'id' => $timesheet->id,
                'company_id' => $timesheet->company_id,
                'team_id' => $timesheet->team_id,
                'user_id' => $timesheet->user->id,
                'user_name' => $timesheet->user->name . ' ' . $timesheet->user->surname,
                'number' => $timesheet->number,
                'status' => $status,
                'entity_id' => $timesheet->entity ? $timesheet->entity->id : '',
                'entity_type' => $timesheet->entity ? ($timesheet->entity->from ? 'Ticket' : 'Project') : '',
                'entity_name' => $timesheet->entity ? $timesheet->entity->name : '',
                'client_id' => $timesheet->entity && $timesheet->entity->client ? $timesheet->entity->client->id : '',
                'client_name' => $timesheet->entity && $timesheet->entity->client ? $timesheet->entity->client->name : '',
                'is_manually' => $timesheet->is_manually ?? 0,
                'billable' => $timesheet->billable ?? 0,
                'date_from' => $timesheet->from,
                'date_to' => $timesheet->to,
                'total_time' => $timesheet->total_time,
                'service_id' => $timesheet->service ? $timesheet->service->id : '',
                'service_name' => $timesheet->service ? $timesheet->service->name : '',
            ];
            foreach ($timesheet->times as $time) {
                $dayOfWeek = Carbon::parse($time->date)->shortEnglishDayOfWeek;
                $row[$dayOfWeek] = $time->time;
                $row[$dayOfWeek . '_decimal'] = $this->convertTimeToDecimal($time->time);
            }
            $row['approver_id'] = $timesheet->approver_id;
            $row['approver_name'] = $timesheet->approver ? $timesheet->approver->name . ' ' . $timesheet->approver->surname : '';
            array_push($rows, $row);
        }
        return $rows;
    }

    public function getTimesheetDataToReportDetail($from, $to, $clients = [], $coworkers = [])
    {
        $timesheets = TrackingTimesheet::with('User')
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
            ->where(function ($q) use ($from, $to) {
                $q->where('from', '<=', Carbon::parse($to)->format(Tracking::$DATE_FORMAT))
                    ->where('to', '>=', Carbon::parse($from)->format(Tracking::$DATE_FORMAT));
            });
        if (!empty($clients)) {
            $projectIds = TrackingProject::whereIn('client_id', $clients)->pluck('id')->all();
            $timesheets->where('entity_type', '=', TrackingProject::class)
                ->whereIn('entity_id', $projectIds);
        }

        if (!empty($coworkers)) {
            $timesheets->whereIn('user_id', $coworkers);
        }

        $timesheets->orderBy('id', 'desc');
        return $timesheets;
    }

    public function getReportReconciliationDetail($from, $to, $groupBy = 'client')
    {
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $result = $this->getReconciliationDataToReport($from, $to, $groupBy);
        foreach ($result as $key => $row) {
            fputcsv($out, $row, ';');
        }
        return stream_get_contents($out);
    }

    public function getReconciliationDataToReport($from, $to, $groupBy = 'client')
    {

        $trackers = $this->getTrackerDataToReportDetail($from, $to);
        $trackers = $this->prepareTrackerDataToReportDetail($trackers);
        $trackers = collect($trackers);
        $trackers->shift();

        $timesheets = $this->getTimesheetDataToReportDetail($from, $to);
        $timesheets = $this->prepareTimesheetDataToReportDetail($timesheets);
        $timesheets = collect($timesheets);
        $timesheets->shift();

        switch ($groupBy) {
            case 'project':
                $groupField = 'entity_id';
                $fieldName = 'entity_name';
                break;
            case 'coworker':
                $groupField = 'user_id';
                $fieldName = 'user_name';
                break;
            case 'client':
            default:
                $groupField = 'client_id';
                $fieldName = 'client_name';
        }

        // grouping
        $trackers = $trackers->groupBy($groupField);
        $timesheets = $timesheets->groupBy($groupField);

        foreach ($trackers as $index => $tracker) {
            $trackers[$index] = [
                'id' => $index,
                'name' => $tracker[0][$fieldName],
                'passed' => $tracker->sum('passed'),
            ];
        }

        foreach ($timesheets as $index => $timesheet) {
            $timesheets[$index] = [
                'id' => $index,
                'name' => $timesheet[0][$fieldName],
                'passed' => $timesheet->sum('total_time'),
            ];
        }

        return $this->prepareReconciliationDataToReport(
            $trackers,
            $timesheets
        );
    }

    public function prepareReconciliationDataToReport($trackers, $timesheets)
    {
        $rows = [
            ['id', 'name', 'tracker_passed', 'passed_decimal', 'timesheet', 'passed_decimal']
        ];
        foreach ($trackers as $item) {
            $rows[$item['id']]['id'] = $item['id'];
            $rows[$item['id']]['name'] = $item['name'];
            $rows[$item['id']]['tracker_passed'] = $item['passed'];
            $rows[$item['id']]['tracker_passed_decimal'] = $this->convertSecondsToDecimal($item['passed']);
            if (!isset($rows[$item['id']]['timesheet_passed'])) {
                $rows[$item['id']]['timesheet_passed'] = 0;
                $rows[$item['id']]['timesheet_passed_decimal'] = 0;
            }
        }
        foreach ($timesheets as $item) {
            $rows[$item['id']]['id'] = $item['id'];
            $rows[$item['id']]['name'] = $item['name'];
            if (!isset($rows[$item['id']]['tracker_passed'])) {
                $rows[$item['id']]['tracker_passed'] = 0;
                $rows[$item['id']]['tracker_passed_decimal'] = 0;
            }
            $rows[$item['id']]['timesheet_passed'] = $item['passed'];
            $rows[$item['id']]['timesheet_passed_decimal'] = $this->convertSecondsToDecimal($item['passed']);
        }

        return $rows;
    }
}
