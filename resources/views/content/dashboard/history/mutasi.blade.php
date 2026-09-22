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

                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>

                        <a href="{{ route('history.mutasi.cetak.semua', request()->query()) }}" target="_blank"
                            class="btn btn-success">
                            <i class="bx bx-printer me-1"></i> Cetak
                        </a>

                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>

                </div>

            </div>

        </div>

        <x-company-filter-banner />

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
        @if(request()->anyFilled(['search', 'perusahaan_id', 'jenis_mutasi', 'tanggal_awal', 'tanggal_akhir']))
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-label-primary px-3 py-2">
                    <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                </span>
                <a href="{{ route('history.mutasi.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-x me-1"></i> Reset Filter
                </a>
            </div>
        @endif
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

                                    @if ($mutasi->id_perusahaan_asal || $mutasi->id_perusahaan_tujuan)
                                        <div class="my-2 p-2 rounded" style="background-color: rgba(0,0,0,0.02); border: 1px dashed #dee2e6;">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="text-muted" style="font-size: 0.72rem;">Asal:</span>
                                                    <x-company-badge :perusahaan="$mutasi->perusahaanAsal" size="small" />
                                                </div>
                                                <i class="bx bx-right-arrow-alt text-muted"></i>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="text-muted" style="font-size: 0.72rem;">Tujuan:</span>
                                                    <x-company-badge :perusahaan="$mutasi->perusahaanTujuan" size="small" />
                                                </div>
                                            </div>
                                        </div>
                                    @endif

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

                                    <div class="d-flex justify-content-center gap-1">

                                        {{-- Detail --}}
                                        <a href="{{ route('history.mutasi.show', $mutasi->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">

                                            <i class="bx bx-show"></i>

                                        </a>

                                        {{-- Cetak --}}
                                        <a href="{{ route('history.mutasi.cetak', $mutasi->id) }}" target="_blank"
                                            class="btn btn-sm btn-icon btn-outline-success" data-bs-toggle="tooltip" title="Cetak">

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

        {{-- Modal Filter --}}
        <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter History Mutasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="GET" action="{{ route('history.mutasi.index') }}">
                        <div class="modal-body">
                            <div class="row g-3">
                                {{-- SEARCH --}}
                                <div class="col-12">
                                    <label class="form-label">Cari Asset / User</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Nama asset, user..."
                                            value="{{ request('search') }}">
                                    </div>
                                </div>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role == 'super_admin')
                                    <div class="col-md-6">
                                        <label class="form-label">Perusahaan</label>
                                        <select name="perusahaan_id" class="form-select">
                                            <option value="">Semua</option>
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
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Mutasi</label>
                                    <select name="jenis_mutasi" class="form-select">
                                        <option value="">Semua</option>
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
                                <div class="col-md-6">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" name="tanggal_awal" class="form-control"
                                        value="{{ request('tanggal_awal') }}">
                                </div>

                                {{-- TANGGAL AKHIR --}}
                                <div class="col-md-6">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" name="tanggal_akhir" class="form-control"
                                        value="{{ request('tanggal_akhir') }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('history.mutasi.index') }}" class="btn btn-outline-secondary">Reset</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
