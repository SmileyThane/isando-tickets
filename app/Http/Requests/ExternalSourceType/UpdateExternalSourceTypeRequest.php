<?php

declare(strict_types=1);

namespace App\Http\Requests\ExternalSourceType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExternalSourceTypeRequest extends FormRequest
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
            'name' => 'nullable|string|max:255|unique:external_source_types,name',
            'description' => 'nullable|string',
        ];
    }
}
