<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('plate', 20);
            $table->string('brand', 120);
            $table->string('model', 120);
            $table->unsignedSmallInteger('year');
            $table->string('owner_name', 160);
            $table->string('owner_phone', 40);
            $table->timestamps();

            $table->unique(['tenant_id', 'plate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
