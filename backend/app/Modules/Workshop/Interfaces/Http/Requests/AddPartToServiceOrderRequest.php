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
            'product_id' => ['required', 'uuid'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
