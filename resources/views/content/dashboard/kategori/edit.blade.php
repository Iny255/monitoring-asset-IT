@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Kategori Barang')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 col-md-8">


            <!-- CARD -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-semibold mb-1">Edit Kategori Barang</h5>
                    <small class="text-muted">
                        Perbarui data kategori barang
                    </small>
                </div>

                <div class="card-body px-4 pb-4">

                    <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- KODE BARANG -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                Kode Barang
                            </label>
                            <input type="text"
                                class="form-control"
                                value="{{ $kategori->kode_barang }}"
                                readonly>
                        </div>

                        <!-- NAMA BARANG -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Nama Barang
                            </label>
                            <input type="text"
                                name="nama_barang"
                                class="form-control @error('nama_barang') is-invalid @enderror"
                                value="{{ old('nama_barang', $kategori->nama_barang) }}"
                                required>

                            @error('nama_barang')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- ACTION -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kategori.index') }}" class="btn btn-secondary px-4">
                                Batal
                            </a>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    Update
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