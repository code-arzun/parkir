<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\ParkingCheckInController;
use App\Http\Controllers\ParkingCheckOutController;
use App\Http\Controllers\ParkingSessionController;
use App\Http\Controllers\ParkingRateController;
use App\Http\Controllers\OwnerDashboardController;

/*
|--------------------------------------------------------------------------
| 1. PORTAL PUBLIK PENGENDARA PARKIR (/pengguna)
|--------------------------------------------------------------------------
*/
// Rute utama portal pengguna
Route::get('/pengguna', [CustomerPortalController::class, 'index'])->name('pengguna.app');

// Redirect dari Root Domain & Alias /tiket ke /pengguna
Route::get('/', function () {
    return redirect()->route('pengguna.app');
});

Route::get('/tiket', function () {
    return redirect()->route('pengguna.app');
});

// API Publik Cek Tiket Digital & Riwayat Pengendara (Tanpa Auth)
// Route::get('/api/customer/ticket', [CustomerPortalController::class, 'checkTicket']);
Route::prefix('api/customer')->group(function () {
    Route::post('/login', [CustomerPortalController::class, 'login']);
    Route::get('/ticket-detail', [CustomerPortalController::class, 'getTicketDetail']);
    Route::post('/profile/update', [CustomerPortalController::class, 'updateProfile']);
});


/*
|--------------------------------------------------------------------------
| 2. AUTENTIKASI PETUGAS & OWNER
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| 3. AREA APLIKASI OPERASIONAL (PETUGAS & OWNER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Aplikasi POS Penjaga Parkir (/pos)
    Route::get('/pos', function () {
        return view('owner.attendant');
    })->name('attendant.app');

    // Dashboard Owner (/dashboard)
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

    // Halaman Pengaturan Tarif Owner (/tarif)
    Route::get('/tarif', [ParkingRateController::class, 'index'])->name('owner.rates');

    // API Internal Operasional PWA
    Route::prefix('api/parking')->group(function () {
        Route::post('/check-in', [ParkingCheckInController::class, 'store']);
        Route::get('/check-out/preview', [ParkingCheckOutController::class, 'show']);
        Route::post('/check-out/{sessionId}', [ParkingCheckOutController::class, 'process']);
        Route::get('/sessions', [ParkingSessionController::class, 'index']);
        Route::get('/vehicles/search', [ParkingCheckInController::class, 'searchVehicle']);
        Route::post('/rates', [ParkingRateController::class, 'storeOrUpdate']);
    });
});