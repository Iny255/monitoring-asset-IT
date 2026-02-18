@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Transaksi Masuk')

@section('content')

<div class="container-fluid px-3">
    <div class="row">
        <div class="col-12">

            <!-- CARD -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-semibold mb-1">Tambah Transaksi Masuk</h5>
                    <small class="text-muted">Silakan isi data barang masuk</small>
                </div>

                <div class="card-body px-4 pb-4">

                    <form action="{{ route('transaksi-masuk.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <!-- KODE MASUK -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Kode Masuk</label>
                                <input type="text" name="kode_masuk" class="form-control" value="{{ $kodeMasuk }}" readonly>
                            </div>

                            <!-- NAMA BARANG -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Nama Barang</label>
                                <select name="id_kategori" class="form-select @error('id_kategori') is-invalid @enderror" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('id_kategori') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- TYPE -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Type</label>
                                <input type="text" name="type"
                                    class="form-control @error('type') is-invalid @enderror"
                                    placeholder="Contoh:Ideapad Slim 1" value="{{ old('type') }}" required>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- MEREK -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Merek</label>
                                <input type="text" name="merek"
                                    class="form-control @error('merek') is-invalid @enderror"
                                    placeholder="Contoh: Asus" value="{{ old('merek') }}" required>
                                @error('merek')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- JUMLAH -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Jumlah</label>
                                <input type="number" name="jumlah"
                                    class="form-control @error('jumlah') is-invalid @enderror"
                                    placeholder="Contoh: 10" value="{{ old('jumlah') }}" required>
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- TANGGAL BELI -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Tanggal Beli</label>
                                <input type="date" name="tgl_beli"
                                    class="form-control @error('tgl_beli') is-invalid @enderror"
                                    value="{{ old('tgl_beli') }}" required>
                                @error('tgl_beli')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                           <!-- GARANSI -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Garansi (Bulan)</label>
                                <input type="number" name="garansi"
                                    class="form-control @error('garansi') is-invalid @enderror"
                                    placeholder="Contoh: 2" value="{{ old('garansi') }}" required>
                                @error('garansi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- SUPPLIER -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Supplier</label>
                                <input type="text" name="supplier"
                                    class="form-control @error('supplier') is-invalid @enderror"
                                    placeholder="Contoh: PT Sumber Jaya" value="{{ old('supplier') }}" required>
                                @error('supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- HARGA -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Harga</label>
                                <input type="text" name="harga"
                                    class="form-control @error('harga') is-invalid @enderror"
                                    placeholder="harga/satuan" value="{{ old('harga') }}" required>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- GAMBAR -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Upload Gambar </label>
                                <input type="file" name="gambar"
                                    class="form-control @error('gambar') is-invalid @enderror"
                                    accept="image/*">
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- ACTION -->
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
            <!-- END CARD -->

        </div>
    </div>
</div>

@endsection
