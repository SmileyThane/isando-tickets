<?php


namespace App\Repositories;


use App\Tracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PDF;
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
            'filters.*.value'       => 'string|in:coworkers,clients&projects,clients,services,billable'
        ];
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    protected function getData(Request $request) {
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

    protected function prepareForPdf(string $html)
    {

        $html = preg_replace('/[\000-\031\200-\377]/', '', $html);

        // Workaround for embedded images in TCPDF library
        $html = preg_replace('/data:image\/.+?;base64,\\s*/si', '@', $html);

        // Workaround for hidding node titles
        $html = preg_replace('/<h2.+?field\-\-label\-hidden.+?<\/h2>/si', '', $html);

        // Remove scripts
        $html = preg_replace('/<script.*?>.+?<\/script>/si', '', $html);

        // Remove CDATA
        $html = preg_replace('/<\!\[CDATA\[(.+?)\]\]>/si', '$1', $html);

        // Remove comments from styles
        $html = preg_replace_callback('/<style(.+?)>(.+?)<\/style>/si', function ($matches) {
            return '<style' . $matches[1] . '>' . preg_replace('/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/si', '', $matches[2]) . '</style>';
        }, $html);

        return $html;
    }

    protected function getMargins($side = null)
    {
        $default = [
            'left' => config('tcpdf.margin_left'),
            'right' => config('tcpdf.margin_right'),
            'top' => config('tcpdf.margin_top'),
            'bottom' => config('tcpdf.margin_bottom'),
            'header' => config('tcpdf.margin_header'),
            'footer' => config('tcpdf.margin_footer'),
        ];

        return $side ? $default[strtolower($side)] : $default;
    }

    public function genPDF($request, $htmlFormat = false) {
        // Page title
        PDF::changeFormat(config('tcpdf.page_format'));
        PDF::setPageOrientation(config('tcpdf.page_orientation'));

        PDF::setMargins($this->getMargins('left'), $this->getMargins('top'), $this->getMargins('right'));
        PDF::setAutoPageBreak(true, $this->getMargins('bottom'));

        $reportName = $request->get('name', 'Report');
        PDF::SetTitle($reportName);

        $html = '';
        $cssPath = storage_path('app') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'pdf.css';
        if (file_exists($cssPath)) {
            $styles = '<style type="text/css">' . PHP_EOL . File::get($cssPath) . PHP_EOL . '</style>';
            $html = $this->prepareForPdf($styles);
        }

        // PREPARING DATA
        $data = $this->getData($request)['tracks'];
        $user = Auth::user();
        $period = $request->get('periodText');

        PDF::SetAuthor($user->full_name);

        // PAGE 1. TITLE
        PDF::SetPrintHeader(false);
        PDF::SetPrintFooter(false);
        PDF::AddPage();

        $html = $html . PHP_EOL . $this->prepareForPdf(view('tracking.pdf.layout', [
                'reportName' => $reportName,
                'user'       => $user,
                'period'     => $period,
                'totalTime'  => '18:00 h'
        ]));
        PDF::WriteHTML($html, true, false, true, false, '');

        PDF::EndPage();

        // HEADER
        PDF::setHeaderMargin($this->getMargins('header'));
        $self = $this;
        PDF::setHeaderCallback(function () use ($self, $reportName, $user, $period) {
            $html = $self->prepareForPdf(view('tracking.pdf.header', [
                'reportName' => $reportName,
                'user'       => $user,
                'period'     => $period
            ]));
            PDF::writeHTML($html, true, false, true, false, '');
        });

        // FOOTER
        PDF::setFooterMargin($this->getMargins('bottom'));
        $self = $this;
        PDF::setFooterCallback(function () use ($self) {
            $html = $self->prepareForPdf(view('tracking.pdf.footer'));
            PDF::writeHTML($html, true, false, true, false, '');
        });

        // PAGE 2 and next
        PDF::SetPrintHeader();
        PDF::SetPrintFooter();
        PDF::AddPage();
        PDF::setPageOrientation('l');
        $html = $this->prepareForPdf(view('tracking.pdf.page', [
            'data' => $data
        ]));
        PDF::WriteHTML($html, true, false, true, false, '');
        PDF::EndPage();

        // GENERATE FILE
        $tmpFileName = storage_path('app') . Auth::id() . '-' . time() . '.pdf';
        if ($htmlFormat) {
            return $html;
        }
        PDF::Output($tmpFileName, 'F');
        return File::get($tmpFileName);
    }

    public function genCSV($request) {
        return '';
    }
}
