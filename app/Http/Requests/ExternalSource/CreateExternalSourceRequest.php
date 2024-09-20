<?php

declare(strict_types=1);

namespace App\Http\Requests\ExternalSource;

use App\Rules\EntityExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateExternalSourceRequest extends FormRequest
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
            'domain' => 'required|string',
            'domain_prefix' => 'required|string',
            'uri' => 'nullable|string',
            'name' => 'required|string',
            'auth_name' => 'required|string',
            'auth_headers' => 'nullable|json',
            'password' => 'nullable|string',
            'billing_price' => 'required|int',
            'billing_currency' => 'required|int',
            'billing_type' => 'required|int',
            'is_billing_auto_renewal' => 'bool',
            'entity_id' => ['nullable', 'int', new EntityExistsRule($this->input('entityType'))],
            'entity_type' => 'nullable|string',
            'external_source_typeId' => 'integer|exists:external_source_types,id',
        ];
    }
}
