@extends('layouts/contentNavbarLayout')

@section('title', 'History Stok Asset')

@section('content')

    <style>
        .history-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
        }

        .history-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #fff;
        }

        .history-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .history-subtitle {
            opacity: .9;
            font-size: 14px;
        }

        .summary-box {
            background: #f8fafc;
            border-radius: 18px;
            padding: 22px 18px;
            text-align: center;
            height: 100%;
            border: 1px solid #e2e8f0;
            transition: .2s ease;
        }

        .summary-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .05);
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .summary-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .summary-value {
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
        }

        .table-history thead th {
            background: #1d4ed8;
            color: #fff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            border: none;
            padding: 16px;
        }

        .table-history tbody td {
            vertical-align: middle;
            padding: 15px;
        }

        .badge-qty-masuk {
            background: #dcfce7;
            color: #166534;
            padding: 7px 12px;
            border-radius: 8px;
            font-weight: 700;
        }

        .badge-qty-keluar {
            background: #fee2e2;
            color: #b91c1c;
            padding: 7px 12px;
            border-radius: 8px;
            font-weight: 700;
        }

        .btn-back {
            border-radius: 12px;
            padding: 10px 22px;
            font-weight: 600;
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 1px solid #dbe2ea;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .form-control,
        .form-select {
            height: 46px;
            border-radius: 12px;
        }

        .dropdown-menu {
            animation: fadeIn .2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @php
$first = $stokGroup->first();

    $allKeluars = collect();

    foreach ($stokGroup as $item) {

        foreach ($item->keluars as $keluar) {

            $allKeluars->push($keluar);
        }
    }

    $allKeluars = $allKeluars->sortByDesc('tgl_keluar');

    @endphp


    <div class="card history-card">

        {{-- HEADER --}}
        <div class="card-header border-0 text-white py-4"
            style="
                    background: linear-gradient(
                    90deg,
                    var(--theme-primary),
                    var(--theme-secondary)
                    );
                    ">

            <div class="history-title">
                History Stok Asset
            </div>

            <div class="history-subtitle">

                {{ $first->kategori->nama_barang ?? '-' }}
                -
                {{ $first->merek }}
                -
                {{ $first->type }}

            </div>

        </div>


        <div class="card-body p-4">
            {{-- TOOLBAR --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

                {{-- SEARCH --}}
                <div style="min-width:300px;">

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bx bx-search"></i>
                        </span>

                        <input type="text" id="searchInput" class="form-control" placeholder="Cari history asset...">

                    </div>

                </div>

                {{-- BUTTON FILTER --}}
                <div class="dropdown">

                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">

                        <i class="bx bx-filter-alt me-1"></i>
                        Filter

                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0"
                        style="min-width:280px; border-radius:16px;">

                        {{-- AKTIVITAS --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Aktivitas
                            </label>

                            <select id="filterAktivitas" class="form-select">

                                <option value="">
                                    Semua
                                </option>

                                <option value="ASSET MASUK">
                                    Asset Masuk
                                </option>

                                <option value="ASSET KELUAR">
                                    Asset Keluar
                                </option>

                            </select>

                        </div>

                        {{-- KONDISI --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Kondisi
                            </label>

                            <select id="filterKondisi" class="form-select">

                                <option value="">
                                    Semua
                                </option>

                                <option value="BARU">
                                    Baru
                                </option>

                                <option value="BEKAS">
                                    Bekas
                                </option>

                            </select>

                        </div>

                        {{-- RESET --}}
                        <button type="button" id="resetFilter" class="btn btn-secondary w-100">

                            Reset Filter

                        </button>

                    </div>

                </div>

            </div>
            {{-- SUMMARY --}}
            <div class="row justify-content-center g-4 mb-4">

                {{-- STOK AWAL --}}
                <div class="col-lg-4 col-md-4 col-sm-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            Stok Awal
                        </div>

                        <div class="summary-value text-primary">
                            {{ $stokAwal }}
                        </div>

                    </div>

                </div>

                {{-- TOTAL KELUAR --}}
                <div class="col-lg-4 col-md-4 col-sm-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            Total Keluar
                        </div>

                        <div class="summary-value text-danger">
                            {{ $totalKeluar }}
                        </div>

                    </div>

                </div>

                {{-- SISA STOK --}}
                <div class="col-lg-4 col-md-4 col-sm-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            Sisa Stok
                        </div>

                        <div class="summary-value text-success">
                            {{ $sisa }}
                        </div>

                    </div>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-bordered table-hover table-history">

                    <thead>

                        <tr>

                            <th width="5%">No</th>

                            <th>Tanggal</th>

                            <th>Aktivitas</th>
                            <th>Kondisi</th>
                            <th>Qty</th>

                            <th>User / Divisi</th>

                            <th>Keterangan</th>

                        </tr>

                    </thead>

                    <tbody id="historyTable">

                        {{-- HISTORY MASUK --}}
                        @foreach ($stokGroup as $index => $masuk)
                            <tr>

                                <td class="text-center">

                                    {{ $index + 1 }}

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($masuk->tgl_beli)->format('d M Y') }}

                                </td>

                                <td>

                                    <span class="badge bg-success">

                                        Asset Masuk

                                    </span>

                                </td>

                                <td class="text-center">

                                    @if ($masuk->kondisi == 'Baru')
                                        <span class="badge bg-primary">
                                            Baru
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Bekas
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    <span class="badge-qty-masuk">

                                        +{{ $masuk->jumlah }}

                                    </span>

                                </td>

                                <td class="text-center">

                                    Gudang

                                </td>

                                <td>

                                    Supplier:
                                    <b>{{ $masuk->supplier }}</b>

                                </td>

                            </tr>
                        @endforeach

                        {{-- HISTORY KELUAR --}}
                        @foreach ($allKeluars as $index => $keluar)
                            <tr>

                                <td class="text-center">

                                    {{ $stokGroup->count() + $index + 1 }}

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($keluar->tgl_keluar)->format('d M Y') }}

                                </td>

                                <td>

                                    <span class="badge bg-danger">

                                        Asset Keluar

                                    </span>

                                </td>

                                <td class="text-center">

                                    @if ($keluar->masuk->kondisi == 'Baru')
                                        <span class="badge bg-primary">
                                            Baru
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Bekas
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    <span class="badge-qty-keluar">

                                        -{{ $keluar->jumlah }}

                                    </span>

                                </td>

                                <td class="text-center">

                                    @if ($keluar->jenis_penerima == 'Perorangan')
                                        {{ $keluar->karyawan->nama_karyawan ?? '-' }}
                                    @else
                                        {{ $keluar->divisi_klr ?? '-' }}
                                    @endif

                                </td>

                                <td>

                                    {{ $keluar->keterangan ?? '-' }}

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- BUTTON --}}
            <div class="mt-4">

                {{-- BUTTON --}}
                <div class="mt-4">

                    <a href="
        {{ auth()->user()->role == 'manager' ? route('manager.laporan.stok') : route('transaksi-masuk.stok') }}
    "
                        class="btn btn-secondary btn-back">

                        ← Kembali

                    </a>

                </div>
            </div>


        </div>


    </div>

    </div>

@endsection
@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('searchInput');

            const filterAktivitas = document.getElementById('filterAktivitas');

            const filterKondisi = document.getElementById('filterKondisi');

            const resetFilter = document.getElementById('resetFilter');

            const rows = document.querySelectorAll('#historyTable tr');

            function filterTable() {

                const search = searchInput.value.toLowerCase();

                const aktivitas = filterAktivitas.value.toLowerCase();

                const kondisi = filterKondisi.value.toLowerCase();

                rows.forEach(row => {

                    const text = row.innerText.toLowerCase();

                    const matchSearch = text.includes(search);

                    const matchAktivitas =
                        aktivitas === '' || text.includes(aktivitas);

                    const matchKondisi =
                        kondisi === '' || text.includes(kondisi);

                    if (matchSearch && matchAktivitas && matchKondisi) {

                        row.style.display = '';

                    } else {

                        row.style.display = 'none';

                    }

                });

            }

            // SEARCH
            searchInput.addEventListener('keyup', filterTable);

            // FILTER
            filterAktivitas.addEventListener('change', filterTable);

            filterKondisi.addEventListener('change', filterTable);

            // RESET
            resetFilter.addEventListener('click', function() {

                searchInput.value = '';

                filterAktivitas.value = '';

                filterKondisi.value = '';

                filterTable();

            });

        });
    </script>

@endsection
