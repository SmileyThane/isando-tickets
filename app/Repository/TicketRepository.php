<?php


namespace App\Repository;


use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\Notifications\ChangedTicketStatus;
use App\ProductCompanyUser;
use App\Role;
use App\TeamCompanyUser;
use App\Ticket;
use App\TicketAnswer;
use App\TicketMerge;
use App\TicketNotice;
use App\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TicketRepository
{

    protected $fileRepo;
    protected $ticketUpdateRepo;

    public function __construct(FileRepository $fileRepository, TicketUpdateRepository $ticketUpdateRepository)
    {
        $this->fileRepo = $fileRepository;
        $this->ticketUpdateRepo = $ticketUpdateRepository;
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
//                DB::enableQueryLog();
        $ticketIds = [];
        $companyUser = Auth::user()->employee;
        $tickets = Ticket::where('from_company_user_id', $companyUser->id);
        $tickets = $this->ticketRoleFilter($companyUser, $tickets);
        $tickets->where(function ($ticketsQuery) use ($companyUser) {
            $ticketsQuery->where('to_company_user_id', $companyUser->id)
                ->orWhere('contact_company_user_id', $companyUser->id);
        })->select('id');
        if ($tickets) {
            $ticketIds = $tickets->pluck('id')->toArray();
        }
        $ticketResult = Ticket::whereIn('id', $ticketIds);
        if ($request->has('search') && trim($request->search)) {
            $ticketResult->where(
                static function ($query) use ($request) {
                    if ($request->has('search_param')) {
                        $key = $request->search_param === 'ID' ? 'id' : 'name';
                        $query->where($key, 'like', '%' . trim($request->search) . '%');
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
        if ($request->minified && $request->minified === 'true') {
            return $ticketResult->select('id', 'name', 'from_entity_id', 'from_entity_type', 'updated_at')->paginate(count($ticketIds));
        }
//        dd(DB::getQueryLog());
        $ticketResult
            ->with(
                'creator.userData', 'assignedPerson.userData',
                'contact.userData', 'product', 'team',
                'priority', 'status', 'category');
        $orderedField = $request->sort_by ?? 'id';
        $orderedDirection = $request->sort_val === 'false' ? 'asc' : 'desc';
        if ($orderedField === 'from_entity_id') {
            $ticketResult = $ticketResult->get();
            if ($orderedDirection === 'asc') {
                $ticketResult = $ticketResult->sortBy('from_company_name', SORT_NATURAL, false);
            } else {
                $ticketResult = $ticketResult->sortByDesc('from_company_name', SORT_NATURAL, true);
            }
        } else {
            $ticketResult = $ticketResult->orderBy($orderedField, $orderedDirection);
        }
        return $ticketResult = $ticketResult->paginate($request->per_page ?? count($ticketIds));
    }

    private function ticketRoleFilter($companyUser, $tickets)
    {
        if (!$companyUser->hasRole(Role::COMPANY_CLIENT)) {
            $tickets->orWhere([['to_entity_type', Company::class], ['to_entity_id', $companyUser->company_id]]);
        }
        if ($companyUser->hasRole(Role::LICENSE_OWNER) || $companyUser->hasRole(Role::ADMIN)) {
            $products = ProductCompanyUser::where('company_user_id', $companyUser->id)->get();
            if ($products) {
                $productsIds = $products->pluck('id')->toArray();
                if ($productsIds) {
                    $tickets->orWhereIn('to_product_id', $productsIds);
                }
            }

        }
        if ($companyUser->hasRole(Role::MANAGER)) {
            $teams = TeamCompanyUser::where('company_user_id', $companyUser->id)->get();
            if ($teams) {
                $teamsIds = $teams->pluck('id')->toArray();
                if ($teamsIds) {
                    $tickets->orWhereIn('to_team_id', $teamsIds);
                }
            }
        }
        if ($companyUser->hasRole(Role::COMPANY_CLIENT)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', Auth::user()->employee->id)->first();
            if ($clientCompanyUser) {
                $tickets->orWhere(function ($query) use ($clientCompanyUser) {
                    $query->where('replicated_to_entity_type', Client::class)
                        ->where('replicated_to_entity_id', $clientCompanyUser->client_id);
                });
            }
        } else {
            $tickets->orWhere(function ($query) {
                $query->where('replicated_to_entity_type', Company::class)
                    ->where('replicated_to_entity_id', Auth::user()->employee->company_id);
            });
        }
        return $tickets;
    }


    public function find($id)
    {
        return Ticket::where('id', $id)
            ->with('creator.userData', 'assignedPerson.userData', 'contact.userData', 'product', 'team', 'category',
                'priority', 'status', 'answers.employee.userData', 'answers.attachments', 'mergedChild',
                'childTickets.answers.employee.userData', 'childTickets.notices.employee.userData', 'childTickets.answers.attachments',
                'histories.employee.userData', 'notices.employee.userData', 'attachments', 'mergedParent')->first()->makeVisible(['to']);
    }

    public function create(Request $request, $employeeId = null): Ticket
    {
        $ticket = new Ticket();
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

    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if ($request->status_id !== $ticket->status_id) {
            $this->updateStatus($request, $id);
        } else {
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
                $ticket->id);
            $ticket->from_entity_id = $request->from_entity_id;
            $ticket->from_entity_type = $request->from_entity_type;
            $ticket->to_team_id = $this->ticketUpdateRepo->setTeamId($ticket->to_team_id, $request->to_team_id, $ticket->id);
            $ticket->due_date = $this->ticketUpdateRepo->setDueDate($ticket->due_date, $request->due_date, $ticket->id);
            $ticket->priority_id = $this->ticketUpdateRepo->setPriorityId($ticket->priority_id, $request->priority_id, $ticket->id);
            $ticket->ticket_type_id = $this->ticketUpdateRepo->setTicketTypeId($ticket->ticket_type_id, $request->ticket_type_id, $ticket->id);
            $ticket->to_product_id = $this->ticketUpdateRepo->setProductId($ticket->to_product_id, $request->to_product_id, $ticket->id);
            $ticket->access_details = $this->ticketUpdateRepo->setAccessDetails($ticket->access_details, $request->access_details, $ticket->id);
            $ticket->connection_details = $this->ticketUpdateRepo->setConnectionDetails($ticket->connection_details, $request->connection_details, $ticket->id);
            $ticket->category_id = $this->ticketUpdateRepo->setCategoryId($ticket->category_id, $request->category_id, $ticket->id);
            $ticket->save();
            $request->status_id = 2;
            $this->updateStatus($request, $id, null, false);
        }
        return $ticket;
    }

    public function updateStatus(Request $request, $id, $employeeId = null, $withHistory = true): bool
    {
        $ticket = Ticket::find($id);
        $ticket->status_id = $request->status_id;
        $ticket->save();
        $this->emailEmployees([$ticket->creator], $ticket, ChangedTicketStatus::class);
        if ($withHistory === true) {
            $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription(
                'status_updated',
                $ticket->status->name,
                true,
                'ticket_statuses'
            );
            $this->ticketUpdateRepo->addHistoryItem($ticket->id, $employeeId, $historyDescription);
        }
        return true;
    }

    public function emailEmployees($companyUsers, Ticket $ticket, $notificationClass): bool
    {
        foreach ($companyUsers as $companyUser) {
            $user = $companyUser->userData;
            $company = $companyUser->companyData;
            if ($user->is_active) {
                try {
                    $user->notify(new $notificationClass($company->name, $user->full_name, $ticket->name, $ticket->id, $user->language->short_code));
                } catch (\Throwable $throwable) {
                    Log::error($throwable);
                    //hack for broken notification system
                }
            }

        }
        return true;
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

    public function markAsSpam($id): bool
    {
        $result = false;
        $ticket = Ticket::find($id);
        if ($ticket) {
            $ticket->is_spam = !$ticket->is_spam;
            $ticket->save();
            $result = $ticket->is_spam;
        }
//        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_spam');
//        $this->ticketUpdateRepo->addHistoryItem($ticket->id, null, $historyDescription);
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
        $this->ticketUpdateRepo->setCompanyUserId($ticket->to_company_user_id, $request->to_company_user_id, $ticket->id);
        return true;
    }

    public function attachContact(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->contact_company_user_id = $request->contact_company_user_id;
        $ticket->save();
        $this->ticketUpdateRepo->setContactCompanyUserId($ticket->contact_company_user_id, $request->contact_company_user_id, $ticket->id);
        return true;
    }

    public function addAnswer(Request $request, $id, $employeeId = null): bool
    {
        $ticketAnswer = new TicketAnswer();
        $ticketAnswer->ticket_id = $id;
        $ticketAnswer->company_user_id = $employeeId ?? Auth::user()->employee->id;
        $ticketAnswer->answer = $request->answer;
        $ticketAnswer->save();
        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            $this->fileRepo->store($file, $ticketAnswer->id, TicketAnswer::class);
        }
        $employee = $employeeId !== null ? CompanyUser::find($employeeId) : Auth::user()->employee;
        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $request->status_id = 3;
        } else {
            $request->status_id = 4;
        }
        $this->updateStatus($request, $ticketAnswer->ticket_id, $employeeId, false);
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('answer_added');
        $this->ticketUpdateRepo->addHistoryItem($ticketAnswer->ticket_id, null, $historyDescription);
        return true;
    }

    public function addNotice(Request $request, $id): bool
    {
        $ticketNotice = new TicketNotice;
        $ticketNotice->company_user_id = Auth::user()->employee->id;
        $ticketNotice->notice = $request->notice;
        $ticketNotice->ticket_id = $id;
        $ticketNotice->save();
        $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('notice_added');
        $this->ticketUpdateRepo->addHistoryItem($ticketNotice->ticket_id, null, $historyDescription);
        return true;
    }

    public function addMerge(Request $request): bool
    {
        if ($request->child_ticket_id) {
            foreach ($request->child_ticket_id as $ticketId) {
                $this->addLink($request, false);
                $ticket = Ticket::find($ticketId);
                $ticket->parent_id = $request->parent_ticket_id;
                $ticket->save();
                $request->status_id = 5;
                $this->updateStatus($request, $ticketId, null, false);
                $historyDescription = $this->ticketUpdateRepo->makeHistoryDescription('ticket_merged');
                $this->ticketUpdateRepo->addHistoryItem($ticket->id, null, $historyDescription);
            }
            $parentTicket = Ticket::find($request->parent_ticket_id);
            $parentTicket->unifier_id = Auth::id();
            $parentTicket->merged_at = now();
            $parentTicket->save();
            return true;
        }
        return false;
    }

    public function addLink(Request $request, $wihHistory = true): bool
    {
        if ($request->child_ticket_id) {
            foreach ($request->child_ticket_id as $ticketId) {
                $ticketMerge = new TicketMerge();
                $ticketMerge->merged_by_user_id = Auth::id();
                $ticketMerge->merge_comment = $request->merge_comment;
                $ticketMerge->parent_ticket_id = $request->parent_ticket_id;
                $ticketMerge->child_ticket_id = $ticketId;
                $ticketMerge->save();
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

    public function removeMerge($id): bool
    {
        $ticket = Ticket::find($id);
        $ticket->parent_id = null;
        $ticket->status_id = TicketStatus::OPEN;
        $ticket->save();
        return true;
    }

    public function filterEmployeesByRoles($employees, $roles)
    {
        return $employees->filter(function ($item) use ($roles) {
            foreach ($item->roles as $role) {
                if (in_array($role->id, $roles, true)) {
                    return $item;
                }
            }
            return null;
        });
    }

}
