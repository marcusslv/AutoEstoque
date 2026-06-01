<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class InventoryItemModel extends Model
{
    use HasUuids;

    protected $table = 'inventory_items';

    protected $fillable = [
        'id',
        'tenant_id',
        'product_id',
        'current_stock',
    ];
}
