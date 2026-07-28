@extends('layouts/contentNavbarLayout')

@section('title', 'Riwayat Perjalanan Aset')

@section('content')
    <style>
        .summary-card {
            border: none;
            border-radius: 14px;
            transition: .2s;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
        }
        .summary-card:hover {
            transform: translateY(-3px);
        }
        .summary-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
            font-size: 24px;
        }
        .badge-status {
            font-size: 11px;
            padding: 6px 10px;
        }
    </style>

    @php
        $first = $inventaris->first();
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 py-4">
                <div>
                    <h3 class="fw-bold mb-1">Riwayat Perjalanan Aset: {{ $first->kode_aset ?? '-' }}</h3>
                    <div class="text-muted">
                        No. Inventaris: {{ $first->no_inventaris ?? '-' }} • {{ $first->dataAset->kategori->nama_barang ?? '-' }} • {{ $first->dataAset->merek ?? '-' }} {{ $first->dataAset->type ?? '-' }} • {{ $first->perusahaan->nama_perusahaan ?? '-' }}
                    </div>
                </div>
                <div>
                    <a href="{{ route('history.perjalanan.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- INFORMASI ASET --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="fw-bold mb-0 text-primary">Informasi Detail Unit Aset</h6>
            </div>
            <div class="card-body py-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kode Aset</small>
                        <h6 class="fw-bold mb-0 text-primary">{{ $first->kode_aset ?? '-' }}</h6>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">No. Inventaris</small>
                        <h6 class="fw-bold mb-0">{{ $first->no_inventaris ?? '-' }}</h6>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kategori & Merek</small>
                        <h6 class="fw-bold mb-0">{{ $first->dataAset->kategori->nama_barang ?? '-' }} - {{ $first->dataAset->merek ?? '-' }} {{ $first->dataAset->type ?? '-' }}</h6>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Status Saat Ini</small>
                        @if($first->status == 'TERSEDIA')
                            <span class="badge bg-success">TERSEDIA</span>
                        @elseif($first->status == 'DIPAKAI')
                            <span class="badge bg-primary">DIPAKAI</span>
                        @elseif($first->status == 'DIPINJAM')
                            <span class="badge bg-info">DIPINJAM</span>
                        @elseif($first->status == 'RUSAK')
                            <span class="badge bg-danger">RUSAK</span>
                        @elseif($first->status == 'AFKIR')
                            <span class="badge bg-secondary">AFKIR</span>
                        @else
                            <span class="badge bg-label-secondary">{{ $first->status }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-md col-sm-6">
                <div class="card summary-card">
                    <div class="card-body text-center p-3">
                        <div class="summary-icon bg-primary text-white mb-2">
                            <i class="bx bx-box-arrow-up"></i>
                        </div>
                        <small class="text-muted d-block">Total Keluar</small>
                        <h4 class="text-primary fw-bold mb-0">{{ $totalKeluar }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md col-sm-6">
                <div class="card summary-card">
                    <div class="card-body text-center p-3">
                        <div class="summary-icon bg-warning text-dark mb-2">
                            <i class="bx bx-transfer"></i>
                        </div>
                        <small class="text-muted d-block">Mutasi</small>
                        <h4 class="text-warning fw-bold mb-0">{{ $totalMutasi }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md col-sm-6">
                <div class="card summary-card">
                    <div class="card-body text-center p-3">
                        <div class="summary-icon bg-info text-white mb-2">
                            <i class="bx bx-wrench"></i>
                        </div>
                        <small class="text-muted d-block">Maintenance</small>
                        <h4 class="text-info fw-bold mb-0">{{ $totalMaintenance }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md col-sm-6">
                <div class="card summary-card">
                    <div class="card-body text-center p-3">
                        <div class="summary-icon bg-dark text-white mb-2">
                            <i class="bx bx-key"></i>
                        </div>
                        <small class="text-muted d-block">Hak Akses</small>
                        <h4 class="text-dark fw-bold mb-0">{{ $totalHakAkses ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md col-sm-6">
                <div class="card summary-card">
                    <div class="card-body text-center p-3">
                        <div class="summary-icon bg-danger text-white mb-2">
                            <i class="bx bx-power-off"></i>
                        </div>
                        <small class="text-muted d-block">Pencabutan</small>
                        <h4 class="text-danger fw-bold mb-0">{{ $totalCabut }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- TIMELINE TABLE & FILTER --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bx bx-time-five me-2"></i> Timeline Kronologis Perjalanan Unit
                    </h5>
                    <div style="width:300px">
                        <input type="text" id="searchTimeline" class="form-control" placeholder="Cari user, aktivitas, keterangan...">
                    </div>
                </div>

                {{-- FORM FILTER --}}
                <form method="GET" action="{{ route('history.perjalanan.show', $id) }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dari Tanggal</label>
                            <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sampai Tanggal</label>
                            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Aktivitas</label>
                            <select name="aktivitas" class="form-select">
                                <option value="">-- Semua Aktivitas --</option>
                                <option value="MASUK" {{ request('aktivitas') == 'MASUK' ? 'selected' : '' }}>MASUK (Penerimaan)</option>
                                <option value="KELUAR" {{ request('aktivitas') == 'KELUAR' ? 'selected' : '' }}>KELUAR (Pemakaian)</option>
                                <option value="HAK AKSES" {{ request('aktivitas') == 'HAK AKSES' ? 'selected' : '' }}>HAK AKSES & APLIKASI</option>
                                <option value="MUTASI" {{ request('aktivitas') == 'MUTASI' ? 'selected' : '' }}>MUTASI</option>
                                <option value="MAINTENANCE" {{ request('aktivitas') == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE / SERVIS</option>
                                <option value="PENCABUTAN" {{ request('aktivitas') == 'PENCABUTAN' ? 'selected' : '' }}>PENCABUTAN</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('history.perjalanan.show', $id) }}" class="btn btn-label-secondary">
                                Reset
                            </a>
                            <a href="{{ route('history.perjalanan.cetak', array_merge(['id' => $id], request()->query())) }}" target="_blank" class="btn btn-danger" title="Cetak PDF">
                                <i class="bx bxs-file-pdf"></i>
                            </a>
                            <a href="{{ route('history.perjalanan.export_excel', array_merge(['id' => $id], request()->query())) }}" class="btn btn-success" title="Export Excel">
                                <i class="bx bxs-file-export"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="110">TANGGAL</th>
                                <th width="140">KODE ASET</th>
                                <th width="130">AKTIVITAS</th>
                                <th>USER ASET</th>
                                <th>LOKASI</th>
                                <th>HAK AKSES</th>
                                <th>KETERANGAN</th>
                                <th width="120">PETUGAS</th>
                            </tr>
                        </thead>
                        <tbody id="timelineBody">
                            @forelse($timeline as $item)
                                <tr>
                                    <td class="text-center font-monospace">
                                        {{ $item['tanggal']->format('d-m-Y') }}
                                    </td>
                                    <td>
                                        @if ($item['aktivitas'] == 'MUTASI' && !empty($item['kode_aset_lama']) && $item['kode_aset_lama'] != $item['kode_aset_baru'])
                                            <div class="fw-bold text-primary">{{ $item['kode_aset_lama'] }}</div>
                                            <div class="text-center my-1"><i class="bx bx-down-arrow-alt text-secondary"></i></div>
                                            <div class="fw-bold text-success">{{ $item['kode_aset_baru'] }}</div>
                                        @else
                                            <strong class="font-monospace">{{ $item['kode_aset'] }}</strong>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($item['aktivitas'])
                                            @case('MASUK')
                                                <span class="badge bg-success badge-status"><i class="bx bx-box-arrow-in-down me-1"></i> MASUK</span>
                                                @break
                                            @case('KELUAR')
                                                <span class="badge bg-primary badge-status"><i class="bx bx-box-arrow-up me-1"></i> KELUAR</span>
                                                @break
                                            @case('HAK AKSES')
                                                <span class="badge bg-dark badge-status"><i class="bx bx-key me-1"></i> HAK AKSES</span>
                                                @break
                                            @case('MUTASI')
                                                <span class="badge bg-warning text-dark badge-status"><i class="bx bx-transfer me-1"></i> MUTASI</span>
                                                @break
                                            @case('MAINTENANCE')
                                                <span class="badge bg-info badge-status"><i class="bx bx-wrench me-1"></i> SERVIS</span>
                                                @break
                                            @case('PENCABUTAN')
                                                <span class="badge bg-danger badge-status"><i class="bx bx-power-off me-1"></i> PENCABUTAN</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $item['aktivitas'] }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><i class="bx bx-user me-1"></i>{{ $item['user_baru'] ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark"><i class="bx bx-map-pin me-1"></i>{{ $item['lokasi_baru'] ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $item['hak_akses'] ?? '-' }}</div>
                                    </td>
                                    <td>
                                        {{ $item['keterangan'] }}
                                        @if (!empty($item['gambar']))
                                            <div class="mt-1">
                                                <a href="{{ asset('storage/' . $item['gambar']) }}" target="_blank" class="badge bg-label-info text-info text-decoration-none">
                                                    <i class="bx bx-image me-1"></i> Foto Bukti
                                                </a>
                                            </div>
                                        @endif
                                        @if (!empty($item['maintenance_id']))
                                            <div class="mt-1">
                                                <a href="{{ route('maintenance.show', $item['maintenance_id']) }}" class="small text-primary fw-semibold text-decoration-none" title="Lihat Detail Servis">
                                                    <i class="bx bx-show me-1"></i> Detail Servis
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small class="fw-semibold text-dark">{{ $item['petugas'] ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bx bx-history fs-1 d-block mb-2"></i>
                                        Belum ada riwayat perjalanan aset yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('searchTimeline');
            if (search) {
                search.addEventListener('keyup', function() {
                    let keyword = this.value.toLowerCase();
                    document.querySelectorAll('#timelineBody tr').forEach(function(row) {
                        row.style.display = row.innerText.toLowerCase().includes(keyword) ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endsection
