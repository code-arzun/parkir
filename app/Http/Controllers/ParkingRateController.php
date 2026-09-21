<?php

namespace App\Http\Controllers;

use App\Models\ParkingRate;
use Illuminate\Http\Request;

class ParkingRateController extends Controller
{
    /**
     * Tampilan Halaman Pengaturan Tarif
     */
    public function index()
    {
        $locationId = auth()->user()->parking_location_id;

        $motorRate = ParkingRate::where('parking_location_id', $locationId)
            ->where('vehicle_type', 'motor')
            ->first();

        $sepedaRate = ParkingRate::where('parking_location_id', $locationId)
            ->where('vehicle_type', 'sepeda')
            ->first();

        return view('owner.rates', compact('motorRate', 'sepedaRate'));
    }
    
    /**
     * Menyimpan atau Memperbarui Aturan Tarif Parkir untuk Lokasi Owner
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'vehicle_type'         => 'required|in:motor,sepeda',
            'rate_type'            => 'required|in:flat,progressive',
            'base_rate'            => 'required|numeric|min:0',
            'hourly_rate'          => 'required|numeric|min:0',
            'grace_period_minutes' => 'required|integer|min:0',
            'max_daily_rate'       => 'nullable|numeric|min:0',
            'lost_ticket_penalty'  => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

        if (!$user->parking_location_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda belum terasosiasi dengan lokasi parkir.'
            ], 403);
        }

        $rate = ParkingRate::updateOrCreate(
            [
                'parking_location_id' => $user->parking_location_id,
                'vehicle_type'        => $request->vehicle_type,
            ],
            [
                'rate_type'            => $request->rate_type,
                'base_rate'            => $request->base_rate,
                'hourly_rate'          => $request->hourly_rate,
                'grace_period_minutes' => $request->grace_period_minutes,
                'max_daily_rate'       => $request->max_daily_rate,
                'lost_ticket_penalty'  => $request->lost_ticket_penalty,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengaturan tarif berhasil disimpan.',
            'data'    => $rate
        ]);
    }
}