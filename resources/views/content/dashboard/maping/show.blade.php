@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Data Maping')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">Detail Maping </h5>
    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- INFO BARANG --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-bold">
                        Informasi Barang
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th>Kode Barang</th>
                                <td>{{ $maping->keluar->kode_barang }}</td>
                            </tr>
                            
                            <tr>
                                <th>Nama Barang</th>
                                <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>{{ $maping->keluar->masuk->type?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Merek</th>
                                <td>{{ $maping->keluar->masuk->merek?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Warna</th>
                                <td>{{ $maping->keluar->warna?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Garansi</th>
                                <td>{{ $maping->keluar->masuk->garansi?? '-' }} Bulan</td>
                            </tr>
                             <tr>
                                <th>No Inventaris</th>
                                <td>{{ $maping->keluar->no_inventaris?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Tanggal Beli</th>
                                <td>{{ $maping->keluar->masuk->tgl_beli?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Nama Karyawan</th>
                                <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                            </tr>
            
                            <tr>
                                <th>Perusahaan</th>
                                <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Processor</th>
                                <td>{{ $maping->processor ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Device ID</th>
                                <td>{{ $maping->device_id ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Produk ID</th>
                                <td>{{ $maping->produk_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>RAM</th>
                                <td>{{ $maping->ram ?? '-' }} GB</td>
                            </tr>
                             <tr>
                                <th>System</th>
                                <td>{{ $maping->system ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>version</th>
                                <td>{{ $maping->version ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Instal On</th>
                                <td>{{ $maping->instal_on ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Aplikasi</th>
                                <td>{{ $maping->aplikasi ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Hak Akses Data PPN</th>
                                <td>{{ $maping->data_p ?? '-' }}</td>
                            </tr>
                             <tr>
                                <th>Hak Akses Data Non PPN</th>
                                <td>{{ $maping->data_n ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            


{{-- TOMBOL --}}
            <div class="mt-4">
                @auth
                    @if (auth()->user()->role === 'manager')
                        <a href="{{ route('manager.maping') }}" class="btn btn-secondary px-4">
                            ← Kembali
                        </a>
                    @else
                        <a href="{{ route('maping.index') }}" class="btn btn-secondary px-4">
                            ← Kembali
                        </a>
                    @endif
                @endauth
            </div>

        </div>
    </div>


@endsection
