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

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-success me-3">
                        <span class="avatar-initial rounded">
                            <i class="bx bx-check-square fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Pelaksanaan Checklist Device</h4>
                        <small class="text-muted">Periksa kondisi seluruh perangkat / device pada setiap lokasi ruangan, dan lakukan verifikasi berkala</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-calendar me-1"></i> Kelola Jadwal Mingguan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-secondary me-3">
                            <i class="bx bx-devices fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Lokasi / Ruangan</small>
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
                            <i class="bx bx-check-shield fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Selesai Dicek</small>
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
                            <i class="bx bx-loader fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sedang Berjalan</small>
                            <h5 class="fw-bold text-info mb-0">{{ $stats['sedang_dicek'] }}</h5>
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
                            <i class="bx bx-time-five fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Belum Dicek</small>
                            <h5 class="fw-bold text-warning mb-0">{{ $stats['belum_dicek'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('checklist.pemeriksaan.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="{{ auth()->user()->role === 'super_admin' ? 'col-6 col-md-1' : 'col-6 col-md-2' }}">
                        <label class="form-label small fw-semibold">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
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
                            <select name="id_perusahaan" id="filterPerusahaan" class="form-select form-select-sm">
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
                        <label class="form-label small fw-semibold">Ruangan / Lokasi</label>
                        <select name="id_lokasi" id="filterLokasi" class="form-select form-select-sm">
                            <option value="">Semua Ruangan</option>
                            @foreach ($lokasis as $lok)
                                <option value="{{ $lok->id }}" 
                                        data-perusahaan="{{ $lok->id_perusahaan }}"
                                        {{ (string)request('id_lokasi', $lokasiId) === (string)$lok->id ? 'selected' : '' }}
                                        @if(auth()->user()->role === 'super_admin' && !empty($perusahaanId) && $lok->id_perusahaan != $perusahaanId) style="display: none;" disabled @endif>
                                    {{ $lok->nama_lokasi }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $lok->perusahaan) ({{ $lok->perusahaan?->nama_perusahaan }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold">Status Pengecekan</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="belum_dicek" {{ request('status') == 'belum_dicek' ? 'selected' : '' }}>Belum Dicek</option>
                            <option value="sedang_dicek" {{ request('status') == 'sedang_dicek' ? 'selected' : '' }}>Sedang Dicek</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="{{ (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) ? 'col-6 col-md-2' : 'col-6 col-md-3' }} d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                            <i class="bx bx-filter-alt me-1"></i> Tampilkan
                        </button>
                        <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- GRID KARTU RUANGAN --}}
    <div class="row g-3">
        @forelse ($ruangans as $ruangan)
            @php
                $pct = $ruangan->persentase;
                $jadwal = $ruangan->jadwal;
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 {{ $ruangan->status === 'selesai' ? 'border-start border-success border-4' : ($ruangan->status === 'sedang_dicek' ? 'border-start border-info border-4' : '') }}">
                    <div class="card-body p-4 d-flex flex-column">
                        {{-- Badge Perusahaan (Role Super Admin) --}}
                        @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                            <div class="mb-2">
                                <span class="badge bg-label-secondary">
                                    <i class="bx bx-building me-1"></i>{{ $ruangan->perusahaan?->nama_perusahaan ?? '-' }}
                                </span>
                            </div>
                        @endif

                        {{-- Header Kartu --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md bg-label-primary me-2">
                                    <i class="bx bx-door-open fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">
                                        {{ $ruangan->lokasi->nama_lokasi ?? '-' }}
                                    </h5>
                                    <small class="text-muted">
                                        {{ $jadwal->periode_label ?? 'Jadwal Rutin' }}
                                    </small>
                                </div>
                            </div>
                            <div>
                                @if ($ruangan->status === 'selesai')
                                    <span class="badge bg-label-success">
                                        <i class="bx bx-check-circle me-1"></i> Selesai
                                    </span>
                                @elseif ($ruangan->status === 'sedang_dicek')
                                    <span class="badge bg-label-info">
                                        <i class="bx bx-loader me-1"></i> Berjalan
                                    </span>
                                @else
                                    <span class="badge bg-label-warning">
                                        <i class="bx bx-time me-1"></i> Belum
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Info Petugas & Kondisi --}}
                        <div class="mb-3 small">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Petugas IT:</span>
                                <span class="fw-semibold">
                                    {{ $ruangan->petugas->name ?? ($jadwal->assignedTo->name ?? 'Belum Ditugaskan') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Target Tanggal:</span>
                                <span class="fw-semibold">
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Kondisi Device:</span>
                                <span>
                                    @if ($ruangan->kondisi_ruangan === 'ada_kendala')
                                        <span class="badge bg-label-danger py-0 px-2">Ada Kendala</span>
                                    @else
                                        <span class="badge bg-label-success py-0 px-2">Semua Normal</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Progress Bar Device --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="text-muted">Pemeriksaan Device</span>
                                <span class="fw-bold {{ $pct == 100 ? 'text-success' : 'text-primary' }}">
                                    {{ $ruangan->total_checked }} / {{ $ruangan->total_device }} Device ({{ $pct }}%)
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $pct }}%;" 
                                     aria-valuenow="{{ $pct }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi (Sticky Bottom of Card) --}}
                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('checklist.pemeriksaan.show', $ruangan->id) }}" 
                               class="btn btn-primary flex-grow-1">
                                <i class="bx bx-check-square me-1"></i> Buka Checklist Device
                            </a>
                            <a href="{{ route('checklist.pemeriksaan.cetak', $ruangan->id) }}" 
                               target="_blank" 
                               class="btn btn-outline-secondary" 
                               title="Cetak Berita Acara Checklist Device">
                                <i class="bx bx-printer"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bx bx-devices text-muted fs-1 mb-2"></i>
                        <h6 class="fw-bold mb-1">Belum Ada Pelaksanaan Checklist Device</h6>
                        <p class="text-muted small mb-3">Buat jadwal mingguan terlebih dahulu agar daftar device di setiap lokasi dapat diperiksa.</p>
                        <a href="{{ route('checklist.jadwal.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i> Buat Jadwal Sekarang
                        </a>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterPerusahaan = document.getElementById('filterPerusahaan');
    const filterLokasi = document.getElementById('filterLokasi');

    function filterLokasiOptions() {
        if (!filterLokasi) return;
        const selectedPerusahaan = filterPerusahaan ? filterPerusahaan.value : '';

        Array.from(filterLokasi.options).forEach(opt => {
            if (!opt.value) return; // skip placeholder "Semua Ruangan"
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
        filterPerusahaan.addEventListener('change', function() {
            filterLokasiOptions();
        });
        filterLokasiOptions();
    }

    if (filterLokasi && filterPerusahaan) {
        filterLokasi.addEventListener('change', function() {
            const selectedOpt = filterLokasi.options[filterLokasi.selectedIndex];
            if (selectedOpt && selectedOpt.getAttribute('data-perusahaan')) {
                const pId = selectedOpt.getAttribute('data-perusahaan');
                if (!filterPerusahaan.value && pId) {
                    filterPerusahaan.value = pId;
                    filterLokasiOptions();
                }
            }
        });
    }
});
</script>
@endpush
