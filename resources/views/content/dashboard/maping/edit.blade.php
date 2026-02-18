@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Data Maping')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-white">
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

                {{-- KODE BARANG --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kode Barang</label>
                    <input type="text"
                        id="kode_barang"
                        class="form-control"
                        value="{{ $maping->keluar->kode_barang ?? '' }}"
                        autocomplete="off"
                        required>

                    <input type="hidden" name="id_keluar" id="id_keluar"
                        value="{{ $maping->id_keluar }}">
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

                {{-- LOKASI --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <select name="id_lokasi" class="form-select">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}"
                            {{ $maping->id_lokasi == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_lokasi }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PERUSAHAAN --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Perusahaan</label>
                    <select name="id_perusahaan" class="form-select">
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach ($perusahaans as $perusahaan)
                        <option value="{{ $perusahaan->id }}"
                            {{ $maping->id_perusahaan == $perusahaan->id ? 'selected' : '' }}>
                            {{ $perusahaan->nama_perusahaan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PROCESSOR --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Processor</label>
                    <input type="text" name="processor" class="form-control"
                        value="{{ $maping->processor }}">
                </div>

                {{-- DEVICE ID --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Device ID</label>
                    <input type="text" name="device_id" class="form-control"
                        value="{{ $maping->device_id }}">
                </div>

                {{-- PRODUK ID --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produk ID</label>
                    <input type="text" name="produk_id" class="form-control"
                        value="{{ $maping->produk_id }}">
                </div>

                {{-- RAM --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">RAM</label>
                    <input type="number" name="ram" class="form-control"
                        value="{{ $maping->ram }}">
                </div>

                {{-- SYSTEM --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">System</label>
                    <input type="text" name="system" class="form-control"
                        value="{{ $maping->system }}">
                </div>

                {{-- VERSION --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Version</label>
                    <input type="text" name="version" class="form-control"
                        value="{{ $maping->version }}">
                </div>

                {{-- INSTAL ON --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Instal On</label>
                    <input type="date" name="instal_on" class="form-control"
                        value="{{ $maping->instal_on }}">
                </div>

                {{-- APLIKASI --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Aplikasi</label>
                    <input type="text" name="aplikasi" class="form-control"
                        value="{{ $maping->aplikasi }}">
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

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    let sudahValidasi = false;
    let kodeTerakhir = document.getElementById('kode_barang').value;

    /* ===============================
       VALIDASI SAAT FOCUS NAMA BARANG
    =================================*/
    document.getElementById('nama_barang').addEventListener('focus', function() {

        const kodeBarang = document.getElementById('kode_barang').value.trim();
        const idMaping = document.getElementById('id_maping').value;

        // Jika kode kosong
        if (!kodeBarang) {
            if (!sudahValidasi) {
                sudahValidasi = true;
                Swal.fire('Peringatan', 'Isi kode barang dulu', 'warning');
            }
            return;
        }

        // Jika kode tidak berubah → jangan validasi ulang
        if (kodeBarang === kodeTerakhir) {
            return;
        }

        fetch("{{ route('maping.getBarang') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    kode_barang: kodeBarang,
                    id_maping: idMaping // penting untuk EDIT
                })
            })
            .then(res => res.json())
            .then(res => {

                /* ===============================
                   KODE TIDAK DITEMUKAN
                =================================*/
                if (!res.status && !res.used) {
                    resetBarang();

                    if (!sudahValidasi) {
                        sudahValidasi = true;
                        Swal.fire('Gagal', 'Kode barang tidak ditemukan', 'error');
                    }
                    return;
                }

                /* ===============================
                   KODE SUDAH DIPAKAI
                =================================*/
                if (res.used) {
                    resetBarang();

                    if (!sudahValidasi) {
                        sudahValidasi = true;
                        Swal.fire('Gagal', 'Kode barang sudah dipakai', 'error');
                    }
                    return;
                }

                /* ===============================
                   DATA VALID
                =================================*/
                sudahValidasi = false;
                kodeTerakhir = kodeBarang;

                document.getElementById('id_keluar').value = res.data.id_keluar ?? '';
                document.getElementById('nama_barang').value = res.data.nama_barang ?? '';
                document.getElementById('type').value = res.data.type ?? '';
                document.getElementById('merek').value = res.data.merek ?? '';
                document.getElementById('warna').value = res.data.warna ?? '';
                document.getElementById('nama_karyawan').value = res.data.nama_karyawan ?? '';
            })
            .catch(err => {
                console.error(err);
                resetBarang();
            });

    });


    /* ===============================
       RESET FLAG JIKA KODE DIUBAH
    =================================*/
    document.getElementById('kode_barang').addEventListener('input', function() {
        sudahValidasi = false;
    });


    /* ===============================
       RESET FIELD BARANG
    =================================*/
    function resetBarang() {
        document.getElementById('id_keluar').value = '';
        document.getElementById('nama_barang').value = '';
        document.getElementById('type').value = '';
        document.getElementById('merek').value = '';
        document.getElementById('warna').value = '';
        document.getElementById('nama_karyawan').value = '';
    }
</script>
@endsection

@endsection