@extends('layouts/contentNavbarLayout')

@section('title', 'History Perjalanan Aset')

@section('content')

    <style>
        .table td {
            vertical-align: middle;
        }

        .badge-count {
            font-size: 13px;
            padding: 6px 10px;
        }
    </style>

    <div class="container-fluid">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-history fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">History Perjalanan Aset</h3>
                            <small class="text-muted">Audit trail & log riwayat perjalanan hidup seluruh unit aset (Masuk -> Keluar -> Mutasi -> Maintenance -> Pencabutan)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('history.perjalanan.export_excel_index', request()->query()) }}">
                                        <i class="bx bxs-file-export me-2 text-success"></i> Export Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('history.perjalanan.cetak_index', request()->query()) }}" target="_blank">
                                        <i class="bx bxs-file-pdf me-2 text-danger"></i> Cetak PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-company-filter-banner />

        @if(request()->anyFilled(['perusahaan_id', 'kategori_id', 'search']))
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-label-primary px-3 py-2">
                    <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                </span>
                <a href="{{ route('history.perjalanan.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-x me-1"></i> Reset Filter
                </a>
            </div>
        @endif

        {{-- TABLE DAFTAR UNIT ASET --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">NO</th>
                                <th>KODE ASET / INVENTARIS</th>
                                @if (in_array($user->role, ['super_admin', '1', 1]) || !$user->id_perusahaan)
                                    <th>PERUSAHAAN</th>
                                @endif
                                <th>KATEGORI BARANG</th>
                                <th>MEREK & TYPE</th>
                                <th class="text-center">STATUS SAAT INI</th>
                                <th>PEMAKAI SAAT INI</th>
                                <th width="140" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventarisList as $index => $inv)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $inventarisList->firstItem() + $index }}</td>
                                    <td>
                                        <span class="fw-bold text-primary d-block">{{ $inv->kode_aset ?? '-' }}</span>
                                        <small class="text-muted">{{ $inv->no_inventaris ?? '-' }}</small>
                                    </td>
                                    @if (in_array($user->role, ['super_admin', '1', 1]) || !$user->id_perusahaan)
                                        <td>
                                            <x-company-badge :perusahaan="$inv->perusahaan" />
                                        </td>
                                    @endif
                                    <td>
                                        <span class="badge bg-label-info fs-6 fw-semibold">
                                            {{ $inv->dataAset?->kategori?->nama_barang ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">
                                            {{ $inv->dataAset?->merek ?? '-' }}
                                        </span>
                                        <small class="text-muted">{{ $inv->dataAset?->type ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($inv->status == 'TERSEDIA')
                                            <span class="badge bg-success">TERSEDIA</span>
                                        @elseif($inv->status == 'DIPAKAI')
                                            <span class="badge bg-primary">DIPAKAI</span>
                                        @elseif($inv->status == 'DIPINJAM')
                                            <span class="badge bg-info">DIPINJAM</span>
                                        @elseif($inv->status == 'RUSAK')
                                            <span class="badge bg-danger">RUSAK</span>
                                        @elseif($inv->status == 'AFKIR')
                                            <span class="badge bg-secondary">AFKIR</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ $inv->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inv->status == 'DIPAKAI' && $inv->keluarTerakhir)
                                            @php
                                                $maping = $inv->keluarTerakhir->maping;
                                                if ($maping && in_array($maping->status, ['aktif', 'servis', 'maintenance'])) {
                                                    $namaPemakai = $maping->jenis_penerima == 'Perorangan' ? ($maping->karyawan?->nama_karyawan ?? '-') : ($maping->divisi ?? '-');
                                                } else {
                                                    $namaPemakai = $inv->keluarTerakhir->jenis_penerima == 'Perorangan' ? ($inv->keluarTerakhir->karyawan?->nama_karyawan ?? '-') : ($inv->keluarTerakhir->divisi_klr ?? '-');
                                                }
                                            @endphp
                                            <span class="badge bg-label-primary">
                                                <i class="bx bx-user me-1"></i>
                                                {{ $namaPemakai }}
                                            </span>
                                        @elseif($inv->status == 'DIPINJAM' && $inv->peminjamanTerakhir)
                                            <span class="badge bg-label-info">
                                                <i class="bx bx-user me-1"></i>
                                                {{ $inv->peminjamanTerakhir->karyawan?->nama_karyawan ?? ($inv->peminjamanTerakhir->karyawanTujuan?->nama_karyawan ?? '-') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Belum dipakai</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('history.perjalanan.show', $inv->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Riwayat">
                                            <i class="bx bx-history me-1"></i> Lihat Riwayat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ (in_array($user->role, ['super_admin', '1', 1]) || !$user->id_perusahaan) ? 8 : 7 }}" class="text-center py-5 text-muted">
                                        <i class="bx bx-info-circle fs-1 d-block mb-2 text-secondary"></i>
                                        Data unit inventaris dan perjalanan aset belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($inventarisList->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $inventarisList->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Modal Filter --}}
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-filter-alt me-2 text-primary"></i> Filter History Perjalanan Aset
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('history.perjalanan.index') }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Cari Kode / Inventaris / Merk / Nama Pemakai</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Cari kode aset, no inventaris, merek, nama pemakai..." value="{{ request('search') }}">
                                </div>
                            </div>

                            @if ($user->role == 'super_admin')
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Perusahaan</label>
                                    <select name="perusahaan_id" id="perusahaan_id" class="form-select">
                                        <option value="">-- Semua Perusahaan --</option>
                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="{{ $user->role == 'super_admin' ? 'col-md-6' : 'col-12' }}">
                                <label class="form-label fw-semibold">Kategori Aset</label>
                                <select name="kategori_id" id="kategori_id" class="form-select">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach ($kategoris as $k)
                                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('history.perjalanan.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const perusahaanSelect = document.getElementById('perusahaan_id');
            const kategoriSelect = document.getElementById('kategori_id');

            if (perusahaanSelect && kategoriSelect) {
                perusahaanSelect.addEventListener('change', function() {
                    const id = this.value;
                    const fetchUrl = id ? ('/dashboard/get-kategori/' + id) : '/dashboard/get-kategori/all';

                    fetch(fetchUrl)
                        .then(res => res.json())
                        .then(data => {
                            let html = '<option value="">-- Semua Kategori --</option>';
                            data.forEach(item => {
                                html += `<option value="${item.id}">${item.nama_barang}</option>`;
                            });
                            kategoriSelect.innerHTML = html;
                        })
                        .catch(err => {
                            console.error('Error fetching kategori:', err);
                        });
                });
            }
        });
    </script>
@endsection
