@extends('admin.layouts.app', [
'activePage' => 'transaksi',
])

@section('content')
<div class="min-height-200px">
   <div class="page-header">
      <div class="row">
         <div class="col-md-6 col-sm-12">
            <div class="title">
               <h4>Data Transaksi</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Data Input</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>

   <div class="pd-20 card-box mb-30">
      <div class="clearfix">
         <div class="pull-left">
            <h2 class="text-primary h2"><i class="icon-copy dw dw-list"></i> List Transaksi</h2>
         </div>
         <div class="pull-right">
            <a href="{{ route('transaksi.topupForm') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Topup</a>
            <a href="{{ route('transaksi.pembelianForm') }}" class="btn btn-success btn-sm"><i class="fa fa-shopping-cart"></i> Payment</a>
         </div>
      </div>
      <hr>

      <div class="row mb-4">
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Filter Tanggal:</label>
                    <input 
                        type="date" 
                        name="tgl" 
                        required 
                        class="form-control" 
                        value="{{ request('tanggal') ?? (isset($tanggal) ? $tanggal : date('Y-m-d')) }}" 
                        max="{{ date('Y-m-d') }}"
                        onchange="location = '/admin/transaksi/filter/'+this.value;"
                    >
                </div>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-block">
                    <i class="fa fa-refresh"></i> Reset
                </a>
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
               <th>Tanggal</th>
               <th>Siswa</th>
               <th>Tipe</th>
               <th>Jumlah</th>
               <th>Status</th>
               <th>Keterangan</th>
               <th class="text-center">Action</th>
            </tr>
         </thead>
         <tbody>
            <?php $no = 1; ?>
            @foreach($data as $transaksi)
            <tr>
               <td class="text-center">{{$no++}}</td>
               <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') }}</td>
               <td>{{$transaksi->siswa->nama}}</td>
               <td>{{ ucfirst($transaksi->tipe) }}</td>
               <td>Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>

               <!-- STATUS -->
               <td>
                  @if($transaksi->status == 'pending')
                     <span class="badge badge-warning" style="font-size:14px;">Pending</span>
                  @else
                     <span class="badge badge-success" style="font-size:14px;">Sukses</span>
                  @endif
               </td>

               <td>{{ $transaksi->keterangan ?? '-' }}</td>

               <td class="text-center" width="18%">
                 @if($transaksi->status === 'pending')
                     <a href="{{ route('transaksi.scan', $transaksi->id) }}"
                        class="btn btn-warning btn-xs"
                        title="Scan Fingerprint">
                        <i class="fa fa-spinner fa-spin"></i>
                     </a>
                  @else
                     <button class="btn btn-secondary btn-xs"
                        title="Sudah Terdaftar" disabled>
                        <i class="fa fa-check-circle"></i>
                     </button>
                  @endif
                  <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="btn btn-success btn-xs">
                     <i class="fa fa-edit"></i>
                  </a>

                  <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#data-{{$transaksi->id}}">
                     <i class="fa fa-trash"></i>
                  </button>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>

<!-- Modal Hapus -->
@foreach($data as $transaksi)
<div class="modal fade" id="data-{{$transaksi->id}}">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-body">
            <h2 class="text-center">Apakah Anda Yakin Menghapus Data Ini?</h2>
            <hr>

            <div class="form-group" style="font-size: 17px;">
               <label>Siswa</label>
               <input class="form-control" value="{{$transaksi->siswa->nama}}" readonly>
            </div>

            <div class="row mt-4">
               <div class="col-md-6">
                  <form action="{{ route('transaksi.delete', $transaksi->id) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-primary btn-block">Ya</button>
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
