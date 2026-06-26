@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Data Maping')

@section('content')

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0 fw-bold text-primary">
                Tambah Data Maping
            </h5>
        </div>

        <div class="card-body">

            {{-- ERROR --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            {{-- VALIDATION --}}
            @if ($errors->any())
                <div class="alert alert-danger">

                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif

            <form action="{{ route('maping.store') }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header ">

                        <h5 class="mb-0">

                            <i class="bx bx-package me-2"></i>

                            Informasi Inventaris

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            {{-- PERUSAHAAN --}}
                            @if (auth()->user()->role == 'super_admin')

                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">

                                        Perusahaan

                                    </label>

                                    <select name="id_perusahaan" id="id_perusahaan" class="form-select" required>

                                        <option value="">
                                            -- Pilih Perusahaan --
                                        </option>

                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}">

                                                {{ $p->nama_perusahaan }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                            @else
                                <input type="hidden" id="id_perusahaan" name="id_perusahaan"
                                    value="{{ auth()->user()->id_perusahaan }}">

                            @endif

                            {{-- KATEGORI --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Kategori Barang

                                </label>

                                <select id="id_kategori" class="form-select">

                                    <option value="">
                                        -- Pilih Kategori --
                                    </option>

                                </select>

                            </div>

                            {{-- KODE ASET --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Kode Inventaris

                                </label>

                                <select id="id_keluar_select" class="form-select">

                                    <option value="">
                                        -- Pilih Kode Inventaris --
                                    </option>

                                </select>

                                <input type="hidden" name="id_keluar" id="id_keluar">

                            </div>

                            {{-- USER ASET --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    User Aset

                                </label>

                                <input type="text" id="nama_karyawan" class="form-control" readonly>

                            </div>

                            {{-- NAMA BARANG --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Nama Barang

                                </label>

                                <input type="text" id="nama_barang" class="form-control" readonly>

                            </div>

                            {{-- LOKASI --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Lokasi

                                </label>

                                <select name="id_lokasi" id="id_lokasi" class="form-select" required>

                                    <option value="">
                                        -- Pilih Lokasi --
                                    </option>

                                </select>

                            </div>

                            {{-- TYPE --}}
                            <div class="col-md-4">

                                <label class="form-label fw-semibold">

                                    Type

                                </label>

                                <input type="text" id="type" class="form-control" readonly>

                            </div>

                            {{-- MEREK --}}
                            <div class="col-md-4">

                                <label class="form-label fw-semibold">

                                    Merek

                                </label>

                                <input type="text" id="merek" class="form-control" readonly>

                            </div>

                            {{-- WARNA --}}
                            <div class="col-md-4">

                                <label class="form-label fw-semibold">

                                    Warna

                                </label>

                                <input type="text" id="warna" class="form-control" readonly>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- =============================================== --}}
                {{-- SPESIFIKASI DEVICE --}}
                {{-- =============================================== --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header ">

                        <h5 class="mb-0">

                            <i class="bx bx-desktop me-2"></i>

                            Spesifikasi Device

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row g-3">
                            {{-- =============================== --}}
                            {{-- PROCESSOR --}}
                            {{-- =============================== --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Processor

                                </label>

                                <input type="text" name="processor" class="form-control" value="{{ old('processor') }}"
                                    placeholder="Contoh : Intel Core i5-1240P">

                            </div>

                            {{-- =============================== --}}
                            {{-- RAM --}}
                            {{-- =============================== --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    RAM

                                </label>

                                <div class="input-group">

                                    <input type="text" name="ram" class="form-control" value="{{ old('ram') }}"
                                        placeholder="8">

                                    <span class="input-group-text">

                                        GB

                                    </span>

                                </div>

                            </div>

                            {{-- =============================== --}}
                            {{-- DEVICE ID --}}
                            {{-- =============================== --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Device ID

                                </label>

                                <input type="text" name="device_id" class="form-control" value="{{ old('device_id') }}"
                                    placeholder="Device ID">

                            </div>

                            {{-- =============================== --}}
                            {{-- PRODUCT ID --}}
                            {{-- =============================== --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">

                                    Product ID

                                </label>

                                <input type="text" name="produk_id" class="form-control"
                                    value="{{ old('produk_id') }}" placeholder="Product ID">

                            </div>

                            {{-- =============================== --}}
                            {{-- SYSTEM --}}
                            {{-- =============================== --}}
                            <div class="col-md-3">

                                <label class="form-label fw-semibold">

                                    Operating System

                                </label>

                                <input type="text" name="system" class="form-control" value="{{ old('system') }}"
                                    placeholder="Windows 11 Pro">

                            </div>

                            {{-- =============================== --}}
                            {{-- VERSION --}}
                            {{-- =============================== --}}
                            <div class="col-md-3">

                                <label class="form-label fw-semibold">

                                    Version

                                </label>

                                <input type="text" name="version" class="form-control" value="{{ old('version') }}"
                                    placeholder="24H2">

                            </div>

                            {{-- =============================== --}}
                            {{-- INSTALL ON --}}
                            {{-- =============================== --}}
                            <div class="col-md-3">

                                <label class="form-label fw-semibold">

                                    Install On

                                </label>

                                <input type="date" name="instal_on" class="form-control"
                                    value="{{ old('instal_on') }}">

                            </div>

                            {{-- =============================== --}}
                            {{-- TANGGAL DIGUNAKAN --}}
                            {{-- =============================== --}}
                            <div class="col-md-3">

                                <label class="form-label fw-semibold">

                                    Tanggal Digunakan

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="date" name="tanggal_digunakan"
                                    class="form-control @error('tanggal_digunakan') is-invalid @enderror"
                                    value="{{ old('tanggal_digunakan', date('Y-m-d')) }}" required>

                                @error('tanggal_digunakan')
                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ======================================================= --}}
                {{-- HAK AKSES & APLIKASI --}}
                {{-- ======================================================= --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header ">

                        <div class="d-flex justify-content-between align-items-center">

                            <h5 class="mb-0">

                                <i class="bx bx-lock-alt me-2"></i>

                                Hak Akses & Aplikasi

                            </h5>

                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalHakAkses">

                                <i class="bx bx-plus"></i>

                                Tambah Hak Akses

                            </button>

                        </div>

                    </div>

                    <div class="card-body">

                        <div id="listHakAkses" class="border rounded bg-light p-3" style="min-height:120px;">

                            <div class="text-center text-muted py-4">

                                <i class="bx bx-lock-alt display-6"></i>

                                <br>

                                Belum ada Hak Akses & Aplikasi dipilih.

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ======================================================= --}}
                {{-- CATATAN --}}
                {{-- ======================================================= --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header ">

                        <h5 class="mb-0">

                            <i class="bx bx-note me-2"></i>

                            Catatan

                        </h5>

                    </div>

                    <div class="card-body">

                        <textarea name="catatan" rows="4" class="form-control" placeholder="Masukkan catatan apabila diperlukan...">{{ old('catatan') }}</textarea>

                    </div>

                </div>

                {{-- ======================================================= --}}
                {{-- ACTION --}}
                {{-- ======================================================= --}}

                <div class="d-flex justify-content-end gap-2 mb-3">

                    <a href="{{ route('maping.index') }}" class="btn btn-secondary">

                        <i class="bx bx-arrow-back"></i>

                        Kembali

                    </a>

                    <button type="submit" class="btn btn-primary">

                        <i class="bx bx-save"></i>

                        Simpan Mapping

                    </button>

                </div>

            </form>
            <div class="modal fade" id="modalHakAkses">

                <div class="modal-dialog modal-lg">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5>

                                Pilih Hak Akses & Aplikasi

                            </h5>

                            <button class="btn-close" data-bs-dismiss="modal">

                            </button>

                        </div>

                        <div class="modal-body">

                            {{-- ===================== --}}
                            {{-- APLIKASI --}}
                            {{-- ===================== --}}

                            <h6 class="fw-bold text-primary mb-3">

                                <i class="bx bx-desktop"></i>

                                Aplikasi

                            </h6>

                            <div class="row mb-4">

                                @foreach ($aplikasis as $item)
                                    <div class="col-md-4 mb-2">

                                        <div class="form-check">

                                            <input class="form-check-input access-check" type="checkbox"
                                                value="{{ $item->id }}" data-nama="{{ $item->nama_akses }}"
                                                id="access{{ $item->id }}">

                                            <label class="form-check-label" for="access{{ $item->id }}">

                                                {{ $item->nama_akses }}

                                            </label>

                                        </div>

                                    </div>
                                @endforeach

                            </div>

                            <hr>

                            {{-- ===================== --}}
                            {{-- HAK AKSES PPN --}}
                            {{-- ===================== --}}

                            <h6 class="fw-bold text-warning mb-3">

                                <i class="bx bx-folder"></i>

                                Hak Akses PPN

                            </h6>

                            <div class="row mb-4">

                                @foreach ($hakAksesPPN as $item)
                                    <div class="col-md-4 mb-2">

                                        <div class="form-check">

                                            <input class="form-check-input access-check" type="checkbox"
                                                value="{{ $item->id }}" data-nama="{{ $item->nama_akses }}"
                                                id="access{{ $item->id }}">

                                            <label class="form-check-label" for="access{{ $item->id }}">

                                                {{ $item->nama_akses }}

                                            </label>

                                        </div>

                                    </div>
                                @endforeach

                            </div>

                            <hr>

                            {{-- ===================== --}}
                            {{-- HAK AKSES NON PPN --}}
                            {{-- ===================== --}}

                            <h6 class="fw-bold text-success mb-3">

                                <i class="bx bx-folder-open"></i>

                                Hak Akses NON PPN

                            </h6>

                            <div class="row">

                                @foreach ($hakAksesNonPPN as $item)
                                    <div class="col-md-4 mb-2">

                                        <div class="form-check">

                                            <input class="form-check-input access-check" type="checkbox"
                                                value="{{ $item->id }}" data-nama="{{ $item->nama_akses }}"
                                                id="access{{ $item->id }}">

                                            <label class="form-check-label" for="access{{ $item->id }}">

                                                {{ $item->nama_akses }}

                                            </label>

                                        </div>

                                    </div>
                                @endforeach

                            </div>

                        </div>
                        <div class="modal-footer">

                            <button class="btn btn-primary" id="btnTambahHakAkses" type="button">

                                Tambahkan

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        @endsection

        @section('scripts')

            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const perusahaanSelect =
                        document.getElementById('id_perusahaan');

                    const isSuperAdmin =
                        perusahaanSelect &&
                        perusahaanSelect.tagName === 'SELECT';
                    const kategoriSelect = document.getElementById('id_kategori');
                    const asetSelect = document.getElementById('id_keluar_select');
                    const lokasiSelect = document.getElementById('id_lokasi');

                    // ==========================
                    // RESET DETAIL
                    // ==========================

                    function resetDetailBarang() {

                        document.getElementById('id_keluar').value = '';

                        document.getElementById('nama_barang').value = '';
                        document.getElementById('type').value = '';
                        document.getElementById('merek').value = '';
                        document.getElementById('warna').value = '';
                        document.getElementById('nama_karyawan').value = '';

                    }

                    // ==========================
                    // LOAD KATEGORI
                    // ==========================

                    function loadKategori(perusahaanId = '') {

                        let url = '/maping/get-kategori';

                        if (perusahaanId !== '') {
                            url += '?id_perusahaan=' + perusahaanId;
                        }

                        fetch(url)

                            .then(response => response.json())

                            .then(data => {

                                kategoriSelect.innerHTML =
                                    '<option value="">-- Pilih Kategori --</option>';

                                data.forEach(item => {

                                    kategoriSelect.innerHTML += `
                        <option value="${item.id}">
                            ${item.nama_barang}
                        </option>
                    `;

                                });

                            })

                            .catch(error => {

                                console.log('ERROR KATEGORI', error);

                            });

                    }

                    // ==========================
                    // LOAD LOKASI
                    // ==========================

                    function loadLokasi(perusahaanId) {

                        lokasiSelect.innerHTML =
                            '<option value="">-- Pilih Lokasi --</option>';

                        if (!perusahaanId) {
                            return;
                        }

                        fetch('/maping/lokasi-by-perusahaan/' + perusahaanId)

                            .then(response => response.json())

                            .then(data => {

                                data.forEach(item => {

                                    lokasiSelect.innerHTML += `
                        <option value="${item.id}">
                            ${item.nama_lokasi}
                        </option>
                    `;

                                });

                            })

                            .catch(error => {

                                console.log('ERROR LOKASI', error);

                            });

                    }

                    // ==========================
                    // LOAD ASET
                    // ==========================

                    function loadAset(kategoriId, perusahaanId) {

                        asetSelect.innerHTML =
                            '<option value="">-- Pilih Kode Aset --</option>';

                        if (!kategoriId) {
                            return;
                        }

                        let url =
                            '/maping/get-aset?id_kategori=' + kategoriId;

                        if (perusahaanId) {
                            url += '&id_perusahaan=' + perusahaanId;
                        }

                        fetch(url)

                            .then(response => response.json())

                            .then(data => {

                                data.forEach(item => {

                                    asetSelect.innerHTML += `
                        <option value="${item.id}">
                            ${item.inventaris?.kode_aset ?? '-'}
                        </option>
                    `;

                                });

                            })

                            .catch(error => {

                                console.log('ERROR ASET', error);

                            });

                    }

                    // ==========================
                    // DETAIL ASET
                    // ==========================

                    function loadDetailAset(idKeluar) {

                        if (!idKeluar) {

                            resetDetailBarang();

                            return;
                        }

                        fetch('/maping/get-detail-aset/' + idKeluar)

                            .then(response => response.json())

                            .then(data => {

                                document.getElementById('id_keluar').value =
                                    data.id_keluar ?? '';

                                document.getElementById('nama_barang').value =
                                    data.nama_barang ?? '';

                                document.getElementById('type').value =
                                    data.type ?? '';

                                document.getElementById('merek').value =
                                    data.merek ?? '';

                                document.getElementById('warna').value =
                                    data.warna ?? '';

                                document.getElementById('nama_karyawan').value =
                                    data.nama_karyawan ?? '';

                            })

                            .catch(error => {

                                console.log('ERROR DETAIL', error);

                                resetDetailBarang();

                            });

                    }

                    // ==========================
                    // SUPER ADMIN
                    // ==========================

                    if (isSuperAdmin) {

                        perusahaanSelect.addEventListener('change', function() {

                            const perusahaanId = this.value;

                            resetDetailBarang();

                            kategoriSelect.innerHTML =
                                '<option value="">-- Pilih Kategori --</option>';

                            asetSelect.innerHTML =
                                '<option value="">-- Pilih Kode Aset --</option>';

                            loadKategori(perusahaanId);

                            loadLokasi(perusahaanId);

                        });

                    } else {

                        // PETUGAS

                        loadKategori("{{ auth()->user()->id_perusahaan }}");

                        loadLokasi("{{ auth()->user()->id_perusahaan }}");

                    }

                    // ==========================
                    // KATEGORI CHANGE
                    // ==========================

                    kategoriSelect.addEventListener('change', function() {

                        const kategoriId = this.value;

                        const perusahaanId = perusahaanSelect ?
                            perusahaanSelect.value :
                            "{{ auth()->user()->id_perusahaan }}";

                        resetDetailBarang();

                        loadAset(kategoriId, perusahaanId);

                    });

                    // ==========================
                    // ASET CHANGE
                    // ==========================

                    asetSelect.addEventListener('change', function() {

                        loadDetailAset(this.value);

                    });

                });

                //tambah hak akses
                document.addEventListener('DOMContentLoaded', function() {

                    const btnTambah = document.getElementById('btnTambahHakAkses');

                    if (!btnTambah) return;

                    btnTambah.addEventListener('click', function() {

                        let list = document.getElementById('listHakAkses');

                        let checked = document.querySelectorAll('.access-check:checked');

                        list.innerHTML = '';

                        if (checked.length === 0) {

                            list.innerHTML = `
                <div class="text-muted">
                    Belum ada Hak Akses dipilih.
                </div>
            `;

                            return;
                        }

                        checked.forEach(function(item) {

                            list.innerHTML += `
                <div class="badge bg-label-primary me-2 mb-2 p-2">

                    ${item.dataset.nama}

                    <input
                        type="hidden"
                        name="accesses[]"
                        value="${item.value}">

                </div>
            `;

                        });

                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById('modalHakAkses')
                        );

                        if (modal) {
                            modal.hide();
                        }

                    });

                });
            </script>

        @endsection
