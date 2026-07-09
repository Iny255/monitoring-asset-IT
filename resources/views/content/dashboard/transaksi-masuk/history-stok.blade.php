@extends('layouts/contentNavbarLayout')

@section('title', 'Riwayat Perjalanan Asset')

@section('content')

    <style>
        .info-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
        }

        .summary-card {
            border: none;
            border-radius: 16px;
            transition: .2s;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
            font-size: 28px;
        }

        .timeline-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        }

        .table thead th {
            background: #1d4ed8;
            color: white;
            border: none;
            vertical-align: middle;
        }

        .badge-status {
            font-size: 12px;
            padding: 7px 12px;
        }
    </style>

    @php

        $first = $inventaris->first();

        $totalKeluar = $timeline->where('aktivitas', 'KELUAR')->count();

        $totalCabut = $timeline->where('aktivitas', 'PENCABUTAN')->count();

        $totalMutasi = $timeline->where('aktivitas', 'MUTASI')->count();

    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>

                    <h2 class="fw-bold mb-1">

                        Riwayat Perjalanan Asset

                    </h2>

                    <div class="text-muted">

                        {{ optional($first->dataAset->kategori)->nama_barang }}

                        •

                        {{ optional($first->dataAset)->merek }}

                        •

                        {{ optional($first->dataAset)->type }}

                    </div>

                </div>

                <a href="{{ route('transaksi-masuk.stok') }}" class="btn btn-outline-secondary">

                    <i class="bx bx-arrow-back"></i>

                    Kembali

                </a>

            </div>

        </div>

        {{-- INFORMASI ASSET --}}
        <div class="card info-card mb-4">

            <div class="card-header">

                <strong>

                    Informasi Asset

                </strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <label class="text-muted">

                            Kategori

                        </label>

                        <h6>

                            {{ optional($first->dataAset->kategori)->nama_barang }}

                        </h6>

                    </div>

                    <div class="col-md-3">

                        <label class="text-muted">

                            Merek

                        </label>

                        <h6>

                            {{ optional($first->dataAset)->merek }}

                        </h6>

                    </div>

                    <div class="col-md-3">

                        <label class="text-muted">

                            Type

                        </label>

                        <h6>

                            {{ optional($first->dataAset)->type }}

                        </h6>

                    </div>

                    <div class="col-md-3">

                        <label class="text-muted">

                            Total Unit

                        </label>

                        <h6>

                            {{ $inventaris->count() }} Unit

                        </h6>

                    </div>

                </div>

            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="row mb-4">

            <div class="col-md-4">

                <div class="card summary-card">

                    <div class="card-body text-center">

                        <div class="summary-icon bg-primary text-white">

                            <i class="bx bx-log-in-circle"></i>

                        </div>

                        <div class="mt-3 text-muted">

                            Total Keluar

                        </div>

                        <h2 class="text-primary">

                            {{ $totalKeluar }}

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card summary-card">

                    <div class="card-body text-center">

                        <div class="summary-icon bg-danger text-white">

                            <i class="bx bx-log-out-circle"></i>

                        </div>

                        <div class="mt-3 text-muted">

                            Pencabutan

                        </div>

                        <h2 class="text-danger">

                            {{ $totalCabut }}

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card summary-card">

                    <div class="card-body text-center">

                        <div class="summary-icon bg-warning text-white">

                            <i class="bx bx-transfer"></i>

                        </div>

                        <div class="mt-3 text-muted">

                            Mutasi

                        </div>

                        <h2 class="text-warning">

                            {{ $totalMutasi }}

                        </h2>

                    </div>

                </div>

            </div>

        </div>

        {{-- TIMELINE --}}
        <div class="card timeline-card">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                    <h5 class="fw-bold mb-0">

                        Timeline Perjalanan Asset

                    </h5>

                    <div style="width:320px">

                        <input type="text" id="searchTimeline" class="form-control"
                            placeholder="Cari user, petugas, aktivitas...">

                    </div>

                </div>

                <form method="GET">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">

                            <label class="form-label fw-semibold">

                                Dari Tanggal

                            </label>

                            <input type="date" name="tanggal_awal" class="form-control"
                                value="{{ request('tanggal_awal') }}">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label fw-semibold">

                                Sampai Tanggal

                            </label>

                            <input type="date" name="tanggal_akhir" class="form-control"
                                value="{{ request('tanggal_akhir') }}">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label fw-semibold">

                                Kode Asset

                            </label>

                            <select name="kode_aset" class="form-select">

                                <option value="">

                                    -- Semua Kode Asset --

                                </option>

                                @foreach ($kodeAsets as $kode)
                                    <option value="{{ $kode }}"
                                        {{ request('kode_aset') == $kode ? 'selected' : '' }}>

                                        {{ $kode }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-3">

                            <div class="d-flex gap-2">

                                <button type="submit" class="btn btn-primary">

                                    <i class="bx bx-search me-1"></i>

                                    Filter

                                </button>

                                <a href="{{ route('stok.history', $inventaris->first()->data_aset_id) }}"
                                    class="btn btn-secondary">

                                    Reset

                                </a>

                            </div>

                        </div>

                    </div>

                    <div class="mt-3">

                        <a href="{{ route('stok.history.cetak', [
                            'dataAsetId' => $inventaris->first()->data_aset_id,
                            'tanggal_awal' => request('tanggal_awal'),
                            'tanggal_akhir' => request('tanggal_akhir'),
                            'kode_aset' => request('kode_aset'),
                        ]) }}"
                            target="_blank" class="btn btn-danger">

                            <i class="bx bxs-file-pdf me-1"></i>

                            Cetak PDF

                        </a>

                    </div>

                </form>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>

                            <tr>

                                <th width="120">Tanggal</th>

                                <th width="150">Kode Asset</th>

                                <th width="150">Aktivitas</th>

                                <th>User</th>

                                <th>Lokasi</th>

                                <th>Keterangan</th>

                                <th width="130">Petugas</th>

                            </tr>

                        </thead>

                        <tbody id="timelineBody">

                            @forelse($timeline as $item)

                                <tr>

                                    {{-- ========================= --}}
                                    {{-- TANGGAL --}}
                                    {{-- ========================= --}}
                                    <td>

                                        {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}

                                    </td>

                                    {{-- ========================= --}}
                                    {{-- KODE ASET --}}
                                    {{-- ========================= --}}
                                    <td>

                                        @if ($item['aktivitas'] == 'MUTASI')
                                            @if (($item['kode_aset_lama'] ?? '-') == ($item['kode_aset_baru'] ?? '-'))
                                                <strong>

                                                    {{ $item['kode_aset_lama'] }}

                                                </strong>
                                            @else
                                                <div class="fw-bold text-primary">

                                                    {{ $item['kode_aset_lama'] }}

                                                </div>

                                                <div class="text-center">

                                                    <i class="bx bx-down-arrow-alt text-secondary"></i>

                                                </div>

                                                <div class="fw-bold text-success">

                                                    {{ $item['kode_aset_baru'] }}

                                                </div>
                                            @endif
                                        @else
                                            <strong>

                                                {{ $item['kode_aset'] }}

                                            </strong>
                                        @endif

                                    </td>

                                    {{-- ========================= --}}
                                    {{-- AKTIVITAS --}}
                                    {{-- ========================= --}}
                                    <td>

                                        @switch($item['aktivitas'])
                                            @case('KELUAR')
                                                <span class="badge bg-primary badge-status">

                                                    <i class="bx bx-log-in-circle me-1"></i>

                                                    KELUAR

                                                </span>
                                            @break

                                            @case('MUTASI')
                                                <span class="badge bg-warning text-dark badge-status">

                                                    <i class="bx bx-transfer me-1"></i>

                                                    MUTASI

                                                </span>
                                            @break

                                            @case('PENCABUTAN')
                                                <span class="badge bg-danger badge-status">

                                                    <i class="bx bx-log-out-circle me-1"></i>

                                                    PENCABUTAN

                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">

                                                    {{ $item['aktivitas'] }}

                                                </span>
                                        @endswitch

                                    </td>

                                    {{-- ========================= --}}
                                    {{-- USER --}}
                                    {{-- ========================= --}}
                                    <td>

                                        @if (!empty($item['user_lama']))
                                            <div class="fw-semibold text-primary">

                                                <i class="bx bx-user me-1"></i>

                                                {{ $item['user_lama'] }}

                                            </div>

                                            <div class="text-center my-1">

                                                <i class="bx bx-down-arrow-alt text-secondary"></i>

                                            </div>
                                        @endif

                                        @if (!empty($item['user_baru']))
                                            <div class="fw-semibold text-success">

                                                <i class="bx bx-user-check me-1"></i>

                                                {{ $item['user_baru'] }}

                                            </div>
                                        @endif

                                    </td>

                                    {{-- ========================= --}}
                                    {{-- LOKASI --}}
                                    {{-- ========================= --}}
                                    <td>

                                        @if (!empty($item['lokasi_lama']))
                                            <div>

                                                <i class="bx bx-map me-1"></i>

                                                {{ $item['lokasi_lama'] }}

                                            </div>

                                            <div class="text-center my-1">

                                                <i class="bx bx-down-arrow-alt text-secondary"></i>

                                            </div>
                                        @endif

                                        @if (!empty($item['lokasi_baru']))
                                            <div>

                                                <i class="bx bx-map-pin me-1"></i>

                                                {{ $item['lokasi_baru'] }}

                                            </div>
                                        @endif

                                    </td>

                                    {{-- ========================= --}}
                                    {{-- KETERANGAN --}}
                                    {{-- ========================= --}}
                                    <td>

                                        {{ $item['keterangan'] }}

                                        @if ($item['aktivitas'] == 'MUTASI')
                                            <div class="mt-2">

                                                <span class="badge bg-info">

                                                    {{ $item['jenis_mutasi'] == 'internal' ? 'Mutasi Internal' : 'Mutasi Antar Perusahaan' }}

                                                </span>

                                                @if ($item['hak_akses'] == 'copy')
                                                    <span class="badge bg-primary">

                                                        Hak Akses Disalin

                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">

                                                        Hak Akses Manual

                                                    </span>
                                                @endif

                                            </div>
                                        @endif

                                    </td>

                                    {{-- ========================= --}}
                                    {{-- PETUGAS --}}
                                    {{-- ========================= --}}
                                    <td>

                                        {{ $item['petugas'] ?? '-' }}

                                    </td>

                                </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center py-5">

                                            <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}"
                                                width="120" class="mb-3">

                                            <br>

                                            <span class="text-muted">

                                                Belum ada riwayat perjalanan asset.

                                            </span>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    @endsection

    @section('scripts')

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const search = document.getElementById('searchTimeline');

                if (search) {

                    search.addEventListener('keyup', function() {

                        let keyword = this.value.toLowerCase();

                        document.querySelectorAll('#timelineBody tr').forEach(function(row) {

                            row.style.display =
                                row.innerText.toLowerCase().includes(keyword) ?
                                '' :
                                'none';

                        });

                    });

                }

            });
        </script>

    @endsection
