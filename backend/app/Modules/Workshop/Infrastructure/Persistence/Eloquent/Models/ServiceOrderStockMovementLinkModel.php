<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class ServiceOrderStockMovementLinkModel extends Model
{
    use HasUuids;

    protected $table = 'service_order_stock_movements';

    protected $fillable = [
        'id',
        'tenant_id',
        'service_order_id',
        'service_order_item_id',
        'stock_movement_id',
    ];
}
