@extends('layouts/contentNavbarLayout')

@section('title', 'History Hak Akses')

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

        {{-- ========================================================= --}}
        {{-- HERO HEADER --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body py-4">

                <div class="d-flex justify-content-between align-items-start flex-wrap">

                    <div>

                        <div class="d-flex align-items-center mb-2">

                            <div class="avatar avatar-md bg-label-primary me-3">

                                <span class="avatar-initial rounded">

                                    <i class="bx bx-history fs-3"></i>

                                </span>

                            </div>

                            <div>

                                <h3 class="fw-bold mb-0">

                                    History Hak Akses

                                </h3>

                                <small class="text-muted">

                                    Riwayat perubahan Hak Akses & Aplikasi Asset

                                </small>

                            </div>

                        </div>

                    </div>

                    <div>

                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">

                            <i class="bx bx-arrow-back me-1"></i>

                            Kembali

                        </a>

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- FILTER --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="fw-bold mb-0">

                    <i class="bx bx-filter-alt me-2 text-primary"></i>

                    Filter Data

                </h5>

            </div>

            <div class="card-body">

                <form method="GET">

                    <div class="row g-3">

                        {{-- PERUSAHAAN --}}
                        @if (auth()->user()->role == 'super_admin')

                            <div class="col-md-3">

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

                                            {{ $perusahaan->nama_perusahaan }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        @endif

                        {{-- USER --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                User Asset

                            </label>

                            <input type="text" name="search" class="form-control"
                                placeholder="Nama User, Kode Asset, No Inventaris..." value="{{ request('search') }}">

                        </div>
                        <div class="col-md-3">

                            <label class="form-label">

                                Jenis

                            </label>

                            <select name="jenis" id="jenis" class="form-select">

                                <option value="">

                                    Semua Jenis

                                </option>

                                <option value="PPN" {{ request('jenis') == 'PPN' ? 'selected' : '' }}>

                                    Hak Akses PPN

                                </option>

                                <option value="NON PPN" {{ request('jenis') == 'NON PPN' ? 'selected' : '' }}>

                                    Hak Akses NON PPN

                                </option>

                                <option value="Software" {{ request('jenis') == 'Software' ? 'selected' : '' }}>

                                    Aplikasi

                                </option>

                            </select>

                        </div>

                        {{-- HAK AKSES --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                Hak Akses

                            </label>

                            <select id="access_id" name="access_id" class="form-select">

                                <option value="">

                                    Semua Hak Akses

                                </option>

                            </select>

                        </div>

                        {{-- AKSI --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                Aktivitas

                            </label>

                            <select name="aksi" class="form-select">

                                <option value="">

                                    Semua

                                </option>

                                <option value="tambah" {{ request('aksi') == 'tambah' ? 'selected' : '' }}>

                                    Ditambahkan

                                </option>

                                <option value="hapus" {{ request('aksi') == 'hapus' ? 'selected' : '' }}>

                                    Dihapus

                                </option>

                            </select>

                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                Tanggal Mulai

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

                        {{-- SEARCH --}}
                        <div class="col-md-6">

                            <label class="form-label">

                                Pencarian

                            </label>

                            <input type="text" name="search" class="form-control"
                                placeholder="Cari asset, user atau hak akses..." value="{{ request('search') }}">

                        </div>

                    </div>

                    <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">

                        <button type="submit" class="btn btn-primary">

                            <i class="bx bx-search-alt me-1"></i> Filter

                        </button>

                        <a href="{{ route('history.hak-akses.index') }}" class="btn btn-outline-secondary">

                            <i class="bx bx-reset me-1"></i> Reset

                        </a>
                        <a href="{{ route('history.hak-akses.cetak', request()->query()) }}" target="_blank"
                            class="btn btn-danger">

                            <i class="bx bxs-file-pdf me-1"></i> Cetak PDF

                        </a>
                        <a href="{{ route('history.hak-akses.export_excel', request()->query()) }}"
                            class="btn btn-success">

                            <i class="bx bxs-file-export me-1"></i> Export Excel

                        </a>

                    </div>


                </form>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- TABEL HISTORY --}}
        {{-- ========================================================= --}}
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-0">

                            <i class="bx bx-history me-2 text-primary"></i>

                            Riwayat Hak Akses

                        </h5>

                    </div>

                    <span class="badge bg-label-primary">

                        Total :
                        {{ $histories->total() }}
                        Data

                    </span>

                </div>

            </div>
            <div class="table-responsive px-3 pb-3">

                <table class="table table-hover table-bordered align-middle mb-0 table-nowrap">


                    <thead class="table-primary">

                        <tr class="text-center">

                            <th width="60">No</th>

                            <th width="150">Tanggal</th>

                            <th width="170">Asset</th>

                            <th width="200">User Asset</th>

                            <th width="220">Hak Akses / Aplikasi</th>

                            <th width="140">Jenis</th>

                            <th width="130">Aktivitas</th>

                            <th width="170">Oleh</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($histories as $history)
                            <tr>

                                <td>

                                    {{ $loop->iteration + ($histories->currentPage() - 1) * $histories->perPage() }}

                                </td>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $history->created_at->format('d-m-Y') }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $history->created_at->format('H:i:s') }}

                                    </small>

                                </td>

                                <td>

                                    <div class="fw-bold text-primary">

                                        {{ $history->maping->keluar->inventaris->no_inventaris ?? '-' }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $history->maping->keluar->inventaris->kode_aset ?? '-' }}

                                    </small>

                                </td>
                                <td>

                                    <div class="fw-semibold">

                                        {{ $history->maping->penerima ?? '-' }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $history->maping->lokasi->nama_lokasi ?? '-' }}

                                    </small>

                                </td>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $history->access?->nama_akses ?? $history->nama_akses ?? '-' }}

                                    </div>
                                    @if(!empty($history->email))
                                        <small class="text-primary d-block font-monospace"><i class="bx bx-envelope me-1"></i>{{ $history->email }}</small>
                                    @endif

                                </td>

                                <td>

                                    @if (($history->access?->kategori ?? $history->kategori) == 'Aplikasi')
                                        <span class="badge bg-label-info">

                                            Aplikasi

                                        </span>
                                    @elseif(($history->access?->jenis ?? $history->jenis) == 'PPN')
                                        <span class="badge bg-label-warning">

                                            Hak Akses PPN

                                        </span>
                                    @else
                                        <span class="badge bg-label-success">

                                            Hak Akses NON PPN

                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($history->aksi == 'tambah')
                                        <span class="badge bg-success">
                                            Ditambahkan
                                        </span>
                                    @elseif ($history->aksi == 'update')
                                        <span class="badge bg-warning text-dark">
                                            Diubah
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Dihapus
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $history->user->name ?? '-' }}

                                    </div>

                                    <small class="text-muted">

                                        {{ ucfirst(str_replace('_', ' ', $history->user->role ?? '-')) }}

                                    </small>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8">

                                    <div class="text-center py-5">

                                        <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}"
                                            width="180">

                                        <h5 class="mt-3">

                                            Belum ada History Hak Akses

                                        </h5>

                                        <p class="text-muted">

                                            Riwayat penambahan maupun penghapusan hak akses akan muncul di sini.

                                        </p>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($histories->count())
                <div class="card-footer bg-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <small class="text-muted">

                            Menampilkan

                            {{ $histories->firstItem() }}

                            -

                            {{ $histories->lastItem() }}

                            dari

                            {{ $histories->total() }}

                            data

                        </small>

                        {{ $histories->links() }}

                    </div>

                </div>
            @endif

        </div>

    </div>

@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            function loadHakAkses() {
                let jenis = $('#jenis').val();
                let perusahaanId = $('select[name="perusahaan_id"]').val();
                let access = $('#access_id');
                let selectedAccess = "{{ request('access_id') }}";

                access.html('<option value="">Semua Hak Akses</option>');

                let targetJenis = jenis ? jenis : 'all';
                let url = "{{ route('hak-akses.filter', ':jenis') }}".replace(':jenis', encodeURIComponent(targetJenis));

                $.ajax({
                    url: url,
                    type: "GET",
                    data: { perusahaan_id: perusahaanId },
                    dataType: "json",
                    success: function(response) {
                        $.each(response, function(i, item) {
                            let isSelected = selectedAccess == item.id ? 'selected' : '';
                            let labelKet = item.jenis ? ` (${item.jenis})` : (item.kategori ? ` (${item.kategori})` : '');
                            access.append(`<option value="${item.id}" ${isSelected}>${item.nama_akses}${labelKet}</option>`);
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }

            $('#jenis, select[name="perusahaan_id"]').on('change', function() {
                loadHakAkses();
            });

            if ($('#jenis').val() || $('select[name="perusahaan_id"]').val() || "{{ request('access_id') }}") {
                loadHakAkses();
            }
        });
    </script>
@endsection
