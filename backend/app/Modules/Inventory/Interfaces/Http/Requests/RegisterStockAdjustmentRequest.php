<?php

namespace App\Modules\Inventory\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterStockAdjustmentRequest extends FormRequest
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
            'direction' => ['required', 'string', 'in:entry,output'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
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
