<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_type', ['motor', 'sepeda'])->default('motor');
            $table->string('license_plate')->unique(); // Plat Nomor (misal: B1234XYZ) atau ID Sepeda (misal: SPD-001)
            $table->string('password');
            $table->string('driver_name');              // Nama Pengemudi/Pemilik
            $table->string('whatsapp_number');          // No WA
            
            // Status Kepemilikan Kendaraan
            $table->enum('ownership_status', [
                'pribadi', 
                'orang_tua', 
                'keluarga', 
                'teman', 
                'kantor', 
                'sewa'
            ])->default('pribadi');
            
            $table->string('master_photo')->nullable(); // Path/URL foto master kendaraan
            $table->string('qr_token')->unique();       // Token unik untuk generate QR Code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};