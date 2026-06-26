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

            <form action="{{ route('maping.update', $maping->id) }}" method="POST">
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

                            Kategori Barang

                        </label>

                        <select id="id_kategori" class="form-select">

                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ optional($maping->keluar->inventaris->dataAset)->kategori_id == $kategori->id ? 'selected' : '' }}>

                                    {{ $kategori->nama_barang }}

                                </option>
                            @endforeach

                        </select>

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

                        <select id="id_keluar_select" class="form-select">

                            <option>

                                -- Pilih Kode Asset --

                            </option>

                        </select>

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
                            value="{{ $maping->keluar->karyawan->nama_karyawan ?? '' }}">

                    </div>

                </div>

                <hr class="my-4">

                {{-- LOKASI --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>

                    <select name="id_lokasi" id="id_lokasi" class="form-select">

                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ $maping->id_lokasi == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach

                    </select>
                </div>

        </div>

        <div class="row g-3 mt-1">

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Status Inventaris
                </label>

                <select name="status" class="form-select">

                    <option value="aktif" {{ $maping->status == 'aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>

                    <option value="dicabut" {{ $maping->status == 'dicabut' ? 'selected' : '' }}>
                        Dicabut
                    </option>

                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Processor
                </label>

                <input type="text" name="processor" class="form-control" value="{{ $maping->processor }}">
            </div>

        </div>

        <div class="row g-3 mt-1">

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Device ID
                </label>

                <input type="text" name="device_id" class="form-control" value="{{ $maping->device_id }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Produk ID
                </label>

                <input type="text" name="produk_id" class="form-control" value="{{ $maping->produk_id }}">
            </div>

        </div>

        <div class="row g-3 mt-1">

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    RAM
                </label>

                <input type="number" name="ram" class="form-control" value="{{ $maping->ram }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    System
                </label>

                <input type="text" name="system" class="form-control" value="{{ $maping->system }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    Version
                </label>

                <input type="text" name="version" class="form-control" value="{{ $maping->version }}">
            </div>

        </div>

        <div class="row g-3 mt-1">

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Instal On
                </label>

                <input type="date" name="instal_on" class="form-control" value="{{ $maping->instal_on }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Aplikasi
                </label>

                <input type="text" name="aplikasi" class="form-control" value="{{ $maping->aplikasi }}">
            </div>

        </div>

        <div class="row g-3 mt-1">

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Hak Akses Data PPN
                </label>

                <textarea name="data_p" rows="4" class="form-control">{{ $maping->data_p }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    Hak Akses Data Non PPN
                </label>

                <textarea name="data_n" rows="4" class="form-control">{{ $maping->data_n }}</textarea>
            </div>

        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> Update
            </button>

            <a href="{{ route('maping.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

        </form>
    </div>
    </div>

@endsection



@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const perusahaanSelect =
                document.getElementById('id_perusahaan');

            const kategoriSelect =
                document.getElementById('id_kategori');

            const asetSelect =
                document.getElementById('id_keluar_select');

            const lokasiSelect =
                document.getElementById('id_lokasi');

            const currentKategori =
                "{{ optional($maping->keluar->inventaris->dataAset)->kategori_id }}";

            const currentKeluar =
                "{{ $maping->id_keluar }}";

            const currentPerusahaan =
                "{{ $maping->id_perusahaan }}";

            function resetDetailBarang() {

                document.getElementById('id_keluar').value = '';

                document.getElementById('nama_barang').value = '';

                document.getElementById('type').value = '';

                document.getElementById('merek').value = '';

                document.getElementById('warna').value = '';

                document.getElementById('nama_karyawan').value = '';
            }

            function loadAset(kategoriId, perusahaanId) {

                asetSelect.innerHTML =
                    '<option value="">-- Pilih Kode Aset --</option>';

                let url =
                    '/maping/get-aset?id_kategori=' +
                    kategoriId +
                    '&current_keluar=' +
                    currentKeluar;

                if (perusahaanId) {
                    url += '&id_perusahaan=' +
                        perusahaanId;
                }

                fetch(url)

                    .then(res => res.json())

                    .then(data => {

                        let options =
                            '<option value="">-- Pilih Kode Aset --</option>';

                        data.forEach(item => {

                            options += `
                    <option value="${item.id}"
                        ${item.id == currentKeluar ? 'selected' : ''}>
                        ${item.inventaris?.kode_aset ?? '-'}
                    </option>
                `;

                        });

                        asetSelect.innerHTML =
                            options;

                    })

                    .catch(error => {

                        console.log(
                            'ERROR ASET',
                            error
                        );

                    });

            }

            function loadDetailAset(idKeluar) {

                if (!idKeluar) {
                    resetDetailBarang();
                    return;
                }

                fetch(
                        '/maping/get-detail-aset/' +
                        idKeluar
                    )

                    .then(res => res.json())

                    .then(data => {

                        document.getElementById(
                                'id_keluar'
                            ).value =
                            data.id_keluar ?? '';

                        document.getElementById(
                                'nama_barang'
                            ).value =
                            data.nama_barang ?? '';

                        document.getElementById(
                                'type'
                            ).value =
                            data.type ?? '';

                        document.getElementById(
                                'merek'
                            ).value =
                            data.merek ?? '';

                        document.getElementById(
                                'warna'
                            ).value =
                            data.warna ?? '';

                        document.getElementById(
                                'nama_karyawan'
                            ).value =
                            data.nama_karyawan ?? '';

                    });

            }

            kategoriSelect.addEventListener(
                'change',
                function() {

                    const kategoriId =
                        this.value;

                    const perusahaanId =
                        perusahaanSelect ?
                        perusahaanSelect.value :
                        currentPerusahaan;

                    loadAset(
                        kategoriId,
                        perusahaanId
                    );
                }
            );

            asetSelect.addEventListener(
                'change',
                function() {

                    loadDetailAset(
                        this.value
                    );

                }
            );

            loadAset(
                currentKategori,
                currentPerusahaan
            );

            loadDetailAset(
                currentKeluar
            );

        });
    </script>
@endsection
