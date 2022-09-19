<?php


namespace App\Http\Controllers\API;

use App\IncidentReportingAction;
use App\IncidentReportingActionBoard;
use App\IncidentReportingActionBoardAccess;
use App\IncidentReportingActionBoardCategory;
use App\IncidentReportingActionBoardHasAction;
use App\IncidentReportingActionBoardPriority;
use App\IncidentReportingActionBoardStageMonitoring;
use App\IncidentReportingActionBoardState;
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
            ->where('parent_id', '=', null)
            ->with([
                'actions.assignee', 'actions.type', 'categories', 'clients', 'stageMonitoring',
                'priority', 'access', 'state', 'childVersions'
            ])
            ->get();

        return self::showResponse(true, $actionBoards);
    }

    public function actions(): JsonResponse
    {
        $actions = IncidentReportingAction::query()->get();

        return self::showResponse(true, $actions);
    }

    public function options(): JsonResponse
    {
        $options = [
            'categories' => IncidentReportingActionBoardCategory::all(),
            'priorities' => IncidentReportingActionBoardPriority::all(),
            'states' => IncidentReportingActionBoardState::all(),
            'accesses' => IncidentReportingActionBoardAccess::all(),
            'stage_monitorings' => IncidentReportingActionBoardStageMonitoring::all(),
            'actions' => [
                'types' => $this->incidentRepo->getActionTypesInCompanyContext(),
                'deadline_time_parameters' => IncidentReportingAction::DEADLINE_TIME_PARAMETER,
                'deadline_time_indicators' => IncidentReportingAction::DEADLINE_TIME_INDICATOR
            ]
        ];

        return self::showResponse(true, $options);
    }

    public function store(Request $request): JsonResponse
    {
        $request['state_id'] = $this->incidentRepo->getProcessStatesInCompanyContext()[0];
        $board = IncidentReportingActionBoard::create($request->all());
        $this->incidentRepo->syncActionBoardRelations($request, $board);

        return self::showResponse(true, $board);
    }

    public function storeAction(Request $request): JsonResponse
    {
        $action = IncidentReportingAction::query()->create($request->all());

        return self::showResponse(true, $action);
    }

    public function clone(Request $request, $id): JsonResponse
    {
        $request['with_child_organizations'] = false;
        $board = IncidentReportingActionBoard::create($request->all());

        IncidentReportingActionBoard::query()
            ->where('id','=', $id)
            ->orWhere('parent_id', '=', $id)
            ->update(['parent_id' => $board->id]);

        $this->incidentRepo->syncActionBoardRelations($request, $board);

        return self::showResponse(true, $board);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $board = IncidentReportingActionBoard::where('id', '=', $id)->first();
        $board->update($request->all());
        $this->incidentRepo->syncActionBoardRelations($request, $board);

        return self::showResponse(true, $board->with([
            'actions.assignee', 'categories', 'clients',
            'stageMonitoring', 'priority', 'access', 'state'
        ])->first());
    }

    public function updateAction(Request $request, $id): JsonResponse
    {
        $action = IncidentReportingAction::query()->where('id', '=', $id)->first();
        if ($action) {
            $action->update($request->all());
        }

        return self::showResponse(true, $action);
    }

    public function delete(Request $request, $id): JsonResponse
    {
        $board = IncidentReportingActionBoard::where('id', '=', $id)->first();
        $request->action_ids = $request->category_ids = $request->client_ids = [];
        $this->incidentRepo->syncActionBoardRelations($request, $board);
        $board->delete();

        return self::showResponse(true);
    }

    public function deleteAction($id): JsonResponse
    {
        IncidentReportingActionBoardHasAction::query()->where('action_id', '=', $id)->delete();
        IncidentReportingAction::query()->where('id', '=', $id)->delete();

        return self::showResponse(true);
    }
}
