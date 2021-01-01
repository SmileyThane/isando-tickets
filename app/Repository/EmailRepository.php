<?php


namespace App\Repository;

use App\Company;
use App\Email;
use App\EmailType;
use Illuminate\Support\Facades\Auth;
use Throwable;

class EmailRepository
{

    public function create($entityId, $entityType, $emailValue, $emailType): Email
    {
        $emails = Email::where('entity_type', $entityType)->where('entity_id', $entityId)->get()->count();
        if (!$emails) {
            $emailType = 1;
        }

        $email = Email::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'email' => $emailValue,
                'email_type' => $emailType
            ]
        );

        if ($emailType === 1) {
            $companyId = $companyId ?? Auth::user()->employee->companyData->id;
            $secondaryType = EmailType::where('entity_type', Company::class)->where('entity_id', $companyId)->first();
            Email::where('id', '<>', $email->id)->where('email_type', 1)->update(['email_type' => $secondaryType ? $secondaryType->id : null]);
        }
        return $email;
    }

    public function update($id, $type, $value): Email
    {
        $email = Email::find($id);
        $email->update([
            'email' => $value,
            'email_type' => $type
        ]);

        if ($type === 1) {
            $companyId = $companyId ?? Auth::user()->employee->companyData->id;
            $secondaryType = EmailType::where('entity_type', Company::class)->where('entity_id', $companyId)->first();
            Email::where('id', '<>', $id)->where('email_type', 1)->update(['email_type' => $secondaryType ? $secondaryType->id : null]);
        }
        return $email;
    }

    public function delete($id): ?bool
    {
        try {
            Email::where('id', $id)->delete();
            return true;
        } catch (Throwable $throwable) {
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
        if ($id !== 1) {
            $type->update([
                'name' => $name,
                'name_de' => $name_de,
                'icon' => $icon
            ]);
            $type->save();
        }
        return $type;
    }

    public function deleteType($id): ?bool
    {
        try {
            if ($id !== 1) {
                EmailType::where('id', $id)->delete();
            }
            return true;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;


        $types = EmailType::where('entity_type', Company::class)->where('entity_id', $companyId)->get();
        return $types->prepend(EmailType::find(1));
    }
}
