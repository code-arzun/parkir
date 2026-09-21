<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_location_id')->constrained()->onDelete('cascade');
            $table->enum('vehicle_type', ['motor', 'sepeda']);
            $table->enum('rate_type', ['flat', 'progressive'])->default('progressive');
            
            // Tarif
            $table->decimal('base_rate', 10, 2)->default(2000); // Tarif Jam Pertama / Flat
            $table->decimal('hourly_rate', 10, 2)->default(1000); // Tarif per Jam Berikutnya (jika progresif)
            $table->integer('grace_period_minutes')->default(10); // Masa tenggang gratis (misal < 10 menit)
            $table->decimal('max_daily_rate', 10, 2)->nullable(); // Batas tarif maksimal per hari
            $table->decimal('lost_ticket_penalty', 10, 2)->default(20000); // Denda tiket hilang
            
            $table->timestamps();

            // Memastikan 1 lokasi hanya punya 1 aturan per jenis kendaraan
            $table->unique(['parking_location_id', 'vehicle_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_rates');
    }
};