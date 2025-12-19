<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Siswa;
use App\Models\RiwayatTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // ============================
    // LIST TRANSAKSI
    // ============================
    public function index(Request $request, $tanggal = null)
{
    $query = Transaksi::with('siswa');

    // Tangkap tanggal dari URL parameter atau request biasa
    $tgl = $tanggal ?? $request->tanggal;

    if ($tgl) {
        $query->whereDate('tanggal', $tgl);
    }

    $data = $query->latest()->get();
    return view('admin.transaksi.index', compact('data'));
}

    // ============================
    // TOPUP
    // ============================
    public function topupForm()
    {
        $siswa = Siswa::all();
        return view('admin.transaksi.topup', compact('siswa'));
    }

    public function topupSubmit(Request $request)
    {
        $request->validate([
            'siswa_id'   => 'required|exists:siswa,id',
            'jumlah'     => 'required|numeric|min:1000',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, &$transaksi) {

            $siswa  = Siswa::findOrFail($request->siswa_id);
            $status = $siswa->fingerprint_id ? 'sukses' : 'pending';

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'siswa_id'   => $siswa->id,
                'tanggal'    => $request->tanggal,
                'tipe'       => 'topup',
                'jumlah'     => $request->jumlah,
                'keterangan' => $request->keterangan,
                'status'     => $status,
            ]);

            // Riwayat
            RiwayatTransaksi::create([
                'siswa_id'      => $siswa->id,
                'transaksi_id'  => $transaksi->id,
                'tipe'          => 'topup',
                'nominal'       => $request->jumlah
            ]);

            // Update saldo hanya jika sukses
            if ($status === 'sukses') {
                $siswa->increment('saldo', $request->jumlah);
            }
        });

        return $transaksi->status === 'sukses'
            ? redirect()->route('transaksi.index')->with('success', 'Topup berhasil!')
            : redirect()->route('transaksi.scan', $transaksi->id);
    }

    // ============================
    // PEMBELIAN
    // ============================
    public function pembelianForm()
    {
        $siswa = Siswa::all();
        return view('admin.transaksi.pembelian', compact('siswa'));
    }

    public function pembelianSubmit(Request $request)
    {
        $request->validate([
            'siswa_id'   => 'required|exists:siswa,id',
            'jumlah'     => 'required|numeric|min:1000',
            'tanggal'    => 'required|date', // Tambahkan validasi tanggal
            'keterangan' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, &$transaksi) {

            $siswa  = Siswa::findOrFail($request->siswa_id);
            $status = $siswa->fingerprint_id ? 'sukses' : 'pending';

            // Cegah saldo minus
            if ($status === 'sukses' && $siswa->saldo < $request->jumlah) {
                abort(400, 'Saldo tidak mencukupi!');
            }

            $transaksi = Transaksi::create([
                'siswa_id'   => $siswa->id,
                'tanggal'    => $request->tanggal, // Gunakan input tanggal dari form, bukan now()
                'tipe'       => 'pembelian',
                'jumlah'     => $request->jumlah,
                'keterangan' => $request->keterangan,
                'status'     => $status,
            ]);

            RiwayatTransaksi::create([
                'siswa_id'     => $siswa->id,
                'transaksi_id' => $transaksi->id,
                'tipe'         => 'payment',
                'nominal'      => $request->jumlah
            ]);

            if ($status === 'sukses') {
                $siswa->decrement('saldo', $request->jumlah);
            }
        });

        return $transaksi->status === 'sukses'
            ? redirect()->route('transaksi.index')->with('success', 'Pembelian berhasil!')
            : redirect()->route('transaksi.scan', $transaksi->id);
    }
    // ============================
    // SCAN & VERIFIKASI FINGERPRINT
    // ============================
    public function scanFingerprint($id)
    {
        $transaksi = Transaksi::with('siswa')->findOrFail($id);
        return view('admin.transaksi.scan', compact('transaksi'));
    }

    public function verifyFingerprint(Request $request, $id)
    {
        $request->validate([
            'finger_id' => 'required'
        ]);

        DB::transaction(function () use ($request, $id) {

            $transaksi = Transaksi::findOrFail($id);

            if ($transaksi->status === 'sukses') {
                abort(400, 'Transaksi sudah diverifikasi!');
            }

            $siswa = Siswa::where('fingerprint_id', $request->finger_id)->first();

            if (!$siswa || $siswa->id !== $transaksi->siswa_id) {
                abort(403, 'Fingerprint tidak cocok!');
            }

            $transaksi->update(['status' => 'sukses']);

            if ($transaksi->tipe === 'topup') {
                $siswa->increment('saldo', $transaksi->jumlah);
            } else {
                if ($siswa->saldo < $transaksi->jumlah) {
                    abort(400, 'Saldo tidak mencukupi!');
                }
                $siswa->decrement('saldo', $transaksi->jumlah);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diverifikasi!');
    }

    // ============================
    // EDIT & UPDATE
    // ============================
    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $siswa = Siswa::all();

        return view('admin.transaksi.edit', compact('transaksi', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id'   => 'required|exists:siswa,id',
            'jumlah'     => 'required|numeric|min:1000',
            'tanggal'    => 'required|date',
            'tipe'       => 'required|in:topup,pembelian'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($request->only('siswa_id', 'jumlah', 'tanggal', 'tipe', 'keterangan'));

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    // ============================
    // DELETE + PERBAIKI SALDO
    // ============================
    public function delete($id)
    {
        DB::transaction(function () use ($id) {

            $transaksi = Transaksi::findOrFail($id);
            $siswa = Siswa::findOrFail($transaksi->siswa_id);

            if ($transaksi->status === 'sukses') {
                if ($transaksi->tipe === 'topup') {
                    $siswa->decrement('saldo', $transaksi->jumlah);
                } else {
                    $siswa->increment('saldo', $transaksi->jumlah);
                }
            }

            RiwayatTransaksi::where('transaksi_id', $transaksi->id)->delete();
            $transaksi->delete();
        });

        return back()->with('success', 'Transaksi dihapus & saldo diperbaiki!');
    }
}
