@extends('admin.layouts.app', [
    'activePage' => 'transaksi',
])

@section('content')
<div class="min-height-200px">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4>Scan Fingerprint</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
                        <li class="breadcrumb-item"><a href="/admin/transaksi">Data Transaksi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Scan Fingerprint</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h2 class="text-primary h2">
                    <i class="icon-copy dw dw-fingerprint"></i> Verifikasi Transaksi
                </h2>
            </div>
            <div class="pull-right">
                <a href="/admin/transaksi" class="btn btn-primary btn-sm">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <hr style="margin-top: 0px">

        <div class="alert alert-info border-radius-7">
            <i class="fa fa-info-circle"></i> Silahkan scan fingerprint siswa untuk melanjutkan konfirmasi transaksi ini.
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Nama Siswa</th>
                            <td>{{ $transaksi->siswa->nama }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Transaksi</th>
                            <td>{{ ucfirst($transaksi->tipe) }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td class="font-weight-bold text-success">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $transaksi->keterangan ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <form method="POST" action="/admin/transaksi/verify/{{ $transaksi->id }}">
            @csrf
            <div class="form-group">
                <label>ID Fingerprint (dari Arduino)<span class="text-danger">*</span></label>
                <input type="number" name="finger_id" class="form-control" placeholder="Masukkan ID fingerprint..." required autofocus>
                <small class="form-text text-muted">Pastikan alat scanner sudah siap menerima input.</small>
            </div>

            <button type="submit" class="btn btn-success mt-1 mr-2">
                <span class="icon-copy ti-check-box"></span> Scan & Verifikasi
            </button>
        </form>
    </div>
</div>
@endsection