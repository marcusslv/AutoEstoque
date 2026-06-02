<?php

namespace App\Modules\Identity\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateWorkshopUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:owner,manager,admin,mechanic'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }
}
