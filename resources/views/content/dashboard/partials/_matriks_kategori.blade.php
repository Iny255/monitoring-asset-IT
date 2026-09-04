@php
    $matriksKategori = $dashboard['matriks_kategori'] ?? null;
    $summary = $matriksKategori['summary'] ?? [];
    $totalAset = $matriksKategori['total_aset'] ?? 0;
@endphp

@if ($matriksKategori)
    {{-- MATRIKS RINGKASAN KATEGORI UTAMA (SESUAI TEMA PERUSAHAAN) --}}
    <div class="row g-3 mb-4">
        {{-- Laptop --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dashboard-card h-100 border-0 shadow-sm category-theme-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="dashboard-label text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Laptop / Notebook</span>
                            <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                                {{ number_format($summary['laptop']['total'] ?? 0) }} <span class="fs-6 fw-normal text-muted">Unit</span>
                            </h3>
                        </div>
                        <div class="dashboard-icon category-theme-icon">
                            <i class="bi bi-laptop"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.75rem;">
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Ready: <strong>{{ $summary['laptop']['tersedia'] ?? 0 }}</strong></span>
                        <span class="text-warning"><i class="bi bi-person me-1"></i>Dipakai: <strong>{{ $summary['laptop']['dipakai'] ?? 0 }}</strong></span>
                        <span class="text-info"><i class="bi bi-clock-history me-1"></i>Pinjam: <strong>{{ $summary['laptop']['dipinjam'] ?? 0 }}</strong></span>
                        @if (($summary['laptop']['rusak'] ?? 0) > 0)
                            <span class="text-danger"><i class="bi bi-wrench me-1"></i>Rusak: <strong>{{ $summary['laptop']['rusak'] }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Printer --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dashboard-card h-100 border-0 shadow-sm category-theme-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="dashboard-label text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Printer & Scanner</span>
                            <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                                {{ number_format($summary['printer']['total'] ?? 0) }} <span class="fs-6 fw-normal text-muted">Unit</span>
                            </h3>
                        </div>
                        <div class="dashboard-icon category-theme-icon">
                            <i class="bi bi-printer"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.75rem;">
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Ready: <strong>{{ $summary['printer']['tersedia'] ?? 0 }}</strong></span>
                        <span class="text-warning"><i class="bi bi-person me-1"></i>Dipakai: <strong>{{ $summary['printer']['dipakai'] ?? 0 }}</strong></span>
                        <span class="text-info"><i class="bi bi-clock-history me-1"></i>Pinjam: <strong>{{ $summary['printer']['dipinjam'] ?? 0 }}</strong></span>
                        @if (($summary['printer']['rusak'] ?? 0) > 0)
                            <span class="text-danger"><i class="bi bi-wrench me-1"></i>Rusak: <strong>{{ $summary['printer']['rusak'] }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- HP / Smartphone --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dashboard-card h-100 border-0 shadow-sm category-theme-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="dashboard-label text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">Smartphone & Gadget</span>
                            <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                                {{ number_format($summary['hp']['total'] ?? 0) }} <span class="fs-6 fw-normal text-muted">Unit</span>
                            </h3>
                        </div>
                        <div class="dashboard-icon category-theme-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.75rem;">
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Ready: <strong>{{ $summary['hp']['tersedia'] ?? 0 }}</strong></span>
                        <span class="text-warning"><i class="bi bi-person me-1"></i>Dipakai: <strong>{{ $summary['hp']['dipakai'] ?? 0 }}</strong></span>
                        <span class="text-info"><i class="bi bi-clock-history me-1"></i>Pinjam: <strong>{{ $summary['hp']['dipinjam'] ?? 0 }}</strong></span>
                        @if (($summary['hp']['rusak'] ?? 0) > 0)
                            <span class="text-danger"><i class="bi bi-wrench me-1"></i>Rusak: <strong>{{ $summary['hp']['rusak'] }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PC & Kategori Lainnya --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dashboard-card h-100 border-0 shadow-sm category-theme-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="dashboard-label text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #697a8d;">PC, Server & Lainnya</span>
                            <h3 class="dashboard-number mb-0" style="color: var(--primary-theme, #0b2f57); font-weight: 700;">
                                {{ number_format(($summary['pc']['total'] ?? 0) + ($summary['lainnya']['total'] ?? 0)) }} <span class="fs-6 fw-normal text-muted">Unit</span>
                            </h3>
                        </div>
                        <div class="dashboard-icon category-theme-icon">
                            <i class="bi bi-pc-display"></i>
                        </div>
                    </div>
                    @php
                        $pcLainTersedia = ($summary['pc']['tersedia'] ?? 0) + ($summary['lainnya']['tersedia'] ?? 0);
                        $pcLainDipakai = ($summary['pc']['dipakai'] ?? 0) + ($summary['lainnya']['dipakai'] ?? 0);
                        $pcLainDipinjam = ($summary['pc']['dipinjam'] ?? 0) + ($summary['lainnya']['dipinjam'] ?? 0);
                        $pcLainRusak = ($summary['pc']['rusak'] ?? 0) + ($summary['lainnya']['rusak'] ?? 0);
                    @endphp
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.75rem;">
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Ready: <strong>{{ $pcLainTersedia }}</strong></span>
                        <span class="text-warning"><i class="bi bi-person me-1"></i>Dipakai: <strong>{{ $pcLainDipakai }}</strong></span>
                        <span class="text-info"><i class="bi bi-clock-history me-1"></i>Pinjam: <strong>{{ $pcLainDipinjam }}</strong></span>
                        @if ($pcLainRusak > 0)
                            <span class="text-danger"><i class="bi bi-wrench me-1"></i>Rusak: <strong>{{ $pcLainRusak }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .category-theme-icon {
            background: linear-gradient(135deg, var(--primary-theme, #0b2f57), var(--secondary-theme, #154b87)) !important;
            color: #fff !important;
            width: 50px;
            height: 50px;
            border-radius: 14px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(11, 47, 87, 0.18);
        }
        .category-theme-card {
            border-radius: 16px !important;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .category-theme-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
@endif
