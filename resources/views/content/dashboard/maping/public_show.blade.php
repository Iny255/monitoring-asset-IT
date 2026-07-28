<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <title>
        Detail Asset
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            padding: 20px;
        }

        .asset-card {
            max-width: 1000px;
            margin: auto;
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .asset-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
        }

        .asset-title {
            font-size: 28px;
            font-weight: 700;
        }

        .asset-subtitle {
            opacity: .9;
        }

        .asset-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 15px;
        }

        .table th {
            width: 35%;
            background: #f8fafc;
            color: #475569;
        }

        .table td {
            color: #0f172a;
            font-weight: 500;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 15px;
        }

        .badge-status {
            font-size: 14px;
            padding: 8px 15px;
        }
    </style>

</head>

<body>

    <div class="container py-4">

        <div class="card asset-card">

            {{-- HEADER --}}
            <div class="asset-header">

                <div class="row align-items-center">

                    <div class="col-md-9">

                        <h2 class="asset-title mb-2">

                            {{ strtoupper($maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-') }}

                        </h2>

                        <div class="asset-subtitle">

                            Monitoring Asset System

                        </div>

                        <div class="mt-2">

                            <span class="badge bg-light text-dark">

                                Kode Aset :
                                {{ $maping->keluar->inventaris->kode_aset ?? '-' }}

                            </span>

                            <span class="badge bg-warning text-dark ms-2">

                                No Inventaris :
                                {{ $maping->keluar->inventaris->no_inventaris ?? '-' }}

                            </span>

                        </div>

                    </div>

                    <div class="col-md-3 text-end">

                        @if ($maping->perusahaan)
                            <h5 class="text-white mb-0">

                                {{ strtoupper($maping->perusahaan->nama_perusahaan) }}

                            </h5>
                        @endif

                    </div>

                </div>

            </div>


            <div class="card-body p-4">

                <div class="row g-4 align-items-start">

                    {{-- FOTO --}}
                    <div class="col-lg-4">

                        <div class="card shadow-sm border-0">

                            <div class="card-header bg-light">

                                <strong>

                                    Foto Asset

                                </strong>

                            </div>

                            <div class="card-body p-3">
                                @php
                                    $gambarPath = $maping->keluar?->gambar;
                                    $hasGambar = $gambarPath && (file_exists(public_path('storage/' . $gambarPath)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarPath));
                                @endphp

                                @if ($hasGambar)
                                    <img src="{{ asset('storage/' . $gambarPath) }}" alt="Foto Asset" class="asset-image img-fluid rounded">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center bg-light rounded py-5 px-3 text-muted" style="min-height:220px; border: 2px dashed #cbd5e1;">
                                        <i class="bx bx-image-alt fs-1 text-secondary mb-2"></i>
                                        <span class="fw-semibold">Tidak Ada Foto Perangkat</span>
                                        <small class="text-muted mt-1 text-center">Foto belum diunggah pada transaksi penyerahan aset</small>
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>

                    {{-- INFORMASI --}}
                    <div class="col-md-8">

                        <div class="section-title">

                            Identitas Asset

                        </div>
                        <table class="table table-bordered">

                            <tr>
                                <th>Kode Aset</th>
                                <td>
                                    {{ $maping->keluar->inventaris->kode_aset ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>No Inventaris</th>
                                <td>
                                    {{ $maping->keluar->inventaris->no_inventaris ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Nama Barang</th>
                                <td>
                                    {{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Type</th>
                                <td>
                                    {{ $maping->keluar->inventaris->dataAset->type ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Merek</th>
                                <td>
                                    {{ $maping->keluar->inventaris->dataAset->merek ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Warna</th>
                                <td>
                                    {{ $maping->keluar->inventaris->dataAset->warna ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>User Asset</th>
                                <td>
                                     {{ $maping->penerima ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Lokasi</th>
                                <td>
                                    {{ $maping->lokasi->nama_lokasi ?? '-' }}
                                </td>
                            </tr>
                            <tr>

                                <th>Tanggal Digunakan</th>

                                <td>

                                    {{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d-m-Y') : '-' }}

                                </td>

                            </tr>

                            <tr>
                                <th>Perusahaan</th>
                                <td>
                                    {{ $maping->perusahaan->nama_perusahaan ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>

                                    @if ($maping->status == 'servis')
                                        <span class="badge bg-secondary badge-status">

                                            SERVIS

                                        </span>
                                    @elseif($maping->status == 'dipinjam')
                                        <span class="badge bg-warning badge-status">

                                            DIPINJAM

                                        </span>
                                    @elseif($maping->status == 'selesai')
                                        <span class="badge bg-danger badge-status">

                                            NON AKTIF

                                        </span>
                                    @elseif($maping->status == 'maintenance')
                                        <span class="badge bg-info badge-status">

                                            MAINTENANCE

                                        </span>
                                    @else
                                        <span class="badge bg-success badge-status">

                                            AKTIF

                                        </span>
                                    @endif

                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

                {{-- SPESIFIKASI --}}
                <div class="mt-4">

                    <div class="section-title">
                        Spesifikasi Perangkat
                    </div>

                    <table class="table table-bordered">

                        <tr>
                            <th>Processor</th>
                            <td>{{ $maping->processor ?? '-' }}</td>
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
                            <th>Version</th>
                            <td>{{ $maping->version ?? '-' }}</td>
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
                            <th>Install On</th>
                            <td>
                                {{ $maping->instal_on ? \Carbon\Carbon::parse($maping->instal_on)->format('d-m-Y') : '-' }}
                            </td>
                        </tr>

                    </table>

                </div>

                {{-- APLIKASI --}}
                @if ($maping->aplikasi)
                    <div class="mt-4">

                        <div class="section-title">
                            Aplikasi Terinstall
                        </div>

                        <div class="alert alert-light border">
                            {!! nl2br(e($maping->aplikasi)) !!}
                        </div>

                    </div>
                @endif

            </div>

        </div>

</body>

</html>
