@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Transaksi Masuk')

@section('content')

    <style>
        /* =========================
                                                   DARK MODE ONLY
                                                ========================= */

        .dark-style .card-dark {
            background: #1f2a3c;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .dark-style .card-dark .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .dark-style .card-dark .card-body {
            color: #cfd3ec;
        }

        /* FORM */

        .dark-style .card-dark .form-control,
        .dark-style .card-dark .form-select {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cfd3ec;
        }

        .dark-style .card-dark .form-control::placeholder {
            color: #94a3b8;
        }

        .dark-style .card-dark .form-control:focus,
        .dark-style .card-dark .form-select:focus {
            background: #0f172a;
            color: #fff;
            border-color: #696cff;
            box-shadow: none;
        }

        /* LABEL */

        .dark-style .card-dark .form-label {
            color: #cfd3ec;
        }

        /* FILE */

        .dark-style .card-dark input[type=file] {
            background: #0f172a;
            color: #cfd3ec;
        }

        /* =========================
           CARD
        ========================= */

        .card-dark {
            border-radius: 20px;
            overflow: hidden;
        }

        /* =========================
           INPUT
        ========================= */

        .form-control,
        .form-select {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #dbe2ea;
            transition: .2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .15);
        }

        /* =========================
           LABEL
        ========================= */

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #5b6475;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn {
            border-radius: 12px;
            height: 45px;
            min-width: 110px;
            font-weight: 600;
        }

        /* =========================
           SECTION TITLE
        ========================= */

        .form-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #696cff;
            margin-bottom: 18px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {
            .card-body {
                padding: 20px !important;
            }
        }
    </style>


    <div class="container-fluid px-4">

        <div class="row justify-content-center">

            <div class="col-12 col-xl-10">

                <div class="card card-dark shadow border-0">

                    <div class="card-header border-0 px-4 pt-4 pb-2">

                        <h5 class="text-primary mb-0">Tambah Transaksi Masuk</h5>
                        <small class="text-muted">Silakan isi data barang masuk</small>

                    </div>


                    <div class="card-body px-4 pb-4 pt-3">

                        <form action="{{ route('transaksi-masuk.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-section-title">
                                Informasi Asset
                            </div>

                            <div class="row">
                                {{-- PERUSAHAAN (HANYA SUPER ADMIN) --}}
                                @if (auth()->user()->role === 'super_admin')
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Perusahaan</label>

                                        <select name="id_perusahaan"
                                            class="form-select @error('id_perusahaan') is-invalid @enderror" required>

                                            <option value="">-- Pilih Perusahaan --</option>

                                            @foreach ($perusahaans as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ old('id_perusahaan') == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama_perusahaan }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('id_perusahaan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                {{-- KODE MASUK --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Kode Masuk</label>

                                    <input type="text" id="kode_masuk" name="kode_masuk" class="form-control"
                                        value="{{ $kodeMasuk }}" readonly>

                                </div>

                                {{-- NAMA BARANG --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Nama Barang</label>

                                    <select name="id_kategori" id="kategori"
                                        class="form-select @error('id_kategori') is-invalid @enderror" required>

                                        <option value="">-- Pilih Barang --</option>

                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                {{ old('id_kategori') == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->nama_barang }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('id_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>


                                {{-- TYPE --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Type</label>

                                    <input type="text" name="type"
                                        class="form-control @error('type') is-invalid @enderror"
                                        placeholder="Contoh: Ideapad Slim 1" value="{{ old('type') }}" required>

                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>


                                {{-- MEREK --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Merek</label>

                                    <input type="text" name="merek"
                                        class="form-control @error('merek') is-invalid @enderror" placeholder="Contoh: Asus"
                                        value="{{ old('merek') }}" required>

                                    @error('merek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Kondisi Asset
                                    </label>

                                    <select name="kondisi" class="form-select">

                                        <option value="Baru">
                                            Baru
                                        </option>

                                        <option value="Bekas">
                                            Bekas
                                        </option>

                                    </select>

                                </div>

                                {{-- JUMLAH --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Jumlah</label>

                                    <input type="number" name="jumlah"
                                        class="form-control @error('jumlah') is-invalid @enderror" placeholder="Contoh: 10"
                                        value="{{ old('jumlah') }}" required>

                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>


                                {{-- TANGGAL BELI --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Tanggal Beli</label>

                                    <input type="date" name="tgl_beli"
                                        class="form-control @error('tgl_beli') is-invalid @enderror"
                                        value="{{ old('tgl_beli') }}" required>

                                    @error('tgl_beli')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>


                                {{-- GARANSI --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Garansi (Bulan)</label>

                                    <input type="number" name="garansi"
                                        class="form-control @error('garansi') is-invalid @enderror" placeholder="Contoh: 12"
                                        value="{{ old('garansi') }}" required>

                                    @error('garansi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>


                                {{-- SUPPLIER --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Supplier</label>

                                    <input type="text" name="supplier"
                                        class="form-control @error('supplier') is-invalid @enderror"
                                        placeholder="Contoh: PT Sumber Jaya" value="{{ old('supplier') }}" required>

                                    @error('supplier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>


                                {{-- HARGA --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Harga</label>

                                    <input type="text" name="harga"
                                        class="form-control @error('harga') is-invalid @enderror"
                                        placeholder="Harga / satuan" value="{{ old('harga') }}" required>

                                    @error('harga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>



                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-3">

                                    <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary px-4">
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn-primary px-4">
                                        Simpan
                                    </button>

                                </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================
            // ELEMENT
            // =========================
            const perusahaanSelect = document.querySelector('[name="id_perusahaan"]');
            const kategoriSelect = document.getElementById('kategori');
            const kodeInput = document.getElementById('kode_masuk');



            // =========================
            // DEFAULT STATE (SUPER ADMIN)
            // =========================
            if (kategoriSelect && perusahaanSelect) {
                kategoriSelect.disabled = true;
                kategoriSelect.innerHTML = '<option value="">-- Pilih Perusahaan dulu --</option>';
            }

            // =========================
            // LOAD KODE MASUK
            // =========================
            function loadKode(id) {
                if (!id || !kodeInput) return;

                fetch('/dashboard/get-kode-masuk/' + id)
                    .then(res => res.json())
                    .then(data => {
                        kodeInput.value = data.kode;
                    })
                    .catch(err => console.log('Kode Error:', err));
            }

            // =========================
            // LOAD KATEGORI
            // =========================
            function loadKategori(id) {
                if (!id || !kategoriSelect) return;

                kategoriSelect.innerHTML = '<option>Loading...</option>';
                kategoriSelect.disabled = true;

                fetch('/dashboard/get-kategori/' + id)
                    .then(res => res.json())
                    .then(data => {

                        let html = '<option value="">-- Pilih Barang --</option>';

                        if (data.length === 0) {
                            html = '<option value="">Data barang kosong</option>';
                        } else {
                            data.forEach(item => {
                                html += `<option value="${item.id}">${item.nama_barang}</option>`;
                            });
                        }

                        kategoriSelect.innerHTML = html;

                        // 🔥 AKTIFKAN SELECT
                        kategoriSelect.disabled = false;

                    })
                    .catch(err => {
                        console.log('Kategori Error:', err);
                        kategoriSelect.innerHTML = '<option>Error load data</option>';
                        kategoriSelect.disabled = true;
                    });
            }

            // =========================
            // EVENT: CHANGE PERUSAHAAN
            // =========================
            if (perusahaanSelect) {

                perusahaanSelect.addEventListener('change', function() {

                    let id = this.value;

                    if (!id) {
                        kategoriSelect.innerHTML = '<option>-- Pilih Perusahaan dulu --</option>';
                        kategoriSelect.disabled = true;
                        return;
                    }

                    loadKode(id);
                    loadKategori(id);
                });

                // 🔥 AUTO LOAD JIKA SUDAH ADA VALUE (EDIT / OLD VALUE)
                if (perusahaanSelect.value) {
                    loadKode(perusahaanSelect.value);
                    loadKategori(perusahaanSelect.value);
                }
            }

        });
    </script>
@endsection
