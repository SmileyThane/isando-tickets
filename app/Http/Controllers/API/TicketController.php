<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\NewTicket;
use App\Permission;
use App\Repositories\TicketRepository;
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
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->all($request));
        }

        return self::showResponse(false);
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

        if ($companyUser = Auth::user()->employee->hasRoleId(Role::COMPANY_CLIENT)) {
            $types->where('id', '!=', TicketType::INTERNAL);
        }

        return self::showResponse(true, $types->get());
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->find($id));
        }

        return self::showResponse(false);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->ticketRepo->validate($request);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS);

        if ($result && $hasAccess) {
            $result = $this->ticketRepo->create($request);
            $employees = $this->ticketRepo->filterEmployeesByRoles($result->to->employees, [Role::LICENSE_OWNER, Role::ADMIN, Role::MANAGER, Role::USER, Role::COMPANY_CLIENT]);
            $this->ticketRepo->emailEmployees($employees, $result, NewTicket::class);
            $success = true;
        }

        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $result = $this->ticketRepo->validate($request);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS);

        if ($result && $hasAccess) {
            return self::showResponse($this->ticketRepo->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function delete($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_DELETE_ACCESS)) {
            return self::showResponse($this->ticketRepo->delete($id));
        }

        return self::showResponse(false);
    }

    public function markAsSpam(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->markAsSpam($request->id));
        }

        return self::showResponse(false);
    }

    public function attachTeam(Request $request, $id)
    {
        $result = false;
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            $result = $this->ticketRepo->attachTeam($request, $id);
            $employees = Team::find($request->team_id)->employees;
            $ticket = Ticket::find($id);
            $this->ticketRepo->emailEmployees($employees, $ticket, NewTicket::class);
        }

        return self::showResponse($result);
    }

    public function attachEmployee(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->attachEmployee($request, $id));
        }

        return self::showResponse(false);
    }

    public function attachContact(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->attachContact($request, $id));
        }

        return self::showResponse(false);
    }

    public function addAnswer(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->addAnswer($request, $id));
        }

        return self::showResponse(false);
    }

    public function addNotice(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->addNotice($request, $id));
        }

        return self::showResponse(false);
    }

    public function addMerge(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->addMerge($request));
        }

        return self::showResponse(false);
    }

    public function removeMerge($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->removeMerge($id));
        }

        return self::showResponse(false);
    }

    public function addLink(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->addLink($request));
        }

        return self::showResponse(false);
    }

    public function addFilter(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->addFilter($request));
        }

        return self::showResponse(false);
    }

    public function getFilters()
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->getFilters());
        }

        return self::showResponse(false);
    }

    public function getFilterParameters(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->getFilterParameters($request));
        }

        return self::showResponse(false);
    }

}
