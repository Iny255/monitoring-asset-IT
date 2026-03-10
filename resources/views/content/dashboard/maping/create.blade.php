@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Data Maping')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0 fw-bold text-primary">Tambah Data Maping</h5>
    </div>

    <div class="card-body">
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

        <form action="{{ route('maping.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                {{-- TRANSAKSI KELUAR --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kode Barang</label>
                    <input type="text"
                        id="kode_barang"
                        class="form-control"
                        placeholder="Masukkan Kode Barang"
                        autocomplete="off"
                        required>

                    {{-- id_keluar hasil pencarian --}}
                    <input type="hidden" name="id_keluar" id="id_keluar">
                </div>
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input type="text" id="nama_barang" class="form-control" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Type</label>
                        <input type="text" id="type" class="form-control" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Merek</label>
                        <input type="text" id="merek" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Warna</label>
                        <input type="text" id="warna" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Nama Karyawan</label>
                        <input type="text" id="nama_karyawan" class="form-control" readonly>
                    </div>

                </div>
                {{-- LOKASI --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <select name="id_lokasi" class="form-select" required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}">
                            {{ $lokasi->nama_lokasi }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PERUSAHAAN --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Perusahaan</label>
                    <select name="id_perusahaan" class="form-select" required>
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach ($perusahaans as $perusahaan)
                        <option value="{{ $perusahaan->id }}">
                            {{ $perusahaan->nama_perusahaan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PROCESSOR --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Processor</label>
                    <input type="text" name="processor"
                        class="form-control"
                        placeholder="Contoh: Intel Core i5">
                       
                </div>

                {{-- DEVICE ID --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Device ID</label>
                    <input type="text" name="device_id"
                        class="form-control">
                       
                </div>

                {{-- PRODUK ID --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produk ID</label>
                    <input type="text" name="produk_id"
                        class="form-control">
                </div>

                {{-- RAM --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">RAM (GB)</label>
                    <input type="number" name="ram"
                        class="form-control"
                        min="1">
                </div>

                {{-- SYSTEM --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">System</label>
                    <input type="text" name="system"
                        class="form-control"
                        placeholder="Windows / Linux">
                </div>

                {{-- VERSION --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Version</label>
                    <input type="text" name="version"
                        class="form-control"
                        placeholder="24H2">
                </div>

                {{-- INSTAL ON --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Instal On</label>
                    <input type="date" name="instal_on"
                        class="form-control">
                </div>

                {{-- APLIKASI --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Aplikasi</label>
                    <input type="text" name="aplikasi"
                        class="form-control"
                        placeholder="Office, Accurate, dll">
                </div>

                {{-- DATA --}}
                <div class="mb-3">
                    <label class="form-label">Hak Akses Data PPN</label>
                    <textarea name="data_p" class="form-control" rows="3" placeholder="Masukkan Folder P">
                    </textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hak Akses Data Non PPN</label>
                    <textarea name="data_n" class="form-control" rows="3" placeholder="Masukkan Folder N">
                    </textarea>
                </div>


            </div>

            {{-- ACTION --}}
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Simpan
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
    let sudahValidasi = false;

    function fetchBarang(kodeBarang) {
        fetch("{{ route('maping.getBarang') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    kode_barang: kodeBarang
                })
            })
            .then(res => res.json())
            .then(res => {

                // ❌ Kode tidak ditemukan
                if (!res.status) {
                    resetBarang();

                    if (!sudahValidasi) {
                        sudahValidasi = true;
                        Swal.fire('Gagal', 'Kode barang tidak ditemukan', 'error');
                    }
                    return;
                }

                // ❌ Kode sudah dipakai
                if (res.used) {
                    resetBarang();

                    if (!sudahValidasi) {
                        sudahValidasi = true;
                        Swal.fire('Gagal', 'Kode barang sudah dipakai', 'error');
                    }
                    return;
                }

                // ✅ Data ditemukan & belum dipakai
                sudahValidasi = false;

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
    }

    document.getElementById('nama_barang').addEventListener('focus', function() {
        const kodeBarang = document.getElementById('kode_barang').value.trim();

        if (!kodeBarang) {
            if (!sudahValidasi) {
                sudahValidasi = true;
                Swal.fire('Peringatan', 'Isi kode barang terlebih dahulu', 'warning');
            }
            return;
        }

        fetchBarang(kodeBarang);
    });

    // reset flag jika kode diubah
    document.getElementById('kode_barang').addEventListener('input', function() {
        sudahValidasi = false;
    });

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