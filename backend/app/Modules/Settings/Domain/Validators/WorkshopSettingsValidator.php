<?php

namespace App\Modules\Settings\Domain\Validators;

use App\Modules\Settings\Domain\Entities\WorkshopSettings;

final class WorkshopSettingsValidator
{
    public static function validate(WorkshopSettings $settings): void
    {
        if (trim($settings->displayName()) === '') {
            $settings->notification()->add(
                field: 'display_name',
                message: 'Workshop display name is required.',
                code: 'workshop_settings.display_name_required',
            );
        }

        if ($settings->minimumStockDefault() < 0) {
            $settings->notification()->add(
                field: 'minimum_stock_default',
                message: 'Minimum stock default cannot be negative.',
                code: 'workshop_settings.minimum_stock_default_negative',
            );
        }

        if (! in_array($settings->currency(), ['BRL'], true)) {
            $settings->notification()->add(
                field: 'currency',
                message: 'Currency is not supported.',
                code: 'workshop_settings.currency_not_supported',
            );
        }

        if (! in_array($settings->plan(), ['starter', 'pro'], true)) {
            $settings->notification()->add(
                field: 'plan',
                message: 'Plan is not supported.',
                code: 'workshop_settings.plan_not_supported',
            );
        }

        if ($settings->userLimit() < 1) {
            $settings->notification()->add(
                field: 'user_limit',
                message: 'User limit must be greater than zero.',
                code: 'workshop_settings.user_limit_invalid',
            );
        }
    }
}
