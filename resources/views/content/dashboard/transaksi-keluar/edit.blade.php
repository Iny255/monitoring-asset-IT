@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Transaksi Keluar')

@section('content')

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="text-primary mb-0">Edit Transaksi Keluar</h5>
        </div>

        <div class="card-body">

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('transaksi-keluar.update', $keluar->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    {{-- SUPER ADMIN --}}
                    @if (auth()->user()->role === 'super_admin')

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Perusahaan</label>

                            <select name="perusahaan_id" id="perusahaan_select" class="form-select" required>

                                <option value="">-- Pilih Perusahaan --</option>

                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}"
                                        {{ $keluar->id_perusahaan == $p->id ? 'selected' : '' }}>

                                        {{ $p->nama_perusahaan }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                    @endif


                    {{-- KODE KELUAR --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Kode Keluar</label>

                        <input type="text" name="kode_keluar" id="kode_keluar" class="form-control"
                            value="{{ $keluar->kode_keluar }}" readonly>

                    </div>


                    {{-- KODE MASUK --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Kode Masuk</label>

                        <input type="text" id="kode_masuk" class="form-control"
                            value="{{ $keluar->masuk->kode_masuk ?? '' }}">

                        <input type="hidden" name="id_masuk" id="id_masuk" value="{{ $keluar->id_masuk }}">

                    </div>

                </div>


                {{-- DATA BARANG --}}
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nama Barang</label>

                        <input type="text" id="nama_barang" class="form-control"
                            value="{{ optional(optional($keluar->masuk)->kategori)->nama_barang }}" readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Type</label>

                        <input type="text" id="type" class="form-control" value="{{ $keluar->masuk->type ?? '' }}"
                            readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Merek</label>

                        <input type="text" id="merek" class="form-control" value="{{ $keluar->masuk->merek ?? '' }}"
                            readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Beli</label>

                        <input type="text" id="tgl_beli" class="form-control"
                            value="{{ $keluar->masuk->tgl_beli ?? '' }}" readonly>
                    </div>

                </div>


                {{-- DATA KELUAR --}}
                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">Kode Barang</label>

                        <input type="text" name="kode_barang" class="form-control"
                            value="{{ old('kode_barang', $keluar->kode_barang) }}">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">Warna</label>

                        <input type="text" name="warna" class="form-control"
                            value="{{ old('warna', $keluar->warna) }}">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">No Inventaris</label>

                        <input type="text" name="no_inventaris" class="form-control"
                            value="{{ old('no_inventaris', $keluar->no_inventaris) }}">

                    </div>

                </div>


                <div class="col-md-4 mb-3">

                    <label class="form-label">Jumlah Keluar</label>

                    <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah', $keluar->jumlah) }}"
                        min="1">

                </div>

                {{-- TANGGAL KELUAR --}}
                <div class="col-md-4 mb-3">

                    <label class="form-label">Tanggal Keluar</label>

                    <input type="date" name="tgl_keluar" class="form-control"
                        value="{{ old('tgl_keluar', $keluar->tgl_keluar ? \Carbon\Carbon::parse($keluar->tgl_keluar)->format('Y-m-d') : '') }}"
                        required>

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">Jenis Penerima</label>

                    <select name="jenis_penerima" id="jenis_penerima" class="form-select">

                        <option value="Perorangan" {{ $keluar->jenis_penerima == 'Perorangan' ? 'selected' : '' }}>

                            Perorangan

                        </option>

                        <option value="Perdivisi" {{ $keluar->jenis_penerima == 'Perdivisi' ? 'selected' : '' }}>

                            Perdivisi

                        </option>

                    </select>

                </div>

        </div>


        {{-- PERORANGAN --}}
        <div id="group_karyawan">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label class="form-label">Nama Karyawan</label>

                    <input type="text" id="nama_karyawan" class="form-control"
                        value="{{ optional($keluar->karyawan)->nama_karyawan }}">

                    <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{ $keluar->id_karyawan }}">

                </div>


                <div class="col-md-4 mb-3">

                    <label class="form-label">Divisi</label>

                    <input type="text" id="divisi" class="form-control"
                        value="{{ optional($keluar->karyawan)->divisi }}" readonly>

                </div>


                <div class="col-md-4 mb-3">

                    <label class="form-label">Perusahaan</label>

                    <input type="text" id="perusahaan" class="form-control"
                        value="{{ $keluar->karyawan?->perusahaan?->nama_perusahaan }}" readonly>

                </div>

            </div>

        </div>


        {{-- PERDIVISI --}}
        <div id="group_divisi">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">Divisi</label>

                    <input type="text" name="divisi_klr" id="divisi_klr" class="form-control"
                        value="{{ $keluar->divisi_klr }}">

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">Perusahaan</label>

                    <input type="text" class="form-control" value="{{ $keluar->perusahaan_klr }}" readonly>

                </div>

            </div>

        </div>


        {{-- KETERANGAN --}}
        <div class="mb-3">

            <label class="form-label">Keterangan</label>

            <textarea name="keterangan" class="form-control" rows="2">{{ $keluar->keterangan }}</textarea>

        </div>


        {{-- BUTTON --}}
        <div class="d-flex justify-content-end gap-2 mt-3">

            <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">

                Kembali

            </a>

            <button class="btn btn-primary">

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
