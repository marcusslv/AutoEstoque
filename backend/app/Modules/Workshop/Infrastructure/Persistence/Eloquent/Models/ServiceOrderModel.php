<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class ServiceOrderModel extends Model
{
    use HasUuids;

    protected $table = 'service_orders';

    protected $fillable = [
        'id',
        'tenant_id',
        'vehicle_id',
        'created_by_user_id',
        'customer_name',
        'services_description',
        'observations',
        'status',
        'opened_at',
    ];
}
