<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverTracking extends Model
{
    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'tracked_at',
        'booking_id',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
