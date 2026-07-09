@extends('layouts/contentNavbarLayout')

@section('title', 'Detail History Mutasi')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HEADER --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h3 class="fw-bold mb-1">

                        <i class="bx bx-transfer-alt text-primary me-2"></i>

                        Detail History Mutasi

                    </h3>

                    <small class="text-muted">

                        Detail riwayat perpindahan asset.

                    </small>

                </div>

                <div>

                    <a href="{{ route('history.mutasi.index') }}"
                        class="btn btn-outline-secondary">

                        <i class="bx bx-arrow-back"></i>

                        Kembali

                    </a>

                    <a href="{{ route('history.mutasi.cetak',$historyMutasi->id) }}"
                        target="_blank"
                        class="btn btn-success">

                        <i class="bx bx-printer"></i>

                        Cetak

                    </a>

                </div>

            </div>

        </div>

    </div>
    <div class="card border-0 shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Ringkasan Asset

        </h5>

    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>

                <td width="220">Nama Asset</td>

                <td>

                    {{ $historyMutasi->nama_aset }}

                </td>

            </tr>

            <tr>

                <td>Kode Asset Lama</td>

                <td>

                    {{ $historyMutasi->kode_aset_lama }}

                </td>

            </tr>

            <tr>

                <td>Kode Asset Baru</td>

                <td>

                    {{ $historyMutasi->kode_aset_baru }}

                </td>

            </tr>

            <tr>

                <td>No Inventaris Lama</td>

                <td>

                    {{ $historyMutasi->no_inventaris_lama }}

                </td>

            </tr>

            <tr>

                <td>No Inventaris Baru</td>

                <td>

                    {{ $historyMutasi->no_inventaris_baru }}

                </td>

            </tr>

        </table>

    </div>

</div>
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Perjalanan Mutasi

        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <div class="alert alert-light">

                    <h6>User Lama</h6>

                    <strong>

                        {{ $historyMutasi->user_lama }}

                    </strong>

                    <hr>

                    <small>

                        Lokasi

                    </small>

                    <br>

                    {{ $historyMutasi->lokasi_lama }}

                </div>

            </div>

            <div class="col-md-6">

                <div class="alert alert-success">

                    <h6>User Baru</h6>

                    <strong>

                        {{ $historyMutasi->user_baru }}

                    </strong>

                    <hr>

                    <small>

                        Lokasi

                    </small>

                    <br>

                    {{ $historyMutasi->lokasi_baru }}

                </div>

            </div>

        </div>

    </div>

</div>
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Perlakuan Hak Akses

        </h5>

    </div>

    <div class="card-body">

        @if($historyMutasi->opsi_hak_akses=='copy')

            <div class="alert alert-success mb-0">

                <i class="bx bx-check-circle me-2"></i>

                Hak akses lama disalin ke user baru.

            </div>

        @else

            <div class="alert alert-warning mb-0">

                <i class="bx bx-edit me-2"></i>

                Hak akses diatur secara manual melalui menu Kelola Hak Akses.

            </div>

        @endif

    </div>

</div>
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Catatan

        </h5>

    </div>

    <div class="card-body">

        {{ $historyMutasi->catatan ?: '-' }}

    </div>

</div>
<div class="card border-0 shadow-sm">

    <div class="card-header">

        <h5>

            Informasi Mutasi

        </h5>

    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>

                <td width="220">

                    Jenis Mutasi

                </td>

                <td>

                    {{ strtoupper(str_replace('_',' ',$historyMutasi->jenis_mutasi)) }}

                </td>

            </tr>

            <tr>

                <td>

                    Perusahaan Asal

                </td>

                <td>

                    {{ optional($historyMutasi->perusahaanAsal)->nama_perusahaan }}

                </td>

            </tr>

            <tr>

                <td>

                    Perusahaan Tujuan

                </td>

                <td>

                    {{ optional($historyMutasi->perusahaanTujuan)->nama_perusahaan }}

                </td>

            </tr>

            <tr>

                <td>

                    Tanggal Mutasi

                </td>

                <td>

                    {{ \Carbon\Carbon::parse($historyMutasi->tanggal_mutasi)->format('d F Y') }}

                </td>

            </tr>

            <tr>

                <td>

                    Petugas

                </td>

                <td>

                    {{ optional($historyMutasi->creator)->name }}

                </td>

            </tr>

        </table>

    </div>

</div>

</div>

@endsection
