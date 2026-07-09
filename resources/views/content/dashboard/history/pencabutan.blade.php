@extends('layouts/contentNavbarLayout')

@section('title', 'History Pencabutan')

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

        {{-- Header --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center">

                        <div class="rounded d-flex align-items-center justify-content-center me-3"
                            style="
                        width:70px;
                        height:70px;
                        background:#f2f3ff;">

                            <i class="bx bx-history text-primary fs-2"></i>

                        </div>

                        <div>

                            <h3 class="mb-1 fw-bold">

                                History Pencabutan

                            </h3>

                            <span class="text-muted">

                                Riwayat seluruh proses pencabutan aset.

                            </span>

                        </div>

                    </div>

                    <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">

                        <i class="bx bx-arrow-back me-1"></i>

                        Kembali

                    </a>

                </div>

            </div>

        </div>

        {{-- Filter --}}
        <div class="card border-0 shadow-sm ">

            <div class="card-header">

                <strong>Filter Data</strong>

            </div>

            <div class="card-body py-4">

                <form method="GET">

                    <div class="row g-3">

                        {{-- Super Admin --}}
                        @if (auth()->user()->role == 'super_admin')

                            <div class="col-md-3">

                                <label class="form-label">
                                    Perusahaan
                                </label>

                                <select class="form-select" name="perusahaan">

                                    <option value="">
                                        Semua Perusahaan
                                    </option>

                                    @foreach ($perusahaans as $perusahaan)
                                        <option value="{{ $perusahaan->id }}"
                                            {{ request('perusahaan') == $perusahaan->id ? 'selected' : '' }}>

                                            {{ $perusahaan->nama_perusahaan }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        @endif

                        <div class="col-md-2">

                            <label class="form-label">
                                Tanggal Awal
                            </label>

                            <input type="date" name="tanggal_awal" class="form-control"
                                value="{{ request('tanggal_awal') }}">

                        </div>

                        <div class="col-md-2">

                            <label class="form-label">
                                Tanggal Akhir
                            </label>

                            <input type="date" name="tanggal_akhir" class="form-control"
                                value="{{ request('tanggal_akhir') }}">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                Cari
                            </label>

                            <input type="text" class="form-control" name="search"
                                placeholder="Kode aset / User / Inventaris" value="{{ request('search') }}">

                        </div>

                        <div class="col-md-4 d-flex align-items-end">

                            <button type="submit" class="btn btn-primary me-2">

                                <i class="bx bx-search-alt"></i>

                                Filter

                            </button>

                            <a href="{{ route('history.pencabutan.index') }}" class="btn btn-secondary me-2">

                                <i class="bx bx-reset"></i>

                                Reset

                            </a>
                            <a href="{{ route('history.pencabutan.cetak', request()->query()) }}" target="_blank"
                                class="btn btn-success">

                                <i class="bx bxs-file-pdf me-1"></i>

                                Cetak PDF

                            </a>

                        </div>
                    </div>

            </div>

            </form>

        </div>

    </div>

    {{-- Card Table --}}
    <div class="card shadow-sm ">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">

                    Riwayat Pencabutan

                </h5>

                <span class="badge bg-label-primary">

                    Total :
                    {{ $histories->total() }}

                </span>

            </div>

        </div>
        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="40">No</th>

                            <th>Tanggal</th>

                            <th>Kode Aset</th>

                            <th>No Inventaris</th>

                            <th>Nama Aset</th>

                            <th>User Lama</th>

                            <th>Lokasi</th>

                            <th>Petugas</th>

                            <th width="120">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($histories as $history)
                            <tr>

                                <td>

                                    {{ $histories->firstItem() + $loop->index }}

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($history->tanggal_pencabutan)->format('d-m-Y') }}

                                </td>

                                <td>

                                    <span class="badge bg-label-primary">

                                        {{ $history->kode_aset }}

                                    </span>

                                </td>

                                <td>

                                    {{ $history->no_inventaris }}

                                </td>

                                <td>

                                    {{ $history->nama_aset }}

                                </td>

                                <td>

                                    {{ $history->user_lama }}

                                </td>

                                <td>

                                    <div>

                                        <strong>

                                            {{ $history->lokasi_lama }}

                                        </strong>

                                    </div>

                                    <small class="text-muted">

                                        →

                                        {{ $history->lokasi_baru }}

                                    </small>

                                </td>

                                <td>

                                    {{ optional($history->creator)->name }}

                                </td>

                                <td>

                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#detailHistory{{ $history->id }}">

                                        <i class="bx bx-show"></i>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-5">

                                    <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}"
                                        width="140" class="mb-3">

                                    <br>

                                    Belum ada history pencabutan.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $histories->links() }}

            </div>

        </div>

    </div>

    </div>
    @foreach ($histories as $history)
        <div class="modal fade" id="detailHistory{{ $history->id }}" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">

                            Detail History Pencabutan

                        </h5>

                        <button class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    Tanggal
                                </label>

                                <div>
                                    {{ \Carbon\Carbon::parse($history->tanggal_pencabutan)->format('d-m-Y') }}
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    Petugas
                                </label>

                                <div>

                                    {{ optional($history->creator)->name }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    Kode Aset
                                </label>

                                <div>

                                    {{ $history->kode_aset }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    No Inventaris
                                </label>

                                <div>

                                    {{ $history->no_inventaris }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    Nama Asset
                                </label>

                                <div>

                                    {{ $history->nama_aset }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    User Lama
                                </label>

                                <div>

                                    {{ $history->user_lama }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    Lokasi Lama
                                </label>

                                <div>

                                    {{ $history->lokasi_lama }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="fw-semibold">
                                    Lokasi Baru
                                </label>

                                <div>

                                    {{ $history->lokasi_baru }}

                                </div>

                            </div>

                            <div class="col-12">

                                <label class="fw-semibold">
                                    Alasan Pencabutan
                                </label>

                                <div class="border rounded p-3 bg-light">

                                    {{ $history->alasan }}

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary" data-bs-dismiss="modal">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>
    @endforeach

@endsection
