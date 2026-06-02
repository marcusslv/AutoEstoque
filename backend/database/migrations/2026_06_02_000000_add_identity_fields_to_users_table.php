<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignUuid('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->string('status', 20)->default('active')->after('password');
            $table->string('role', 40)->default('admin')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn(['status', 'role']);
        });
    }
};
