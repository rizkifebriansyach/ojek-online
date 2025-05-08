<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'interval_seconds',
        'price_per_km'
    ];

    protected $casts = [
        'interval_seconds' => 'integer',
        'price_per_km' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($model){
            if(static::count()>0){
                return false;
            }
        });
    }

    public static function getSetting()
    {
        return static::first();
    }
}
