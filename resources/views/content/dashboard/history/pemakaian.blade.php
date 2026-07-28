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
                    <div class="d-flex gap-2">
                        <a href="{{ route('history.pemakaian.export.excel', request()->query()) }}" class="btn btn-outline-success">
                            <i class="bx bxs-file-export me-1"></i> Export Excel
                        </a>
                        <a href="{{ route('history.pemakaian.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-danger">
                            <i class="bx bxs-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('history.pemakaian.index') }}">
                    <div class="row g-3">
                        {{-- FILTER PERUSAHAAN (SUPER ADMIN) --}}
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

                        {{-- FILTER KATEGORI --}}
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                        </div>

                        {{-- TANGGAL AKHIR --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                        </div>

                        {{-- SEARCH BOX --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari Kode, Inventaris, User, Divisi..." value="{{ request('search') }}">
                        </div>

                        {{-- TOMBOL FILTER & RESET --}}
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('history.pemakaian.index') }}" class="btn btn-label-secondary" title="Reset Filter">
                                <i class="bx bx-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">NO</th>
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
                                    <td colspan="8" class="text-center py-5 text-muted">
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
@endsection
