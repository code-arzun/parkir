<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ParkingLocation;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Super Admin
        $superAdmin = User::create([
            'name'     => 'Super Admin SaaS',
            'email'    => 'admin@parkirpwa.com',
            'password' => Hash::make('password123'),
            'role'     => 'super_admin',
        ]);

        // 2. Buat Akun Owner Parkir
        $owner = User::create([
            'name'     => 'Bapak Owner (Pemilik Cafe)',
            'email'    => 'owner@cafemerdeka.com',
            'password' => Hash::make('password123'),
            'role'     => 'owner',
        ]);

        // 3. Buat Lokasi Parkir Milik Owner
        $location = ParkingLocation::create([
            'owner_id' => $owner->id,
            'name'     => 'Parkir Cafe Merdeka',
            'address'  => 'Jl. Merdeka No. 45, Surabaya',
        ]);

        // Hubungkan Owner ke Lokasi Parkirnya
        $owner->update(['parking_location_id' => $location->id]);

        // 4. Buat 2 Akun Penjaga Parkir (Attendant)
        User::create([
            'name'                => 'Penjaga Shift Pagi',
            'email'               => 'penjaga1@cafemerdeka.com',
            'password'            => Hash::make('password123'),
            'role'                => 'attendant',
            'parking_location_id' => $location->id,
        ]);

        User::create([
            'name'                => 'Penjaga Shift Malam',
            'email'               => 'penjaga2@cafemerdeka.com',
            'password'            => Hash::make('password123'),
            'role'                => 'attendant',
            'parking_location_id' => $location->id,
        ]);

        // 5. Buat Sample Data Kendaraan Pelanggan (Motor & Sepeda)
        Vehicle::create([
            'vehicle_type'     => 'motor',
            'license_plate'    => 'B1234XYZ',
            'password'         => Hash::make('B1234XYZ'),
            'driver_name'      => 'Budi Santoso',
            'whatsapp_number'   => '081234567890',
            'ownership_status' => 'pribadi',
            'qr_token'         => (string) Str::uuid(),
        ]);

        Vehicle::create([
            'vehicle_type'     => 'sepeda',
            'license_plate'    => 'SPD-260917-001',
            'password'         => Hash::make('SPD-260917-001'),
            'driver_name'      => 'Siti Aminah',
            'whatsapp_number'   => '089876543210',
            'ownership_status' => 'pribadi',
            'qr_token'         => (string) Str::uuid(),
        ]);
    }
}