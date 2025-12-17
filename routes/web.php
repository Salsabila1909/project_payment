<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TransaksiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// CLEAR CACHE
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('optimize');
    Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return '<h1>Cache berhasil dibersihkan!</h1>';
});

// LOGIN
Route::get('/', function () {
    return view('auth.login');
});
Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// DASHBOARD
Route::middleware('auth')->group(function () {
    Route::get('/keluar', [HomeController::class, 'keluar']);
    Route::get('/admin/home', [HomeController::class, 'index']);
    Route::get('/admin/change', [HomeController::class, 'change']);
    Route::post('/admin/change_password', [HomeController::class, 'change_password']);
});

/* ===============================================================
 *                      ROUTE SISWA
 * =============================================================== */
Route::prefix('admin/siswa')
    ->name('admin.siswa.')
    ->middleware(['auth', 'cekLevel:1 2'])
    ->controller(SiswaController::class)
    ->group(function () {

        // READ
        Route::get('/', 'read')->name('read');

        // ADD + CREATE
        Route::get('/add', 'add')->name('add');
        Route::post('/create', 'create')->name('create');

        // EDIT + UPDATE
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');

        // DELETE
        Route::delete('/delete/{id}', 'delete')->name('delete');

        // DETAIL + RIWAYAT
        Route::get('/detail/{id}', 'detail')->name('detail');

        // TOPUP SALDO
        Route::post('/topup/{id}', 'topup')->name('topup');

        // PEMBAYARAN SALDO
        Route::post('/payment/{id}', 'payment')->name('payment');

        // ➜ Riwayat transaksi siswa
        Route::get('/riwayat/{id}', 'riwayat')->name('riwayat');

        // ------------------------------------------------------
        // FORM DAFTAR FINGERPRINT UNTUK ADMIN — NEW
        // ------------------------------------------------------
        Route::get('/fingerprint/daftar/{id}', 'fingerprintForm')->name('fingerprint.form'); // NEW
    });


/* ===============================================================
 *                      ROUTE TRANSAKSI (UMUM)
 * =============================================================== */
Route::prefix('admin/transaksi')
    ->name('transaksi.')
    ->middleware('auth')
    ->controller(TransaksiController::class)
    ->group(function () {

        Route::get('/', 'index')->name('index');
        Route::get('/topup', 'topupForm')->name('topupForm');
        Route::post('/topup', 'topupSubmit')->name('topupSubmit');

        Route::get('/pembelian', 'pembelianForm')->name('pembelianForm');
        Route::post('/pembelian', 'pembelianSubmit')->name('pembelianSubmit');

        // FINGERPRINT (SCAN)
        Route::get('/scan/{id}', 'scanFingerprint')->name('scan');
        Route::post('/verify/{id}', 'verifyFingerprint')->name('verify');

        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');

        Route::delete('/delete/{id}', 'delete')->name('delete');
    });

/* ===============================================================
 *                  API UNTUK ARDUINO FINGERPRINT — NEW
 * =============================================================== */
Route::post('/api/fingerprint/store', [SiswaController::class, 'apiStoreFingerprint']); // NEW
