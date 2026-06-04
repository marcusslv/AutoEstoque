<?php

namespace App\Modules\Settings\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class WorkshopSettingsModel extends Model
{
    use HasUuids;

    protected $table = 'workshop_settings';

    protected $fillable = [
        'tenant_id',
        'display_name',
        'legal_name',
        'document',
        'phone',
        'email',
        'address',
        'timezone',
        'currency',
        'allow_negative_stock',
        'auto_deduct_stock_on_service_order_finish',
        'minimum_stock_default',
        'notify_minimum_stock',
        'notify_zero_stock',
        'notification_email',
        'notification_phone',
        'plan',
        'user_limit',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'allow_negative_stock' => 'boolean',
            'auto_deduct_stock_on_service_order_finish' => 'boolean',
            'minimum_stock_default' => 'integer',
            'notify_minimum_stock' => 'boolean',
            'notify_zero_stock' => 'boolean',
            'user_limit' => 'integer',
        ];
    }
}
