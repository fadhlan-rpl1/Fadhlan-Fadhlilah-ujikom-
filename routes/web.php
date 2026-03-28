<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\OwnerController; 

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Rute utama /dashboard yang mengalihkan ke rute spesifik role
    Route::get('/dashboard', function() {
        $role = auth()->user()->role;
        if ($role == 'admin') return redirect('/admin/dashboard');
        if ($role == 'petugas') return redirect('/petugas/dashboard');
        if ($role == 'owner') return redirect('/owner/dashboard');
        return redirect('/')->with('loginError', 'Role tidak terdaftar!');
    });

    /* --- GRUP ADMIN --- */
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function() { return view('admin.dashboard'); });
        
        Route::get('/transaksi', [TransaksiController::class, 'index']);
        Route::post('/transaksi', [TransaksiController::class, 'store']);
        Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);
        
        Route::get('/tarif', [TarifController::class, 'index']);
        Route::post('/tarif', [TarifController::class, 'store']);
        Route::put('/tarif/{id}', [TarifController::class, 'update']);
        Route::delete('/tarif/{id}', [TarifController::class, 'destroy']);
        
        Route::get('/area', [AreaParkirController::class, 'index']);
        Route::post('/area', [AreaParkirController::class, 'store']);
        Route::put('/area/{id}', [AreaParkirController::class, 'update']);
        Route::delete('/area/{id}', [AreaParkirController::class, 'destroy']);
        
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::get('/logs', [LogController::class, 'index']);
    });

   /* --- GRUP PETUGAS --- */
    Route::prefix('petugas')->group(function () {
        Route::get('/dashboard', [PetugasController::class, 'index']);
        
        // Simpan Data
        Route::post('/transaksi/store', [TransaksiController::class, 'store']);
        
        // EDIT Data Kendaraan
        Route::put('/transaksi/update-data/{id}', [PetugasController::class, 'updateData']);
        
        // BAYAR Kendaraan (Checkout)
        Route::put('/transaksi/bayar/{id}', [PetugasController::class, 'bayar']);
        
        // HAPUS Data
        Route::delete('/transaksi/delete/{id}', [PetugasController::class, 'destroy']);
        
        // HALAMAN CETAK STRUK
        Route::get('/transaksi/struk/{id}', [PetugasController::class, 'struk']);
    });

  /* --- GRUP OWNER --- */
    Route::prefix('owner')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'index']);
        
        // RUTE BARU: Untuk Download PDF
        Route::get('/laporan/download', [OwnerController::class, 'downloadPDF']);
        
        // 👉 RUTE BARU: Untuk Fitur Hapus Transaksi (Memperbaiki 404 Not Found) 👈
        Route::delete('/transaksi/delete/{id}', [OwnerController::class, 'destroy']);
    });
});