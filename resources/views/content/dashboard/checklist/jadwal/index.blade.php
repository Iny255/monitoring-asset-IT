@extends('layouts/contentNavbarLayout')

@section('title', 'Jadwal Rutin Mingguan Pengecekan Device')

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

    {{-- PAGE HEADER (CLEAN & MODERN) --}}
    <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-2 mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item text-secondary">Checklist Device</li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Jadwal Rutin</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">Jadwal Rutin Mingguan Device</h4>
            <p class="text-muted small mb-0">Atur ruangan yang diperiksa secara otomatis setiap minggunya.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center shadow-xs">
                <i class="bx bx-task me-1"></i> Pelaksanaan Checklist
            </a>
            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                <i class="bx bx-plus me-1"></i> Tambah Jadwal Rutin
            </button>
        </div>
    </div>

    {{-- FILTER & COMPACT METRIC BAR (CLEAN & MINIMALIST) --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center gy-2">
                
                {{-- Filter Perusahaan & Ruangan --}}
                <div class="col-12 col-lg-7">
                    <form action="{{ route('checklist.jadwal.index') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="input-group input-group-sm" style="max-width: 220px;">
                                <span class="input-group-text bg-light text-muted"><i class="bx bx-buildings"></i></span>
                                <select name="id_perusahaan" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Perusahaan</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}" {{ (string)request('id_perusahaan') === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="input-group input-group-sm" style="max-width: 200px;">
                            <span class="input-group-text bg-light text-muted"><i class="bx bx-door-open"></i></span>
                            <select name="id_lokasi" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Ruangan</option>
                                @foreach ($lokasis as $lok)
                                    @if (!request('id_perusahaan') || $lok->id_perusahaan == request('id_perusahaan'))
                                        <option value="{{ $lok->id }}" {{ (string)request('id_lokasi') === (string)$lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        @if (request()->anyFilled(['id_perusahaan', 'id_lokasi', 'hari', 'assigned_to']))
                            <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-sm btn-outline-danger py-1 px-2 d-inline-flex align-items-center">
                                <i class="bx bx-x me-1"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Metric Counters Mini --}}
                <div class="col-12 col-lg-5 d-flex justify-content-start justify-content-lg-end align-items-center gap-2 pt-2 pt-lg-0 border-top border-lg-0 border-light flex-wrap">
                    <div class="d-flex align-items-center gap-1 me-2" title="Total Jadwal Rutin">
                        <span class="text-muted small">Total:</span>
                        <span class="badge bg-light text-dark fw-bold border">{{ $stats['total_jadwal'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1" title="Jadwal Aktif">
                        <span class="text-muted small">Aktif:</span>
                        <span class="badge bg-label-success fw-bold">{{ $stats['total_aktif'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1" title="Ruangan Tercover">
                        <span class="text-muted small">Lokasi:</span>
                        <span class="badge bg-label-info fw-bold">{{ $stats['total_lokasi'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1" title="Hari Terisi">
                        <span class="text-muted small">Hari:</span>
                        <span class="badge bg-label-warning fw-bold">{{ $stats['hari_terisi'] }}/7</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        #jadwalHariTabs .nav-link {
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.85rem;
            color: #4b5563;
            transition: all 0.2s ease;
        }
        #jadwalHariTabs .nav-link.active {
            background: linear-gradient(135deg, var(--theme-primary), var(--theme-secondary)) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(var(--theme-primary-rgb), 0.3) !important;
        }
        #jadwalHariTabs .nav-link.active .badge {
            background-color: #ffffff !important;
            color: var(--theme-primary) !important;
        }
        #jadwalHariTabs .nav-link:not(.active):hover {
            color: var(--theme-primary) !important;
            background-color: rgba(var(--theme-primary-rgb), 0.06) !important;
        }
        @media (max-width: 768px) {
            #jadwalHariTabs {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
                gap: 4px;
            }
            #jadwalHariTabs .nav-item {
                flex: 0 0 auto !important;
            }
            #jadwalHariTabs .nav-link {
                white-space: nowrap !important;
                padding: 6px 10px !important;
                font-size: 0.8rem !important;
            }
        }
    </style>

    {{-- NAVIGASI TAB HARI (SENIN S/D MINGGU) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom p-2 p-md-3">
            <ul class="nav nav-pills nav-fill flex-wrap gap-1" id="jadwalHariTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="tab-semua" data-bs-toggle="pill" data-bs-target="#content-semua" type="button" role="tab">
                        <i class="bx bx-grid-alt me-1"></i> Semua Hari
                        <span class="badge bg-primary text-white ms-1 rounded-pill">{{ $stats['total_jadwal'] }}</span>
                    </button>
                </li>
                @foreach (['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $hVal => $hLabel)
                    @php $countH = isset($jadwalPerHari[$hVal]) ? $jadwalPerHari[$hVal]->count() : 0; @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-{{ $hVal }}" data-bs-toggle="pill" data-bs-target="#content-{{ $hVal }}" type="button" role="tab">
                            {{ $hLabel }}
                            @if ($countH > 0)
                                <span class="badge bg-label-primary ms-1 rounded-pill">{{ $countH }}</span>
                            @else
                                <span class="badge bg-label-secondary ms-1 rounded-pill">0</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content p-0" id="jadwalHariTabsContent">

                {{-- TAB: SEMUA HARI --}}
                <div class="tab-pane fade show active" id="content-semua" role="tabpanel">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">No</th>
                                    <th>Hari Rutin</th>
                                    <th>Lokasi / Ruangan Device</th>
                                    @if (auth()->user()->role === 'super_admin')
                                        <th>Perusahaan</th>
                                    @endif
                                    <th>Petugas IT Penanggung Jawab</th>
                                    <th>Jam Pengecekan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allRutins as $index => $jadwal)
                                    @include('content.dashboard.checklist.jadwal._row_jadwal', ['jadwal' => $jadwal, 'index' => $index + 1])
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role === 'super_admin' ? 8 : 7 }}" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bx bx-calendar-x fs-1 d-block mb-2"></i>
                                                Belum ada jadwal rutin mingguan yang dibuat.
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                                                <i class="bx bx-plus me-1"></i> Buat Jadwal Rutin Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB PER HARI (SENIN S/D MINGGU) --}}
                @foreach (['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $hVal => $hLabel)
                    @php $items = $jadwalPerHari[$hVal] ?? collect(); @endphp
                    <div class="tab-pane fade" id="content-{{ $hVal }}" role="tabpanel">
                        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-dark">
                                    <i class="bx bx-calendar me-1 text-primary"></i> Jadwal Tetap Hari {{ $hLabel }}
                                </span>
                                <small class="text-muted ms-2">({{ $items->count() }} Ruangan terdaftar)</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-tambah-spesifik" data-hari="{{ $hVal }}">
                                <i class="bx bx-plus me-1"></i> Tambah Ruangan Hari {{ $hLabel }}
                            </button>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">No</th>
                                        <th>Lokasi / Ruangan Device</th>
                                        @if (auth()->user()->role === 'super_admin')
                                            <th>Perusahaan</th>
                                        @endif
                                        <th>Petugas IT Penanggung Jawab</th>
                                        <th>Jam Pengecekan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $index => $jadwal)
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-label-info me-2">
                                                        <i class="bx bx-door-open fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark d-block">
                                                            {{ $jadwal->lokasi->nama_lokasi ?? '-' }}
                                                        </span>
                                                        @if ($jadwal->catatan)
                                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                                                <i class="bx bx-note me-1"></i>{{ $jadwal->catatan }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            @if (auth()->user()->role === 'super_admin')
                                                <td>
                                                    <span class="badge bg-label-secondary">
                                                        <i class="bx bx-building me-1"></i>{{ $jadwal->perusahaan?->nama_perusahaan ?? '-' }}
                                                    </span>
                                                </td>
                                            @endif
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
                                            <td>
                                                <small class="text-muted font-monospace">
                                                    <i class="bx bx-time-five me-1"></i>
                                                    {{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '08:00' }} - {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '17:00' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('checklist.jadwal.toggle', $jadwal->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Klik untuk ubah status">
                                                        @if ($jadwal->is_active)
                                                            <span class="badge bg-label-success">
                                                                <i class="bx bx-check-circle me-1"></i> Aktif
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-secondary">
                                                                <i class="bx bx-pause-circle me-1"></i> Nonaktif
                                                            </span>
                                                        @endif
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('checklist.jadwal.edit', $jadwal->id) }}" 
                                                       class="btn btn-sm btn-icon btn-outline-secondary" 
                                                       title="Edit Jadwal Rutin">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <form action="{{ route('checklist.jadwal.destroy', $jadwal->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal rutin {{ $jadwal->lokasi->nama_lokasi ?? '' }} di hari {{ $hLabel }}?\n\n(Catatan: Riwayat pelaksanaan checklist yang sudah ada tidak akan ikut terhapus)');"
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Jadwal Rutin">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->role === 'super_admin' ? 7 : 6 }}" class="text-center py-4">
                                                <div class="text-muted small">
                                                    <i class="bx bx-calendar-plus fs-3 d-block mb-1"></i>
                                                    Belum ada ruangan yang dijadwalkan pada hari {{ $hLabel }}.
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-tambah-spesifik" data-hari="{{ $hVal }}">
                                                    <i class="bx bx-plus me-1"></i> Tambah Ruangan untuk Hari {{ $hLabel }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

{{-- MODAL POP-UP TAMBAH JADWAL RUTIN (INPUT SEKALI BERLAKU SELAMANYA) --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form action="{{ route('checklist.jadwal.store') }}" method="POST" id="formModalJadwal" class="modal-content border-0 shadow">
            @csrf
            
            {{-- MODAL HEADER --}}
            <div class="modal-header border-bottom py-3 bg-white sticky-top">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary me-2">
                        <i class="bx bx-calendar-plus fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTambahJadwalTitle">Tambah Jadwal Rutin Mingguan</h5>
                        <small class="text-muted">Jadwalkan ruangan pada hari tertentu. Otomatis berlaku setiap minggu selamanya.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- MODAL BODY --}}
            <div class="modal-body py-3" style="max-height: calc(80vh - 130px); overflow-y: auto;">
                
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

                {{-- Pilihan Hari Rutin --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Pilih Hari Rutin Pengecekan <span class="text-danger">*</span>
                    </label>
                    <select name="hari" id="modalHari" class="form-select" required>
                        <option value="">-- Pilih Hari --</option>
                        <option value="senin" {{ old('hari') == 'senin' ? 'selected' : '' }}>📅 Hari Senin</option>
                        <option value="selasa" {{ old('hari') == 'selasa' ? 'selected' : '' }}>📅 Hari Selasa</option>
                        <option value="rabu" {{ old('hari') == 'rabu' ? 'selected' : '' }}>📅 Hari Rabu</option>
                        <option value="kamis" {{ old('hari') == 'kamis' ? 'selected' : '' }}>📅 Hari Kamis</option>
                        <option value="jumat" {{ old('hari') == 'jumat' ? 'selected' : '' }}>📅 Hari Jumat</option>
                        <option value="sabtu" {{ old('hari') == 'sabtu' ? 'selected' : '' }}>📅 Hari Sabtu</option>
                        <option value="minggu" {{ old('hari') == 'minggu' ? 'selected' : '' }}>📅 Hari Minggu</option>
                    </select>
                    <div class="form-text small">Ruangan ini akan selalu diperiksa pada hari tersebut di setiap minggunya.</div>
                </div>

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
                        Seluruh device yang ter-mapping aktif di lokasi ini akan otomatis dimasukkan ke lembar pemeriksaan. Anda dapat mendaftarkan beberapa ruangan di hari yang sama.
                    </div>
                </div>

                {{-- Petugas IT Penanggung Jawab --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Petugas IT Penanggung Jawab (Opsional)</label>
                    <select name="assigned_to" id="modalPetugas" class="form-select">
                        <option value="">-- Pilih Petugas IT (Opsional) --</option>
                        @if (isset($petugasList))
                            @foreach ($petugasList as $p)
                                <option value="{{ $p->id }}" 
                                        data-perusahaan="{{ $p->id_perusahaan }}"
                                        {{ old('assigned_to', auth()->id()) == $p->id ? 'selected' : '' }}>
                                    👤 {{ $p->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-text small">Teknisi utama yang bertanggung jawab pada jadwal hari tersebut.</div>
                </div>

                {{-- Jam Mulai & Jam Selesai --}}
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Jam Mulai Pengecekan</label>
                        <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '08:00') }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Jam Selesai (Batas Akhir)</label>
                        <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', '17:00') }}">
                    </div>
                </div>

                {{-- Preview Item Checklist yang Diterapkan --}}
                <div class="mb-3 p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold small text-dark">
                            <i class="bx bx-list-check text-primary me-1 fs-5 align-middle"></i> Item Standar Pemeriksaan:
                        </span>
                        <span class="badge bg-primary rounded-pill">
                            {{ isset($masterItems) ? $masterItems->count() : 0 }} Poin
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
                </div>

                {{-- Catatan Khusus --}}
                <div class="mb-2">
                    <label class="form-label fw-semibold">Catatan / Instruksi Khusus</label>
                    <textarea name="catatan" rows="2" class="form-control" 
                              placeholder="Contoh: Pastikan suhu AC stabil dan kabel LAN tertata rapi...">{{ old('catatan') }}</textarea>
                </div>

            </div>

            {{-- MODAL FOOTER --}}
            <div class="modal-footer border-top py-2 d-flex justify-content-between bg-white sticky-bottom">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bx bx-check me-1"></i> Simpan Jadwal Rutin
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalHari = document.getElementById('modalHari');
    const modalTambahJadwal = document.getElementById('modalTambahJadwal');
    const modalInstance = modalTambahJadwal ? new bootstrap.Modal(modalTambahJadwal) : null;

    // Tombol Cepat Tambah Ruangan di Hari Spesifik
    document.querySelectorAll('.btn-tambah-spesifik').forEach(btn => {
        btn.addEventListener('click', function() {
            const hari = this.getAttribute('data-hari');
            if (modalHari && hari) {
                modalHari.value = hari;
            }
            if (modalInstance) {
                modalInstance.show();
            }
        });
    });

    // Filter Perusahaan & Lokasi untuk Super Admin
    const perusahaanEl = document.getElementById('modalPerusahaan');
    const lokasiEl = document.getElementById('modalLokasi');
    const petugasEl = document.getElementById('modalPetugas');

    function filterModalOptions() {
        if (!perusahaanEl || !lokasiEl) return;
        const selectedPerusahaan = perusahaanEl.value;

        Array.from(lokasiEl.options).forEach(opt => {
            if (!opt.value) return;
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

    // Auto-open modal jika ?tambah=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tambah') === '1' && modalInstance) {
        modalInstance.show();
    }
});
</script>
@endpush

@endsection
