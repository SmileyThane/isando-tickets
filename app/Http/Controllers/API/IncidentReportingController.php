<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\IncidentReporting\ActionBoardStatus;
use App\IncidentReporting\FocusPriority;
use App\IncidentReporting\ImpactPotential;
use App\IncidentReportingAction;
use App\IncidentReportingActionBoard;
use App\IncidentReportingActionBoardAccess;
use App\IncidentReportingActionBoardHasAction;
use App\IncidentReportingActionBoardStageMonitoring;
use App\Repositories\IncidentReportingRepository;
use App\Repositories\IxarmaRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function addActionType(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createActionType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editActionType(Request $request, $id)
    {
        return self::showResponse(true, $this->incidentRepo->updateActionType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteActionType($id)
    {
        return self::showResponse($this->incidentRepo->deleteActionType($id));
    }

    public function listActionBoardStatuses(Request $request)
    {
        $companyId = Auth::user()->employee->companyData->id;
        $statuses = ActionBoardStatus::query()->where('company_id', '=', $companyId)->get();
        return self::showResponse(true, $statuses);
    }

    public function addActionBoardStatus(Request $request)
    {
        $companyId = Auth::user()->employee->companyData->id;
        return self::showResponse(true, ActionBoardStatus::query()->create([
                'name' => $request->name ?? '',
                'name_de' => $request->name_de,
                'position' => $request->position,
                'company_id' => $companyId
            ]
        ));
    }

    public function editActionBoardStatus(Request $request, $id)
    {
        $companyId = Auth::user()->employee->companyData->id;
        $ABStatus = ActionBoardStatus::query()->where('id', '=', $id);
        if ($ABStatus) {
            $ABStatus->update([
                    'name' => $request->name ?? '',
                    'name_de' => $request->name_de,
                    'position' => $request->position,
                    'company_id' => $companyId
                ]
            );

            return self::showResponse(true, ActionBoardStatus::query()->find($id));
        }

        return self::showResponse(false);
    }

    public function deleteActionBoardStatus($id)
    {
        ActionBoardStatus::query()->where('id', '=', $id)->delete();

        return self::showResponse(true);
    }


    public function listEventTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getEventTypesInCompanyContext());
    }

    public function addEventType(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createEventType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editEventType(Request $request, $id)
    {
        return self::showResponse(true, $this->incidentRepo->updateEventType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteEventType($id)
    {
        return self::showResponse($this->incidentRepo->deleteEventType($id));
    }

    public function listFocusPriorities(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getFocusPrioritiesInCompanyContext());
    }

    public function addFocusPriority(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createFocusPriority(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editFocusPriority(Request $request, $id)
    {
        $result = FocusPriority::query()->where('id', '=', $id)->update(
            [
                'name' => $request->name ?? '',
                'name_de' => $request->name_de,
                'position' => $request->position,
                'color' => $request->color
            ]);
        return self::showResponse(true, $result);
    }

    public function updateActionBoard(Request $request, $typeId, $id): JsonResponse
    {
        $board = IncidentReportingActionBoard::where('id', '=', $id)->where('type_id', '=', $typeId)->first();
        $request['updated_by'] = Auth::id();
        $updatedAttributes = $this->incidentRepo->compareUpdatedAttributes($board, $request->all());
        $board->update($request->all());

        foreach ($updatedAttributes as $updatedAttribute) {
            $this->incidentRepo->logActionBoard($board->id, $updatedAttribute . '_updated');
        }

        $this->incidentRepo->syncActionBoardRelations($request, $board);
        $board = IncidentReportingActionBoard::query()->where('id', '=', $id);

        return self::showResponse(true, $board->with([
            'actions.assignee.userData', 'actions.type', 'categories', 'clients', 'stageMonitoring',
            'priority', 'access', 'state', 'childVersions', 'impactPotentials', 'updatedBy', 'status',
            'actionBoards.impactPotentials', 'actionBoards.actions', 'logs'
        ])->first());
    }


    public function deleteFocusPriority($id)
    {
        return self::showResponse($this->incidentRepo->deleteFocusPriority($id));
    }

    public function listImpactPotentials(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getImpactPotentialsInCompanyContext());
    }

    public function addImpactPotential(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createImpactPotential(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editImpactPotential(Request $request, $id)
    {
        $result = ImpactPotential::query()->where('id', '=', $id)->update(
            [
                'name' => $request->name ?? '',
                'name_de' => $request->name_de,
                'position' => $request->position,
                'color' => $request->color
            ]);

        return self::showResponse(true, $result);
    }

    public function deleteImpactPotential($id)
    {
        return self::showResponse($this->incidentRepo->deleteImpactPotential($id));
    }

    public function listProcessStates(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getProcessStatesInCompanyContext());
    }

    public function addProcessState(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createProcessState(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editProcessState(Request $request, $id)
    {
        return self::showResponse(true, $this->incidentRepo->updateProcessState(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteProcessState($id)
    {
        return self::showResponse($this->incidentRepo->deleteProcessState($id));
    }

    public function listTeamRoles(Request $request): JsonResponse
    {
        return self::showResponse(true, $this->incidentRepo->getTeamRoleInCompanyContext());
    }

    public function addTeamRole(Request $request): JsonResponse
    {
        return self::showResponse(true, $this->incidentRepo->createTeamRole(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editTeamRole(Request $request, $id): JsonResponse
    {
        return self::showResponse(true, $this->incidentRepo->updateTeamRole(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteTeamRole($id): JsonResponse
    {
        return self::showResponse($this->incidentRepo->deleteTeamRole($id));
    }


    public function listResourceTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getResourceTypesInCompanyContext());
    }

    public function addResourceType(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createResourceType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editResourceType(Request $request, $id)
    {
        return self::showResponse(true, $this->incidentRepo->updateResourceType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteResourceType($id)
    {
        return self::showResponse($this->incidentRepo->deleteResourceType($id));
    }

    public function listStakeholderTypes(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->getStakeholderTypesInCompanyContext());
    }

    public function addStakeholderType(Request $request)
    {
        return self::showResponse(true, $this->incidentRepo->createStakeholderType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editStakeholderType(Request $request, $id)
    {
        return self::showResponse(true, $this->incidentRepo->updateStakeholderType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteStakeholderType(Request $request, $id)
    {
        return self::showResponse($this->incidentRepo->deleteStakeholderType($id));
    }

    public function listIxarmaCompanies(Request $request)
    {
        return self::showResponse($this->ixarmaRepo->getOrganizations($request->company_id));
    }

    public function listIxarmaParticipants(Request $request)
    {
        return self::showResponse($this->ixarmaRepo->getParticipants($request->company_id));
    }

    public function listActionBoards($typeId): JsonResponse
    {
        $actionBoardsQuery = IncidentReportingActionBoard::query()
            ->where('parent_id', '=', null);
        if ($typeId > 0) {
            $actionBoardsQuery->where('type_id', '=', $typeId);
        } else {
            $actionBoardsQuery->where('type_id', '!=', abs($typeId));
        }
        $actionBoards = $actionBoardsQuery->with([
            'actions.assignee.userData', 'actions.type', 'categories', 'clients', 'stageMonitoring',
            'priority', 'access', 'state', 'childVersions', 'impactPotentials', 'updatedBy', 'status',
            'actionBoards.impactPotentials', 'actionBoards.actions', 'logs'
        ])
            ->orderBy('name')
            ->get();

        return self::showResponse(true, $actionBoards);
    }

    public function actionsActionBoards($typeId): JsonResponse
    {
        switch ($typeId) {
            case IncidentReportingActionBoard::SCENARIOS:
                $actions = IncidentReportingActionBoard::query()
                    ->where('type_id', '=', IncidentReportingActionBoard::ACTION_BOARDS)
                    ->get();
                break;

            case IncidentReportingActionBoard::IR:
            case IncidentReportingActionBoard::ACTION_BOARDS:
            default:
                $actions = IncidentReportingAction::query()
                    ->where('related_to_ir_ab_id', '=', null)
                    ->get();
                break;
        }

        return self::showResponse(true, $actions);
    }

    public function optionsActionBoards(): JsonResponse
    {
        $options = [
            'categories' => $this->incidentRepo->getEventTypesInCompanyContext(),
            'priorities' => $this->incidentRepo->getFocusPrioritiesInCompanyContext(),
            'states' => $this->incidentRepo->getProcessStatesInCompanyContext(),
            'accesses' => IncidentReportingActionBoardAccess::all(),
            'stage_monitorings' => IncidentReportingActionBoardStageMonitoring::all(),
            'impact_potentials' => $this->incidentRepo->getImpactPotentialsInCompanyContext(),
            'actions' => [
                'types' => $this->incidentRepo->getActionTypesInCompanyContext(),
                'deadline_time_parameters' => IncidentReportingAction::DEADLINE_TIME_PARAMETER,
                'deadline_time_indicators' => IncidentReportingAction::DEADLINE_TIME_INDICATOR
            ]
        ];

        return self::showResponse(true, $options);
    }

    public function storeActionBoard(Request $request, $typeId): JsonResponse
    {
        if (is_null($request['version'])) {

            $request['version'] = '0';
        }
        $request['updated_by'] = Auth::id();
        $request['type_id'] = $typeId;
        $board = IncidentReportingActionBoard::create($request->all());
        $this->incidentRepo->logActionBoard($board->id, 'created');
        $this->incidentRepo->syncActionBoardRelations($request, $board);

        $result = IncidentReportingActionBoard::query()->where('id', '=', $board->id)
            ->with([
                'actions.assignee.userData', 'actions.type', 'categories', 'clients', 'stageMonitoring',
                'priority', 'access', 'state', 'childVersions', 'impactPotentials', 'updatedBy', 'status',
                'actionBoards.impactPotentials', 'actionBoards.actions', 'logs'
            ])->first();

        return self::showResponse(true, $result);
    }

    public function storeAction(Request $request): JsonResponse
    {
        $action = IncidentReportingAction::query()->create($request->all());

        if ($request['action_board_id']) {
            $actionBoard = IncidentReportingActionBoard::where('id', '=', $request['action_board_id'])->first();
            $request['actions'] = $actionBoard->actions;
            $request['action_boards'] = $actionBoard->action_boards;
            $request['actions'][] = $action;
            $request['categories'] = $actionBoard->categories;
            $request['clients'] = $actionBoard->clients;

            $this->incidentRepo->syncActionBoardRelations($request, $actionBoard);
        }

        return self::showResponse(true, $action);
    }

    public function cloneActionBoards(Request $request, $id): JsonResponse
    {
        $request['with_child_organizations'] = false;
        $request['updated_by'] = Auth::id();
        $board = IncidentReportingActionBoard::create($request->all());

        IncidentReportingActionBoard::query()
            ->where('id', '=', $id)
            ->orWhere('parent_id', '=', $id)
            ->update(['parent_id' => $board->id]);

        $this->incidentRepo->syncActionBoardRelations($request, $board);

        return self::showResponse(true, $board);
    }

    public function updateAction(Request $request, $id): JsonResponse
    {
        $action = IncidentReportingAction::query()->where('id', '=', $id)->first();
        if ($action) {
            $action->update($request->all());
        }

        return self::showResponse(true, $action);
    }

    public function deleteActionBoard(Request $request, $id): JsonResponse
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
