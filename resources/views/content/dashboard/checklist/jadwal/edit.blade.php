@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Jadwal Checklist Device')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- BREADCRUMB --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <span class="text-muted fw-light">Checklist Device /</span> Edit Jadwal: {{ $jadwal->kode_jadwal }}
        </h5>
        <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-label-warning me-2">
                            <i class="bx bx-edit fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Ubah Jadwal Checklist Device</h6>
                            <small class="text-muted">
                                @if(in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                    Perusahaan: <strong>{{ $jadwal->perusahaan?->nama_perusahaan ?? '-' }}</strong> &bull;
                                @endif
                                Lokasi Device: <strong>{{ $jadwal->lokasi->nama_lokasi ?? '-' }}</strong> ({{ $jadwal->periode_label }})
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body py-4">
                    <form action="{{ route('checklist.jadwal.update', $jadwal->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Tanggal Mulai & Tanggal Selesai --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control" 
                                       value="{{ old('tanggal_mulai', $jadwal->tanggal_mulai->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control" 
                                       value="{{ old('tanggal_selesai', $jadwal->tanggal_selesai->format('Y-m-d')) }}" required>
                            </div>
                        </div>

                        {{-- Petugas IT & Status --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Petugas IT Penanggung Jawab</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Pilih Petugas IT --</option>
                                    @foreach ($petugasList as $p)
                                        <option value="{{ $p->id }}" {{ old('assigned_to', $jadwal->assigned_to) == $p->id ? 'selected' : '' }}>
                                            👤 {{ $p->name }} @if(auth()->user()->role === 'super_admin' && $p->perusahaan) ({{ $p->perusahaan->nama_perusahaan }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Status Jadwal <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="terjadwal" {{ old('status', $jadwal->status) == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                    <option value="berjalan" {{ old('status', $jadwal->status) == 'berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                                    <option value="selesai" {{ old('status', $jadwal->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="terlewat" {{ old('status', $jadwal->status) == 'terlewat' ? 'selected' : '' }}>Terlewat</option>
                                </select>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan / Instruksi Khusus</label>
                            <textarea name="catatan" rows="3" class="form-control">{{ old('catatan', $jadwal->catatan) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-check me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
