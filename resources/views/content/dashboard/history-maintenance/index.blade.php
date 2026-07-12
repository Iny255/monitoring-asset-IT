@extends('layouts/contentNavbarLayout')

@section('title', 'History Service & Maintenance')

@section('content')
<div class="row">

    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h4 class="fw-bold mb-1">

                    History Service & Maintenance

                </h4>

                <p class="text-muted mb-0">

                    Riwayat seluruh aktivitas Service & Maintenance Inventaris.

                </p>

            </div>

            <div>

                <a href="{{ route('maintenance.index') }}"
                    class="btn btn-secondary">

                    <i class="bx bx-arrow-back"></i>

                    Kembali

                </a>

            </div>

        </div>

    </div>

</div>
{{-- ===========================
    FILTER
=========================== --}}
<div class="row">

    <div class="col-12">

        <div class="card mb-4">

            <div class="card-header">

                <h5 class="mb-0">

                    <i class="bx bx-filter-alt"></i>

                    Filter Data

                </h5>

            </div>

            <div class="card-body">

                <form method="GET"
                    action="{{ route('history.maintenance.index') }}">

                    <div class="row">

                        {{-- Perusahaan --}}
                        @if(auth()->user()->role=='super_admin')

                            <div class="col-md-3 mb-3">

                                <label class="form-label">

                                    Perusahaan

                                </label>

                                <select
                                    name="perusahaan_id"
                                    class="form-select">

                                    <option value="">

                                        Semua

                                    </option>

                                    @foreach($perusahaans as $perusahaan)

                                        <option
                                            value="{{ $perusahaan->id }}"
                                            {{ request('perusahaan_id')==$perusahaan->id ? 'selected' : '' }}>

                                            {{ $perusahaan->nama_perusahaan }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        @endif

                        {{-- Tanggal Awal --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Tanggal Awal

                            </label>

                            <input
                                type="date"
                                name="tanggal_awal"
                                class="form-control"
                                value="{{ request('tanggal_awal') }}">

                        </div>

                        {{-- Tanggal Akhir --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Tanggal Akhir

                            </label>

                            <input
                                type="date"
                                name="tanggal_akhir"
                                class="form-control"
                                value="{{ request('tanggal_akhir') }}">

                        </div>

                        {{-- Jenis --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Jenis

                            </label>

                            <select
                                name="jenis"
                                class="form-select">

                                <option value="">

                                    Semua

                                </option>

                                <option
                                    value="Service"
                                    {{ request('jenis')=='Service'?'selected':'' }}>

                                    Service

                                </option>

                                <option
                                    value="Maintenance"
                                    {{ request('jenis')=='Maintenance'?'selected':'' }}>

                                    Maintenance

                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="row">

                        {{-- Status --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Status

                            </label>

                            <select
                                name="status"
                                class="form-select">

                                <option value="">Semua</option>

                                <option value="Pengajuan"
                                    {{ request('status')=='Pengajuan'?'selected':'' }}>
                                    Pengajuan
                                </option>

                                <option value="Diproses"
                                    {{ request('status')=='Diproses'?'selected':'' }}>
                                    Diproses
                                </option>

                                <option value="Selesai"
                                    {{ request('status')=='Selesai'?'selected':'' }}>
                                    Selesai
                                </option>

                                <option value="Tidak Dapat Diperbaiki"
                                    {{ request('status')=='Tidak Dapat Diperbaiki'?'selected':'' }}>
                                    Tidak Dapat Diperbaiki
                                </option>

                                <option value="Dibatalkan"
                                    {{ request('status')=='Dibatalkan'?'selected':'' }}>
                                    Dibatalkan
                                </option>

                            </select>

                        </div>

                        {{-- Asal --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Asal

                            </label>

                            <select
                                name="asal"
                                class="form-select">

                                <option value="">

                                    Semua

                                </option>

                                <option
                                    value="Manual"
                                    {{ request('asal')=='Manual'?'selected':'' }}>

                                    Manual

                                </option>

                                <option
                                    value="Mapping"
                                    {{ request('asal')=='Mapping'?'selected':'' }}>

                                    Mapping

                                </option>

                                <option
                                    value="Peminjaman"
                                    {{ request('asal')=='Peminjaman'?'selected':'' }}>

                                    Peminjaman

                                </option>

                            </select>

                        </div>

                        {{-- Search --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Pencarian

                            </label>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Kode Service, Kode Aset, No Inventaris, Vendor..."
                                value="{{ request('search') }}">

                        </div>

                    </div>

                    <div class="d-flex justify-content-end">

                        <button
                            type="submit"
                            class="btn btn-primary me-2">

                            <i class="bx bx-search"></i>

                            Filter

                        </button>

                        <a href="{{ route('history.maintenance.index') }}"
                            class="btn btn-outline-secondary me-2">

                            <i class="bx bx-reset"></i>

                            Reset

                        </a>

                        <a href="{{ route('history.maintenance.cetak', request()->query()) }}"
                            target="_blank"
                            class="btn btn-danger">

                            <i class="bx bxs-file-pdf"></i>

                            Cetak PDF

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
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

                                @if(auth()->user()->role=='super_admin')

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

                                    @if(auth()->user()->role=='super_admin')

                                        <td>

                                            {{ $item->inventaris->perusahaan->nama_perusahaan ?? '-' }}

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
                                            class="btn btn-sm btn-info"
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
@endsection