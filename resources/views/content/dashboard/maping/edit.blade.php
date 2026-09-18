@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Mapping Aset')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-edit fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Edit Mapping Aset</h4>
                            <small class="text-muted">Perbarui data alokasi penerima, lokasi, dan spesifikasi perangkat</small>
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
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('maping.update', $maping->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="id_perusahaan" name="id_perusahaan" value="{{ $maping->id_perusahaan }}">

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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Perusahaan</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $maping->perusahaan->nama_perusahaan ?? '-' }}" readonly>
                        </div>

                        {{-- UNIT INVENTARIS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit Inventaris Terpasang <span class="text-danger">*</span></label>
                            <select name="inventaris_id" id="inventaris_id" class="form-select">
                                @if ($currentInventaris)
                                    <option value="{{ $currentInventaris->id }}" selected>
                                        [Saat Ini] [{{ $currentInventaris->no_inventaris }}] {{ $currentInventaris->kode_aset }} - {{ $currentInventaris->dataAset->kategori->nama_barang ?? '' }} {{ $currentInventaris->dataAset->merek ?? '' }} {{ $currentInventaris->dataAset->type ?? '' }}
                                    </option>
                                @endif
                                @if (isset($availableInventaris) && $availableInventaris->isNotEmpty())
                                    <optgroup label="Tukar dengan Unit Tersedia Lainnya di Gudang:">
                                        @foreach ($availableInventaris as $inv)
                                            @if (!$currentInventaris || $inv->id != $currentInventaris->id)
                                                <option value="{{ $inv->id }}" {{ old('inventaris_id') == $inv->id ? 'selected' : '' }}>
                                                    [{{ $inv->no_inventaris }}] {{ $inv->kode_aset }} - {{ $inv->dataAset->kategori->nama_barang ?? '' }} {{ $inv->dataAset->merek ?? '' }} {{ $inv->dataAset->type ?? '' }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            <small class="text-muted">Pilih unit lain jika ingin menukar fisik inventaris yang dialokasikan.</small>
                        </div>

                        {{-- JENIS PENERIMA --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Penerima <span class="text-danger">*</span></label>
                            @php
                                $selectedJenis = old('jenis_penerima', $maping->jenis_penerima);
                            @endphp
                            <select name="jenis_penerima" id="jenis_penerima" class="form-select" required>
                                <option value="Perorangan" {{ in_array($selectedJenis, ['Perorangan']) ? 'selected' : '' }}>
                                    Perorangan (Karyawan)
                                </option>
                                <option value="Perdivisi" {{ in_array($selectedJenis, ['Per Divisi', 'Perdivisi']) ? 'selected' : '' }}>
                                    Per Divisi
                                </option>
                            </select>
                        </div>

                        {{-- KARYAWAN AUTOCOMPLETE --}}
                        <div class="col-md-4 position-relative" id="box_karyawan">
                            <label class="form-label fw-semibold">Pilih Karyawan (Autocomplete) <span class="text-danger">*</span></label>
                            <input type="hidden" name="karyawan_id" id="karyawan_id"
                                value="{{ old('karyawan_id', $maping->karyawan_id) }}">
                            <input type="text" id="search_karyawan" class="form-control"
                                placeholder="Ketik Nama Karyawan..." autocomplete="off"
                                value="{{ old('search_karyawan', $maping->karyawan?->nama_karyawan ?? '') }}">
                            <div id="result_karyawan" class="list-group position-absolute w-100 shadow-lg z-3 mt-1"
                                style="display:none; max-height: 220px; overflow-y: auto;"></div>
                            <small class="text-muted">Ketik nama untuk mencari dan memilih karyawan.</small>
                        </div>

                        {{-- DIVISI --}}
                        <div class="col-md-4 d-none" id="box_divisi">
                            <label class="form-label fw-semibold">Nama Divisi <span class="text-danger">*</span></label>
                            <input type="text" name="divisi" id="divisi" class="form-control"
                                placeholder="Misal: IT, Finance, HR..."
                                value="{{ old('divisi', $maping->divisi ?? $maping->keluar?->divisi_klr) }}">
                        </div>

                        {{-- LOKASI --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lokasi Penempatan <span class="text-danger">*</span></label>
                            <select name="id_lokasi" id="id_lokasi" class="form-select" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasis as $lok)
                                    <option value="{{ $lok->id }}"
                                        {{ old('id_lokasi', $maping->id_lokasi) == $lok->id ? 'selected' : '' }}>
                                        {{ $lok->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TANGGAL DIGUNAKAN --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Digunakan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_digunakan" class="form-control"
                                value="{{ old('tanggal_digunakan', $maping->tanggal_digunakan) }}" required>
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
                            <input type="text" name="processor" class="form-control text-uppercase"
                                placeholder="Misal: INTEL CORE I7-12700"
                                value="{{ old('processor', $maping->processor) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">RAM</label>
                            <input type="text" name="ram" class="form-control text-uppercase"
                                placeholder="Misal: 16 GB" value="{{ old('ram', $maping->ram) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Device ID / Serial Number</label>
                            <input type="text" name="device_id" class="form-control text-uppercase"
                                placeholder="Masukkan Serial / Device ID"
                                value="{{ old('device_id', $maping->device_id) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product ID</label>
                            <input type="text" name="produk_id" class="form-control text-uppercase"
                                placeholder="Masukkan Product ID"
                                value="{{ old('produk_id', $maping->produk_id) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sistem Operasi (OS)</label>
                            <input type="text" name="system" class="form-control text-uppercase"
                                placeholder="Misal: WINDOWS 11 PRO"
                                value="{{ old('system', $maping->system) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Versi OS</label>
                            <input type="text" name="version" class="form-control text-uppercase"
                                placeholder="Misal: 22H2" value="{{ old('version', $maping->version) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Instalasi</label>
                            <input type="date" name="instal_on" class="form-control"
                                value="{{ old('instal_on', $maping->instal_on) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Unggah / Ganti Foto Perangkat <small class="text-danger">(Maksimal 1 MB - JPG, JPEG, PNG, WEBP)</small></label>
                            <input type="file" name="gambar" id="input_gambar_edit" class="form-control"
                                accept="image/jpeg,image/png,image/jpg,image/webp" onchange="validateFotoSizeEdit(this)">
                            @if ($maping->keluar?->gambar)
                                <div class="mt-2" id="existing_foto_box">
                                    <small class="text-muted d-block mb-1">Foto Saat Ini:</small>
                                    <img src="{{ asset('storage/' . $maping->keluar->gambar) }}" alt="Foto Perangkat"
                                        class="img-thumbnail rounded" style="max-height: 180px; object-fit: contain;">
                                </div>
                            @endif
                            <div id="foto_edit_preview_box" class="mt-2 d-none">
                                <small class="text-muted d-block mb-1">Preview Foto Baru:</small>
                                <img id="foto_edit_preview" src="#" alt="Preview Foto Baru"
                                    class="img-thumbnail rounded" style="max-height: 180px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="2"
                                placeholder="Catatan kondisi fisik / kelengkapan unit...">{{ old('catatan', $maping->catatan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary px-4 py-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="bx bx-save me-1"></i> Simpan Perubahan Mapping
                </button>
            </div>

        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // 1. TOGGLE JENIS PENERIMA
            function togglePenerima() {
                let jenis = $('#jenis_penerima').val();
                if (jenis === 'Perorangan') {
                    $('#box_karyawan').removeClass('d-none').show();
                    $('#box_divisi').addClass('d-none').hide();
                } else {
                    $('#box_karyawan').addClass('d-none').hide();
                    $('#box_divisi').removeClass('d-none').show();
                }
            }

            $('#jenis_penerima').on('change', togglePenerima);
            togglePenerima();

            // 2. AUTOCOMPLETE KARYAWAN
            $('#search_karyawan').on('keyup input', function() {
                let keyword = $(this).val();
                let perusahaanId = $('#id_perusahaan').val();

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

            $(document).on('click', '.select-karyawan-item', function() {
                let id = $(this).data('id');
                let nama = $(this).data('nama');
                $('#karyawan_id').val(id);
                $('#search_karyawan').val(nama);
                $('#result_karyawan').hide().html('');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#box_karyawan').length) {
                    $('#result_karyawan').hide();
                }
            });

        });

        // Validasi Ukuran Foto Edit Maksimal 1 MB (Client Side)
        function validateFotoSizeEdit(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSizeInBytes = 1048576; // 1 MB
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
