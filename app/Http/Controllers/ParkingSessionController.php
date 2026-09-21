<?php

namespace App\Http\Controllers;

use App\Models\ParkingSession;
use Illuminate\Http\Request;

class ParkingSessionController extends Controller
{
    // Laporan & Histori Sesi Parkir (dengan Filter)
    // public function index(Request $request)
    // {
    //     $user = auth()->user();

    //     $query = ParkingSession::with(['vehicle', 'attendant', 'parkingLocation'])
    //         ->where('parking_location_id', $user->parking_location_id);

    //     // Filter berdasarkan Status Parkir (active / completed)
    //     if ($request->has('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     // Filter berdasarkan Status Bayar (paid / unpaid)
    //     if ($request->has('payment_status')) {
    //         $query->where('payment_status', $request->payment_status);
    //     }

    //     // Filter rentang tanggal
    //     if ($request->has('start_date') && $request->has('end_date')) {
    //         $query->whereBetween('check_in_time', [$request->start_date, $request->end_date]);
    //     }

    //     $sessions = $query->latest()->paginate(20);

    //     return response()->json(['status' => 'success', 'data' => $sessions]);
    // }
    public function index(Request $request)
    {
        $user = auth()->user();

        $sessions = ParkingSession::with('vehicle')
            ->where('parking_location_id', $user->parking_location_id)
            ->where('status', 'active')
            ->orderBy('check_in_time', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $sessions
        ]);
    }

    // Ringkasan Laporan Pendapatan (Dashboard Owner)
    public function summary(Request $request)
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        $activeVehicles = ParkingSession::where('parking_location_id', $user->parking_location_id)
            ->where('status', 'active')
            ->count();

        $todayRevenue = ParkingSession::where('parking_location_id', $user->parking_location_id)
            ->where('status', 'completed')
            ->whereDate('check_out_time', $today)
            ->sum('total_fee');

        $todayTransactions = ParkingSession::where('parking_location_id', $user->parking_location_id)
            ->whereDate('check_in_time', $today)
            ->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'active_vehicles_count' => $activeVehicles,
                'today_revenue'        => (float) $todayRevenue,
                'today_transactions'   => $todayTransactions,
            ]
        ]);
    }
}