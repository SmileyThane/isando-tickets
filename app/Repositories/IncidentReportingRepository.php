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
use App\IncidentReportingActionBoard;
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

    public function syncActionBoardRelations(Request $request, IncidentReportingActionBoard $board)
    {
        $board->actions()->sync($request->action_ids);
        $board->categories()->sync($request->category_ids);
        $board->clients()->sync($request->client_ids);
    }
}
