@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Aset Saya - Monitoring Aset IT')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 py-sm-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3 flex-shrink-0 category-theme-icon">
                        <span class="avatar-initial rounded">
                            <i class="bi bi-person-workspace fs-3 text-white"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 fs-5 fs-sm-4" style="color: var(--primary-theme, #0b2f57) !important;">Dashboard & Aset Saya</h4>
                        <small class="text-muted">Ringkasan matriks inventaris perangkat IT yang Anda gunakan dan pinjam</small>
                    </div>
                </div>
                <div>
                    @if ($karyawan)
                        <span class="badge bg-label-success p-2 fs-6">
                            <i class="bi bi-person-badge me-1"></i> {{ $karyawan->nama_karyawan }} (NIK: {{ $karyawan->kode_karyawan ?? '-' }})
                        </span>
                    @else
                        <span class="badge bg-label-warning p-2 fs-6">
                            <i class="bi bi-exclamation-triangle me-1"></i> Belum Terhubung Karyawan
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT UNTUK USER TANPA KARYAWAN_ID --}}
    @if (!$user->karyawan_id)
        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-2 me-3 text-warning flex-shrink-0"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Akun Belum Terhubung dengan Data Karyawan</h5>
                    <p class="mb-0 small">Akun Anda (<strong>{{ $user->username }}</strong>) belum ditautkan dengan Master Data Karyawan. Untuk menampilkan daftar perangkat dan aset yang Anda gunakan, silakan hubungi <strong>Petugas IT Support / Admin</strong> untuk mentautkan profil karyawan Anda.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- SUMMARY CARDS PER KATEGORI (SESUAI TEMA PERUSAHAAN) --}}
    <div class="row g-3 mb-4">
        {{-- Total Semua Aset --}}
        <div class="col-12 col-sm-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100 category-theme-card cursor-pointer active-filter" onclick="filterByCategory('all')" id="card-filter-all">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="dashboard-label text-uppercase d-block mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Total Semua Aset</span>
                        <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                            {{ $totalAset }} <span class="fs-6 fw-normal text-muted">Unit</span>
                        </h3>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $mapings->count() }} Tetap · {{ $peminjamans->where('status', 'dipinjam')->count() }} Pinjam</small>
                    </div>
                    <div class="dashboard-icon category-theme-icon">
                        <i class="bi bi-layers-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laptop --}}
        <div class="col-12 col-sm-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100 category-theme-card cursor-pointer" onclick="filterByCategory('laptop')" id="card-filter-laptop">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="dashboard-label text-uppercase d-block mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Laptop / Notebook</span>
                        <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                            {{ $totalLaptop }} <span class="fs-6 fw-normal text-muted">Unit</span>
                        </h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Perangkat Mobile</small>
                    </div>
                    <div class="dashboard-icon category-theme-icon">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Printer --}}
        <div class="col-12 col-sm-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100 category-theme-card cursor-pointer" onclick="filterByCategory('printer')" id="card-filter-printer">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="dashboard-label text-uppercase d-block mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Printer & Scanner</span>
                        <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                            {{ $totalPrinter }} <span class="fs-6 fw-normal text-muted">Unit</span>
                        </h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Perangkat Cetak</small>
                    </div>
                    <div class="dashboard-icon category-theme-icon">
                        <i class="bi bi-printer"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- HP / Smartphone --}}
        <div class="col-12 col-sm-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100 category-theme-card cursor-pointer" onclick="filterByCategory('hp')" id="card-filter-hp">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="dashboard-label text-uppercase d-block mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Smartphone / HP</span>
                        <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                            {{ $totalHp }} <span class="fs-6 fw-normal text-muted">Unit</span>
                        </h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Gadget & Komunikasi</small>
                    </div>
                    <div class="dashboard-icon category-theme-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- PC / Lainnya --}}
        <div class="col-12 col-sm-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100 category-theme-card cursor-pointer" onclick="filterByCategory('lainnya')" id="card-filter-lainnya">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="dashboard-label text-uppercase d-block mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">PC & Lainnya</span>
                        <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                            {{ $totalPc + $totalLainnya }} <span class="fs-6 fw-normal text-muted">Unit</span>
                        </h3>
                        <small class="text-muted" style="font-size: 0.75rem;">PC, Monitor & Aksesoris</small>
                    </div>
                    <div class="dashboard-icon category-theme-icon">
                        <i class="bi bi-pc-display"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER TABS / PILLS BAR --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="fw-bold small text-muted me-1"><i class="bi bi-funnel me-1"></i>Filter Kategori:</span>
                    <button type="button" class="btn btn-sm btn-primary active category-filter-btn" data-filter="all" onclick="filterByCategory('all')">
                        Semua Kategori ({{ $totalAset }})
                    </button>
                    @if ($totalLaptop > 0)
                        <button type="button" class="btn btn-sm btn-outline-theme category-filter-btn" data-filter="laptop" onclick="filterByCategory('laptop')">
                            <i class="bi bi-laptop me-1"></i> Laptop ({{ $totalLaptop }})
                        </button>
                    @endif
                    @if ($totalPrinter > 0)
                        <button type="button" class="btn btn-sm btn-outline-theme category-filter-btn" data-filter="printer" onclick="filterByCategory('printer')">
                            <i class="bi bi-printer me-1"></i> Printer ({{ $totalPrinter }})
                        </button>
                    @endif
                    @if ($totalHp > 0)
                        <button type="button" class="btn btn-sm btn-outline-theme category-filter-btn" data-filter="hp" onclick="filterByCategory('hp')">
                            <i class="bi bi-phone me-1"></i> HP ({{ $totalHp }})
                        </button>
                    @endif
                    @if (($totalPc + $totalLainnya) > 0)
                        <button type="button" class="btn btn-sm btn-outline-theme category-filter-btn" data-filter="lainnya" onclick="filterByCategory('lainnya')">
                            <i class="bi bi-box-seam me-1"></i> Lainnya ({{ $totalPc + $totalLainnya }})
                        </button>
                    @endif
                </div>
                <div id="filter-active-status" class="small text-muted">
                    Menampilkan <strong>semua aset</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR ASET UTAMA (MAPPING) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 fs-6 fs-sm-5">
                    <i class="bi bi-laptop me-2" style="color: var(--primary-theme, #0b2f57);"></i> Perangkat & Aset Utama Dipakai
                </h5>
                <span class="badge" style="background: linear-gradient(135deg, var(--primary-theme, #0b2f57), var(--secondary-theme, #154b87)); color: #fff;" id="badge-count-mapping">{{ $mapings->count() }} Perangkat</span>
            </div>
        </div>
        
        {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
        <div class="card-body p-0 d-none d-md-block">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0" id="table-mapping-assets">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode / No. Inventaris</th>
                            <th>Nama Aset & Kategori</th>
                            <th>Spesifikasi & Hardware</th>
                            <th>Lokasi Penempatan</th>
                            <th>Tgl Digunakan</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-mapping">
                        @forelse ($mapings as $maping)
                            @php
                                $inventaris = $maping->keluar?->inventaris;
                                $dataAset = $inventaris?->dataAset;
                                $catName = $dataAset?->kategori?->nama_barang ?? $inventaris?->kategori?->nama_barang ?? 'Lainnya';
                                $catKey = strtolower($catName);
                            @endphp
                            <tr class="asset-item-row" data-category="{{ $catKey }}" data-nama="{{ strtolower($dataAset->nama_barang ?? '') }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong style="color: var(--primary-theme, #0b2f57);">{{ $inventaris->no_inventaris ?? '-' }}</strong>
                                    @if ($inventaris?->kode_aset)
                                        <br><small class="text-muted">Kode: {{ $inventaris->kode_aset }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $dataAset->nama_barang ?? 'Perangkat IT' }}</strong>
                                    <br>
                                    <span class="badge bg-label-secondary">
                                        <i class="bi bi-tag me-1"></i>{{ $catName }}
                                    </span>
                                </td>
                                <td>
                                    @if ($maping->processor || $maping->ram || $maping->system)
                                        <small class="d-block"><strong>Proc:</strong> {{ $maping->processor ?? '-' }}</small>
                                        <small class="d-block"><strong>RAM:</strong> {{ $maping->ram ?? '-' }} | <strong>OS:</strong> {{ $maping->system ?? '-' }}</small>
                                        @if ($maping->device_id)
                                            <small class="text-muted"><strong>ID:</strong> {{ $maping->device_id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($maping->lokasi)
                                        <span class="badge bg-label-info"><i class="bi bi-geo-alt me-1"></i>{{ $maping->lokasi->nama_lokasi }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $maping->status ?? 'Aktif' }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('e-ticket.index') }}" class="btn btn-sm btn-outline-warning" title="Laporkan Kendala untuk Perangkat ini">
                                        <i class="bi bi-exclamation-octagon me-1"></i> Lapor Kendala
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row-mapping">
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <span class="text-muted">Belum ada perangkat atau aset utama yang terdaftar atas nama Anda.</span>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="no-match-row-mapping" style="display: none;">
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-search fs-2 d-block mb-2 text-muted"></i>
                                <span class="text-muted">Tidak ada perangkat aset utama pada kategori ini.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MOBILE CARD VIEW (d-block d-md-none) --}}
        <div class="card-body p-3 d-block d-md-none" id="mobile-mapping-container">
            @forelse ($mapings as $maping)
                @php
                    $inventaris = $maping->keluar?->inventaris;
                    $dataAset = $inventaris?->dataAset;
                    $catName = $dataAset?->kategori?->nama_barang ?? $inventaris?->kategori?->nama_barang ?? 'Lainnya';
                    $catKey = strtolower($catName);
                @endphp
                <div class="card border shadow-none mb-3 rounded-3 bg-body asset-item-card" data-category="{{ $catKey }}" data-nama="{{ strtolower($dataAset->nama_barang ?? '') }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">{{ $dataAset->nama_barang ?? 'Perangkat IT' }}</h6>
                                <span class="badge bg-label-primary"><i class="bi bi-barcode me-1"></i>{{ $inventaris->no_inventaris ?? '-' }}</span>
                            </div>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $maping->status ?? 'Aktif' }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Kategori:</small>
                                <span class="badge bg-label-secondary text-truncate max-w-100"><i class="bi bi-tag me-1"></i>{{ $catName }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Penempatan:</small>
                                <span class="badge bg-label-info text-truncate max-w-100"><i class="bi bi-geo-alt me-1"></i>{{ $maping->lokasi->nama_lokasi ?? '-' }}</span>
                            </div>
                            @if ($maping->processor || $maping->ram || $maping->system)
                                <div class="col-12">
                                    <small class="text-muted d-block">Spesifikasi:</small>
                                    <small class="fw-semibold text-dark d-block">{{ $maping->processor ?? '-' }} | {{ $maping->ram ?? '-' }} | {{ $maping->system ?? '-' }}</small>
                                </div>
                            @endif
                            <div class="col-12">
                                <small class="text-muted d-block">Tgl Digunakan:</small>
                                <small class="fw-semibold text-dark">{{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d/m/Y') : '-' }}</small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('e-ticket.index') }}" class="btn btn-warning btn-sm w-100 fw-bold py-2">
                                <i class="bi bi-exclamation-octagon me-1"></i> Lapor Kendala IT
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted" id="mobile-empty-mapping">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada perangkat atau aset utama yang terdaftar.
                </div>
            @endforelse
            <div class="text-center py-4 text-muted" id="mobile-no-match-mapping" style="display: none;">
                <i class="bi bi-search fs-2 d-block mb-2 text-muted"></i>
                Tidak ada perangkat aset utama pada kategori ini.
            </div>
        </div>
    </div>

    {{-- DAFTAR PINJAMAN ASET SEMENTARA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 fs-6 fs-sm-5">
                    <i class="bi bi-clipboard-check me-2" style="color: var(--primary-theme, #0b2f57);"></i> Peminjaman Aset Sementara
                </h5>
                <span class="badge" style="background: linear-gradient(135deg, var(--primary-theme, #0b2f57), var(--secondary-theme, #154b87)); color: #fff;" id="badge-count-loan">{{ $peminjamans->count() }} Transaksi</span>
            </div>
        </div>
        
        {{-- DESKTOP TABLE VIEW --}}
        <div class="card-body p-0 d-none d-md-block">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0" id="table-loan-assets">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode Pinjam</th>
                            <th>Nama Aset & Inventaris</th>
                            <th>Kategori</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Rencana Kembali</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-loan">
                        @forelse ($peminjamans as $pinjam)
                            @php
                                $inventaris = $pinjam->inventaris;
                                $dataAset = $inventaris?->dataAset;
                                $catName = $dataAset?->kategori?->nama_barang ?? $inventaris?->kategori?->nama_barang ?? 'Lainnya';
                                $catKey = strtolower($catName);
                            @endphp
                            <tr class="loan-item-row" data-category="{{ $catKey }}" data-nama="{{ strtolower($dataAset->nama_barang ?? '') }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong style="color: var(--primary-theme, #0b2f57);">{{ $pinjam->kode_peminjaman }}</strong></td>
                                <td>
                                    <strong class="text-dark">{{ $dataAset->nama_barang ?? '-' }}</strong>
                                    @if ($inventaris?->no_inventaris)
                                        <br><small class="text-muted">No: {{ $inventaris->no_inventaris }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">
                                        <i class="bi bi-tag me-1"></i>{{ $catName }}
                                    </span>
                                </td>
                                <td>{{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $pinjam->tanggal_rencana_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $pinjam->keperluan ?? '-' }}</td>
                                <td>
                                    @if ($pinjam->status === 'dipinjam')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Dipinjam</span>
                                    @elseif ($pinjam->status === 'kembali')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Dikembalikan</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($pinjam->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row-loan">
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <span class="text-muted">Tidak ada data peminjaman aset sementara.</span>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="no-match-row-loan" style="display: none;">
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-search fs-2 d-block mb-2 text-muted"></i>
                                <span class="text-muted">Tidak ada peminjaman aset pada kategori ini.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MOBILE CARD VIEW --}}
        <div class="card-body p-3 d-block d-md-none" id="mobile-loan-container">
            @forelse ($peminjamans as $pinjam)
                @php
                    $inventaris = $pinjam->inventaris;
                    $dataAset = $inventaris?->dataAset;
                    $catName = $dataAset?->kategori?->nama_barang ?? $inventaris?->kategori?->nama_barang ?? 'Lainnya';
                    $catKey = strtolower($catName);
                @endphp
                <div class="card border shadow-none mb-3 rounded-3 bg-body loan-item-card" data-category="{{ $catKey }}" data-nama="{{ strtolower($dataAset->nama_barang ?? '') }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="fw-bold d-block" style="color: var(--primary-theme, #0b2f57);">{{ $pinjam->kode_peminjaman }}</small>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $dataAset->nama_barang ?? '-' }}</h6>
                            </div>
                            @if ($pinjam->status === 'dipinjam')
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Dipinjam</span>
                            @elseif ($pinjam->status === 'kembali')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Dikembalikan</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($pinjam->status) }}</span>
                            @endif
                        </div>
                        <hr class="my-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Kategori:</small>
                                <span class="badge bg-label-secondary text-truncate max-w-100"><i class="bi bi-tag me-1"></i>{{ $catName }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Tgl Pinjam:</small>
                                <small class="fw-semibold text-dark">{{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') : '-' }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Tgl Kembali:</small>
                                <small class="fw-semibold text-dark">{{ $pinjam->tanggal_rencana_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</small>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Keperluan:</small>
                                <small class="fw-semibold text-dark">{{ $pinjam->keperluan ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted" id="mobile-empty-loan">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Tidak ada data peminjaman aset sementara.
                </div>
            @endforelse
            <div class="text-center py-4 text-muted" id="mobile-no-match-loan" style="display: none;">
                <i class="bi bi-search fs-2 d-block mb-2 text-muted"></i>
                Tidak ada peminjaman aset pada kategori ini.
            </div>
        </div>
    </div>

</div>

<style>
.category-theme-icon {
    background: linear-gradient(135deg, var(--primary-theme, #0b2f57), var(--secondary-theme, #154b87)) !important;
    color: #fff !important;
    width: 48px;
    height: 48px;
    border-radius: 14px;
    font-size: 1.35rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(11, 47, 87, 0.16);
    flex-shrink: 0;
}
.category-theme-card {
    border-radius: 16px !important;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    border: 1px solid #eef2f7 !important;
}
.category-theme-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
}
.category-theme-card.active-filter {
    border: 2px solid var(--primary-theme, #0b2f57) !important;
    background-color: rgba(11, 47, 87, 0.02) !important;
}
.btn-outline-theme {
    border: 1px solid var(--primary-theme, #0b2f57) !important;
    color: var(--primary-theme, #0b2f57) !important;
    background: transparent;
    transition: all 0.2s ease;
}
.btn-outline-theme:hover {
    background: linear-gradient(135deg, var(--primary-theme, #0b2f57), var(--secondary-theme, #154b87)) !important;
    color: #fff !important;
}
.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
function filterByCategory(categoryKey) {
    const key = categoryKey.toLowerCase();
    
    // Update summary card active styling
    document.querySelectorAll('.category-theme-card').forEach(c => c.classList.remove('active-filter'));
    const activeCard = document.getElementById('card-filter-' + key);
    if (activeCard) {
        activeCard.classList.add('active-filter');
    }

    // Update Filter Buttons state
    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        if (btn.getAttribute('data-filter') === key) {
            btn.classList.add('active', 'btn-primary');
            btn.classList.remove('btn-outline-theme');
        } else {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-outline-theme');
        }
    });

    // Update Status text
    const statusEl = document.getElementById('filter-active-status');
    if (statusEl) {
        if (key === 'all') {
            statusEl.innerHTML = 'Menampilkan <strong>semua aset</strong>';
        } else {
            statusEl.innerHTML = `Filter aktif: <span class="badge" style="background: var(--primary-theme, #0b2f57); color: #fff;">${key.toUpperCase()}</span> <a href="javascript:void(0)" onclick="filterByCategory('all')" class="text-danger ms-1 small text-decoration-underline">Reset</a>`;
        }
    }

    // Helper matcher
    function isMatch(itemCategory, itemNama) {
        if (key === 'all') return true;
        const cat = (itemCategory || '').toLowerCase();
        const nama = (itemNama || '').toLowerCase();
        
        if (key === 'laptop') {
            return cat.includes('laptop') || cat.includes('notebook') || cat.includes('macbook') || nama.includes('laptop') || nama.includes('thinkpad');
        }
        if (key === 'printer') {
            return cat.includes('printer') || cat.includes('scanner') || cat.includes('cetak') || nama.includes('printer') || nama.includes('epson') || nama.includes('canon');
        }
        if (key === 'hp') {
            return cat.includes('hp') || cat.includes('handphone') || cat.includes('smartphone') || cat.includes('phone') || cat.includes('ponsel') || cat.includes('tablet') || cat.includes('ipad') || nama.includes('samsung') || nama.includes('iphone') || nama.includes('redmi') || nama.includes('oppo');
        }
        if (key === 'lainnya') {
            const isLap = cat.includes('laptop') || cat.includes('notebook') || cat.includes('macbook');
            const isPrin = cat.includes('printer') || cat.includes('scanner') || cat.includes('cetak');
            const isPhone = cat.includes('hp') || cat.includes('handphone') || cat.includes('smartphone') || cat.includes('phone') || cat.includes('ponsel') || cat.includes('tablet') || cat.includes('ipad');
            return !isLap && !isPrin && !isPhone;
        }
        return cat.includes(key);
    }

    // Filter Mapping Desktop Table
    let visibleMapping = 0;
    document.querySelectorAll('.asset-item-row').forEach(row => {
        const cat = row.getAttribute('data-category');
        const nama = row.getAttribute('data-nama');
        if (isMatch(cat, nama)) {
            row.style.display = '';
            visibleMapping++;
        } else {
            row.style.display = 'none';
        }
    });
    const noMatchMapping = document.getElementById('no-match-row-mapping');
    if (noMatchMapping) {
        noMatchMapping.style.display = (visibleMapping === 0 && document.querySelectorAll('.asset-item-row').length > 0) ? '' : 'none';
    }

    // Filter Mapping Mobile Cards
    let visibleMobileMapping = 0;
    document.querySelectorAll('.asset-item-card').forEach(card => {
        const cat = card.getAttribute('data-category');
        const nama = card.getAttribute('data-nama');
        if (isMatch(cat, nama)) {
            card.style.display = '';
            visibleMobileMapping++;
        } else {
            card.style.display = 'none';
        }
    });
    const mobileNoMatchMapping = document.getElementById('mobile-no-match-mapping');
    if (mobileNoMatchMapping) {
        mobileNoMatchMapping.style.display = (visibleMobileMapping === 0 && document.querySelectorAll('.asset-item-card').length > 0) ? '' : 'none';
    }

    // Filter Loan Desktop Table
    let visibleLoan = 0;
    document.querySelectorAll('.loan-item-row').forEach(row => {
        const cat = row.getAttribute('data-category');
        const nama = row.getAttribute('data-nama');
        if (isMatch(cat, nama)) {
            row.style.display = '';
            visibleLoan++;
        } else {
            row.style.display = 'none';
        }
    });
    const noMatchLoan = document.getElementById('no-match-row-loan');
    if (noMatchLoan) {
        noMatchLoan.style.display = (visibleLoan === 0 && document.querySelectorAll('.loan-item-row').length > 0) ? '' : 'none';
    }

    // Filter Loan Mobile Cards
    let visibleMobileLoan = 0;
    document.querySelectorAll('.loan-item-card').forEach(card => {
        const cat = card.getAttribute('data-category');
        const nama = card.getAttribute('data-nama');
        if (isMatch(cat, nama)) {
            card.style.display = '';
            visibleMobileLoan++;
        } else {
            card.style.display = 'none';
        }
    });
    const mobileNoMatchLoan = document.getElementById('mobile-no-match-loan');
    if (mobileNoMatchLoan) {
        mobileNoMatchLoan.style.display = (visibleMobileLoan === 0 && document.querySelectorAll('.loan-item-card').length > 0) ? '' : 'none';
    }
}
</script>
@endsection
