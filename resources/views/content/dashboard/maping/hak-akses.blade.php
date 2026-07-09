@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Hak Akses')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ===================================================== --}}
        {{-- HERO HEADER --}}
        {{-- ===================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body py-4">

                <div class="d-flex justify-content-between align-items-start flex-wrap">

                    {{-- Informasi --}}
                    <div>

                        <div class="d-flex align-items-center mb-2">

                            <div class="avatar avatar-md bg-label-primary me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-lock-alt fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <h3 class="fw-bold mb-0">

                                    Kelola Hak Akses

                                </h3>

                                <small class="text-muted">

                                    Kelola Hak Akses & Aplikasi Asset

                                </small>

                            </div>

                        </div>

                        <div class="mt-3">

                            <span class="badge bg-label-primary me-2">

                                {{ $maping->keluar->inventaris->kode_aset }}

                            </span>

                            <span class="badge bg-label-success me-2">

                                {{ $maping->keluar->inventaris->no_inventaris }}

                            </span>

                            <span class="badge bg-label-info">

                                {{ strtoupper($maping->penerima ?? '-') }}

                            </span>

                        </div>

                        <div class="mt-3">

                            <h5 class="mb-1 fw-bold">

                                {{ strtoupper($maping->keluar->inventaris->dataAset->merek ?? '-') }}

                                {{ strtoupper($maping->keluar->inventaris->dataAset->type ?? '-') }}

                            </h5>

                            <span class="text-muted">

                                {{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                                •
                                {{ $maping->perusahaan->nama_perusahaan ?? '-' }}

                            </span>

                        </div>

                    </div>

                    {{-- Tombol --}}
                    <div class="text-end">

                        <a href="{{ route('maping.index', $maping->id) }}" class="btn btn-outline-secondary">

                            <i class="bx bx-arrow-back me-1"></i>

                            Kembali

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- INFORMASI ASSET --}}
        {{-- ===================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="fw-bold mb-0">

                    <i class="bx bx-desktop me-2 text-primary"></i>

                    Informasi Asset

                </h5>

            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- Kode Asset --}}
                    <div class="col-lg-3 col-md-6">

                        <small class="text-muted">

                            Kode Asset

                        </small>

                        <h6 class="fw-bold mt-1">

                            {{ $maping->keluar->inventaris->kode_aset ?? '-' }}

                        </h6>

                    </div>

                    {{-- No Inventaris --}}
                    <div class="col-lg-3 col-md-6">

                        <small class="text-muted">

                            No Inventaris

                        </small>

                        <h6 class="fw-bold mt-1">

                            {{ $maping->keluar->inventaris->no_inventaris ?? '-' }}

                        </h6>

                    </div>

                    {{-- User --}}
                    <div class="col-lg-3 col-md-6">

                        <small class="text-muted">

                            User Asset

                        </small>

                        <h6 class="fw-bold mt-1">

                            {{ $maping->penerima ?? '-' }}

                        </h6>

                    </div>

                    {{-- Lokasi --}}
                    <div class="col-lg-3 col-md-6">

                        <small class="text-muted">

                            Lokasi

                        </small>

                        <h6 class="fw-bold mt-1">

                            {{ $maping->lokasi->nama_lokasi ?? '-' }}

                        </h6>

                    </div>

                </div>

                <hr>

                <div class="row g-4">

                    {{-- Nama Barang --}}
                    <div class="col-lg-4">

                        <small class="text-muted">

                            Nama Barang

                        </small>

                        <h6 class="fw-semibold mt-1">

                            {{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                        </h6>

                    </div>

                    {{-- Merk --}}
                    <div class="col-lg-4">

                        <small class="text-muted">

                            Merk / Type

                        </small>

                        <h6 class="fw-semibold mt-1">

                            {{ strtoupper($maping->keluar->inventaris->dataAset->merek ?? '-') }}

                            {{ strtoupper($maping->keluar->inventaris->dataAset->type ?? '-') }}

                        </h6>

                    </div>

                    {{-- Perusahaan --}}
                    <div class="col-lg-4">

                        <small class="text-muted">

                            Perusahaan

                        </small>

                        <h6 class="fw-semibold mt-1">

                            {{ $maping->perusahaan->nama_perusahaan ?? '-' }}

                        </h6>

                    </div>

                </div>

            </div>

        </div>

        {{-- ===================================================== --}}
        {{-- HAK AKSES AKTIF --}}
        {{-- ===================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-1">

                            <i class="bx bx-shield-quarter text-primary me-2"></i>

                            Hak Akses & Aplikasi Aktif

                        </h5>

                        <small class="text-muted">

                            Daftar hak akses dan aplikasi yang sedang digunakan oleh asset.

                        </small>

                    </div>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAccess">

                        <i class="bx bx-plus me-1"></i>

                        Tambah Hak Akses

                    </button>

                </div>

            </div>

            <div class="card-body">

                <div class="row g-4">

                    @forelse($maping->mapingAccesses as $item)
                        <div class="col-xl-4 col-lg-6">

                            <div class="card border shadow-sm h-100">

                                <div class="card-body">

                                    {{-- Nama --}}
                                    <h5 class="fw-bold mb-3">

                                        {{ strtoupper($item->access->nama_akses) }}

                                    </h5>

                                    {{-- Badge --}}
                                    <div class="mb-4">

                                        @if ($item->access->kategori == 'Aplikasi')
                                            <span class="badge bg-label-primary me-1">

                                                APLIKASI

                                            </span>
                                        @else
                                            <span class="badge bg-label-success me-1">

                                                HAK AKSES

                                            </span>
                                        @endif


                                        @switch($item->access->jenis)
                                            @case('Software')
                                                <span class="badge bg-label-info">

                                                    SOFTWARE

                                                </span>
                                            @break

                                            @case('PPN')
                                                <span class="badge bg-label-warning">

                                                    PPN

                                                </span>
                                            @break

                                            @case('NON PPN')
                                                <span class="badge bg-label-secondary">

                                                    NON PPN

                                                </span>
                                            @break
                                        @endswitch

                                    </div>

                                    {{-- Informasi --}}
                                    <div class="small text-muted mb-3">

                                        Ditambahkan pada

                                        <b>

                                            {{ $item->created_at->format('d-m-Y H:i') }}

                                        </b>

                                    </div>

                                    <hr>

                                    {{-- Tombol --}}
                                    <div class="d-grid">

                                        <form
                                            action="{{ route('maping.hak-akses.destroy', [
                                                'maping' => $maping->id,
                                                'access' => $item->id,
                                            ]) }}"
                                            method="POST" class="form-delete">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger">

                                                <i class="bx bx-trash me-1"></i>

                                                Hapus Hak Akses

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                        @empty

                            <div class="col-12">

                                <div class="alert alert-warning mb-0">

                                    <i class="bx bx-info-circle me-2"></i>

                                    Belum ada Hak Akses maupun Aplikasi yang terdaftar pada asset ini.

                                </div>

                            </div>
                        @endforelse

                        {{-- ===================================================== --}}
                        {{-- MODAL TAMBAH HAK AKSES --}}
                        {{-- ===================================================== --}}

                        <div class="modal fade" id="modalTambahAccess" tabindex="-1" aria-hidden="true">

                            <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                <form action="{{ route('maping.hak-akses.store', $maping->id) }}" method="POST">

                                    @csrf

                                    <div class="modal-content border-0 shadow">

                                        {{-- HEADER --}}
                                        <div class="modal-header">

                                            <h5 class="modal-title fw-bold">

                                                <i class="bx bx-lock-alt me-2 text-primary"></i>

                                                Tambah Hak Akses & Aplikasi

                                            </h5>

                                            <button class="btn-close" data-bs-dismiss="modal"></button>

                                        </div>

                                        {{-- BODY --}}
                                        <div class="modal-body">

                                            @php
                                                $selected = $maping->mapingAccesses->pluck('access_id')->toArray();
                                            @endphp

                                            {{-- ===================== --}}
                                            {{-- APLIKASI --}}
                                            {{-- ===================== --}}

                                            <div class="mb-4">

                                                <h6 class="fw-bold text-primary mb-3">

                                                    <i class="bx bx-desktop me-2"></i>

                                                    Aplikasi

                                                </h6>

                                                <div class="row">

                                                    @foreach ($aplikasis as $access)
                                                        @if (!in_array($access->id, $selected))
                                                            <div class="col-md-6 mb-2">

                                                                <div class="form-check">

                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="accesses[]" value="{{ $access->id }}"
                                                                        id="app{{ $access->id }}">

                                                                    <label class="form-check-label"
                                                                        for="app{{ $access->id }}">

                                                                        {{ $access->nama_akses }}

                                                                    </label>

                                                                </div>

                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </div>

                                            </div>

                                            <hr>

                                            {{-- ===================== --}}
                                            {{-- PPN --}}
                                            {{-- ===================== --}}

                                            <div class="mb-4">

                                                <h6 class="fw-bold text-warning mb-3">

                                                    <i class="bx bx-folder me-2"></i>

                                                    Hak Akses PPN

                                                </h6>

                                                <div class="row">

                                                    @foreach ($hakAksesPPN as $access)
                                                        @if (!in_array($access->id, $selected))
                                                            <div class="col-md-6 mb-2">

                                                                <div class="form-check">

                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="accesses[]" value="{{ $access->id }}"
                                                                        id="ppn{{ $access->id }}">

                                                                    <label class="form-check-label"
                                                                        for="ppn{{ $access->id }}">

                                                                        {{ $access->nama_akses }}

                                                                    </label>

                                                                </div>

                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </div>

                                            </div>

                                            <hr>

                                            {{-- ===================== --}}
                                            {{-- NON PPN --}}
                                            {{-- ===================== --}}

                                            <div>

                                                <h6 class="fw-bold text-success mb-3">

                                                    <i class="bx bx-folder-open me-2"></i>

                                                    Hak Akses NON PPN

                                                </h6>

                                                <div class="row">

                                                    @foreach ($hakAksesNonPPN as $access)
                                                        @if (!in_array($access->id, $selected))
                                                            <div class="col-md-6 mb-2">

                                                                <div class="form-check">

                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="accesses[]" value="{{ $access->id }}"
                                                                        id="nonppn{{ $access->id }}">

                                                                    <label class="form-check-label"
                                                                        for="nonppn{{ $access->id }}">

                                                                        {{ $access->nama_akses }}

                                                                    </label>

                                                                </div>

                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </div>

                                            </div>

                                        </div>

                                        {{-- FOOTER --}}
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">

                                                Batal

                                            </button>

                                            <button type="submit" class="btn btn-primary">

                                                <i class="bx bx-save me-1"></i>

                                                Simpan Hak Akses

                                            </button>

                                        </div>


                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        @endsection
        @section('page-script')

            <script>
                document.querySelectorAll('.form-delete').forEach(function(form) {

                    form.addEventListener('submit', function(e) {

                        e.preventDefault();

                        Swal.fire({

                            title: 'Hapus Hak Akses?',

                            text: 'Hak akses akan dihapus dari asset.',

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
            </script>

        @endsection
