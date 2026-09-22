@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Jadwal Rutin Mingguan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- BREADCRUMB --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                <i class="bx bx-home-alt me-1"></i> Dashboard
            </a>
            <span class="text-muted">/</span>
            <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Jadwal
            </a>
        </div>
        <h5 class="fw-bold mb-0 d-none d-sm-block">
            <span class="text-muted fw-light">Edit Jadwal /</span> {{ $jadwal->lokasi->nama_lokasi ?? '-' }}
        </h5>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
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
                            <h6 class="fw-bold mb-0">Ubah Jadwal Rutin Mingguan</h6>
                            <small class="text-muted">
                                @if(in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                    Perusahaan: <strong>{{ $jadwal->perusahaan?->nama_perusahaan ?? '-' }}</strong> &bull;
                                @endif
                                Lokasi: <strong>{{ $jadwal->lokasi->nama_lokasi ?? '-' }}</strong> &bull;
                                Hari: <strong>{{ ucfirst($jadwal->hari) }}</strong>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body py-4">
                    <form action="{{ route('checklist.jadwal.update', $jadwal->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            {{-- Hari --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Hari Rutin <span class="text-danger">*</span></label>
                                <select name="hari" class="form-select" required>
                                    @foreach (['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $hVal => $hLabel)
                                        <option value="{{ $hVal }}" {{ old('hari', $jadwal->hari) == $hVal ? 'selected' : '' }}>
                                            📅 {{ $hLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text small">Pemeriksaan ruangan ini akan selalu dijalankan pada hari ini setiap minggunya.</div>
                            </div>

                            {{-- Ruangan / Lokasi --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Lokasi / Ruangan <span class="text-danger">*</span></label>
                                <select name="id_lokasi" class="form-select" required>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}" {{ old('id_lokasi', $jadwal->id_lokasi) == $lok->id ? 'selected' : '' }}>
                                            🚪 {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Petugas IT & Status Aktif --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Petugas IT Penanggung Jawab</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Belum Ditugaskan --</option>
                                    @foreach ($petugasList as $p)
                                        <option value="{{ $p->id }}" {{ old('assigned_to', $jadwal->assigned_to) == $p->id ? 'selected' : '' }}>
                                            👤 {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Status Jadwal Rutin <span class="text-danger">*</span></label>
                                <select name="is_active" class="form-select" required>
                                    <option value="1" {{ old('is_active', $jadwal->is_active) ? 'selected' : '' }}>🟢 Aktif (Berjalan Setiap Minggu)</option>
                                    <option value="0" {{ !old('is_active', $jadwal->is_active) ? 'selected' : '' }}>⚪ Nonaktif (Ditangguhkan Sementara)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Jam Mulai & Jam Selesai --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" 
                                       value="{{ old('jam_mulai', $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '08:00') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" 
                                       value="{{ old('jam_selesai', $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '17:00') }}">
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan / Instruksi Khusus</label>
                            <textarea name="catatan" rows="3" class="form-control" 
                                      placeholder="Instruksi khusus pengecekan di ruangan ini...">{{ old('catatan', $jadwal->catatan) }}</textarea>
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
