<?php

namespace App\Modules\Workshop\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AddPartToServiceOrderRequest extends FormRequest
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
            'product_id' => ['required', 'uuid'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return array_merge($this->all(), [
            'X-User-Id' => $this->header('X-User-Id'),
        ]);
    }
}
