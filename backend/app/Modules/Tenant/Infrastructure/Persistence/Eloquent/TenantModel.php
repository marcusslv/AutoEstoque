<?php

namespace App\Modules\Tenant\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class TenantModel extends Model
{
    use HasUuids;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'document',
        'status',
    ];
}
