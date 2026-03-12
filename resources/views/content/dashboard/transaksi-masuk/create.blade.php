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
    </style>


    <div class="container-fluid px-3">

        <div class="row">

            <div class="col-12">

                <div class="card card-dark shadow-sm">

                    <div class="card-header px-4 pt-4">

                        <h5 class="text-primary mb-0">Tambah Transaksi Masuk</h5>
                        <small class="text-muted">Silakan isi data barang masuk</small>

                    </div>


                    <div class="card-body px-4 pb-4">

                        <form action="{{ route('transaksi-masuk.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                {{-- KODE MASUK --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Kode Masuk</label>

                                    <input type="text" name="kode_masuk" class="form-control" value="{{ $kodeMasuk }}"
                                        readonly>

                                </div>


                                {{-- NAMA BARANG --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Nama Barang</label>

                                    <select name="id_kategori"
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


                                {{-- GAMBAR --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">Upload Gambar (Max 2MB)</label>

                                    <input type="file" name="gambar"
                                        class="form-control @error('gambar') is-invalid @enderror"
                                        accept="image/jpeg,image/png" onchange="validateFileSize(this)">

                                    <small class="text-muted">
                                        Format: JPG, JPEG, PNG. Maksimal 2MB
                                    </small>

                                    @error('gambar')
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


<script>
    function validateFileSize(input) {

        const file = input.files[0];

        if (file) {

            const maxSize = 2 * 1024 * 1024;

            if (file.size > maxSize) {

                alert("Ukuran gambar maksimal 2MB!");

                input.value = "";

            }

        }

    }
</script>
