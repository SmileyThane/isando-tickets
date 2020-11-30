<?php


namespace App\Repository;

use App\Company;
use App\NotificationTemplate;
use App\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function createTemplate($entityId, $entityType, $type, $name, $description, $text, $priority): Notification
    {
        return NotificationTemplate::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'notification_type_id' => $type,
                'name' => $name,
                'description' => $description,
                'text' => $text,
                'priority' => $priority
            ]
        );
    }

    public function updateTemplate($id, $type, $name, $description, $text, $priority): Notification
    {
        $notification = NotificationTemplate::find($id);
        $notification->update(
            [
                'notification_type_id' => $type,
                'name' => $name,
                'description' => $description,
                'text' => $text,
                'priority' => $priority
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
}
