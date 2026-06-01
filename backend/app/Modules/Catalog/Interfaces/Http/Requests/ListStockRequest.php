<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListStockRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
