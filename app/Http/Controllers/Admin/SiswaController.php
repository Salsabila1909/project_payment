<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Transaksi;

class SiswaController extends Controller
{
    /* ===============================
     * READ DATA
     * =============================== */
    public function read()
    {
        $siswa = Siswa::orderBy('id', 'DESC')->get();
        return view('admin.siswa.index', compact('siswa'));
    }

    public function add()
    {
        return view('admin.siswa.tambah');
    }

    /* ===============================
     * CREATE DATA (ADMIN)
     * =============================== */
    public function create(Request $r)
    {
        $r->validate([
            'nis'  => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|file|max:4096'
        ]);

        $foto = null;
        if ($r->hasFile('foto')) {
            $namaFile = time().'_'.$r->foto->getClientOriginalName();
            $r->foto->move(public_path('foto_siswa'), $namaFile);
            $foto = $namaFile;
        }

        Siswa::create([
            'nis'            => $r->nis,
            'nama'           => $r->nama,
            'contact'        => $r->contact,
            'alamat'         => $r->alamat,
            'foto'           => $foto,
            'saldo'          => $r->saldo ?? 0,
            'fingerprint_id' => null,               // WAJIB NULL
            'status'         => 'Belum Terdaftar',  // ENUM BENAR
        ]);

        return redirect()
            ->route('admin.siswa.read')
            ->with('success', 'Data siswa berhasil ditambahkan');
    }

    /* ===============================
     * EDIT DATA (ADMIN)
     * =============================== */
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    /* ===============================
     * UPDATE DATA (ADMIN)
     * =============================== */
    public function update(Request $r, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $r->validate([
            'nis'  => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|file|max:4096'
        ]);

        $foto = $siswa->foto;

        if ($r->hasFile('foto')) {
            if ($foto && file_exists(public_path('foto_siswa/'.$foto))) {
                unlink(public_path('foto_siswa/'.$foto));
            }

            $namaFile = time().'_'.$r->foto->getClientOriginalName();
            $r->foto->move(public_path('foto_siswa'), $namaFile);
            $foto = $namaFile;
        }

        $siswa->update([
            'nis'     => $r->nis,
            'nama'    => $r->nama,
            'contact' => $r->contact,
            'alamat'  => $r->alamat,
            'foto'    => $foto,
            'saldo'   => $r->saldo,
            // fingerprint_id & status TIDAK BOLEH DIUBAH ADMIN
        ]);

        return redirect()
            ->route('admin.siswa.read')
            ->with('success', 'Data siswa berhasil diupdate');
    }

    /* ===============================
     * DELETE DATA
     * =============================== */
    public function delete($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto && file_exists(public_path('foto_siswa/'.$siswa->foto))) {
            unlink(public_path('foto_siswa/'.$siswa->foto));
        }

        $siswa->delete();

        return back()->with('success', 'Data siswa berhasil dihapus');
    }

    /* ===============================
     * DETAIL & RIWAYAT
     * =============================== */
    public function riwayat($id)
    {
        $siswa = Siswa::findOrFail($id);
        $riwayat = Transaksi::where('siswa_id', $id)
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('admin.siswa.riwayat', compact('siswa', 'riwayat'));
    }

    /* ===============================
     * TOPUP SALDO
     * =============================== */
    public function topup(Request $r, $id)
    {
        $r->validate(['nominal' => 'required|numeric|min:1']);

        $siswa = Siswa::findOrFail($id);
        $siswa->saldo += $r->nominal;
        $siswa->save();

        Transaksi::create([
            'siswa_id'  => $id,
            'tanggal'   => now()->format('Y-m-d'),
            'tipe'      => 'topup',
            'jumlah'    => $r->nominal,
            'keterangan'=> 'Topup Manual',
            'status'    => 'sukses'
        ]);

        return back()->with('success', 'Topup berhasil');
    }

    /* ===============================
     * PEMBAYARAN MANUAL
     * =============================== */
    public function payment(Request $r, $id)
    {
        $r->validate(['nominal' => 'required|numeric|min:1']);

        $siswa = Siswa::findOrFail($id);

        if ($siswa->saldo < $r->nominal) {
            return back()->with('error', 'Saldo tidak cukup');
        }

        $siswa->saldo -= $r->nominal;
        $siswa->save();

        Transaksi::create([
            'siswa_id'  => $id,
            'tanggal'   => now()->format('Y-m-d'),
            'tipe'      => 'pembelian',
            'jumlah'    => $r->nominal,
            'keterangan'=> 'Pembayaran Manual',
            'status'    => 'sukses'
        ]);

        return back()->with('success', 'Pembayaran berhasil');
    }
}
