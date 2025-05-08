<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    const STATUS_FINDING_DRIVER = 'finding_driver';
    const STATUS_DRIVER_PICKUP = 'driver_pickup';
    const STATUS_DRIVER_DELIVER = 'driver_deliver';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'customer_id',
        'driver_id',
        'latitude_origin',
        'longitude_origin',
        'address_origin',
        'latitude_destination',
        'longitude_destination',
        'address_destination',
        'distance',
        'price',
        'status',
    ];
}
