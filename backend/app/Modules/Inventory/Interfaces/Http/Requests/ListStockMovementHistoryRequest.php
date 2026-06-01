<?php

namespace App\Modules\Inventory\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListStockMovementHistoryRequest extends FormRequest
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
            'product_id' => ['nullable', 'uuid'],
            'direction' => ['nullable', 'string', 'in:entry,output'],
            'type' => ['nullable', 'string', 'max:40'],
            'user_id' => ['nullable', 'uuid'],
            'occurred_from' => ['nullable', 'date'],
            'occurred_to' => ['nullable', 'date', 'after_or_equal:occurred_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
