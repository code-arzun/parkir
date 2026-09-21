<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Daftar semua kendaraan yang terdaftar di sistem
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Pencarian berdasarkan Nopol / ID Sepeda atau Nama Pengemudi
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('license_plate', 'LIKE', "%{$search}%")
                  ->orWhere('driver_name', 'LIKE', "%{$search}%")
                  ->orWhere('whatsapp_number', 'LIKE', "%{$search}%");
        }

        $vehicles = $query->latest()->paginate(20);

        return response()->json(['status' => 'success', 'data' => $vehicles]);
    }

    // Detail profil kendaraan & histori singkat
    public function show($id)
    {
        $vehicle = Vehicle::with(['parkingSessions' => function ($q) {
            $q->latest()->take(10); // 10 transaksi terakhir
        }])->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => $vehicle]);
    }

    // Update profil pelanggan (Nama, WA, Status Kepemilikan)
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'driver_name'      => 'sometimes|required|string|max:255',
            'whatsapp_number'   => 'sometimes|required|string|max:20',
            'ownership_status' => 'sometimes|required|in:pribadi,orang_tua,keluarga,teman,kantor,sewa',
        ]);

        $vehicle->update($request->only(['driver_name', 'whatsapp_number', 'ownership_status']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil pelanggan berhasil diperbarui.',
            'data'    => $vehicle
        ]);
    }

    // Hapus data kendaraan
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kendaraan berhasil dihapus.'
        ]);
    }
}