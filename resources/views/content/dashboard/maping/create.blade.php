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
                                <label class="form-label">Lokasi</label>

                                <input type="text" id="lokasi" class="form-control"
                                    placeholder="Lokasi akan terisi otomatis" readonly>

                                <input type="hidden" name="id_lokasi" id="id_lokasi">
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

                                <input type="text" name="device_id" class="form-control"
                                    value="{{ old('device_id') }}" placeholder="Device ID">

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

                            <div id="listAplikasi" class="row mb-4">

                                <div class="col-12 text-center text-muted">
                                    Belum ada data aplikasi.
                                </div>

                            </div>

                            <hr>

                            {{-- ===================== --}}
                            {{-- HAK AKSES PPN --}}
                            {{-- ===================== --}}

                            <h6 class="fw-bold text-warning mb-3">
                                <i class="bx bx-folder"></i>
                                Hak Akses PPN
                            </h6>

                            <div id="listHakAksesPPN" class="row mb-4">

                                <div class="col-12 text-center text-muted">
                                    Belum ada data Hak Akses PPN.
                                </div>

                            </div>

                            <hr>

                            {{-- ===================== --}}
                            {{-- HAK AKSES NON PPN --}}
                            {{-- ===================== --}}

                            <h6 class="fw-bold text-success mb-3">
                                <i class="bx bx-folder-open"></i>
                                Hak Akses NON PPN
                            </h6>

                            <div id="listHakAksesNonPPN" class="row">

                                <div class="col-12 text-center text-muted">
                                    Belum ada data Hak Akses NON PPN.
                                </div>

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

                    const perusahaanSelect = document.getElementById('id_perusahaan');
                    const kategoriSelect = document.getElementById('id_kategori');
                    const asetSelect = document.getElementById('id_keluar_select');
                    const lokasiSelect = document.getElementById('id_lokasi');

                    const isSuperAdmin = perusahaanSelect &&
                        perusahaanSelect.tagName === 'SELECT';

                    //--------------------------------------------------
                    // RESET
                    //--------------------------------------------------

                    function resetDetailBarang() {

                        document.getElementById('id_keluar').value = '';
                        document.getElementById('nama_barang').value = '';
                        document.getElementById('nama_karyawan').value = '';
                        document.getElementById('type').value = '';
                        document.getElementById('merek').value = '';
                        document.getElementById('warna').value = '';

                        document.getElementById('lokasi').value = '';
                        document.getElementById('id_lokasi').value = '';
                    }

                    //--------------------------------------------------
                    // LOAD KATEGORI
                    //--------------------------------------------------

                    function loadKategori(perusahaanId = '') {

                        kategoriSelect.innerHTML =
                            '<option value="">Loading...</option>';

                        let url = '/maping/get-kategori';

                        if (perusahaanId) {
                            url += '?id_perusahaan=' + perusahaanId;
                        }

                        fetch(url)
                            .then(res => res.json())
                            .then(function(data) {

                                kategoriSelect.innerHTML =
                                    '<option value="">-- Pilih Kategori --</option>';

                                data.forEach(function(item) {

                                    kategoriSelect.innerHTML +=
                                        `<option value="${item.id}">
                    ${item.nama_barang}
                </option>`;

                                });

                            })
                            .catch(function(error) {
                                console.log(error);
                            });

                    }



                    //--------------------------------------------------
                    // LOAD ASET
                    //--------------------------------------------------

                    function loadAset(kategoriId, perusahaanId) {

                        asetSelect.innerHTML =
                            '<option value="">-- Pilih Kode Inventaris --</option>';

                        if (!kategoriId) return;

                        let url =
                            '/maping/get-aset?id_kategori=' + kategoriId;

                        if (perusahaanId) {
                            url += '&id_perusahaan=' + perusahaanId;
                        }

                        fetch(url)

                            .then(res => res.json())

                            .then(function(data) {

                                data.forEach(function(item) {

                                    asetSelect.innerHTML += `
                                    <option value="${item.id_keluar}">
                                        ${item.kode_aset}
                                    </option>
                                `;

                                });
                            });

                    }

                    //--------------------------------------------------
                    // DETAIL ASET
                    //--------------------------------------------------

                    function loadDetailAset(idKeluar) {

                        if (!idKeluar) {

                            resetDetailBarang();
                            return;

                        }

                        fetch('/maping/get-detail-aset/' + idKeluar)

                            .then(res => res.json())

                            .then(function(data) {

                                document.getElementById('id_keluar').value =
                                    data.id_keluar ?? '';

                                document.getElementById('nama_barang').value =
                                    data.nama_barang ?? '';
                                document.getElementById('nama_karyawan').value =
                                    data.user_aset ?? '';

                                document.getElementById('type').value =
                                    data.type ?? '';

                                document.getElementById('merek').value =
                                    data.merek ?? '';

                                document.getElementById('warna').value =
                                    data.warna ?? '';

                                document.getElementById('lokasi').value =
                                    data.nama_lokasi ?? '';

                                document.getElementById('id_lokasi').value =
                                    data.lokasi_id ?? '';

                            })

                            .catch(function(error) {

                                console.log(error);

                                resetDetailBarang();

                            });

                    }

                    function renderAccess(data) {

                        const aplikasi = document.getElementById('listAplikasi');
                        const ppn = document.getElementById('listHakAksesPPN');
                        const nonppn = document.getElementById('listHakAksesNonPPN');

                        aplikasi.innerHTML = '';
                        ppn.innerHTML = '';
                        nonppn.innerHTML = '';

                        function createCheckbox(item) {

                            return `
            <div class="col-md-4 mb-2">
                <div class="form-check">

                    <input
                        class="form-check-input access-check"
                        type="checkbox"
                        value="${item.id}"
                        data-nama="${item.nama_akses}"
                        id="access${item.id}">

                    <label class="form-check-label"
                        for="access${item.id}">
                        ${item.nama_akses}
                    </label>

                </div>
            </div>
        `;
                        }

                        if (data.aplikasis.length > 0) {

                            data.aplikasis.forEach(function(item) {

                                aplikasi.innerHTML += createCheckbox(item);

                            });

                        } else {

                            aplikasi.innerHTML =
                                '<div class="col-12 text-center text-muted">Belum ada data.</div>';

                        }

                        if (data.hakAksesPPN.length > 0) {

                            data.hakAksesPPN.forEach(function(item) {

                                ppn.innerHTML += createCheckbox(item);

                            });

                        } else {

                            ppn.innerHTML =
                                '<div class="col-12 text-center text-muted">Belum ada data.</div>';

                        }

                        if (data.hakAksesNonPPN.length > 0) {

                            data.hakAksesNonPPN.forEach(function(item) {

                                nonppn.innerHTML += createCheckbox(item);

                            });

                        } else {

                            nonppn.innerHTML =
                                '<div class="col-12 text-center text-muted">Belum ada data.</div>';

                        }

                    }

                    function loadAccess(idPerusahaan) {

                        if (!idPerusahaan) {

                            document.getElementById('listAplikasi').innerHTML = '';
                            document.getElementById('listHakAksesPPN').innerHTML = '';
                            document.getElementById('listHakAksesNonPPN').innerHTML = '';

                            return;
                        }

                        fetch('/maping/get-access?id_perusahaan=' + idPerusahaan)

                            .then(response => response.json())

                            .then(function(data) {

                                renderAccess(data);

                            })

                            .catch(function(error) {

                                console.log(error);

                            });

                    }


                    //--------------------------------------------------
                    // SUPER ADMIN
                    //--------------------------------------------------

                    if (isSuperAdmin) {

                        perusahaanSelect.addEventListener('change', function() {

                            resetDetailBarang();

                            asetSelect.innerHTML =
                                '<option value="">-- Pilih Kode Inventaris --</option>';

                            loadKategori(this.value);

                            loadAccess(this.value);

                        });

                    } else {

                        loadKategori("{{ auth()->user()->id_perusahaan }}");

                        loadAccess("{{ auth()->user()->id_perusahaan }}");

                    }

                    //--------------------------------------------------
                    // CHANGE KATEGORI
                    //--------------------------------------------------

                    kategoriSelect.addEventListener('change', function() {

                        resetDetailBarang();

                        let perusahaanId = isSuperAdmin ?
                            perusahaanSelect.value :
                            "{{ auth()->user()->id_perusahaan }}";

                        loadAset(this.value, perusahaanId);

                    });

                    //--------------------------------------------------
                    // CHANGE ASET
                    //--------------------------------------------------

                    asetSelect.addEventListener('change', function() {

                        loadDetailAset(this.value);

                    });

                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const btnTambah = document.getElementById('btnTambahHakAkses');

                    if (!btnTambah) return;

                    btnTambah.addEventListener('click', function() {

                        let list = document.getElementById('listHakAkses');

                        let checked = document.querySelectorAll('.access-check:checked');

                        list.innerHTML = '';

                        if (checked.length === 0) {

                            list.innerHTML = '<div class="text-muted">Belum ada Hak Akses dipilih.</div>';

                            return;

                        }

                        checked.forEach(function(item) {

                            list.innerHTML +=
                                `<div class="badge bg-label-primary me-2 mb-2 p-2">
                ${item.dataset.nama}
                <input type="hidden"
                       name="accesses[]"
                       value="${item.value}">
            </div>`;

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
