@extends('layouts/contentNavbarLayout')

@section('title', 'Pelaksanaan Checklist Device')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ALERT NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- PAGE HEADER (CLEAN & MODERN) --}}
    <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-2 mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item text-secondary">Checklist Device</li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Pelaksanaan Harian</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">Pelaksanaan Checklist Device</h4>
            <p class="text-muted small mb-0">Inspeksi & monitoring fisik perangkat per ruangan terjadwal.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center shadow-xs" id="btnOpenScanner">
                <i class="bx bx-camera me-1"></i> Scan QR Perangkat
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#modalFilter">
                <i class="bx bx-filter-alt me-1"></i> Filter
                @if(request()->anyFilled(['hari', 'id_lokasi', 'status', 'id_perusahaan']) || $modeTanggal === 'all')
                    <span class="badge bg-primary ms-1 px-1 py-0" style="font-size: 0.65rem;">•</span>
                @endif
            </button>
            <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center shadow-xs">
                <i class="bx bx-calendar-repeat me-1"></i> Kelola Jadwal Rutin
            </a>
        </div>
    </div>

    {{-- DATE CONTROLLER & COMPACT STATS STRIP (CLEAN & MINIMALIST) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center gy-2">
                
                {{-- Navigasi Tanggal --}}
                <div class="col-12 col-lg-7 d-flex align-items-center flex-wrap gap-2">
                    {{-- Tombol Prev --}}
                    <a href="{{ route('checklist.pemeriksaan.index', array_merge(request()->except(['tanggal', 'page']), ['tanggal' => $prevDate, 'mode_tanggal' => 'single'])) }}" 
                       class="btn btn-sm btn-outline-secondary d-flex align-items-center px-2 py-1" title="Hari Sebelumnya">
                        <i class="bx bx-chevron-left fs-5"></i>
                    </a>

                    {{-- Tombol Hari Ini --}}
                    <a href="{{ route('checklist.pemeriksaan.index', array_merge(request()->except(['tanggal', 'page', 'hari']), ['tanggal' => date('Y-m-d'), 'mode_tanggal' => 'single'])) }}" 
                       class="btn btn-sm py-1 px-2 {{ $tanggal === date('Y-m-d') && $modeTanggal !== 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Hari Ini
                    </a>

                    {{-- Tombol Next --}}
                    <a href="{{ route('checklist.pemeriksaan.index', array_merge(request()->except(['tanggal', 'page']), ['tanggal' => $nextDate, 'mode_tanggal' => 'single'])) }}" 
                       class="btn btn-sm btn-outline-secondary d-flex align-items-center px-2 py-1" title="Hari Berikutnya">
                        <i class="bx bx-chevron-right fs-5"></i>
                    </a>

                    {{-- Label Tanggal Terpilih --}}
                    <div class="d-flex align-items-center gap-2 ms-1 flex-wrap">
                        @if ($modeTanggal === 'all' || (!empty($hari) && empty(request('tanggal'))))
                            <span class="badge bg-label-warning fw-semibold px-2 py-1">
                                <i class="bx bx-filter me-1"></i> Seluruh Hari: {{ ucfirst($hari) }}
                            </span>
                        @else
                            <span class="fw-bold text-dark fs-6">
                                Hari {{ $namaHariTanggal }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                            </span>
                            @if ($tanggal === date('Y-m-d'))
                                <span class="badge bg-label-success py-1 px-2" style="font-size: 0.72rem;">Hari Ini</span>
                            @endif
                        @endif
                    </div>

                    {{-- Quick Date Input --}}
                    <form action="{{ route('checklist.pemeriksaan.index') }}" method="GET" class="d-inline-flex align-items-center ms-auto ms-lg-2">
                        @if (request()->filled('id_perusahaan'))
                            <input type="hidden" name="id_perusahaan" value="{{ request('id_perusahaan') }}">
                        @endif
                        @if (request()->filled('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if (request()->filled('id_lokasi'))
                            <input type="hidden" name="id_lokasi" value="{{ request('id_lokasi') }}">
                        @endif
                        <input type="date" name="tanggal" value="{{ $tanggal }}" 
                               class="form-control form-control-sm border text-muted" 
                               onchange="this.form.submit()" 
                               style="width: 130px; font-size: 0.8rem;" 
                               title="Pilih tanggal spesifik">
                    </form>
                </div>

                {{-- Metric Counters Mini (Total, Selesai, Sedang Dicek, Belum) --}}
                <div class="col-12 col-lg-5 d-flex justify-content-start justify-content-lg-end align-items-center gap-2 pt-2 pt-lg-0 border-top border-lg-0 border-light flex-wrap">
                    <div class="d-flex align-items-center gap-1 me-2" title="Total Ruangan Terjadwal">
                        <span class="text-muted small">Ruangan:</span>
                        <span class="badge bg-light text-dark fw-bold border">{{ $stats['total'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1" title="Selesai Diperiksa">
                        <span class="text-muted small">Selesai:</span>
                        <span class="badge bg-label-success fw-bold">{{ $stats['selesai'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1" title="Sedang Dicek">
                        <span class="text-muted small">Proses:</span>
                        <span class="badge bg-label-info fw-bold">{{ $stats['sedang_dicek'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1" title="Belum Dicek">
                        <span class="text-muted small">Belum:</span>
                        <span class="badge bg-label-warning fw-bold">{{ $stats['belum_dicek'] }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ACTIVE FILTER BADGES (RINGKAS) --}}
    @if(request()->anyFilled(['hari', 'id_lokasi', 'status', 'bulan', 'tahun', 'id_perusahaan']) || $modeTanggal === 'all')
        <div class="d-flex align-items-center flex-wrap gap-2 mb-3 small">
            <span class="text-muted"><i class="bx bx-filter-alt me-1"></i>Filter Aktif:</span>
            @if(request()->filled('hari'))
                <span class="badge bg-label-primary">Hari: {{ ucfirst(request('hari')) }}</span>
            @endif
            @if(request()->filled('tanggal') && $modeTanggal !== 'all')
                <span class="badge bg-label-info">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</span>
            @endif
            @if(request()->filled('status'))
                <span class="badge bg-label-secondary">Status: {{ str_replace('_', ' ', ucfirst(request('status'))) }}</span>
            @endif
            @if(request()->filled('id_lokasi'))
                @php $lokSelected = $lokasis->firstWhere('id', request('id_lokasi')); @endphp
                @if($lokSelected)
                    <span class="badge bg-label-secondary">Ruangan: {{ $lokSelected->nama_lokasi }}</span>
                @endif
            @endif
            <a href="{{ route('checklist.pemeriksaan.index') }}" class="badge bg-light text-danger border text-decoration-none">
                <i class="bx bx-x me-1"></i>Reset
            </a>
        </div>
    @endif

    {{-- GRID KARTU RUANGAN CHECKLIST (CLEAN & ELEGANT) --}}
    <div class="row g-3">
        @forelse ($ruangans as $ruangan)
            @php
                $pct = $ruangan->persentase;
                $jadwal = $ruangan->jadwal;
                $jadwalRutin = $ruangan->jadwalRutin;
                $tglFormat = $ruangan->tanggal_pemeriksaan 
                    ? $ruangan->tanggal_pemeriksaan->translatedFormat('d M Y') 
                    : ($ruangan->tanggal_cek ? $ruangan->tanggal_cek->translatedFormat('d M Y') : '-');
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 {{ $ruangan->status === 'selesai' ? 'border-start border-success border-4' : ($ruangan->status === 'sedang_dicek' ? 'border-start border-info border-4' : 'border-start border-warning border-4') }}">
                    <div class="card-body p-3 d-flex flex-column">
                        
                        {{-- Baris Atas: Ruangan & Status Badge --}}
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark fs-6">
                                    {{ $ruangan->lokasi->nama_lokasi ?? '-' }}
                                </h6>
                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                    @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $ruangan?->perusahaan)
                                        {{ $ruangan->perusahaan?->nama_perusahaan }} &bull;
                                    @endif
                                    {{ $ruangan->nama_hari ?: ucfirst($ruangan->hari ?: 'Rutin') }}
                                </small>
                            </div>
                            <div>
                                @if ($ruangan->status === 'selesai')
                                    <span class="badge bg-label-success py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="bx bx-check me-1"></i> Selesai
                                    </span>
                                @elseif ($ruangan->status === 'sedang_dicek')
                                    <span class="badge bg-label-info py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="bx bx-loader me-1"></i> Sedang Dicek
                                    </span>
                                @else
                                    <span class="badge bg-label-warning py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="bx bx-time me-1"></i> Belum Dicek
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Metadata Singkat: Petugas & Kondisi --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 pt-1 border-top border-light small text-muted" style="font-size: 0.8rem;">
                            <span class="text-truncate me-2" title="Petugas IT">
                                <i class="bx bx-user me-1"></i>{{ $ruangan->petugas->name ?? ($jadwalRutin->assignedTo->name ?? ($jadwal->assignedTo->name ?? 'Belum Ditugaskan')) }}
                            </span>
                            <span>
                                @if ($ruangan->kondisi_ruangan === 'ada_kendala')
                                    <span class="text-danger fw-semibold"><i class="bx bx-error me-1"></i>Ada Kendala</span>
                                @else
                                    <span class="text-success fw-semibold"><i class="bx bx-check-circle me-1"></i>Normal</span>
                                @endif
                            </span>
                        </div>

                        {{-- Progress Bar Device (Slim) --}}
                        <div class="mb-3 mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.78rem;">
                                <span class="text-muted">Progres Pengecekan:</span>
                                <span class="fw-bold" style="color: {{ $pct == 100 ? '#198754' : 'var(--theme-primary)' }};">
                                    {{ $ruangan->total_checked }}/{{ $ruangan->total_device }} Device ({{ $pct }}%)
                                </span>
                            </div>
                            <div class="progress rounded-pill" style="height: 6px;">
                                <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $pct }}%;" 
                                     aria-valuenow="{{ $pct }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Buka Lembar Checklist --}}
                        <div>
                            @php
                                $showParams = ['id' => $ruangan->id];
                                if (request()->filled('id_perusahaan')) {
                                    $showParams['id_perusahaan'] = request('id_perusahaan');
                                } elseif ($ruangan->id_perusahaan) {
                                    $showParams['id_perusahaan'] = $ruangan->id_perusahaan;
                                }
                            @endphp
                            <a href="{{ route('checklist.pemeriksaan.show', $showParams) }}" 
                               class="btn btn-sm btn-primary w-100 py-1">
                                <i class="bx bx-check-square me-1"></i> Buka Lembar Checklist
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bx bx-calendar-x text-muted fs-1 mb-2"></i>
                        <h6 class="fw-bold mb-1">Tidak Ada Jadwal Checklist Device</h6>
                        <p class="text-muted small mb-3">
                            Pada hari {{ $namaHariTanggal }} ({{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}), belum ada ruangan yang terdaftar dalam jadwal rutin mingguan.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('checklist.jadwal.index', ['tambah' => 1]) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i> Tambah Jadwal Rutin untuk Hari {{ $namaHariTanggal }}
                            </a>
                            <a href="{{ route('checklist.pemeriksaan.index', ['mode_tanggal' => 'all']) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-list-ul me-1"></i> Lihat Semua Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($ruangans->hasPages())
        <div class="mt-4">
            {{ $ruangans->links() }}
        </div>
    @endif

</div>

{{-- MODAL FILTER HARI & TANGGAL CHECKLIST --}}
<div class="modal fade" id="modalFilter" tabindex="-1" aria-labelledby="modalFilterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('checklist.pemeriksaan.index') }}" method="GET" class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 bg-white sticky-top">
                <h5 class="modal-title fw-bold" id="modalFilterTitle">
                    <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Checklist Berdasarkan Hari & Tanggal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3" style="max-height: calc(80vh - 130px); overflow-y: auto;">
                <div class="row g-3">
                    
                    {{-- Mode Pencarian --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Mode Pencarian Tanggal / Hari</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode_tanggal" id="modeSingle" value="single" {{ $modeTanggal !== 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modeSingle">
                                    Pilih Tanggal Spesifik (Harian)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode_tanggal" id="modeAll" value="all" {{ $modeTanggal === 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modeAll">
                                    Pencarian Berdasarkan Hari (Misal: Semua Hari Senin)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Spesifik --}}
                    <div class="col-md-6" id="fieldTanggalContainer">
                        <label class="form-label fw-semibold">Tanggal Spesifik</label>
                        <input type="date" name="tanggal" id="filterInputTanggal" class="form-control" value="{{ $tanggal }}">
                        <div class="form-text small">Sistem otomatis mendeteksi hari untuk tanggal tersebut.</div>
                    </div>

                    {{-- Pilihan Hari --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Filter Hari Tertentu</label>
                        <select name="hari" class="form-select">
                            <option value="">Semua Hari</option>
                            <option value="senin" {{ strtolower($hari) == 'senin' ? 'selected' : '' }}>📅 Hari Senin</option>
                            <option value="selasa" {{ strtolower($hari) == 'selasa' ? 'selected' : '' }}>📅 Hari Selasa</option>
                            <option value="rabu" {{ strtolower($hari) == 'rabu' ? 'selected' : '' }}>📅 Hari Rabu</option>
                            <option value="kamis" {{ strtolower($hari) == 'kamis' ? 'selected' : '' }}>📅 Hari Kamis</option>
                            <option value="jumat" {{ strtolower($hari) == 'jumat' ? 'selected' : '' }}>📅 Hari Jumat</option>
                            <option value="sabtu" {{ strtolower($hari) == 'sabtu' ? 'selected' : '' }}>📅 Hari Sabtu</option>
                            <option value="minggu" {{ strtolower($hari) == 'minggu' ? 'selected' : '' }}>📅 Hari Minggu</option>
                        </select>
                    </div>

                    {{-- Bulan & Tahun (Untuk Riwayat) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bulan (Opsional)</label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @foreach ([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $mNum => $mName)
                                <option value="{{ $mNum }}" {{ request('bulan', $bulan) == $mNum ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ request('tahun', $tahun) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Perusahaan (Super Admin) --}}
                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Perusahaan</label>
                            <select name="id_perusahaan" id="filterPerusahaan" class="form-select">
                                <option value="">Semua Perusahaan</option>
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}" {{ (string)request('id_perusahaan', $perusahaanId) === (string)$p->id ? 'selected' : '' }}>
                                        🏢 {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Ruangan / Lokasi --}}
                    <div class="{{ auth()->user()->role === 'super_admin' ? 'col-md-6' : 'col-12' }}">
                        <label class="form-label fw-semibold">Ruangan / Lokasi</label>
                        <select name="id_lokasi" id="filterLokasi" class="form-select">
                            <option value="">Semua Ruangan</option>
                            @foreach ($lokasis as $lok)
                                <option value="{{ $lok->id }}" 
                                        data-perusahaan="{{ $lok->id_perusahaan }}"
                                        {{ (string)request('id_lokasi', $lokasiId) === (string)$lok->id ? 'selected' : '' }}>
                                    🚪 {{ $lok->nama_lokasi }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $lok->perusahaan) ({{ $lok->perusahaan?->nama_perusahaan }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Pengecekan --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Status Pengecekan</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="belum_dicek" {{ request('status') == 'belum_dicek' ? 'selected' : '' }}>Belum Dicek</option>
                            <option value="sedang_dicek" {{ request('status') == 'sedang_dicek' ? 'selected' : '' }}>Sedang Dicek</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-top py-2 d-flex justify-content-between bg-white sticky-bottom">
                <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-outline-secondary">Reset</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SCANNER QR PERANGKAT --}}
<div class="modal fade" id="modalScannerQR" tabindex="-1" aria-labelledby="modalScannerQRLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center" id="modalScannerQRLabel">
                    <i class="bx bx-camera me-2 fs-4"></i> Scan QR / Barcode Perangkat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <p class="text-muted small mb-2">Arahkan kamera ke stiker barcode/QR yang tertempel pada perangkat.</p>
                <div id="qr-reader" style="width: 100%; max-width: 360px; margin: 0 auto; border-radius: 12px; overflow: hidden;" class="shadow-xs bg-dark"></div>
                <div id="qr-reader-status" class="small text-muted mt-2 fw-semibold">Menghubungkan ke kamera...</div>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterPerusahaan = document.getElementById('filterPerusahaan');
    const filterLokasi = document.getElementById('filterLokasi');

    function filterLokasiOptions() {
        if (!filterLokasi) return;
        const selectedPerusahaan = filterPerusahaan ? filterPerusahaan.value : '';

        Array.from(filterLokasi.options).forEach(opt => {
            if (!opt.value) return;
            const optPerusahaan = opt.getAttribute('data-perusahaan');
            if (!selectedPerusahaan || optPerusahaan === selectedPerusahaan) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
                if (filterLokasi.value === opt.value) {
                    filterLokasi.value = '';
                }
            }
        });
    }

    if (filterPerusahaan) {
        filterPerusahaan.addEventListener('change', filterLokasiOptions);
        filterLokasiOptions();
    }

    // Camera QR Scanner Handler
    let html5QrScanner = null;
    const modalScannerEl = document.getElementById('modalScannerQR');
    const bsModalScanner = modalScannerEl ? new bootstrap.Modal(modalScannerEl) : null;
    const btnOpenScanner = document.getElementById('btnOpenScanner');

    if (btnOpenScanner && modalScannerEl) {
        btnOpenScanner.addEventListener('click', function () {
            bsModalScanner.show();
        });

        modalScannerEl.addEventListener('shown.bs.modal', function () {
            startCameraScanner();
        });

        modalScannerEl.addEventListener('hidden.bs.modal', function () {
            stopCameraScanner();
        });
    }

    function startCameraScanner() {
        const statusEl = document.getElementById('qr-reader-status');
        if (statusEl) statusEl.textContent = 'Menghubungkan ke kamera smartphone/laptop...';

        if (typeof Html5Qrcode === 'undefined') {
            if (statusEl) statusEl.innerHTML = '<span class="text-danger">Library scanner sedang dimuat. Silakan coba sesaat lagi.</span>';
            return;
        }

        if (!html5QrScanner) {
            html5QrScanner = new Html5Qrcode("qr-reader");
        }

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrScanner.start({ facingMode: "environment" }, config, onQrScanned, onQrScanError)
            .then(() => {
                if (statusEl) statusEl.textContent = 'Kamera aktif. Silakan arahkan ke barcode perangkat.';
            })
            .catch(err => {
                html5QrScanner.start({ facingMode: "user" }, config, onQrScanned, onQrScanError)
                    .then(() => {
                        if (statusEl) statusEl.textContent = 'Kamera depan aktif.';
                    })
                    .catch(err2 => {
                        if (statusEl) statusEl.innerHTML = '<span class="text-danger">Kamera tidak dapat diakses atau tidak ada izin browser.</span>';
                    });
            });
    }

    function stopCameraScanner() {
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                html5QrScanner.clear();
            }).catch(e => console.error(e));
        }
    }

    function onQrScanError(err) {
        // Ignore frame scan misses
    }

    function onQrScanned(decodedText) {
        stopCameraScanner();
        if (bsModalScanner) bsModalScanner.hide();

        const statusEl = document.getElementById('qr-reader-status');
        if (statusEl) statusEl.textContent = 'Barcode terdeteksi: ' + decodedText + '. Membuka halaman...';

        if (decodedText.includes('/maping/') || decodedText.includes('/peminjaman/')) {
            window.location.href = decodedText;
        } else {
            window.location.href = "{{ url('/maping') }}/" + encodeURIComponent(decodedText.trim());
        }
    }
});
</script>
@endpush

@endsection
