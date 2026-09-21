<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_location_id',
        'vehicle_type',
        'rate_type',
        'base_rate',
        'hourly_rate',
        'grace_period_minutes',
        'max_daily_rate',
        'lost_ticket_penalty',
    ];

    public function parkingLocation()
    {
        return $table->belongsTo(ParkingLocation::class);
    }
}