<?php


namespace App\Repository;

use App\Company;
use App\Notification;
use App\NotificationType;
use Illuminate\Support\Facades\Auth;

class NotificationRepository
{

    public function create($entityId, $entityType, $type, $name, $description, $text, $priority): Notification
    {
        return Notification::firstOrCreate(
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

    public function update($id, $type, $name, $description, $text, $priority): Notification
    {
        $notification = Notification::find($id);
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

    public function delete($id): ?bool
    {
        try {
            Notification::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function createType($name, $name_de, $icon, $entityId = null $entityType = null): NotificationType
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
