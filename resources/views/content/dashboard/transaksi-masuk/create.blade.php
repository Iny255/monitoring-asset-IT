@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Penerimaan Aset')

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

                        <h5 class="text-primary mb-0">Tambah Penerimaan Aset</h5>
                        <small class="text-muted">Silakan isi data penerimaan aset</small>

                    </div>


                    <div class="card-body px-4 pb-4 pt-3">

                        <form action="{{ route('transaksi-masuk.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-section-title">
                                Informasi Asset
                            </div>

                            <div class="row">


                                <div class="row">


                                    {{-- PERUSAHAAN --}}
                                    @if (auth()->user()->role === 'super_admin')
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-medium">
                                                Perusahaan
                                            </label>

                                            <select name="perusahaan_id" id="perusahaan"
                                                class="form-select @error('perusahaan_id') is-invalid @enderror" required>

                                                <option value="">
                                                    -- Pilih Perusahaan --
                                                </option>

                                                @foreach ($perusahaans as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('perusahaan_id') == $p->id ? 'selected' : '' }}>

                                                        {{ $p->nama_perusahaan }}

                                                    </option>
                                                @endforeach

                                            </select>

                                            @error('perusahaan_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    @endif


                                    {{-- SUPPLIER --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Supplier
                                        </label>

                                        <select name="supplier_id" id="supplier"
                                            class="form-select @error('supplier_id') is-invalid @enderror" required>

                                            <option value="">
                                                -- Pilih Supplier --
                                            </option>

                                            @if (auth()->user()->role !== 'super_admin')

                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">
                                                        {{ $supplier->nama_supplier }}
                                                    </option>
                                                @endforeach

                                            @endif

                                        </select>

                                        @error('supplier_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    {{-- DATA ASET --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Data Aset
                                        </label>

                                        <select name="data_aset_id" id="data_aset"
                                            class="form-select @error('data_aset_id') is-invalid @enderror" required>

                                            <option value="">
                                                -- Pilih Data Aset --
                                            </option>

                                            @if (auth()->user()->role !== 'super_admin')

                                                @foreach ($dataAsets as $aset)
                                                    <option value="{{ $aset->id }}">
                                                        {{ $aset->kategori->nama_barang }}
                                                        -
                                                        {{ $aset->merek }}
                                                        -
                                                        {{ $aset->type }}
                                                    </option>
                                                @endforeach

                                            @endif

                                        </select>

                                        @error('data_aset_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Tanggal Pembelian
                                        </label>

                                        <input type="date" name="tanggal_pembelian"
                                            class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                            value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>

                                        @error('tanggal_pembelian')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    {{-- JUMLAH --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Jumlah
                                        </label>

                                        <input type="number" name="jumlah" min="1"
                                            class="form-control @error('jumlah') is-invalid @enderror"
                                            value="{{ old('jumlah') }}" required>

                                        @error('jumlah')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    {{-- HARGA SATUAN --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Harga Satuan
                                        </label>

                                        <input type="number" name="harga_satuan" min="0"
                                            class="form-control @error('harga_satuan') is-invalid @enderror"
                                            value="{{ old('harga_satuan') }}" required>

                                        @error('harga_satuan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    {{-- GARANSI --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Garansi (Bulan)
                                        </label>

                                        <input type="number" name="garansi" min="0"
                                            class="form-control @error('garansi') is-invalid @enderror"
                                            value="{{ old('garansi') }}">

                                        @error('garansi')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    {{-- KETERANGAN PENERIMAAN --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-medium">
                                            Ket. Penerimaan
                                        </label>

                                        <select name="ket_penerimaan"
                                            class="form-select @error('ket_penerimaan') is-invalid @enderror" required>

                                            <option value="BAIK">
                                                BAIK
                                            </option>

                                            <option value="RUSAK">
                                                RUSAK
                                            </option>

                                        </select>

                                        @error('ket_penerimaan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    {{-- INFORMASI --}}
                                    <div class="col-md-12">

                                        <div class="alert alert-info">

                                            <strong>Informasi :</strong>

                                            Setelah disimpan sistem otomatis membuat:

                                            <ul class="mb-0 mt-2">

                                                <li>No Inventaris (INV-001 dst)</li>

                                                <li>Kode Aset (L.01-001 dst)</li>

                                                <li>Data Inventaris sesuai jumlah penerimaan</li>

                                            </ul>

                                        </div>

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

            const perusahaan = document.getElementById('perusahaan');
            const supplier = document.getElementById('supplier');
            const dataAset = document.getElementById('data_aset');

            if (!perusahaan) return;

            perusahaan.addEventListener('change', function() {

                let perusahaanId = this.value;

                // RESET
                supplier.innerHTML =
                    '<option value="">Loading Supplier...</option>';

                dataAset.innerHTML =
                    '<option value="">Loading Data Aset...</option>';

                if (!perusahaanId) {

                    supplier.innerHTML =
                        '<option value="">-- Pilih Supplier --</option>';

                    dataAset.innerHTML =
                        '<option value="">-- Pilih Data Aset --</option>';

                    return;
                }

                // ==========================
                // LOAD SUPPLIER
                // ==========================

                fetch('/dashboard/get-supplier/' + perusahaanId)

                    .then(response => response.json())

                    .then(data => {

                        let html =
                            '<option value="">-- Pilih Supplier --</option>';

                        if (data.length === 0) {

                            html =
                                '<option value="">Supplier tidak tersedia</option>';

                        } else {

                            data.forEach(item => {

                                html += `
                            <option value="${item.id}">
                                ${item.nama_supplier}
                            </option>
                        `;

                            });

                        }

                        supplier.innerHTML = html;

                    })

                    .catch(error => {

                        console.log(error);

                        supplier.innerHTML =
                            '<option value="">Gagal memuat supplier</option>';

                    });

                // ==========================
                // LOAD DATA ASET
                // ==========================

                fetch('/dashboard/get-data-aset/' + perusahaanId)

                    .then(response => response.json())

                    .then(data => {

                        let html =
                            '<option value="">-- Pilih Data Aset --</option>';

                        if (data.length === 0) {

                            html =
                                '<option value="">Data aset tidak tersedia</option>';

                        } else {

                            data.forEach(item => {

                                html += `
                            <option value="${item.id}">
                                ${item.kategori.nama_barang}
                                - ${item.merek}
                                - ${item.type}
                            </option>
                        `;

                            });

                        }

                        dataAset.innerHTML = html;

                    })

                    .catch(error => {

                        console.log(error);

                        dataAset.innerHTML =
                            '<option value="">Gagal memuat data aset</option>';

                    });

            });

        });
    </script>
@endsection
