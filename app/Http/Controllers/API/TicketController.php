<?php


namespace App\Http\Controllers\API;

use App\Company;
use App\Http\Controllers\Controller;
use App\Notifications\NewTicket;
use App\Permission;
use App\Repositories\TicketRepository;
use App\Team;
use App\Ticket;
use App\TicketCategory;
use App\TicketPriority;
use App\TicketStatus;
use App\TicketType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $ticketRepo;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepo = $ticketRepository;
    }

    public function priorities()
    {
        return self::showResponse(true, TicketPriority::all());
    }

    public function categories()
    {
        return self::showResponse(true, TicketCategory::all());

    }

    public function getStatuses()
    {
        return self::showResponse(true, TicketStatus::all());
    }

    public function getTypes()
    {
        $types = TicketType::where('name', '!=', null)
            ->where('entity_type', Company::class)
            ->where('entity_id', Auth::user()->employee->companyData->id);

        if (Auth::user()->employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
            $types->where('id', '!=', TicketType::INTERNAL);
        }

        return self::showResponse(true, $types->get());
    }

    public function get(Request $request)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->all($request));
        }

        return self::showResponse(false);
    }

    public function getAllTypesInCompanyContext()
    {
        $types = TicketType::where('entity_type', Company::class)->where('entity_id', Auth::user()->employee->companyData->id);
        return self::showResponse(true, $types->get());
    }

    public function addType(Request $request): JsonResponse
    {
        $companyId = $request->company_id ?? Auth::user()->employee->companyData->id;

        return self::showResponse(true, TicketType::firstOrCreate([
            'entity_type' => Company::class,
            'entity_id' => $companyId,
            'name' => $request->name,
            'name_de' => $request->name_de,
            'icon' => $request->icon
        ]));
    }

    public function updateType(Request $request, $id): JsonResponse
    {
        $type = TicketType::findOrFail($id);
        if ($id !== 1) {
            $type->update([
                'name' => $request->name,
                'name_de' => $request->name_de,
                'icon' => $request->icon
            ]);
            $type->save();
        }
        return self::showResponse(true, $type);
    }

    public function update(Request $request, $id)
    {
        $result = $this->ticketRepo->validate($request);
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS);

        if ($result && $hasAccess) {
            return self::showResponse(true, $this->ticketRepo->update($request, $id));
        }

        return self::showResponse(false);
    }

    public function deleteType($id): JsonResponse
    {
        try {
            if ($id !== 1) {
                TicketType::where('id', $id)->delete();
            }
            return self::showResponse(true);
        } catch (Throwable $throwable) {
            return self::showResponse(false);
        }
    }

    public function delete($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_DELETE_ACCESS)) {
            return self::showResponse($this->ticketRepo->delete($id));
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
            $clientEmployees = $result->toClient->employees;
            $employees = [];
            foreach ($clientEmployees as $clientEmployee) {
                if ($clientEmployee->employee && $clientEmployee->employee->userData)
                {
                    $employees[] = $clientEmployee->employee;
                }
            }

            $this->ticketRepo->emailEmployees(
                $this->ticketRepo->filterEmailRecipients($employees, $result, Ticket::ACTION_NEW_TICKET),
                $result,
                NewTicket::class,
            );
            $success = true;
        }

        return self::showResponse($success, $result);
    }

    public function updateDescription(Request $request, $id)
    {
        $hasAccess = Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS);
        if ($hasAccess) {
            return self::showResponse(true, $this->ticketRepo->updateDescription($request, $id));
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
            $this->ticketRepo->emailEmployees(
                $this->ticketRepo->filterEmailRecipients($employees, $ticket, Ticket::ACTION_ATTACH_TEAM_TO_TICKET),
                $ticket,
                NewTicket::class
            );
        }

        return self::showResponse($result);
    }

    public function find($id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_READ_ACCESS)) {
            return self::showResponse(true, $this->ticketRepo->find($id));
        }

        return self::showResponse(false);
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

    public function editAnswer(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->editAnswer($request, $id));
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


    public function editNotice(Request $request, $id)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_WRITE_ACCESS)) {
            return self::showResponse($this->ticketRepo->editNotice($request, $id));
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
            $result = $this->ticketRepo->addFilter($request);
            return self::showResponse($result ? true : false, $result);
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

    public function removeFilter(Request $request, $filterId)
    {
        if (Auth::user()->employee->hasPermissionId(Permission::TICKET_DELETE_ACCESS)) {
            $result = $this->ticketRepo->removeFilter($filterId);
            return self::showResponse($result ? true : false, $result);
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
