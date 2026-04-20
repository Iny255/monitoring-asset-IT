@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Karyawan')

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- FORM SISI KIRI -->
        <div class="col-lg-6 col-md-8 ms-2">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Edit Data Karyawan</h5>
                    <small class="text-muted">Perbarui informasi karyawan</small>
                </div>

                <div class="card-body px-4 py-4">
                    <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- KODE KARYAWAN -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Kode Karyawan</label>
                            <input type="text"
                                   name="kode_karyawan"
                                   class="form-control form-control-lg @error('kode_karyawan') is-invalid @enderror"
                                   value="{{ old('kode_karyawan', $karyawan->kode_karyawan) }}"
                                    placeholder="Kode karyawan">
                                
                            @error('kode_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama Karyawan</label>
                            <input type="text"
                                   name="nama_karyawan"
                                   class="form-control form-control-lg @error('nama_karyawan') is-invalid @enderror"
                                   value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}"
                                   placeholder="Nama lengkap">
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
                                   value="{{ old('jabatan', $karyawan->jabatan) }}"
                                   placeholder="Staff IT {{ isset($perusahaan) ? $perusahaan->nama_perusahaan : '' }}">
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
                                   value="{{ old('divisi', $karyawan->divisi) }}"
                                   placeholder="IT Support">
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
                                   value="{{ old('perusahaan', $karyawan->perusahaan) }}"
                                   placeholder="PT Contoh Sejahtera">
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
                                Update
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
