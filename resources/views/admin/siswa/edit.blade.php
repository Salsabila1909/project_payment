@extends('admin.layouts.app', [
'activePage' => 'siswa',
])
@section('content')
<div class="min-height-200px">
   <div class="page-header">
      <div class="row">
         <div class="col-md-12 col-sm-12">
            <div class="title">
               <h4>Data Siswa</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Data Input</a></li>
                  <li class="breadcrumb-item"><a href="/admin/siswa">Data Siswa</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Edit Data Siswa</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>

   <div class="pd-20 card-box mb-30">
      <div class="clearfix">
         <div class="pull-left">
            <h2 class="text-primary h2">
               <i class="icon-copy dw dw-edit-file"></i> Edit Data Siswa
            </h2>
         </div>
         <div class="pull-right">
            <a href="/admin/siswa" class="btn btn-primary btn-sm">
               <i class="fa fa-arrow-left"></i> Back
            </a>
         </div>
      </div>
      <hr style="margin-top: 0px">

      <form action="/admin/siswa/update/{{ $siswa->id }}" method="POST" enctype="multipart/form-data">
         @csrf

         <div class="form-group">
            <label>NIS<span class="text-danger">*</span></label>
            <input type="text" name="nis" value="{{ $siswa->nis }}" required class="form-control" placeholder="Masukkan NIS .....">
         </div>

         <div class="form-group">
            <label>Nama Siswa<span class="text-danger">*</span></label>
            <input type="text" name="nama" value="{{ $siswa->nama }}" required class="form-control" placeholder="Masukkan Nama Siswa .....">
         </div>

         <div class="form-group">
            <label>Contact (Opsional)</label>
            <input type="text" name="contact" value="{{ $siswa->contact }}" class="form-control" placeholder="Masukkan Nomor Kontak .....">
         </div>

         <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat" value="{{ $siswa->alamat }}" class="form-control" placeholder="Masukkan Alamat .....">
         </div>

         <div class="form-group">
            <label>Foto</label><br>
            @php
               $path = public_path('foto_siswa/'.$siswa->foto);
            @endphp

            @if($siswa->foto && file_exists($path))
               <img src="{{ asset('foto_siswa/'.$siswa->foto) }}" width="70" class="mb-2">
            @else
               <img src="{{ asset('foto_siswa/default.png') }}" width="70" class="mb-2">
            @endif

            <input type="file" name="foto" class="form-control mt-2">
            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
         </div>

         <div class="form-group">
            <label>Saldo</label>
            <input type="number" name="saldo" value="{{ $siswa->saldo }}" class="form-control">
         </div>
         <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
               <option value="belum" {{ $siswa->status == 'belum' ? 'selected' : '' }}>
               Belum Terdaftar
               </option>
               <option value="terdaftar" {{ $siswa->status == 'terdaftar' ? 'selected' : '' }}>
                  Terdaftar
               </option>
            </select>
         </div>

         <button type="submit" class="btn btn-primary mt-1 mr-2">
            <span class="icon-copy ti-save"></span> Update Data
         </button>
      </form>
   </div>
</div>
@endsection
