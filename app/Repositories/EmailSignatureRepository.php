<?php


namespace App\Repositories;

use App\User;
use App\EmailSignature;
use Illuminate\Support\Facades\Auth;
use Throwable;

class EmailSignatureRepository
{

    public function create($entityId, $entityType, $name, $signature): EmailSignature
    {
        return EmailSignature::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'name' => $name,
                'signature' => $signature
            ]
        );
    }

    public function update($id, $name, $value): EmailSignature
    {
        $signature = EmailSignature::find($id);
        $signature->update(
            [
                'name' => $name,
                'signature' => $value
            ]
        );
        return $signature;
    }

    public function delete($id): ?bool
    {
        try {
            EmailSignature::where('id', $id)->delete();
            return true;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    public function get($entityId = null, $entityType = null)
    {
        $entityId = $entityId ?? Auth::user()->id;
        $entityType = $entityType ?? User::class;
        return EmailSignature::where('entity_type', $entityType)->where('entity_id', $entityId)->get();
    }
}
