<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'address',
    ];

    // Relasi: Lokasi Parkir dimiliki oleh 1 Owner (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Relasi: Lokasi Parkir memiliki banyak Penjaga (Attendant)
    public function attendants()
    {
        return $this->hasMany(User::class, 'parking_location_id');
    }

    // Relasi: Lokasi Parkir memiliki banyak Sesi Parkir
    public function parkingSessions()
    {
        return $this->hasMany(ParkingSession::class);
    }

    public function rates()
    {
        return $this->hasMany(ParkingRate::class);
    }

}