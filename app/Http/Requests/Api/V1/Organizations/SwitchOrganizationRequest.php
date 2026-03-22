<?php

namespace App\Http\Requests\Api\V1\Organizations;

use Illuminate\Foundation\Http\FormRequest;

class SwitchOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
        ];
    }
}