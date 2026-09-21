<?php

namespace App\Http\Controllers;

use App\Models\ParkingRate;
use App\Models\ParkingSession;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParkingCheckOutController extends Controller
{
    /**
     * Preview perhitungan durasi & tarif sebelum Check-Out diselesaikan
     */
    public function show(Request $request)
    {
        $search = trim($request->query('search'));

        if (!$search) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Query pencarian tidak boleh kosong.'
            ], 400);
        }

        $vehicle = Vehicle::where('license_plate', strtoupper(str_replace(' ', '', $search)))
            ->orWhere('qr_token', $search)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kendaraan tidak ditemukan di sistem.'
            ], 404);
        }

        $session = ParkingSession::where('vehicle_id', $vehicle->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kendaraan {$vehicle->license_plate} tidak sedang dalam sesi parkir aktif."
            ], 404);
        }

        // 1. Ambil Aturan Tarif Lokasi untuk Jenis Kendaraan Ini
        $rateRule = ParkingRate::where('parking_location_id', $session->parking_location_id)
            ->where('vehicle_type', $vehicle->vehicle_type)
            ->first();

        // Nilai Default jika Owner belum mengatur tarif
        $baseRate = $rateRule ? $rateRule->base_rate : ($vehicle->vehicle_type === 'motor' ? 2000 : 1000);
        $hourlyRate = $rateRule ? $rateRule->hourly_rate : 1000;
        $rateType = $rateRule ? $rateRule->rate_type : 'progressive';
        $graceMinutes = $rateRule ? $rateRule->grace_period_minutes : 10;
        $maxDailyRate = $rateRule ? $rateRule->max_daily_rate : null;

        // 2. Kalkulasi Durasi
        $checkInTime = Carbon::parse($session->check_in_time);
        $now = Carbon::now();
        $totalMinutes = $checkInTime->diffInMinutes($now);
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $durationText = ($hours > 0 ? "{$hours} Jam " : "") . "{$minutes} Mnt";

        // 3. Kalkulasi Tarif Dinamis
        if ($totalMinutes <= $graceMinutes) {
            // Bebas Biaya jika masih dalam Masa Tenggang (Grace Period)
            $totalFee = 0;
        } elseif ($rateType === 'flat') {
            // Tarif Sekali Masuk (Flat)
            $totalFee = $baseRate;
        } else {
            // Tarif Progresif (Jam Pertama + Jam Berikutnya)
            $billableHours = max(1, ceil($totalMinutes / 60));
            $totalFee = $baseRate + (($billableHours - 1) * $hourlyRate);

            // Batasi dengan Max Daily Rate jika diatur
            if ($maxDailyRate && $totalFee > $maxDailyRate) {
                $totalFee = $maxDailyRate;
            }
        }

        // Jika sudah bayar lunas saat Check-In, sisa tagihan = 0
        $remainingFee = $session->payment_status === 'paid' ? 0 : $totalFee;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'session_id'     => $session->id,
                'license_plate'  => $vehicle->license_plate,
                'vehicle_type'   => $vehicle->vehicle_type,
                'driver_name'    => $vehicle->driver_name,
                'check_in_time'  => $checkInTime->format('d/m/Y H:i'),
                'duration'       => [
                    'total_minutes' => $totalMinutes,
                    'text'          => $durationText,
                ],
                'total_fee'      => $totalFee,
                'payment_status' => $session->payment_status,
                'remaining_fee'  => $remainingFee,
            ]
        ]);
    }

    /**
     * Memproses Penyelesaian Check-Out (Pelunasan Transaksi)
     */
    public function process(Request $request, $sessionId)
    {
        $session = ParkingSession::where('id', $sessionId)
            ->where('status', 'active')
            ->firstOrFail();

        $vehicle = Vehicle::findOrFail($session->vehicle_id);

        // Ambil Aturan Tarif Lokasi
        $rateRule = ParkingRate::where('parking_location_id', $session->parking_location_id)
            ->where('vehicle_type', $vehicle->vehicle_type)
            ->first();

        $baseRate = $rateRule ? $rateRule->base_rate : ($vehicle->vehicle_type === 'motor' ? 2000 : 1000);
        $hourlyRate = $rateRule ? $rateRule->hourly_rate : 1000;
        $rateType = $rateRule ? $rateRule->rate_type : 'progressive';
        $graceMinutes = $rateRule ? $rateRule->grace_period_minutes : 10;
        $maxDailyRate = $rateRule ? $rateRule->max_daily_rate : null;

        // Kalkulasi Waktu & Biaya Akhir
        $checkInTime = Carbon::parse($session->check_in_time);
        $now = Carbon::now();
        $totalMinutes = $checkInTime->diffInMinutes($now);

        if ($totalMinutes <= $graceMinutes) {
            $totalFee = 0;
        } elseif ($rateType === 'flat') {
            $totalFee = $baseRate;
        } else {
            $billableHours = max(1, ceil($totalMinutes / 60));
            $totalFee = $baseRate + (($billableHours - 1) * $hourlyRate);

            if ($maxDailyRate && $totalFee > $maxDailyRate) {
                $totalFee = $maxDailyRate;
            }
        }

        // Update Sesi Parkir ke Status Completed
        $session->update([
            'check_out_time' => $now,
            'total_fee'      => $totalFee, // Simpan total biaya final ke DB
            'payment_status' => 'paid',
            'payment_method' => $request->input('payment_method', 'cash'),
            'status'         => 'completed',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Check-out berhasil diproses.'
        ]);
    }
}