<?php

namespace App\Http\Controllers;

use App\Models\ParkingRate;
use App\Models\ParkingSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $locationId = $user->parking_location_id;

        // 1. Ringkasan Angka Utama
        $todayRevenue = ParkingSession::where('parking_location_id', $locationId)
            ->where('payment_status', 'paid')
            ->whereDate('updated_at', Carbon::today())
            ->sum('total_fee');

        $monthRevenue = ParkingSession::where('parking_location_id', $locationId)
            ->where('payment_status', 'paid')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('total_fee');

        $activeVehiclesCount = ParkingSession::where('parking_location_id', $locationId)
            ->where('status', 'active')
            ->count();

        $completedTodayCount = ParkingSession::where('parking_location_id', $locationId)
            ->where('status', 'completed')
            ->whereDate('check_out_time', Carbon::today())
            ->count();

        // 2. Breakdown Jenis Kendaraan Aktif
        $activeMotor = ParkingSession::where('parking_location_id', $locationId)
            ->where('status', 'active')
            ->whereHas('vehicle', fn($q) => $q->where('vehicle_type', 'motor'))
            ->count();

        $activeSepeda = ParkingSession::where('parking_location_id', $locationId)
            ->where('status', 'active')
            ->whereHas('vehicle', fn($q) => $q->where('vehicle_type', 'sepeda'))
            ->count();

        // 3. Transaksi Terbaru (10 Terakhir)
        $recentTransactions = ParkingSession::with(['vehicle', 'attendant'])
            ->where('parking_location_id', $locationId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $motorRate = ParkingRate::where('parking_location_id', $locationId)
            ->where('vehicle_type', 'motor')
            ->first();

        $sepedaRate = ParkingRate::where('parking_location_id', $locationId)
            ->where('vehicle_type', 'sepeda')
            ->first();

        return view('owner.dashboard', compact(
            'todayRevenue',
            'monthRevenue',
            'activeVehiclesCount',
            'completedTodayCount',
            'activeMotor',
            'activeSepeda',
            'recentTransactions',
            'motorRate',
            'sepedaRate'
        ));
    }
}