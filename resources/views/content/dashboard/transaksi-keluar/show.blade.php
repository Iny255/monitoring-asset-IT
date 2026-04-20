@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Transaksi Keluar')

@section('content')

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold text-primary">Detail Transaksi Keluar</h5>
        </div>

        <div class="card-body">

            {{-- BARANG & FOTO --}}
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
                                    <th width="40%">Kode Keluar</th>
                                    <td>{{ $keluar->kode_keluar }}</td>
                                </tr>
                                <tr>
                                    <th>Kode Masuk</th>
                                    <td>{{ $keluar->masuk->kode_masuk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ $keluar->masuk->type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $keluar->masuk->merek ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Beli</th>
                                    {{ \Carbon\Carbon::parse(optional($keluar->masuk)->tgl_beli)->format('d-m-Y') ?? '-' }}
                                </tr>
                                <tr>
                                    <th>Kode Barang</th>
                                    <td>{{ $keluar->kode_barang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Warna</th>
                                    <td>{{ $keluar->warna ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No Inventaris</th>
                                    <td>{{ $keluar->no_inventaris ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Keluar</th>
                                    <td>{{ $keluar->jumlah }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $keluar->keterangan }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- FOTO BARANG --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-header bg-light fw-bold">
                            Foto Barang
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            @if ($keluar->masuk && $keluar->masuk->gambar)
                                <img src="{{ asset('storage/' . $keluar->masuk->gambar) }}"
                                    class="img-fluid rounded shadow" style="max-height: 280px;">
                            @else
                                <div class="text-muted">
                                    <i class="bx bx-image-alt bx-lg mb-2"></i>
                                    <p class="mb-0">Gambar tidak tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- INFO PENERIMA --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light fw-bold">
                            Informasi Penerima
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">

                                {{-- PERORANGAN --}}
                                @if ($keluar->jenis_penerima == 'Perorangan')
                                    <tr>
                                        <th width="40%">Nama Karyawan</th>
                                        <td>{{ optional($keluar->karyawan)->nama_karyawan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Divisi</th>
                                        <td>{{ optional($keluar->karyawan)->divisi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Perusahaan</th>
                                        <td>{{ optional($keluar->karyawan)->perusahaan ?? '-' }}</td>
                                    </tr>

                                    {{-- PERDIVISI --}}
                                @elseif($keluar->jenis_penerima == 'Perdivisi')
                                    <tr>
                                        <th width="40%">Divisi</th>
                                        <td>{{ $keluar->divisi_klr ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Perusahaan</th>
                                        <td>{{ $keluar->perusahaan_klr ?? '-' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            Data penerima tidak tersedia
                                        </td>
                                    </tr>
                                @endif

                            </table>
                        </div>
                    </div>
                </div>
            </div>


            {{-- TOMBOL --}}
            <div class="mt-4">
                @auth
                    @if (auth()->user()->role === 'manager')
                        <a href="{{ route('manager.laporan.keluar') }}" class="btn btn-secondary px-4">
                            ← Kembali
                        </a>
                    @else
                        <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary px-4">
                            ← Kembali
                        </a>
                    @endif
                @endauth
            </div>

        </div>
    </div>


@endsection
