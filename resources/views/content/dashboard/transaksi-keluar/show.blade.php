@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Transaksi Keluar')

@section('content')

    <div class="card border-0 shadow-sm">

        {{-- HEADER --}}
        <div class="card-header bg-white py-3">
            <h4 class="mb-0 fw-bold text-primary">
                Detail Transaksi Keluar
            </h4>
        </div>

        <div class="card-body">

            {{-- ROW ATAS --}}
            <div class="row g-4">

                {{-- INFORMASI BARANG --}}
                <div class="col-lg-7">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-header bg-light fw-bold">
                            Informasi Barang
                        </div>

                        <div class="card-body p-0">

                            <table class="table table-bordered align-middle mb-0">

                                <tr>
                                    <th width="35%">Kode Keluar</th>
                                    <td>{{ $keluar->kode_keluar }}</td>
                                </tr>

                                <tr>
                                    <th>Kode Masuk</th>
                                    <td>{{ $keluar->masuk?->kode_masuk ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $keluar->masuk?->kategori?->nama_barang ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Type</th>
                                    <td>{{ $keluar->masuk?->type ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $keluar->masuk?->merek ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Tanggal Beli</th>
                                    <td>
                                        {{ $keluar->masuk?->tgl_beli ? \Carbon\Carbon::parse($keluar->masuk->tgl_beli)->format('d-m-Y') : '-' }}
                                    </td>
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
                                    <th>Tanggal Keluar</th>
                                    <td>
                                        {{ $keluar->tgl_keluar ? \Carbon\Carbon::parse($keluar->tgl_keluar)->format('d-m-Y') : '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $keluar->keterangan }}</td>
                                </tr>

                            </table>

                        </div>

                    </div>

                </div>

                {{-- FOTO --}}
                <div class="col-lg-5">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-header bg-light fw-bold">
                            Foto Barang
                        </div>

                        <div class="card-body text-center">

                            @if ($keluar->gambar)
                                <img src="{{ asset('storage/' . $keluar->gambar) }}"
                                    class="img-fluid rounded shadow-sm border mb-3"
                                    style="max-height: 420px; object-fit: cover;">

                                <div>
                                    <a href="{{ asset('storage/' . $keluar->gambar) }}" download class="btn btn-primary">

                                        <i class="bx bx-download"></i>
                                        Download Gambar

                                    </a>
                                </div>
                            @elseif ($keluar->masuk?->gambar)
                                <img src="{{ asset('storage/' . $keluar->masuk->gambar) }}"
                                    class="img-fluid rounded shadow-sm border mb-3"
                                    style="max-height: 420px; object-fit: cover;">

                                <div>
                                    <a href="{{ asset('storage/' . $keluar->masuk->gambar) }}" download
                                        class="btn btn-primary">

                                        <i class="bx bx-download"></i>
                                        Download Gambar

                                    </a>
                                </div>
                            @else
                                <div class="text-muted py-5">

                                    <i class="bx bx-image-alt display-4"></i>

                                    <p class="mt-3 mb-0">
                                        Gambar tidak tersedia
                                    </p>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

            {{-- INFORMASI PENERIMA --}}
            <div class="row mt-4">

                <div class="col-lg-7">

                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-light fw-bold">
                            Informasi Penerima
                        </div>

                        <div class="card-body p-0">

                            <table class="table table-bordered align-middle mb-0">

                                @if ($keluar->jenis_penerima == 'Perorangan')
                                    <tr>
                                        <th width="35%">Nama Karyawan</th>
                                        <td>{{ $keluar->karyawan?->nama_karyawan ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Divisi</th>
                                        <td>{{ $keluar->karyawan?->divisi ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Perusahaan</th>
                                        <td>{{ $keluar->karyawan?->perusahaan?->nama_perusahaan ?? '-' }}</td>
                                    </tr>
                                @elseif($keluar->jenis_penerima == 'Perdivisi')
                                    <tr>
                                        <th width="35%">Divisi</th>
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

            {{-- BUTTON --}}
            <div class="mt-4">

                @if (auth()->user()->role === 'manager')
                    <a href="{{ route('manager.laporan.keluar') }}" class="btn btn-secondary px-4">

                        <i class="bx bx-arrow-back"></i>
                        Kembali

                    </a>
                @else
                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary px-4">

                        <i class="bx bx-arrow-back"></i>
                        Kembali

                    </a>
                @endif

            </div>

        </div>

    </div>

@endsection
