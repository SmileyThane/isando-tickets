<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    protected $notificationRepo;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepo = $notificationRepository;
    }

    public function get(Request $request)
    {
        return self::showResponse(true, $this->notificationRepo->getTemplatesInCompanyContext($request));
    }

    public function add(Request $request)
    {
        $notification = $this->notificationRepo->createTemplate($request['entity_id'], $request['entity_type'], $request['notification_type_id'], $request['name'], $request['description'], $request['text'], $request['priority'], $request['recipients']);
        return self::showResponse(true, $notification);
    }

    public function edit(Request $request, $id)
    {
        $notification = $this->notificationRepo->updateTemplate($id, $request['notification_type'], $request['name'], $request['description'], $request['text'], $request['priority'], $request['recipients']);
        return self::showResponse(true, $notification);
    }

    public function delete($id)
    {
        return self::showResponse($this->notificationRepo->deleteTemplate($id));
    }

    public function find($id)
    {
        return self::showResponse(true, $this->notificationRepo->findTemplate($id));
    }

    public function send(Request $request)
    {
        $attachments = [];
        $attachmentNames = [];
        foreach ($request->files as $file) {
            $attachments[] = [
                'data' => $file->get(),
                'name' => $file->getClientOriginalName()
            ];
            $attachmentNames[] = $file->getClientOriginalName();
        }

        $notification = new Notification($request['recipients'], $request['subject'], $request['body'], $attachments);
        Mail::send($notification);
        $history = $this->notificationRepo->addHistory($request['subject'], $request['body'], $request['notification_type'], $request['priority'], $notification->bcc, $attachmentNames);
        return self::showResponse(true, $history);
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

    public function history(Request $request)
    {
        return self::showResponse(true, $this->notificationRepo->getHistoryInCompanyContext($request));
    }

    public function historyDetails($id)
    {
        return self::showResponse(true, $this->notificationRepo->findHistory($id));
    }
}
