<?php

namespace App\Modules\Identity\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class CreateWorkshopUserRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', 'string', 'in:owner,manager,admin,mechanic'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
