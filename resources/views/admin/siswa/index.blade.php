@extends('admin.layouts.app', [
'activePage' => 'siswa',
])
@section('content')
<div class="min-height-200px">
   <div class="page-header">
      <div class="row">
         <div class="col-md-6 col-sm-12">
            <div class="title">
               <h4>Data Siswa</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Data Input</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Data Siswa</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>

   <div class="pd-20 card-box mb-30">
      <div class="clearfix">
         <div class="pull-left">
            <h2 class="text-primary h2"><i class="icon-copy dw dw-list"></i> List Data Siswa</h2>
         </div>
         <div class="pull-right">
            <a href="/admin/siswa/add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah Data</a>
         </div>
      </div>
      <hr>

      @if (session('error'))
      <div class="alert alert-danger">
         {{ session('error') }}
         <button type="button" class="close" data-dismiss="alert">×</button>
      </div>
      @endif

      @if (session('success'))
      <div class="alert alert-success">
         {{ session('success') }}
         <button type="button" class="close" data-dismiss="alert">×</button>
      </div>
      @endif

      <table class="table table-striped table-bordered data-table hover">
         <thead class="bg-primary text-white">
            <tr>
               <th width="5%">No</th>
               <th>NIS</th>
               <th>Nama</th>
               <th>Contact</th>
               <th>Alamat</th>
               <th>Foto</th>
               <th>Status</th>
               <th>Saldo</th> 
               <th class="text-center">Action</th>
            </tr>
         </thead>
         <tbody>
            <?php $no = 1; ?>
            @foreach($siswa as $data)
            <tr>
               <td class="text-center">{{$no++}}</td>
               <td>{{$data->nis}}</td>
               <td>{{$data->nama}}</td>
               <td>{{$data->contact}}</td>
               <td>{{$data->alamat}}</td>

               <td>
               @if($data->foto && file_exists(public_path('foto_siswa/'.$data->foto)))
               <img src="{{ asset('foto_siswa/'.$data->foto) }}" width="50" style="border-radius:5px;">
               @else
               <img src="{{ asset('foto_siswa/default.png') }}" width="50" style="border-radius:5px;">
               @endif
               </td>

               <td>
                 @if($data->status === 'Terdaftar')
               <span class="badge badge-success" style="font-size:14px;">Terdaftar</span>
               @else
               <span class="badge badge-danger" style="font-size:14px;">Belum</span>
               @endif

               </td>

               <td>
                  <span class="badge badge-success" style="font-size:14px;">
                     Rp {{ number_format($data->saldo, 0, ',', '.') }}
                  </span>
               </td>

               <td class="text-center" width="18%">
                  <a href="/admin/siswa/riwayat/{{ $data->id }}" class="btn btn-info btn-xs">
                     <i class="fa fa-history"></i>
                  </a>

                  <a href="/admin/siswa/edit/{{$data->id}}" class="btn btn-success btn-xs">
                     <i class="fa fa-edit"></i>
                  </a>

                  <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#data-{{$data->id}}">
                     <i class="fa fa-trash"></i>
                  </button>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>

@foreach($siswa as $data)
<div class="modal fade" id="data-{{$data->id}}">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-body">
            <h2 class="text-center">Apakah Anda Yakin Menghapus Data Ini?</h2>
            <hr>

            <div class="form-group" style="font-size: 17px;">
               <label>Nama</label>
               <input class="form-control" value="{{$data->nama}}" readonly>
            </div>

            <div class="row mt-4">
               <div class="col-md-6">
                  <form action="{{ route('admin.siswa.delete', $data->id) }}" method="POST">
                     @csrf
                     @method('DELETE')
                     <button type="submit" class="btn btn-primary btn-block">
                        Ya
                     </button>
                  </form>
               </div>

               <div class="col-md-6">
                  <button class="btn btn-danger btn-block" data-dismiss="modal">Tidak</button>
               </div>
            </div>

         </div>
      </div>
   </div>
</div>
@endforeach

@endsection
