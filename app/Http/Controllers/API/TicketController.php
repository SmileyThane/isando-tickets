<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\NewTicket;
use App\Repository\TicketRepository;
use App\Role;
use App\Team;
use App\Ticket;
use App\TicketCategory;
use App\TicketPriority;
use App\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $ticketRepo;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepo = $ticketRepository;
    }

    public function get(Request $request)
    {
        $tickets = $this->ticketRepo->all($request);
        return self::showResponse(true, $tickets);
    }

    public function priorities()
    {
        return self::showResponse(true, TicketPriority::all());
    }

    public function categories()
    {
        return self::showResponse(true, TicketCategory::all());

    }

    public function types()
    {
        $types = TicketType::where('name', '!=', null);
        if ($companyUser = Auth::user()->employee->hasRole(Role::COMPANY_CLIENT)) {
            $types->where('id', '!=', TicketType::INTERNAL);
        }
        return self::showResponse(true, $types->get());
    }

    public function find($id)
    {
        $ticket = $this->ticketRepo->find($id);
        return self::showResponse(true, $ticket);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->ticketRepo->validate($request);
        if ($result === true) {
            $result = $this->ticketRepo->create($request);
            $employees = $this->ticketRepo->filterEmployeesByRoles($result->to->employees, [Role::LICENSE_OWNER, Role::ADMIN, Role::MANAGER, Role::USER, Role::COMPANY_CLIENT]);
            $this->ticketRepo->emailEmployees($employees, $result, NewTicket::class);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->ticketRepo->validate($request);
        if ($result === true) {
            $result = $this->ticketRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete($id)
    {
        $result = $this->ticketRepo->delete($id);
        return self::showResponse($result);
    }

    public function markAsSpam(Request $request)
    {
        $result = $this->ticketRepo->markAsSpam($request->id);
        return self::showResponse(true, $result);
    }

    public function attachTeam(Request $request, $id)
    {
        $result = $this->ticketRepo->attachTeam($request, $id);
        $employees = Team::find($request->team_id)->employees;
        $ticket = Ticket::find($id);
        $this->ticketRepo->emailEmployees($employees, $ticket, NewTicket::class);
        return self::showResponse($result);
    }

    public function attachEmployee(Request $request, $id)
    {
        $result = $this->ticketRepo->attachEmployee($request, $id);
        return self::showResponse($result);
    }

    public function attachContact(Request $request, $id)
    {
        $result = $this->ticketRepo->attachContact($request, $id);
        return self::showResponse($result);
    }

    public function addAnswer(Request $request, $id)
    {
        $result = $this->ticketRepo->addAnswer($request, $id);
        return self::showResponse($result);
    }

    public function addNotice(Request $request, $id)
    {
        $result = $this->ticketRepo->addNotice($request, $id);
        return self::showResponse($result);
    }

    public function addMerge(Request $request)
    {
        $result = $this->ticketRepo->addMerge($request);
        return self::showResponse($result);
    }

    public function removeMerge($id)
    {
        $result = $this->ticketRepo->removeMerge($id);
        return self::showResponse($result);
    }

    public function addLink(Request $request)
    {
        $result = $this->ticketRepo->addLink($request);
        return self::showResponse($result);
    }

}
