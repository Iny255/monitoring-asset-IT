@extends('layouts/contentNavbarLayout')

@section('title', 'Peminjaman Aset')

@section('content')
    <style>
        .btn-group .btn {

            width: 40px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0;

        }

        .btn-group form {

            margin: 0;

        }

        .btn-group i {

            font-size: 18px;

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
                                <i class="bx bx-time-five fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Peminjaman Aset</h3>
                            <small class="text-muted">Kelola transaksi peminjaman aset sementara dan pengembalian</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bx bx-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('peminjaman.cetak', request()->query()) }}" target="_blank">
                                        <i class="bx bxs-file-pdf text-danger me-2"></i> Export PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('peminjaman.exportExcel', request()->query()) }}">
                                        <i class="bx bxs-file-export text-success me-2"></i> Export Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Input Peminjaman
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <x-company-filter-banner />

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                    @if(request()->anyFilled(['tanggal_awal', 'tanggal_akhir', 'perusahaan', 'jenis', 'status', 'search']))
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-label-primary px-3 py-2">
                                <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                            </span>
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> Reset Filter
                            </a>
                        </div>
                    @endif

                    {{-- Table --}}

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th width="5%">No</th>

                                    <th>No Transaksi</th>

                                    <th>Inventaris</th>
                                    @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                        <th>Perusahaan</th>
                                    @endif

                                    <th>Jenis</th>

                                    <th>Peminjam</th>

                                    <th>Tanggal Pinjam</th>

                                    <th>Status</th>

                                    <th width="15%" class="text-center">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($peminjamans as $item)

                                    <tr>

                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>

                                            <div class="fw-bold">
                                                {{ $item->kode_peminjaman }}
                                            </div>

                                        </td>

                                        <td>

                                            @if ($item->inventaris)
                                                <div class="fw-semibold">

                                                    {{ $item->inventaris->kode_aset }}

                                                </div>

                                                <small class="text-muted">

                                                    {{ $item->inventaris->dataAset->nama_barang ?? '-' }}

                                                    {{ $item->inventaris->dataAset->merek ?? '' }}

                                                    {{ $item->inventaris->dataAset->type ?? '' }}

                                                </small>
                                            @else
                                                -
                                            @endif

                                        </td>
                                        @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                            <td>
                                                <x-company-badge :perusahaan="$item->inventaris?->perusahaan" />
                                            </td>
                                        @endif

                                        <td>

                                            @if ($item->jenis_peminjaman == 'internal')
                                                <span class="badge bg-label-primary">

                                                    INTERNAL

                                                </span>
                                            @else
                                                <span class="badge bg-label-warning">

                                                    ANTAR PERUSAHAAN

                                                </span>
                                            @endif

                                        </td>

                                        <td>

                                            @if ($item->jenis_peminjaman == 'internal')
                                                @if ($item->karyawan)
                                                    <div class="fw-semibold">

                                                        {{ $item->karyawan->nama_karyawan }}

                                                    </div>

                                                    <small class="text-muted">

                                                        {{ $item->karyawan->kode_karyawan }}

                                                    </small>
                                                @else
                                                    -
                                                @endif
                                            @else
                                                <div class="fw-semibold">

                                                    {{ $item->perusahaanTujuan->nama_perusahaan ?? '-' }}

                                                </div>

                                                <small class="text-muted">

                                                    {{ $item->karyawanTujuan->nama_karyawan ?? '-' }}

                                                </small>
                                            @endif

                                            @if ($item->lokasi)
                                                <div class="mt-1">
                                                    <span class="badge bg-label-info font-monospace py-0 px-1" style="font-size: 0.72rem;" title="Lokasi Ruangan Penggunaan">
                                                        <i class="bx bx-map-pin me-1"></i>{{ $item->lokasi->nama_lokasi }}
                                                    </span>
                                                </div>
                                            @endif

                                        </td>

                                        <td>

                                            {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}

                                        </td>

                                        <td>

                                            @switch($item->status)
                                                @case('Dipinjam')
                                                    <span class="badge bg-label-danger">

                                                        DIPINJAM

                                                    </span>
                                                @break

                                                @case('Dikembalikan')
                                                    <span class="badge bg-label-success">

                                                        DIKEMBALIKAN

                                                    </span>
                                                @break

                                                @case('Hilang')
                                                    <span class="badge bg-label-dark">

                                                        HILANG

                                                    </span>
                                                @break
                                            @endswitch

                                        </td>

                                        <td class="text-center">

                                            <div class="d-flex justify-content-center gap-1">

                                                {{-- Detail --}}
                                                <a href="{{ route('peminjaman.show', $item->id) }}"
                                                    class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" title="Detail">

                                                    <i class="bx bx-show"></i>

                                                </a>

                                                {{-- Jika masih dipinjam --}}
                                                @if ($item->status == 'Dipinjam')
                                                    <a href="{{ route('peminjaman.edit', $item->id) }}"
                                                        class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip"
                                                        title="Pengembalian">

                                                        <i class="bx bx-undo"></i>

                                                    </a>
                                                @endif

                                                {{-- Jika sudah dikembalikan dalam kondisi rusak --}}
                                                @if ($item->status == 'Dikembalikan' && $item->kondisi_kembali == 'Rusak')
                                                    @if (!$item->maintenanceTerakhir)
                                                        {{-- Belum ada Service --}}
                                                        <a href="{{ route('peminjaman.servis', $item->id) }}"
                                                            class="btn btn-sm btn-icon btn-outline-danger" data-bs-toggle="tooltip"
                                                            title="Service">

                                                            <i class="bx bx-wrench"></i>

                                                        </a>
                                                    @else
                                                        {{-- Sudah ada Service --}}
                                                        <a href="{{ route('maintenance.show', $item->maintenanceTerakhir->id) }}"
                                                            class="btn btn-sm btn-icon btn-outline-success" data-bs-toggle="tooltip"
                                                            title="Lihat Service">

                                                            <i class="bx bx-receipt"></i>

                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- Hapus --}}
                                                <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data peminjaman {{ $item->kode_peminjaman }}?\n\n(Status inventaris akan dikembalikan menjadi TERSEDIA jika masih dipinjam)');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>

                                        </td>
                                    </tr>

                                    @empty

                                        <tr>

                                            <td colspan="{{ auth()->user()->role == 'super_admin' ? 9 : 8 }}"
                                                class="text-center">

                                                Tidak ada data peminjaman.

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>
                            </table>

                        </div>

                        <div class="mt-3">

                            {{ $peminjamans->links() }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    <!-- ================= FILTER MODAL ================= -->
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="GET" action="{{ route('peminjaman.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Peminjaman Aset
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="{{ request('tanggal_awal') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="{{ request('tanggal_akhir') }}">
                            </div>

                            @if (auth()->user()->role == 'super_admin')
                                <div class="col-md-6">
                                    <label class="form-label">Perusahaan</label>
                                    <select name="perusahaan" class="form-select">
                                        <option value="">Semua Perusahaan</option>
                                        @foreach ($perusahaans as $perusahaan)
                                            <option value="{{ $perusahaan->id }}"
                                                {{ request('perusahaan') == $perusahaan->id ? 'selected' : '' }}>
                                                {{ $perusahaan->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-{{ auth()->user()->role == 'super_admin' ? '6' : '12' }}">
                                <label class="form-label">Jenis Peminjaman</label>
                                <select name="jenis" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="internal" {{ request('jenis') == 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="antar_perusahaan" {{ request('jenis') == 'antar_perusahaan' ? 'selected' : '' }}>Antar Perusahaan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                    <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Lokasi / Ruangan</label>
                                <select name="id_lokasi" class="form-select">
                                    <option value="">Semua Lokasi</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}" {{ request('id_lokasi') == $lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Cari</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Kode / Inventaris / Peminjam" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                            <i class="bx bx-refresh me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
