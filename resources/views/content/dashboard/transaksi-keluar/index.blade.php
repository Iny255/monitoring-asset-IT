@extends('layouts/contentNavbarLayout')

@section('title', 'Pemakaian Aset')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="text-primary mb-0">
                Data Pemakaian Aset
            </h5>

            <div class="d-flex gap-2">

                @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                    <a href="{{ route('transaksi-keluar.create') }}" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="bx bx-plus"></i> Tambah Data
                    </a>
                @endif

                <a href="{{ url('/dashboard/transaksi-masuk/stok') }}" class="btn btn-primary px-4 py-2 fw-semibold">
                    Cek Stok
                </a>

            </div>

        </div>

        {{-- BODY --}}
        <div class="card-body">

            {{-- FILTER --}}
            <div class="card border mb-4">

                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        Filter Data Pemakaian Aset
                    </h6>
                </div>

                <div class="card-body">

                    <form method="GET" action="{{ route('transaksi-keluar.index') }}">

                        <div class="row g-3">

                            @if (auth()->user()->role === 'super_admin')
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Perusahaan
                                    </label>

                                    <select name="perusahaan_id" class="form-select">

                                        <option value="">
                                            Semua Perusahaan
                                        </option>

                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}"
                                                {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>

                                                {{ $p->nama_perusahaan }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                            @endif

                            <div class="col-md-3">

                                <label class="form-label">
                                    Tanggal Awal
                                </label>

                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="{{ request('tanggal_awal') }}">

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Tanggal Akhir
                                </label>

                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="{{ request('tanggal_akhir') }}">

                            </div>
                            <div class="col-md-3">

                                <label class="form-label">
                                    Kategori
                                </label>

                                <select name="kategori_id" class="form-select">

                                    <option value="">
                                        Semua Kategori
                                    </option>

                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>

                                            {{ $kategori->nama_barang }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Pencarian
                                </label>

                                <input type="text" name="search" class="form-control"
                                    placeholder="Kode aset / Karyawan / Divisi" value="{{ request('search') }}">

                            </div>

                        </div>

                        <div class="mt-3 d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i> Tampilkan
                            </button>

                            <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">
                                <i class="bx bx-refresh"></i> Reset
                            </a>

                            <a href="{{ route('transaksi-keluar.cetak', request()->query()) }}" target="_blank"
                                class="btn btn-danger">

                                <i class="bx bx-printer"></i> Cetak PDF

                            </a>

                        </div>

                    </form>

                </div>

            </div>

            {{-- TABEL --}}
            <div class="table-responsive">

                <table class="table table-bordered table-hover table-pemakaian mb-0">

                    <thead class="table-primary">

                        <tr>

                            <th width="50">NO</th>

                            @if (auth()->user()->role === 'super_admin')
                                <th width="180">PERUSAHAAN</th>
                            @endif

                            <th width="120">KODE ASET</th>
                            <th width="120">NO INVENTARIS</th>
                            <th>DATA ASET</th>
                            <th width="180">PENERIMA</th>
                            <th width="120">DIVISI</th>
                            <th width="120">TANGGAL KELUAR</th>
                            <th width="100">FOTO</th>
                            <th width="130">AKSI</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($keluars as $index => $keluar)
                            <tr>

                                <td class="text-center">
                                    {{ $keluars->firstItem() + $index }}
                                </td>

                                @if (auth()->user()->role === 'super_admin')
                                    <td>
                                        {{ $keluar->perusahaan->nama_perusahaan ?? '-' }}
                                    </td>
                                @endif

                                <td class="text-center">

                                    <span class="badge bg-label-primary">
                                        {{ $keluar->inventaris->kode_aset ?? '-' }}
                                    </span>

                                </td>

                                <td class="text-center">
                                    {{ $keluar->inventaris->no_inventaris ?? '-' }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $keluar->inventaris->dataAset->merek ?? '-' }}
                                        -
                                        {{ $keluar->inventaris->dataAset->type ?? '-' }}

                                    </small>

                                </td>

                                <td>

                                    @if ($keluar->jenis_penerima == 'Perorangan')
                                        {{ $keluar->karyawan->nama_karyawan ?? '-' }}
                                    @else
                                        <span class="badge bg-label-primary">
                                            DIVISI
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($keluar->jenis_penerima == 'Perorangan')
                                        {{ $keluar->karyawan->divisi ?? '-' }}
                                    @else
                                        {{ $keluar->divisi_klr }}
                                    @endif

                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($keluar->tgl_keluar)->format('d-m-Y') }}
                                </td>

                                <td class="text-center">

                                    @if ($keluar->gambar)
                                        <img src="{{ asset('storage/' . $keluar->gambar) }}" width="70"
                                            class="rounded border">
                                    @else
                                        <span class="text-muted">
                                            -
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    <div class="btn-group">

                                        <a href="{{ route('transaksi-keluar.show', $keluar->id) }}"
                                            class="btn btn-info btn-sm">

                                            <i class="bx bx-show"></i>

                                        </a>

                                        <a href="{{ route('transaksi-keluar.edit', $keluar->id) }}"
                                            class="btn btn-warning btn-sm">

                                            <i class="bx bx-edit"></i>

                                        </a>

                                        <button type="button" class="btn btn-danger btn-sm btn-delete"
                                            data-id="{{ $keluar->id }}">

                                            <i class="bx bx-trash"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role == 'super_admin' ? 10 : 9 }}" class="text-center">

                                    Data pemakaian aset belum tersedia

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $keluars->links('pagination::bootstrap-4') }}
            </div>

        </div>

    </div>

@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // DELETE
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data transaksi keluar ini akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });
            // EDIT
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Edit data ini?',
                        text: 'Kamu akan diarahkan ke halaman edit',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Edit',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/dashboard/transaksi-keluar/${id}/edit`;
                        }
                    });
                });
            });

        });
    </script>
@endsection
