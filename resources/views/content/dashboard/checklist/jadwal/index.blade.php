@extends('layouts/contentNavbarLayout')

@section('title', 'Jadwal Pengecekan Device')

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
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-primary me-3">
                        <span class="avatar-initial rounded">
                            <i class="bx bx-calendar-check fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Jadwal Mingguan Pengecekan Device</h4>
                        <small class="text-muted">Atur perencanaan perawatan dan inspeksi device berkala per lokasi setiap minggu</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-task me-1"></i> Pelaksanaan Checklist Device
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                        <i class="bx bx-plus me-1"></i> Buat Jadwal Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-secondary me-3">
                            <i class="bx bx-calendar fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Jadwal</small>
                            <h5 class="fw-bold mb-0">{{ $stats['total'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-success me-3">
                            <i class="bx bx-check-double fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Selesai</small>
                            <h5 class="fw-bold text-success mb-0">{{ $stats['selesai'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-info me-3">
                            <i class="bx bx-loader-circle fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sedang Berjalan</small>
                            <h5 class="fw-bold text-info mb-0">{{ $stats['berjalan'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-warning me-3">
                            <i class="bx bx-time fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Terjadwal</small>
                            <h5 class="fw-bold text-warning mb-0">{{ $stats['terjadwal'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('checklist.jadwal.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="{{ auth()->user()->role === 'super_admin' ? 'col-6 col-md-1' : 'col-6 col-md-2' }}">
                        <label class="form-label small fw-semibold">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ request('tahun', $tahun) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm">
                            <option value="">Semua Bulan</option>
                            @foreach ([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $mNum => $mName)
                                <option value="{{ $mNum }}" {{ request('bulan', $bulan) == $mNum ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold">Perusahaan</label>
                            <select name="id_perusahaan" id="filterPerusahaanJadwal" class="form-select form-select-sm">
                                <option value="">Semua Perusahaan</option>
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}" {{ (string)request('id_perusahaan', $perusahaanId) === (string)$p->id ? 'selected' : '' }}>
                                        🏢 {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="{{ auth()->user()->role === 'super_admin' ? 'col-12 col-md-2' : 'col-12 col-md-3' }}">
                        <label class="form-label small fw-semibold">Ruangan / Tempat</label>
                        <select name="id_lokasi" id="filterLokasiJadwal" class="form-select form-select-sm">
                            <option value="">Semua Ruangan</option>
                            @foreach ($lokasis as $lok)
                                <option value="{{ $lok->id }}" 
                                        data-perusahaan="{{ $lok->id_perusahaan }}"
                                        {{ (string)request('id_lokasi') === (string)$lok->id ? 'selected' : '' }}
                                        @if(auth()->user()->role === 'super_admin' && !empty($perusahaanId) && $lok->id_perusahaan != $perusahaanId) style="display: none;" disabled @endif>
                                     {{ $lok->nama_lokasi }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $lok->perusahaan) ({{ $lok->perusahaan?->nama_perusahaan }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="terjadwal" {{ request('status') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="{{ auth()->user()->role === 'super_admin' ? 'col-6 col-md-2' : 'col-6 col-md-3' }} d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST JADWAL --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Kode & Ruangan</th>
                        @if (auth()->user()->role === 'super_admin')
                            <th>Perusahaan</th>
                        @endif
                        <th>Periode Mingguan</th>
                        <th>Petugas IT</th>
                        <th class="text-center">Progress Device</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwals as $index => $jadwal)
                        @php
                            $ruangan = $jadwal->checklistRuangan;
                            $totalDev = $ruangan ? $ruangan->total_device : 0;
                            $checkedDev = $ruangan ? $ruangan->total_checked : 0;
                            $pct = $totalDev > 0 ? min(100, round(($checkedDev / $totalDev) * 100)) : 0;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $jadwals->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-label-info me-2">
                                        <i class="bx bx-door-open fs-5"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">
                                            {{ $jadwal->lokasi->nama_lokasi ?? '-' }}
                                        </span>
                                        <small class="text-muted font-monospace">{{ $jadwal->kode_jadwal }}</small>
                                    </div>
                                </div>
                            </td>
                            @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                <td>
                                    <span class="badge bg-label-secondary">
                                        <i class="bx bx-building me-1"></i>{{ $jadwal->perusahaan?->nama_perusahaan ?? '-' }}
                                    </span>
                                </td>
                            @endif
                            <td>
                                <div class="badge bg-label-primary mb-1">
                                    Minggu ke-{{ $jadwal->minggu_ke }} ({{ $jadwal->nama_bulan }} {{ $jadwal->tahun }})
                                </div>
                                <div class="small text-muted">
                                    <i class="bx bx-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d M Y') }}
                                </div>
                            </td>
                            <td>
                                @if ($jadwal->assignedTo)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs bg-label-success me-2">
                                            <span class="avatar-initial rounded-circle">{{ substr($jadwal->assignedTo->name, 0, 1) }}</span>
                                        </div>
                                        <span class="small fw-semibold">{{ $jadwal->assignedTo->name }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-label-secondary">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td class="text-center" style="min-width: 140px;">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                        <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-primary' }}" 
                                             role="progressbar" 
                                             style="width: {{ $pct }}%;" 
                                             aria-valuenow="{{ $pct }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="fw-semibold text-muted">{{ $checkedDev }}/{{ $totalDev }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($jadwal->status === 'selesai')
                                    <span class="badge bg-label-success">
                                        <i class="bx bx-check me-1"></i> Selesai
                                    </span>
                                @elseif ($jadwal->status === 'berjalan')
                                    <span class="badge bg-label-info">
                                        <i class="bx bx-loader me-1"></i> Sedang Berjalan
                                    </span>
                                @else
                                    <span class="badge bg-label-warning">
                                        <i class="bx bx-time me-1"></i> Terjadwal
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if ($ruangan)
                                        <a href="{{ route('checklist.pemeriksaan.show', $ruangan->id) }}" 
                                           class="btn btn-sm btn-icon btn-primary" 
                                           title="Pelaksanaan Checklist Device">
                                            <i class="bx bx-task"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('checklist.jadwal.edit', $jadwal->id) }}" 
                                       class="btn btn-sm btn-icon btn-outline-secondary" 
                                       title="Edit Jadwal">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('checklist.jadwal.destroy', $jadwal->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Data pelaksanaan checklist device terkait juga akan terhapus.');"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Jadwal">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'super_admin' ? 8 : 7 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bx bx-calendar-x fs-1 d-block mb-2"></i>
                                    Belum ada jadwal pengecekan untuk periode yang dipilih.
                                </div>
                                <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                                    <i class="bx bx-plus me-1"></i> Buat Jadwal Sekarang
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($jadwals->hasPages())
            <div class="card-footer py-3">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL POP-UP BUAT JADWAL BARU (RESPONSIVE WEB & MOBILE) --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            
            {{-- MODAL HEADER --}}
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary me-2">
                        <i class="bx bx-calendar-plus fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTambahJadwalTitle">Buat Jadwal Checklist Device</h5>
                        <small class="text-muted">Jadwalkan inspeksi rutin device per lokasi / ruangan setiap minggu</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- MODAL BODY --}}
            <form action="{{ route('checklist.jadwal.store') }}" method="POST" id="formModalJadwal">
                @csrf
                <div class="modal-body py-3">
                    
                    {{-- Perusahaan (Jika Super Admin) --}}
                    @if (auth()->user()->role === 'super_admin' && isset($perusahaans) && $perusahaans->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                            <select name="id_perusahaan" id="modalPerusahaan" class="form-select" required>
                                <option value="">-- Pilih Perusahaan --</option>
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}" {{ (old('id_perusahaan', $perusahaanId) == $p->id) ? 'selected' : '' }}>
                                        🏢 {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Lokasi / Ruangan Device --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Lokasi / Ruangan Device <span class="text-danger">*</span>
                        </label>
                        <select name="id_lokasi" id="modalLokasi" class="form-select" required>
                            <option value="">-- Pilih Lokasi / Ruangan --</option>
                            @foreach ($lokasis as $lok)
                                <option value="{{ $lok->id }}" 
                                        data-perusahaan="{{ $lok->id_perusahaan }}" 
                                        {{ old('id_lokasi') == $lok->id ? 'selected' : '' }}>
                                    🚪 {{ $lok->nama_lokasi }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $lok->perusahaan) ({{ $lok->perusahaan?->nama_perusahaan }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">
                            Seluruh device yang ter-mapping aktif di lokasi ini akan otomatis dimasukkan ke checklist.
                        </div>
                    </div>

                    {{-- Periode: Tahun, Bulan, Minggu Ke --}}
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" id="modalTahun" class="form-select" required>
                                @for ($y = ($currentYear ?? date('Y')) - 1; $y <= ($currentYear ?? date('Y')) + 2; $y++)
                                    <option value="{{ $y }}" {{ old('tahun', $currentYear ?? date('Y')) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" id="modalBulan" class="form-select" required>
                                @foreach ([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $mNum => $mName)
                                    <option value="{{ $mNum }}" {{ old('bulan', $currentMonth ?? date('n')) == $mNum ? 'selected' : '' }}>
                                        {{ $mName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Minggu Ke- <span class="text-danger">*</span></label>
                            <select name="minggu_ke" id="modalMinggu" class="form-select" required>
                                <option value="1" {{ old('minggu_ke', 1) == 1 ? 'selected' : '' }}>Minggu ke-1</option>
                                <option value="2" {{ old('minggu_ke', 2) == 2 ? 'selected' : '' }}>Minggu ke-2</option>
                                <option value="3" {{ old('minggu_ke', 3) == 3 ? 'selected' : '' }}>Minggu ke-3</option>
                                <option value="4" {{ old('minggu_ke', 4) == 4 ? 'selected' : '' }}>Minggu ke-4</option>
                                <option value="5" {{ old('minggu_ke', 5) == 5 ? 'selected' : '' }}>Minggu ke-5</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tanggal Mulai & Tanggal Selesai --}}
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" id="modalTanggalMulai" class="form-control" 
                                   value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai (Due Date) <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" id="modalTanggalSelesai" class="form-control" 
                                   value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+6 days'))) }}" required>
                        </div>
                    </div>

                    {{-- Preview Item Checklist yang Diterapkan --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small text-dark">
                                <i class="bx bx-list-check text-primary me-1 fs-5 align-middle"></i> Item Standar yang Diterapkan:
                            </span>
                            <span class="badge bg-primary rounded-pill">
                                {{ isset($masterItems) ? $masterItems->count() : 0 }} Poin Pemeriksaan
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @if (isset($masterItems) && $masterItems->isNotEmpty())
                                @foreach ($masterItems as $mItem)
                                    <span class="badge bg-white text-dark border py-1 px-2 font-monospace" style="font-size: 0.75rem;">
                                        <i class="bx bx-check text-success me-1"></i>{{ $mItem->nama_item }}
                                    </span>
                                @endforeach
                            @else
                                <small class="text-muted">Belum ada item checklist aktif. Kelola di menu Master Item Cek.</small>
                            @endif
                        </div>
                        <div class="mt-2 text-end">
                            <a href="{{ route('checklist.item.index') }}" class="small text-primary text-decoration-none">
                                <i class="bx bx-cog me-1"></i>Kelola Master Item Cek &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Petugas IT Penanggung Jawab --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Petugas IT yang Ditugaskan</label>
                        <select name="assigned_to" id="modalPetugas" class="form-select">
                            <option value="">-- Pilih Petugas IT (Opsional) --</option>
                            @if (isset($petugasList))
                                @foreach ($petugasList as $p)
                                    <option value="{{ $p->id }}" 
                                            data-perusahaan="{{ $p->id_perusahaan }}"
                                            {{ old('assigned_to', auth()->id()) == $p->id ? 'selected' : '' }}>
                                        👤 {{ $p->name }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $p->perusahaan) ({{ $p->perusahaan?->nama_perusahaan }}) @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text small">Teknisi yang bertanggung jawab melakukan pengecekan device di lokasi tersebut.</div>
                    </div>

                    {{-- Catatan Khusus --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Catatan / Instruksi Khusus</label>
                        <textarea name="catatan" rows="2" class="form-control" 
                                  placeholder="Contoh: Periksa suhu ruangan server dan update antivirus pada seluruh PC...">{{ old('catatan') }}</textarea>
                    </div>

                </div>

                {{-- MODAL FOOTER --}}
                <div class="modal-footer border-top py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-check me-1"></i> Buat & Terbitkan Jadwal
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Kalkulator rentang tanggal minggu ke-N
    function updateModalDates() {
        const tahunEl = document.getElementById('modalTahun');
        const bulanEl = document.getElementById('modalBulan');
        const mingguEl = document.getElementById('modalMinggu');
        const startEl = document.getElementById('modalTanggalMulai');
        const endEl = document.getElementById('modalTanggalSelesai');

        if (!tahunEl || !bulanEl || !mingguEl || !startEl || !endEl) return;

        const tahun = parseInt(tahunEl.value);
        const bulan = parseInt(bulanEl.value) - 1; // 0-indexed di JS
        const minggu = parseInt(mingguEl.value);

        let startDay = 1 + (minggu - 1) * 7;
        let startDate = new Date(tahun, bulan, startDay);
        let endDate = new Date(tahun, bulan, startDay + 6);

        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        startEl.value = formatDate(startDate);
        endEl.value = formatDate(endDate);
    }

    document.getElementById('modalTahun')?.addEventListener('change', updateModalDates);
    document.getElementById('modalBulan')?.addEventListener('change', updateModalDates);
    document.getElementById('modalMinggu')?.addEventListener('change', updateModalDates);

    // Filter Ruangan & Petugas berdasarkan Perusahaan (Role: super_admin)
    const perusahaanEl = document.getElementById('modalPerusahaan');
    const lokasiEl = document.getElementById('modalLokasi');
    const petugasEl = document.getElementById('modalPetugas');

    function filterModalOptions() {
        if (!perusahaanEl || !lokasiEl) return;
        const selectedPerusahaan = perusahaanEl.value;

        // Filter Lokasi
        Array.from(lokasiEl.options).forEach(opt => {
            if (!opt.value) return; // skip placeholder
            const optPerusahaan = opt.getAttribute('data-perusahaan');
            if (!selectedPerusahaan || optPerusahaan === selectedPerusahaan) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
                if (lokasiEl.value === opt.value) {
                    lokasiEl.value = '';
                }
            }
        });

        // Filter Petugas
        if (petugasEl) {
            Array.from(petugasEl.options).forEach(opt => {
                if (!opt.value) return;
                const optPerusahaan = opt.getAttribute('data-perusahaan');
                if (!selectedPerusahaan || !optPerusahaan || optPerusahaan === selectedPerusahaan) {
                    opt.style.display = '';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                    if (petugasEl.value === opt.value) {
                        petugasEl.value = '';
                    }
                }
            });
        }
    }

    if (perusahaanEl) {
        perusahaanEl.addEventListener('change', filterModalOptions);
        filterModalOptions();
    }

    // Auto-select Perusahaan jika user memilih Lokasi terlebih dahulu
    if (lokasiEl && perusahaanEl) {
        lokasiEl.addEventListener('change', function() {
            const selectedOpt = lokasiEl.options[lokasiEl.selectedIndex];
            if (selectedOpt && selectedOpt.getAttribute('data-perusahaan')) {
                const pId = selectedOpt.getAttribute('data-perusahaan');
                if (perusahaanEl.value !== pId) {
                    perusahaanEl.value = pId;
                    filterModalOptions();
                }
            }
        });
    }

    // Filter Bar Perusahaan & Lokasi (Role: super_admin)
    const filterPerusahaanJadwal = document.getElementById('filterPerusahaanJadwal');
    const filterLokasiJadwal = document.getElementById('filterLokasiJadwal');

    function filterBarLokasiOptions() {
        if (!filterLokasiJadwal) return;
        const selectedPerusahaan = filterPerusahaanJadwal ? filterPerusahaanJadwal.value : '';

        Array.from(filterLokasiJadwal.options).forEach(opt => {
            if (!opt.value) return;
            const optPerusahaan = opt.getAttribute('data-perusahaan');
            if (!selectedPerusahaan || optPerusahaan === selectedPerusahaan) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
                if (filterLokasiJadwal.value === opt.value) {
                    filterLokasiJadwal.value = '';
                }
            }
        });
    }

    if (filterPerusahaanJadwal) {
        filterPerusahaanJadwal.addEventListener('change', filterBarLokasiOptions);
        filterBarLokasiOptions();
    }

    if (filterLokasiJadwal && filterPerusahaanJadwal) {
        filterLokasiJadwal.addEventListener('change', function() {
            const selectedOpt = filterLokasiJadwal.options[filterLokasiJadwal.selectedIndex];
            if (selectedOpt && selectedOpt.getAttribute('data-perusahaan')) {
                const pId = selectedOpt.getAttribute('data-perusahaan');
                if (!filterPerusahaanJadwal.value && pId) {
                    filterPerusahaanJadwal.value = pId;
                    filterBarLokasiOptions();
                }
            }
        });
    }

    // Auto-open modal jika ada parameter ?tambah=1 atau jika ada error validasi
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const shouldOpen = urlParams.get('tambah') === '1' || {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }};
        if (shouldOpen) {
            const modalEl = document.getElementById('modalTambahJadwal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    });
</script>
@endpush

@endsection
