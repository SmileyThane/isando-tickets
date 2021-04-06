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

    public function find(int $id)
    {
        return self::showResponse(true, $this->internalBillingRepo->find($id));
    }

    public function create(Request $request)
    {
        $result = $this->internalBillingRepo->create(
            $request->entity_id,
            $request->entity_type,
            $request->cost,
            $request->currency_id
        );

        return self::showResponse(true, $result);
    }

    public function update(Request $request, $id)
    {
        $result = $this->internalBillingRepo->update(
            $id,
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
