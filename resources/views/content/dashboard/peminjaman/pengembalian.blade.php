@extends('layouts/contentNavbarLayout')

@section('title', 'Pengembalian Aset')

@section('content')
    @php
        use Carbon\Carbon;

        $today = Carbon::today();

        $rencana = Carbon::parse($peminjaman->tanggal_rencana_kembali);

        $terlambat = $today->gt($rencana);

        $selisih = $today->diffInDays($rencana);

        $durasi = Carbon::parse($peminjaman->tanggal_pinjam)->diffInDays($rencana);
    @endphp

    <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="row">

            {{-- ===========================
            INFORMASI PEMINJAMAN
        ============================ --}}

            <div class="col-lg-7">

                <div class="card shadow-sm">

                    <div class="card-header">

                        <h4 class="mb-1">

                            Pengembalian Aset

                        </h4>

                        <small class="text-muted">

                            Lengkapi data pengembalian aset.

                        </small>

                    </div>

                    <div class="card-body">

                        {{-- ALERT TERLAMBAT --}}

                        @if ($terlambat)
                            <div class="alert alert-danger d-flex align-items-center">

                                <i class="bx bx-error-circle fs-2 me-2"></i>

                                <div>

                                    <strong>

                                        Pengembalian Terlambat

                                    </strong>

                                    <br>

                                    Terlambat

                                    <b>{{ $selisih }} Hari</b>

                                    dari jadwal pengembalian.

                                </div>

                            </div>
                        @else
                            <div class="alert alert-success d-flex align-items-center">

                                <i class="bx bx-check-circle fs-2 me-2"></i>

                                <div>

                                    <strong>

                                        Pengembalian Tepat Waktu

                                    </strong>

                                    <br>

                                    Batas pengembalian

                                    <b>

                                        {{ Carbon::parse($peminjaman->tanggal_rencana_kembali)->format('d-m-Y') }}

                                    </b>

                                </div>

                            </div>
                        @endif

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Kode Peminjaman

                                </label>

                                <input type="text" class="form-control" value="{{ $peminjaman->kode_peminjaman }}"
                                    readonly>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Jenis Peminjaman

                                </label>

                                <input type="text" class="form-control"
                                    value="{{ strtoupper(str_replace('_', ' ', $peminjaman->jenis_peminjaman)) }}" readonly>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Tanggal Pinjam

                                </label>

                                <input type="date" class="form-control" value="{{ $peminjaman->tanggal_pinjam }}"
                                    readonly>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Rencana Kembali

                                </label>

                                <input type="date" class="form-control"
                                    value="{{ $peminjaman->tanggal_rencana_kembali }}" readonly>

                                <div class="mt-2">

                                    @if ($terlambat)
                                        <span class="badge bg-danger">

                                            Terlambat {{ $selisih }} Hari

                                        </span>
                                    @else
                                        <span class="badge bg-success">

                                            Belum Jatuh Tempo

                                        </span>
                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Durasi Rencana

                                </label>

                                <input type="text" class="form-control" value="{{ $durasi }} Hari" readonly>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Status

                                </label>

                                <input type="text" class="form-control" value="{{ strtoupper($peminjaman->status) }}"
                                    readonly>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Peminjam

                            </label>

                            @if ($peminjaman->jenis_peminjaman == 'internal')
                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->karyawan->kode_karyawan ?? '' }} - {{ $peminjaman->karyawan->nama_karyawan ?? '' }}"
                                    readonly>
                            @else
                                <input type="text" class="form-control"
                                    value="{{ $peminjaman->perusahaanTujuan->nama_perusahaan ?? '' }}" readonly>
                            @endif

                        </div>

                        <div class="mb-4">

                            <label class="form-label">

                                Keperluan

                            </label>

                            <textarea class="form-control" rows="4" readonly>{{ $peminjaman->keperluan }}</textarea>

                        </div>

                        <hr>

                        <h5 class="mb-3">

                            Informasi Pengembalian

                        </h5>


                        <div class="row">

                            {{-- Tanggal Kembali --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Tanggal Kembali

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="date" name="tanggal_kembali"
                                    class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                    value="{{ old('tanggal_kembali', now()->format('Y-m-d')) }}" required>

                                @error('tanggal_kembali')
                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>
                                @enderror

                            </div>

                            {{-- Kondisi --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Kondisi Aset

                                    <span class="text-danger">*</span>

                                </label>

                                <div class="border rounded p-3">

                                    <div class="form-check">

                                        <input class="form-check-input" type="radio" name="kondisi_kembali" id="baik"
                                            value="Baik" {{ old('kondisi_kembali', 'Baik') == 'Baik' ? 'checked' : '' }}>

                                        <label class="form-check-label" for="baik">

                                            <span class="badge bg-label-success">

                                                Baik

                                            </span>

                                        </label>

                                    </div>

                                    <div class="form-check mt-2">

                                        <input class="form-check-input" type="radio" name="kondisi_kembali" id="rusak"
                                            value="Rusak" {{ old('kondisi_kembali') == 'Rusak' ? 'checked' : '' }}>

                                        <label class="form-check-label" for="rusak">

                                            <span class="badge bg-label-danger">

                                                Rusak

                                            </span>

                                        </label>

                                    </div>

                                    <div class="form-check mt-2">

                                        <input class="form-check-input" type="radio" name="kondisi_kembali" id="hilang"
                                            value="Hilang" {{ old('kondisi_kembali') == 'Hilang' ? 'checked' : '' }}>

                                        <label class="form-check-label" for="hilang">

                                            <span class="badge bg-dark">

                                                Hilang

                                            </span>

                                        </label>

                                    </div>

                                </div>

                                @error('kondisi_kembali')
                                    <small class="text-danger">

                                        {{ $message }}

                                    </small>
                                @enderror

                            </div>

                        </div>

                        {{-- Keterangan --}}

                        <div class="mb-4">

                            <label class="form-label">

                                Keterangan Pengembalian

                            </label>

                            <textarea class="form-control @error('keterangan_kembali') is-invalid @enderror" rows="5"
                                name="keterangan_kembali" placeholder="Contoh: Kondisi baik, charger lengkap, tas laptop disertakan...">{{ old('keterangan_kembali') }}</textarea>

                            @error('keterangan_kembali')
                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>
            {{-- =========================================================
    INFORMASI INVENTARIS
========================================================= --}}

            <div class="col-lg-5">

                <div class="card shadow-sm h-100">

                    <div class="card-header">

                        <h5 class="mb-0">

                            Informasi Inventaris

                        </h5>

                        <small class="text-muted">

                            Informasi aset yang dipinjam.

                        </small>

                    </div>

                    <div class="card-body">

                        <div class="row mb-3">

                            <div class="col-5">

                                <small class="text-uppercase text-muted">

                                    Kode Aset

                                </small>

                            </div>

                            <div class="col-7 fw-semibold">

                                {{ $peminjaman->inventaris->kode_aset }}

                            </div>

                        </div>

                        <div class="row mb-3">

                            <div class="col-5">

                                <small class="text-uppercase text-muted">

                                    No Inventaris

                                </small>

                            </div>

                            <div class="col-7">

                                {{ $peminjaman->inventaris->no_inventaris }}

                            </div>

                        </div>

                        <div class="row mb-3">

                            <div class="col-5">

                                <small class="text-uppercase text-muted">

                                    Nama Barang

                                </small>

                            </div>

                            <div class="col-7">

                                {{ $peminjaman->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                            </div>

                        </div>

                        <div class="row mb-3">

                            <div class="col-5">

                                <small class="text-uppercase text-muted">

                                    Data Aset

                                </small>

                            </div>

                            <div class="col-7">

                                <strong>

                                    {{ $peminjaman->inventaris->dataAset->merek }}

                                </strong>

                                <br>

                                {{ $peminjaman->inventaris->dataAset->type }}

                                <br>

                                {{ $peminjaman->inventaris->dataAset->warna }}

                            </div>

                        </div>

                        <div class="row mb-3">

                            <div class="col-5">

                                <small class="text-uppercase text-muted">

                                    Perusahaan

                                </small>

                            </div>

                            <div class="col-7">

                                {{ $peminjaman->inventaris->perusahaan->nama_perusahaan }}

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-5">

                                <small class="text-uppercase text-muted">

                                    Status

                                </small>

                            </div>

                            <div class="col-7">

                                @switch($peminjaman->inventaris->status)
                                    @case('TERSEDIA')
                                        <span class="badge bg-label-success">

                                            TERSEDIA

                                        </span>
                                    @break

                                    @case('DIPAKAI')
                                        <span class="badge bg-label-primary">

                                            DIPAKAI

                                        </span>
                                    @break

                                    @case('DIPINJAM')
                                        <span class="badge bg-label-warning">

                                            DIPINJAM

                                        </span>
                                    @break

                                    @case('RUSAK')
                                        <span class="badge bg-label-danger">

                                            RUSAK

                                        </span>
                                    @break

                                    @default
                                        <span class="badge bg-label-secondary">

                                            {{ $peminjaman->inventaris->status }}

                                        </span>
                                @endswitch

                            </div>

                        </div>

                        <hr>

                        <div class="alert alert-info mb-0">

                            <i class="bx bx-info-circle"></i>

                            Pastikan kondisi aset telah diperiksa sebelum proses pengembalian disimpan.

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Tombol --}}

        <div class="d-flex justify-content-end mt-4">

            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary me-2">

                <i class="bx bx-arrow-back"></i>

                Kembali

            </a>

            <button type="submit" class="btn btn-primary">

                <i class="bx bx-save"></i>

                Simpan Pengembalian

            </button>

        </div>

    </form>

@endsection
