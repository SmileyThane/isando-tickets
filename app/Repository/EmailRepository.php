<?php


namespace App\Repository;

use App\Company;
use App\Email;
use App\EmailType;
use Illuminate\Support\Facades\Auth;

class EmailRepository
{

    public function create($entityId, $entityType, $emailValue, $emailType): Email
    {
        return Email::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'email' => $emailValue,
                'email_type' => $emailType
            ]
        );
    }

    public function update($id, $type, $value): Email
    {
        $email = Email::find($id);
        $email->email = $value;
        $email->email_type = $type;
        $email->save();
        return $email;
    }

    public function delete($id): ?bool
    {
        try {
            Email::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function createType($name, $name_de, $icon, $companyId = null): EmailType
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        return EmailType::firstOrCreate([
            'entity_type' => Company::class,
            'entity_id' => $companyId,
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $name_de, $icon): EmailType
    {
        $type = EmailType::findOrFail($id);
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
            EmailType::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return EmailType::where('entity_type', Company::class)->where('entity_id', $companyId)->get();
    }
}
