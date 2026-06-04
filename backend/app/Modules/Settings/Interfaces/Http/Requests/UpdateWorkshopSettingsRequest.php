<?php

namespace App\Modules\Settings\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateWorkshopSettingsRequest extends FormRequest
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
            'display_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'timezone'],
            'currency' => ['required', 'string', 'in:BRL'],
            'allow_negative_stock' => ['required', 'boolean'],
            'auto_deduct_stock_on_service_order_finish' => ['required', 'boolean'],
            'minimum_stock_default' => ['required', 'integer', 'min:0'],
            'notify_minimum_stock' => ['required', 'boolean'],
            'notify_zero_stock' => ['required', 'boolean'],
            'notification_email' => ['nullable', 'email', 'max:255'],
            'notification_phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
