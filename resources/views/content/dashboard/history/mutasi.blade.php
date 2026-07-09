@extends('layouts/contentNavbarLayout')

@section('title', 'History Mutasi Asset')

@section('content')

    <style>
        .table th {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            font-size: .82rem;
            font-weight: 700;
        }

        .table td {
            vertical-align: middle;
            padding: 14px 16px;
        }

        .table td small {
            display: block;
            color: #98a4b5;
            margin-top: 3px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ========================================================= --}}
        {{-- HERO HEADER --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body py-4">

                <div class="d-flex justify-content-between align-items-start flex-wrap">

                    <div>

                        <div class="d-flex align-items-center mb-2">

                            <div class="avatar avatar-md bg-label-primary me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-transfer-alt fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <h3 class="fw-bold mb-0">

                                    History Mutasi Asset

                                </h3>

                                <small class="text-muted">

                                    Riwayat perpindahan asset antar user, lokasi maupun perusahaan.

                                </small>

                            </div>

                        </div>

                    </div>

                    <div>

                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">

                            <i class="bx bx-arrow-back me-1"></i>

                            Kembali

                        </a>

                    </div>

                </div>

            </div>

        </div>
        {{-- ========================================================= --}}
        {{-- SUMMARY --}}
        {{-- ========================================================= --}}

        <div class="row mb-4">

            {{-- TOTAL MUTASI --}}
            <div class="col-xl-3 col-md-6 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="avatar avatar-lg bg-label-primary me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-transfer-alt fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <small class="text-muted d-block">

                                    Total Mutasi

                                </small>

                                <h3 class="mb-0">

                                    {{ $totalMutasi }}

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- INTERNAL --}}
            <div class="col-xl-3 col-md-6 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="avatar avatar-lg bg-label-success me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-buildings fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <small class="text-muted d-block">

                                    Mutasi Internal

                                </small>

                                <h3 class="mb-0">

                                    {{ $internal }}

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ANTAR PERUSAHAAN --}}
            <div class="col-xl-3 col-md-6 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="avatar avatar-lg bg-label-warning me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-git-compare fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <small class="text-muted d-block">

                                    Antar Perusahaan

                                </small>

                                <h3 class="mb-0">

                                    {{ $antarPerusahaan }}

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- BULAN INI --}}
            <div class="col-xl-3 col-md-6 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="avatar avatar-lg bg-label-info me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-calendar fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <small class="text-muted d-block">

                                    Bulan Ini

                                </small>

                                <h3 class="mb-0">

                                    {{ $bulanIni }}

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        {{-- ========================================================= --}}
        {{-- FILTER --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="fw-bold mb-0">

                        <i class="bx bx-filter-alt text-primary me-2"></i>

                        Filter Data

                    </h5>

                    <span class="badge bg-label-primary">

                        History Mutasi

                    </span>

                </div>

            </div>

            <div class="card-body">

                <form method="GET">

                    <div class="row g-3">

                        {{-- SEARCH --}}
                        <div class="col-lg-4">

                            <label class="form-label">

                                Cari Asset / User

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bx bx-search"></i>

                                </span>

                                <input type="text" name="search" class="form-control" placeholder="Nama asset, user..."
                                    value="{{ request('search') }}">

                            </div>

                        </div>

                        {{-- PERUSAHAAN --}}
                        @if (auth()->user()->role == 'super_admin')

                            <div class="col-lg-2">

                                <label class="form-label">

                                    Perusahaan

                                </label>

                                <select name="perusahaan_id" class="form-select">

                                    <option value="">

                                        Semua

                                    </option>

                                    @foreach ($perusahaans as $perusahaan)
                                        <option value="{{ $perusahaan->id }}"
                                            {{ request('perusahaan_id') == $perusahaan->id ? 'selected' : '' }}>

                                            {{ $perusahaan->nama_perusahaan }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        @endif

                        {{-- JENIS --}}
                        <div class="col-lg-2">

                            <label class="form-label">

                                Jenis Mutasi

                            </label>

                            <select name="jenis_mutasi" class="form-select">

                                <option value="">

                                    Semua

                                </option>

                                <option value="internal" {{ request('jenis_mutasi') == 'internal' ? 'selected' : '' }}>

                                    Internal

                                </option>

                                <option value="antar_perusahaan"
                                    {{ request('jenis_mutasi') == 'antar_perusahaan' ? 'selected' : '' }}>

                                    Antar Perusahaan

                                </option>

                            </select>

                        </div>

                        {{-- TANGGAL AWAL --}}
                        <div class="col-lg-2">

                            <label class="form-label">

                                Dari

                            </label>

                            <input type="date" name="tanggal_awal" class="form-control"
                                value="{{ request('tanggal_awal') }}">

                        </div>

                        {{-- TANGGAL AKHIR --}}
                        <div class="col-lg-2">

                            <label class="form-label">

                                Sampai

                            </label>

                            <input type="date" name="tanggal_akhir" class="form-control"
                                value="{{ request('tanggal_akhir') }}">

                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>

                            <button class="btn btn-primary">

                                <i class="bx bx-search-alt me-1"></i>

                                Filter

                            </button>

                            <a href="{{ route('history.mutasi.index') }}" class="btn btn-outline-secondary">

                                <i class="bx bx-refresh me-1"></i>

                                Reset

                            </a>

                        </div>

                        <div>

                            <a href="{{ route('history.mutasi.cetak.semua', request()->query()) }}" target="_blank"
                                class="btn btn-success">

                                <i class="bx bx-printer me-1"></i>

                                Cetak

                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>
        <div class="card border-0 shadow-sm">

            {{-- HEADER TABLE --}}
            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-1">

                            <i class="bx bx-transfer-alt text-primary me-2"></i>

                            Riwayat Mutasi Asset

                        </h5>

                        <small class="text-muted">

                            Total Data :

                            <strong>{{ $mutasis->total() }}</strong>

                        </small>

                    </div>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="5%">No</th>

                            <th width="12%">Tanggal</th>

                            <th width="22%">Asset</th>

                            <th width="28%">Mutasi</th>

                            <th width="10%">Hak Akses</th>

                            <th width="13%">Petugas</th>

                            <th width="10%" class="text-center">

                                Aksi

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($mutasis as $mutasi)
                            <tr>

                                <td>

                                    {{ $mutasis->firstItem() + $loop->index }}

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d-m-Y') }}

                                </td>

                                <td>

                                    <strong>

                                        {{ $mutasi->nama_aset }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $mutasi->kode_aset_baru }}

                                    </small>

                                    <br>

                                    <small class="text-muted">

                                        {{ $mutasi->no_inventaris_baru }}

                                    </small>

                                </td>



                                <td>

                                    <div class="mb-3">

                                        <div class="fw-semibold text-primary">

                                            <i class="bx bx-user me-1"></i>

                                            {{ $mutasi->user_lama }}

                                        </div>

                                        <div class="ps-4 my-1">

                                            <i class="bx bx-down-arrow-alt text-muted"></i>

                                        </div>

                                        <div class="fw-semibold text-success">

                                            <i class="bx bx-user-check me-1"></i>

                                            {{ $mutasi->user_baru }}

                                        </div>

                                    </div>

                                    <hr class="my-2">

                                    <div>

                                        <div>

                                            <i class="bx bx-map me-1"></i>

                                            {{ $mutasi->lokasi_lama }}

                                        </div>

                                        <div class="ps-4 my-1">

                                            <i class="bx bx-down-arrow-alt text-muted"></i>

                                        </div>

                                        <div>

                                            <i class="bx bx-map-pin me-1"></i>

                                            {{ $mutasi->lokasi_baru }}

                                        </div>

                                    </div>

                                    @if ($mutasi->jenis_mutasi == 'antar_perusahaan')
                                        <div class="mt-2">

                                            <span class="badge bg-warning">

                                                Antar Perusahaan

                                            </span>

                                        </div>
                                    @else
                                        <div class="mt-2">

                                            <span class="badge bg-success">

                                                Internal

                                            </span>

                                        </div>
                                    @endif

                                </td>

                                <td>

                                    @if ($mutasi->opsi_hak_akses == 'copy')
                                        <span class="badge bg-primary">

                                            HAK AKSES DISALIN

                                        </span>
                                    @else
                                        <span class="badge bg-warning">

                                            HAK AKSES DIATUR SENDIRI

                                        </span>
                                    @endif

                                </td>


                                <td>

                                    {{ optional($mutasi->creator)->name }}

                                </td>

                                <td class="text-center">

                                    <div class="btn-group" role="group">

                                        {{-- Detail --}}
                                        <a href="{{ route('history.mutasi.show', $mutasi->id) }}"
                                            class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Lihat Detail">

                                            <i class="bx bx-show"></i>

                                        </a>

                                        {{-- Cetak --}}
                                        <a href="{{ route('history.mutasi.cetak', $mutasi->id) }}" target="_blank"
                                            class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Cetak">

                                            <i class="bx bx-printer"></i>

                                        </a>

                                    </div>

                                </td>
                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-5">

                                    <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}"
                                        width="150">

                                    <br><br>

                                    <span class="text-muted">

                                        Belum ada History Mutasi.

                                    </span>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($mutasis->hasPages())
                <div class="card-footer bg-white">

                    {{ $mutasis->links() }}

                </div>
            @endif

        </div>
    @endsection
