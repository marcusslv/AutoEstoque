<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->unsignedBigInteger('created_by_user_id');
            $table->string('customer_name', 160);
            $table->text('services_description');
            $table->text('observations')->nullable();
            $table->string('status', 20);
            $table->timestamp('opened_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
