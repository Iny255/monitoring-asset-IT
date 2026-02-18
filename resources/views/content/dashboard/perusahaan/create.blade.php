@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Perusahaan')

@section('content')

<div class="container-fluid">
    <div class="row ">
        <div class="col-lg-7 col-md-9">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Tambah Data Perusahaan</h5>
                    <small class="text-muted">Silakan isi data dengan benar</small>
                </div>

                <div class="card-body px-4 py-4">
                    <form action="{{ route('perusahaan.store') }}" method="POST">
                        @csrf

                        <!-- KODE PERUSAHAAN -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                Kode Perusahaan
                            </label>
                            <input type="text"
                                   name="kode_perusahaan"
                                   class="form-control"
                                   value="{{ $kodePerusahaan }}"
                                   readonly>
                                </div>

                        <!-- NAMA PERUSAHAAN -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama Perusahaan</label>
                            <input type="text"
                                   name="nama_perusahaan"
                                   class="form-control form-control-lg @error('nama_perusahaan') is-invalid @enderror"
                                   placeholder="Nama perusahaan"
                                   value="{{ old('nama_perusahaan') }}">

                            @error('nama_perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- BUTTON -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('perusahaan.index') }}" class="btn btn-secondary px-4">
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
