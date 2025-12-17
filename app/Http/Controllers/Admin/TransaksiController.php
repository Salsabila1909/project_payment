<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Siswa;
use App\Models\RiwayatTransaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $data = Transaksi::with('siswa')->orderBy('id', 'DESC')->get();
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
        $siswa = Siswa::findOrFail($request->siswa_id);
        $status = $siswa->fingerprint_id ? 'sukses' : 'pending';

        // Insert Transaksi
        $transaksi = Transaksi::create([
            'siswa_id'   => $siswa->id,
            'tanggal'    => $request->tanggal,       // ← BERUBAH: dari now() ke input form
            'tipe'       => 'topup',
            'jumlah'     => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status'     => $status,
        ]);

        // Tambah ke Riwayat
        RiwayatTransaksi::create([
            'siswa_id' => $siswa->id,
            'tipe'     => 'topup',
            'nominal'  => $request->jumlah
        ]);

        // Jika fingerprint sudah terdaftar — langsung tambahkan saldo
        if ($status === 'sukses') {
            $siswa->saldo += $request->jumlah;
            $siswa->save();

            return redirect()->route('transaksi.index')
                ->with('success', 'Topup berhasil!');
        }

        return redirect()->route('transaksi.scan', $transaksi->id);
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
        $siswa = Siswa::findOrFail($request->siswa_id);
        $status = $siswa->fingerprint_id ? 'sukses' : 'pending';

        // Insert Transaksi Pembelian
        $transaksi = Transaksi::create([
            'siswa_id'   => $siswa->id,
            'tanggal' => now(),
            'tipe'       => 'pembelian',
            'jumlah'     => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status'     => $status,
        ]);

        // Insert ke Riwayat
        RiwayatTransaksi::create([
            'siswa_id' => $siswa->id,
            'tipe'     => 'payment',
            'nominal'  => $request->jumlah
        ]);

        // Jika fingerprint cocok — langsung potong saldo
        if ($status === 'sukses') {
            $siswa->saldo -= $request->jumlah;
            $siswa->save();

            return redirect()->route('transaksi.index')
                ->with('success', 'Pembelian berhasil!');
        }

        return redirect()->route('transaksi.scan', $transaksi->id);
    }

    // ============================
    // SCAN FINGERPRINT
    // ============================
    public function scanFingerprint($id)
    {
        $transaksi = Transaksi::with('siswa')->findOrFail($id);
        return view('admin.transaksi.scan', compact('transaksi'));
    }

    public function verifyFingerprint(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $siswa = Siswa::where('fingerprint_id', $request->finger_id)->first();

        if (!$siswa || $siswa->id != $transaksi->siswa_id) {
            return back()->with('error', 'Fingerprint tidak cocok!');
        }

        // Ubah status → sukses
        $transaksi->update(['status' => 'sukses']);

        // Update saldo sesuai tipe
        if ($transaksi->tipe == 'topup') {
            $siswa->saldo += $transaksi->jumlah;
        } else {
            $siswa->saldo -= $transaksi->jumlah;
        }

        $siswa->save();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diverifikasi!');
    }

    // ============================
    // EDIT & UPDATE TRANSAKSI
    // ============================
    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $siswa = Siswa::all();

        return view('admin.transaksi.edit', compact('transaksi', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update(
            $request->only('siswa_id', 'jumlah', 'keterangan', 'tipe', 'tanggal')
        );

        return redirect()->route('transaksi.index')
            ->with('success', 'Data transaksi berhasil diupdate!');
    }

    // ============================
    // HAPUS TRANSAKSI + PERBAIKI SALDO + HAPUS RIWAYAT
    // ============================
    public function delete($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $siswa = Siswa::findOrFail($transaksi->siswa_id);

        if ($transaksi->status === 'sukses') {

            if ($transaksi->tipe === 'topup') {
                $siswa->saldo -= $transaksi->jumlah;
            } else {
                $siswa->saldo += $transaksi->jumlah;
            }

            $siswa->save();
        }

        // Hapus riwayat transaksi
        RiwayatTransaksi::where('siswa_id', $siswa->id)
            ->where('nominal', $transaksi->jumlah)
            ->delete();

        $transaksi->delete();

        return back()->with('success', 'Transaksi dihapus & saldo telah diperbaiki!');
    }
}
