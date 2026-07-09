@extends('layouts/contentNavbarLayout')

@section('title','Pencabutan Asset')

@section('content')

<form method="POST"
      action="{{ route('pencabutan.store',$maping->id) }}">

@csrf

<div class="card mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <div class="d-flex align-items-center">

                <div class="avatar avatar-lg bg-label-danger me-3">

                    <i class="bx bx-power-off fs-2"></i>

                </div>

                <div>

                    <h3 class="mb-1">

                        Pencabutan Asset

                    </h3>

                    <small class="text-muted">

                        Proses pengembalian asset ke perusahaan.

                    </small>

                </div>

            </div>

            <a href="{{ route('maping.index') }}"
               class="btn btn-outline-secondary">

                <i class="bx bx-arrow-back me-1"></i>

                Kembali

            </a>

        </div>

    </div>

</div>
<div class="card mb-4">

    <div class="card-header">

        <h4 class="mb-0">

            <i class="bx bx-package me-2"></i>

            Informasi Asset

        </h4>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Kode Asset

                </label>

                <input
                    class="form-control"
                    readonly
                    value="{{ $maping->keluar->inventaris->kode_aset }}">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    No Inventaris

                </label>

                <input
                    class="form-control"
                    readonly
                    value="{{ $maping->keluar->inventaris->no_inventaris }}">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Nama Asset

                </label>

                <input
                    class="form-control"
                    readonly
                    value="{{ $maping->keluar->inventaris->dataAset->kategori->nama_barang }}">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Perusahaan

                </label>

                <input
                    class="form-control"
                    readonly
                    value="{{ $maping->perusahaan->nama_perusahaan }}">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    User Asset

                </label>

                <input
                    class="form-control"
                    readonly
                    value="{{ $maping->penerima }}">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Lokasi Saat Ini

                </label>

                <input
                    class="form-control"
                    readonly
                    value="{{ $maping->lokasi->nama_lokasi }}">

            </div>

        </div>

    </div>

</div>
<div class="card mb-4">

    <div class="card-header">

        <h4 class="mb-0">

            <i class="bx bx-edit me-2"></i>

            Form Pencabutan

        </h4>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Tanggal Pencabutan *

                </label>

                <input
                    type="date"
                    name="tanggal_pencabutan"
                    class="form-control"
                    value="{{ date('Y-m-d') }}">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Lokasi Setelah Dicabut *

                </label>

                <select
                    name="id_lokasi"
                    id="id_lokasi"
                    class="form-select">

                    <option value="">

                        Pilih Lokasi

                    </option>

                    @foreach($lokasis as $lokasi)

                        <option value="{{ $lokasi->id }}">

                            {{ $lokasi->nama_lokasi }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Status

                </label>

                <input
                    class="form-control"
                    readonly
                    value="Selesai">

            </div>

            <div class="col-md-12">

                <label class="form-label">

                    Alasan Pencabutan

                </label>

                <textarea
                    name="alasan"
                    rows="4"
                    class="form-control"
                    placeholder="Masukkan alasan pencabutan asset..."></textarea>

            </div>

        </div>

    </div>

</div>
<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex align-items-center">

            <div class="avatar avatar-md bg-label-danger me-3">

                <i class="bx bx-power-off fs-4"></i>

            </div>

            <div>

                <h4 class="mb-0">

                    Ringkasan Pencabutan

                </h4>

                <small class="text-muted">

                    Informasi asset yang akan dicabut

                </small>

            </div>

        </div>

    </div>

    <div class="card-body">

        <div class="row">

            <!-- KIRI -->

            <div class="col-lg-6">

                <table class="table table-borderless">

                    <tr>

                        <td width="180">

                            <strong>Kode Asset</strong>

                        </td>

                        <td>

                            {{ $maping->keluar->inventaris->kode_aset }}

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>No Inventaris</strong>

                        </td>

                        <td>

                            {{ $maping->keluar->inventaris->no_inventaris }}

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>Nama Asset</strong>

                        </td>

                        <td>

                            {{ $maping->keluar->inventaris->dataAset->kategori->nama_barang }}

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>Perusahaan</strong>

                        </td>

                        <td>

                            {{ $maping->perusahaan->nama_perusahaan }}

                        </td>

                    </tr>

                </table>

            </div>


            <!-- KANAN -->

            <div class="col-lg-6">

                <table class="table table-borderless">

                    <tr>

                        <td width="180">

                            <strong>User Lama</strong>

                        </td>

                        <td>

                            {{ $maping->penerima }}

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>Lokasi Lama</strong>

                        </td>

                        <td>

                            {{ $maping->lokasi->nama_lokasi }}

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>Lokasi Baru</strong>

                        </td>

                        <td>

                            <span id="previewLokasi">

                                -

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>Status</strong>

                        </td>

                        <td>

                            <span class="badge bg-label-success">

                                Selesai

                            </span>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>
<div class="d-flex justify-content-end mb-5">

    <button
        type="submit"
        id="btnSimpan"
        class="btn btn-danger">

        <i class="bx bx-power-off me-1"></i>

        Simpan Pencabutan

    </button>

</div>

</form>
@endsection
@section('scripts')

<script>

$(function(){

    $('#id_lokasi').change(function(){

        let lokasi = $('#id_lokasi option:selected').text();

        if($(this).val()==""){

            lokasi='-';

        }

        $('#previewLokasi').text(lokasi);

    });

});

</script>

@endsection