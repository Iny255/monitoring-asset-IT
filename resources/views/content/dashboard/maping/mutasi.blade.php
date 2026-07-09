@extends('layouts/contentNavbarLayout')

@section('title', 'Mutasi Asset')

@section('content')


    <style>
        #resultUser {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;

            width: 100%;

            background: #fff;

            border: 1px solid #d9dee3;

            border-radius: 8px;

            z-index: 9999;

            max-height: 250px;

            overflow-y: auto;

            box-shadow: 0 10px 25px rgba(0, 0, 0, .12);

            display: none;
        }

        #resultUser a {

            cursor: pointer;

        }

        #resultUser a:hover {

            background: #f5f5f5;

        }

        #resultUser .list-group-item {

            cursor: pointer;

            border: none;

            border-bottom: 1px solid #ececec;

        }

        #resultUser .list-group-item:last-child {

            border-bottom: none;

        }

        #resultUser .list-group-item:hover {

            background: #f5f7fb;

        }

        .access-card {

            transition: .25s;

            border: 1px solid #dbe2ea;

            cursor: pointer;

        }

        .access-card:hover {

            transform: translateY(-2px);

            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);

        }

        .access-card.active {

            border: 2px solid #696cff;

            background: #f8f9ff;

        }
    </style>
    @if (session('success'))
        <div class="alert alert-success">

            {{ session('success') }}

        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">

            {{ session('error') }}

        </div>
    @endif


    <div class="row">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}
        <div class="col-12 mb-4">

            <div class="card shadow-sm border-0">

                <div class="card-body d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center">

                        <div class="avatar avatar-lg bg-label-primary me-3">

                            <i class="bx bx-transfer fs-3"></i>

                        </div>

                        <div>

                            <h4 class="mb-1 fw-bold">
                                Mutasi Asset
                            </h4>

                            <small class="text-muted">
                                Proses mutasi internal maupun antar perusahaan.
                            </small>

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
        {{-- INFORMASI ASSET --}}
        {{-- ========================================================= --}}
        <div class="col-12">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        <i class="bx bx-package me-2"></i>

                        Informasi Asset

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row g-3">

                        {{-- Kode Asset --}}
                        <div class="col-md-4">

                            <label class="form-label">

                                Kode Asset

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->keluar->inventaris->kode_aset }}"
                                readonly>

                        </div>

                        {{-- No Inventaris --}}
                        <div class="col-md-4">

                            <label class="form-label">

                                No Inventaris

                            </label>

                            <input type="text" class="form-control"
                                value="{{ $maping->keluar->inventaris->no_inventaris }}" readonly>

                        </div>

                        {{-- Nama Asset --}}
                        <div class="col-md-4">

                            <label class="form-label">

                                Nama Asset

                            </label>

                            <input type="text" class="form-control"
                                value="{{ $maping->keluar->inventaris->dataAset->kategori->nama_barang }}" readonly>

                        </div>

                        {{-- Perusahaan --}}
                        <div class="col-md-4">

                            <label class="form-label">

                                Perusahaan

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->perusahaan->nama_perusahaan }}"
                                readonly>

                        </div>

                        {{-- User / Divisi Asset --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                User / Divisi Asset
                            </label>

                            <input type="text" class="form-control"
                                value="{{ $maping->keluar->jenis_penerima == 'Perorangan'
                                    ? optional($maping->karyawan)->nama_karyawan
                                    : $maping->keluar->divisi_klr }}"
                                readonly>

                        </div>
                        {{-- Lokasi --}}
                        <div class="col-md-4">

                            <label class="form-label">

                                Lokasi Saat Ini

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->lokasi->nama_lokasi }}" readonly>

                        </div>

                        {{-- Processor --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                Processor

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->processor }}" readonly>

                        </div>

                        {{-- RAM --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                RAM

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->ram }}" readonly>

                        </div>

                        {{-- Device ID --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                Device ID

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->device_id }}" readonly>

                        </div>

                        {{-- Product ID --}}
                        <div class="col-md-3">

                            <label class="form-label">

                                Product ID

                            </label>

                            <input type="text" class="form-control" value="{{ $maping->produk_id }}" readonly>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- FORM MUTASI --}}
        {{-- ========================================================= --}}

        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <b>Validasi gagal:</b>

                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('maping.mutasi.store', $maping->id) }}" method="POST">

                @csrf

                <div class="card shadow-sm border-0">

                    <div class="card-header">

                        <h5 class="mb-0">

                            <i class="bx bx-transfer-alt me-2"></i>

                            Form Mutasi Asset

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            {{-- ================================================= --}}
                            {{-- JENIS MUTASI --}}
                            {{-- ================================================= --}}

                            <div class="col-md-4">

                                <label class="form-label">

                                    Jenis Mutasi

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="jenis_mutasi" id="jenis_mutasi" class="form-select" required>

                                    <option value="">Pilih Jenis Mutasi</option>

                                    <option value="internal">

                                        Internal Perusahaan

                                    </option>

                                    <option value="antar_perusahaan">

                                        Antar Perusahaan

                                    </option>

                                </select>

                            </div>

                            {{-- ================================================= --}}
                            {{-- PERUSAHAAN TUJUAN --}}
                            {{-- ================================================= --}}

                            <div class="col-md-4" id="perusahaanArea" style="display:none;">

                                <label class="form-label">

                                    Perusahaan Tujuan

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="id_perusahaan_tujuan" id="id_perusahaan" class="form-select">

                                    <option value="">Pilih Perusahaan</option>

                                    @foreach ($perusahaans as $perusahaan)
                                        @if ($perusahaan->id != $maping->id_perusahaan)
                                            <option value="{{ $perusahaan->id }}">
                                                {{ $perusahaan->nama_perusahaan }}
                                            </option>
                                        @endif
                                    @endforeach

                                </select>

                            </div>

                            {{-- ================================================= --}}
                            {{-- LOKASI TUJUAN --}}
                            {{-- ================================================= --}}

                            <div class="col-md-4">

                                <label class="form-label">

                                    Lokasi Tujuan

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="id_lokasi" id="id_lokasi" class="form-select" required>

                                    <option value="">

                                        Pilih Lokasi

                                    </option>

                                </select>

                            </div>
                            {{-- ================================================= --}}
                            {{-- JENIS PENERIMA --}}
                            {{-- ================================================= --}}

                            <div class="col-md-4">

                                <label class="form-label">
                                    Jenis Penerima
                                </label>

                                <input type="text" class="form-control" value="{{ $maping->keluar->jenis_penerima }}"
                                    readonly>

                                <input type="hidden" name="jenis_penerima" id="jenis_penerima"
                                    value="{{ $maping->keluar->jenis_penerima }}">

                            </div>

                            {{-- ================================================= --}}
                            {{-- USER BARU --}}
                            {{-- ================================================= --}}
                            <div class="col-md-6" id="karyawanArea">

                                <label class="form-label">
                                    User Asset Baru
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="position-relative">

                                    <input type="text" id="searchUser" class="form-control"
                                        placeholder="Cari nama user..." autocomplete="off">

                                    <input type="hidden" name="id_karyawan" id="id_karyawan">

                                    <div id="resultUser" class="list-group">
                                    </div>

                                </div>

                            </div>
                            <div class="col-md-6" id="divisiArea" style="display:none;">

                                <label class="form-label">
                                    Divisi Tujuan
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="divisi" id="divisi" class="form-select">

                                    <option value="">Pilih Divisi</option>

                                </select>

                            </div>

                            {{-- ================================================= --}}
                            {{-- TANGGAL --}}
                            {{-- ================================================= --}}

                            <div class="col-md-3">

                                <label class="form-label">

                                    Tanggal Mutasi

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="date" name="tanggal_mutasi" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>

                            </div>

                            {{-- ================================================= --}}
                            {{-- STATUS --}}
                            {{-- ================================================= --}}

                            <div class="col-md-3">

                                <label class="form-label">

                                    Status

                                </label>

                                <input type="text" class="form-control" value="Menunggu Diproses" readonly>

                            </div>

                            {{-- ================================================= --}}
                            {{-- ALASAN --}}
                            {{-- ================================================= --}}

                            <div class="col-12">

                                <label class="form-label">

                                    Alasan Mutasi

                                </label>

                                <textarea name="catatan" rows="3" class="form-control" placeholder="Masukkan alasan mutasi asset..."></textarea>
                            </div>

                        </div>

                    </div>
                    <hr class="my-4">

                    {{-- ========================================================= --}}
                    {{-- RINGKASAN MUTASI --}}
                    {{-- ========================================================= --}}

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="col-12">

                            <div class="border rounded-3 p-4 bg-light">

                                <div class="d-flex align-items-center mb-4">

                                    <div class="avatar avatar-sm bg-label-primary me-3">

                                        <i class="bx bx-transfer"></i>

                                    </div>

                                    <div>

                                        <h5 class="mb-0">

                                            Ringkasan Mutasi

                                        </h5>

                                        <small class="text-muted">

                                            Informasi asset yang akan dimutasikan

                                        </small>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">

                                        <table class="table table-borderless table-sm mb-0">

                                            <tbody>

                                                <tr>

                                                    <td width="180" class="text-muted">

                                                        Kode Asset

                                                    </td>

                                                    <td width="10">

                                                        :

                                                    </td>

                                                    <td>

                                                        <strong>

                                                            {{ $maping->keluar->inventaris->kode_aset }}

                                                        </strong>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-muted">

                                                        No Inventaris

                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        {{ $maping->keluar->inventaris->no_inventaris }}

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-muted">

                                                        Nama Asset

                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        {{ $maping->keluar->inventaris->dataAset->kategori->nama_barang }}

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-muted">

                                                        Perusahaan Asal

                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        {{ $maping->perusahaan->nama_perusahaan }}

                                                    </td>

                                                </tr>

                                            </tbody>

                                        </table>

                                    </div>

                                    <div class="col-md-6">

                                        <table class="table table-borderless table-sm mb-0">

                                            <tbody>

                                                <tr>

                                                    <td width="180" class="text-muted">

                                                        Lokasi Saat Ini

                                                    </td>

                                                    <td width="10">

                                                        :

                                                    </td>

                                                    <td>

                                                        {{ $maping->lokasi->nama_lokasi }}

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-muted">

                                                        User Saat Ini

                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        {{ $maping->keluar->jenis_penerima == 'Perorangan'
                                                            ? optional($maping->karyawan)->nama_karyawan
                                                            : $maping->keluar->divisi_klr }}

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-muted">

                                                        Jenis Mutasi

                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        <span id="previewJenis" class="badge bg-label-secondary">

                                                            Belum Dipilih

                                                        </span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-muted">

                                                        Tujuan

                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        <span id="previewPerusahaan">-</span>

                                                        </span>

                                                    </td>

                                                </tr>
                                                <tr>

                                                    <td class="text-muted">
                                                        Penerima Baru
                                                    </td>

                                                    <td>:</td>

                                                    <td>

                                                        <span id="previewUser">-</span>

                                                    </td>

                                                </tr>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                    {{-- ========================================================= --}}
                    {{-- HAK AKSES --}}
                    {{-- ========================================================= --}}

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white">

                            <div class="d-flex align-items-center">

                                <div class="avatar avatar-sm bg-label-info me-3">

                                    <i class="bx bx-key"></i>

                                </div>

                                <div>

                                    <h5 class="mb-0">

                                        Hak Akses Asset

                                    </h5>

                                    <small class="text-muted">

                                        Pilih bagaimana hak akses akan diperlakukan setelah mutasi.

                                    </small>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            {{-- ========================================= --}}
                            {{-- HAK AKSES SAAT INI --}}
                            {{-- ========================================= --}}

                            <label class="form-label fw-semibold mb-3">

                                Hak Akses Saat Ini

                            </label>

                            <div class="row">

                                @forelse($maping->mapingAccesses as $akses)
                                    @php

                                        $kategori = strtoupper($akses->access->kategori ?? '');

                                        switch ($kategori) {
                                            case 'APLIKASI':
                                                $icon = 'bx-grid-alt';
                                                $bg = 'bg-label-primary';
                                                break;

                                            case 'HAK AKSES':
                                                $icon = 'bx-folder';
                                                $bg = 'bg-label-success';
                                                break;

                                            default:
                                                $icon = 'bx-shield';
                                                $bg = 'bg-label-secondary';
                                        }

                                    @endphp

                                    <div class="col-lg-4 col-md-6 mb-3">

                                        <div class="card border shadow-sm h-100">

                                            <div class="card-body">

                                                <div class="d-flex">

                                                    <div class="avatar avatar-md {{ $bg }} me-3">

                                                        <span class="avatar-initial rounded">

                                                            <i class="bx {{ $icon }}"></i>

                                                        </span>

                                                    </div>

                                                    <div class="flex-grow-1">

                                                        <h6 class="fw-bold mb-3">

                                                            {{ strtoupper($akses->access->nama_akses) }}

                                                        </h6>

                                                        <span class="badge bg-label-success me-1">

                                                            {{ $akses->access->kategori }}

                                                        </span>

                                                        @if ($akses->access->jenis)
                                                            <span class="badge bg-label-warning">

                                                                {{ $akses->access->jenis }}

                                                            </span>
                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="col-12">

                                        <div class="alert alert-warning mb-0">

                                            Asset ini belum memiliki hak akses.

                                        </div>

                                    </div>
                                @endforelse

                            </div>
                        </div>
                        {{-- ========================================================= --}}
                        {{-- PERLAKUAN HAK AKSES --}}
                        {{-- ========================================================= --}}

                        <div class="card border-0 shadow-sm mt-4">

                            <div class="card-header">

                                <h5 class="mb-0">

                                    <i class="bx bx-copy-alt me-2"></i>

                                    Perlakuan Hak Akses

                                </h5>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    {{-- COPY --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="w-100">

                                            <input type="radio" name="opsi_hak_akses" value="copy"
                                                class="d-none access-option" checked>

                                            <div class="card access-card active h-100">

                                                <div class="card-body">

                                                    <div class="d-flex">

                                                        <div class="avatar avatar-md bg-label-primary me-3">

                                                            <span class="avatar-initial rounded">

                                                                <i class="bx bx-copy-alt"></i>

                                                            </span>

                                                        </div>

                                                        <div class="flex-grow-1">

                                                            <div class="d-flex justify-content-between">

                                                                <h6 class="fw-bold">

                                                                    Salin Hak Akses Lama

                                                                </h6>

                                                                <span class="badge bg-label-success">

                                                                    Direkomendasikan

                                                                </span>

                                                            </div>

                                                            <p class="text-muted mt-2">

                                                                Seluruh aplikasi dan hak akses
                                                                akan dipindahkan ke user baru.

                                                            </p>

                                                            <span class="badge bg-label-primary">

                                                                COPY ACCESS

                                                            </span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </label>

                                    </div>

                                    {{-- MANUAL --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="w-100">

                                            <input type="radio" name="opsi_hak_akses" value="manual"
                                                class="d-none access-option">

                                            <div class="card access-card h-100">

                                                <div class="card-body">

                                                    <div class="d-flex">

                                                        <div class="avatar avatar-md bg-label-warning me-3">

                                                            <span class="avatar-initial rounded">

                                                                <i class="bx bx-edit"></i>

                                                            </span>

                                                        </div>

                                                        <div class="flex-grow-1">

                                                            <h6 class="fw-bold">

                                                                Atur Manual

                                                            </h6>

                                                            <p class="text-muted mt-2">

                                                                Hak akses tidak dipindahkan.
                                                                Kelola kembali melalui menu
                                                                Kelola Hak Akses.

                                                            </p>

                                                            <span class="badge bg-label-warning">

                                                                MANUAL

                                                            </span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </label>

                                    </div>

                                </div>

                                <div class="text-end mt-3">

                                    <button type="submit" id="btnSimpan" class="btn btn-primary">

                                        <i class="bx bx-save me-1"></i>

                                        Simpan Mutasi

                                    </button>

                                </div>

                            </div>

                        </div>

            </form>

        </div>

    </div>

@endsection
@section('scripts')

    <script>
        $(document).ready(function() {

            /*=====================================================
            =            KONFIGURASI AWAL
            =====================================================*/

            const perusahaanAsal = {{ $maping->id_perusahaan }};

            const jenisPenerima = $('#jenis_penerima').val();


            /*=====================================================
            =            TOGGLE PENERIMA
            =====================================================*/

            function togglePenerima() {

                if (jenisPenerima == "Perorangan") {

                    $('#karyawanArea').show();

                    $('#divisiArea').hide();

                    $('#searchUser').prop('required', true);

                    $('#divisi').prop('required', false);

                } else {

                    $('#karyawanArea').hide();

                    $('#divisiArea').show();

                    $('#searchUser').prop('required', false);

                    $('#divisi').prop('required', true);

                }

            }


            /*=====================================================
            =            LOAD LOKASI
            =====================================================*/

            function loadLokasi(idPerusahaan) {

                if (idPerusahaan == "") {

                    $('#id_lokasi').html('<option value="">Pilih Lokasi</option>');

                    return;

                }

                $.ajax({

                    url: "{{ route('maping.getLokasi') }}",

                    type: "GET",

                    data: {
                        perusahaan: idPerusahaan
                    },

                    success: function(res) {

                        let html = '<option value="">Pilih Lokasi</option>';

                        $.each(res, function(i, row) {

                            html += `
                    <option value="${row.id}">
                        ${row.nama_lokasi}
                    </option>
                `;

                        });

                        $('#id_lokasi').html(html);

                        updatePreview();

                    }

                });

            }


            /*=====================================================
            =            LOAD DIVISI
            =====================================================*/

            function loadDivisi(idPerusahaan) {

                if (idPerusahaan == "") {

                    $('#divisi').html('<option value="">Pilih Divisi</option>');

                    return;

                }

                $.ajax({

                    url: "{{ route('maping.getDivisi') }}",

                    type: "GET",

                    data: {
                        perusahaan: idPerusahaan
                    },

                    beforeSend: function() {

                        $('#divisi').html('<option>Loading...</option>');

                    },

                    success: function(res) {

                        let html = '<option value="">Pilih Divisi</option>';

                        $.each(res, function(i, row) {

                            html += `
                    <option value="${row.divisi}">
                        ${row.divisi}
                    </option>
                `;

                        });

                        $('#divisi').html(html);

                        updatePreview();

                    },

                    error: function() {

                        $('#divisi').html('<option value="">Tidak ada divisi</option>');

                    }

                });

            }


            /*=====================================================
            =            JENIS MUTASI
            =====================================================*/

            $('#jenis_mutasi').change(function() {

                resetUser();

                let jenis = $(this).val();

                if (jenis == "antar_perusahaan") {

                    $('#perusahaanArea').show();

                    let perusahaan = $('#id_perusahaan').val();

                    if (perusahaan != "") {

                        loadLokasi(perusahaan);

                        loadDivisi(perusahaan);

                    }

                } else {

                    $('#perusahaanArea').hide();

                    loadLokasi(perusahaanAsal);

                    loadDivisi(perusahaanAsal);

                }

                updatePreview();

            });


            /*=====================================================
            =            PERUSAHAAN TUJUAN
            =====================================================*/

            $('#id_perusahaan').change(function() {

                resetUser();

                let perusahaan = $(this).val();

                loadLokasi(perusahaan);

                loadDivisi(perusahaan);

                updatePreview();

            });


            /*=====================================================
            =            LOKASI
            =====================================================*/

            $('#id_lokasi').change(function() {

                updatePreview();

            });


            /*=====================================================
            =            DIVISI
            =====================================================*/

            $('#divisi').change(function() {

                $('#searchUser').val('');

                $('#id_karyawan').val('');

                $('#resultUser').hide();

                updatePreview();

            });


            /*=====================================================
            =            LOAD PERTAMA
            =====================================================*/

            togglePenerima();

            loadLokasi(perusahaanAsal);

            loadDivisi(perusahaanAsal);
            /*
            |--------------------------------------------------------------------------
            | AUTOCOMPLETE USER ASSET BARU
            |--------------------------------------------------------------------------
            */

            let timerUser = null;

            $('#searchUser').on('keyup', function() {

                clearTimeout(timerUser);

                let keyword = $(this).val().trim();

                if ($('#jenis_penerima').val() != 'Perorangan') {
                    return;
                }

                if (keyword.length < 2) {

                    $('#resultUser').hide();

                    return;

                }

                let perusahaan =
                    $('#jenis_mutasi').val() == 'antar_perusahaan' ?
                    $('#id_perusahaan').val() :
                    perusahaanAsal;

                let divisi = $('#divisi').val();

                timerUser = setTimeout(function() {

                    $.ajax({

                        url: "{{ route('maping.searchUserMutasi') }}",

                        type: "GET",

                        data: {

                            keyword: keyword,

                            perusahaan: perusahaan,

                            divisi: divisi

                        },

                        success: function(res) {

                            let html = '';

                            if (res.length == 0) {

                                html = `
                    <div class="autocomplete-item text-danger">
                        Data tidak ditemukan
                    </div>`;

                            } else {

                                $.each(res, function(i, item) {

                                    html += `
                        <div class="autocomplete-item"

                            data-id="${item.id}"

                            data-nama="${item.nama_karyawan}"

                            data-divisi="${item.divisi}"

                            data-kode="${item.kode_karyawan}">

                            <strong>${item.nama_karyawan}</strong><br>

                            <small class="text-muted">

                                ${item.kode_karyawan}

                                •

                                ${item.divisi}

                            </small>

                        </div>`;

                                });

                            }

                            $('#resultUser')

                                .html(html)

                                .fadeIn(150);

                        }

                    });

                }, 300);

            });


            /*
            |--------------------------------------------------------------------------
            | PILIH USER
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '.autocomplete-item', function() {

                if ($(this).data('id') == undefined) {

                    return;

                }

                $('#searchUser').val(

                    $(this).data('nama')

                );

                $('#id_karyawan').val(

                    $(this).data('id')

                );

                $('#resultUser').fadeOut(150);

                updatePreview();

            });


            /*
            |--------------------------------------------------------------------------
            | HIDE AUTOCOMPLETE
            |--------------------------------------------------------------------------
            */

            $(document).click(function(e) {

                if (

                    !$(e.target).closest('#searchUser').length &&

                    !$(e.target).closest('#resultUser').length

                ) {

                    $('#resultUser').fadeOut(150);

                }

            });


            /*
            |--------------------------------------------------------------------------
            | RESET USER
            |--------------------------------------------------------------------------
            */

            function resetUser() {

                $('#searchUser').val('');

                $('#id_karyawan').val('');

                $('#resultUser').hide();

            }
            /*
            |--------------------------------------------------------------------------
            | UPDATE PREVIEW
            |--------------------------------------------------------------------------
            */

            function updatePreview() {

                /*=========================
                  Jenis Mutasi
                =========================*/

                let jenis = $('#jenis_mutasi option:selected').text();

                if ($('#jenis_mutasi').val() == '') {
                    jenis = 'Belum Dipilih';
                }

                $('#previewJenis').text(jenis);


                /*=========================
                  Tujuan
                =========================*/

                let tujuan = "{{ $maping->perusahaan->nama_perusahaan }}";

                if ($('#jenis_mutasi').val() == 'antar_perusahaan') {

                    tujuan = $('#id_perusahaan option:selected').text();

                    if ($('#id_perusahaan').val() == '') {

                        tujuan = '-';

                    }

                }

                $('#previewPerusahaan').text(tujuan);


                /*=========================
                  Penerima Baru
                =========================*/

                let penerima = '-';

                if ($('#jenis_penerima').val() == 'Perorangan') {

                    penerima = $('#searchUser').val();

                } else {

                    penerima = $('#divisi option:selected').text();

                }

                if (
                    penerima == '' ||
                    penerima == 'Pilih Divisi'
                ) {

                    penerima = '-';

                }

                $('#previewUser').text(penerima);

            }
            updatePreview();
            /*
            |--------------------------------------------------------------------------
            | VALIDASI FORM
            |--------------------------------------------------------------------------
            */

            $('form').on('submit', function(e) {

                e.preventDefault();

                let jenis = $('#jenis_mutasi').val();

                if (jenis == '') {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Jenis mutasi belum dipilih'
                    });

                    return;
                }

                if (jenis == 'antar_perusahaan' && $('#id_perusahaan').val() == '') {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih perusahaan tujuan terlebih dahulu'
                    });

                    return;
                }

                if ($('#id_lokasi').val() == '') {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Lokasi tujuan belum dipilih'
                    });

                    return;
                }

                if ($('#jenis_penerima').val() == 'Perorangan') {

                    if ($('#id_karyawan').val() == '') {

                        Swal.fire({
                            icon: 'warning',
                            title: 'User Asset baru belum dipilih'
                        });

                        return;
                    }

                } else {

                    if ($('#divisi').val() == '') {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Divisi belum dipilih'
                        });

                        return;
                    }

                }

                let namaUser = '-';

                if ($('#jenis_penerima').val() == 'Perorangan') {

                    namaUser = $('#searchUser').val();

                } else {

                    namaUser = $('#divisi option:selected').text();

                }

                if (
                    namaUser == '' ||
                    namaUser == 'Pilih Divisi'
                ) {

                    namaUser = '-';

                }


                let lokasi = $('#id_lokasi option:selected').text();

                let perusahaan = "{{ $maping->perusahaan->nama_perusahaan }}";

                if (jenis == 'antar_perusahaan') {

                    perusahaan = $('#id_perusahaan option:selected').text();

                    if ($('#id_perusahaan').val() == '') {

                        perusahaan = '-';

                    }

                }
                /*
                |--------------------------------------------------------------------------
                | KONFIRMASI
                |--------------------------------------------------------------------------
                */

                Swal.fire({

                    title: 'Konfirmasi Mutasi',

                    html: `

<table class="table table-sm text-start">

<tr>

<td width="180"><b>Jenis Mutasi</b></td>

<td>${$('#jenis_mutasi option:selected').text()}</td>

</tr>

<tr>

<td><b>Perusahaan Tujuan</b></td>

<td>${perusahaan}</td>

</tr>

<tr>

<td><b>Lokasi Tujuan</b></td>

<td>${lokasi}</td>

</tr>

<tr>

<td><b>Penerima Baru</b></td>

<td>${namaUser}</td>

</tr>

</table>

<div class="alert alert-warning mt-2 mb-0">

Mutasi akan diproses sesuai data di atas.

</div>

`,

                    icon: 'question',

                    showCancelButton: true,

                    confirmButtonText: 'Ya, Simpan Mutasi',

                    cancelButtonText: 'Batal',

                    reverseButtons: true

                }).then((result) => {

                    if (result.isConfirmed) {

                        $('#btnSimpan')

                            .prop('disabled', true)

                            .html(`
            <span class="spinner-border spinner-border-sm me-2"></span>

            Menyimpan...
        `);

                        if (result.isConfirmed) {

    console.log('SUBMIT FORM');

    $('#btnSimpan')
        .prop('disabled', true)
        .html(`
            <span class="spinner-border spinner-border-sm me-2"></span>
            Menyimpan...
        `);

    e.currentTarget.submit();

}

                    }
                });

            });


            /*
            |--------------------------------------------------------------------------
            | PREVIEW OTOMATIS
            |--------------------------------------------------------------------------
            */

            $('#jenis_mutasi').change(updatePreview);

            $('#id_perusahaan').change(updatePreview);

            $('#id_lokasi').change(updatePreview);

            $('#divisi').change(updatePreview);

            $('#searchUser').keyup(updatePreview);



            /*
            |--------------------------------------------------------------------------
            | ESC
            |--------------------------------------------------------------------------
            */

            $(document).keyup(function(e) {

                if (e.key === "Escape") {

                    $('#resultUser').fadeOut(150);

                }

            });
        });

        //pindah border biru
        function updateAccessCard() {

            $('.access-card').removeClass('active');

            $('input[name="opsi_hak_akses"]:checked')

                .closest('label')

                .find('.access-card')

                .addClass('active');

        }

        updateAccessCard();

        $('input[name="opsi_hak_akses"]').on('change', function() {

            updateAccessCard();

            updatePreview();

            togglePenerima();

            updateAccessCard();

        });
    </script>
@endsection
