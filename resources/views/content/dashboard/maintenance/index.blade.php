@extends('layouts/contentNavbarLayout')

@section('title', 'Service & Maintenance')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-wrench fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Service & Maintenance</h3>
                            <small class="text-muted">Kelola transaksi perbaikan, klaim garansi, dan perawatan aset</small>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Input Service
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">




        <div class="card-body border-bottom">

            <form action="{{ route('maintenance.index') }}" method="GET">

                <div class="row">

                    {{-- Perusahaan --}}
                    @if (auth()->user()->role == 'super_admin')
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-uppercase fw-semibold">
                                Perusahaan
                            </label>

                            <select name="perusahaan_id" class="form-select">
                                <option value="">Semua</option>

                                @foreach ($perusahaans as $perusahaan)
                                    <option value="{{ $perusahaan->id }}"
                                        {{ request('perusahaan_id') == $perusahaan->id ? 'selected' : '' }}>
                                        {{ $perusahaan->nama_perusahaan }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    @endif

                    {{-- Tanggal Awal --}}
                    <div class="col-md-3 mb-3">

                        <label class="form-label text-uppercase fw-semibold">

                            Tanggal Awal

                        </label>

                        <input type="date" name="tanggal_awal" class="form-control"
                            value="{{ request('tanggal_awal') }}">

                    </div>

                    {{-- Tanggal Akhir --}}
                    <div class="col-md-3 mb-3">

                        <label class="form-label text-uppercase fw-semibold">

                            Tanggal Akhir

                        </label>

                        <input type="date" name="tanggal_akhir" class="form-control"
                            value="{{ request('tanggal_akhir') }}">

                    </div>

                    {{-- Jenis --}}
                    <div class="col-md-3 mb-3">

                        <label class="form-label text-uppercase fw-semibold">

                            Jenis

                        </label>

                        <select name="jenis" class="form-select">

                            <option value="">Semua</option>

                            <option value="Service" {{ request('jenis') == 'Service' ? 'selected' : '' }}>
                                Service
                            </option>

                            <option value="Maintenance" {{ request('jenis') == 'Maintenance' ? 'selected' : '' }}>
                                Maintenance
                            </option>

                        </select>

                    </div>

                </div>
                <div class="row">

                    {{-- Status --}}
                    <div class="col-md-3 mb-3">

                        <label class="form-label text-uppercase fw-semibold">

                            Status

                        </label>

                        <select name="status" class="form-select">

                            <option value="">Semua</option>

                            <option value="Pengajuan" {{ request('status') == 'Pengajuan' ? 'selected' : '' }}>
                                Pengajuan
                            </option>

                            <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>

                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>

                            <option value="Tidak Dapat Diperbaiki"
                                {{ request('status') == 'Tidak Dapat Diperbaiki' ? 'selected' : '' }}>
                                Tidak Dapat Diperbaiki
                            </option>

                            <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>

                        </select>

                    </div>

                    {{-- Search --}}
                    <div class="col-md-7 mb-3">

                        <label class="form-label text-uppercase fw-semibold">

                            Cari

                        </label>

                        <input type="text" name="search" class="form-control"
                            placeholder="Kode Service / Kode Aset / No Inventaris" value="{{ request('search') }}">

                    </div>

                    {{-- Tombol --}}
                    <div class="col-12 d-flex align-items-center gap-2 flex-wrap mt-2">

                        <button type="submit" class="btn btn-primary">

                            <i class="bx bx-search me-1"></i> Filter

                        </button>

                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">

                            <i class="bx bx-reset me-1"></i> Reset

                        </a>

                        <a href="{{ route('maintenance.cetak', request()->query()) }}" target="_blank"
                            class="btn btn-danger">

                            <i class="bx bxs-file-pdf me-1"></i> Cetak PDF

                        </a>

                        <a href="{{ route('maintenance.export_excel', request()->query()) }}"
                            class="btn btn-success">

                            <i class="bx bxs-file-export me-1"></i> Export Excel

                        </a>

                    </div>

                </div>
            </form>

        </div>
        <div class="table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>No Transaksi</th>

                        <th>Tanggal</th>

                        <th>Inventaris</th>
                        @if (auth()->user()->role == 'super_admin')
                            <th>Perusahaan</th>
                        @endif

                        <th>Jenis</th>
                        <th>Asal</th>

                        <th>Status</th>

                        <th>Biaya</th>

                        <th width="80">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($maintenances as $item)
                        <tr>

                            <td>
                                {{ $maintenances->firstItem() + $loop->index }}
                            </td>

                            <td>
                                {{ $item->kode_service }}
                                @if ($item->gambar)
                                    <br>
                                    <span class="badge bg-label-info text-info" title="Ada Foto Bukti Servis">
                                        <i class="bx bx-image me-1"></i> Foto
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $item->tanggal->format('d-m-Y') }}
                            </td>

                            <td>
                                <strong>{{ $item->inventaris->kode_aset }}</strong>
                                <br>
                                <small>{{ $item->inventaris->dataAset->nama_barang }}</small>
                            </td>
                            @if (auth()->user()->role == 'super_admin')
                                <td>
                                    {{ $item->inventaris->perusahaan->nama_perusahaan ?? '-' }}
                                </td>
                            @endif

                            <td>
                                @if ($item->jenis == 'Service')
                                    <span class="badge bg-label-danger">Service</span>
                                @else
                                    <span class="badge bg-label-warning">Maintenance</span>
                                @endif
                            </td>
                            <td>

                                @if ($item->asal == 'Manual')
                                    <span class="badge bg-label-secondary">
                                        Manual
                                    </span>
                                @elseif($item->asal == 'Mapping')
                                    <span class="badge bg-label-primary">
                                        Mapping
                                    </span>
                                @else
                                    <span class="badge bg-label-info">
                                        Peminjaman
                                    </span>
                                @endif

                            </td>

                            <td>
                                @switch($item->status)
                                    @case('Pengajuan')
                                        <span class="badge bg-secondary">Pengajuan</span>
                                    @break

                                    @case('Diproses')
                                        <span class="badge bg-info">Diproses</span>
                                    @break

                                    @case('Selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @break

                                    @case('Tidak Dapat Diperbaiki')
                                        <span class="badge bg-danger">Tidak Dapat Diperbaiki</span>
                                    @break

                                    @default
                                        <span class="badge bg-dark">Dibatalkan</span>
                                @endswitch
                            </td>

                            <td>
                                Rp {{ number_format($item->biaya, 0, ',', '.') }}
                            </td>

                            <td class="text-center">

                                <div class="btn-group shadow-sm" role="group">

                                    {{-- Detail --}}
                                    <a href="{{ route('maintenance.show', $item->id) }}"
                                        class="btn btn-info btn-sm text-white" data-bs-toggle="tooltip" title="Detail">

                                        <i class="bx bx-show"></i>

                                    </a>

                                    {{-- ========================= --}}
                                    {{-- STATUS PENGAJUAN --}}
                                    {{-- ========================= --}}
                                    @if ($item->status == 'Pengajuan')
                                        {{-- Edit --}}
                                        <a href="{{ route('maintenance.edit', $item->id) }}"
                                            class="btn btn-warning btn-sm text-white" data-bs-toggle="tooltip"
                                            title="Edit">

                                            <i class="bx bx-edit"></i>

                                        </a>

                                        {{-- Proses --}}
                                        <form action="{{ route('maintenance.proses', $item->id) }}" method="POST"
                                            class="d-inline form-proses">

                                            @csrf
                                            @method('PATCH')

                                            <button type="button" class="btn btn-primary btn-sm btn-proses"
                                                data-id="{{ $item->id }}" data-kode="{{ $item->kode_service }}"
                                                title="Proses">

                                                <i class="bx bx-play"></i>

                                            </button>

                                        </form>
                                        <form action="{{ route('maintenance.dibatalkan', $item->id) }}" method="POST"
                                            class="d-inline form-dibatalkan">

                                            @csrf
                                            @method('PATCH')

                                            <button class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                                                title="Batalkan">

                                                <i class="bx bx-x-circle text-white"></i>

                                            </button>

                                        </form>

                                        {{-- Hapus --}}
                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('maintenance.destroy', $item->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                data-id="{{ $item->id }}" data-kode="{{ $item->kode_service }}"
                                                title="Hapus">

                                                <i class="bx bx-trash"></i>

                                            </button>

                                        </form>
                                    @endif


                                    {{-- ========================= --}}
                                    {{-- STATUS DIPROSES --}}
                                    {{-- ========================= --}}
                                    @if ($item->status == 'Diproses')
                                        {{-- Selesai --}}
                                        <form action="{{ route('maintenance.selesai', $item->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            <button type="button" class="btn btn-success btn-sm btn-selesai"
                                                data-id="{{ $item->id }}" data-kode="{{ $item->kode_service }}"
                                                title="Selesai">

                                                <i class="bx bx-check"></i>

                                            </button>

                                        </form>

                                        {{-- Tidak Dapat Diperbaiki --}}
                                        <form action="{{ route('maintenance.tidakDapatDiperbaiki', $item->id) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            <button type="button" class="btn btn-dark btn-sm btn-rusak"
                                                data-id="{{ $item->id }}" data-kode="{{ $item->kode_service }}"
                                                title="Tidak Dapat Diperbaiki">

                                                <i class="bx bx-x"></i>

                                            </button>

                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                        @empty

                            <tr>
                                <td colspan="{{ auth()->user()->role == 'super_admin' ? 9 : 8 }}" class="text-center py-4">
                                    <i class="bx bx-folder-open fs-2 d-block mb-2"></i>
                                    Belum ada data Service & Maintenance.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="card-footer">
                {{ $maintenances->links() }}
            </div>

        </div>

    @endsection
    @section('page-script')

        <script>
            $(document).on('click', '.btn-delete', function() {

                let id = $(this).data('id');
                let kode = $(this).data('kode');

                Swal.fire({

                    title: 'Hapus Data?',

                    html: 'Transaksi <br><strong>' +
                        kode +
                        '</strong><br>akan dihapus.',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#696cff',

                    cancelButtonColor: '#8592a3',

                    confirmButtonText: '<i class="bx bx-trash"></i> Ya, Hapus',

                    cancelButtonText: 'Batal',

                    reverseButtons: true

                }).then((result) => {

                    if (result.isConfirmed) {

                        $('#delete-form-' + id).submit();

                    }

                });

            });
            $(document).on('click', '.btn-proses', function() {

                let id = $(this).data('id');
                let kode = $(this).data('kode');

                Swal.fire({

                    title: 'Proses Service?',

                    html: 'Transaksi <br><strong>' + kode +
                        '</strong><br>akan diproses.',

                    icon: 'question',

                    showCancelButton: true,

                    confirmButtonColor: '#696cff',

                    cancelButtonColor: '#8592a3',

                    confirmButtonText: '<i class="bx bx-play"></i> Ya, Proses',

                    cancelButtonText: 'Batal',

                    reverseButtons: true

                }).then((result) => {

                    if (result.isConfirmed) {

                        $(this)
                            .closest('form')
                            .submit();

                    }

                });

            });
            $(document).on('click', '.btn-selesai', function() {

                let form = $(this).closest('form');

                let kode = $(this).data('kode');

                Swal.fire({

                    title: 'Selesaikan Service?',

                    html: 'Transaksi <br><strong>' + kode +
                        '</strong><br>akan diselesaikan.',

                    icon: 'question',

                    showCancelButton: true,

                    confirmButtonText: 'Ya',

                    cancelButtonText: 'Batal'

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });
            $(document).on('click', '.btn-rusak', function() {

                let form = $(this).closest('form');

                let kode = $(this).data('kode');

                Swal.fire({

                    title: 'Tidak Dapat Diperbaiki?',

                    html: 'Transaksi <strong>' + kode + '</strong><br><br>' +
                        'Inventaris akan berubah menjadi <b>RUSAK</b>.',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#d33',

                    cancelButtonColor: '#6c757d',

                    confirmButtonText: 'Ya',

                    cancelButtonText: 'Batal'

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });
            $('.form-dibatalkan').submit(function(e) {

                e.preventDefault();

                let form = this;

                Swal.fire({

                    title: 'Batalkan Service?',

                    text: 'Pengajuan Service akan dibatalkan.',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonText: 'Ya',

                    cancelButtonText: 'Batal'

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });
        </script>

    @endsection
