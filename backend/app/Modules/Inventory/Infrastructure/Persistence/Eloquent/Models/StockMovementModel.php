<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class StockMovementModel extends Model
{
    use HasUuids;

    protected $table = 'stock_movements';

    protected $fillable = [
        'id',
        'tenant_id',
        'product_id',
        'user_id',
        'direction',
        'type',
        'quantity',
        'reason',
        'note',
        'unit_cost_in_cents',
        'occurred_at',
    ];
}
