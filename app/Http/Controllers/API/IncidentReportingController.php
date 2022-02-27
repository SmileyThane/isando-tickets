<?php


namespace App\Http\Controllers\API;

use App\IncidentReporting\ActionType;
use App\IncidentReporting\EventType;
use App\IncidentReporting\FocusPriority;
use App\IncidentReporting\ImpactPotential;
use App\IncidentReporting\ResourceType;
use App\IncidentReporting\StakeholderType;
use App\Repositories\IncidentReportingRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncidentReportingController extends Controller
{
    protected $repo;

    public function __construct(IncidentReportingRepository $repository)
    {
        $this->repo = $repository;
    }


    public function listActionTypes(Request $request)
    {
        return self::showResponse(true, $this->repo->getActionTypesInCompanyContext());
    }

    public function addActionType(Request $request) {
        return self::showResponse(true, $this->repo->createActionType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editActionType(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateActionType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteActionType($id) {
        return self::showResponse($this->repo->deleteActionType($id));
    }

    public function listEventTypes(Request $request)
    {
        return self::showResponse(true, $this->repo->getEventTypesInCompanyContext());
    }

    public function addEventType(Request $request) {
        return self::showResponse(true, $this->repo->createEventType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editEventType(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateEventType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteEventType($id) {
        return self::showResponse($this->repo->deleteEventType($id));
    }

    public function listFocusPriorities(Request $request)
    {
        return self::showResponse(true, $this->repo->getFocusPrioritiesInCompanyContext());
    }

    public function addFocusPriority(Request $request) {
        return self::showResponse(true, $this->repo->createFocusPriority(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editFocusPriority(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateFocusPriority(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteFocusPriority($id) {
        return self::showResponse($this->repo->deleteFocusPriority($id));
    }

    public function listImpactPotentials(Request $request)
    {
        return self::showResponse(true, $this->repo->getImpactPotentialsInCompanyContext());
    }

    public function addImpactPotential(Request $request) {
        return self::showResponse(true, $this->repo->createImpactPotential(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editImpactPotential(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateImpactPotential(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteImpactPotential($id) {
        return self::showResponse($this->repo->deleteImpactPotential($id));
    }

    public function listProcessStates(Request $request)
    {
        return self::showResponse(true, $this->repo->getProcessStatesInCompanyContext());
    }

    public function addProcessState(Request $request) {
        return self::showResponse(true, $this->repo->createProcessState(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editProcessState(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateProcessState(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteProcessState($id) {
        return self::showResponse($this->repo->deleteProcessState($id));
    }

    public function listResourceTypes(Request $request)
    {
        return self::showResponse(true, $this->repo->getResourceTypesInCompanyContext());
    }

    public function addResourceType(Request $request) {
        return self::showResponse(true, $this->repo->createResourceType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editResourceType(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateResourceType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteResourceType($id) {
        return self::showResponse($this->repo->deleteResourceType($id));
    }

    public function listStakeholderTypes(Request $request)
    {
        return self::showResponse(true, $this->repo->getStakeholderTypesInCompanyContext());
    }

    public function addStakeholderType(Request $request) {
        return self::showResponse(true, $this->repo->createStakeholderType(
            $request->name ?? '',
            $request->name_de,
            $request->position,
            $request->company_id
        ));
    }

    public function editStakeholderType(Request $request, $id) {
        return self::showResponse(true, $this->repo->updateStakeholderType(
            $id,
            $request->name ?? '',
            $request->name_de,
            $request->position
        ));
    }

    public function deleteStakeholderType($id) {
        return self::showResponse($this->repo->deleteStakeholderType($id));
    }
}
