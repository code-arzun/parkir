<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ParkingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomerPortalController extends Controller
{
    /**
     * Tampilan Utama Portal Pengguna (Single Page PWA)
     */
    public function index()
    {
        return view('customer.index');
    }

    /**
     * API Process Login Pengendara
     */
    public function login(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string',
            'password'      => 'required|string',
        ]);

        $search = strtoupper(str_replace(' ', '', trim($request->license_plate)));

        $vehicle = Vehicle::where('license_plate', $search)
            ->orWhere('qr_token', $search)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kendaraan dengan Nopol/ID ini belum terdaftar di sistem.'
            ], 404);
        }

        // Verifikasi Hash Password
        if (!Hash::check($request->password, $vehicle->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nomor Kendaraan atau Password/PIN salah.'
            ], 401);
        }

        // Cek Apakah Masih Menggunakan Password Default (Nopol)
        $isDefaultPassword = Hash::check($vehicle->license_plate, $vehicle->password);
        $isDefaultName = ($vehicle->driver_name === 'Pelanggan Umum' || empty($vehicle->driver_name));
        $isFirstLogin = $isDefaultPassword || $isDefaultName;
        // $isFirstLogin = Hash::check($vehicle->license_plate, $vehicle->password);

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'vehicle_id'       => $vehicle->id,
                'license_plate'    => $vehicle->license_plate,
                'vehicle_type'     => $vehicle->vehicle_type,
                'driver_name'      => $vehicle->driver_name,
                'whatsapp_number'  => $vehicle->whatsapp_number,
                'ownership_status' => $vehicle->ownership_status,
                'qr_token'         => $vehicle->qr_token,
                'is_first_login'   => $isFirstLogin, // Flag penanda harus ubah password
            ]
        ]);
    }

    /**
     * API Fetch Detail Tiket & Riwayat (Membutuhkan ID Kendaraan)
     */
    public function getTicketDetail(Request $request)
    {
        $vehicleId = $request->query('vehicle_id');

        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data kendaraan tidak ditemukan.'
            ], 404);
        }

        // 1. Sesi Parkir Aktif
        $activeSession = ParkingSession::with(['parkingLocation', 'vehicle'])
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'active')
            ->first();

        $activeData = null;
        if ($activeSession) {
            $checkInTime = Carbon::parse($activeSession->check_in_time);
            $now = Carbon::now()->setlocale('id');
            $totalMinutes = $checkInTime->diffInMinutes($now);

            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            // Ambil Aturan Tarif Lokasi
            $rateRule = \App\Models\ParkingRate::where('parking_location_id', $activeSession->parking_location_id)
                ->where('vehicle_type', $vehicle->vehicle_type)
                ->first();

            $baseRate = $rateRule ? $rateRule->base_rate : ($vehicle->vehicle_type === 'motor' ? 2000 : 1000);
            $hourlyRate = $rateRule ? $rateRule->hourly_rate : 1000;
            $rateType = $rateRule ? $rateRule->rate_type : 'progressive';
            $graceMinutes = $rateRule ? $rateRule->grace_period_minutes : 10;
            $maxDailyRate = $rateRule ? $rateRule->max_daily_rate : null;

            // Kalkulasi Biaya Berjalan (Estimated Fee)
            if ($totalMinutes <= $graceMinutes) {
                $estimatedFee = 0;
            } elseif ($rateType === 'flat') {
                $estimatedFee = $baseRate;
            } else {
                $billableHours = max(1, ceil($totalMinutes / 60));
                $estimatedFee = $baseRate + (($billableHours - 1) * $hourlyRate);

                if ($maxDailyRate && $estimatedFee > $maxDailyRate) {
                    $estimatedFee = $maxDailyRate;
                }
            }

            // Jika sudah bayar lunas saat Check-In, estimasi tagihan saat ini = 0
            $remainingFee = $activeSession->payment_status === 'paid' ? 0 : $estimatedFee;

            $activeData = [
                'session_id'     => $activeSession->id,
                'location_name'  => $activeSession->parkingLocation->name ?? 'Lokasi Parkir',
                // 'check_in_time'  => $checkInTime->isoFormat('dddd, DD/MM/YYYY HH:mm'),
                'check_in_day'     => $checkInTime->isoFormat('dddd'),      // Contoh: Selasa
                'check_in_date'    => $checkInTime->format('d/m/Y'),        // Contoh: 22/09/2026
                'check_in_clock'   => $checkInTime->format('H:i') . ' WIB',  // Contoh: 14:30 WIB
                'duration'       => ($hours > 0 ? "{$hours} Jam " : "") . "{$minutes} Mnt",
                'payment_status' => $activeSession->payment_status,
                'estimated_fee'  => $estimatedFee,
                'remaining_fee'  => $remainingFee,
            ];
        }

        // 2. Riwayat Parkir (10 Terakhir)
        $history = ParkingSession::with('parkingLocation')
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                Carbon::setLocale('id');
                return [
                    'location_name'  => $item->parkingLocation->name ?? 'Lokasi Parkir',
                    'check_in_time'  => Carbon::parse($item->check_in_time)->isoFormat('dddd, DD/MM/YYYY HH:mm'),
                    'check_out_time' => Carbon::parse($item->check_out_time)->isoFormat('dddd, DD/MM/YYYY HH:mm'),
                    'total_fee'      => $item->total_fee,
                    'payment_method' => strtoupper($item->payment_method),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'vehicle'        => $vehicle,
                'active_session' => $activeData,
                'history'        => $history,
            ]
        ]);
    }

    /**
     * API Update Profil & Ubah Password Pengendara
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'driver_name'      => 'required|string|max:100',
            'whatsapp_number'  => 'required|numeric|digits_between:9,13',
            'ownership_status' => 'required|string',
            'new_password'     => 'nullable|string|min:4',
        ],[
            'whatsapp_number.numeric'        => 'Nomor WhatsApp hanya boleh berisi angka.',
            'whatsapp_number.digits_between' => 'Nomor WhatsApp harus terdiri dari 9 hingga 13 digit.',
            'new_password.min'               => 'Password/PIN baru minimal 4 karakter.',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Cek Keamanan: Password Baru tidak boleh sama dengan Nopol
            if ($request->filled('new_password')) {
                $cleanNopol = strtoupper(str_replace(' ', '', $vehicle->license_plate));
                $cleanPass  = strtoupper(str_replace(' ', '', $request->new_password));

                if ($cleanPass === $cleanNopol) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Password baru tidak boleh sama dengan Nomor Polisi kendaraan Anda.'
                    ], 422);
                }
            }

        $updateData = [
            'driver_name'      => $request->driver_name,
            'whatsapp_number'  => $request->whatsapp_number,
            'ownership_status' => $request->ownership_status,
        ];

        // Jika mengisi password baru
        if ($request->filled('new_password')) {
            $updateData['password'] = Hash::make($request->new_password);
        }

        $vehicle->update($updateData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil & Password berhasil diperbarui!',
            'data'    => $vehicle
        ]);
    }
}