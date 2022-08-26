<?php


namespace App\Http\Controllers\API;

use App\IncidentReportingActionBoard;
use App\Providers\IxarmaServiceProvider;
use App\Repositories\IncidentReportingRepository;
use App\Repositories\IxarmaRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentReportingController extends Controller
{
    protected $incidentRepo;
    protected $ixarmaRepo;

//    public function __construct(IxarmaServiceProvider $service, IncidentReportingRepository $incidentRepo, IxarmaRepository $ixarmaRepo)
//    {
//        $this->incidentRepo = $incidentRepo;
//        $this->ixarmaRepo = $ixarmaRepo;
//        $this->ixarmaRepo->initRepo($service);
//    }

    public function __construct(IncidentReportingRepository $incidentRepo, IxarmaRepository $ixarmaRepo)
    {
        $this->incidentRepo = $incidentRepo;
        $this->ixarmaRepo = $ixarmaRepo;
//        $this->ixarmaRepo->initRepo($service);
    }


    public function listActionTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getActionTypesInCompanyContext());
    }

    public function addActionType(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createActionType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editActionType(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateActionType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteActionType($id) {
        return self::showResponse($this->incidentRepo->deleteActionType($id));
    }

    public function listEventTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getEventTypesInCompanyContext());
    }

    public function addEventType(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createEventType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editEventType(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateEventType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteEventType($id) {
        return self::showResponse($this->incidentRepo->deleteEventType($id));
    }

    public function listFocusPriorities(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getFocusPrioritiesInCompanyContext());
    }

    public function addFocusPriority(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createFocusPriority(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editFocusPriority(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateFocusPriority(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteFocusPriority($id) {
        return self::showResponse($this->incidentRepo->deleteFocusPriority($id));
    }

    public function listImpactPotentials(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getImpactPotentialsInCompanyContext());
    }

    public function addImpactPotential(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createImpactPotential(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editImpactPotential(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateImpactPotential(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteImpactPotential($id) {
        return self::showResponse($this->incidentRepo->deleteImpactPotential($id));
    }

    public function listProcessStates(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getProcessStatesInCompanyContext());
    }

    public function addProcessState(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createProcessState(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editProcessState(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateProcessState(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteProcessState($id) {
        return self::showResponse($this->incidentRepo->deleteProcessState($id));
    }

    public function listResourceTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getResourceTypesInCompanyContext());
    }

    public function addResourceType(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createResourceType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editResourceType(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateResourceType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteResourceType($id) {
        return self::showResponse($this->incidentRepo->deleteResourceType($id));
    }

    public function listStakeholderTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getStakeholderTypesInCompanyContext());
    }

    public function addStakeholderType(Request $request) {
        return self::showResponse(true, $this->incidentRepo->createStakeholderType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editStakeholderType(Request $request, $id) {
        return self::showResponse(true, $this->incidentRepo->updateStakeholderType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteStakeholderType(Request $request, $id) {
        return self::showResponse($this->incidentRepo->deleteStakeholderType($id));
    }

    public function listIxarmaCompanies(Request $request) {
        return self::showResponse($this->ixarmaRepo->getOrganizations($request->company_id));
    }

    public function listIxarmaParticipants(Request $request) {
        return self::showResponse($this->ixarmaRepo->getParticipants($request->company_id));
    }

    public function index(): JsonResponse
    {
        $actionBoards = IncidentReportingActionBoard::query()
            ->with([
                'actions.assignee', 'categories', 'clients',
                'stageMonitoring', 'priority', 'access', 'state'
            ])
            ->get();
        return self::showResponse(true, $actionBoards);
    }

    public function store(Request $request): JsonResponse
    {
        $board = IncidentReportingActionBoard::create($request->all());
        $this->syncActionBoardRelations($request, $board);

        return self::showResponse(true, $board);
    }

    public function index(): JsonResponse
    {
        $board->actions()->sync($request->action_ids);
        $board->categories()->sync($request->category_ids);
        $board->clients()->sync($request->client_ids);
    }
}
