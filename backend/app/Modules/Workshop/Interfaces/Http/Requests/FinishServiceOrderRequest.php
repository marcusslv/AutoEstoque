<?php

namespace App\Modules\Workshop\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class FinishServiceOrderRequest extends FormRequest
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
            'X-User-Id' => ['required', 'uuid'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return [
            'X-User-Id' => $this->header('X-User-Id'),
        ];
    }
}
