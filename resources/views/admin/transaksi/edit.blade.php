@extends('admin.layouts.app', [
'activePage' => 'transaksi',
])

@section('content')
<div class="min-height-200px">
   <div class="page-header">
      <div class="row">
         <div class="col-md-6 col-sm-12">
            <div class="title">
               <h4>Edit Transaksi</h4>
            </div>
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

      <form action="/admin/transaksi/update/{{$transaksi->id}}" method="POST">
         @csrf
         <div class="form-group">
            <label>Pilih Siswa</label>
            <select name="siswa_id" class="form-control" required>
               @foreach($siswa as $s)
                  <option value="{{ $s->id }}" {{ $transaksi->siswa_id == $s->id ? 'selected' : '' }}>
                     {{ $s->nama }} (Saldo: Rp {{ number_format($s->saldo,0,',','.') }})
                  </option>
               @endforeach
            </select>
         </div>

         <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $transaksi->tanggal }}" required>
         </div>

         <div class="form-group">
            <label>Tipe</label>
            <select name="tipe" class="form-control" required>
               <option value="topup" {{ $transaksi->tipe=='topup'?'selected':'' }}>Topup</option>
               <option value="pembelian" {{ $transaksi->tipe=='pembelian'?'selected':'' }}>Pembelian</option>
            </select>
         </div>

         <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $transaksi->jumlah }}" required>
         </div>

         <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ $transaksi->keterangan }}">
         </div>

         <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
               <option value="pending" {{ $transaksi->status=='pending'?'selected':'' }}>Pending</option>
               <option value="sukses" {{ $transaksi->status=='sukses'?'selected':'' }}>Sukses</option>
            </select>
         </div>

         <button type="submit" class="btn btn-success">Update Transaksi</button>
      </form>
   </div>
</div>
@endsection
