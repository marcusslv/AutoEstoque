<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_settings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->unique()->constrained('tenants')->cascadeOnDelete();
            $table->string('display_name');
            $table->string('legal_name')->nullable();
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->char('currency', 3)->default('BRL');
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('auto_deduct_stock_on_service_order_finish')->default(true);
            $table->unsignedInteger('minimum_stock_default')->default(0);
            $table->boolean('notify_minimum_stock')->default(true);
            $table->boolean('notify_zero_stock')->default(true);
            $table->string('notification_email')->nullable();
            $table->string('notification_phone')->nullable();
            $table->string('plan')->default('starter');
            $table->unsignedInteger('user_limit')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_settings');
    }
};
