<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\InternalBillingRepository;
use Illuminate\Http\Request;

class InternalBillingController extends Controller
{
    protected $internalBillingRepo;

    public function __construct(InternalBillingRepository $internalBillingRepo)
    {
        $this->internalBillingRepo = $internalBillingRepo;
    }

    public function index(Request $request)
    {
        $additionalUserIds = $request->additional_user_ids;
        $internalBillingId = $request->internal_billing_id;
        return self::showResponse(true, $this->internalBillingRepo->index($additionalUserIds, $internalBillingId));
    }

    public function find(int $id)
    {
        return self::showResponse(true, $this->internalBillingRepo->find($id));
    }

    public function create(Request $request)
    {
        $result = $this->internalBillingRepo->create(
            $request->name,
            $request->cost,
            $request->currency_id,
            $request->entity_id,
            $request->entity_type,

        );

        return self::showResponse(true, $result);
    }

    public function update(Request $request, $id)
    {
        $result = $this->internalBillingRepo->update(
            $id,
            $request->name,
            $request->cost,
            $request->currency_id
        );

        return self::showResponse(true, $result);
    }

    public function delete($id)
    {
        return self::showResponse($this->internalBillingRepo->delete($id));
    }
}
