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

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div>
                        <h5 class="mb-0">Data Peminjaman Aset</h5>
                        <small class="text-muted">
                            Daftar transaksi peminjaman inventaris.
                        </small>
                    </div>

                    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i>
                        Tambah Peminjaman
                    </a>

                </div>

                <div class="card-body">

                    {{-- Filter --}}

                    <form method="GET">

                        <div class="row mb-4">

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Pinjam</label>

                                <div class="row g-2">

                                    <div class="col-6">
                                        <input type="date" name="tanggal_awal" class="form-control"
                                            value="{{ request('tanggal_awal') }}" placeholder="Dari">
                                    </div>

                                    <div class="col-6">
                                        <input type="date" name="tanggal_akhir" class="form-control"
                                            value="{{ request('tanggal_akhir') }}" placeholder="Sampai">
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Jenis Peminjaman</label>

                                <select name="jenis" class="form-select">

                                    <option value="">Semua</option>

                                    <option value="internal" {{ request('jenis') == 'internal' ? 'selected' : '' }}>
                                        Internal
                                    </option>

                                    <option value="antar_perusahaan"
                                        {{ request('jenis') == 'antar_perusahaan' ? 'selected' : '' }}>
                                        Antar Perusahaan
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label class="form-label">
                                    Status
                                </label>

                                <select name="status" class="form-select">

                                    <option value="">Semua</option>

                                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>
                                        Dipinjam
                                    </option>

                                    <option value="Dikembalikan"
                                        {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>
                                        Dikembalikan
                                    </option>

                                    <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>
                                        Hilang
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Cari
                                </label>

                                <input type="text" name="search" class="form-control"
                                    placeholder="Kode / Inventaris / Peminjam" value="{{ request('search') }}">

                            </div>

                        </div>

                        <div class="text-end mb-3">

                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                                Filter
                            </button>

                            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-reset"></i>
                                Reset
                            </a>

                            <a href="{{ route('peminjaman.cetak', request()->query()) }}" target="_blank"
                                class="btn btn-danger">

                                <i class="bx bxs-file-pdf me-1"></i>

                                Cetak PDF

                            </a>
                        </div>

                    </form>

                    {{-- Table --}}

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th width="5%">No</th>

                                    <th>No Transaksi</th>

                                    <th>Inventaris</th>

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

                                            <div class="btn-group shadow-sm" role="group">

                                                {{-- Detail --}}
                                                <a href="{{ route('peminjaman.show', $item->id) }}"
                                                    class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Detail">

                                                    <i class="bx bx-show text-white"></i>

                                                </a>

                                                {{-- Jika masih dipinjam --}}
                                                @if ($item->status == 'Dipinjam')
                                                    <a href="{{ route('peminjaman.edit', $item->id) }}"
                                                        class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                                        title="Pengembalian">

                                                        <i class="bx bx-undo text-white"></i>

                                                    </a>
                                                @endif

                                                {{-- Jika sudah dikembalikan dalam kondisi rusak --}}
                                                @if ($item->status == 'Dikembalikan' && $item->kondisi_kembali == 'Rusak')
                                                    @if (!$item->maintenanceTerakhir)
                                                        {{-- Belum ada Service --}}
                                                        <a href="{{ route('peminjaman.servis', $item->id) }}"
                                                            class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                            title="Service">

                                                            <i class="bx bx-wrench text-white"></i>

                                                        </a>
                                                    @else
                                                        {{-- Sudah ada Service --}}
                                                        <a href="{{ route('maintenance.show', $item->maintenanceTerakhir->id) }}"
                                                            class="btn btn-success btn-sm" data-bs-toggle="tooltip"
                                                            title="Lihat Service">

                                                            <i class="bx bx-receipt text-white"></i>

                                                        </a>
                                                    @endif
                                                @endif

                                            </div>

                                        </td>
                                    </tr>

                                    @empty

                                        <tr>

                                            <td colspan="8" class="text-center">

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

    @endsection
