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
                <div class="row g-3">

                    {{-- ========================================= --}}
                    {{-- PERUSAHAAN --}}
                    {{-- ========================================= --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Perusahaan
                        </label>

                        @if (auth()->user()->role === 'super_admin')

                            <select name="id_perusahaan" id="id_perusahaan" class="form-select" required>

                                <option value="">
                                    -- Pilih Perusahaan --
                                </option>

                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}"
                                        {{ $maping->id_perusahaan == $p->id ? 'selected' : '' }}>

                                        {{ $p->nama_perusahaan }}

                                    </option>
                                @endforeach

                            </select>
                        @else
                            <input type="text" class="form-control"
                                value="{{ auth()->user()->perusahaan->nama_perusahaan ?? '-' }}" readonly>

                            <input type="hidden" name="id_perusahaan" id="id_perusahaan"
                                value="{{ auth()->user()->id_perusahaan }}">

                        @endif

                    </div>

                    {{-- ========================================= --}}
                    {{-- KODE BARANG --}}
                    {{-- ========================================= --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Kode Barang
                        </label>

                        <input type="text" id="kode_barang" class="form-control"
                            value="{{ $maping->keluar->kode_barang ?? '' }}" autocomplete="off" required>

                        <input type="hidden" name="id_keluar" id="id_keluar" value="{{ $maping->id_keluar }}">

                    </div>

                    {{-- DATA BARANG --}}
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" id="nama_barang" class="form-control"
                                value="{{ $maping->keluar->masuk->kategori->nama_barang ?? '' }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type</label>
                            <input type="text" id="type" class="form-control"
                                value="{{ $maping->keluar->masuk->type ?? '' }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Merek</label>
                            <input type="text" id="merek" class="form-control"
                                value="{{ $maping->keluar->masuk->merek ?? '' }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warna</label>
                            <input type="text" id="warna" class="form-control"
                                value="{{ $maping->keluar->warna ?? '' }}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nama Karyawan</label>
                            <input type="text" id="nama_karyawan" class="form-control"
                                value="{{ $maping->keluar->karyawan->nama_karyawan ?? '' }}" readonly>
                        </div>

                    </div>

                    {{-- ========================================= --}}
                    {{-- LOKASI --}}
                    {{-- ========================================= --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Lokasi
                        </label>

                        <select name="id_lokasi" id="id_lokasi" class="form-select" required>

                            <option value="">
                                -- Pilih Lokasi --
                            </option>

                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}"
                                    {{ $maping->id_lokasi == $lokasi->id ? 'selected' : '' }}>

                                    {{ $lokasi->nama_lokasi }}

                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- STATUS INVENTARIS --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status Inventaris</label>

                        <select name="status" class="form-select" required>

                            <option value="aktif" {{ $maping->status == 'aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="dicabut" {{ $maping->status == 'dicabut' ? 'selected' : '' }}>
                                Dicabut
                            </option>

                        </select>

                        <small class="text-muted">
                            Status dapat diubah jika inventaris ingin diaktifkan kembali.
                        </small>

                    </div>
                    {{-- PROCESSOR --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Processor</label>
                        <input type="text" name="processor" class="form-control" value="{{ $maping->processor }}">
                    </div>

                    {{-- DEVICE ID --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Device ID</label>
                        <input type="text" name="device_id" class="form-control" value="{{ $maping->device_id }}">
                    </div>

                    {{-- PRODUK ID --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Produk ID</label>
                        <input type="text" name="produk_id" class="form-control" value="{{ $maping->produk_id }}">
                    </div>

                    {{-- RAM --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">RAM</label>
                        <input type="number" name="ram" class="form-control" value="{{ $maping->ram }}">
                    </div>

                    {{-- SYSTEM --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">System</label>
                        <input type="text" name="system" class="form-control" value="{{ $maping->system }}">
                    </div>

                    {{-- VERSION --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Version</label>
                        <input type="text" name="version" class="form-control" value="{{ $maping->version }}">
                    </div>

                    {{-- INSTAL ON --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Instal On</label>
                        <input type="date" name="instal_on" class="form-control" value="{{ $maping->instal_on }}">
                    </div>

                    {{-- APLIKASI --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Aplikasi</label>
                        <input type="text" name="aplikasi" class="form-control" value="{{ $maping->aplikasi }}">
                    </div>

                    {{-- DATA P --}}
                    <div class="mb-3">
                        <label class="form-label">Hak Akses Data PPN</label>
                        <textarea name="data_p" class="form-control" rows="3">{{ $maping->data_p }}</textarea>
                    </div>

                    {{-- DATA N --}}
                    <div class="mb-3">
                        <label class="form-label">Hak Akses Data Non PPN</label>
                        <textarea name="data_n" class="form-control" rows="3">{{ $maping->data_n }}</textarea>
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
        let kodeTerakhir =
            document.getElementById('kode_barang').value;

        /* =====================================
           VALIDASI KODE BARANG
        ===================================== */
        document.getElementById('nama_barang')
            .addEventListener('focus', function() {

                const kodeBarang =
                    document.getElementById('kode_barang').value.trim();

                const perusahaanId =
                    document.getElementById('id_perusahaan')?.value;

                const idMaping =
                    document.getElementById('id_maping').value;

                // validasi perusahaan
                if (!perusahaanId) {

                    Swal.fire(
                        'Peringatan',
                        'Pilih perusahaan terlebih dahulu',
                        'warning'
                    );

                    return;
                }

                // validasi kode
                if (!kodeBarang) {

                    Swal.fire(
                        'Peringatan',
                        'Isi kode barang terlebih dahulu',
                        'warning'
                    );

                    return;
                }

                // kode tidak berubah
                if (kodeBarang === kodeTerakhir) {
                    return;
                }

                fetch("{{ route('maping.getBarang') }}", {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]').content
                        },

                        body: JSON.stringify({
                            kode_barang: kodeBarang,
                            perusahaan_id: perusahaanId,
                            id_maping: idMaping
                        })

                    })

                    .then(res => res.json())

                    .then(res => {

                        // tidak ditemukan
                        if (!res.status && !res.used) {

                            resetBarang();

                            Swal.fire(
                                'Gagal',
                                'Kode barang tidak ditemukan',
                                'error'
                            );

                            return;
                        }

                        // sudah dipakai
                        if (res.used) {

                            resetBarang();

                            Swal.fire(
                                'Gagal',
                                'Kode barang sudah dipakai',
                                'error'
                            );

                            return;
                        }

                        // sukses
                        kodeTerakhir = kodeBarang;

                        document.getElementById('id_keluar').value =
                            res.data.id_keluar ?? '';

                        document.getElementById('nama_barang').value =
                            res.data.nama_barang ?? '';

                        document.getElementById('type').value =
                            res.data.type ?? '';

                        document.getElementById('merek').value =
                            res.data.merek ?? '';

                        document.getElementById('warna').value =
                            res.data.warna ?? '';

                        document.getElementById('nama_karyawan').value =
                            res.data.nama_karyawan ?? '';

                    })

                    .catch(err => {

                        console.log(err);

                        resetBarang();

                        Swal.fire(
                            'Error',
                            'Terjadi kesalahan server',
                            'error'
                        );

                    });

            });

        /* =====================================
           RESET BARANG
        ===================================== */
        function resetBarang() {

            document.getElementById('id_keluar').value = '';
            document.getElementById('nama_barang').value = '';
            document.getElementById('type').value = '';
            document.getElementById('merek').value = '';
            document.getElementById('warna').value = '';
            document.getElementById('nama_karyawan').value = '';

        }

        /* =====================================
           LOAD LOKASI BERDASARKAN PERUSAHAAN
        ===================================== */
        document.addEventListener('DOMContentLoaded', function() {

            const perusahaanSelect =
                document.getElementById('id_perusahaan');

            const lokasiSelect =
                document.getElementById('id_lokasi');

            const lokasiSelected =
                "{{ $maping->id_lokasi }}";

            // super admin
            if (
                perusahaanSelect &&
                "{{ auth()->user()->role }}" === 'super_admin'
            ) {

                loadLokasi(perusahaanSelect.value);

                perusahaanSelect.addEventListener('change', function() {

                    resetBarang();

                    document.getElementById('kode_barang').value = '';

                    loadLokasi(this.value);

                });

            }

            function loadLokasi(perusahaanId) {

                lokasiSelect.innerHTML =
                    '<option value="">-- Pilih Lokasi --</option>';

                if (!perusahaanId) {
                    return;
                }

                fetch(`/maping/lokasi-by-perusahaan/${perusahaanId}`)

                    .then(response => response.json())

                    .then(data => {

                        data.forEach(lokasi => {

                            lokasiSelect.innerHTML += `
                            <option value="${lokasi.id}"
                                ${lokasi.id == lokasiSelected ? 'selected' : ''}>
                                ${lokasi.nama_lokasi}
                            </option>
                        `;

                        });

                    })

                    .catch(error => {

                        console.log(error);

                    });

            }

        });
    </script>
@endsection
