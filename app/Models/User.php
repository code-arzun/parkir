<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'parking_location_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: User (Penjaga/Owner) terhubung ke 1 Lokasi Parkir
    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class, 'parking_location_id');
    }

    // Relasi: User (Owner) memiliki banyak lokasi parkir
    public function ownedLocations()
    {
        return $this->hasMany(ParkingLocation::class, 'owner_id');
    }
}