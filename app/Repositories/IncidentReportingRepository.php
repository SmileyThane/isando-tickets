<?php


namespace App\Repositories;

use App\IncidentReporting\ActionType;
use App\IncidentReporting\EventType;
use App\IncidentReporting\FocusPriority;
use App\IncidentReporting\ImpactPotential;
use App\IncidentReporting\ProcessState;
use App\IncidentReporting\ReferenceBook;
use App\IncidentReporting\ResourceType;
use App\IncidentReporting\StakeholderType;
use App\IncidentReporting\TeamRole;
use App\IncidentReportingAction;
use App\IncidentReportingActionBoard;
use App\IncidentReportingActionBoardHasAction;
use App\IncidentReportingActionBoardLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;


class IncidentReportingRepository
{
    public function getActionTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return ActionType::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createActionType($name, $name_de = null, $position = null, $company_id = null): ActionType
    {
        return $this->_create(ActionType::class, $name, $name_de, $position, $company_id);
    }

    protected function _create($referenceBook, $name, $name_de = null, $position = null, $company_id = null): ReferenceBook
    {
        $company_id = $company_id ?? Auth::user()->employee->companyData->id;
        return $referenceBook::firstOrCreate([
            'company_id' => $company_id,
            'name' => $name,
            'name_de' => $name_de,
            'position' => $position
        ]);
    }

    // ActionType

    public function updateActionType($id, $name, $name_de = null, $position = null): ?ActionType
    {
        return $this->_update(ActionType::class, $id, $name, $name_de, $position);
    }

    protected function _update($referenceBook, $id, $name, $name_de = null, $position = null): ?ReferenceBook
    {
        $item = $referenceBook::find($id);
        if (!$item) {
            return null;
        }

        $item->update([
            'name' => $name,
            'name_de' => $name_de,
            'position' => $position
        ]);
        return $item;
    }

    public function deleteActionType($id): bool
    {
        return $this->_delete(ActionType::class, $id);
    }

    protected function _delete($referenceBook, $id): bool
    {
        try {
            $referenceBook::where('id', $id)->delete();
            return true;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    // EventType

    public function getEventTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return EventType::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createEventType($name, $name_de = null, $position = null, $company_id = null): EventType
    {
        return $this->_create(EventType::class, $name, $name_de, $position, $company_id);
    }

    public function updateEventType($id, $name, $name_de = null, $position = null): ?EventType
    {
        return $this->_update(EventType::class, $id, $name, $name_de, $position);
    }

    public function deleteEventType($id): bool
    {
        return $this->_delete(EventType::class, $id);
    }

    // FocusPriority
    public function getFocusPrioritiesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return FocusPriority::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createFocusPriority($name, $name_de = null, $position = null, $company_id = null): FocusPriority
    {
        return $this->_create(FocusPriority::class, $name, $name_de, $position, $company_id);
    }

    public function updateFocusPriority($id, $name, $name_de = null, $position = null): ?FocusPriority
    {
        return $this->_update(FocusPriority::class, $id, $name, $name_de, $position);
    }

    public function deleteFocusPriority($id): bool
    {
        return $this->_delete(FocusPriority::class, $id);
    }

    // ImpactPotential
    public function getImpactPotentialsInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return ImpactPotential::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createImpactPotential($name, $name_de = null, $position = null, $company_id = null): ImpactPotential
    {
        return $this->_create(ImpactPotential::class, $name, $name_de, $position, $company_id);
    }

    public function updateImpactPotential($id, $name, $name_de = null, $position = null, $company_id = null): ?ImpactPotential
    {
        return $this->_update(ImpactPotential::class, $id, $name, $name_de, $position);
    }

    public function deleteImpactPotential($id): bool
    {
        return $this->_delete(ImpactPotential::class, $id);
    }

    // ProcessState
    public function getProcessStatesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return ProcessState::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createProcessState($name, $name_de = null, $position = null, $company_id = null): ProcessState
    {
        return $this->_create(ProcessState::class, $name, $name_de, $position, $company_id);
    }

    public function updateProcessState($id, $name, $name_de = null, $position = null): ?ProcessState
    {
        return $this->_update(ProcessState::class, $id, $name, $name_de, $position);
    }

    public function deleteProcessState($id): bool
    {
        return $this->_delete(ProcessState::class, $id);
    }

    // TeamRole
    public function getTeamRoleInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return TeamRole::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createTeamRole($name, $name_de = null, $position = null, $company_id = null): TeamRole
    {
        return $this->_create(TeamRole::class, $name, $name_de, $position, $company_id);
    }

    public function updateTeamRole($id, $name, $name_de = null, $position = null): ?TeamRole
    {
        return $this->_update(TeamRole::class, $id, $name, $name_de, $position);
    }

    public function deleteTeamRole($id): bool
    {
        return $this->_delete(TeamRole::class, $id);
    }


    // ResourceType
    public function getResourceTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return ResourceType::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createResourceType($name, $name_de = null, $position = null, $company_id = null): ResourceType
    {
        return $this->_create(ResourceType::class, $name, $name_de, $position, $company_id);
    }

    public function updateResourceType($id, $name, $name_de = null, $position = null): ?ResourceType
    {
        return $this->_update(ResourceType::class, $id, $name, $name_de, $position);
    }

    public function deleteResourceType($id): bool
    {
        return $this->_delete(ResourceType::class, $id);
    }

    // StakeholderType
    public function getStakeholderTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return StakeholderType::where('company_id', $companyId)->orderBy('position', 'ASC')->get();
    }

    public function createStakeholderType($name, $name_de = null, $position = null, $company_id = null): StakeholderType
    {
        return $this->_create(StakeholderType::class, $name, $name_de, $position, $company_id);
    }

    public function updateStakeholderType($id, $name, $name_de = null, $position = null): ?StakeholderType
    {
        return $this->_update(StakeholderType::class, $id, $name, $name_de, $position);
    }

    public function deleteStakeholderType($id): bool
    {
        return $this->_delete(StakeholderType::class, $id);
    }

    public function syncActionBoardRelations(
        Request                      $request,
        IncidentReportingActionBoard $board,
        bool                         $created = false
    )
    {
        if ((int)$board->type_id === IncidentReportingActionBoard::IR) {
            if (!$created) {
                $this->logActionBoard($board->id, 'incident_actions_updated');
            }
            $board->actions()->sync([]);
            $actionIds = $this->prepareRelationToSync($request->actions);

            if ($actionIds) {
                foreach ($actionIds as $key => $actionId) {
                    $action = IncidentReportingAction::query()->find($actionId);
                    $clonedAction = $action->replicate();
                    $clonedAction->related_to_ir_ab_id = $board->id;
                    $clonedAction->save();
                    if ($action->related_to_ir_ab_id !== null) {
                        $action->delete();
                    }

                    IncidentReportingActionBoardHasAction::query()->create([
                        'action_board_id' => $board->id,
                        'action_id' => $clonedAction->id,
                        'action_type' => IncidentReportingAction::class,
                        'order' => $key
                    ]);
                }
            }
        } else {
            $board->actions()->sync($this->prepareRelationToSync($request->actions));
        }
        $board->actionBoards()->sync($this->prepareRelationToSync($request->action_boards));
        $board->categories()->sync($this->prepareRelationToSync($request->categories));
        $board->clients()->sync($this->prepareRelationToSync($request->clients));
    }

    public function logActionBoard($id, $log)
    {
        IncidentReportingActionBoardLog::query()->create([
            'action_board_id' => $id,
            'log' => $log
        ]);
    }

    private function prepareRelationToSync($relation)
    {
        $temp = [];
        if (is_array($relation) && count($relation) > 0) {
            foreach ($relation as $item) {
                if (!is_integer($item)) {
                    $temp[] = $item['id'];
                }
            }

        }

        return count($temp) > 0 ? $temp : $relation;
    }

    public function compareUpdatedAttributes($board, $request): array
    {
        $result = [];
        $attributes = array_diff(
            array_keys($board->getAttributes()),
            $board->getHidden(),
            ['updated_at']
        );
        foreach ($attributes as $attribute) {
            if (isset($request[$attribute]) && $board[$attribute] !== $request[$attribute]) {
                $result[] = $attribute;
            }
        }

        return $result;
    }
}
