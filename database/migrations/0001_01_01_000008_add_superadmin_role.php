<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to include superadmin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'seller') DEFAULT 'seller'");

        // superadmin doesn't belong to any company
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'seller') DEFAULT 'seller'");
    }
};
