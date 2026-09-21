<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_location_id',
        'vehicle_id',
        'attendant_id',
        'check_in_time',
        'check_out_time',
        'payment_status',
        'payment_method',
        'total_fee',
        'status',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    // Relasi ke Lokasi Parkir
    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }

    // Relasi ke Akun Kendaraan
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relasi ke Penjaga/Attendant yang memproses
    public function attendant()
    {
        return $this->belongsTo(User::class, 'attendant_id');
    }
}