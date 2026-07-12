@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Peminjaman')

@section('content')

    <div class="row">

        {{-- ==============================
        INFORMASI PEMINJAMAN
    =============================== --}}
        <div class="col-lg-7">

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h4 class="mb-1">

                        Detail Peminjaman Aset

                    </h4>

                    <small class="text-muted">

                        Informasi lengkap transaksi peminjaman aset.

                    </small>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-uppercase fw-bold">

                                Kode Peminjaman

                            </label>

                            <input type="text" class="form-control" value="{{ $peminjaman->kode_peminjaman }}" readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-uppercase fw-bold">

                                Status

                            </label>

                            <div>

                                @switch($peminjaman->status)
                                    @case('Dipinjam')
                                        <span class="badge bg-warning fs-6">

                                            DIPINJAM

                                        </span>
                                    @break

                                    @case('Selesai')
                                        <span class="badge bg-success fs-6">

                                            DIKEMBALIKAN

                                        </span>
                                    @break

                                    @case('Hilang')
                                        <span class="badge bg-danger fs-6">

                                            HILANG

                                        </span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">

                                            {{ strtoupper($peminjaman->status) }}

                                        </span>
                                @endswitch

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-uppercase fw-bold">

                                Jenis Peminjaman

                            </label>

                            <input type="text" class="form-control"
                                value="{{ strtoupper(str_replace('_', ' ', $peminjaman->jenis_peminjaman)) }}" readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-uppercase fw-bold">

                                Dibuat Oleh

                            </label>

                            <input type="text" class="form-control" value="{{ $peminjaman->user->name ?? '-' }}"
                                readonly>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-uppercase fw-bold">

                                Tanggal Pinjam

                            </label>

                            <input type="text" class="form-control"
                                value="{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}"
                                readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-uppercase fw-bold">

                                Rencana Kembali

                            </label>

                            <input type="text" class="form-control"
                                value="{{ \Carbon\Carbon::parse($peminjaman->tanggal_rencana_kembali)->translatedFormat('d F Y') }}"
                                readonly>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ==============================
        INFORMASI INVENTARIS
    =============================== --}}
        <div class="col-lg-5">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">

                        Informasi Inventaris

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>

                            <th width="40%">

                                Kode Aset

                            </th>

                            <td>

                                {{ $peminjaman->inventaris->kode_aset }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                No Inventaris

                            </th>

                            <td>

                                {{ $peminjaman->inventaris->no_inventaris }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Nama Barang

                            </th>

                            <td>

                                {{ $peminjaman->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Data Aset

                            </th>

                            <td>

                                <strong>

                                    {{ $peminjaman->inventaris->dataAset->merek }}

                                </strong>

                                <br>

                                {{ $peminjaman->inventaris->dataAset->type }}

                                <br>

                                {{ $peminjaman->inventaris->dataAset->warna }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Perusahaan

                            </th>

                            <td>

                                {{ $peminjaman->inventaris->perusahaan->nama_perusahaan }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Status Inventaris

                            </th>

                            <td>

                                @switch($peminjaman->inventaris->status)
                                    @case('TERSEDIA')
                                        <span class="badge bg-success">

                                            TERSEDIA

                                        </span>
                                    @break

                                    @case('DIPINJAM')
                                        <span class="badge bg-warning">

                                            DIPINJAM

                                        </span>
                                    @break

                                    @case('DIPAKAI')
                                        <span class="badge bg-primary">

                                            DIPAKAI

                                        </span>
                                    @break

                                    @case('RUSAK')
                                        <span class="badge bg-danger">

                                            RUSAK

                                        </span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">

                                            {{ $peminjaman->inventaris->status }}

                                        </span>
                                @endswitch

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>
        {{-- ======================================
        INFORMASI PEMINJAM
    ======================================= --}}

        <div class="col-12 mt-4">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">

                        Informasi Peminjam

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        @if ($peminjaman->jenis_peminjaman == 'internal')
                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-bold">

                                    Kode Karyawan

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->karyawan->kode_karyawan ?? '-' }}" readonly>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-bold">

                                    Nama Karyawan

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->karyawan->nama_karyawan ?? '-' }}" readonly>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-bold">

                                    Divisi

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->karyawan->divisi ?? '-' }}" readonly>

                            </div>
                        @else
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Perusahaan Tujuan

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->perusahaanTujuan->nama_perusahaan ?? '-' }}" readonly>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Karyawan Tujuan

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->karyawanTujuan->nama_karyawan ?? '-' }}" readonly>

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>

        {{-- ======================================
        KEPERLUAN
    ======================================= --}}

        <div class="col-12 mt-4">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">

                        Keperluan Peminjaman

                    </h5>

                </div>

                <div class="card-body">

                    <textarea class="form-control" rows="4" readonly>{{ $peminjaman->keperluan }}</textarea>

                </div>

            </div>

        </div>

        {{-- ======================================
        TIMELINE
    ======================================= --}}

        <div class="col-12 mt-4">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">

                        Timeline Peminjaman

                    </h5>

                </div>

                <div class="card-body">

                    <ul class="timeline mb-0">

                        <li class="timeline-item timeline-item-primary mb-4">

                            <span class="timeline-indicator timeline-indicator-primary">

                                <i class="bx bx-log-in-circle"></i>

                            </span>

                            <div class="timeline-event">

                                <div class="timeline-header mb-1">

                                    <h6 class="mb-0">

                                        Aset Dipinjam

                                    </h6>

                                    <small>

                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}

                                    </small>

                                </div>

                                <p class="mb-0">

                                    Transaksi peminjaman berhasil dibuat.

                                </p>

                            </div>

                        </li>

                        <li class="timeline-item timeline-item-warning mb-4">

                            <span class="timeline-indicator timeline-indicator-warning">

                                <i class="bx bx-calendar"></i>

                            </span>

                            <div class="timeline-event">

                                <div class="timeline-header mb-1">

                                    <h6 class="mb-0">

                                        Jadwal Pengembalian

                                    </h6>

                                    <small>

                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_rencana_kembali)->translatedFormat('d F Y') }}

                                    </small>

                                </div>

                                <p class="mb-0">

                                    Tanggal maksimal pengembalian aset.

                                </p>

                            </div>

                        </li>

                        @if ($peminjaman->tanggal_kembali)
                            <li class="timeline-item timeline-item-success">

                                <span class="timeline-indicator timeline-indicator-success">

                                    <i class="bx bx-check-circle"></i>

                                </span>

                                <div class="timeline-event">

                                    <div class="timeline-header mb-1">

                                        <h6 class="mb-0">

                                            Aset Dikembalikan

                                        </h6>

                                        <small>

                                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}

                                        </small>

                                    </div>

                                    <p class="mb-0">

                                        Kondisi :

                                        <strong>

                                            {{ $peminjaman->kondisi_kembali }}

                                        </strong>

                                    </p>

                                </div>

                            </li>
                        @else
                            <li class="timeline-item timeline-item-secondary">

                                <span class="timeline-indicator timeline-indicator-secondary">

                                    <i class="bx bx-time-five"></i>

                                </span>

                                <div class="timeline-event">

                                    <div class="timeline-header">

                                        <h6 class="mb-0">

                                            Belum Dikembalikan

                                        </h6>

                                    </div>

                                </div>

                            </li>
                        @endif

                    </ul>

                </div>

            </div>

        </div>
        {{-- ======================================
        INFORMASI PENGEMBALIAN
    ======================================= --}}

        <div class="col-12 mt-4">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">

                        Informasi Pengembalian

                    </h5>

                </div>

                <div class="card-body">

                    @if ($peminjaman->status == 'Dipinjam')

                        <div class="alert alert-warning mb-0">

                            <i class="bx bx-time-five me-1"></i>

                            Aset masih dipinjam dan belum dilakukan proses pengembalian.

                        </div>
                    @else
                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-bold">

                                    Tanggal Kembali

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}"
                                    readonly>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-bold">

                                    Kondisi Aset

                                </label>

                                <div class="mt-2">

                                    @switch($peminjaman->kondisi_kembali)
                                        @case('Baik')
                                            <span class="badge bg-success fs-6">

                                                BAIK

                                            </span>
                                        @break

                                        @case('Rusak')
                                            <span class="badge bg-danger fs-6">

                                                RUSAK

                                            </span>
                                        @break

                                        @case('Hilang')
                                            <span class="badge bg-dark fs-6">

                                                HILANG

                                            </span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">

                                                -

                                            </span>
                                    @endswitch

                                </div>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-bold">

                                    Status Transaksi

                                </label>

                                <div class="mt-2">

                                    @if ($peminjaman->status == 'Dikembalikan')
                                        <span class="badge bg-success fs-6">

                                            DIKEMBALIKAN

                                        </span>
                                    @elseif($peminjaman->status == 'Hilang')
                                        <span class="badge bg-danger fs-6">

                                            HILANG

                                        </span>
                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Keterangan Pengembalian

                            </label>

                            <textarea class="form-control" rows="5" readonly>{{ $peminjaman->keterangan_kembali ?: '-' }}</textarea>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

    {{-- ======================================
    BUTTON
====================================== --}}

    <div class="d-flex justify-content-end mt-4">

        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">

            <i class="bx bx-arrow-back me-1"></i>

            Kembali

        </a>

    </div>

@endsection
