@extends('admin.layouts.app', [
'activePage' => 'transaksi',
])

@section('content')
<div class="min-height-200px">
   <div class="page-header">
      <div class="row">
         <div class="col-md-6 col-sm-12">
            <div class="title">
               <h4>Top Up Saldo Siswa</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Data Input</a></li>
                    <li class="breadcrumb-item"><a href="/admin/siswa">Data Topup</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Data Topup</li>
                </ol>
            </nav>
         </div>
      </div>
   </div>

   <div class="pd-20 card-box mb-30">
      @if(session('error'))
         <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if(session('success'))
         <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <form action="/admin/transaksi/topup" method="POST">
         @csrf

         <div class="form-group">
            <label>Pilih Siswa</label>
            <select name="siswa_id" class="form-control" required>
               <option value="">-- Pilih Siswa --</option>
               @foreach($siswa as $s)
                  <option value="{{ $s->id }}">{{ $s->nama }} (Saldo: Rp {{ number_format($s->saldo,0,',','.') }})</option>
               @endforeach
            </select>
         </div>

         <div class="form-group">
            <label>Jumlah Topup</label>
            <input type="number" name="jumlah" class="form-control" placeholder="Masukkan jumlah" required>
         </div>
         <div class="form-group">
            <label>Tanggal Topup</label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ date('Y-m-d') }}" required>
            <small class="text-muted">Format: tgl-bln-thn</small>
         </div>
         <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
         </div>

         <button type="submit" class="btn btn-primary">Submit & Scan Fingerprint</button>
      </form>
   </div>
</div>
@endsection
