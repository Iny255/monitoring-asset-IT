@extends('layouts/contentNavbarLayout')

@section('title', 'Pelaksanaan Checklist Device: ' . ($ruangan->lokasi->nama_lokasi ?? 'Lokasi'))

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

    {{-- BREADCRUMB & HEADER NAV --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <span class="text-muted fw-light">Pelaksanaan Checklist Device /</span> {{ $ruangan->lokasi->nama_lokasi ?? 'Lokasi' }}
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('checklist.pemeriksaan.cetak', $ruangan->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-printer me-1"></i> Cetak Laporan Device
            </a>
            <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ROOM SUMMARY CARD --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center gy-3">
                <div class="col-12 col-md-7">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-lg bg-label-primary me-3">
                            <i class="bx bx-door-open fs-2"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h4 class="fw-bold mb-0 text-dark">{{ $ruangan->lokasi->nama_lokasi ?? '-' }}</h4>
                                @if ($ruangan->status === 'selesai')
                                    <span class="badge bg-label-success fs-tiny">
                                        <i class="bx bx-check-double me-1"></i> SELESAI DICEK
                                    </span>
                                @elseif ($ruangan->status === 'sedang_dicek')
                                    <span class="badge bg-label-info fs-tiny">
                                        <i class="bx bx-loader me-1"></i> SEDANG BERJALAN
                                    </span>
                                @else
                                    <span class="badge bg-label-warning fs-tiny">
                                        <i class="bx bx-time me-1"></i> BELUM DICEK
                                    </span>
                                @endif

                                @if ($ruangan->kondisi_ruangan === 'ada_kendala')
                                    <span class="badge bg-label-danger fs-tiny">
                                        <i class="bx bx-error me-1"></i> ADA KENDALA
                                    </span>
                                @else
                                    <span class="badge bg-label-success fs-tiny">
                                        <i class="bx bx-check-shield me-1"></i> SEMUA NORMAL
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted d-block mt-1">
                                @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $ruangan?->perusahaan)
                                    <i class="bx bx-building me-1"></i> Perusahaan: <strong>{{ $ruangan->perusahaan?->nama_perusahaan }}</strong> &bull;
                                @endif
                                <i class="bx bx-calendar me-1"></i> {{ $ruangan->jadwal->periode_label ?? 'Jadwal Rutin' }} 
                                &bull; <i class="bx bx-user me-1"></i> Petugas: <strong>{{ $ruangan->petugas->name ?? ($ruangan->jadwal->assignedTo->name ?? 'Belum Ada') }}</strong>
                                @if ($ruangan->tanggal_cek)
                                    &bull; <i class="bx bx-time me-1"></i> Waktu Selesai: <strong>{{ $ruangan->tanggal_cek->format('d M Y, H:i') }}</strong>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Action / Progress --}}
                <div class="col-12 col-md-5 text-md-end">
                    <div class="d-flex flex-column align-items-md-end gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Progress Pemeriksaan:</span>
                            <span class="fw-bold fs-5 {{ $ruangan->persentase == 100 ? 'text-success' : 'text-primary' }}">
                                {{ $ruangan->total_checked }} / {{ $ruangan->total_device }} Device ({{ $ruangan->persentase }}%)
                            </span>
                        </div>
                        <div class="progress w-100" style="height: 10px; max-width: 320px;">
                            <div class="progress-bar {{ $ruangan->persentase == 100 ? 'bg-success' : 'bg-primary' }}" 
                                 role="progressbar" 
                                 style="width: {{ $ruangan->persentase }}%;" 
                                 aria-valuenow="{{ $ruangan->persentase }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>

                        {{-- Tombol Cepat: Tandai Semua OK --}}
                        @if ($ruangan->total_device > 0 && $ruangan->status !== 'selesai')
                            <form action="{{ route('checklist.pemeriksaan.mark-all-ok', $ruangan->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Apakah Anda yakin semua perangkat device di lokasi ini dalam kondisi NORMAL/OK?');"
                                  class="mt-2 w-100" style="max-width: 320px;">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 shadow-sm py-2">
                                    <i class="bx bx-check-double fs-5 me-1"></i> Tandai Semua Device OK (Normal)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SELESAI BANNER (Jika 100% Selesai) --}}
    @if ($ruangan->status === 'selesai')
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 py-3" role="alert">
            <div class="avatar avatar-md bg-success text-white me-3 d-flex align-items-center justify-content-center rounded-circle">
                <i class="bx bx-check-circle fs-3"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading fw-bold mb-1 text-success">Pemeriksaan Device di Lokasi Ini Sudah Selesai (100%)</h6>
                <small class="text-muted">
                    Seluruh perangkat ({{ $ruangan->total_device }} unit) telah diverifikasi oleh 
                    <strong>{{ $ruangan->petugas->name ?? 'Petugas IT' }}</strong> pada {{ $ruangan->tanggal_cek ? $ruangan->tanggal_cek->format('d M Y H:i') : now()->format('d M Y') }}.
                </small>
            </div>
            <a href="{{ route('checklist.pemeriksaan.cetak', $ruangan->id) }}" target="_blank" class="btn btn-sm btn-success text-white">
                <i class="bx bx-printer me-1"></i> Cetak Lembar BAP Device
            </a>
        </div>
    @endif

    {{-- INFO ITEM PEMERIKSAAN AKTIF --}}
    @if (isset($activeMasterItems) && $activeMasterItems->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <span class="small fw-bold text-dark">
                        <i class="bx bx-list-check text-primary me-1 fs-5 align-middle"></i> Poin Pemeriksaan Standar (Total {{ $activeMasterItems->count() }} Item):
                    </span>
                    <a href="{{ route('checklist.item.index') }}" class="small text-primary text-decoration-none">
                        <i class="bx bx-cog me-1"></i>Kelola Master Item Cek &rarr;
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    @foreach ($activeMasterItems as $mIdx => $mItem)
                        <span class="badge bg-label-secondary py-1 px-2 font-monospace" style="font-size: 0.75rem;">
                            {{ $mIdx + 1 }}. {{ $mItem->nama_item }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- DAFTAR DEVICE DI LOKASI --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bx bx-devices me-1 text-primary"></i> Daftar Device di Lokasi Ini ({{ $ruangan->checklistDevices->count() }} Unit)
        </h6>
        <small class="text-muted">Klik pada perangkat untuk memeriksa item checklist atau mencentang status</small>
    </div>

    <div class="row g-3">
        @forelse ($ruangan->checklistDevices as $index => $device)
            @php
                $inventaris = $device->inventaris;
                $dataAset = $inventaris?->dataAset;
                $kategori = $dataAset?->kategori?->nama_kategori ?? 'Aset';
                $mapping = $device->maping;

                // Tentukan Icon berdasarkan kategori
                $katLower = strtolower($kategori);
                $icon = 'bx-laptop';
                if (str_contains($katLower, 'pc') || str_contains($katLower, 'desktop') || str_contains($katLower, 'computer')) {
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
            @endphp

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 {{ $device->status_device === 'normal' ? 'border-start border-success border-4' : ($device->status_device === 'ada_kendala' ? 'border-start border-danger border-4' : 'border-start border-warning border-4') }}">
                    
                    {{-- CARD HEADER: DEVICE INFO & USER --}}
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-md {{ $device->status_device === 'normal' ? 'bg-label-success' : ($device->status_device === 'ada_kendala' ? 'bg-label-danger' : 'bg-label-warning') }} rounded">
                                    <i class="bx {{ $icon }} fs-3"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $dataAset->nama_data_aset ?? 'Perangkat IT' }}
                                        </h6>
                                        <span class="badge bg-label-secondary fs-tiny">{{ $kategori }}</span>
                                    </div>
                                    <div class="small text-muted font-monospace mb-1">
                                        <i class="bx bx-barcode me-1"></i>{{ $inventaris->kode_aset ?? '-' }}
                                        @if ($inventaris->no_inventaris)
                                            <span class="text-muted">({{ $inventaris->no_inventaris }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge Device --}}
                            <div>
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
                        </div>

                        {{-- USER PENGGUNA INFO --}}
                        <div class="p-2 mb-3 rounded bg-light border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bx-user-circle fs-4 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Pengguna Device:</small>
                                    <span class="fw-semibold text-dark">
                                        {{ $device->nama_pengguna ?? ($mapping->penerima ?? 'Umum / Belum Ditugaskan') }}
                                    </span>
                                </div>
                            </div>
                            @if ($mapping && $mapping->processor)
                                <div class="text-end small text-muted d-none d-sm-block" style="font-size: 0.75rem;">
                                    <span>{{ $mapping->processor }} &bull; {{ $mapping->ram }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- QUICK ACTION BUTTONS (MOBILE FRIENDLY) --}}
                        <div class="d-flex gap-2 mb-3">
                            {{-- 1-Click Normal (OK) --}}
                            <form action="{{ route('checklist.pemeriksaan.device.mark-ok', ['ruanganId' => $ruangan->id, 'deviceId' => $device->id]) }}" 
                                  method="POST" 
                                  class="flex-grow-1">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-sm w-100 {{ $device->status_device === 'normal' ? 'btn-success text-white' : 'btn-outline-success' }} py-2 fw-semibold">
                                    <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> 
                                    {{ $device->status_device === 'normal' ? 'Sudah Normal (OK)' : 'Tandai Normal (OK)' }}
                                </button>
                            </form>

                            {{-- Buka / Toggle Detail Checklist Items --}}
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary py-2 px-3 fw-semibold"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseDetail{{ $device->id }}" 
                                    aria-expanded="{{ $device->status_device === 'ada_kendala' ? 'true' : 'false' }}">
                                <i class="bx bx-list-check me-1 fs-5 align-middle"></i> Item Cek 
                                <i class="bx bx-chevron-down ms-1"></i>
                            </button>
                        </div>

                        {{-- NOTIFIKASI KENDALA & SHORTCUT MAINTENANCE --}}
                        @if ($device->status_device === 'ada_kendala')
                            <div class="alert alert-danger p-2 mb-3 small d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bx bx-error-circle me-1"></i>
                                    <strong>Kendala:</strong> {{ $device->catatan_kendala ?? 'Ditemukan masalah pada perangkat.' }}
                                </div>
                                <a href="{{ route('maintenance.create') }}?inventaris_id={{ $device->inventaris_id }}&maping_id={{ $device->maping_id }}&keluhan={{ urlencode($device->catatan_kendala ?? 'Kendala saat checklist') }}" 
                                   class="btn btn-xs btn-danger text-white text-nowrap ms-2">
                                    <i class="bx bx-wrench me-1"></i> Buat Service
                                </a>
                            </div>
                        @endif

                        {{-- COLLAPSIBLE CHECKLIST FORM PER ITEM --}}
                        <div class="collapse {{ $device->status_device === 'ada_kendala' ? 'show' : '' }}" id="collapseDetail{{ $device->id }}">
                            <form action="{{ route('checklist.pemeriksaan.device.update', ['ruanganId' => $ruangan->id, 'deviceId' => $device->id]) }}" 
                                  method="POST" 
                                  class="border-top pt-3 mt-2">
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
                                                <input class="form-check-input" 
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
                                        <select name="status_device" class="form-select form-select-sm" required>
                                            <option value="normal" {{ $device->status_device === 'normal' ? 'selected' : '' }}>
                                                ✔ Kondisi Baik / Normal
                                            </option>
                                            <option value="ada_kendala" {{ $device->status_device === 'ada_kendala' ? 'selected' : '' }}>
                                                ✖ Ada Kendala / Rusak
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-semibold">Catatan Kendala (Jika Ada)</label>
                                        <input type="text" 
                                               name="catatan_kendala" 
                                               class="form-control form-control-sm" 
                                               placeholder="Contoh: Kipas berisik, perlu instal antivirus..." 
                                               value="{{ $device->catatan_kendala }}">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-primary px-3">
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

</div>
@endsection
