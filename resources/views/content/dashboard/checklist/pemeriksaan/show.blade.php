@extends('layouts/contentNavbarLayout')

@section('title', 'Pelaksanaan Checklist Device: ' . ($ruangan->lokasi->nama_lokasi ?? 'Lokasi'))

@section('styles')
<style>
    /* Animasi & Sorotan QR Scanner */
    @keyframes pulseHighlight {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        50% { transform: scale(1.02); box-shadow: 0 0 0 16px rgba(25, 135, 84, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    .pulse-highlight {
        animation: pulseHighlight 1.4s ease-in-out 3;
        border: 2px solid #198754 !important;
    }
    .device-card {
        transition: all 0.3s ease-in-out;
    }
    .progress-bar {
        transition: width 0.5s ease-in-out;
    }
    .qr-proof-box svg {
        width: 100% !important;
        height: auto !important;
        max-width: 170px;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ALERT NOTIFIKASI STANDARD --}}
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
                    <li class="breadcrumb-item"><a href="{{ route('checklist.pemeriksaan.index') }}" class="text-secondary">Checklist Device</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $ruangan->lokasi->nama_lokasi ?? 'Ruangan' }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="fw-bold mb-0 text-dark">{{ $ruangan->lokasi->nama_lokasi ?? '-' }}</h4>
                
                {{-- Badge Status Ruangan --}}
                <span id="badgeRoomStatus" class="badge {{ $ruangan->status === 'selesai' ? 'bg-label-success' : ($ruangan->status === 'sedang_dicek' ? 'bg-label-info' : 'bg-label-warning') }} py-1 px-2" style="font-size: 0.72rem;">
                    @if ($ruangan->status === 'selesai')
                        <i class="bx bx-check-double me-1"></i> Selesai
                    @elseif ($ruangan->status === 'sedang_dicek')
                        <i class="bx bx-loader me-1"></i> Sedang Berjalan
                    @else
                        <i class="bx bx-time me-1"></i> Belum Dicek
                    @endif
                </span>

                {{-- Badge Kondisi Ruangan --}}
                <span id="badgeRoomCondition" class="badge {{ $ruangan->kondisi_ruangan === 'ada_kendala' ? 'bg-label-danger' : 'bg-label-success' }} py-1 px-2" style="font-size: 0.72rem;">
                    @if ($ruangan->kondisi_ruangan === 'ada_kendala')
                        <i class="bx bx-error me-1"></i> Ada Kendala
                    @else
                        <i class="bx bx-check-shield me-1"></i> Semua Normal
                    @endif
                </span>

                <span class="badge bg-label-primary py-1 px-2 d-none d-sm-inline-flex align-items-center" style="font-size: 0.70rem;">
                    <span class="spinner-grow spinner-grow-sm text-primary me-1" style="width: 0.55rem; height: 0.55rem;" role="status"></span>
                    Realtime Active
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $ruangan?->perusahaan)
                    {{ $ruangan->perusahaan?->nama_perusahaan }} &bull;
                @endif
                {{ $ruangan->jadwalRutin ? 'Jadwal Rutin Hari ' . ucfirst($ruangan->jadwalRutin->hari) : ($ruangan->hari ? 'Jadwal Hari ' . ($ruangan->nama_hari ?: ucfirst($ruangan->hari)) : ($ruangan->jadwal->periode_label ?? 'Checklist')) }} &bull;
                Petugas: <strong id="headerPetugasName">{{ $ruangan->petugas->name ?? ($ruangan->jadwalRutin->assignedTo->name ?? ($ruangan->jadwal->assignedTo->name ?? 'Belum Ada')) }}</strong>
                <span id="headerTanggalCekWrap" class="{{ $ruangan->tanggal_cek ? '' : 'd-none' }}">
                    &bull; Selesai: <span id="headerTanggalCek">{{ $ruangan->tanggal_cek ? $ruangan->tanggal_cek->format('d/m/Y H:i') : '' }}</span>
                </span>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center shadow-xs">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('checklist.pemeriksaan.cetak', $ruangan->id) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center shadow-xs">
                <i class="bx bx-printer me-1"></i> Cetak Berita Acara
            </a>
        </div>
    </div>

    {{-- COMPACT PROGRESS & ACTION BAR (REALTIME INTERACTIVE) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center gy-3">
                
                {{-- Progress Bar --}}
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-muted">Progres Pengecekan Perangkat:</span>
                        <span id="progressStatsText" class="fw-bold fs-6 {{ $ruangan->persentase == 100 ? 'text-success' : 'text-primary' }}">
                            {{ $ruangan->total_checked }} / {{ $ruangan->total_device }} Device ({{ $ruangan->persentase }}%)
                        </span>
                    </div>
                    <div class="progress rounded-pill" style="height: 9px;">
                        <div id="roomProgressBar" 
                             class="progress-bar {{ $ruangan->persentase == 100 ? 'bg-success' : 'bg-primary' }}" 
                             role="progressbar" 
                             style="width: {{ $ruangan->persentase }}%;" 
                             aria-valuenow="{{ $ruangan->persentase }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (Scan QR Kamera & 1-Click Massal) --}}
                <div class="col-12 col-md-6 d-flex justify-content-md-end gap-2 flex-wrap">
                    {{-- Tombol Scan QR Kamera Lapangan --}}
                    <button type="button" class="btn btn-sm btn-outline-primary py-2 px-3 d-inline-flex align-items-center justify-content-center" id="btnOpenScanner">
                        <i class="bx bx-camera me-1 fs-5"></i> Scan QR Perangkat
                    </button>

                    @if ($ruangan->total_device > 0)
                        <form action="{{ route('checklist.pemeriksaan.mark-all-ok', $ruangan->id) }}" 
                              method="POST" 
                              id="formMarkAll"
                              class="d-inline-block">
                            @csrf
                            <button type="button" 
                                    id="btnMarkAll" 
                                    class="btn btn-sm {{ $ruangan->status === 'selesai' ? 'btn-outline-danger' : 'btn-success' }} py-2 px-3 d-inline-flex align-items-center justify-content-center" 
                                    data-action="{{ $ruangan->status === 'selesai' ? 'reset' : 'mark_all' }}">
                                @if ($ruangan->status === 'selesai')
                                    <i class="bx bx-undo me-1"></i> Batalkan Semua Normal (Reset)
                                @else
                                    <i class="bx bx-check-double me-1"></i> Tandai Semua Normal
                                @endif
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- DAFTAR DEVICE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark fs-6">
            <i class="bx bx-devices me-1 text-primary"></i> Daftar Perangkat ({{ $ruangan->checklistDevices->count() }} Unit)
        </h6>
        @if (isset($activeMasterItems) && $activeMasterItems->isNotEmpty())
            <small class="text-muted">
                {{ $activeMasterItems->count() }} Standar Pengecekan Terpasang
            </small>
        @endif
    </div>

    <div class="row g-3" id="devicesContainer">
        @forelse ($ruangan->checklistDevices as $index => $device)
            @php
                $inventaris = $device->inventaris;
                $dataAset = $inventaris?->dataAset;
                $jenisAset = $dataAset?->kategori?->nama_barang ?? ($dataAset?->kategori?->nama_kategori ?? 'Perangkat IT');
                $spekAset = trim(($dataAset?->merek ?? '') . ' ' . ($dataAset?->type ?? '') . ' ' . ($dataAset?->warna ?? ''));
                $mapping = $device->maping;
                $isChecked = in_array($device->status_device, ['normal', 'ada_kendala']);
                $checkedBy = $device->checkedBy;

                // Tentukan Icon berdasarkan jenis aset
                $katLower = strtolower($jenisAset);
                $icon = 'bx-laptop';
                if (str_contains($katLower, 'pc') || str_contains($katLower, 'desktop') || str_contains($katLower, 'komputer') || str_contains($katLower, 'computer')) {
                    $icon = 'bx-desktop';
                } elseif (str_contains($katLower, 'printer') || str_contains($katLower, 'scanner')) {
                    $icon = 'bx-printer';
                } elseif (str_contains($katLower, 'server') || str_contains($katLower, 'rack')) {
                    $icon = 'bx-server';
                } elseif (str_contains($katLower, 'switch') || str_contains($katLower, 'router') || str_contains($katLower, 'network')) {
                    $icon = 'bx-network-chart';
                } elseif (str_contains($katLower, 'ups')) {
                    $icon = 'bx-battery-charging';
                }

                $borderClass = $device->status_device === 'normal' 
                    ? 'border-start border-success border-4' 
                    : ($device->status_device === 'ada_kendala' ? 'border-start border-danger border-4' : 'border-start border-warning border-4');

                $avatarBg = $device->status_device === 'normal' 
                    ? 'bg-label-success' 
                    : ($device->status_device === 'ada_kendala' ? 'bg-label-danger' : 'bg-label-warning');

                $qrUrl = $mapping ? route('maping.public_show', $mapping->uuid ?? $mapping->id) : '';
            @endphp

            <div class="col-12 col-lg-6" id="colDevice{{ $device->id }}">
                <div class="card border-0 shadow-sm h-100 device-card {{ $borderClass }}" 
                     id="cardDevice{{ $device->id }}"
                     data-device-id="{{ $device->id }}"
                     data-kode="{{ $inventaris->kode_aset ?? '' }}"
                     data-mapping-id="{{ $mapping->id ?? '' }}"
                     data-mapping-uuid="{{ $mapping->uuid ?? '' }}">
                    
                    {{-- CARD BODY: DEVICE INFO & USER --}}
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-md {{ $avatarBg }} rounded device-avatar" id="avatarDevice{{ $device->id }}">
                                    <i class="bx {{ $icon }} fs-3"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $jenisAset }}
                                        </h6>
                                        @if ($device->is_pinjaman)
                                            <span class="badge bg-label-info fs-tiny" title="Perangkat Pinjaman Sementara">
                                                <i class="bx bx-time-five me-1"></i>PINJAMAN
                                            </span>
                                        @endif
                                        <span class="badge bg-label-secondary fs-tiny">{{ $spekAset ?: '-' }}</span>
                                    </div>
                                    <div class="small text-muted font-monospace mb-1">
                                        <i class="bx bx-barcode me-1"></i>{{ $inventaris->kode_aset ?? '-' }}
                                        @if ($inventaris->no_inventaris)
                                            <span class="text-muted">({{ $inventaris->no_inventaris }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge Device & Bukti QR Code Button --}}
                            <div class="text-end d-flex flex-column align-items-end gap-1">
                                <div id="statusBadgeContainer{{ $device->id }}">
                                    @if ($device->status_device === 'normal')
                                        <span class="badge bg-success text-white py-1 px-2">
                                            <i class="bx bx-check me-1"></i> NORMAL
                                        </span>
                                    @elseif ($device->status_device === 'ada_kendala')
                                        <span class="badge bg-danger text-white py-1 px-2">
                                            <i class="bx bx-x me-1"></i> KENDALA
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning py-1 px-2">
                                            <i class="bx bx-time me-1"></i> BELUM DICEK
                                        </span>
                                    @endif
                                </div>

                                {{-- Tombol Bukti QR Code Mapping & Form F-IT --}}
                                <div class="d-flex gap-1 mt-1 flex-wrap justify-content-end">
                                    @if ($mapping)
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-primary btn-show-qr d-inline-flex align-items-center"
                                                id="btnBuktiQr{{ $device->id }}"
                                                data-device-id="{{ $device->id }}"
                                                data-kode="{{ $inventaris->kode_aset ?? '-' }}"
                                                data-nama="{{ $jenisAset }}"
                                                data-spek="{{ $spekAset }}"
                                                data-kategori="{{ $jenisAset }}"
                                                data-user="{{ $device->nama_pengguna ?? ($mapping->penerima ?? 'Umum') }}"
                                                data-status="{{ $device->status_device }}"
                                                data-waktu="{{ $device->checked_at ? $device->checked_at->format('d M Y, H:i') : 'Belum Dicek' }}"
                                                data-petugas="{{ $checkedBy?->name ?? ($device->checked_at ? ($ruangan->petugas?->name ?? 'Petugas IT') : '-') }}"
                                                data-kendala="{{ $device->catatan_kendala ?? '' }}"
                                                data-qr-url="{{ $qrUrl }}">
                                            <i class="bx bx-qr me-1"></i> Bukti QR
                                        </button>
                                    @endif
                                    @if ($inventaris)
                                        <a href="{{ route('history.perjalanan.dokumen_perawatan.cetak', ['id' => $inventaris->id, 'tahun' => date('Y')]) }}" 
                                           target="_blank"
                                           class="btn btn-xs btn-outline-secondary d-inline-flex align-items-center"
                                           title="Cetak Form Perawatan Tahunan (F-IT-001/00 & F-IT-002/00)">
                                            <i class="bx bx-file me-1"></i> Form F-IT
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- USER PENGGUNA INFO --}}
                        <div class="p-2 mb-2 rounded bg-light border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx {{ $device->is_pinjaman ? 'bx-briefcase text-info' : 'bx-user-circle text-primary' }} fs-4"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.73rem;">
                                        {{ $device->is_pinjaman ? 'Peminjam Aset:' : 'Pengguna Device:' }}
                                    </small>
                                    <span class="fw-semibold text-dark small" id="userDevice{{ $device->id }}">
                                        {{ $device->nama_pengguna ?? ($mapping->penerima ?? 'Umum / Belum Ditugaskan') }}
                                    </span>
                                    @if ($device->is_pinjaman && $device->peminjaman)
                                        <small class="text-info d-block" style="font-size: 0.72rem;">
                                            <i class="bx bx-calendar-check me-1"></i>Batas kembali: {{ \Carbon\Carbon::parse($device->peminjaman->tanggal_rencana_kembali)->format('d M Y') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            @if ($mapping && $mapping->processor)
                                <div class="text-end small text-muted d-none d-sm-block" style="font-size: 0.73rem;">
                                    <span>{{ $mapping->processor }} &bull; {{ $mapping->ram }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- INFORMASI PENGECEKAN KOMPREHENSIF (AUDIT TRAIL) --}}
                        <div class="p-2 mb-3 rounded border check-info-box {{ $isChecked ? 'bg-label-secondary border-primary-subtle' : 'bg-white text-muted' }}" 
                             id="checkInfoBox{{ $device->id }}">
                            @if ($isChecked)
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                                    <div>
                                        <i class="bx bx-calendar-check text-success me-1"></i>
                                        <span class="text-muted">Dicek:</span> 
                                        <strong class="text-dark" id="checkTime{{ $device->id }}">
                                            {{ $device->checked_at ? $device->checked_at->format('d/m/Y H:i') : '-' }}
                                        </strong>
                                    </div>
                                    <div>
                                        <i class="bx bx-user-check text-primary me-1"></i>
                                        <span class="text-muted">Petugas:</span> 
                                        <strong class="text-dark" id="checkOfficer{{ $device->id }}">
                                            {{ $checkedBy?->name ?? ($ruangan->petugas?->name ?? 'Petugas IT') }}
                                        </strong>
                                    </div>
                                </div>
                            @else
                                <div class="small text-muted d-flex align-items-center">
                                    <i class="bx bx-time me-1"></i> 
                                    <span>Perangkat ini belum diperiksa pada jadwal hari ini.</span>
                                </div>
                            @endif
                        </div>

                        {{-- QUICK ACTION BUTTONS (MOBILE FRIENDLY) --}}
                        <div class="d-flex gap-2 mb-3">
                            {{-- 1-Click Normal (OK) / Batalkan Normal --}}
                            <form action="{{ route('checklist.pemeriksaan.device.mark-ok', ['ruanganId' => $ruangan->id, 'deviceId' => $device->id]) }}" 
                                  method="POST" 
                                  class="flex-grow-1 form-mark-device"
                                  id="formMarkDevice{{ $device->id }}"
                                  data-device-id="{{ $device->id }}"
                                  data-kode="{{ $inventaris->kode_aset ?? 'Device' }}"
                                  data-nama="{{ $jenisAset }}{{ $spekAset ? ' ' . $spekAset : '' }}"
                                  data-status="{{ $device->status_device }}">
                                @csrf
                                <button type="button" 
                                        class="btn btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center btn-toggle-mark {{ $device->status_device === 'normal' ? 'btn-success text-white' : 'btn-outline-success' }}"
                                        id="btnToggleMark{{ $device->id }}"
                                        title="{{ $device->status_device === 'normal' ? 'Klik untuk membatalkan tanda normal' : 'Klik untuk menandai perangkat normal' }}">
                                    <i class="bx bx-check-circle me-1 fs-5"></i> 
                                    <span class="btn-text">{{ $device->status_device === 'normal' ? 'Normal (OK)' : 'Tandai Normal (OK)' }}</span>
                                    @if ($device->status_device === 'normal')
                                        <span class="badge bg-white text-success ms-2 px-2 py-1 shadow-xs btn-undo-badge">
                                            <i class="bx bx-undo me-1"></i>Batalkan
                                        </span>
                                    @endif
                                </button>
                            </form>

                            {{-- Buka / Toggle Detail Checklist Items --}}
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary py-2 px-3 fw-semibold d-inline-flex align-items-center"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseDetail{{ $device->id }}" 
                                    aria-expanded="{{ $device->status_device === 'ada_kendala' ? 'true' : 'false' }}">
                                <i class="bx bx-list-check me-1 fs-5 align-middle"></i> Item Cek 
                                <i class="bx bx-chevron-down ms-1"></i>
                            </button>
                        </div>

                        {{-- NOTIFIKASI KENDALA & SHORTCUT MAINTENANCE --}}
                        <div id="kendalaBox{{ $device->id }}" class="{{ $device->status_device === 'ada_kendala' ? '' : 'd-none' }}">
                            <div class="alert alert-danger p-2 mb-3 small d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bx bx-error-circle me-1"></i>
                                    <strong>Kendala:</strong> 
                                    <span id="kendalaText{{ $device->id }}">{{ $device->catatan_kendala ?? 'Ditemukan masalah pada perangkat.' }}</span>
                                </div>
                                <a href="{{ route('maintenance.create') }}?inventaris_id={{ $device->inventaris_id }}&maping_id={{ $device->maping_id }}&keluhan={{ urlencode($device->catatan_kendala ?? 'Kendala saat checklist') }}" 
                                    class="btn btn-xs btn-danger text-white text-nowrap ms-2"
                                    id="btnCreateTicket{{ $device->id }}"
                                    target="_blank">
                                    <i class="bx bx-wrench me-1"></i> Buat Service
                                </a>
                            </div>
                        </div>

                        {{-- COLLAPSIBLE CHECKLIST FORM PER ITEM --}}
                        <div class="collapse {{ $device->status_device === 'ada_kendala' ? 'show' : '' }}" id="collapseDetail{{ $device->id }}">
                            <form action="{{ route('checklist.pemeriksaan.device.update', ['ruanganId' => $ruangan->id, 'deviceId' => $device->id]) }}" 
                                  method="POST" 
                                  class="border-top pt-3 mt-2 form-update-device"
                                  data-device-id="{{ $device->id }}">
                                @csrf

                                <h6 class="small fw-bold mb-2 text-muted text-uppercase">
                                    Item Pemeriksaan Standar
                                </h6>

                                {{-- Daftar Item Cek --}}
                                <div class="list-group list-group-flush mb-3">
                                    @forelse ($device->items as $item)
                                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                                            <div class="me-2">
                                                <span class="small d-block fw-semibold text-dark">{{ $item->nama_item }}</span>
                                                <small class="text-muted">{{ $item->kategori_item }}</small>
                                            </div>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input item-checkbox-{{ $device->id }}" 
                                                       type="checkbox" 
                                                       role="switch" 
                                                       name="items[{{ $item->id }}]" 
                                                       value="1" 
                                                       id="itemSwitch{{ $item->id }}"
                                                       {{ $item->is_ok ? 'checked' : '' }}
                                                       style="width: 2.8em; height: 1.4em;">
                                            </div>
                                        </div>
                                    @empty
                                        <p class="small text-muted py-2">Belum ada item checklist spesifik.</p>
                                    @endforelse
                                </div>

                                {{-- Pilih Status Device & Catatan --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-semibold">Status Akhir Device</label>
                                        <select name="status_device" class="form-select form-select-sm select-status-device" required id="selectStatus{{ $device->id }}">
                                            <option value="normal" {{ $device->status_device === 'normal' ? 'selected' : '' }}>
                                                ✔ Kondisi Baik / Normal
                                            </option>
                                            <option value="ada_kendala" {{ $device->status_device === 'ada_kendala' ? 'selected' : '' }}>
                                                ✖ Ada Kendala / Rusak
                                            </option>
                                            <option value="belum_dicek" {{ $device->status_device === 'belum_dicek' ? 'selected' : '' }}>
                                                ⏳ Belum Diperiksa (Reset / Batalkan)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-semibold">Catatan Kendala (Jika Ada)</label>
                                        <input type="text" 
                                               name="catatan_kendala" 
                                               id="inputCatatan{{ $device->id }}"
                                               class="form-control form-control-sm" 
                                               placeholder="Contoh: Kipas berisik, keyboard error..." 
                                               value="{{ $device->catatan_kendala }}">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-primary px-3 btn-submit-device" id="btnSubmitDevice{{ $device->id }}">
                                        <i class="bx bx-save me-1"></i> Simpan Pengecekan
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bx bx-devices text-muted fs-1 mb-2"></i>
                        <h6 class="fw-bold mb-1">Tidak Ada Device Aktif di Lokasi Ini</h6>
                        <p class="text-muted small mb-0">Belum ada perangkat aset yang di-mapping ke lokasi {{ $ruangan->lokasi->nama_lokasi ?? '-' }}.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- BOTTOM RETURN BAR --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Ruangan
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                <i class="bx bx-home-alt me-1"></i> Dashboard
            </a>
        </div>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- MODAL 1: BUKTI VERIFIKASI DIGITAL (QR CODE MAPPING)                       --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalBuktiQR" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <span class="badge bg-label-success px-2 py-1 rounded-pill small">
                    <i class="bx bx-check-shield me-1"></i> BUKTI CHECKLIST RESMI
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2 px-4 pb-4">
                <h5 class="fw-bold mb-0 text-dark" id="qrModalKodeAset">-</h5>
                <p class="text-muted small mb-3" id="qrModalNamaAset">-</p>

                {{-- Container QR Code SVG --}}
                <div class="p-3 bg-white border rounded shadow-xs d-inline-block mb-3 qr-proof-box" id="qrModalSvgContainer">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="small text-muted mb-3 font-monospace" style="font-size: 0.75rem;">
                    Scan QR ini untuk melihat data live aset pada sistem Mapping.
                </div>

                {{-- Detail Sertifikat Checklist --}}
                <div class="bg-light p-3 rounded text-start small border mb-3">
                    <div class="d-flex justify-content-between mb-1 pb-1 border-bottom">
                        <span class="text-muted">Status Kondisi:</span>
                        <span class="fw-bold" id="qrModalStatus">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 pb-1 border-bottom">
                        <span class="text-muted">Waktu Pengecekan:</span>
                        <span class="fw-semibold text-dark" id="qrModalWaktu">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 pb-1 border-bottom">
                        <span class="text-muted">Petugas IT:</span>
                        <span class="fw-semibold text-dark" id="qrModalPetugas">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 pb-1 border-bottom">
                        <span class="text-muted">Pengguna:</span>
                        <span class="fw-semibold text-dark" id="qrModalPengguna">-</span>
                    </div>
                    <div class="d-flex justify-content-between" id="qrModalKendalaWrap">
                        <span class="text-muted">Catatan:</span>
                        <span class="fw-semibold text-danger" id="qrModalKendala">-</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="#" id="qrModalLinkAset" target="_blank" class="btn btn-sm btn-primary">
                        <i class="bx bx-link-external me-1"></i> Buka Informasi Mapping Aset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 2: SCANNER KAMERA QR DI LAPANGAN (SCAN-TO-CHECKLIST)                 --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalScannerQR" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bx bx-camera me-1 text-primary"></i> Scan QR Perangkat di Lapangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <p class="small text-muted mb-2">Arahkan kamera ke stiker QR Code Mapping pada perangkat PC/laptop.</p>
                <div id="qr-reader" style="width: 100%; min-height: 260px; background: #f8fafc;" class="rounded border overflow-hidden position-relative"></div>
                <div id="qr-reader-status" class="mt-2 small fw-semibold text-muted">Mengaktifkan kamera...</div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup Kamera</button>
                <small class="text-muted">Otomatis mencocokkan device di ruangan ini</small>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- Library QR Scanner CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const RUANGAN_ID = '{{ $ruangan->id }}';

    // Inisialisasi Toast SweetAlert
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
    });

    // =========================================================================
    // 1. HELPER UPDATE DOM REALTIME
    // =========================================================================
    function updateProgressUI(ruangan) {
        if (!ruangan) return;

        // Progress text
        const statText = document.getElementById('progressStatsText');
        if (statText) {
            statText.textContent = `${ruangan.total_checked} / ${ruangan.total_device} Device (${ruangan.persentase}%)`;
            statText.className = `fw-bold fs-6 ${ruangan.persentase === 100 ? 'text-success' : 'text-primary'}`;
        }

        // Progress bar width & color
        const bar = document.getElementById('roomProgressBar');
        if (bar) {
            bar.style.width = `${ruangan.persentase}%`;
            bar.setAttribute('aria-valuenow', ruangan.persentase);
            bar.className = `progress-bar ${ruangan.persentase === 100 ? 'bg-success' : 'bg-primary'}`;
        }

        // Badge Room Status
        const badgeStatus = document.getElementById('badgeRoomStatus');
        if (badgeStatus) {
            if (ruangan.status === 'selesai') {
                badgeStatus.className = 'badge bg-label-success py-1 px-2';
                badgeStatus.innerHTML = '<i class="bx bx-check-double me-1"></i> Selesai';
            } else if (ruangan.status === 'sedang_dicek') {
                badgeStatus.className = 'badge bg-label-info py-1 px-2';
                badgeStatus.innerHTML = '<i class="bx bx-loader me-1"></i> Sedang Berjalan';
            } else {
                badgeStatus.className = 'badge bg-label-warning py-1 px-2';
                badgeStatus.innerHTML = '<i class="bx bx-time me-1"></i> Belum Dicek';
            }
        }

        // Badge Room Condition
        const badgeCondition = document.getElementById('badgeRoomCondition');
        if (badgeCondition) {
            if (ruangan.kondisi_ruangan === 'ada_kendala') {
                badgeCondition.className = 'badge bg-label-danger py-1 px-2';
                badgeCondition.innerHTML = '<i class="bx bx-error me-1"></i> Ada Kendala';
            } else {
                badgeCondition.className = 'badge bg-label-success py-1 px-2';
                badgeCondition.innerHTML = '<i class="bx bx-check-shield me-1"></i> Semua Normal';
            }
        }

        // Tombol Mark All
        const btnMarkAll = document.getElementById('btnMarkAll');
        if (btnMarkAll) {
            if (ruangan.status === 'selesai') {
                btnMarkAll.dataset.action = 'reset';
                btnMarkAll.className = 'btn btn-sm btn-outline-danger py-2 px-3 d-inline-flex align-items-center justify-content-center';
                btnMarkAll.innerHTML = '<i class="bx bx-undo me-1"></i> Batalkan Semua Normal (Reset)';
            } else {
                btnMarkAll.dataset.action = 'mark_all';
                btnMarkAll.className = 'btn btn-sm btn-success py-2 px-3 d-inline-flex align-items-center justify-content-center';
                btnMarkAll.innerHTML = '<i class="bx bx-check-double me-1"></i> Tandai Semua Normal';
            }
        }

        // Header info Petugas & Tanggal Cek
        if (ruangan.petugas_name) {
            const elPetugas = document.getElementById('headerPetugasName');
            if (elPetugas) elPetugas.textContent = ruangan.petugas_name;
        }
        if (ruangan.tanggal_cek) {
            const wrapTgl = document.getElementById('headerTanggalCekWrap');
            const elTgl = document.getElementById('headerTanggalCek');
            if (wrapTgl) wrapTgl.classList.remove('d-none');
            if (elTgl) elTgl.textContent = ruangan.tanggal_cek;
        }
    }

    function updateDeviceCardUI(device) {
        if (!device) return;

        const card = document.getElementById(`cardDevice${device.id}`);
        if (!card) return;

        // Update border class card
        card.classList.remove('border-success', 'border-danger', 'border-warning');
        if (device.status_device === 'normal') {
            card.className = 'card border-0 shadow-sm h-100 device-card border-start border-success border-4';
        } else if (device.status_device === 'ada_kendala') {
            card.className = 'card border-0 shadow-sm h-100 device-card border-start border-danger border-4';
        } else {
            card.className = 'card border-0 shadow-sm h-100 device-card border-start border-warning border-4';
        }

        // Update avatar background
        const avatar = document.getElementById(`avatarDevice${device.id}`);
        if (avatar) {
            avatar.classList.remove('bg-label-success', 'bg-label-danger', 'bg-label-warning');
            if (device.status_device === 'normal') avatar.classList.add('bg-label-success');
            else if (device.status_device === 'ada_kendala') avatar.classList.add('bg-label-danger');
            else avatar.classList.add('bg-label-warning');
        }

        // Update status badge
        const badgeContainer = document.getElementById(`statusBadgeContainer${device.id}`);
        if (badgeContainer) {
            if (device.status_device === 'normal') {
                badgeContainer.innerHTML = '<span class="badge bg-success text-white py-1 px-2"><i class="bx bx-check me-1"></i> NORMAL</span>';
            } else if (device.status_device === 'ada_kendala') {
                badgeContainer.innerHTML = '<span class="badge bg-danger text-white py-1 px-2"><i class="bx bx-x me-1"></i> KENDALA</span>';
            } else {
                badgeContainer.innerHTML = '<span class="badge bg-label-warning py-1 px-2"><i class="bx bx-time me-1"></i> BELUM DICEK</span>';
            }
        }

        // Update Bukti QR button dataset
        const btnBuktiQr = document.getElementById(`btnBuktiQr${device.id}`);
        if (btnBuktiQr) {
            btnBuktiQr.dataset.status = device.status_device;
            btnBuktiQr.dataset.waktu = device.checked_at || 'Belum Dicek';
            btnBuktiQr.dataset.petugas = device.checked_by_name || '-';
            btnBuktiQr.dataset.kendala = device.catatan_kendala || '';
            if (device.qr_url) btnBuktiQr.dataset.qrUrl = device.qr_url;
            if (device.qr_svg) btnBuktiQr.dataset.qrSvg = device.qr_svg;
        }

        // Update Audit Info Box
        const checkInfoBox = document.getElementById(`checkInfoBox${device.id}`);
        if (checkInfoBox) {
            if (device.is_checked) {
                checkInfoBox.className = 'p-2 mb-3 rounded border check-info-box bg-label-secondary border-primary-subtle';
                checkInfoBox.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                        <div>
                            <i class="bx bx-calendar-check text-success me-1"></i>
                            <span class="text-muted">Dicek:</span> 
                            <strong class="text-dark">${device.checked_at || '-'}</strong>
                        </div>
                        <div>
                            <i class="bx bx-user-check text-primary me-1"></i>
                            <span class="text-muted">Petugas:</span> 
                            <strong class="text-dark">${device.checked_by_name || '-'}</strong>
                        </div>
                    </div>
                `;
            } else {
                checkInfoBox.className = 'p-2 mb-3 rounded border check-info-box bg-white text-muted';
                checkInfoBox.innerHTML = `
                    <div class="small text-muted d-flex align-items-center">
                        <i class="bx bx-time me-1"></i> 
                        <span>Perangkat ini belum diperiksa pada jadwal hari ini.</span>
                    </div>
                `;
            }
        }

        // Update Toggle Mark Button & form
        const formMark = document.getElementById(`formMarkDevice${device.id}`);
        const btnToggle = document.getElementById(`btnToggleMark${device.id}`);
        if (formMark && btnToggle) {
            formMark.dataset.status = device.status_device;
            if (device.status_device === 'normal') {
                btnToggle.className = 'btn btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center btn-toggle-mark btn-success text-white';
                btnToggle.title = 'Klik untuk membatalkan tanda normal';
                btnToggle.innerHTML = `
                    <i class="bx bx-check-circle me-1 fs-5"></i> 
                    <span class="btn-text">Normal (OK)</span>
                    <span class="badge bg-white text-success ms-2 px-2 py-1 shadow-xs btn-undo-badge">
                        <i class="bx bx-undo me-1"></i>Batalkan
                    </span>
                `;
            } else {
                btnToggle.className = 'btn btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center btn-toggle-mark btn-outline-success';
                btnToggle.title = 'Klik untuk menandai perangkat normal';
                btnToggle.innerHTML = `
                    <i class="bx bx-check-circle me-1 fs-5"></i> 
                    <span class="btn-text">Tandai Normal (OK)</span>
                `;
            }
        }

        // Update Kendala Box
        const kendalaBox = document.getElementById(`kendalaBox${device.id}`);
        const kendalaText = document.getElementById(`kendalaText${device.id}`);
        if (kendalaBox) {
            if (device.status_device === 'ada_kendala') {
                kendalaBox.classList.remove('d-none');
                if (kendalaText) kendalaText.textContent = device.catatan_kendala || 'Ditemukan masalah pada perangkat.';
                const btnTicket = document.getElementById(`btnCreateTicket${device.id}`);
                if (btnTicket) {
                    btnTicket.href = `{{ route('maintenance.create') }}?inventaris_id=${device.id}&keluhan=${encodeURIComponent(device.catatan_kendala || 'Kendala checklist')}`;
                }
            } else {
                kendalaBox.classList.add('d-none');
            }
        }

        // Update Select & Input in form
        const selectStatus = document.getElementById(`selectStatus${device.id}`);
        if (selectStatus) selectStatus.value = device.status_device;

        const inputCatatan = document.getElementById(`inputCatatan${device.id}`);
        if (inputCatatan && device.catatan_kendala) inputCatatan.value = device.catatan_kendala;

        // Update checkbox items
        if (device.items && Array.isArray(device.items)) {
            device.items.forEach(item => {
                const sw = document.getElementById(`itemSwitch${item.id}`);
                if (sw) sw.checked = !!item.is_ok;
            });
        }
    }

    // =========================================================================
    // 2. AJAX TOGGLE 1-CLICK DEVICE OK / BATALKAN
    // =========================================================================
    document.querySelectorAll('.btn-toggle-mark').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const deviceId = form.dataset.deviceId;
            const kode = form.dataset.kode || 'Perangkat';
            const nama = form.dataset.nama || '';
            const currentStatus = form.dataset.status;
            const url = form.action;

            if (currentStatus === 'normal') {
                Swal.fire({
                    title: 'Batalkan Status Normal?',
                    html: `Apakah Anda yakin ingin membatalkan status normal untuk perangkat <strong>${kode}</strong> ${nama ? '(' + nama + ')' : ''}?<br><small class="text-muted mt-2 d-block">Status perangkat akan dikembalikan ke <b>Belum Dicek</b>.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bx bx-undo me-1"></i> Ya, Batalkan!',
                    cancelButtonText: 'Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeMarkDeviceOk(url, deviceId, form);
                    }
                });
            } else {
                // Instantly execute mark normal
                executeMarkDeviceOk(url, deviceId, form);
            }
        });
    });

    function executeMarkDeviceOk(url, deviceId, form) {
        const btn = document.getElementById(`btnToggleMark${deviceId}`);
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...';
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(res => res.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.success) {
                updateDeviceCardUI(data.device);
                updateProgressUI(data.ruangan);

                Toast.fire({
                    icon: 'success',
                    title: data.message || 'Status berhasil diperbarui'
                });
            } else {
                if (btn) btn.innerHTML = originalHtml;
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan sistem.', 'error');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
            console.error(err);
            Toast.fire({
                icon: 'error',
                title: 'Koneksi gagal saat memperbarui data.'
            });
        });
    }

    // =========================================================================
    // 3. AJAX SUBMIT DETAIL CHECKLIST ITEMS FORM
    // =========================================================================
    document.querySelectorAll('.form-update-device').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const deviceId = this.dataset.deviceId;
            const submitBtn = document.getElementById(`btnSubmitDevice${deviceId}`);
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';
            }

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }

                if (data.success) {
                    updateDeviceCardUI(data.device);
                    updateProgressUI(data.ruangan);

                    // Collapse collapse detail if normal or reset
                    if (data.device.status_device !== 'ada_kendala') {
                        const collapseEl = document.getElementById(`collapseDetail${deviceId}`);
                        if (collapseEl && typeof bootstrap !== 'undefined') {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                            if (bsCollapse) bsCollapse.hide();
                        }
                    }

                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Pengecekan berhasil disimpan!'
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal menyimpan hasil checklist.', 'error');
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan komunikasi dengan server.', 'error');
            });
        });
    });

    // =========================================================================
    // 4. AJAX TANDAI SEMUA NORMAL / RESET MASSAL
    // =========================================================================
    const btnMarkAll = document.getElementById('btnMarkAll');
    if (btnMarkAll) {
        btnMarkAll.addEventListener('click', function (e) {
            e.preventDefault();
            const form = document.getElementById('formMarkAll');
            const action = this.dataset.action;
            const url = form.action;

            if (action === 'reset') {
                Swal.fire({
                    title: 'Batalkan Semua Status Normal?',
                    html: 'Seluruh perangkat pada lokasi ini akan dibatalkan status normalnya dan dikembalikan ke <b>Belum Dicek</b>.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bx bx-undo me-1"></i> Ya, Batalkan Semua',
                    cancelButtonText: 'Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeMarkAll(url);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Tandai Semua Device Normal?',
                    html: 'Seluruh perangkat pada lokasi ini akan diverifikasi dalam kondisi <b>NORMAL / BAIK</b>.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bx bx-check-double me-1"></i> Ya, Tandai Semua',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeMarkAll(url);
                    }
                });
            }
        });
    }

    function executeMarkAll(url) {
        const btn = document.getElementById('btnMarkAll');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses Seluruh Device...';
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(res => res.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.success) {
                updateProgressUI(data.ruangan);
                if (data.devices) {
                    Object.values(data.devices).forEach(dev => {
                        updateDeviceCardUI(dev);
                    });
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2200,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            } else {
                if (btn) btn.innerHTML = originalHtml;
                Swal.fire('Gagal', data.message || 'Gagal memproses aksi massal.', 'error');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
            console.error(err);
            Swal.fire('Error', 'Gagal memproses permintaan massal.', 'error');
        });
    }

    // =========================================================================
    // 5. MODAL BUKTI VERIFIKASI QR CODE MAPPING
    // =========================================================================
    const modalBuktiQREl = document.getElementById('modalBuktiQR');
    const bsModalBuktiQR = modalBuktiQREl ? new bootstrap.Modal(modalBuktiQREl) : null;

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-show-qr');
        if (!btn) return;

        const kode = btn.dataset.kode || '-';
        const nama = btn.dataset.nama || '-';
        const spek = btn.dataset.spek || '';
        const user = btn.dataset.user || '-';
        const status = btn.dataset.status || 'belum_dicek';
        const waktu = btn.dataset.waktu || '-';
        const petugas = btn.dataset.petugas || '-';
        const kendala = btn.dataset.kendala || '';
        const qrUrl = btn.dataset.qrUrl || '#';
        const qrSvg = btn.dataset.qrSvg || null;

        document.getElementById('qrModalKodeAset').textContent = kode;
        document.getElementById('qrModalNamaAset').textContent = spek ? `${nama} (${spek})` : nama;
        document.getElementById('qrModalPengguna').textContent = user;
        document.getElementById('qrModalWaktu').textContent = waktu;
        document.getElementById('qrModalPetugas').textContent = petugas;
        document.getElementById('qrModalLinkAset').href = qrUrl;

        // Status Badge in Modal
        const elStatus = document.getElementById('qrModalStatus');
        if (status === 'normal') {
            elStatus.innerHTML = '<span class="badge bg-success"><i class="bx bx-check me-1"></i> NORMAL (KONDISI BAIK)</span>';
        } else if (status === 'ada_kendala') {
            elStatus.innerHTML = '<span class="badge bg-danger"><i class="bx bx-error me-1"></i> ADA KENDALA</span>';
        } else {
            elStatus.innerHTML = '<span class="badge bg-warning text-dark"><i class="bx bx-time me-1"></i> BELUM DICEK</span>';
        }

        // Catatan Kendala Wrap
        const wrapKendala = document.getElementById('qrModalKendalaWrap');
        const elKendala = document.getElementById('qrModalKendala');
        if (kendala) {
            wrapKendala.classList.remove('d-none');
            elKendala.textContent = kendala;
        } else {
            wrapKendala.classList.add('d-none');
        }

        // Render QR Code SVG
        const containerSvg = document.getElementById('qrModalSvgContainer');
        if (qrSvg) {
            containerSvg.innerHTML = qrSvg;
        } else if (qrUrl && qrUrl !== '#') {
            // Gunakan SVG generator online jika belum terisi di dataset
            containerSvg.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(qrUrl)}" alt="QR Code" style="width: 170px; height: 170px;">`;
        } else {
            containerSvg.innerHTML = '<span class="text-muted small">QR Code tidak tersedia.</span>';
        }

        if (bsModalBuktiQR) {
            bsModalBuktiQR.show();
        }
    });

    // =========================================================================
    // 6. SCANNER KAMERA QR DI LAPANGAN (SCAN-TO-CHECKLIST)
    // =========================================================================
    let html5QrCodeScanner = null;
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
            if (statusEl) statusEl.innerHTML = '<span class="text-danger">Library kamera scanner sedang dimuat. Silakan coba sebentar lagi.</span>';
            return;
        }

        if (!html5QrCodeScanner) {
            html5QrCodeScanner = new Html5Qrcode("qr-reader");
        }

        const config = { fps: 10, qrbox: { width: 220, height: 220 } };

        html5QrCodeScanner.start({ facingMode: "environment" }, config, onQrCodeScanned, onQrCodeScanError)
            .then(() => {
                if (statusEl) statusEl.textContent = 'Kamera aktif. Silakan arahkan ke QR Code perangkat.';
            })
            .catch(err => {
                console.warn('Gagal membuka kamera belakang, mencoba default...', err);
                html5QrCodeScanner.start({ facingMode: "user" }, config, onQrCodeScanned, onQrCodeScanError)
                    .then(() => {
                        if (statusEl) statusEl.textContent = 'Kamera aktif.';
                    })
                    .catch(err2 => {
                        console.error('Camera error:', err2);
                        if (statusEl) statusEl.innerHTML = '<span class="text-danger">Izin kamera tidak diberikan atau perangkat tidak memiliki kamera aktif.</span>';
                    });
            });
    }

    function stopCameraScanner() {
        if (html5QrCodeScanner && html5QrCodeScanner.isScanning) {
            html5QrCodeScanner.stop().then(() => {
                html5QrCodeScanner.clear();
            }).catch(err => console.error('Stop scanner error:', err));
        }
    }

    function onQrCodeScanError(errorMessage) {
        // Abaikan frame yang tidak mengandung QR
    }

    function onQrCodeScanned(decodedText, decodedResult) {
        stopCameraScanner();
        if (bsModalScanner) bsModalScanner.hide();

        const statusEl = document.getElementById('qr-reader-status');
        if (statusEl) statusEl.textContent = 'QR Berhasil dibaca: ' + decodedText;

        // Cari kartu device yang cocok
        let matchedCard = null;
        const allCards = document.querySelectorAll('.device-card');

        allCards.forEach(card => {
            const kode = (card.dataset.kode || '').trim().toLowerCase();
            const uuid = (card.dataset.mappingUuid || '').trim().toLowerCase();
            const id = (card.dataset.mappingId || '').trim();
            const textLower = decodedText.toLowerCase();

            if ((kode && textLower.includes(kode)) || 
                (uuid && textLower.includes(uuid)) || 
                (id && textLower.includes(`/maping/${id}`) || textLower.endsWith(`/${id}`))) {
                matchedCard = card;
            }
        });

        if (matchedCard) {
            // Scroll ke kartu
            matchedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            matchedCard.classList.add('pulse-highlight');
            setTimeout(() => {
                matchedCard.classList.remove('pulse-highlight');
            }, 4500);

            const kodeAset = matchedCard.dataset.kode || 'Perangkat Terpilih';
            const deviceId = matchedCard.dataset.deviceId;

            Swal.fire({
                title: 'Perangkat Ditemukan!',
                html: `QR Code cocok dengan perangkat <strong>${kodeAset}</strong> di ruangan ini.<br>Pilih tindakan pengecekan:`,
                icon: 'success',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#198754',
                denyButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bx bx-check me-1"></i> Langsung Tandai Normal',
                denyButtonText: '<i class="bx bx-list-check me-1"></i> Buka Detail Checklist',
                cancelButtonText: 'Selesai'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formMark = document.getElementById(`formMarkDevice${deviceId}`);
                    if (formMark) {
                        executeMarkDeviceOk(formMark.action, deviceId, formMark);
                    }
                } else if (result.isDenied) {
                    const collapseEl = document.getElementById(`collapseDetail${deviceId}`);
                    if (collapseEl && typeof bootstrap !== 'undefined') {
                        const bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: true });
                        bsCollapse.show();
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ditemukan',
                html: `Hasil scan: <code>${decodedText}</code><br><br>Perangkat ini tidak terdaftar pada sesi checklist ruangan ini.`
            });
        }
    }

    // =========================================================================
    // 7. BACKGROUND POLLING SYNC (UNTUK MULTI-TEKNISI REALTIME)
    // =========================================================================
    const SYNC_URL = '{{ route('checklist.pemeriksaan.sync-status', $ruangan->id) }}';
    let syncTimer = null;

    function pollSync() {
        if (document.hidden) return; // Jangan polling jika tab di minimize

        fetch(SYNC_URL, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateProgressUI(data.ruangan);
                if (data.devices) {
                    Object.values(data.devices).forEach(dev => {
                        updateDeviceCardUI(dev);
                    });
                }
            }
        })
        .catch(err => {
            // Silent error on polling
        });
    }

    // Interval sinkronisasi background setiap 6 detik
    syncTimer = setInterval(pollSync, 6000);

    // Sinkronkan langsung saat tab aktif kembali setelah pengguna scan via smartphone
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            pollSync();
        }
    });
});
</script>
@endsection
