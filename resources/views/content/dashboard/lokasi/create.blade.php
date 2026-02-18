@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Lokasi Barang')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 col-md-8">

            <!-- CARD -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-semibold mb-1">Tambah Lokasi Barang</h5>
                    <small class="text-muted">
                        Silakan isi data lokasi barang
                    </small>
                </div>

                <div class="card-body px-4 pb-4">

                    <form action="{{ route('lokasi.store') }}" method="POST">
                        @csrf

                        <!-- KODE LOKASI -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                Kode Lokasi
                            </label>
                            <input type="text"
                                   name="kode_lokasi"
                                   class="form-control"
                                   value="{{ $kodeLokasi }}"
                                   readonly>
                        </div>

                        <!-- NAMA LOKASI -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Nama Lokasi
                            </label>
                            <input type="text"
                                   name="nama_lokasi"
                                   class="form-control @error('nama_lokasi') is-invalid @enderror"
                                   placeholder="Contoh: Admin 1"
                                   value="{{ old('nama_lokasi') }}"
                                   required>

                            @error('nama_lokasi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- ACTION -->
                         <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('lokasi.index') }}" class="btn btn-secondary px-4">
                                Batal
                            </a>
                        <div class="d-flex justify-content-end">
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
