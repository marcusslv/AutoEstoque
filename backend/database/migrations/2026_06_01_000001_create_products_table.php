<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku', 80);
            $table->string('barcode', 120)->nullable();
            $table->string('category', 120)->nullable();
            $table->string('brand', 120)->nullable();
            $table->string('supplier', 160)->nullable();
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->unsignedBigInteger('cost_in_cents');
            $table->char('currency', 3)->default('BRL');
            $table->timestamps();

            $table->unique(['tenant_id', 'sku']);
            $table->unique(['tenant_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
