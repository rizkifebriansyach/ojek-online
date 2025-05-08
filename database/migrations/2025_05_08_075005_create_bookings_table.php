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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->nullOnDelete();
            $table->decimal('latitude_origin', 10, 8);
            $table->decimal('longitude_origin', 10, 8);
            $table->string('address_origin');
            $table->decimal('latitude_destination', 10, 8);
            $table->decimal('longitude_destination', 10, 8);
            $table->string('address_destination');
            $table->decimal('distance', 10, 2);
            $table->decimal('price', 10, 2);
            $table->enum('status', [
                'finding_driver',
                'driver_pickup',
                'driver_deliver',
                'arrived',
                'paid',
                'cancelled',
            ])->default('finding_driver');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
