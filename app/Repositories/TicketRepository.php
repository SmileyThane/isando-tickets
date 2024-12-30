<?php

namespace App\Repositories;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\Notifications\ChangedFollowTicketStatus;
use App\Notifications\ChangedTicketStatus;
use App\Notifications\SendTicketReply;
use App\Permission;
use App\ProductCompanyUser;
use App\TeamCompanyUser;
use App\Ticket;
use App\TicketAnswer;
use App\TicketFilter;
use App\TicketMerge;
use App\TicketNotice;
use App\TicketStatus;
use App\UserNotificationStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TicketRepository
{

    protected $fileRepo;
    protected $ticketUpdateRepo;
    protected $ticketSelectRepo;

    public function __construct(
        FileRepository         $fileRepository,
        TicketUpdateRepository $ticketUpdateRepository,
        TicketSelectRepository $ticketSelectRepository
    )
    {
        $this->fileRepo = $fileRepository;
        $this->ticketUpdateRepo = $ticketUpdateRepository;
        $this->ticketSelectRepo = $ticketSelectRepository;
    }

    public function validate($request)
    {
        $params = [
            'from_entity_id' => 'required',
            'from_entity_type' => 'required',
            'to_entity_id' => 'required',
            'to_entity_type' => 'required',
            'priority_id' => 'required',
            'name' => 'required',
        ];
        $validator = Validator::make($request->all(), $params);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }

    public function all(Request $request)
    {
//        DB::enableQueryLog();

//        $key = 'ticket_' . base64_encode(json_encode($request->all()));
//        if (Cache::has($key)) {
//            return Cache::get($key);
//        }

        $ticketIds = [];
        $companyUser = Auth::user()->employee;
        $tickets = Ticket::query();
        $tickets = $this->ticketAccessFilter($companyUser, $tickets);
        $tickets->orWhere('from_company_user_id', $companyUser->id);
        $tickets->orWhere(static function ($ticketsQuery) use ($companyUser) {
            $ticketsQuery->where('to_company_user_id', $companyUser->id)
                ->orWhere('contact_company_user_id', $companyUser->id);
        })->select('id');
        if ($tickets) {
            $ticketIds = $tickets->pluck('id')->toArray();
        }
        $ticketResult = Ticket::query()->whereIn('id', $ticketIds)->whereNull('parent_id');
        if (($request->has('search') && !empty($request->search)) ||
            ($request->has('filter_id') && $request->filter_id !== null)
        ) {
            $ticketResult->where(
                static function ($query) use ($request) {
                    if ($request->filter_id === null) {
                        switch (strtolower($request->search_param)) {
                            case 'id':
                                $query->where('number', 'like', '%' . trim($request->search) . '%');
                                break;
                            case 'contact.user_data.full_name':
                            case 'contact':
                                $search = trim($request->search);
                                $query->whereHas('contact.userData', static function ($q) use ($search) {
                                    $q->where(static function ($_query) use ($search) {
                                        $_query->where('name', 'LIKE', '%' . $search . '%')
                                            ->orWhere('surname', 'LIKE', '%' . $search . '%')
                                            ->orWhereHas('emails', static function ($_q) use ($search) {
                                                $_q->where('email', 'LIKE', '%' . $search . '%');
                                            });
                                    });
                                });
                                break;
                            case 'company':
                                $search = trim($request->search);
                                $query->where(static function ($ticketsQuery) use ($search) {
                                    $ticketsQuery->whereHas('toCompany', static function ($q) use ($search) {
                                        $q->where('name', 'like', '%' . trim($search) . '%');
                                    });
                                    $ticketsQuery->orWhereHas('toClient', static function ($q) use ($search) {
                                        $q->where('name', 'like', '%' . trim($search) . '%');
                                    });
                                    $ticketsQuery->orWhereHas('fromCompany', static function ($q) use ($search) {
                                        $q->where('name', 'like', '%' . trim($search) . '%');
                                    });
                                    $ticketsQuery->orWhereHas('fromClient', static function ($q) use ($search) {
                                        $q->where('name', 'like', '%' . trim($search) . '%');
                                    });
                                });
                                break;
                            default:
                                $query->where('name', 'like', '%' . trim($request->search) . '%')
                                    ->orWhere('number', 'like', '%' . trim($request->search) . '%');
                        }
                    } elseif ($request->filter_id !== null) {
                        $filter = TicketFilter::find($request->filter_id);
                        if ($filter) {
                            $filterArray = json_decode($filter->filter_parameters, true);
                            foreach ($filterArray as $key => $filterItem) {
                                $value = $filterItem['compare_parameter'] === 'like' ? '%' . $filterItem['value'] . '%' : $filterItem['value'];
                                $query->where($filterItem['field'], $filterItem['compare_parameter'], $value);
                            }
                        }
                    } else {
                        $query->where('name', 'like', '%' . trim($request->search) . '%')
                            ->orWhere('description', 'like', '%' . trim($request->search) . '%');
                    }
                }
            );
        }
        if ($request->with_spam === "true") {
            $ticketResult->where('is_spam', 1);
        }
        if ($request->only_for_user === "true") {
            $ticketResult->where('to_company_user_id', Auth::user()->employee->id);
        }

        if ($request->only_open === "true") {
            $ticketResult->whereIn('status_id', [1, 2, 3, 4])->whereNot('ticket_type_id', 7);
        }
        if ($request->status) {
            if (is_array($request->status)) {
                $ticketResult->whereIn('status_id', $request->status);
            } else {
                $ticketResult->where('status_id', '=', $request->status);
            }
        } else {
            $ticketResult->where('status_id', '<>', Ticket::STATUS_CLOSED);
        }

        if ($request->type) {
            if (is_array($request->type)) {
                $ticketResult->whereIn('ticket_type_id', $request->type);
            } else {
                $ticketResult->where('ticket_type_id', '=', $request->type);
            }
        }

        if ($request->priority) {
            if (is_array($request->priority)) {
                $ticketResult->whereIn('priority_id', $request->priority);
            } else {
                $ticketResult->where('priority_id', '=', $request->priority);
            }
        }

        if ($request->minified && $request->minified === "true") {
            return $ticketResult
                ->with('creator.userData')
                ->select(
                    'id',
                    'name',
                    'number',
                    'from_entity_id',
                    'from_company_user_id',
                    'from_entity_type',
                    'created_at',
                    'updated_at',
                    'parent_id'
                )
                ->paginate(count($ticketIds));
        }

        $ticketResult
            ->with(
                'product',
                'priority',
                'status',
                'category',
                'billedBy'
            );
        $orderedField = $request->sort_by ?? 'id';
        $orderedDirection = $request->sort_val === 'false' ? 'asc' : 'desc';
        if ($orderedField === 'from_entity_id') {
            $ticketResult = $ticketResult->get();
            if ($orderedDirection === 'asc') {
                $ticketResult = $ticketResult->sortBy('from_company_name', SORT_NATURAL);
            } else {
                $ticketResult = $ticketResult->sortByDesc('from_company_name', SORT_NATURAL);
            }
        } elseif ($orderedField === 'contact.user_data.full_name') {
            $ticketResult = $ticketResult->get();
            if ($orderedDirection === 'asc') {
                $ticketResult = $ticketResult->sortBy('contact.userData.name', SORT_NATURAL);
            } else {
                $ticketResult = $ticketResult->sortByDesc('contact.userData.name', SORT_NATURAL);
            }
        } elseif ($orderedField === 'category.name') {
            $ticketResult = $ticketResult->get();
            if ($orderedDirection === 'asc') {
                $ticketResult = $ticketResult->sortBy('category.name', SORT_NATURAL);
            } else {
                $ticketResult = $ticketResult->sortByDesc('category.name', SORT_NATURAL);
            }
        } else {
            $ticketResult = $ticketResult->orderBy($orderedField, $orderedDirection);
        }

        $result = $ticketResult->paginate($request->per_page ?? count($ticketIds));
//        Cache::remember($key, 120, static function () use ($result) {
//            return $result;
//        });

//        dd(DB::getQueryLog());

        return $result;
    }

    private function ticketAccessFilter($companyUser, $tickets)
    {
        $permissionIds = $companyUser->getPermissionIds();
        if (!in_array(Permission::EMPLOYEE_CLIENT_ACCESS, $permissionIds, true)) {
            $tickets->orWhere([['to_entity_type', Company::class], ['to_entity_id', $companyUser->company_id]]);
            $tickets->orWhere([['from_entity_type', Company::class], ['from_entity_id', $companyUser->company_id]]);
        } else {
            $clientIds = [];
            $clientCompanyUsers = $companyUser->assignedToClients()->get();
            if ($clientCompanyUsers !== null) {
                $clientIds = $clientCompanyUsers->pluck('client_id')->toArray();
            }
            $tickets->orWhere(static function ($ticketsQuery) use ($clientIds) {
                $ticketsQuery->where('from_entity_type', Client::class)
                    ->whereIn('from_entity_id', $clientIds);
            });
            $tickets->orWhere(static function ($ticketsQuery) use ($clientIds) {
                $ticketsQuery->where('to_entity_type', Client::class)
                    ->whereIn('to_entity_id', $clientIds);
            });
        }
        if (in_array(Permission::EMPLOYEE_TICKET_ADMIN_ACCESS, $permissionIds, true)) {
            $products = ProductCompanyUser::where('company_user_id', $companyUser->id)->get();
            if ($products) {
                $productsIds = $products->pluck('id')->toArray();
                if ($productsIds) {
                    $tickets->orWhereIn('to_product_id', $productsIds);
                }
            }
        }
        if (in_array(Permission::EMPLOYEE_TICKET_MANAGER_ACCESS, $permissionIds, true)) {
            $teams = TeamCompanyUser::where('company_user_id', $companyUser->id)->get();
            if ($teams) {
                $teamsIds = $teams->pluck('id')->toArray();
                if ($teamsIds) {
                    $tickets->orWhereIn('to_team_id', $teamsIds);
                }
            }
        }
        if (in_array(Permission::EMPLOYEE_CLIENT_ACCESS, $permissionIds, true)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', Auth::user()->employee->id)->first();
            if ($clientCompanyUser) {
                $tickets->orWhere(static function ($query) use ($clientCompanyUser) {
                    $query->where('replicated_to_entity_type', Client::class)
                        ->where('replicated_to_entity_id', $clientCompanyUser->client_id);
                });
            }
        } else {
            $tickets->orWhere(static function ($query) {
                $query->where('replicated_to_entity_type', Company::class)
                    ->where('replicated_to_entity_id', Auth::user()->employee->company_id);
            });
        }

        return $tickets;
    }

    public function find($id)
    {
        $ticket = Ticket::query()->where('id', $id)
            ->with(
                'creator.userData',
                'assignedPerson.userData',
                'contact.userData',
                'product.category',
                'team',
                'category',
                'priority',
                'status',
                'answers.employee.userData',
                'answers.attachments',
                'mergedChild',
                'childTickets.answers.employee.userData',
                'childTickets.attachments',
                'childTickets.notices.employee.userData',
                'childTickets.answers.attachments',
                'histories.employee.userData',
                'notices.employee.userData',
                'attachments',
                'mergedParent',
                'billedBy',
                'followers'
            )->first();
        if ($ticket) {
            $ticket->append(['from', 'to'])->makeVisible(['from', 'to', 'description', 'contact', 'assignedPerson']);
        }

        return $ticket;
    }

    public function create(Request $request, $employeeId = null): Ticket
    {
        if ($request->from_entity_type === CompanyUser::class) {
            $request->request->add(['contact_company_user_id' => $request->from_entity_id]);
            $employeeId = $request->from_entity_id;
        }
        $ticket = new Ticket();

        $ticket = $this->preparePredefinedTitles($ticket, $request);

        $ticket->name = $request->name;
        $ticket->description = $request->description;
        $ticket->from_entity_id = $request->from_entity_id;
        $ticket->from_entity_type = $request->from_entity_type;
        $ticket->to_entity_id = $request->to_entity_id;
        $ticket->to_entity_type = $request->to_entity_type;
        $ticket->from_company_user_id = $employeeId ?? Auth::user()->employee->id;
        $ticket->contact_company_user_id = $request->contact_company_user_id;
        $ticket->to_company_user_id = $request->to_company_user_id;
        $ticket->to_team_id = $request->to_team_id;
        $ticket->to_product_id = $request->to_product_id;
        $ticket->priority_id = $request->priority_id;
        $ticket->due_date = $request->due_date;
        $ticket->availability = $request->availability;
        $ticket->connection_details = $request->connection_details;
        $ticket->access_details = $request->access_details;
        $ticket->category_id = $request->category_id;
        $ticket->save();
        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            $this->fileRepo->store($file, $ticket->id, Ticket::class);
        }
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_created');
        $this->ticketUpdateRepo->addHistoryItem($ticket->id, $employeeId, $historyDescription);

        return $ticket;
    }

    private function preparePredefinedTitles($ticket, $request)
    {
        if ($request->contact_company_user_id) {
            $companyUser = CompanyUser::find($request->contact_company_user_id);
            if ($companyUser) {
                $ticket->contact_full_name = $companyUser->userData->full_name;
            }
        }

        if ($request->to_company_user_id) {
            $companyUser = CompanyUser::find($request->to_company_user_id);
            if ($companyUser) {
                $ticket->assigned_person_full_name = $companyUser->userData->full_name;
            }
        }

        if ($request->from_entity_type && $request->from_entity_id) {
            $entity = $request->from_entity_type::find($request->from_entity_id);
            if ($entity) {
                $ticket->from_entity_name = $entity->name;
            }
        }

        return $ticket;
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::with('followers')->find($id);
        $request['ticket_action'] = Ticket::ACTION_UPDATE_TICKET;
        if ($request->status_id !== $ticket->status_id) {
            $this->updateStatus($request, $id);
        } else {

            $ticket = $this->preparePredefinedTitles($ticket, $request);

            $ticket->contact_company_user_id = $this->ticketUpdateRepo->setContactCompanyUserId(
                $ticket->contact_company_user_id,
                $request->contact_company_user_id,
                $ticket->id
            );
            $ticket->to_company_user_id = $this->ticketUpdateRepo->setCompanyUserId(
                $ticket->to_company_user_id,
                $request->to_company_user_id,
                $ticket->id
            );
            $this->ticketUpdateRepo->setFrom(
                $ticket->from_entity_type,
                $request->from_entity_type,
                $ticket->from_entity_id,
                $request->from_entity_id,
                $ticket->id
            );

            $ticket->description = str_replace("<p>&nbsp;</p>", '<br/>', $ticket->description);

            $ticket->name = $request->name;
            $ticket->description = $request->description;
            $ticket->from_entity_id = $request->from_entity_id;
            $ticket->from_entity_type = $request->from_entity_type;
            $ticket->internal_billing_id = $request->internal_billing_id;
            $ticket->to_team_id = $this->ticketUpdateRepo->setTeamId($ticket->to_team_id, $request->to_team_id, $ticket->id);
            $ticket->due_date = $this->ticketUpdateRepo->setDueDate($ticket->due_date, $request->due_date, $ticket->id);
            $ticket->priority_id = $this->ticketUpdateRepo->setPriorityId($ticket->priority_id, $request->priority_id, $ticket->id);
            $ticket->ticket_type_id = $this->ticketUpdateRepo->setTicketTypeId($ticket->ticket_type_id, $request->ticket_type_id, $ticket->id);
            $ticket->to_product_id = $this->ticketUpdateRepo->setProductId($ticket->to_product_id, $request->to_product_id, $ticket->id);
            $ticket->access_details = $this->ticketUpdateRepo->setAccessDetails($ticket->access_details, $request->access_details, $ticket->id);
            $ticket->connection_details = $this->ticketUpdateRepo->setConnectionDetails($ticket->connection_details, $request->connection_details, $ticket->id);
            $ticket->category_id = $this->ticketUpdateRepo->setCategoryId($ticket->category_id, $request->category_id, $ticket->id);
            $ticket->followers()->sync(collect($request->followers)->pluck('id'));
            $ticket->save();
            $request->status_id = 2;
            $this->updateStatus($request, $id, null, false);
        }

        return $ticket;
    }

    public function updateStatus(Request $request, $id, $employeeId = null, $withHistory = true): bool
    {
        $isInternal = ($request->is_internal === "true");
        $ticket = Ticket::query()->find($id);
        $ticket->status_id = $request->status_id;
        $ticket->save();
        if ($isInternal === false) {
            $this->emailEmployees(
                $this->filterEmailRecipients($ticket->to->employees, $ticket, $request->ticket_action ?? ''),
                $ticket,
                ChangedTicketStatus::class,
            );
            // Notify followers
            $followers = $ticket->followers()->get()->all();
            foreach ($followers as $follower) {
                $follower->notify(
                    new ChangedFollowTicketStatus(
                        $follower->employee->companyData->name,
                        $follower->title,
                        $follower->full_name,
                        $ticket->name,
                        $ticket->id,
                        $follower->language->short_code
                    )
                );
            }
        }

        if ($withHistory === true) {
            if ($request->status_id === TicketStatus::CLOSED) {
                $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_closed');
            } else {
                $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription(
                    'status_updated',
                    $ticket->status->name,
                    true,
                    'ticket_statuses'
                );
            }
            $this->ticketUpdateRepo->addHistoryItem($ticket->id, $employeeId, $historyDescription);
        }
        return true;
    }

    public function emailEmployees($companyUsers, Ticket $ticket, $notificationClass): bool
    {
        foreach ($companyUsers as $companyUser) {
            if ($companyUser !== null) {
                $user = $companyUser->userData;
                $company = $companyUser->companyData;
                if ($user && $user->is_active) {
                    try {
                        $user->notify(
                            new $notificationClass(
                                $company->name,
                                $user->title,
                                $user->full_name,
                                $ticket->name,
                                $ticket->id,
                                $user->language->short_code
                            )
                        );
                    } catch (Throwable $throwable) {
                        Log::error($throwable);
                        //hack for broken notification system
                    }
                }
            }
        }

        return true;
    }

    public function filterEmailRecipients($employees, Ticket $ticket, string $notificationType): array
    {
        $recipients = [];
        foreach ($employees as $employee) {
            $user = $employee->userData;
            if (!$user) {
                continue;
            }

            if ($user->notificationStatuses->isEmpty()) {
                $recipients[] = $employee;
            } else {
                switch ($notificationType) {
                    case Ticket::ACTION_NEW_TICKET:
                        if ($user->hasNotificationStatus(UserNotificationStatus::TICKET_NEW_ASSIGNED_TO_COMPANY)
                            || ($user->hasNotificationStatus(UserNotificationStatus::TICKET_NEW_ASSIGNED_TO_ME)
                                && $ticket->assignedPerson
                                && $user->id === $ticket->assignedPerson->user_id)
                            || ($user->hasNotificationStatus(UserNotificationStatus::TICKET_NEW_ASSIGNED_TO_TEAM)
                                && $ticket->team && $ticket->team->employees->contains('company_user_id', $employee->id))
                        ) {
                            $recipients[] = $employee;
                        }
                        break;
                    case Ticket::ACTION_UPDATE_TICKET:
                        if ($user->hasNotificationStatus(UserNotificationStatus::TICKET_UPDATED_ASSIGNED_TO_COMPANY)
                            || ($user->hasNotificationStatus(UserNotificationStatus::TICKET_UPDATED_ASSIGNED_TO_ME)
                                && $ticket->assignedPerson
                                && $user->id === $ticket->assignedPerson->id)
                            || ($user->hasNotificationStatus(UserNotificationStatus::TICKET_UPDATED_ASSIGNED_TO_TEAM)
                                && $ticket->team && $ticket->team->employees->contains('company_user_id', $employee->id))
                        ) {
                            $recipients[] = $employee;
                        }
                        break;
                    case Ticket::ACTION_ATTACH_TEAM_TO_TICKET:
                        if ($user->hasNotificationStatus(UserNotificationStatus::TICKET_UPDATED_ASSIGNED_TO_TEAM)) {
                            $recipients[] = $employee;
                        }
                        break;
                    default:
                        if ($user->hasNotificationStatus(UserNotificationStatus::TICKET_CLIENT_RESPONSE_ASSIGNED_TO_ME)
                            && $ticket->assignedPerson
                            && $user->id === $ticket->assignedPerson->user_id) {
                            $recipients[] = $employee;
                        }
                }
            }
        }

        return $recipients;
    }

    public function updateDescription(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->description = str_replace(['<br>', '<br>'], '', $request->description);;
        $ticket->save();

        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            $this->fileRepo->store($file, $ticket->id, Ticket::class);
        }

        return $ticket;
    }

    public function markAsSpam($id): bool
    {
        $result = false;
        $ticket = Ticket::find($id);
        if ($ticket) {
            $ticket->is_spam = !$ticket->is_spam;
            $ticket->save();
            $result = $ticket->is_spam;
        }

        return $result;
    }

    public function attachTeam(Request $request, $id): bool
    {
        $ticket = Ticket::find($id);
        $ticket->team_id = $request->team_id;
        $ticket->save();
        $this->ticketUpdateRepo->setTeamId($ticket->team_id, $request->team_id, $ticket->id);

        return true;
    }

    public function attachEmployee(Request $request, $id): bool
    {
        $ticket = Ticket::find($id);
        $ticket->to_company_user_id = $request->to_company_user_id;
        $ticket->save();
        $this->ticketUpdateRepo->setCompanyUserId(
            $ticket->to_company_user_id,
            $request->to_company_user_id,
            $ticket->id
        );

        return true;
    }

    public function attachContact(Request $request, $id): bool
    {
        $ticket = Ticket::find($id);
        $ticket->contact_company_user_id = $request->contact_company_user_id;
        $ticket->save();
        $this->ticketUpdateRepo->setContactCompanyUserId(
            $ticket->contact_company_user_id,
            $request->contact_company_user_id,
            $ticket->id
        );

        return true;
    }

    public function addAnswer(Request $request, $id, $employeeId = null, $replyShouldBeEmailed = true): bool
    {
        $isInternal = ($request->is_internal === "true") ? 1 : 0;
        $ticketAnswer = new TicketAnswer();
        $ticketAnswer->ticket_id = $id;
        $ticketAnswer->company_user_id = $employeeId ?? Auth::user()->employee->id;
        $ticketAnswer->answer = $request->answer;
        $ticketAnswer->is_internal = $isInternal;
        $ticketAnswer->save();

        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            $this->fileRepo->store($file, $ticketAnswer->id, TicketAnswer::class);
        }
        $employee = $employeeId !== null ? CompanyUser::find($employeeId) : Auth::user()->employee;
        if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $request->status_id = 3;
        } else {
            $request->status_id = 4;
        }

        if ($replyShouldBeEmailed && (bool)$isInternal === false) {
            $this->sendEmailWithReply($id, $request->answer, $ticketAnswer->attachments);
        }
        $this->updateStatus($request, $ticketAnswer->ticket_id, $employeeId, false);
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('answer_added');
        $this->ticketUpdateRepo->addHistoryItem($ticketAnswer->ticket_id, $employeeId, $historyDescription);

        return true;
    }

    public function sendEmailWithReply($ticketId, $reply, $files): bool
    {
        $ticket = Ticket::find($ticketId);

        $assignedPerson = $ticket->assignedPerson;
        $ticketCreator = $ticket->creator;
        $contact = $ticket->contact;

        if ($assignedPerson && Auth::id() !== $assignedPerson->user_id) {
            Log::debug('Reply to assignedPerson');
            $assignedPerson->userData->notify(
                new SendTicketReply(
                    $ticket->name,
                    $reply,
                    $files
                )
            );
        }

        if ($ticketCreator && Auth::id() !== $ticketCreator->user_id) {
            Log::debug('Reply to creator');
            $ticketCreator->userData->notify(
                new SendTicketReply(
                    $ticket->name,
                    $reply,
                    $files
                )
            );
        }

        if ($contact && Auth::id() !== $contact->user_id) {
            Log::debug('Reply to contact');
            $contact->userData->notify(
                new SendTicketReply(
                    $ticket->name,
                    $reply,
                    $files
                )
            );
        }

        return true;
    }

    public function editAnswer(Request $request, $id, $employeeId = null): bool
    {
        $ticketAnswer = TicketAnswer::find($request->answer_id);
        $ticketAnswer->answer = $request->answer;
        $ticketAnswer->save();

        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            $this->fileRepo->store($file, $ticketAnswer->id, TicketAnswer::class);
        }
        $employee = $employeeId !== null ? CompanyUser::find($employeeId) : Auth::user()->employee;
        if ($employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $request->status_id = 3;
        } else {
            $request->status_id = 4;
        }
        $this->updateStatus($request, $ticketAnswer->ticket_id, $employeeId, false);
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('answer_updated');
        $this->ticketUpdateRepo->addHistoryItem($ticketAnswer->ticket_id, null, $historyDescription);

        return true;
    }

    public function addNotice(Request $request, $id): bool
    {
        $ticketNotice = new TicketNotice();
        $ticketNotice->company_user_id = Auth::user()->employee->id;
        $ticketNotice->notice = $request->notice;
        $ticketNotice->ticket_id = $id;
        $ticketNotice->save();
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('notice_added');
        $this->ticketUpdateRepo->addHistoryItem($ticketNotice->ticket_id, null, $historyDescription);

        return true;
    }

    public function editNotice(Request $request, $id): bool
    {
        $ticketNotice = TicketNotice::query()->find($request->notice_id);
        $ticketNotice->notice = $request->notice;
        $ticketNotice->save();
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('notice_updated');
        $this->ticketUpdateRepo->addHistoryItem($ticketNotice->ticket_id, null, $historyDescription);

        return true;
    }

    public function addMerge(Request $request): bool
    {
        if ($request->child_ticket_id) {
            $childNumbers = '';
            $parentTicket = Ticket::find($request->parent_ticket_id);
            foreach ($request->child_ticket_id as $ticketId) {
                $this->addLink($request, false);
                $ticket = Ticket::find($ticketId);
                $ticket->parent_id = $request->parent_ticket_id;
                $ticket->unifier_id = Auth::id();
                $ticket->merged_at = now();
                $ticket->save();
                $request->status_id = 5;
                $this->updateStatus($request, $ticketId, null, false);
                $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription(
                    'ticket_merged',
                    $parentTicket->number
                );
                $this->ticketUpdateRepo->addHistoryItem($ticket->id, null, $historyDescription);
                $childNumbers .= $ticket->number . ' ';
            }
            $parentTicket->unifier_id = Auth::id();
            $parentTicket->merged_at = now();
            $parentTicket->merge_comment = $request->merge_comment;
            $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_merged', $childNumbers);
            $this->ticketUpdateRepo->addHistoryItem($parentTicket->id, null, $historyDescription);
            $parentTicket->save();

            return true;
        }

        return false;
    }

    public function addLink(Request $request, $wihHistory = true): bool
    {
        if ($request->child_ticket_id) {
            foreach ($request->child_ticket_id as $ticketId) {
                TicketMerge::query()->firstOrCreate(
                    [
                        'parent_ticket_id' => $request->parent_ticket_id,
                        'child_ticket_id' => $ticketId
                    ],
                    [
                        'merge_comment' => $request->merge_comment,
                        'merged_by_user_id' => Auth::id()

                    ]
                );
                if ($wihHistory === true) {
                    $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_linked');
                    $this->ticketUpdateRepo->addHistoryItem($ticketId, null, $historyDescription);
                    $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_linked');
                    $this->ticketUpdateRepo->addHistoryItem($request->parent_ticket_id, null, $historyDescription);
                }
            }

            return true;
        }

        return false;
    }

    public function addFilter(Request $request): ?TicketFilter
    {
        try {
            $ticketFilter = new TicketFilter();
            $ticketFilter->user_id = Auth::id();
            $ticketFilter->name = $request->name;
            $ticketFilter->filter_parameters = json_encode($request->filter_parameters);
            $ticketFilter->save();

            return $ticketFilter;
        } catch (Throwable $throwable) {
            return null;
        }
    }

    public function getFilters()
    {
        return TicketFilter::where('user_id', Auth::id())->get();
    }

    public function removeFilter($filterId): bool
    {
        try {
            TicketFilter::where('user_id', Auth::id())
                ->where('id', '=', $filterId)
                ->delete();

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function delete($id): bool
    {
        $result = false;
        $ticket = Ticket::find($id);
        if ($ticket) {
            TicketMerge::where('parent_ticket_id', $id)->orWhere('child_ticket_id', $id)->delete();
            $ticket->delete();
            $result = true;
        }
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_deleted');
        $this->ticketUpdateRepo->addHistoryItem($ticket->id, null, $historyDescription);

        return $result;
    }

    public function getFilterParameters(Request $request): array
    {
        return $this->ticketSelectRepo->getFilterParameters($request);
    }

    public function removeMerge($id): bool
    {
        $ticket = Ticket::find($id);
        $ticket->parent_id = null;
        $ticket->status_id = TicketStatus::OPEN;
        $ticket->save();

        return true;
    }
}
