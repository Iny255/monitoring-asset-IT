@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Data Maping')

@section('content')

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-primary">Edit Data Maping</h5>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('maping.update', $maping->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="id_maping" value="{{ $maping->id }}">
                <input type="hidden" name="id_keluar" id="id_keluar" value="{{ $maping->id_keluar }}">

                <div class="row g-4">

                    {{-- ========================= --}}
                    {{-- PERUSAHAAN --}}
                    {{-- ========================= --}}
                    @if (auth()->user()->role == 'super_admin')

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Perusahaan

                            </label>

                            <select name="id_perusahaan" id="id_perusahaan" class="form-select">

                                @foreach ($perusahaans as $perusahaan)
                                    <option value="{{ $perusahaan->id }}"
                                        {{ $maping->id_perusahaan == $perusahaan->id ? 'selected' : '' }}>

                                        {{ $perusahaan->nama_perusahaan }}

                                    </option>
                                @endforeach

                            </select>

                        </div>
                    @else
                        <input type="hidden" id="id_perusahaan" name="id_perusahaan"
                            value="{{ auth()->user()->id_perusahaan }}">

                    @endif


                    {{-- ========================= --}}
                    {{-- KATEGORI --}}
                    {{-- ========================= --}}

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                           

                        </label>

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Kategori Barang
                            </label>

                            <input type="text" class="form-control"
                                value="{{ optional($maping->keluar->inventaris->dataAset->kategori)->nama_barang }}"
                                readonly>

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                <div class="row g-4">

                    {{-- ========================= --}}
                    {{-- KODE ASET --}}
                    {{-- ========================= --}}

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Kode Asset
                        </label>

                        <input type="text" id="kode_aset" class="form-control"
                            value="{{ optional($maping->keluar->inventaris)->kode_aset }}" readonly>

                    </div>


                    {{-- ========================= --}}
                    {{-- NAMA BARANG --}}
                    {{-- ========================= --}}

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            Nama Barang

                        </label>

                        <input type="text" id="nama_barang" class="form-control" readonly
                            value="{{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '' }}">

                    </div>


                    {{-- ========================= --}}
                    {{-- TYPE --}}
                    {{-- ========================= --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Type

                        </label>

                        <input type="text" id="type" class="form-control" readonly
                            value="{{ $maping->keluar->inventaris->dataAset->type ?? '' }}">

                    </div>


                    {{-- ========================= --}}
                    {{-- MEREK --}}
                    {{-- ========================= --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Merek

                        </label>

                        <input type="text" id="merek" class="form-control" readonly
                            value="{{ $maping->keluar->inventaris->dataAset->merek ?? '' }}">

                    </div>


                    {{-- ========================= --}}
                    {{-- WARNA --}}
                    {{-- ========================= --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Warna

                        </label>

                        <input type="text" id="warna" class="form-control" readonly
                            value="{{ $maping->keluar->inventaris->dataAset->warna ?? '' }}">

                    </div>


                    {{-- ========================= --}}
                    {{-- USER ASET --}}
                    {{-- ========================= --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            User Asset

                        </label>

                        <input type="text" id="nama_karyawan" class="form-control" readonly
                            value="{{ $maping->keluar->jenis_penerima == 'Perorangan'
                                ? optional($maping->karyawan)->nama_karyawan
                                : $maping->keluar->divisi_klr }}"
                            </div>

                    </div>

                    <hr class="my-4">

                    {{-- ====================================================== --}}
                    {{-- INFORMASI MAPPING --}}
                    {{-- ====================================================== --}}

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white">

                            <h5 class="mb-0 fw-bold">

                                <i class="bx bx-map text-primary me-2"></i>

                                Informasi Mapping

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row g-4">

                                {{-- LOKASI --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">

                                        Lokasi Asset <span class="text-danger">*</span>

                                    </label>

                                    <select name="id_lokasi" class="form-select" required>

                                        @foreach ($lokasis as $lokasi)
                                            <option value="{{ $lokasi->id }}"
                                                {{ $maping->id_lokasi == $lokasi->id ? 'selected' : '' }}>

                                                {{ $lokasi->nama_lokasi }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- TANGGAL DIGUNAKAN --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">

                                        Tanggal Digunakan <span class="text-danger">*</span>

                                    </label>

                                    <input type="date" name="tanggal_digunakan" class="form-control"
                                        value="{{ old('tanggal_digunakan', $maping->tanggal_digunakan) }}" required>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ====================================================== --}}
                    {{-- SPESIFIKASI --}}
                    {{-- ====================================================== --}}

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white">

                            <h5 class="mb-0 fw-bold">

                                <i class="bx bx-chip text-success me-2"></i>

                                Spesifikasi Perangkat

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row g-4">

                                {{-- PROCESSOR --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Processor

                                    </label>

                                    <input type="text" name="processor" class="form-control"
                                        value="{{ old('processor', $maping->processor) }}">

                                </div>

                                {{-- RAM --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        RAM

                                    </label>

                                    <input type="text" name="ram" class="form-control"
                                        value="{{ old('ram', $maping->ram) }}">

                                </div>

                                {{-- DEVICE ID --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Device ID

                                    </label>

                                    <input type="text" name="device_id" class="form-control"
                                        value="{{ old('device_id', $maping->device_id) }}">

                                </div>

                                {{-- PRODUCT ID --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Product ID

                                    </label>

                                    <input type="text" name="produk_id" class="form-control"
                                        value="{{ old('produk_id', $maping->produk_id) }}">

                                </div>

                                {{-- SYSTEM --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Operating System

                                    </label>

                                    <input type="text" name="system" class="form-control"
                                        value="{{ old('system', $maping->system) }}">

                                </div>

                                {{-- VERSION --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Version

                                    </label>

                                    <input type="text" name="version" class="form-control"
                                        value="{{ old('version', $maping->version) }}">

                                </div>

                                {{-- INSTALL ON --}}
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Install On

                                    </label>

                                    <input type="date" name="instal_on" class="form-control"
                                        value="{{ old('instal_on', $maping->instal_on) }}">

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ====================================================== --}}
                    {{-- FOTO PERANGKAT --}}
                    {{-- ====================================================== --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bx bx-image text-primary me-2"></i>
                                Foto Perangkat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Unggah / Ganti Foto Perangkat <small class="text-danger">(Maksimal 1 MB - JPG, JPEG, PNG, WEBP)</small></label>
                                <input type="file" name="gambar" id="input_gambar_edit" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="validateFotoSizeEdit(this)">
                            </div>
                            @if($maping->keluar?->gambar)
                                <div class="mt-2" id="existing_foto_box">
                                    <small class="text-muted d-block mb-1">Foto Saat Ini:</small>
                                    <img src="{{ asset('storage/' . $maping->keluar->gambar) }}" alt="Foto Perangkat" class="img-thumbnail rounded" style="max-height: 180px; object-fit: contain;">
                                </div>
                            @endif
                            <div id="foto_edit_preview_box" class="mt-2 d-none">
                                <small class="text-muted d-block mb-1">Preview Foto Baru:</small>
                                <img id="foto_edit_preview" src="#" alt="Preview Foto Baru" class="img-thumbnail rounded" style="max-height: 180px; object-fit: contain;">
                            </div>
                        </div>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- CATATAN --}}
                    {{-- ====================================================== --}}

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white">

                            <h5 class="mb-0 fw-bold">

                                <i class="bx bx-note text-warning me-2"></i>

                                Catatan

                            </h5>

                        </div>

                        <div class="card-body">

                            <textarea name="catatan" rows="4" class="form-control" placeholder="Tambahkan catatan jika diperlukan...">{{ old('catatan', $maping->catatan) }}</textarea>

                        </div>

                    </div>


                    <div class="d-flex justify-content-between mt-4">

                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">

                            <i class="bx bx-arrow-back"></i>

                            Kembali

                        </a>

                        <button type="submit" class="btn btn-primary">

                            <i class="bx bx-save"></i>

                            Update Mapping

                        </button>

                    </div>

            </form>
        </div>
    </div>

@endsection



@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const currentKeluar = "{{ $maping->id_keluar }}";

            /*
            |--------------------------------------------------------------------------
            | Reset Detail Barang
            |--------------------------------------------------------------------------
            */

            function resetDetailBarang() {

                document.getElementById('id_keluar').value = '';

                document.getElementById('kode_aset').value = '';

                document.getElementById('nama_barang').value = '';

                document.getElementById('type').value = '';

                document.getElementById('merek').value = '';

                document.getElementById('warna').value = '';

                document.getElementById('nama_karyawan').value = '';

            }

            /*
            |--------------------------------------------------------------------------
            | Load Detail Asset
            |--------------------------------------------------------------------------
            */

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

                        document.getElementById('kode_aset').value =
                            data.kode_aset ?? '';

                        document.getElementById('nama_barang').value =
                            data.nama_barang ?? '';

                        document.getElementById('type').value =
                            data.type ?? '';

                        document.getElementById('merek').value =
                            data.merek ?? '';

                        document.getElementById('warna').value =
                            data.warna ?? '';

                        document.getElementById('nama_karyawan').value =
                            data.user_aset ?? '';

                    })

                    .catch(error => {

                        console.error('Gagal mengambil detail asset :', error);

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | Pertama kali halaman dibuka
            |--------------------------------------------------------------------------
            */

            loadDetailAset(currentKeluar);

        });

        // Validasi Ukuran Foto Edit Maksimal 1 MB (Client Side)
        function validateFotoSizeEdit(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSizeInBytes = 1048576; // 1 MB = 1024 * 1024 bytes
                if (file.size > maxSizeInBytes) {
                    let sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran File Terlalu Besar',
                            text: 'Ukuran foto maksimal 1 MB. File yang Anda pilih berukuran ' + sizeInMB + ' MB.'
                        });
                    } else {
                        alert('Ukuran foto maksimal 1 MB. File yang Anda pilih berukuran ' + sizeInMB + ' MB.');
                    }
                    input.value = '';
                    document.getElementById('foto_edit_preview_box').classList.add('d-none');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('foto_edit_preview').src = e.target.result;
                    document.getElementById('foto_edit_preview_box').classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('foto_edit_preview_box').classList.add('d-none');
            }
        }
    </script>
@endsection
