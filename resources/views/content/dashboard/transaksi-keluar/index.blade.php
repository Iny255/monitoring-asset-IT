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

    <x-company-filter-banner />

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">

            <h5 class="text-primary mb-0">
                Data Pemakaian Aset
            </h5>

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
                            <a class="dropdown-item" href="{{ route('transaksi-keluar.cetak', request()->query()) }}" target="_blank">
                                <i class="bx bxs-file-pdf text-danger me-2"></i> Export PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaksi-keluar.exportExcel', request()->query()) }}">
                                <i class="bx bxs-file-export text-success me-2"></i> Export Excel
                            </a>
                        </li>
                    </ul>
                </div>

                @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                    <a href="{{ route('transaksi-keluar.create') }}" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="bx bx-plus"></i> Tambah Data
                    </a>
                @endif

            </div>

        </div>

        {{-- BODY --}}
        <div class="card-body">

            @if(request()->anyFilled(['perusahaan_id', 'tanggal_awal', 'tanggal_akhir', 'kategori_id', 'search']))
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-label-primary px-3 py-2">
                        <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                    </span>
                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-x me-1"></i> Reset Filter
                    </a>
                </div>
            @endif

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
                                        <x-company-badge :perusahaan="$keluar->perusahaan" />
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

                                    <div class="d-flex justify-content-center gap-1">

                                        <a href="{{ route('transaksi-keluar.show', $keluar->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-primary" title="Detail">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <a href="{{ route('transaksi-keluar.edit', $keluar->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-secondary" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                            data-id="{{ $keluar->id }}" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                        <form id="delete-form-{{ $keluar->id }}"
                                            action="{{ route('transaksi-keluar.destroy', $keluar->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

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

    <!-- ================= FILTER MODAL ================= -->
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="GET" action="{{ route('transaksi-keluar.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Pemakaian Aset
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @if (auth()->user()->role === 'super_admin')
                                <div class="col-md-6">
                                    <label class="form-label">Perusahaan</label>
                                    <select name="perusahaan_id" class="form-select">
                                        <option value="">Semua Perusahaan</option>
                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}"
                                                {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-{{ auth()->user()->role === 'super_admin' ? '6' : '12' }}">
                                <label class="form-label">Kategori</label>
                                <select name="kategori_id" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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

                            <div class="col-12">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Kode aset / Karyawan / Divisi" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">
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
