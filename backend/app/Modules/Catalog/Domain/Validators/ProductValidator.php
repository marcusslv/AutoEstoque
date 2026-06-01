<?php

namespace App\Modules\Catalog\Domain\Validators;

use App\Modules\Catalog\Domain\Entities\Product;

final class ProductValidator
{
    public static function validate(Product $product): void
    {
        if (trim($product->name()) === '') {
            $product->notification()->add(
                field: 'name',
                message: 'Product name is required.',
                code: 'product.name_required',
            );
        }

        if ($product->minimumStock() < 0) {
            $product->notification()->add(
                field: 'minimum_stock',
                message: 'Minimum stock cannot be negative.',
                code: 'product.minimum_stock_negative',
            );
        }
    }
}
