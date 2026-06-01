<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:80'],
            'barcode' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:120'],
            'brand' => ['nullable', 'string', 'max:120'],
            'supplier' => ['nullable', 'string', 'max:160'],
            'minimum_stock' => ['sometimes', 'integer', 'min:0'],
            'cost_in_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
        ];
    }
}
