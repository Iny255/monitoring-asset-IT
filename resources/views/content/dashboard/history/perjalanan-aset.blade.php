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
                </div>
            </div>
        </div>

        {{-- FILTER DATA --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('history.perjalanan.index') }}">
                    <div class="row g-3">
                        @if ($user->role == 'super_admin')
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Perusahaan</label>
                                <select name="perusahaan_id" class="form-select">
                                    <option value="">-- Semua Perusahaan --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori Aset</label>
                            <select name="kategori_id" class="form-select">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($kategoris as $k)
                                    <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cari Kode Aset / Inventaris / Merk</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari kode aset, no inventaris, merek..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end gap-1">
                            <button type="submit" class="btn btn-primary" title="Cari">
                                <i class="bx bx-search"></i>
                            </button>
                            <a href="{{ route('history.perjalanan.index') }}" class="btn btn-label-secondary" title="Reset">
                                <i class="bx bx-refresh"></i>
                            </a>
                            <a href="{{ route('history.perjalanan.export_excel_index', request()->query()) }}" class="btn btn-success" title="Export Excel">
                                <i class="bx bxs-file-export me-1"></i> Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE DAFTAR UNIT ASET --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">NO</th>
                                <th>KODE ASET / INVENTARIS</th>
                                @if ($user->role == 'super_admin')
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
                                    @if ($user->role == 'super_admin')
                                        <td>
                                            <span class="fw-bold text-dark">
                                                {{ $inv->perusahaan?->nama_perusahaan ?? '-' }}
                                            </span>
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
                                            <span class="badge bg-label-primary">
                                                <i class="bx bx-user me-1"></i>
                                                {{ $inv->keluarTerakhir->jenis_penerima == 'Perorangan' ? ($inv->keluarTerakhir->karyawan?->nama_karyawan ?? '-') : ($inv->keluarTerakhir->divisi_klr ?? '-') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Belum dipakai</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('history.perjalanan.show', $inv->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-history me-1"></i> Lihat Riwayat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $user->role == 'super_admin' ? 8 : 7 }}" class="text-center py-5 text-muted">
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
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const perusahaanSelect = document.querySelector('form[action="{{ route('history.perjalanan.index') }}"] select[name="perusahaan_id"]');
            const kategoriSelect = document.querySelector('form[action="{{ route('history.perjalanan.index') }}"] select[name="kategori_id"]');

            if (perusahaanSelect && kategoriSelect) {
                perusahaanSelect.addEventListener('change', function() {
                    const id = this.value;
                    if (!id) {
                        kategoriSelect.innerHTML = '<option value="">-- Semua Kategori --</option>';
                        return;
                    }

                    fetch('/dashboard/get-kategori/' + id)
                        .then(res => res.json())
                        .then(data => {
                            let html = '<option value="">-- Semua Kategori --</option>';
                            data.forEach(item => {
                                html += `<option value="${item.id}">${item.nama_barang}</option>`;
                            });
                            kategoriSelect.innerHTML = html;
                        });
                });
            }
        });
    </script>
@endsection
