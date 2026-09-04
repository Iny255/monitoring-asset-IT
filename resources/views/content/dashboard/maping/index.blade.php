@extends('layouts/contentNavbarLayout')

@section('title', 'Mapping Inventaris')

@section('content')
    <style>
        .table td {

            vertical-align: middle;

        }

        .dropdown-menu {

            min-width: 220px;

        }

        .dropdown-item {

            padding: .55rem 1rem;

        }

        .dropdown-item i {

            width: 22px;

        }

        .badge {

            font-size: 12px;

        }

        .card-header {

            padding: 18px 22px;

        }
    </style>

    <div class="container-fluid">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ALERT ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-sitemap fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Mapping Aset</h3>
                            <small class="text-muted">Kelola penyerahan aset dari stok gudang sekaligus pendaftaran spesifikasi perangkat</small>
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
                                    <a class="dropdown-item" href="{{ route('maping.print', request()->query()) }}" target="_blank">
                                        <i class="bx bxs-file-pdf text-danger me-2"></i> Export PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('maping.export.excel', request()->query()) }}">
                                        <i class="bx bxs-file-export text-success me-2"></i> Export Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('maping.pemakaian') }}" class="btn btn-outline-info">
                            <i class="bx bx-laptop me-1"></i> Pemakaian Aset
                        </a>
                        <a href="{{ route('maping.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Tambah Mapping Aset
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">


                {{-- TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-primary text-center">

                            <tr>

                                <th width="60">NO</th>

                                <th width="140">KODE ASET</th>

                                <th>DATA ASET</th>

                                <th width="170">USER ASET</th>

                                <th width="160">
                                    LOKASI
                                </th>

                                <th width="110">
                                    QR CODE
                                </th>

                                <th width="130">
                                    TGL DIGUNAKAN
                                </th>

                                <th width="120">STATUS</th>

                                @if (auth()->user()->role == 'super_admin')
                                    <th width="180">PERUSAHAAN</th>
                                @endif

                                <th width="120">ACTION</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($mapings as $maping)
                                <tr>

                                    {{-- NO --}}
                                    <td class="text-center">

                                        {{ ($mapings->currentPage() - 1) * $mapings->perPage() + $loop->iteration }}

                                    </td>

                                    {{-- KODE ASET --}}
                                    <td>

                                        <strong>

                                            {{ $maping->keluar->inventaris->kode_aset ?? '-' }}

                                        </strong>

                                    </td>

                                    {{-- DATA ASET --}}
                                    <td>

                                        <strong>

                                            {{ strtoupper($maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-') }}

                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            {{ strtoupper($maping->keluar->inventaris->dataAset->merek ?? '-') }}

                                            {{ strtoupper($maping->keluar->inventaris->dataAset->type ?? '-') }}

                                        </small>

                                    </td>

                                    {{-- USER --}}
                                    <td>

                                        <div class="fw-semibold">

                                            {{ strtoupper($maping->penerima) }}

                                        </div>

                                        <small class="text-muted">

                                            @if ($maping->jenis_penerima == 'Perorangan')
                                                <span class="badge bg-label-primary">

                                                    Perorangan

                                                </span>
                                            @else
                                                <span class="badge bg-label-warning">

                                                    Per Divisi

                                                </span>
                                            @endif

                                        </small>

                                    </td>

                                    {{-- LOKASI --}}
                                    <td>

                                        {{ strtoupper($maping->lokasi->nama_lokasi ?? '-') }}

                                    </td>
                                    <td class="text-center">

                                        @if ($maping->id)
                                            <a href="{{ route('maping.public_show', $maping->uuid ?? $maping->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary" title="Lihat QR Code">

                                                <i class="bx bx-qr"></i>

                                            </a>
                                        @else
                                            -
                                        @endif

                                    </td>

                                    {{-- TANGGAL DIGUNAKAN --}}
                                    <td class="text-center">

                                        {{ \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d-m-Y') }}

                                    </td>

                                    {{-- STATUS --}}
                                    <td class="text-center">

                                        @switch($maping->status)
                                            @case('servis')
                                                <span class="badge bg-label-secondary">

                                                    Servis

                                                </span>
                                            @break

                                            @case('aktif')
                                                <span class="badge bg-label-success">

                                                    Aktif

                                                </span>
                                            @break

                                            @case('dipinjam')
                                                <span class="badge bg-label-warning">

                                                    Dipinjam

                                                </span>
                                            @break

                                            @case('selesai')
                                                <span class="badge bg-label-danger">

                                                    Non Aktif

                                                </span>
                                            @break

                                            @case('maintenance')
                                                <span class="badge bg-label-info">

                                                    Maintenance

                                                </span>
                                            @break
                                        @endswitch

                                    </td>

                                    {{-- PERUSAHAAN --}}
                                    @if (auth()->user()->role == 'super_admin')
                                        <td>

                                            {{ strtoupper($maping->perusahaan->nama_perusahaan ?? '-') }}

                                        </td>
                                    @endif

                                    {{-- ACTION --}}
                                    <td class="text-center">

                                        <div class="dropdown">

                                            <button class="btn btn-sm btn-primary dropdown-toggle"
                                                data-bs-toggle="dropdown">

                                                Aksi

                                            </button>

                                            <ul class="dropdown-menu">

                                                {{-- Selalu boleh --}}
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('maping.show', $maping->id) }}">
                                                        <i class="bx bx-show me-2"></i>
                                                        Detail
                                                    </a>
                                                </li>

                                                {{-- Hanya status aktif --}}
                                                @if ($maping->status == 'aktif')
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('maping.edit', $maping->id) }}">
                                                            <i class="bx bx-edit me-2"></i>
                                                            Edit
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('maping.hak-akses', $maping->id) }}">
                                                            <i class="bx bx-lock-alt me-2"></i>
                                                            Kelola Hak Akses
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('maping.mutasi', $maping->id) }}">
                                                            <i class="bx bx-transfer me-2"></i>
                                                            Mutasi
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('maping.servis', $maping->id) }}">
                                                            <i class="bx bx-wrench me-2"></i>
                                                            Servis & Maintenance
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('pencabutan.create', $maping->id) }}">
                                                            <i class="bx bx-power-off me-2"></i>
                                                            Pencabutan
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <form action="{{ route('maping.destroy', $maping->id) }}"
                                                            method="POST" class="form-delete">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit" class="dropdown-item text-danger">

                                                                <i class="bx bx-trash me-2"></i>

                                                                Hapus

                                                            </button>

                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <form action="{{ route('maping.reactivate', $maping->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success"
                                                                onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali mapping dan unit aset ini?')">
                                                                <i class="bx bx-check-circle me-2"></i>
                                                                Aktifkan Kembali
                                                            </button>
                                                        </form>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('maping.edit', $maping->id) }}">
                                                            <i class="bx bx-edit me-2"></i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                @endif

                                            </ul>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                    <tr>

                                        <td colspan="{{ auth()->user()->role == 'super_admin' ? 9 : 8 }}" class="text-center">

                                            Belum ada data Mapping.

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-3">

                        {{ $mapings->links('pagination::bootstrap-5') }}

                    </div>

                </div>

            </div>

        </div>
        <!-- ================= FILTER MODAL ================= -->
        <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-xl">

                <div class="modal-content">

                    <form method="GET" action="{{ route('maping.index') }}">

                        <div class="modal-header">

                            <h5 class="modal-title">

                                <i class="bx bx-filter-alt me-2"></i>

                                Filter Data Mapping

                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>

                        <div class="modal-body">

                            <div class="row">

                                {{-- Perusahaan --}}
                                @if (auth()->user()->role == 'super_admin')

                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            Perusahaan
                                        </label>

                                        <select name="perusahaan_id" class="form-select">

                                            <option value="">
                                                Semua Perusahaan
                                            </option>

                                            @foreach ($perusahaans as $perusahaan)
                                                <option value="{{ $perusahaan->id }}"
                                                    {{ request('perusahaan_id') == $perusahaan->id ? 'selected' : '' }}>

                                                    {{ strtoupper($perusahaan->nama_perusahaan) }}

                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                @endif

                                {{-- Lokasi --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Lokasi
                                    </label>

                                    <select name="lokasi_id" class="form-select">

                                        <option value="">
                                            Semua Lokasi
                                        </option>

                                        @foreach ($lokasis as $lokasi)
                                            <option value="{{ $lokasi->id }}"
                                                {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>

                                                {{ strtoupper($lokasi->nama_lokasi) }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- Status --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select name="status" class="form-select">

                                        <option value="">
                                            Semua Status
                                        </option>

                                        <option value="aktif">Aktif</option>
                                        <option value="selesai">Non Aktif</option>
                                        <option value="dipinjam">Dipinjam</option>
                                        <option value="servis">Servis</option>
                                        <option value="maintenance">Maintenance</option>

                                    </select>

                                </div>

                                {{-- Kategori --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Kategori Aset
                                    </label>

                                    <select name="kategori_id" class="form-select">

                                        <option value="">
                                            Semua Kategori
                                        </option>

                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>

                                                {{ strtoupper($kategori->nama_barang) }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- Dari --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Dari Tanggal Digunakan
                                    </label>

                                    <input type="date" name="tanggal_awal" class="form-control"
                                        value="{{ request('tanggal_awal') }}">

                                </div>

                                {{-- Sampai --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Sampai Tanggal Digunakan
                                    </label>

                                    <input type="date" name="tanggal_akhir" class="form-control"
                                        value="{{ request('tanggal_akhir') }}">

                                </div>

                                {{-- Processor --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Processor
                                    </label>

                                    <input type="text" name="processor" class="form-control"
                                        value="{{ request('processor') }}">

                                </div>

                                {{-- RAM --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        RAM
                                    </label>

                                    <input type="text" name="ram" class="form-control" value="{{ request('ram') }}">

                                </div>

                                {{-- Operating System --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Operating System
                                    </label>

                                    <input type="text" name="system" class="form-control"
                                        value="{{ request('system') }}">

                                </div>

                                {{-- Search --}}
                                <div class="col-md-12">

                                    <label class="form-label">

                                        Pencarian Umum

                                    </label>

                                    <input type="text" name="search" class="form-control"
                                        placeholder="Kode Aset, User Aset, Merek, Type, Device ID, Product ID, Serial Number..."
                                        value="{{ request('search') }}">

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <a href="{{ route('maping.index') }}" class="btn btn-secondary">

                                <i class="bx bx-refresh"></i>

                                Reset

                            </a>

                            <button class="btn btn-primary">

                                <i class="bx bx-search"></i>

                                Terapkan Filter

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

                document.querySelectorAll('.form-delete').forEach(function(form) {

                    form.addEventListener('submit', function(e) {

                        e.preventDefault();

                        Swal.fire({

                            title: 'Hapus Mapping?',

                            text: 'Data Mapping akan dihapus.',

                            icon: 'warning',

                            showCancelButton: true,

                            confirmButtonColor: '#696cff',

                            cancelButtonColor: '#8592a3',

                            confirmButtonText: 'Ya, Hapus',

                            cancelButtonText: 'Batal'

                        }).then((result) => {

                            if (result.isConfirmed) {

                                form.submit();

                            }

                        });

                    });

                });

            });

            $(document).ready(function() {
                $('#modalFilter select[name="perusahaan_id"]').on('change', function() {
                    let perusahaanId = $(this).val();
                    let $lokasiSelect = $('#modalFilter select[name="lokasi_id"]');
                    let $kategoriSelect = $('#modalFilter select[name="kategori_id"]');

                    $.ajax({
                        url: "{{ route('maping.getFilterOptions') }}",
                        type: "GET",
                        data: { perusahaan_id: perusahaanId },
                        success: function(res) {
                            $lokasiSelect.empty().append('<option value="">Semua Lokasi</option>');
                            if (res.lokasis && res.lokasis.length > 0) {
                                res.lokasis.forEach(function(item) {
                                    $lokasiSelect.append('<option value="' + item.id + '">' + item.nama_lokasi.toUpperCase() + '</option>');
                                });
                            }

                            $kategoriSelect.empty().append('<option value="">Semua Kategori</option>');
                            if (res.kategoris && res.kategoris.length > 0) {
                                res.kategoris.forEach(function(item) {
                                    $kategoriSelect.append('<option value="' + item.id + '">' + item.nama_barang.toUpperCase() + '</option>');
                                });
                            }
                        }
                    });
                });
            });
        </script>

    @endsection
