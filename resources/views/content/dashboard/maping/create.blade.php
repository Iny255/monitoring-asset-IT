@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Mapping Aset')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-sitemap fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Tambah Mapping Aset</h3>
                            <small class="text-muted">Form terpadu penyerahan aset dari stok gudang sekaligus pendaftaran spesifikasi perangkat</small>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT MESSAGES --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('maping.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- CARD 1: ALOKASI & PENERIMA --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="bx bx-package me-2"></i> 1. Informasi Alokasi & Penerima Aset
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        {{-- PERUSAHAAN --}}
                        @if (auth()->user()->role == 'super_admin')
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="id_perusahaan" id="id_perusahaan" class="form-select" required>
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}" {{ old('id_perusahaan') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" id="id_perusahaan" name="id_perusahaan" value="{{ auth()->user()->id_perusahaan }}">
                        @endif

                        {{-- KATEGORI --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Filter Kategori Barang</label>
                            <select id="id_kategori" class="form-select">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NOTIFIKASI SISA STOK TERSEDIA --}}
                        <div class="col-12 d-none" id="box_stok_info">
                            <div id="stok_info_alert" class="alert d-flex align-items-center py-2 px-3 mb-0 shadow-sm" role="alert">
                                <i id="stok_info_icon" class="bx me-2 fs-4"></i>
                                <div>
                                    <span id="stok_info_text" class="fw-semibold"></span>
                                </div>
                            </div>
                        </div>

                        {{-- PILIH UNIT INVENTARIS --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Pilih Unit Inventaris (Stok Tersedia) <span class="text-danger">*</span></label>
                            <select name="inventaris_id" id="inventaris_id" class="form-select" required>
                                <option value="">-- Pilih Unit Inventaris --</option>
                                @foreach ($inventarisAvailable as $inv)
                                    <option value="{{ $inv->id }}" {{ old('inventaris_id') == $inv->id ? 'selected' : '' }}>
                                        [{{ $inv->no_inventaris }}] {{ $inv->kode_aset }} - {{ $inv->dataAset->kategori->nama_barang ?? '' }} {{ $inv->dataAset->merek ?? '' }} {{ $inv->dataAset->type ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- JENIS PENERIMA --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Penerima <span class="text-danger">*</span></label>
                            <select name="jenis_penerima" id="jenis_penerima" class="form-select" required>
                                <option value="Perorangan" {{ old('jenis_penerima', 'Perorangan') == 'Perorangan' ? 'selected' : '' }}>Perorangan (Karyawan)</option>
                                <option value="Per Divisi" {{ old('jenis_penerima') == 'Per Divisi' ? 'selected' : '' }}>Per Divisi</option>
                            </select>
                        </div>

                        {{-- KARYAWAN AUTOCOMPLETE --}}
                        <div class="col-md-4 position-relative" id="box_karyawan">
                            <label class="form-label fw-semibold">Pilih Karyawan (Autocomplete)</label>
                            <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ old('karyawan_id') }}">
                            <input type="text" id="search_karyawan" class="form-control" placeholder="Ketik Nama Karyawan..." autocomplete="off">
                            <div id="result_karyawan" class="list-group position-absolute w-100 shadow-lg z-3 mt-1" style="display:none; max-height: 220px; overflow-y: auto;"></div>
                        </div>

                        {{-- DIVISI --}}
                        <div class="col-md-4 d-none" id="box_divisi">
                            <label class="form-label fw-semibold">Nama Divisi</label>
                            <input type="text" name="divisi" id="divisi" class="form-control" placeholder="Misal: IT, Finance, HR..." value="{{ old('divisi') }}">
                        </div>

                        {{-- LOKASI --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lokasi Penempatan <span class="text-danger">*</span></label>
                            <select name="id_lokasi" id="id_lokasi" class="form-select" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasis as $lok)
                                    <option value="{{ $lok->id }}" {{ old('id_lokasi') == $lok->id ? 'selected' : '' }}>
                                        {{ $lok->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TANGGAL DIGUNAKAN --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Digunakan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_digunakan" class="form-control" value="{{ old('tanggal_digunakan', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: SPESIFIKASI DEVICE --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="bx bx-laptop me-2"></i> 2. Spesifikasi & Identitas Device
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Processor</label>
                            <input type="text" name="processor" class="form-control text-uppercase" placeholder="Misal: INTEL CORE I7-12700" value="{{ old('processor') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">RAM</label>
                            <input type="text" name="ram" class="form-control text-uppercase" placeholder="Misal: 16 GB" value="{{ old('ram') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Device ID / Serial Number</label>
                            <input type="text" name="device_id" class="form-control text-uppercase" placeholder="Masukkan Serial / Device ID" value="{{ old('device_id') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product ID</label>
                            <input type="text" name="produk_id" class="form-control text-uppercase" placeholder="Masukkan Product ID" value="{{ old('produk_id') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sistem Operasi (OS)</label>
                            <input type="text" name="system" class="form-control text-uppercase" placeholder="Misal: WINDOWS 11 PRO" value="{{ old('system') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Versi OS</label>
                            <input type="text" name="version" class="form-control text-uppercase" placeholder="Misal: 22H2" value="{{ old('version') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Instalasi</label>
                            <input type="date" name="instal_on" class="form-control" value="{{ old('instal_on') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Unggah Foto Perangkat / Aset <small class="text-danger">(Maksimal 1 MB - JPG, JPEG, PNG, WEBP)</small></label>
                            <input type="file" name="gambar" id="input_gambar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="validateFotoSize(this)">
                            <div id="foto_preview_box" class="mt-2 d-none">
                                <img id="foto_preview" src="#" alt="Preview Foto" class="img-thumbnail rounded" style="max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan kondisi fisik / kelengkapan unit...">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('maping.index') }}" class="btn btn-label-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="bx bx-save me-1"></i> Simpan Mapping Aset
                </button>
            </div>
        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // 1. TOGGLE JENIS PENERIMA
            $('#jenis_penerima').change(function() {
                let jenis = $(this).val();
                if (jenis === 'Perorangan') {
                    $('#box_karyawan').removeClass('d-none').show();
                    $('#box_divisi').addClass('d-none').hide();
                } else {
                    $('#box_karyawan').addClass('d-none').hide();
                    $('#box_divisi').removeClass('d-none').show();
                }
            }).trigger('change');

            function getPerusahaanId() {
                @if (auth()->user()->role == 'super_admin')
                    return $('#id_perusahaan').val();
                @else
                    return {{ auth()->user()->id_perusahaan }};
                @endif
            }

            // 2. FILTER MULTI PERUSAHAAN (FOR SUPER ADMIN)
            @if (auth()->user()->role == 'super_admin')
                $('#id_perusahaan').change(function() {
                    let perusahaanId = $(this).val();

                    // Reset semua field turunan
                    $('#id_kategori').html('<option value="">-- Semua Kategori --</option>');
                    $('#inventaris_id').html('<option value="">-- Pilih Unit Inventaris --</option>');
                    $('#id_lokasi').html('<option value="">-- Pilih Lokasi --</option>');
                    $('#box_stok_info').addClass('d-none').hide();
                    $('#karyawan_id').val('');
                    $('#search_karyawan').val('');

                    if (!perusahaanId) return;

                    // Load Kategori
                    $.get('/dashboard/maping/get-kategori', { id_perusahaan: perusahaanId }, function(data) {
                        let html = '<option value="">-- Semua Kategori --</option>';
                        data.forEach(function(item) {
                            html += `<option value="${item.id}">${item.nama_barang}</option>`;
                        });
                        $('#id_kategori').html(html);
                    });

                    // Load Lokasi
                    $.get('/dashboard/maping/get-lokasi-by-perusahaan/' + perusahaanId, function(data) {
                        let html = '<option value="">-- Pilih Lokasi --</option>';
                        data.forEach(function(item) {
                            html += `<option value="${item.id}">${item.nama_lokasi}</option>`;
                        });
                        $('#id_lokasi').html(html);
                    });
                });
            @endif

            // 3. FILTER STOK INVENTARIS SESUAI KATEGORI BARANG
            $('#id_kategori').change(function() {
                let kategoriId = $(this).val();
                let perusahaanId = getPerusahaanId();

                if (!kategoriId) {
                    $('#box_stok_info').addClass('d-none').hide();
                    $('#inventaris_id').html('<option value="">-- Pilih Unit Inventaris --</option>');
                    return;
                }

                loadAvailableInventaris(perusahaanId, kategoriId);
            });

            function loadAvailableInventaris(perusahaanId, kategoriId) {
                $('#inventaris_id').html('<option value="">-- Loading Stok... --</option>');

                $.get('/dashboard/maping/get-available-inventaris', {
                    id_perusahaan: perusahaanId,
                    id_kategori: kategoriId
                }, function(data) {
                    let html = '<option value="">-- Pilih Unit Inventaris --</option>';
                    let count = data.length;

                    if (count === 0) {
                        html = '<option value="">-- Tidak ada stok tersedia --</option>';
                        $('#stok_info_alert').removeClass('alert-success alert-info alert-primary').addClass('alert-danger');
                        $('#stok_info_icon').attr('class', 'bx bx-x-circle me-2 fs-4 text-danger');
                        $('#stok_info_text').html('⚠️ Stok Habis! Tidak ada unit yang berstatus TERSEDIA di gudang untuk kategori ini.');
                    } else {
                        data.forEach(function(item) {
                            html += `<option value="${item.id}">[${item.no_inventaris}] ${item.kode_aset} - ${item.nama_barang} ${item.merek} ${item.type}</option>`;
                        });
                        $('#stok_info_alert').removeClass('alert-danger alert-info alert-primary').addClass('alert-success');
                        $('#stok_info_icon').attr('class', 'bx bx-check-circle me-2 fs-4 text-success');
                        $('#stok_info_text').html(`✅ <strong>Stok Tersedia: ${count} Unit</strong> di gudang (Siap dialokasikan).`);
                    }

                    $('#box_stok_info').removeClass('d-none').show();
                    $('#inventaris_id').html(html);
                });
            }

            // 4. AUTOCOMPLETE KARYAWAN
            $('#search_karyawan').on('keyup input', function() {
                let keyword = $(this).val();
                let perusahaanId = getPerusahaanId();

                if (keyword.length < 1) {
                    $('#result_karyawan').hide().html('');
                    $('#karyawan_id').val('');
                    return;
                }

                $.get('/dashboard/maping/search-karyawan', {
                    q: keyword,
                    id_perusahaan: perusahaanId
                }, function(data) {
                    let html = '';
                    if (data.length === 0) {
                        html = '<div class="list-group-item text-muted small py-2">Karyawan tidak ditemukan</div>';
                    } else {
                        data.forEach(function(item) {
                            html += `
                                <a href="javascript:void(0)" class="list-group-item list-group-item-action py-2 select-karyawan-item" 
                                   data-id="${item.id}" data-nama="${item.nama_karyawan}">
                                    <div class="fw-bold text-dark">${item.nama_karyawan}</div>
                                    <small class="text-muted">${item.kode_karyawan ?? '-'} • Divisi: ${item.divisi ?? '-'}</small>
                                </a>
                            `;
                        });
                    }
                    $('#result_karyawan').html(html).show();
                });
            });

            // Handle Klik Item Autocomplete Karyawan
            $(document).on('click', '.select-karyawan-item', function() {
                let id = $(this).data('id');
                let nama = $(this).data('nama');

                $('#karyawan_id').val(id);
                $('#search_karyawan').val(nama);
                $('#result_karyawan').hide().html('');
            });

            // Sembunyikan dropdown autocomplete ketika diklik luar
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#box_karyawan').length) {
                    $('#result_karyawan').hide();
                }
            });

        });

        // Validasi Ukuran Foto Maksimal 1 MB (Client Side)
        function validateFotoSize(input) {
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
                    document.getElementById('foto_preview_box').classList.add('d-none');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('foto_preview').src = e.target.result;
                    document.getElementById('foto_preview_box').classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('foto_preview_box').classList.add('d-none');
            }
        }
    </script>
@endsection
