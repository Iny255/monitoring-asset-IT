@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Transaksi Keluar')

@section('content')

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header bg-white border-bottom">
            <h4 class="mb-0 text-primary fw-bold">
                Edit Transaksi Keluar
            </h4>
        </div>

        <div class="card-body">

            {{-- ALERT --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">

                    {{ session('error') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>

                </div>
            @endif

            <form action="{{ route('transaksi-keluar.update', $keluar->id) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- ========================================= --}}
                {{-- SUPER ADMIN --}}
                {{-- ========================================= --}}

                @if (auth()->user()->role === 'super_admin')

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-light fw-semibold">
                            Data Perusahaan
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Perusahaan
                                    </label>

                                    <select name="perusahaan_id" id="perusahaan_select" class="form-select" required>

                                        <option value="">
                                            -- Pilih Perusahaan --
                                        </option>

                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $keluar->id_perusahaan == $p->id ? 'selected' : '' }}>

                                                {{ $p->nama_perusahaan }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ========================================= --}}
                {{-- INFORMASI BARANG --}}
                {{-- ========================================= --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-light fw-semibold">
                        Informasi Barang
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- KODE KELUAR --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kode Keluar
                                </label>

                                <input type="text" name="kode_keluar" id="kode_keluar" class="form-control"
                                    value="{{ $keluar->kode_keluar }}" readonly>

                            </div>

                            {{-- KODE MASUK --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kode Masuk
                                </label>

                                <input type="text" id="kode_masuk" class="form-control"
                                    value="{{ $keluar->masuk->kode_masuk ?? '' }}">

                                <input type="hidden" name="id_masuk" id="id_masuk" value="{{ $keluar->id_masuk }}">

                            </div>

                            {{-- NAMA BARANG --}}
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Nama Barang
                                </label>

                                <input type="text" id="nama_barang" class="form-control"
                                    value="{{ optional(optional($keluar->masuk)->kategori)->nama_barang }}" readonly>

                            </div>

                            {{-- TYPE --}}
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Type
                                </label>

                                <input type="text" id="type" class="form-control"
                                    value="{{ $keluar->masuk->type ?? '' }}" readonly>

                            </div>

                            {{-- MEREK --}}
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Merek
                                </label>

                                <input type="text" id="merek" class="form-control"
                                    value="{{ $keluar->masuk->merek ?? '' }}" readonly>

                            </div>

                            {{-- TANGGAL BELI --}}
                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Tanggal Beli
                                </label>

                                <input type="text" id="tgl_beli" class="form-control"
                                    value="{{ $keluar->masuk->tgl_beli ?? '' }}" readonly>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- DETAIL TRANSAKSI --}}
                {{-- ========================================= --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-light fw-semibold">
                        Detail Transaksi Keluar
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- KODE BARANG --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Kode Barang
                                </label>

                                <input type="text" name="kode_barang" class="form-control"
                                    value="{{ old('kode_barang', $keluar->kode_barang) }}">

                            </div>

                            {{-- WARNA --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Warna
                                </label>

                                <input type="text" name="warna" class="form-control"
                                    value="{{ old('warna', $keluar->warna) }}">

                            </div>

                            {{-- NO INVENTARIS --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    No Inventaris
                                </label>

                                <input type="text" name="no_inventaris" class="form-control"
                                    value="{{ old('no_inventaris', $keluar->no_inventaris) }}">

                            </div>

                            {{-- JUMLAH --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Jumlah Keluar
                                </label>

                                <input type="number" name="jumlah" class="form-control"
                                    value="{{ old('jumlah', $keluar->jumlah) }}" min="1">

                            </div>

                            {{-- TANGGAL --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Tanggal Keluar
                                </label>

                                <input type="date" name="tgl_keluar" class="form-control"
                                    value="{{ old('tgl_keluar', $keluar->tgl_keluar ? \Carbon\Carbon::parse($keluar->tgl_keluar)->format('Y-m-d') : '') }}"
                                    required>

                            </div>

                            {{-- JENIS --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Jenis Penerima
                                </label>

                                <select name="jenis_penerima" id="jenis_penerima" class="form-select">

                                    <option value="Perorangan"
                                        {{ $keluar->jenis_penerima == 'Perorangan' ? 'selected' : '' }}>

                                        Perorangan

                                    </option>

                                    <option value="Perdivisi"
                                        {{ $keluar->jenis_penerima == 'Perdivisi' ? 'selected' : '' }}>

                                        Perdivisi

                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- PERORANGAN --}}
                {{-- ========================================= --}}

                <div id="group_karyawan" class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-light fw-semibold">
                        Informasi Penerima
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- NAMA --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Nama Karyawan
                                </label>

                                <input type="text" id="nama_karyawan" class="form-control"
                                    value="{{ optional($keluar->karyawan)->nama_karyawan }}">

                                <input type="hidden" name="id_karyawan" id="id_karyawan"
                                    value="{{ $keluar->id_karyawan }}">

                            </div>

                            {{-- DIVISI --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Divisi
                                </label>

                                <input type="text" id="divisi" class="form-control"
                                    value="{{ optional($keluar->karyawan)->divisi }}" readonly>

                            </div>

                            {{-- PERUSAHAAN --}}
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Perusahaan
                                </label>

                                <input type="text" id="perusahaan" class="form-control"
                                    value="{{ $keluar->karyawan?->perusahaan?->nama_perusahaan }}" readonly>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- PERDIVISI --}}
                {{-- ========================================= --}}

                <div id="group_divisi" class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-light fw-semibold">
                        Informasi Divisi
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Divisi
                                </label>

                                <input type="text" name="divisi_klr" id="divisi_klr" class="form-control"
                                    value="{{ $keluar->divisi_klr }}">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Perusahaan
                                </label>

                                <input type="text" class="form-control" value="{{ $keluar->perusahaan_klr }}"
                                    readonly>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- UPLOAD GAMBAR --}}
                {{-- ========================================= --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-light fw-semibold">
                        Upload Gambar
                    </div>

                    <div class="card-body">

                        <div class="row align-items-center">

                            <div class="col-md-6 mb-3">

                                <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png">

                                <small class="text-muted">
                                    Format JPG, JPEG, PNG • Maksimal 2 MB
                                </small>

                            </div>

                            <div class="col-md-6 text-center">

                                @if ($keluar->gambar)
                                    <img src="{{ asset('storage/' . $keluar->gambar) }}"
                                        class="img-fluid rounded shadow border"
                                        style="max-height:250px; object-fit:cover;">
                                @else
                                    <div class="text-muted py-4">

                                        <i class="bx bx-image-alt display-5"></i>

                                        <p class="mb-0 mt-2">
                                            Gambar tidak tersedia
                                        </p>

                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- KETERANGAN --}}
                {{-- ========================================= --}}

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-light fw-semibold">
                        Keterangan
                    </div>

                    <div class="card-body">

                        <textarea name="keterangan" class="form-control" rows="3">{{ $keluar->keterangan }}</textarea>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- BUTTON --}}
                {{-- ========================================= --}}

                <div class="d-flex justify-content-end gap-2 border-top pt-4">

                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary px-4">

                        Kembali

                    </a>

                    <button type="submit" class="btn btn-primary px-4">

                        Update

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

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // =========================
            // ELEMENT
            // =========================

            const perusahaanSelect = document.getElementById('perusahaan_select');

            const kodeKeluar = document.getElementById('kode_keluar');

            const kodeMasuk = document.getElementById('kode_masuk');

            const jenisPenerima = document.getElementById('jenis_penerima');

            const groupKaryawan = document.getElementById('group_karyawan');

            const groupDivisi = document.getElementById('group_divisi');


            // =========================
            // TOGGLE PENERIMA
            // =========================

            function togglePenerima() {

                let jenis = jenisPenerima.value;

                if (jenis === 'Perorangan') {

                    groupKaryawan.style.display = 'block';
                    groupDivisi.style.display = 'none';

                } else {

                    groupKaryawan.style.display = 'none';
                    groupDivisi.style.display = 'block';

                }
            }

            togglePenerima();

            jenisPenerima.addEventListener('change', togglePenerima);


            // =========================
            // SUPER ADMIN
            // AUTO KODE KELUAR
            // =========================

            if (perusahaanSelect) {

                perusahaanSelect.addEventListener('change', function() {

                    let id = this.value;

                    if (!id) return;

                    // 🔥 AUTO KODE KELUAR
                    fetch('/dashboard/get-kode-keluar/' + id)

                        .then(res => res.json())

                        .then(data => {

                            kodeKeluar.value = data.kode;

                        });


                    // 🔥 RESET DATA BARANG
                    document.getElementById('kode_masuk').value = '';
                    document.getElementById('id_masuk').value = '';

                    document.getElementById('nama_barang').value = '';
                    document.getElementById('type').value = '';
                    document.getElementById('merek').value = '';
                    document.getElementById('tgl_beli').value = '';

                });

            }


            // =========================
            // AUTOFILL KODE MASUK
            // =========================

            kodeMasuk.addEventListener('blur', function() {

                let kode = this.value.trim();

                if (!kode) return;

                fetch("{{ route('transaksi-keluar.autofill') }}", {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf
                        },

                        body: JSON.stringify({

                            kode_masuk: kode,

                            perusahaan_id: perusahaanSelect ?
                                perusahaanSelect.value : null

                        })

                    })

                    .then(res => res.json())

                    .then(res => {

                        if (!res.status) {

                            Swal.fire(
                                'Gagal',
                                'Kode masuk tidak ditemukan',
                                'error'
                            );

                            return;
                        }

                        document.getElementById('id_masuk').value = res.data.id_masuk;

                        document.getElementById('nama_barang').value = res.data.nama_barang;

                        document.getElementById('type').value = res.data.type;

                        document.getElementById('merek').value = res.data.merek;

                        document.getElementById('tgl_beli').value = res.data.tgl_beli;

                    });

            });


            // =========================
            // AUTOFILL KARYAWAN
            // =========================

            document.getElementById('nama_karyawan')
                .addEventListener('blur', function() {

                    if (jenisPenerima.value !== 'Perorangan') return;

                    let nama = this.value.trim();

                    if (!nama) return;

                    fetch("{{ route('keluar.getKaryawanByNama') }}", {

                            method: "POST",

                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrf
                            },

                            body: JSON.stringify({

                                nama_karyawan: nama,

                                perusahaan_id: perusahaanSelect ?
                                    perusahaanSelect.value : null

                            })

                        })

                        .then(res => res.json())

                        .then(res => {

                            if (!res.status) {

                                Swal.fire(
                                    'Gagal',
                                    'Nama karyawan tidak ditemukan',
                                    'error'
                                );

                                return;
                            }

                            document.getElementById('id_karyawan').value = res.data.id;

                            document.getElementById('divisi').value = res.data.divisi;

                            document.getElementById('perusahaan').value = res.data.perusahaan;

                        });

                });

        });
    </script>

@endsection
