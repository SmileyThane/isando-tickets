<?php

declare(strict_types=1);

namespace App\Http\Requests\ExternalSource;

use App\Rules\EntityExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExternalSourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'domain' => 'sometimes|string',
            'domain_prefix' => 'sometimes|string',
            'uri' => 'nullable|string',
            'name' => 'sometimes|string',
            'auth_name' => 'sometimes|string',
            'auth_headers' => 'nullable|json',
            'password' => 'nullable|string',
            'billing_price' => 'sometimes|int',
            'billing_currency' => 'sometimes|int',
            'billing_type' => 'sometimes|int',
            'is_billing_auto_renewal' => 'bool',
            'entity_id' => ['nullable', 'int', new EntityExistsRule($this->input('entityType'))],
            'entity_type' => 'nullable|string',
            'external_source_typeId' => 'integer|exists:external_source_types,id',
        ];
    }
}
