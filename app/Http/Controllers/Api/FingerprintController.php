<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class FingerprintController extends Controller
{
    /**
     * Ambil siswa yang statusnya Belum Terdaftar
     * Dipanggil Arduino (GET)
     */
    public function pending()
    {
        $siswa = Siswa::where('status', 'Belum Terdaftar')->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada siswa menunggu'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'siswa_id' => $siswa->id,
                'nama' => $siswa->nama
            ]
        ], 200);
    }

    /**
     * Simpan fingerprint ID setelah enroll
     * Dipanggil Arduino (POST)
     */
    public function register(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|integer',
            'fingerprint_id' => 'required|integer',
        ]);

        $siswa = Siswa::find($request->siswa_id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        $siswa->fingerprint_id = $request->fingerprint_id;
        $siswa->status = 'Terdaftar';
        $siswa->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil diregistrasi'
        ], 200);
    }

    /**
     * Scan fingerprint untuk transaksi
     */
    public function scan(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1'
        ]);

        $siswa = Siswa::where('fingerprint_id', $request->fingerprint_id)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Fingerprint tidak dikenal'
            ], 404);
        }

        if ($siswa->saldo < $request->jumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak cukup'
            ], 400);
        }

        $siswa->saldo -= $request->jumlah;
        $siswa->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil',
            'saldo' => $siswa->saldo
        ], 200);
    }
}
