@extends('layouts/contentNavbarLayout')

@section('title', 'Buat Tiket Helpdesk Baru')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('e-ticket.index') }}" class="btn btn-icon btn-label-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-0">Pengajuan Tiket Helpdesk Baru</h4>
                        <small class="text-muted">Isi formulir di bawah ini untuk melaporkan kendala IT</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i> Form Detail Tiket</h5>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('e-ticket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Kendala / Subjek Tiket <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" 
                                   placeholder="Contoh: Komputer Layar Blue Screen / Printer Rusak" value="{{ old('judul') }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kategori Tiket <span class="text-danger">*</span></label>
                                <select name="ticket_category_id" class="form-select @error('ticket_category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('ticket_category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nama_kategori }} (Target SLA: {{ $cat->sla_jam }} Jam)
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tingkat Prioritas <span class="text-danger">*</span></label>
                                <select name="prioritas" class="form-select @error('prioritas') is-invalid @enderror" required>
                                    <option value="low" {{ old('prioritas') == 'low' ? 'selected' : '' }}>Low (Rendah)</option>
                                    <option value="medium" {{ old('prioritas', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Sedang)</option>
                                    <option value="high" {{ old('prioritas') == 'high' ? 'selected' : '' }}>High (Tinggi)</option>
                                    <option value="urgent" {{ old('prioritas') == 'urgent' ? 'selected' : '' }}>Urgent (Darurat)</option>
                                </select>
                                @error('prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Perusahaan</label>
                                <select name="id_perusahaan" class="form-select">
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($perusahaans as $pt)
                                        <option value="{{ $pt->id }}" {{ old('id_perusahaan', auth()->user()->id_perusahaan) == $pt->id ? 'selected' : '' }}>
                                            {{ $pt->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lokasi Penempatan</label>
                                <select name="lokasi_id" class="form-select">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}" {{ old('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Karyawan / Pelapor</label>
                                <select name="karyawan_id" class="form-select">
                                    <option value="">-- Pilih Karyawan Pelapor --</option>
                                    @foreach ($karyawans as $kary)
                                        <option value="{{ $kary->id }}" {{ old('karyawan_id') == $kary->id ? 'selected' : '' }}>
                                            {{ $kary->nama_karyawan }} ({{ $kary->nik ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Aset / Perangkat Terkait (Opsional)</label>
                                <select name="inventaris_id" class="form-select">
                                    <option value="">-- Tidak Terhubung ke Aset Fisik --</option>
                                    @foreach ($inventarisList as $inv)
                                        <option value="{{ $inv->id }}" {{ old('inventaris_id') == $inv->id ? 'selected' : '' }}>
                                            [{{ $inv->kode_aset }}] {{ $inv->dataAset->kategori->nama_barang ?? '' }} - {{ $inv->perusahaan->nama_perusahaan ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih perangkat jika kendala berkaitan dengan aset tertentu</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Kendala Secara Detail <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="5"
                                      placeholder="Jelaskan secara detail kronologi kendala, pesan error yang muncul, atau bantuan yang dibutuhkan..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Lampiran Bukti Foto Error / Dokumen (Opsional)</label>
                            <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror">
                            <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX, ZIP (Maks: 5MB)</small>
                            @error('lampiran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('e-ticket.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Submit Tiket Helpdesk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
