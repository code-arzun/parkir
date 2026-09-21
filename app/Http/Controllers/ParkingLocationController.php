<?php

namespace App\Http\Controllers;

use App\Models\ParkingLocation;
use Illuminate\Http\Request;

class ParkingLocationController extends Controller
{
    // Tampilkan semua lokasi parkir milik owner yang login (atau semua jika super_admin)
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $locations = ParkingLocation::with('owner')->get();
        } else {
            $locations = ParkingLocation::where('owner_id', $user->id)->get();
        }

        return response()->json(['status' => 'success', 'data' => $locations]);
    }

    // Tambah lokasi parkir baru
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $user = auth()->user();

        $location = ParkingLocation::create([
            'owner_id' => $user->id,
            'name'     => $request->name,
            'address'  => $request->address,
        ]);

        // Auto-assign lokasi parkir ke user owner jika belum ada
        if (!$user->parking_location_id) {
            $user->update(['parking_location_id' => $location->id]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Lokasi parkir berhasil ditambahkan.',
            'data'    => $location
        ], 201);
    }

    // Detail lokasi parkir
    public function show($id)
    {
        $location = ParkingLocation::with(['owner', 'attendants'])->findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $location]);
    }

    // Update lokasi parkir
    public function update(Request $request, $id)
    {
        $location = ParkingLocation::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $location->update($request->only(['name', 'address']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Data lokasi parkir diperbarui.',
            'data'    => $location
        ]);
    }

    // Hapus lokasi parkir
    public function destroy($id)
    {
        $location = ParkingLocation::findOrFail($id);
        $location->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lokasi parkir berhasil dihapus.'
        ]);
    }
}