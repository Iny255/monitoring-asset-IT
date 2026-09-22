@extends('layouts/contentNavbarLayout')

@section('title', 'History Pemakaian Aset')

@section('content')
    <style>
        .table th {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            font-size: .82rem;
            font-weight: 700;
        }

        .table td {
            vertical-align: middle;
            padding: 14px 16px;
        }

        .table td small {
            display: block;
            color: #98a4b5;
            margin-top: 3px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

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
                            <h3 class="fw-bold mb-0">History Pemakaian Aset</h3>
                            <small class="text-muted">Rekapitulasi riwayat pengeluaran dan pemakaian unit aset di perusahaan</small>
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
                                    <a class="dropdown-item" href="{{ route('history.pemakaian.export.excel', request()->query()) }}">
                                        <i class="bx bxs-file-export me-2 text-success"></i> Export Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('history.pemakaian.cetak', request()->query()) }}" target="_blank">
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

        @if(request()->anyFilled(['perusahaan_id', 'kategori_id', 'lokasi_id', 'karyawan_id', 'tanggal_awal', 'tanggal_akhir', 'search']))
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-label-primary px-3 py-2">
                    <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                </span>
                <a href="{{ route('history.pemakaian.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-x me-1"></i> Reset Filter
                </a>
            </div>
        @endif

        {{-- TABLE CARD --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">NO</th>
                                @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                    <th>PERUSAHAAN</th>
                                @endif
                                <th>TGL KELUAR</th>
                                <th>NO INVENTARIS</th>
                                <th>DATA ASET</th>
                                <th>PENERIMA / USER ASET</th>
                                <th>LOKASI</th>
                                <th>PETUGAS INPUT</th>
                                <th width="100">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $item)
                                <tr>
                                    <td class="text-center font-monospace">
                                        {{ ($histories->currentPage() - 1) * $histories->perPage() + $loop->iteration }}
                                    </td>
                                    @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                        <td>
                                            <x-company-badge :perusahaan="$item->perusahaan" />
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        <span class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">
                                            {{ $item->inventaris->no_inventaris ?? '-' }}
                                        </div>
                                        <small>{{ $item->inventaris->kode_aset ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ strtoupper($item->inventaris->dataAset->kategori->nama_barang ?? '-') }}
                                        </div>
                                        <small>
                                            {{ strtoupper($item->inventaris->dataAset->merek ?? '-') }}
                                            {{ strtoupper($item->inventaris->dataAset->type ?? '-') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($item->jenis_penerima == 'Perorangan')
                                            <div class="fw-semibold text-dark">
                                                {{ strtoupper($item->karyawan->nama_karyawan ?? '-') }}
                                            </div>
                                            <span class="badge bg-label-primary">Perorangan</span>
                                        @else
                                            <div class="fw-semibold text-dark">
                                                {{ strtoupper($item->divisi_klr ?? '-') }}
                                            </div>
                                            <span class="badge bg-label-warning">Per Divisi</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ strtoupper($item->lokasi->nama_lokasi ?? '-') }}
                                    </td>
                                    <td class="text-center">
                                        <small class="text-dark fw-semibold">
                                            {{ $item->user->name ?? 'Sistem' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->maping)
                                            <a href="{{ route('maping.show', $item->maping->id) }}" class="btn btn-sm btn-label-info" title="Lihat Detail Mapping">
                                                <i class="bx bx-show me-1"></i> Mapping
                                            </a>
                                        @else
                                            <span class="badge bg-label-secondary">Belum Mapping</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) ? 9 : 8 }}" class="text-center py-5 text-muted">
                                        <i class="bx bx-info-circle fs-1 d-block mb-2"></i>
                                        Belum ada riwayat pemakaian aset yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if ($histories->hasPages())
                    <div class="card-footer d-flex justify-content-end py-3">
                        {{ $histories->links() }}
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
                        <i class="bx bx-filter-alt me-2 text-primary"></i> Filter History Pemakaian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('history.pemakaian.index') }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- SEARCH BOX --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Cari Kode, Inventaris, User, Divisi..." value="{{ request('search') }}">
                                </div>
                            </div>

                            {{-- FILTER PERUSAHAAN (SUPER ADMIN) --}}
                            @if ($user->role == 'super_admin')
                                <div class="col-md-6">
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

                            {{-- FILTER KATEGORI --}}
                            <div class="col-md-6">
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

                            {{-- FILTER LOKASI --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lokasi</label>
                                <select name="lokasi_id" class="form-select">
                                    <option value="">-- Semua Lokasi --</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}" {{ request('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FILTER USER ASET --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">User Aset (Karyawan)</label>
                                <select name="karyawan_id" class="form-select">
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach ($karyawans as $kar)
                                        <option value="{{ $kar->id }}" {{ request('karyawan_id') == $kar->id ? 'selected' : '' }}>
                                            {{ $kar->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- TANGGAL AWAL --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                            </div>

                            {{-- TANGGAL AKHIR --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('history.pemakaian.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
