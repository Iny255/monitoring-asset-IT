@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Transaksi Masuk')

@section('content')

<div class="container-fluid px-3">
    <div class="row">
        <div class="col-12">

            <div class="card shadow-sm rounded-4 border-0">
                
                {{-- HEADER --}}
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">Detail Transaksi Masuk</h5>
                </div>

                {{-- BODY --}}
                <div class="card-body px-4 py-3">

                    <table class="table table-bordered align-middle">
                        <tr>
                            <th width="220">Kode Masuk</th>
                            <td>{{ $masuk->kode_masuk }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $masuk->kategori->nama_barang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>{{ $masuk->type }}</td>
                        </tr>
                        <tr>
                            <th>Merek</th>
                            <td>{{ $masuk->merek }}</td>
                        </tr>
                         <tr>
                            <th>Merek</th>
                            <td>{{ $masuk->merek }}</td>
                        </tr>
                         <tr>
                            <th>Kondisi</th>
                            <td>{{ $masuk->kondisi }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>{{ $masuk->jumlah }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Beli</th>
                            <td>{{ \Carbon\Carbon::parse($masuk->tgl_beli)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Garansi</th>
                            <td>{{ $masuk->garansi }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>{{ $masuk->supplier }}</td>
                        </tr>
                        <tr>
                            <th>Harga/Satuan</th>
                            <td>Rp. {{ number_format($masuk->harga, 0, ',', '.') }}</td>
                        </tr>
                        
                    </table>

                    {{-- GARIS PEMISAH --}}
                    <hr class="my-4">

                    {{-- FOOTER BUTTON --}}
                    <div class="d-flex justify-content-start">
                        @auth
                            @if (auth()->user()->role === 'manager')
                                <a href="{{ route('manager.laporan.masuk') }}" class="btn btn-secondary px-4">
                                    ← Kembali
                                </a>
                            @else
                                <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary px-4">
                                    ← Kembali
                                </a>
                            @endif
                        @endauth
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
