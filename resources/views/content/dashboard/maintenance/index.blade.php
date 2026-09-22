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
                                    <a class="dropdown-item" href="{{ route('maintenance.cetak', request()->query()) }}" target="_blank">
                                        <i class="bx bxs-file-pdf text-danger me-2"></i> Export PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('maintenance.export_excel', request()->query()) }}">
                                        <i class="bx bxs-file-export text-success me-2"></i> Export Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Input Service
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <x-company-filter-banner />

        <div class="card border-0 shadow-sm">
            @if(request()->anyFilled(['perusahaan_id', 'tanggal_awal', 'tanggal_akhir', 'jenis', 'status', 'search']))
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-label-primary px-3 py-2">
                            <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                        </span>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            @endif
        <div class="table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>No Transaksi</th>

                        <th>Tanggal</th>

                        <th>Kode Aset</th>
                        @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                            <th>Perusahaan</th>
                        @endif

                        <th>Jenis</th>

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
                                {{ $item->tanggal ? $item->tanggal->format('d-m-Y') : ($item->created_at ? $item->created_at->format('d-m-Y') : '-') }}
                            </td>

                            <td>
                                @if ($item->inventaris)
                                    <a href="{{ route('history.perjalanan.show', $item->inventaris_id ?? $item->inventaris->id) }}"
                                        class="fw-bold text-primary text-decoration-none"
                                        title="Lihat Riwayat Tracking Aset">
                                        {{ $item->inventaris->kode_aset ?? '-' }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        {{ $item->inventaris->dataAset?->kategori?->nama_barang ?? '-' }}
                                        @if (!empty($item->inventaris->dataAset?->merek))
                                            • {{ $item->inventaris->dataAset->merek }}
                                        @endif
                                        @if (!empty($item->inventaris->dataAset?->type))
                                            {{ $item->inventaris->dataAset->type }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                <td>
                                    <x-company-badge :perusahaan="$item->inventaris?->perusahaan" />
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

                                <div class="d-flex justify-content-center gap-1">

                                    {{-- Detail --}}
                                    <a href="{{ route('maintenance.show', $item->id) }}"
                                        class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" title="Detail">

                                        <i class="bx bx-show"></i>

                                    </a>

                                    {{-- ========================= --}}
                                    {{-- STATUS PENGAJUAN --}}
                                    {{-- ========================= --}}
                                    @if ($item->status == 'Pengajuan')
                                        {{-- Edit --}}
                                        <a href="{{ route('maintenance.edit', $item->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip"
                                            title="Edit">

                                            <i class="bx bx-edit"></i>

                                        </a>

                                        {{-- Proses --}}
                                        <form action="{{ route('maintenance.proses', $item->id) }}" method="POST"
                                            class="d-inline form-proses">

                                            @csrf
                                            @method('PATCH')

                                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-proses"
                                                data-id="{{ $item->id }}" data-kode="{{ $item->kode_service }}"
                                                title="Proses">

                                                <i class="bx bx-play"></i>

                                            </button>

                                        </form>
                                        <form action="{{ route('maintenance.dibatalkan', $item->id) }}" method="POST"
                                            class="d-inline form-dibatalkan">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip"
                                                title="Batalkan">

                                                <i class="bx bx-x-circle"></i>

                                            </button>

                                        </form>

                                        {{-- Hapus --}}
                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('maintenance.destroy', $item->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
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

                                            <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-selesai"
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

                                            <button type="button" class="btn btn-sm btn-icon btn-outline-dark btn-rusak"
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

    <!-- ================= FILTER MODAL ================= -->
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('maintenance.index') }}" method="GET">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Service & Maintenance
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @if (auth()->user()->role == 'super_admin')
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase fw-semibold">Perusahaan</label>
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

                            <div class="col-md-{{ auth()->user()->role == 'super_admin' ? '6' : '12' }}">
                                <label class="form-label text-uppercase fw-semibold">Jenis</label>
                                <select name="jenis" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Service" {{ request('jenis') == 'Service' ? 'selected' : '' }}>Service</option>
                                    <option value="Maintenance" {{ request('jenis') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="{{ request('tanggal_awal') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="{{ request('tanggal_akhir') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Pengajuan" {{ request('status') == 'Pengajuan' ? 'selected' : '' }}>Pengajuan</option>
                                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Tidak Dapat Diperbaiki" {{ request('status') == 'Tidak Dapat Diperbaiki' ? 'selected' : '' }}>Tidak Dapat Diperbaiki</option>
                                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold">Cari</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Kode Service / Kode Aset / Jenis / Merek / Type / No Inventaris" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
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
