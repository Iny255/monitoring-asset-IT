@extends('layouts/contentNavbarLayout')

@section('title', 'Riwayat Stok Asset')

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

        /* ====================================
           TABLE
        ==================================== */

        .table-wrapper {
            padding: 0;
        }

        .table-history {
            margin-bottom: 0;
        }

        .table-history th,
        .table-history td {
            vertical-align: middle;
        }

        .table-history th {
            white-space: nowrap;
        }

        /* ====================================
           FOOTER
        ==================================== */

        .action-footer {
            padding: 15px 25px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }

        .btn-back {
            min-width: 140px;
            height: 45px;
            border-radius: 12px;
            font-weight: 600;
        }
    </style>
    @php

        $first = $inventaris->first();

        $totalAset = $inventaris->count();

        $tersedia = $inventaris->where('status', 'TERSEDIA')->count();

        $dipakai = $inventaris->where('status', 'DIPAKAI')->count();

        $dipinjam = $inventaris->where('status', 'DIPINJAM')->count();

        $rusak = $inventaris->where('status', 'RUSAK')->count();

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
                Riwayat Stok Aset
            </div>

            <div class="history-subtitle">

                {{ $first->dataAset->kategori->nama_barang ?? '-' }}
                -
                {{ $first->dataAset->merek ?? '-' }}
                -
                {{ $first->dataAset->type ?? '-' }}

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

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Status Asset
                            </label>

                            <select id="filterStatus" class="form-select">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="TERSEDIA">
                                    Tersedia
                                </option>

                                <option value="DIPAKAI">
                                    Dipakai
                                </option>

                                <option value="DIPINJAM">
                                    Dipinjam
                                </option>

                                <option value="RUSAK">
                                    Rusak
                                </option>

                            </select>

                        </div>

                        <button type="button" id="resetFilter" class="btn btn-secondary w-100">

                            Reset Filter

                        </button>

                    </div>

                </div>
            </div>
            {{-- SUMMARY --}}
            <div class="row g-3 mb-4">

                <div class="col-lg col-md-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            TOTAL ASSET
                        </div>

                        <div class="summary-value text-primary">

                            {{ $totalAset }}

                        </div>

                    </div>

                </div>

                <div class="col-lg col-md-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            TERSEDIA
                        </div>

                        <div class="summary-value text-success">

                            {{ $tersedia }}

                        </div>

                    </div>

                </div>

                <div class="col-lg col-md-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            DIPAKAI
                        </div>

                        <div class="summary-value text-info">

                            {{ $dipakai }}

                        </div>

                    </div>

                </div>

                <div class="col-lg col-md-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            DIPINJAM
                        </div>

                        <div class="summary-value text-warning">

                            {{ $dipinjam }}

                        </div>

                    </div>

                </div>

                <div class="col-lg col-md-6">

                    <div class="summary-box">

                        <div class="summary-label">
                            RUSAK
                        </div>

                        <div class="summary-value text-danger">

                            {{ $rusak }}

                        </div>

                    </div>

                </div>

            </div>

            {{-- ALERT --}}
            <div class="alert alert-primary mb-4">

                <i class="bx bx-info-circle me-1"></i>

                Saat ini terdapat

                <strong>{{ $dipakai }}</strong> asset digunakan,

                <strong>{{ $tersedia }}</strong> asset tersedia,

                <strong>{{ $dipinjam }}</strong> asset dipinjam,

                dan

                <strong>{{ $rusak }}</strong> asset rusak.

            </div>

            {{-- TABLE --}}
            <div class="table-responsive mb-4">

                <table class="table table-bordered table-hover table-history mb-0">

                    <thead>

                        <tr>

                            <th width="6%">NO</th>

                            <th width="18%">KODE ASSET</th>

                            <th width="20%">NO INVENTARIS</th>

                            <th width="15%">STATUS</th>

                            <th width="25%">PEMAKAI</th>

                            <th width="16%">TANGGAL KELUAR</th>

                        </tr>

                    </thead>

                    <tbody id="historyTable">

                        @forelse($inventaris as $index => $item)

                            <tr>

                                <td class="text-center">
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    {{ $item->kode_aset }}
                                </td>

                                <td>
                                    {{ $item->no_inventaris }}
                                </td>

                                <td class="text-center">

                                    @switch($item->status)
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

                                </td>

                                <td class="text-center">

                                    @if ($item->keluar)
                                        @if ($item->keluar->jenis_penerima == 'Perorangan')
                                            <span class="badge bg-label-primary px-3 py-2">

                                                {{ $item->keluar->karyawan->nama_karyawan ?? '-' }}

                                            </span>
                                        @else
                                            <span class="badge bg-label-info px-3 py-2">

                                                {{ $item->keluar->divisi_klr ?? '-' }}

                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-success px-3 py-2">

                                            Belum Dipakai

                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    @if ($item->keluar)
                                        {{ \Carbon\Carbon::parse($item->keluar->tgl_keluar)->format('d-m-Y') }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-4 text-muted">

                                        Tidak ada data inventaris

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- FOOTER --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 border-top pt-3">

                    <div class="text-muted">

                        Menampilkan
                        <strong>{{ $totalAset }}</strong>
                        unit asset inventaris

                    </div>

                    <a href="{{ route('transaksi-masuk.stok') }}" class="btn btn-secondary btn-back">

                        <i class="bx bx-arrow-back me-1"></i>

                        Kembali

                    </a>

                </div>

            </div> {{-- tutup card-body --}}
        </div>

    @endsection
    @section('scripts')

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('searchInput');
                const filterStatus = document.getElementById('filterStatus');
                const resetFilter = document.getElementById('resetFilter');

                function filterTable() {

                    const keyword = searchInput.value.toLowerCase();
                    const status = filterStatus.value.toLowerCase();

                    document.querySelectorAll('#historyTable tr').forEach(function(row) {

                        const text = row.innerText.toLowerCase();

                        const matchKeyword =
                            text.includes(keyword);

                        const matchStatus =
                            status === '' ||
                            text.includes(status);

                        row.style.display =
                            (matchKeyword && matchStatus) ?
                            '' :
                            'none';

                    });

                }

                searchInput.addEventListener(
                    'keyup',
                    filterTable
                );

                filterStatus.addEventListener(
                    'change',
                    filterTable
                );

                resetFilter.addEventListener(
                    'click',
                    function() {

                        searchInput.value = '';
                        filterStatus.value = '';

                        filterTable();

                    }
                );

            });
        </script>

    @endsection
