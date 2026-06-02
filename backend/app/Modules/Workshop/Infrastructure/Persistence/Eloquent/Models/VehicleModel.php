<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class VehicleModel extends Model
{
    use HasUuids;

    protected $table = 'vehicles';

    protected $fillable = [
        'id',
        'tenant_id',
        'plate',
        'brand',
        'model',
        'year',
        'owner_name',
        'owner_phone',
    ];
}
