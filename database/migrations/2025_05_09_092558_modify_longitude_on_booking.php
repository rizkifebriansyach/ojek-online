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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('longitude_origin', 11, 8)->change();
            $table->decimal('longitude_destination', 11, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('longitude_origin', 10, 8)->change();
            $table->decimal('longitude_destination', 10, 8)->change();
        });
    }
};
