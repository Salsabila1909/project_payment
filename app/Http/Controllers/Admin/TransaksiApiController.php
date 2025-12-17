<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiApiController extends Controller
{
    public function checkAndCreate(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|integer',
            'tipe' => 'required|in:topup,pembelian',
            'jumlah' => 'required|integer',
            'keterangan' => 'nullable|string'
        ]);

        $siswa = Siswa::where('fingerprint_id', $request->fingerprint_id)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Fingerprint tidak terdaftar'], 404);
        }

        // contoh: jika pembelian, potong saldo
        if ($request->tipe === 'pembelian') {
            if ($siswa->saldo < $request->jumlah) {
                return response()->json(['success' => false, 'message' => 'Saldo tidak cukup'], 400);
            }
            $siswa->saldo -= $request->jumlah;
            $siswa->save();
        } else if ($request->tipe === 'topup') {
            $siswa->saldo += $request->jumlah;
            $siswa->save();
        }

        $transaksi = Transaksi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'tipe' => $request->tipe,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status' => 'sukses'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi sukses',
            'data' => [
                'transaksi' => $transaksi,
                'siswa' => $siswa
            ]
        ]);
    }
}
