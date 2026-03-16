<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evaluation_sessions', function (Blueprint $table) {
            $table->index(['company_id', 'created_at'], 'idx_eval_company_created');
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_eval_company_created');
        });
    }
};
