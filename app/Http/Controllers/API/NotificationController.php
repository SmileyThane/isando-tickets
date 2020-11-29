<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\NotificationRepository;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationRepo;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepo = $notificationRepository;
    }

    public function add(Request $request)
    {
        $notification = $this->notificationRepo->create($request['entity_id'], $request['entity_type'], $request['notification_type'], $request['name'], $request['description'], $request['text'], $request['priority']);
        return self::showResponse(true, $notification);
    }

    public function edit(Request $request, $id)
    {
        $notification = $this->notificationRepo->update($id,$request['notification_type'], $request['name'], $request['description'], $request['text'], $request['priority']); 
        return self::showResponse(true, $notification);
    }

    public function delete($id)
    {
        return self::showResponse($this->notificationRepo->delete($id));
    }

    public function getTypes()
    {
        return self::showResponse(true, $this->notificationRepo->getTypesInCompanyContext());
    }

    public function addType(Request $request)
    {
        $type = $this->notificationRepo->createType($request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function updateType(Request $request, $id)
    {
        $type = $this->notificationRepo->updateType($id, $request->name, $request->name_de, $request->icon);
        return self::showResponse(true, $type);
    }

    public function deleteType($id)
    {
        return self::showResponse($this->notificationRepo->deleteType($id));
    }
}
