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
        Schema::table('driver_trackings', function (Blueprint $table) {
            $table->decimal('longitude', 11, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_trackings', function (Blueprint $table) {
            $table->decimal('longitude', 10, 8)->change();
        });
    }
};
