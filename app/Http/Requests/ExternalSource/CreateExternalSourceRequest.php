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
            'domainPrefix' => 'required|string',
            'uri' => 'nullable|string',
            'name' => 'required|string',
            'authName' => 'required|string',
            'authHeaders' => 'nullable|string',
            'password' => 'nullable|string',
            'billingPrice' => 'required|int',
            'billingCurrency' => 'required|int',
            'billingType' => 'required|int',
            'isBillingAutoRenewal' => 'bool|string',
            'entityId' => ['nullable', 'int', new EntityExistsRule($this->input('entityType'))],
            'entityType' => 'nullable|string',
            'externalSourceTypeId' => 'id|exists:external_source_types,id',
        ];
    }
}
