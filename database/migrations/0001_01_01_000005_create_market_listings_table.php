<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_listings', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('storage', 10);
            $table->decimal('price', 10, 2);
            $table->string('city');
            $table->enum('source', ['facebook', 'olx', 'manual'])->default('manual');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->date('collected_at');
            $table->timestamps();

            $table->index(['model', 'storage', 'city', 'collected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_listings');
    }
};
