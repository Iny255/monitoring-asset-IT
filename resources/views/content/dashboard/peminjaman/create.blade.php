@extends('layouts/contentNavbarLayout')

@section('title', 'Pengajuan Peminjaman Barang')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-10">

                <div class="card shadow-sm border-0">

                    {{-- HEADER --}}
                    <div class="card-header text-white py-3"
                        style="background: linear-gradient(
                    90deg,
                    var(--theme-primary),
                    var(--theme-secondary)
                    );
                    ">

                        <h4 class="mb-0 text-white fw-bold">
                            Pengajuan Peminjaman
                        </h4>

                    </div>

                    <div class="card-body">

                        <form action="{{ route('peminjaman.store') }}" method="POST">

                            @csrf

                            {{-- TIPE --}}
                            <input type="hidden" name="tipe_peminjam" id="jenis_perusahaan_input" value="internal">

                            <div class="row">

                                {{-- ========================================= --}}
                                {{-- KIRI --}}
                                {{-- ========================================= --}}
                                <div class="col-md-6">

                                    {{-- KODE --}}
                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Kode Barang
                                        </label>

                                        <input type="text" id="kode_barang" class="form-control">

                                        <input type="hidden" name="keluar_id" id="keluar_id">

                                    </div>

                                    {{-- NAMA --}}
                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Nama Barang
                                        </label>

                                        <input type="text" id="nama_barang" class="form-control" readonly>

                                    </div>

                                    {{-- JENIS --}}
                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Jenis Peminjam
                                        </label>

                                        <select id="jenis_perusahaan" class="form-select">

                                            <option value="internal">
                                                Internal
                                            </option>

                                            <option value="external">
                                                Eksternal
                                            </option>

                                        </select>

                                    </div>

                                    {{-- ========================================= --}}
                                    {{-- INTERNAL --}}
                                    {{-- ========================================= --}}
                                    <div id="form_internal">

                                        {{-- SUPER ADMIN --}}
                                        @if (auth()->user()->role === 'super_admin')

                                            <div class="mb-3">

                                                <label class="form-label fw-semibold">
                                                    Perusahaan
                                                </label>

                                                <select name="perusahaan_id" id="perusahaan_id" class="form-select">

                                                    <option value="">
                                                        -- Pilih Perusahaan --
                                                    </option>

                                                    @foreach ($perusahaans as $p)
                                                        <option value="{{ $p->id }}">

                                                            {{ $p->nama_perusahaan }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                            </div>
                                        @else
                                            {{-- USER BIASA --}}
                                            <div class="mb-3">

                                                <label class="form-label fw-semibold">
                                                    Perusahaan
                                                </label>

                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->perusahaan->nama_perusahaan ?? '-' }}"
                                                    readonly>

                                                <input type="hidden" name="perusahaan_id" id="perusahaan_id"
                                                    value="{{ auth()->user()->perusahaan->id ?? '' }}">

                                            </div>

                                        @endif

                                        {{-- KARYAWAN --}}
                                        <div class="mb-3 position-relative">

                                            <label class="form-label fw-semibold">
                                                Nama Karyawan
                                            </label>

                                            <input type="text" id="nama" class="form-control" autocomplete="off">

                                            <input type="hidden" name="karyawan_id" id="karyawan_id">

                                            <div id="hasil_nama" class="autocomplete-box d-none">
                                            </div>

                                        </div>

                                        {{-- LOKASI --}}
                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                Lokasi
                                            </label>

                                            <select name="lokasi_id" id="lokasi_id" class="form-select">

                                                @foreach ($lokasis as $l)
                                                    <option value="{{ $l->id }}">

                                                        {{ $l->nama_lokasi }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>

                                    </div>

                                    {{-- ========================================= --}}
                                    {{-- EXTERNAL --}}
                                    {{-- ========================================= --}}
                                    <div id="form_external" class="d-none">

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                Nama Peminjam
                                            </label>

                                            <input type="text" name="nama_eksternal" class="form-control">

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                Perusahaan
                                            </label>

                                            <input type="text" name="perusahaan_eksternal" class="form-control">

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                Lokasi
                                            </label>

                                            <input type="text" name="lokasi_manual" class="form-control">

                                        </div>

                                    </div>

                                </div>

                                {{-- ========================================= --}}
                                {{-- KANAN --}}
                                {{-- ========================================= --}}
                                <div class="col-md-6">

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Tanggal Pinjam
                                        </label>

                                        <input type="date" name="tanggal_pinjam" class="form-control" required>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Rencana Kembali
                                        </label>

                                        <input type="date" name="tanggal_rencana_kembali" class="form-control" required>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Keperluan
                                        </label>

                                        <textarea name="keperluan" class="form-control"></textarea>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Catatan
                                        </label>

                                        <textarea name="catatan" class="form-control"></textarea>

                                    </div>

                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <div class="text-end mt-3">

                                <button class="btn btn-primary px-4">

                                    Simpan

                                </button>
                                <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('page-style')

    <style>
        .autocomplete-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            z-index: 9999;
            max-height: 220px;
            overflow-y: auto;
            border-radius: 0 0 10px 10px;
        }

        .autocomplete-item {
            padding: 10px 14px;
            cursor: pointer;
            transition: .2s;
            border-bottom: 1px solid #f1f1f1;
        }

        .autocomplete-item:hover {
            background: #f5f7ff;
        }
    </style>

@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const kodeInput =
                document.getElementById('kode_barang');

            const namaInput =
                document.getElementById('nama_barang');

            const keluarId =
                document.getElementById('keluar_id');

            const jenis =
                document.getElementById('jenis_perusahaan');

            const hiddenJenis =
                document.getElementById('jenis_perusahaan_input');

            const internal =
                document.getElementById('form_internal');

            const external =
                document.getElementById('form_external');

            const inputNama =
                document.getElementById('nama');

            const inputId =
                document.getElementById('karyawan_id');

            const resultBox =
                document.getElementById('hasil_nama');

            const perusahaanSelect =
                document.getElementById('perusahaan_id');

            // =========================================
            // SWITCH INTERNAL / EXTERNAL
            // =========================================

            jenis.addEventListener('change', function() {

                hiddenJenis.value = this.value;

                if (this.value === 'external') {

                    internal.classList.add('d-none');
                    external.classList.remove('d-none');

                } else {

                    internal.classList.remove('d-none');
                    external.classList.add('d-none');

                }

            });

            // =========================================
            // CEK STATUS BARANG
            // =========================================

            kodeInput.addEventListener('blur', function() {

                let kode = this.value.trim();

                if (!kode) return;

                fetch(`/peminjaman/cek-status/${kode}`)

                    .then(res => res.json())

                    .then(res => {

                        if (res.dipinjam) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Barang sedang dipinjam',
                                text: 'Tidak bisa dipinjam ulang'
                            });

                            kodeInput.value = '';
                            namaInput.value = '';
                            keluarId.value = '';

                            return;
                        }

                        fetch(`/dashboard/peminjaman/get-nama-barang/${kode}`)

                            .then(res => res.json())

                            .then(data => {

                                if (data.status === 'ok') {

                                    namaInput.value =
                                        data.nama_barang;

                                    keluarId.value =
                                        data.keluar_id;

                                } else {

                                    Swal.fire(
                                        'Kode barang tidak ditemukan'
                                    );

                                }

                            });

                    });

            });

            // =========================================
            // AUTOCOMPLETE KARYAWAN
            // =========================================

            let debounce;

            inputNama.addEventListener('input', function() {

                let keyword =
                    this.value.trim();

                if (keyword.length < 2) {

                    closeDropdown();
                    return;

                }

                let perusahaanId =
                    perusahaanSelect.value;

                if (!perusahaanId) {

                    Swal.fire(
                        'Peringatan',
                        'Pilih perusahaan terlebih dahulu',
                        'warning'
                    );

                    return;

                }

                clearTimeout(debounce);

                debounce = setTimeout(() => {

                    fetch(
                            `/dashboard/peminjaman/search-karyawan?q=${keyword}&perusahaan_id=${perusahaanId}`
                        )

                        .then(res => res.json())

                        .then(data => {

                            resultBox.innerHTML = '';

                            if (!data.length) {

                                resultBox.innerHTML = `
                        <div class="p-2 text-muted">
                            Tidak ditemukan
                        </div>
                    `;

                                resultBox.classList.remove('d-none');

                                return;
                            }

                            data.forEach(item => {

                                let div =
                                    document.createElement('div');

                                div.classList.add('autocomplete-item');

                                div.textContent =
                                    item.nama_karyawan;

                                div.addEventListener('mousedown', function(e) {

                                    e.preventDefault();

                                    inputNama.value =
                                        item.nama_karyawan;

                                    inputId.value =
                                        item.id;

                                    closeDropdown();

                                });

                                resultBox.appendChild(div);

                            });

                            resultBox.classList.remove('d-none');

                        });

                }, 300);

            });

            function closeDropdown() {

                resultBox.innerHTML = '';
                resultBox.classList.add('d-none');

            }

            document.addEventListener('click', function(e) {

                if (
                    !resultBox.contains(e.target) &&
                    e.target !== inputNama
                ) {
                    closeDropdown();
                }

            });

        });
        document.addEventListener('DOMContentLoaded', function() {

            const perusahaanSelect =
                document.getElementById('perusahaan_id');

            const lokasiSelect =
                document.getElementById('lokasi_id');

            if (!perusahaanSelect || !lokasiSelect) return;

            perusahaanSelect.addEventListener('change', function() {

                const perusahaanId = this.value;

                // reset lokasi
                lokasiSelect.innerHTML =
                    '<option value="">-- Pilih Lokasi --</option>';

                if (!perusahaanId) return;

                fetch(`/dashboard/peminjaman/lokasi-by-perusahaan/${perusahaanId}`)

                    .then(res => res.json())

                    .then(data => {

                        data.forEach(lokasi => {

                            lokasiSelect.innerHTML += `
                        <option value="${lokasi.id}">
                            ${lokasi.nama_lokasi}
                        </option>
                    `;

                        });

                    })

                    .catch(err => {

                        console.log(err);

                    });

            });

        });
    </script>

@endsection
