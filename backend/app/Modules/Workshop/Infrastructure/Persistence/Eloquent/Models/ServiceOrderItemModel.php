<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class ServiceOrderItemModel extends Model
{
    use HasUuids;

    protected $table = 'service_order_items';

    protected $fillable = [
        'id',
        'tenant_id',
        'service_order_id',
        'product_id',
        'added_by_user_id',
        'quantity',
    ];
}
