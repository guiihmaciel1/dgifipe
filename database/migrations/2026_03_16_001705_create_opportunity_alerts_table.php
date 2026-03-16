<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunity_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('storage');
            $table->decimal('listing_price', 10, 2);
            $table->decimal('market_average', 10, 2);
            $table->decimal('suggested_buy_price', 10, 2);
            $table->decimal('potential_profit', 10, 2);
            $table->decimal('profit_percentage', 5, 2);
            $table->string('source');
            $table->string('city');
            $table->string('title')->nullable();
            $table->string('url', 500)->nullable();
            $table->enum('status', ['new', 'viewed', 'dismissed'])->default('new');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_alerts');
    }
};
