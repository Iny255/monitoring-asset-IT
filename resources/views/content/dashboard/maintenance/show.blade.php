@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Service & Maintenance')

@section('content')

    @php
        /** @var \App\Models\Maintenance $maintenance */
    @endphp

    <div class="row"></div>

    <div class="row">
        <div class="col-lg-6">

            <div class="card mb-4">

                <div class="card-header">


                    <h5 class="mb-0">

                        <i class="bx bx-wrench"></i>

                        Informasi Service

                    </h5>

                </div>

                <div class="card-body">
                    <table class="table table-borderless">

                        <tr>

                            <th width="220">Kode Service</th>

                            <td>{{ $maintenance->kode_service }}</td>

                        </tr>

                        <tr>

                            <th>Tanggal</th>

                            <td>{{ $maintenance->tanggal ? \Carbon\Carbon::parse($maintenance->tanggal)->format('d-m-Y') : '-' }}
                            </td>

                        </tr>

                        <tr>

                            <th>Jenis</th>

                            <td>{{ $maintenance->jenis }}</td>

                        </tr>

                        <tr>

                            <th>Kategori</th>

                            <td>{{ $maintenance->kategori ?? '-' }}</td>

                        </tr>

                        <tr>

                            <th>Status</th>

                            <td>

                                <span class="badge bg-label-primary">

                                    {{ $maintenance->status }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <th>Vendor</th>

                            <td>{{ $maintenance->vendor ?: '-' }}</td>

                        </tr>

                        <tr>

                            <th>Biaya</th>

                            <td>

                                {{ $maintenance->biaya !== null ? 'Rp ' . number_format((float) $maintenance->biaya, 0, ',', '.') : '-' }}

                            </td>

                        </tr>

                        <tr>

                            <th>Tanggal Selesai</th>

                            <td>

                                {{ optional($maintenance->tanggal_selesai)->format('d-m-Y') ?: '-' }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>
        <div class="col-lg-6">

            <div class="card mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        <i class="bx bx-package"></i>

                        Informasi Inventaris

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>

                            <th width="220">Kode Aset</th>

                            <td>{{ $maintenance->inventaris->kode_aset }}</td>

                        </tr>

                        <tr>

                            <th>No Inventaris</th>

                            <td>{{ $maintenance->inventaris->no_inventaris }}</td>

                        </tr>

                        <tr>

                            <th>Nama Barang</th>

                            <td>{{ $maintenance->inventaris->dataAset->kategori->nama_barang }}</td>

                        </tr>


                        <tr>

                            <th>Merek</th>

                            <td>{{ $maintenance->inventaris->dataAset->merek }}</td>

                        </tr>

                        <tr>

                            <th>Type</th>

                            <td>{{ $maintenance->inventaris->dataAset->type }}</td>

                        </tr>

                        <tr>

                            <th>Warna</th>

                            <td>{{ $maintenance->inventaris->dataAset->warna }}</td>

                        </tr>

                        <tr>

                            <th>Perusahaan</th>

                            <td>{{ $maintenance->inventaris->perusahaan->nama_perusahaan }}</td>

                        </tr>

                        <tr>

                            <th>Status Inventaris</th>

                            <td>

                                @php
                                    $status = $maintenance->inventaris->status;
                                @endphp

                                @if ($status == 'TERSEDIA')
                                    <span class="badge bg-label-success">TERSEDIA</span>
                                @elseif($status == 'DIPAKAI')
                                    <span class="badge bg-label-primary">DIPAKAI</span>
                                @elseif($status == 'DIPINJAM')
                                    <span class="badge bg-label-warning">DIPINJAM</span>
                                @elseif($status == 'RUSAK')
                                    <span class="badge bg-label-danger">RUSAK</span>
                                @else
                                    <span class="badge bg-label-secondary">{{ $status }}</span>
                                @endif

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>
        <div class="col-lg-6">

            <div class="card mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        <i class="bx bx-notepad"></i>

                        Hasil Service

                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-4">

                        <label class="fw-bold">

                            Keluhan

                        </label>

                        <div class="border rounded p-3 bg-light">

                            {{ $maintenance->keluhan ?: '-' }}

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="fw-bold">

                            Diagnosa

                        </label>

                        <div class="border rounded p-3 bg-light">

                            {{ $maintenance->diagnosa ?: '-' }}

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="fw-bold">

                            Tindakan

                        </label>

                        <div class="border rounded p-3 bg-light">

                            {{ $maintenance->tindakan ?: '-' }}

                        </div>

                    </div>

                    <div>

                        <label class="fw-bold">

                            Catatan

                        </label>

                        <div class="border rounded p-3 bg-light">

                            {{ $maintenance->catatan ?: '-' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>
        {{-- ===========================
    ASAL SERVICE
=========================== --}}
        <div class="col-lg-6">

            <div class="card mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        <i class="bx bx-transfer"></i>

                        Asal Service

                    </h5>

                </div>

                <div class="card-body">

                    @if ($maintenance->asal == 'Manual')

                        <div class="alert alert-primary mb-0">

                            <i class="bx bx-info-circle me-1"></i>

                            Service ini dibuat secara <strong>Manual</strong> melalui menu
                            Service & Maintenance.

                        </div>
                    @elseif($maintenance->asal == 'Mapping')
                        <table class="table table-borderless">

                            <tr>

                                <th width="220">Asal</th>

                                <td>Mapping Inventaris</td>

                            </tr>

                            <tr>

                                <th>Status Mapping</th>

                                <td>

                                    <span class="badge bg-label-primary">

                                        {{ $maintenance->maping->status ?? '-' }}

                                    </span>

                                </td>

                            </tr>

                            <tr>

                                <th>Karyawan</th>

                                <td>

                                    {{ optional($maintenance->maping->karyawan)->nama_karyawan ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>Lokasi</th>

                                <td>

                                    {{ optional($maintenance->maping->lokasi)->nama_lokasi ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>Tanggal Mapping</th>

                                <td>

                                    {{ optional($maintenance->maping->tanggal)->format('d-m-Y') ?? '-' }}

                                </td>

                            </tr>

                        </table>
                    @elseif($maintenance->asal == 'Peminjaman')
                        <table class="table table-borderless">

                            <tr>

                                <th width="220">Asal</th>

                                <td>Peminjaman Inventaris</td>

                            </tr>

                            <tr>

                                <th>Kode Peminjaman</th>

                                <td>

                                    {{ $maintenance->peminjaman->kode_peminjaman ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>Peminjam</th>

                                <td>

                                    {{ optional($maintenance->peminjaman->karyawan)->nama_karyawan ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>Tanggal Pinjam</th>

                                <td>
                                    {{ $maintenance->peminjaman->tanggal_pinjam
                                        ? \Carbon\Carbon::parse($maintenance->peminjaman->tanggal_pinjam)->format('d-m-Y')
                                        : '-' }}
                                </td>

                            </tr>

                            <tr>

                                <th>Tanggal Kembali</th>

                                <td>
                                    {{ $maintenance->peminjaman->tanggal_kembali
                                        ? \Carbon\Carbon::parse($maintenance->peminjaman->tanggal_kembali)->format('d-m-Y')
                                        : '-' }}
                                </td>

                            </tr>

                            <tr>

                                <th>Kondisi Kembali</th>

                                <td>

                                    @php
                                        $kondisi = $maintenance->peminjaman->kondisi_kembali ?? null;
                                    @endphp

                                    @if ($kondisi == 'Baik')
                                        <span class="badge bg-label-success">
                                            Baik
                                        </span>
                                    @elseif($kondisi == 'Rusak')
                                        <span class="badge bg-label-danger">
                                            Rusak
                                        </span>
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                        </table>

                    @endif

                </div>

            </div>

        </div>
    </div>
    {{-- ======================================
    BUTTON
====================================== --}}

    <div class="d-flex justify-content-end mt-4">

        <a href="{{ $backRoute }}" class="btn btn-secondary">

            <i class="bx bx-arrow-back me-1"></i>

            Kembali

        </a>

    </div>
    </div>
@endsection
