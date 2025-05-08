<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_number',
        'is_active',
        'current_latitude',
        'current_longitude',
        'last_online'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_online' => 'datetime',
        'current_latitude' => 'float',
        'current_longitude' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
