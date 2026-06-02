<?php

namespace App\Modules\Identity\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AuthenticateUserRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'token_name' => ['nullable', 'string', 'max:80'],
        ];
    }
}
