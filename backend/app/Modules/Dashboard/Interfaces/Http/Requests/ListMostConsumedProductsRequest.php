<?php

namespace App\Modules\Dashboard\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListMostConsumedProductsRequest extends FormRequest
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
            'period_from' => ['nullable', 'date'],
            'period_to' => ['nullable', 'date', 'after_or_equal:period_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
