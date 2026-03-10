@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Transaksi Keluar')

@section('content')

<div class="card shadow-sm">
    <div class="card-header ">
        <h5 class="text-primary mb-0">Edit Transaksi Keluar</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('transaksi-keluar.update', $keluar->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- KODE KELUAR --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Keluar</label>
                <input type="text" class="form-control" value="{{ $keluar->kode_keluar }}" readonly>
            </div>

            {{-- ===================== KODE MASUK ===================== --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Masuk</label>
                <input type="text" id="kode_masuk" class="form-control"
                    value="{{ $keluar->masuk->kode_masuk }}">
                <input type="hidden" name="id_masuk" id="id_masuk"
                    value="{{ $keluar->id_masuk }}">
            </div>

            {{-- ===================== INFO BARANG ===================== --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" id="nama_barang" class="form-control"
                        value="{{ $keluar->masuk->kategori->nama_barang }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" id="type" class="form-control"
                        value="{{ $keluar->masuk->type }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Merek</label>
                    <input type="text" id="merek" class="form-control"
                        value="{{ $keluar->masuk->merek }}" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Beli</label>
                <input type="text" id="tgl_beli" class="form-control"
                    value="{{ $keluar->masuk->tgl_beli }}" readonly>
            </div>

            {{-- ===================== DATA BARANG ===================== --}}
            <div class="mb-3">
                <label class="form-label">Kode Barang</label>
                <input type="text" name="kode_barang" class="form-control"
                    value="{{ old('kode_barang', $keluar->kode_barang) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Warna</label>
                <input type="text" name="warna" class="form-control"
                    value="{{ old('warna', $keluar->warna) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">No Inventaris</label>
                <input type="text" name="no_inventaris" class="form-control"
                    value="{{ old('no_inventaris', $keluar->no_inventaris) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control"
                    value="{{ $keluar->jumlah }}" min="1">
            </div>

            {{-- ===================== JENIS PENERIMA ===================== --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Penerima</label>
                <select name="jenis_penerima" id="jenis_penerima" class="form-select">
                    <option value="Perorangan" {{ $keluar->jenis_penerima=='Perorangan'?'selected':'' }}>Perorangan</option>
                    <option value="Perdivisi" {{ $keluar->jenis_penerima=='Perdivisi'?'selected':'' }}>Perdivisi</option>
                </select>
            </div>

            {{-- ===================== PERORANGAN ===================== --}}
            <div id="blok_perorangan">

                <div class="mb-3">
                    <label class="form-label">Nama Karyawan</label>
                    <input type="text" id="nama_karyawan" class="form-control"
                        value="{{ optional($keluar->karyawan)->nama_karyawan }}">
                    <input type="hidden" name="id_karyawan" id="id_karyawan"
                        value="{{ $keluar->id_karyawan }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Divisi</label>
                        <input type="text" id="divisi" class="form-control"
                            value="{{ optional($keluar->karyawan)->divisi }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" id="perusahaan" class="form-control"
                            value="{{ optional($keluar->karyawan)->perusahaan }}" readonly>
                    </div>
                </div>

            </div>

            {{-- ===================== PERDIVISI ===================== --}}
            <div id="blok_divisi">

                <div class="mb-3">
                    <label class="form-label">Divisi</label>
                    <input type="text" name="divisi_klr" class="form-control"
                        value="{{ $keluar->divisi_klr }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Perusahaan</label>
                    <input type="text" name="perusahaan_klr" class="form-control"
                        value="{{ $keluar->perusahaan_klr }}">
                </div>

            </div>

            {{-- ===================== KETERANGAN ===================== --}}
            <div class="mb-4">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control">{{ $keluar->keterangan }}</textarea>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">Kembali</a>

        </form>
    </div>
</div>

@endsection


@section('scripts')

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        const jenis = document.getElementById('jenis_penerima');
        const blokPerorangan = document.getElementById('blok_perorangan');
        const blokDivisi = document.getElementById('blok_divisi');

        const kodeMasukInput = document.getElementById('kode_masuk');
        const namaKaryawanInput = document.getElementById('nama_karyawan');

        const idMasuk = document.getElementById('id_masuk');
        const idKaryawan = document.getElementById('id_karyawan');

        const namaBarang = document.getElementById('nama_barang');
        const typeBarang = document.getElementById('type');
        const merekBarang = document.getElementById('merek');
        const tglBeli = document.getElementById('tgl_beli');

        const divisi = document.getElementById('divisi');
        const perusahaan = document.getElementById('perusahaan');

        let firstLoad = true; // supaya edit tidak muncul swal saat load

        // ================= TOGGLE JENIS =================
        function togglePenerima() {

            if (jenis.value === 'Perorangan') {
                blokPerorangan.style.display = 'block';
                blokDivisi.style.display = 'none';
            } else {
                blokPerorangan.style.display = 'none';
                blokDivisi.style.display = 'block';

                // reset karyawan jika pindah ke divisi
                namaKaryawanInput.value = '';
                idKaryawan.value = '';
                divisi.value = '';
                perusahaan.value = '';
            }
        }

        togglePenerima();
        jenis.addEventListener('change', togglePenerima);


        // ================= AUTOFILL KODE MASUK =================
        kodeMasukInput.addEventListener('blur', function() {

            let kode = this.value.trim();
            if (kode === '') return;

            fetch(`{{ url('/dashboard/masuk-by-kode') }}/${kode}`)
                .then(res => res.json())
                .then(res => {

                    if (!res.status) {
                        if (!firstLoad) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Kode masuk tidak ditemukan'
                            });
                        }
                        return;
                    }

                    const d = res.data;

                    idMasuk.value = d.id_masuk;
                    namaBarang.value = d.nama_barang;
                    typeBarang.value = d.type;
                    merekBarang.value = d.merek;
                    tglBeli.value = d.tgl_beli;

                    firstLoad = false;
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengambil data kode masuk'
                    });
                });
        });


        // ================= AUTOFILL KARYAWAN =================
        namaKaryawanInput.addEventListener('blur', function() {

            if (jenis.value !== 'Perorangan') return;

            let nama = this.value.trim();
            if (nama === '') return;

            fetch("{{ route('keluar.getKaryawanByNama') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrf
                    },
                    body: JSON.stringify({
                        nama_karyawan: nama
                    })
                })
                .then(res => res.json())
                .then(res => {

                    if (!res.status) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Karyawan tidak ditemukan'
                        });
                        return;
                    }

                    const d = res.data;

                    idKaryawan.value = d.id;
                    divisi.value = d.divisi;
                    perusahaan.value = d.perusahaan;
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengambil data karyawan'
                    });
                });
        });

    });
</script>

@endsection