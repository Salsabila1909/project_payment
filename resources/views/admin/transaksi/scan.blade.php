@extends('admin.layouts.app', ['activePage' => 'transaksi'])

@section('content')
<div class="min-height-200px">
   <div class="page-header">
      <div class="row">
         <div class="col-md-6 col-sm-12">
            <div class="title">
               <h4>Scan Fingerprint Transaksi</h4>
            </div>
         </div>
      </div>
   </div>

   <div class="pd-20 card-box mb-30">
      <h3>Scan Fingerprint Untuk Konfirmasi</h3>

      <div class="alert alert-info">
         Silahkan scan fingerprint siswa untuk melanjutkan transaksi.
      </div>

      <table class="table table-bordered">
         <tr>
            <th>Nama Siswa</th>
            <td>{{ $transaksi->siswa->nama }}</td>
         </tr>
         <tr>
            <th>Tipe Transaksi</th>
            <td>{{ ucfirst($transaksi->tipe) }}</td>
         </tr>
         <tr>
            <th>Jumlah</th>
            <td>Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
         </tr>
         <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') }}</td>
         </tr>
         <tr>
            <th>Keterangan</th>
            <td>{{ $transaksi->keterangan ?? '-' }}</td>
         </tr>
      </table>

      <form method="POST" action="/admin/transaksi/verify/{{ $transaksi->id }}">
         @csrf

         <div class="form-group mt-3">
            <label>ID Fingerprint (dari Arduino)</label>
            <input type="number" name="finger_id" class="form-control" placeholder="Masukkan ID fingerprint" required>
         </div>

         <button type="submit" class="btn btn-success btn-lg mt-3">
            Scan & Verifikasi Fingerprint
         </button>
      </form>
   </div>
</div>
@endsection
