<?php

namespace App\Repositories;

use App\Language;
use App\TicketCategory;
use App\TicketPriority;
use App\TicketStatus;
use App\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketSelectRepository
{
    protected $translationsArray;

    public function getFilterParameters(Request $request): array
    {
        $this->translationsArray = Language::find(Auth::user()->language_id ?? 1)->lang_map;
        $logicalCompareParams = $this->getLogicalCompareParameters();
        $stringCompareParams = $this->getStringCompareParameters();
        return [
            [
                'name' => $this->translationsArray->ticket->number,
                'active' => false,
                'type' => 'string',
                'query_fields' => ['id'],
                'compareParams' => $logicalCompareParams,
            ],
            [
                'name' => $this->translationsArray->ticket->status,
                'active' => false,
                'type' => 'select',
                'query_fields' => ['status_id'],
                'compareParams' => $logicalCompareParams,
                'prefilled_values' => TicketStatus::all()
            ],
            [
                'name' => $this->translationsArray->ticket->priority,
                'active' => false,
                'type' => 'select',
                'query_fields' => ['priority_id'],
                'compareParams' => $logicalCompareParams,
                'prefilled_values' => TicketPriority::all()
            ],
            [
                'name' => $this->translationsArray->main->category,
                'active' => false,
                'type' => 'select',
                'query_fields' => ['category_id'],
                'compareParams' => $logicalCompareParams,
                'prefilled_values' => TicketCategory::all()
            ],
            [
                'name' => $this->translationsArray->main->type,
                'active' => false,
                'type' => 'select',
                'query_fields' => ['ticket_type_id'],
                'compareParams' => $logicalCompareParams,
                'prefilled_values' => TicketType::all()
            ],
            [
                'name' => $this->translationsArray->ticket->subject,
                'active' => false,
                'type' => 'string',
                'query_fields' => ['name'],
                'compareParams' => $stringCompareParams,
            ],
            [
                'name' => $this->translationsArray->ticket->product_name,
                'active' => false,
                'type' => 'select',
                'query_fields' => ['to_product_id'],
                'compareParams' => [
                    0 => [
                        'name' => $this->translationsArray->filter->equal,
                        'value' => '='
                    ]
                ],
                'prefilled_values' => (new ProductRepository())->all($request)->all(),
            ],
            [
                'name' => $this->translationsArray->team->members,
                'active' => false,
                'type' => 'select',
                'query_fields' => ['to_team_id'],
                'compareParams' => [
                    0 => [
                        'name' => $this->translationsArray->filter->equal,
                        'value' => '='
                    ]
                ],
                'prefilled_values' => (new TeamRepository())->all($request)->all(),
            ],
//            [
//                'name' => 'members',
//                'query_fields' => ['to_company_user_id'],
//                'compareParams' => [
//                    'name' => $this->translationsArray->filter->equal,
//                    'value' => '='
//                ],
//            ]
        ];
    }

    private function getLogicalCompareParameters(): array
    {

        return [
            [
                'name' => $this->translationsArray->filter->more,
                'value' => '>'
            ],
            [
                'name' => $this->translationsArray->filter->more_or_equal,
                'value' => '>='
            ],
            [
                'name' => $this->translationsArray->filter->equal,
                'value' => '='
            ],
            [
                'name' => $this->translationsArray->filter->less_or_equal,
                'value' => '<='
            ],
            [
                'name' => $this->translationsArray->filter->less,
                'value' => '<'
            ]
        ];
    }

    private function getStringCompareParameters(): array
    {

        return [
            [
                'name' => $this->translationsArray->filter->similar,
                'value' => 'like'
            ],
            [
                'name' => $this->translationsArray->filter->equal,
                'value' => '='
            ]
        ];
    }
}
