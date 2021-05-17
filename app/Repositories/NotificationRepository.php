<?php


namespace App\Repositories;

use App\Company;
use App\NotificationTemplate;
use App\NotificationType;
use App\Permission;
use App\SentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationRepository
{

    public function getTemplatesInCompanyContext(Request $request)
    {
        $companyId = $request['company_id'] ?? Auth::user()->employee->companyData->id;
        $notifications = NotificationTemplate::where('entity_type', Company::class)->where('entity_id', $companyId)->with('type')->get();

        $orderBy = $request->sort_by ?? 'name';
        $request->sort_by = null;

        $orderFunc = function ($item, $key) use ($orderBy) {
            switch ($orderBy) {
                case 'id':
                    return $item->id;
                case 'type':
                    return $item->type ? $item->type->name : '';
                case 'priority':
                    return $item->priority;
                case 'name':
                    return $item->name;
                case 'description':
                    return $item->description;
            }
        };

        if ($request->sort_val === 'false') {
            $notifications = $notifications->sortBy($orderFunc);
        } else {
            $notifications = $notifications->sortByDesc($orderFunc);
        }

        return $notifications->paginate($request->per_page ?? $notifications->count());
    }

    public function createTemplate($entityId, $entityType, $type, $name, $description, $text, $priority, $recipients): NotificationTemplate
    {
        $entityId = $entityId ?? Auth::user()->employee->companyData->id;
        $entityType = $entityType ?? Company::class;

        return NotificationTemplate::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'notification_type_id' => $type,
                'name' => $name,
                'description' => $description,
                'text' => $text,
                'priority' => $priority,
                'recipients' => $recipients
            ]
        );
    }

    public function updateTemplate($id, $type, $name, $description, $text, $priority, $recipients): NotificationTemplate
    {
        $notification = NotificationTemplate::find($id);
        $notification->update(
            [
                'notification_type_id' => $type,
                'name' => $name,
                'description' => $description,
                'text' => $text,
                'priority' => $priority,
                'recipients' => $recipients
            ]
        );
        return $notification;
    }

    public function deleteTemplate($id): ?bool
    {
        try {
            NotificationTemplate::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function findTemplate($id): ?NotificationTemplate
    {
        return NotificationTemplate::find($id);
    }

    public function createType($name, $name_de, $icon, $entityId = null, $entityType = null): NotificationType
    {
        $entityId = $entityId ?? Auth::user()->employee->companyData->id;
        $entityType = $entityType ?? Company::class;

        return NotificationType::firstOrCreate([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $name_de, $icon): NotificationType
    {
        $type = NotificationType::findOrFail($id);
        $type->update([
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
        $type->save();
        return $type;
    }

    public function deleteType($id): ?bool
    {
        try {
            NotificationType::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return NotificationType::where('entity_type', Company::class)->where('entity_id', $companyId)->get();
    }

    public function getHistoryInCompanyContext(Request $request)
    {
        $companyUser = Auth::user()->employee;
        $companyId = $request['company_id'] ?? $companyUser->companyData->id;

        $notifications = new Collection([]);
        if ($companyUser->is_clientable || $companyUser->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)) {
//           $notifications = SentNotification::where('entity_type', Company::class)->where('entity_id', $companyId)->with(['type', 'sender'])->get();
            foreach (Auth::user()->emails as $email) {
                $notifications = $notifications->merge(SentNotification::whereNotNull(DB::raw("JSON_SEARCH(recipients, 'one', '" . $email->email . "')"))->with(['type', 'sender'])->get());
            }

            $notifications = $notifications->unique();
        } else {
            $notifications = SentNotification::where('entity_type', Company::class)->where('entity_id', $companyId)->with(['type', 'sender'])->get();
        }

        $orderBy = $request->sort_by ?? 'created_at';
        $request->sort_by = null;

        $orderFunc = function ($item, $key) use ($orderBy) {
            switch ($orderBy) {
                case 'id':
                    return $item->id;
                case 'type':
                    return $item->type ? $item->type->name : '';
                case 'priority':
                    return $item->priority;
                case 'subject':
                    return $item->subject;
                case 'sender':
                    return $item->sender->full_name;
                case 'created_at':
                    return $item->created_at;
            }
        };

        if ($request->sort_val === 'false') {
            $notifications = $notifications->sortBy($orderFunc);
        } else {
            $notifications = $notifications->sortByDesc($orderFunc);
        }

        return $notifications->paginate($request->per_page ?? $notifications->count());
    }

    public function addHistory($subject, $text, $typeId, $priority, $recipients, $attachmnentNames, $userId = null, $entityId = null, $entityType = null)
    {
        $entityId = $entityId ?? Auth::user()->employee->companyData->id;
        $entityType = $entityType ?? Company::class;
        $userId = $userId ?? Auth::user()->id;
        return SentNotification::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,

            'subject' => $subject,
            'text' => $text,
            'notification_type_id' => $typeId,
            'priority' => $priority,
            'recipients' => $recipients,
            'attachments' => $attachmnentNames,
            'user_id' => $userId
        ]);
    }

    public function findHistory($id)
    {
        return SentNotification::with(['type', 'sender'])->find($id);
    }

}
