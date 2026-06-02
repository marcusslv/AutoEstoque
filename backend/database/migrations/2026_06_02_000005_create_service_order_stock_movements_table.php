<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_order_stock_movements', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('service_order_id')->constrained('service_orders')->cascadeOnDelete();
            $table->foreignUuid('service_order_item_id')->constrained('service_order_items')->cascadeOnDelete();
            $table->foreignUuid('stock_movement_id')->constrained('stock_movements')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('stock_movement_id');
            $table->index(['tenant_id', 'service_order_id']);
            $table->index(['tenant_id', 'service_order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order_stock_movements');
    }
};
