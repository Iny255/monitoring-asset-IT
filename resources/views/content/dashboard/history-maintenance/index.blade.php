@extends('layouts/contentNavbarLayout')

@section('title', 'History Service & Maintenance')

@section('content')
<div class="row">

    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>

                <h4 class="fw-bold mb-1">

                    History Service & Maintenance

                </h4>

                <p class="text-muted mb-0">

                    Riwayat seluruh aktivitas Service & Maintenance Inventaris.

                </p>

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
                            <a class="dropdown-item" href="{{ route('history.maintenance.cetak', request()->query()) }}" target="_blank">
                                <i class="bx bxs-file-pdf text-danger me-2"></i> Cetak PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('history.maintenance.export_excel', request()->query()) }}">
                                <i class="bx bxs-file-export text-success me-2"></i> Export Excel
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('maintenance.index') }}"
                    class="btn btn-secondary">

                    <i class="bx bx-arrow-back"></i>

                    Kembali

                </a>

            </div>

        </div>

    </div>

</div>
@if(request()->anyFilled(['perusahaan_id', 'tanggal_awal', 'tanggal_akhir', 'jenis', 'status', 'asal', 'search']))
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="badge bg-label-primary px-3 py-2">
            <i class="bx bx-filter-alt me-1"></i> Filter Aktif
        </span>
        <a href="{{ route('history.maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-x me-1"></i> Reset Filter
        </a>
    </div>
@endif

<x-company-filter-banner />

{{-- ===========================
    DATA HISTORY
=========================== --}}
<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0">

                    <i class="bx bx-history"></i>

                    History Service & Maintenance

                </h5>

                <span class="badge bg-primary">

                    Total Data :
                    {{ $laporan->total() }}

                </span>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="50" class="text-center">

                                    No

                                </th>

                                <th width="100">

                                    Tanggal

                                </th>

                                @if(in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)

                                    <th>

                                        Perusahaan

                                    </th>

                                @endif

                                <th width="150">

                                    Kode Service

                                </th>

                                <th>

                                    Inventaris

                                </th>

                                <th width="120">

                                    Jenis

                                </th>

                                <th width="120">

                                    Asal

                                </th>

                                <th width="170">

                                    Status

                                </th>

                                <th>

                                    Vendor

                                </th>

                                <th width="120">

                                    Biaya

                                </th>

                                <th width="90" class="text-center">

                                    Aksi

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($laporan as $item)

                                <tr>

                                    <td class="text-center">

                                        {{ $laporan->firstItem() + $loop->index }}

                                    </td>

                                    <td>

                                        {{ $item->tanggal->format('d-m-Y') }}

                                    </td>

                                    @if(in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)

                                        <td>
                                            <x-company-badge :perusahaan="$item->inventaris?->perusahaan" />
                                        </td>

                                    @endif

                                    <td>

                                        <strong>

                                            {{ $item->kode_service }}

                                        </strong>

                                    </td>

                                    <td>

                                        <strong>

                                            {{ $item->inventaris->kode_aset }}

                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            {{ $item->inventaris->dataAset->nama_barang }}

                                            {{ $item->inventaris->dataAset->merek }}

                                            {{ $item->inventaris->dataAset->type }}

                                        </small>

                                    </td>

                                    <td>

                                        @if($item->jenis=='Service')

                                            <span class="badge bg-label-primary">

                                                Service

                                            </span>

                                        @else

                                            <span class="badge bg-label-info">

                                                Maintenance

                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($item->asal=='Manual')

                                            <span class="badge bg-label-secondary">

                                                Manual

                                            </span>

                                        @elseif($item->asal=='Mapping')

                                            <span class="badge bg-label-success">

                                                Mapping

                                            </span>

                                        @else

                                            <span class="badge bg-label-warning">

                                                Peminjaman

                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @switch($item->status)

                                            @case('Pengajuan')

                                                <span class="badge bg-label-warning">

                                                    Pengajuan

                                                </span>

                                            @break

                                            @case('Diproses')

                                                <span class="badge bg-label-info">

                                                    Diproses

                                                </span>

                                            @break

                                            @case('Selesai')

                                                <span class="badge bg-label-success">

                                                    Selesai

                                                </span>

                                            @break

                                            @case('Tidak Dapat Diperbaiki')

                                                <span class="badge bg-label-danger">

                                                    Tidak Dapat Diperbaiki

                                                </span>

                                            @break

                                            @case('Dibatalkan')

                                                <span class="badge bg-label-dark">

                                                    Dibatalkan

                                                </span>

                                            @break

                                            @default

                                                <span class="badge bg-label-secondary">

                                                    {{ $item->status }}

                                                </span>

                                        @endswitch

                                    </td>

                                    <td>

                                        {{ $item->vendor ?: '-' }}

                                    </td>

                                    <td>

                                        Rp {{ number_format($item->biaya,0,',','.') }}

                                    </td>

                                    <td class="text-center">

                                        <a href="{{ route('maintenance.show',$item->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-primary"
                                            title="Detail">

                                            <i class="bx bx-show"></i>

                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ auth()->user()->role=='super_admin' ? 11 : 10 }}"
                                        class="text-center py-5">

                                        <i class="bx bx-folder-open fs-1 d-block mb-2"></i>

                                        Tidak ada data History Service & Maintenance.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            @if($laporan->hasPages())

                <div class="card-footer">

                    {{ $laporan->links() }}

                </div>

            @endif

        </div>

    </div>

</div>

<!-- ================= FILTER MODAL ================= -->
<div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="GET" action="{{ route('history.maintenance.index') }}">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-filter-alt me-2 text-primary"></i> Filter History Service & Maintenance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @if(auth()->user()->role=='super_admin')
                            <div class="col-md-6">
                                <label class="form-label">Perusahaan</label>
                                <select name="perusahaan_id" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($perusahaans as $perusahaan)
                                        <option value="{{ $perusahaan->id }}"
                                            {{ request('perusahaan_id')==$perusahaan->id ? 'selected' : '' }}>
                                            {{ $perusahaan->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-{{ auth()->user()->role == 'super_admin' ? '6' : '12' }}">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="">Semua</option>
                                <option value="Service" {{ request('jenis')=='Service'?'selected':'' }}>Service</option>
                                <option value="Maintenance" {{ request('jenis')=='Maintenance'?'selected':'' }}>Maintenance</option>
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

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua</option>
                                <option value="Pengajuan" {{ request('status')=='Pengajuan'?'selected':'' }}>Pengajuan</option>
                                <option value="Diproses" {{ request('status')=='Diproses'?'selected':'' }}>Diproses</option>
                                <option value="Selesai" {{ request('status')=='Selesai'?'selected':'' }}>Selesai</option>
                                <option value="Tidak Dapat Diperbaiki" {{ request('status')=='Tidak Dapat Diperbaiki'?'selected':'' }}>Tidak Dapat Diperbaiki</option>
                                <option value="Dibatalkan" {{ request('status')=='Dibatalkan'?'selected':'' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Asal</label>
                            <select name="asal" class="form-select">
                                <option value="">Semua</option>
                                <option value="Manual" {{ request('asal')=='Manual'?'selected':'' }}>Manual</option>
                                <option value="Mapping" {{ request('asal')=='Mapping'?'selected':'' }}>Mapping</option>
                                <option value="Peminjaman" {{ request('asal')=='Peminjaman'?'selected':'' }}>Peminjaman</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Pencarian</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Kode Service, Kode Aset, No Inventaris, Vendor..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('history.maintenance.index') }}" class="btn btn-secondary">
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