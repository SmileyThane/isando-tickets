<?php


namespace App\Repository;


use App\Company;
use App\CompanyUser;
use App\Notifications\NewTicket;
use App\ProductCompanyUser;
use App\Role;
use App\TeamCompanyUser;
use App\Ticket;
use App\TicketAnswer;
use App\TicketHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketRepository
{

    protected $fileRepo;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepo = $fileRepository;
    }

    public function validate($request)
    {
        $params = [
            'from_entity_id' => 'required',
            'from_entity_type' => 'required',
            'to_entity_id' => 'required',
            'to_entity_type' => 'required',
//            'from_company_user_id' => 'required',
//            'contact_company_user_id' => 'required',
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
        $companyUser = Auth::user()->employee;
        $tickets = Ticket::where('from_company_user_id', $companyUser->id)
            ->orWhere('to_company_user_id', $companyUser->id)
            ->orWhere('contact_company_user_id', $companyUser->id);
        if ($companyUser->hasRole(Role::LICENSE_OWNER) || $companyUser->hasRole(Role::ADMIN)) {
            $products = ProductCompanyUser::where('company_user_id', $companyUser->id)->get();
            if ($products) {
                $productsIds = $products->pluck('id')->toArray();
                if ($productsIds) {
                    $tickets->orWhereIn('to_product_id', $productsIds);
                }
            }
            $tickets->orWhere([['to_entity_type', Company::class], ['to_entity_id', $companyUser->company_id]]);
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
        return $tickets->with('creator.userData', 'assignedPerson.userData', 'contact.userData', 'product', 'team', 'priority', 'status')->orderBy('updated_at', 'desc')->paginate(1000);
    }


    public function find($id)
    {
        return Ticket::where('id', $id)
            ->with('creator', 'assignedPerson.userData', 'contact.userData', 'product', 'team',
                'priority', 'status', 'answers.employee.userData', 'answers.attachments',
                'histories.employee.userData', 'notices.employee.userData', 'attachments')->first()->makeVisible(['to']);
    }

    public function create(Request $request, $employeeId = null)
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
        $ticket->connection_details = $request->connection_details;
        $ticket->access_details = $request->access_details;
        $ticket->save();
        $this->addHistoryItem($ticket->id, $employeeId, 'Ticket created');
        $files = array_key_exists('files', $request->all()) ? $request['files'] : [];
        foreach ($files as $file) {
            $this->fileRepo->store($file, $ticket->id, Ticket::class);
        }
        return $ticket;
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if ($request->status_id !== $ticket->status_id) {
            $this->updateStatus($request, $id);
        } else {
            $ticket->contact_company_user_id = $request->contact_company_user_id;
            $ticket->to_company_user_id = $request->to_company_user_id;
            $ticket->to_team_id = $request->to_team_id;
            $ticket->due_date = $request->due_date;
            $ticket->priority_id = $request->priority_id;
            $ticket->from_entity_id = $request->from_entity_id;
            $ticket->from_entity_type = $request->from_entity_type;
            $ticket->access_details = $request->access_details;
            $ticket->connection_details = $request->connection_details;
            $ticket->save();
            $request->status_id = 2;
            $this->updateStatus($request, $id);
            $this->addHistoryItem($ticket->id, null, 'Ticket updated');
        }
        return $ticket;
    }

    public function updateStatus(Request $request, $id, $employeeId = null): bool
    {
        $ticket = Ticket::find($id);
        $ticket->status_id = $request->status_id;
        $ticket->save();
        $this->addHistoryItem($ticket->id, $employeeId,'Status updated');
        return true;
    }

    public function delete($id)
    {
        $result = false;
        $ticket = Ticket::find($id);
        if ($ticket) {
            $ticket->delete();
            $result = true;
        }
        return $result;
    }

    public function attachTeam(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->team_id = $request->team_id;
        $ticket->save();
        $this->addHistoryItem($ticket->id, null, 'Team attached');
        return true;
    }

    public function attachEmployee(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->to_company_user_id = $request->to_company_user_id;
        $ticket->save();
        $this->addHistoryItem($ticket->id, null, 'Employee attached');
        return true;
    }

    public function attachContact(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->contact_company_user_id = $request->contact_company_user_id;
        $ticket->save();
        $this->addHistoryItem($ticket->id, null,'Contact person updated');
        return true;
    }

    public function addAnswer(Request $request, $id, $employeeId = null)
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
        $ticket = Ticket::find($ticketAnswer->ticket_id);


        $employee = $employeeId !== null ? CompanyUser::find($employeeId) : Auth::user()->employee;

        if ($employee->hasRole(Role::COMPANY_CLIENT)) {
            $request->status_id = 3;
        } else {
            $request->status_id = 4;
        }
        $this->updateStatus($request, $ticketAnswer->ticket_id, $employeeId);
        $this->addHistoryItem($ticketAnswer->ticket_id, $employeeId, 'Answer added');
        return true;
    }

    public function addNotice(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->contact_company_user_id = $request->contact_company_user_id;
        $ticket->save();
        $this->addHistoryItem($ticket->id, null,'Notice added');
        return true;
    }

    private function addHistoryItem($ticketId, $companyUserId = null, $description = null)
    {
        $ticketHistory = new TicketHistory();
        $ticketHistory->company_user_id = $companyUserId ?? Auth::user()->employee->id;
        $ticketHistory->ticket_id = $ticketId;
        $ticketHistory->description = $description;
        $ticketHistory->save();
        return true;
    }

    public function emailEmployees($companyUsers, Ticket $ticket)
    {
        foreach ($companyUsers as $companyUser) {
            $user = $companyUser->userData;
            $user->notify(new NewTicket($user->name, $ticket->name, $ticket->id));
        }
    }

    public function filterEmployeesByRoles($employees, $roles)
    {
        return $employees->filter(function ($item) use ($roles) {
            foreach ($item->roles as $role) {
                if (in_array($role->id, $roles, true)) {
                    return $item;
                }
            }
        });

    }

}
