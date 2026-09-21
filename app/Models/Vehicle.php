<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'license_plate',
        'password',
        'driver_name',
        'whatsapp_number',
        'ownership_status',
        'master_photo',
        'qr_token',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi: Kendaraan memiliki banyak Sesi Parkir (Riwayat Parkir)
    public function parkingSessions()
    {
        return $this->hasMany(ParkingSession::class);
    }
}