<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->boolean('allow_concurrent_sessions')->default(false)->after('accessory_options');
            $table->unsignedInteger('session_lifetime_days')->nullable()->after('allow_concurrent_sessions');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['allow_concurrent_sessions', 'session_lifetime_days']);
        });
    }
};
