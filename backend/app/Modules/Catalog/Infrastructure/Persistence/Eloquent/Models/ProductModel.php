<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class ProductModel extends Model
{
    use HasUuids;

    protected $table = 'products';

    protected $fillable = [
        'id',
        'tenant_id',
        'name',
        'sku',
        'barcode',
        'category',
        'brand',
        'supplier',
        'minimum_stock',
        'cost_in_cents',
        'currency',
    ];
}
