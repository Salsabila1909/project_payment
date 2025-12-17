@extends('admin.layouts.app', [
    'activePage' => 'siswa',
])

@section('content')
<div class="min-height-200px">

    <div class="page-header mb-3">
        <h4>Riwayat Transaksi Siswa</h4>
    </div>
    
    <div class="row">

        <!-- ============================== -->
        <!--   PROFILE SISWA (KIRI)        -->
        <!-- ============================== -->
        <div class="col-md-4">
            <div class="card card-box p-3 shadow">

                <div class="text-center mb-3">

                    <img 
                        src="{{ 
                            $siswa->foto && file_exists(public_path('foto_siswa/'.$siswa->foto)) 
                            ? asset('foto_siswa/'.$siswa->foto) 
                            : asset('foto_siswa/default.png') 
                        }}" 
                        class="rounded-circle" 
                        width="100"
                    >

                    <h5 class="mt-2 mb-0">{{ $siswa->nama }}</h5>
                    <small class="text-muted">NIS: {{ $siswa->nis }}</small>

                    <!-- SALDO DITAMPILKAN DI BAWAH NIS -->
                    <div class="mt-2">
                        <strong>Saldo:</strong><br>
                        <span class="badge badge-success" style="font-size:14px;">
                            Rp {{ number_format($siswa->saldo) }}
                        </span>
                    </div>
                </div>

                <hr>

                <p>
                    <strong>Contact:</strong><br>
                    {{ $siswa->contact ?? '-' }}
                </p>

                <p>
                    <strong>Alamat:</strong><br>
                    {{ $siswa->alamat ?? '-' }}
                </p>

                <a href="{{ route('admin.siswa.read') }}" 
                   class="btn btn-secondary btn-block mt-3">
                    Kembali
                </a>
            </div>
        </div>

        <!-- ============================== -->
        <!--   TABEL RIWAYAT TRANSAKSI     -->
        <!-- ============================== -->
        <div class="col-md-8">
            <div class="card card-box p-3 shadow">

                <h5 class="mb-3">Daftar Riwayat Transaksi</h5>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($riwayat as $r)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <!-- FORMAT TANGGAL MENJADI dd-mm-yyyy -->
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>

                            <td>
                                @if($r->tipe == 'topup')
                                    <span class="badge badge-success">Topup</span>
                                @else
                                    <span class="badge badge-danger">Pembelian</span>
                                @endif
                            </td>

                            <td>Rp {{ number_format($r->jumlah) }}</td>

                            <td>
                                @if($r->status == 'sukses')
                                    <span class="badge badge-primary">Sukses</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>

                            <td>{{ $r->keterangan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div><!-- end row -->

</div>
@endsection
