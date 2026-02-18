@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Karyawan')

@section('content')

<div class="container-fluid">
    <div class="row ">
        <div class="col-lg-7 col-md-9">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Tambah Data Karyawan</h5>
                    <small class="text-muted">Silakan isi data dengan benar</small>
                </div>

                <div class="card-body px-4 py-4">
                    <form action="{{ route('karyawan.store') }}" method="POST">
                        @csrf

                        <!-- KODE KARYAWAN -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Kode Karyawan</label>
                            <input type="text"
                                   name="kode_karyawan"
                                   class="form-control form-control-lg @error('kode_karyawan') is-invalid @enderror"
                                   placeholder="Masukkan IUD Karyawan"
                                   value="{{ old('kode_karyawan') }}">

                            @error('kode_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NAMA KARYAWAN -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama Karyawan</label>
                            <input type="text"
                                   name="nama_karyawan"
                                   class="form-control form-control-lg @error('nama_karyawan') is-invalid @enderror"
                                   placeholder="Nama lengkap"
                                   value="{{ old('nama_karyawan') }}">

                            @error('nama_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- JABATAN -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Jabatan</label>
                            <input type="text"
                                   name="jabatan"
                                   class="form-control form-control-lg @error('jabatan') is-invalid @enderror"
                                   placeholder="Contoh: Staff IT"
                                   value="{{ old('jabatan') }}">

                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- DIVISI -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Divisi</label>
                            <input type="text"
                                   name="divisi"
                                   class="form-control form-control-lg @error('divisi') is-invalid @enderror"
                                   placeholder="Contoh: IT"
                                   value="{{ old('divisi') }}">

                            @error('divisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PERUSAHAAN -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Perusahaan</label>
                            <input type="text"
                                   name="perusahaan"
                                   class="form-control form-control-lg @error('perusahaan') is-invalid @enderror"
                                   placeholder="Nama perusahaan"
                                   value="{{ old('perusahaan') }}">

                            @error('perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('karyawan.index') }}" class="btn btn-secondary px-4">
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
