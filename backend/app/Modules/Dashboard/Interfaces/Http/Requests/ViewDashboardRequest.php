<?php

namespace App\Modules\Dashboard\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ViewDashboardRequest extends FormRequest
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
            'date' => ['nullable', 'date'],
            'recent_movements_limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ];
    }
}
