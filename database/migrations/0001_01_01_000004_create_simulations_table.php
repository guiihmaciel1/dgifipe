<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_session_id')->constrained()->cascadeOnDelete();
            $table->string('model');
            $table->string('storage', 10);
            $table->unsignedTinyInteger('battery_health');
            $table->json('conditions');
            $table->decimal('market_average', 10, 2)->nullable();
            $table->decimal('price_min', 10, 2)->nullable();
            $table->decimal('price_max', 10, 2)->nullable();
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->unsignedInteger('listings_count')->default(0);
            $table->boolean('low_data_warning')->default(false);
            $table->timestamps();

            $table->index(['evaluation_session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};
