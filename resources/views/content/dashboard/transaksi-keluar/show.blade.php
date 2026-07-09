@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Pemakaian Aset')

@section('content')

    <style>
        .summary-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            transition: .2s;
        }

        .summary-card:hover {
            transform: translateY(-2px);
        }

        .summary-card .card-body {
            padding: 20px;
        }

        .summary-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        .info-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
        }

        .info-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 700;
        }

        .table-detail th {
            width: 35%;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
        }

        .table-detail td {
            color: #1e293b;
        }

        .asset-image {
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            border-radius: 12px;
        }

        .action-footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .table th {
            background: #f8fafc;
            font-weight: 600;
        }

        .card-header {
            padding: 14px 18px;
        }

        .card-header.fw-bold {
            font-size: 15px;
        }

        .img-fluid.asset-preview {
            max-height: 320px;
            object-fit: contain;
        }
    </style>
    <div class="card border-0 shadow-sm">

        {{-- HEADER --}}
        <div class="card-header text-white py-4"
            style="background: linear-gradient(90deg, var(--theme-primary), var(--theme-secondary));">

            <h3 class="mb-1 fw-bold">
                Detail Pemakaian Aset
            </h3>

            <small>
                {{ $keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}
                -
                {{ $keluar->inventaris->dataAset->merek ?? '-' }}
                -
                {{ $keluar->inventaris->dataAset->type ?? '-' }}
            </small>

        </div>

        <div class="card-body">

            {{-- SUMMARY --}}
            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Kode Aset</small>
                            <h5 class="mb-0 fw-bold">
                                {{ $keluar->inventaris->kode_aset ?? '-' }}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">No Inventaris</small>
                            <h5 class="mb-0 fw-bold">
                                {{ $keluar->inventaris->no_inventaris ?? '-' }}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Status Aset</small>

                            @switch($keluar->inventaris->status)
                                @case('TERSEDIA')
                                    <span class="badge bg-success">
                                        TERSEDIA
                                    </span>
                                @break

                                @case('DIPAKAI')
                                    <span class="badge bg-primary">
                                        DIPAKAI
                                    </span>
                                @break

                                @case('DIPINJAM')
                                    <span class="badge bg-warning">
                                        DIPINJAM
                                    </span>
                                @break

                                @default
                                    <span class="badge bg-danger">
                                        RUSAK
                                    </span>
                            @endswitch

                        </div>
                    </div>
                </div>

            </div>

            {{-- DETAIL --}}
            <div class="row">

                {{-- KIRI --}}
                <div class="col-lg-7">

                    {{-- INFORMASI ASSET --}}
                    <div class="card shadow-sm border-0 mb-4">

                        <div class="card-header bg-light fw-bold">
                            Informasi Aset
                        </div>

                        <div class="card-body p-0">

                            <table class="table table-bordered mb-0">

                                <tr>
                                    <th width="35%">Kode Aset</th>
                                    <td>{{ $keluar->inventaris->kode_aset ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>No Inventaris</th>
                                    <td>{{ $keluar->inventaris->no_inventaris ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $keluar->inventaris->dataAset->merek ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Type</th>
                                    <td>{{ $keluar->inventaris->dataAset->type ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Perusahaan</th>
                                    <td>{{ $keluar->inventaris->perusahaan->nama_perusahaan ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Tanggal Keluar</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($keluar->tgl_keluar)->format('d-m-Y') }}
                                    </td>
                                </tr>

                            </table>

                        </div>

                    </div>

                    {{-- INFORMASI PENERIMA --}}
                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-light fw-bold">
                            Informasi Penerima
                        </div>

                        <div class="card-body p-0">

                            <table class="table table-bordered mb-0">

                                @if ($keluar->jenis_penerima == 'Perorangan')
                                    <tr>
                                        <th width="35%">Nama Karyawan</th>
                                        <td>{{ $keluar->karyawan->nama_karyawan ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Divisi</th>
                                        <td>{{ $keluar->karyawan->divisi ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Lokasi Penempatan</th>
                                        <td>{{ $keluar->lokasi->nama_lokasi ?? '-' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <th width="35%">Divisi</th>
                                        <td>{{ $keluar->divisi_klr ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Lokasi Penempatan</th>
                                        <td>{{ $keluar->lokasi->nama_lokasi ?? '-' }}</td>
                                    </tr>
                                @endif
                            </table>

                        </div>

                    </div>

                </div>

                {{-- KANAN --}}
                <div class="col-lg-5">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-bold">
                            Foto Aset
                        </div>

                        <div class="card-body d-flex flex-column justify-content-center text-center">

                            @if ($keluar->gambar)
                                <img src="{{ asset('storage/' . $keluar->gambar) }}"
                                    class="img-fluid rounded border shadow-sm mx-auto mb-3"
                                    style="
                        max-height: 320px;
                        width: auto;
                        object-fit: contain;
                    ">

                                <div>

                                    <a href="{{ asset('storage/' . $keluar->gambar) }}" download class="btn btn-primary">

                                        <i class="bx bx-download me-1"></i>
                                        Download Gambar

                                    </a>

                                </div>
                            @else
                                <div class="text-muted">

                                    <i class="bx bx-image-alt display-4"></i>

                                    <p class="mt-3 mb-0">
                                        Foto tidak tersedia
                                    </p>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>

                {{-- BUTTON --}}
                <div class="mt-4">

                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">

                        <i class="bx bx-arrow-back me-1"></i>
                        Kembali

                    </a>

                </div>

            </div>

        </div>

    @endsection
