<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ParkingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ParkingCheckInController extends Controller
{
    /**
     * Memproses Check-In Cepat Kendaraan (Motor / Sepeda)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Request (Data Pengemudi Dibuat Nullable agar Check-In Instan)
        $request->validate([
            'vehicle_type'     => 'required|in:motor,sepeda',
            'license_plate'    => 'required_if:vehicle_type,motor|nullable|string|max:15',
            'driver_name'      => 'nullable|string|max:255',
            'whatsapp_number'   => 'nullable|string|max:20',
            'ownership_status' => 'nullable|in:pribadi,orang_tua,keluarga,teman,kantor,sewa',
            'master_photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'pay_upfront'      => 'nullable|boolean',
            'total_fee'        => 'nullable|numeric|min:0',
        ]);

        $user = auth()->user();

        // Pastikan Penjaga / Owner memiliki lokasi parkir yang terasosiasi
        if (!$user->parking_location_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda belum terasosiasi dengan lokasi parkir mana pun.'
            ], 403);
        }

        // 2. Format / Auto-Generate Nopol atau ID Sepeda
        if ($request->vehicle_type === 'sepeda') {
            $licensePlate = $this->generateBicycleId();
        } else {
            // Bersihkan format plat nomor (misal: "b 1234 xyz" -> "B1234XYZ")
            $licensePlate = strtoupper(str_replace(' ', '', trim($request->license_plate)));
        }

        // 3. Handle Upload Foto Master (jika ada)
        $masterPhotoPath = null;
        if ($request->hasFile('master_photo')) {
            $file = $request->file('master_photo');
            $fileName = time() . '_' . $licensePlate . '.' . $file->getClientOriginalExtension();
            $masterPhotoPath = $file->storeAs('vehicles/master_photos', $fileName, 'public');
        }

        // 4. Cari atau Buat Akun Kendaraan Baru (Nilai Default jika Kosong)
        $vehicle = Vehicle::firstOrCreate(
            ['license_plate' => $licensePlate],
            [
                'vehicle_type'     => $request->vehicle_type,
                'password'         => Hash::make($licensePlate), // Default Password = Nopol/ID
                'driver_name'      => $request->driver_name ?? 'Pelanggan Umum',
                'whatsapp_number'   => $request->whatsapp_number ?? '-',
                'ownership_status' => $request->ownership_status ?? 'pribadi',
                'master_photo'     => $masterPhotoPath,
                'qr_token'         => (string) Str::uuid(), // Token Unik untuk QR Code PWA
            ]
        );

        // Jika kendaraan lama tapi penjaga mengunggah foto master baru
        if (!$vehicle->wasRecentlyCreated && $masterPhotoPath) {
            if ($vehicle->master_photo) {
                Storage::disk('public')->delete($vehicle->master_photo);
            }
            $vehicle->update([
                'master_photo' => $masterPhotoPath,
            ]);
        }

        // 5. Proteksi Double Check-In
        $activeSession = ParkingSession::where('vehicle_id', $vehicle->id)
            ->where('status', 'active')
            ->first();

        if ($activeSession) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kendaraan dengan Nopol/ID {$licensePlate} sedang aktif terparkir di lokasi.",
                'session' => $activeSession
            ], 422);
        }

        // 6. Buat Sesi Parkir Baru
        $isPaidUpfront = $request->boolean('pay_upfront', false);

        $session = ParkingSession::create([
            'parking_location_id' => $user->parking_location_id,
            'vehicle_id'          => $vehicle->id,
            'attendant_id'        => $user->id,
            'check_in_time'       => now(),
            'payment_status'      => $isPaidUpfront ? 'paid' : 'unpaid',
            'payment_method'      => 'cash',
            'total_fee'           => $isPaidUpfront ? ($request->total_fee ?? 0) : 0,
            'status'              => 'active',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Check-In berhasil disimpan.',
            'data'    => [
                'session_id'     => $session->id,
                'license_plate'  => $vehicle->license_plate,
                'vehicle_type'   => $vehicle->vehicle_type,
                'driver_name'    => $vehicle->driver_name,
                'qr_token'       => $vehicle->qr_token,
                'check_in_time'  => $session->check_in_time->format('Y-m-d H:i:s'),
                'payment_status' => $session->payment_status,
                'photo_url'      => $vehicle->master_photo ? asset('storage/' . $vehicle->master_photo) : null,
            ]
        ], 201);
    }

    /**
     * Helper Method: Auto-Generate ID Unik untuk Sepeda
     * Format: SPD-YYMMDD-001 (contoh: SPD-260918-001)
     */
    private function generateBicycleId(): string
    {
        $prefix = 'SPD-' . Carbon::now()->format('ymd') . '-';

        $latestBicycle = Vehicle::where('vehicle_type', 'sepeda')
            ->where('license_plate', 'LIKE', $prefix . '%')
            ->orderBy('license_plate', 'desc')
            ->first();

        if ($latestBicycle) {
            $lastNumber = (int) substr($latestBicycle->license_plate, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }

    public function searchVehicle(Request $request)
    {
        $query = trim($request->query('query'));

        if (!$query || strlen($query) < 2) {
            return response()->json([
                'status' => 'success',
                'data'   => []
            ]);
        }

        $vehicles = \App\Models\Vehicle::where('license_plate', 'LIKE', '%' . strtoupper(str_replace(' ', '', $query)) . '%')
            ->limit(5)
            ->get(['id', 'license_plate', 'driver_name', 'vehicle_type']);

        return response()->json([
            'status' => 'success',
            'data'   => $vehicles
        ]);
    }
}