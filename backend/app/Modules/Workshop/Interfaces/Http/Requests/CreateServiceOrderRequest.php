<?php

namespace App\Modules\Workshop\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateServiceOrderRequest extends FormRequest
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
            'vehicle_id' => ['required', 'uuid'],
            'customer_name' => ['required', 'string', 'max:160'],
            'services_description' => ['required', 'string', 'max:5000'],
            'observations' => ['nullable', 'string', 'max:5000'],
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
