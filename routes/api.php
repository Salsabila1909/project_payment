<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FingerprintController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ambil siswa yang menunggu enroll fingerprint
Route::get('/fingerprint/pending', [FingerprintController::class, 'pending']);

// Simpan hasil enroll fingerprint dari Arduino
Route::post('/fingerprint/register', [FingerprintController::class, 'register']);

// Scan fingerprint untuk transaksi
Route::post('/fingerprint/scan', [FingerprintController::class, 'scan']);
