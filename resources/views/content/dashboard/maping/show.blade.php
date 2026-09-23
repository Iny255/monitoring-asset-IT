@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Data Mapping')

@section('content')

    <div class="container-fluid" style="margin-top:35px;">


        {{-- ============================= --}}
        {{-- HEADER --}}
        {{-- ============================= --}}
        <div class="card border-0 shadow-sm mb-4 mt-4">

            {{-- HEADER --}}
            <div class="card-header bg-white border-bottom py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>

                        <h3 class="fw-bold text-dark mb-1">

                            <i class="bx bx-desktop text-primary me-2"></i>

                            Detail Mapping Asset

                        </h3>

                        <span class="text-muted">

                            {{ $maping->keluar->inventaris->kode_aset ?? '-' }}

                            •

                            {{ strtoupper($maping->keluar->inventaris->dataAset->merek ?? '-') }}

                            {{ strtoupper($maping->keluar->inventaris->dataAset->type ?? '-') }}

                        </span>

                    </div>

                    <div>

                        <span class="badge bg-label-primary px-3 py-2">

                            {{ strtoupper($maping->perusahaan->nama_perusahaan ?? '-') }}

                        </span>

                    </div>

                </div>

            </div>

            {{-- SUMMARY --}}
            <div class="card-body">

                <div class="row g-3">

                    {{-- ====================== --}}
                    {{-- KODE ASET --}}
                    {{-- ====================== --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div class="avatar avatar-md bg-label-primary me-3">

                                        <i class="bx bx-barcode fs-3"></i>

                                    </div>

                                    <div>

                                        <small class="text-muted">

                                            Kode Asset

                                        </small>

                                        <h5 class="fw-bold mb-0">

                                            {{ $maping->keluar->inventaris->kode_aset ?? '-' }}

                                        </h5>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ====================== --}}
                    {{-- NO INVENTARIS --}}
                    {{-- ====================== --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div class="avatar avatar-md bg-label-success me-3">

                                        <i class="bx bx-package fs-3"></i>

                                    </div>

                                    <div>

                                        <small class="text-muted">

                                            No Inventaris

                                        </small>

                                        <h5 class="fw-bold mb-0">

                                            {{ $maping->keluar->inventaris->no_inventaris ?? '-' }}

                                        </h5>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ====================== --}}
                    {{-- USER --}}
                    {{-- ====================== --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div class="avatar avatar-md bg-label-info me-3">

                                        <i class="bx bx-user fs-3"></i>

                                    </div>

                                    <div>

                                        <small class="text-muted">

                                            User Asset

                                        </small>

                                        <h6 class="fw-bold mb-0">

                                            {{ strtoupper($maping->penerima ?? '-') }}

                                        </h6>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ====================== --}}
                    {{-- STATUS --}}
                    {{-- ====================== --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div class="avatar avatar-md bg-label-warning me-3">

                                        <i class="bx bx-check-circle fs-3"></i>

                                    </div>

                                    <div>

                                        <small class="text-muted">

                                            Status Mapping

                                        </small>

                                        <br>

                                        @switch($maping->status)
                                            @case('servis')
                                                <span class="badge bg-secondary">

                                                    SERVIS

                                                </span>
                                            @break

                                            @case('dipinjam')
                                                <span class="badge bg-warning text-dark">

                                                    DIPINJAM

                                                </span>
                                            @break

                                            @case('selesai')
                                                <span class="badge bg-danger">

                                                    NON AKTIF

                                                </span>
                                            @break

                                            @case('maintenance')
                                                <span class="badge bg-info">

                                                    MAINTENANCE

                                                </span>
                                            @break

                                            @case('mutasi')
                                                <span class="badge bg-primary">

                                                    MUTASI

                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-success">

                                                    AKTIF

                                                </span>
                                        @endswitch

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="row g-4">

            {{-- ========================= --}}
            {{-- INFORMASI & SPESIFIKASI --}}
            {{-- ========================= --}}
            <div class="col-lg-8">

                {{-- INFORMASI MAPPING --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white">

                        <h5 class="mb-0 fw-bold">

                            <i class="bx bx-info-circle text-primary me-2"></i>

                            Informasi Asset

                        </h5>

                    </div>

                    <div class="card-body p-0">

                        <table class="table table-hover table-bordered mb-0">

                            <tbody>

                                <tr>
                                    <th width="35%">Perusahaan</th>
                                    <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Kode Asset</th>
                                    <td>{{ $maping->keluar->inventaris->kode_aset ?? '-' }}</td>
                                </tr>
        

                                <tr>
                                    <th>No Inventaris</th>
                                    <td>{{ $maping->keluar->inventaris->no_inventaris ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Merk</th>
                                    <td>{{ $maping->keluar->inventaris->dataAset->merek ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Type</th>
                                    <td>{{ $maping->keluar->inventaris->dataAset->type ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Warna</th>
                                    <td>{{ $maping->keluar->inventaris->dataAset->warna ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>User Asset</th>
                                    <td>
                                        @if (in_array($maping->jenis_penerima, ['Per Divisi', 'Perdivisi']) || $maping->jenis_penerima !== 'Perorangan')
                                            {{ strtoupper($maping->penerima ?? '-') }}
                                        @else
                                            {{ $maping->penerima ?? '-' }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Tanggal Digunakan</th>
                                    <td>

                                        {{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d-m-Y') : '-' }}

                                    </td>
                                </tr>

                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $maping->catatan ?: '-' }}</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- SPESIFIKASI --}}
                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white">

                        <h5 class="mb-0 fw-bold">

                            <i class="bx bx-chip text-success me-2"></i>

                            Spesifikasi Perangkat

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row g-4">

                            <div class="col-md-6">

                                <small class="text-muted">

                                    Processor

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->processor ?? '-' }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted">

                                    RAM

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->ram ?? '-' }} GB

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted">

                                    Operating System

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->system ?? '-' }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted">

                                    Version

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->version ?? '-' }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted">

                                    Device ID

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->device_id ?? '-' }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted">

                                    Product ID

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->produk_id ?? '-' }}

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted">

                                    Install On

                                </small>

                                <div class="fw-semibold">

                                    {{ $maping->instal_on ? \Carbon\Carbon::parse($maping->instal_on)->format('d-m-Y') : '-' }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- FOTO + QR --}}
            {{-- ========================= --}}
            <div class="col-lg-4">

                {{-- FOTO --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white">

                        <h5 class="mb-0 fw-bold">

                            <i class="bx bx-image me-2 text-primary"></i>

                            Foto Asset

                        </h5>

                    </div>

                    <div class="card-body text-center p-3">
                        @php
                            $gambarPath = $maping->keluar?->gambar;
                            $hasGambar = $gambarPath && (file_exists(public_path('storage/' . $gambarPath)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarPath));
                        @endphp

                        @if ($hasGambar)
                            <img src="{{ asset('storage/' . $gambarPath) }}"
                                class="img-fluid rounded border shadow-sm" style="max-height:260px;object-fit:contain;">
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center bg-light rounded py-4 px-3 text-muted" style="min-height:180px; border: 2px dashed #cbd5e1;">
                                <i class="bx bx-image-alt fs-1 text-secondary mb-2"></i>
                                <span class="fw-semibold small">Tidak Ada Foto Perangkat</span>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- QR BARCODE TERPADU --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bx bx-qr text-success me-2"></i>
                            QR Code Mapping Terpadu
                        </h5>
                        <span class="badge bg-label-primary font-monospace">Realtime</span>
                    </div>

                    <div class="card-body text-center">
                        <div id="qr-code" class="p-2 bg-light rounded d-inline-block shadow-xs">
                            {!! QrCode::size(200)->generate(route('maping.public_show', $maping->uuid ?? $maping->id)) !!}
                        </div>

                        <div class="mt-3">
                            <h5 class="fw-bold mb-1">
                                {{ $maping->keluar->inventaris->kode_aset ?? '-' }}
                            </h5>
                            <p class="text-muted small mb-0">
                                Scan QR ini untuk melihat spek aset dan status pengecekan secara realtime, atau untuk mengisi checklist langsung oleh Petugas IT.
                            </p>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-primary" onclick="downloadQR()">
                                <i class="bx bx-download me-1"></i> Download QR Stiker
                            </button>
                            <a href="{{ route('maping.public_show', $maping->uuid ?? $maping->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-link-external me-1"></i> Buka Tampilan Hasil Scan
                            </a>
                        </div>
                    </div>
                </div>

                {{-- STATUS CHECKLIST REALTIME --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bx bx-calendar-check text-primary me-2"></i>
                            Status Pengecekan Device
                        </h5>
                        @if ($latestChecklist)
                            @if ($latestChecklist->status_device === 'normal')
                                <span class="badge bg-success">NORMAL</span>
                            @elseif ($latestChecklist->status_device === 'ada_kendala')
                                <span class="badge bg-danger">KENDALA</span>
                            @else
                                <span class="badge bg-secondary">BELUM DICEK</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">BELUM ADA</span>
                        @endif
                    </div>

                    <div class="card-body">
                        @if ($latestChecklist)
                            <div class="mb-3">
                                <div class="text-muted small">Pemeriksaan Terakhir:</div>
                                <div class="fw-bold text-dark">
                                    {{ $latestChecklist->checked_at ? $latestChecklist->checked_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum selesai diperiksa' }}
                                </div>
                                <div class="small text-muted mt-1">
                                    Petugas: <strong class="text-primary">{{ $latestChecklist->checkedBy->name ?? 'Petugas IT' }}</strong>
                                </div>
                            </div>

                            @if ($latestChecklist->status_device === 'ada_kendala' && $latestChecklist->catatan_kendala)
                                <div class="alert alert-danger py-2 px-3 small mb-3">
                                    <strong>Catatan Kendala:</strong><br>
                                    {{ $latestChecklist->catatan_kendala }}
                                </div>
                            @endif

                            @if ($latestChecklist->items->isNotEmpty())
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">Kondisi Komponen:</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($latestChecklist->items as $item)
                                            <span class="badge {{ $item->is_ok ? 'bg-label-success' : 'bg-label-danger' }}" style="font-size: 11px;">
                                                {{ $item->is_ok ? '✔' : '✖' }} {{ $item->nama_item }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-3 text-muted small">
                                <i class="bx bx-info-circle fs-3 text-secondary d-block mb-1"></i>
                                Perangkat ini belum memiliki riwayat pengecekan checklist.
                            </div>
                        @endif

                        <div class="d-grid gap-2 mt-3 pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalChecklistMapping">
                                <i class="bx bx-check-square me-1"></i> Input / Perbarui Checklist
                            </button>
                            @if ($latestChecklist && $latestChecklist->checklist_ruangan_id)
                                <a href="{{ route('checklist.pemeriksaan.show', $latestChecklist->checklist_ruangan_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-door-open me-1"></i> Buka Sesi Checklist Ruangan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
        {{-- ========================= --}}
        {{-- HAK AKSES & APLIKASI --}}
        {{-- ========================= --}}
        <div class="card border-0 shadow-sm mt-4">

            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

                <h5 class="mb-0 fw-bold">

                    <i class="bx bx-shield-quarter text-primary me-2"></i>

                    Hak Akses & Aplikasi

                </h5>

                @php
                    $registeredEmails = $maping->mapingAccesses->pluck('email')->filter()->unique();
                @endphp
                @if($registeredEmails->isNotEmpty())
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="text-muted small me-1">Email Terdaftar:</span>
                        @foreach($registeredEmails as $em)
                            <span class="badge bg-label-primary font-monospace">
                                <i class="bx bx-envelope me-1"></i>{{ $em }}
                            </span>
                        @endforeach
                    </div>
                @endif

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="60" class="text-center">NO</th>

                                <th>NAMA HAK AKSES / APLIKASI</th>

                                <th>EMAIL TERDAFTAR</th>

                                <th width="160" class="text-center">KATEGORI</th>

                                <th width="160" class="text-center">JENIS</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($maping->mapingAccesses as $item)

                                <tr>

                                    <td class="text-center fw-medium">{{ $loop->iteration }}</td>

                                    <td>

                                        <span class="fw-semibold text-dark">{{ $item->nama_akses }}</span>

                                    </td>

                                    <td>
                                        @if (!empty($item->email))
                                            <span class="fw-semibold text-dark font-monospace">
                                                <i class="bx bx-envelope text-primary me-1"></i>{{ $item->email }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">

                                        @if ($item->kategori == 'Aplikasi')

                                            <span class="badge bg-label-primary px-3 py-2">

                                                <i class="bx bx-grid-alt me-1"></i> Aplikasi

                                            </span>

                                        @else

                                            <span class="badge bg-label-success px-3 py-2">

                                                <i class="bx bx-key me-1"></i> Hak Akses

                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-center">

                                        @switch($item->jenis)

                                            @case('Software')

                                                <span class="badge bg-label-info px-3 py-2">

                                                    Software

                                                </span>

                                            @break

                                            @case('PPN')

                                                <span class="badge bg-label-warning px-3 py-2">

                                                    PPN

                                                </span>

                                            @break

                                            @default

                                                <span class="badge bg-label-secondary px-3 py-2">

                                                    NON PPN

                                                </span>

                                        @endswitch

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center text-muted py-4">

                                        <i class="bx bx-info-circle fs-4 d-block mb-1"></i>

                                        Belum ada Hak Akses maupun Aplikasi.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


            {{-- BUTTON --}}
            <div class="d-flex justify-content-between mt-4">

                <a href="{{ route('maping.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i>
                    Kembali
                </a>

                <div class="d-flex gap-2">
                    @if ($maping->status != 'aktif')
                        <form action="{{ route('maping.reactivate', $maping->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali mapping dan unit aset ini?')">
                                <i class="bx bx-check-circle me-1"></i>
                                Aktifkan Kembali
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('maping.edit', $maping->id) }}" class="btn btn-warning">
                        <i class="bx bx-edit"></i>
                        Edit Data
                    </a>
                </div>

            </div>


        </div>

        <script>
            function downloadQR() {

                const svg = document.querySelector('#qr-code svg');

                const serializer = new XMLSerializer();

                const source = serializer.serializeToString(svg);

                const image = new Image();

                image.src =
                    'data:image/svg+xml;base64,' +
                    btoa(unescape(encodeURIComponent(source)));

                image.onload = function() {

                    const canvas = document.createElement('canvas');

                    canvas.width = image.width;

                    canvas.height = image.height;

                    const ctx = canvas.getContext('2d');

                    ctx.drawImage(image, 0, 0);

                    const pngFile = canvas.toDataURL('image/png');

                    const downloadLink = document.createElement('a');

                    downloadLink.download =
                        'QR-{{ $maping->keluar->inventaris->kode_aset ?? 'asset' }}.png';

                    downloadLink.href = pngFile;

                    downloadLink.click();
                };
            }

            // Handler Modal Checklist
            document.addEventListener('DOMContentLoaded', function() {
                const rNormal = document.getElementById('modalStatusNormal');
                const rKendala = document.getElementById('modalStatusKendala');
                const boxKendala = document.getElementById('modalCatatanKendalaBox');

                function toggleModalCatatan() {
                    if (rKendala && rKendala.checked) {
                        boxKendala.style.display = 'block';
                    } else if (boxKendala) {
                        boxKendala.style.display = 'none';
                    }
                }

                if (rNormal) rNormal.addEventListener('change', toggleModalCatatan);
                if (rKendala) rKendala.addEventListener('change', toggleModalCatatan);
            });
        </script>

        {{-- MODAL INPUT CHECKLIST DEVICE LANGSUNG DARI MAPPING --}}
        <div class="modal fade" id="modalChecklistMapping" tabindex="-1" aria-labelledby="modalChecklistMappingLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('maping.checklist.submit', $maping->uuid ?? $maping->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold" id="modalChecklistMappingLabel">
                                <i class="bx bx-check-square text-primary me-1"></i> Input Checklist Device
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="small text-muted">Perangkat:</div>
                                <div class="fw-bold text-dark fs-6">{{ $maping->keluar->inventaris->kode_aset ?? '-' }} ({{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? 'Perangkat' }})</div>
                                <div class="small text-muted">User: {{ (in_array($maping->jenis_penerima, ['Per Divisi', 'Perdivisi']) || $maping->jenis_penerima !== 'Perorangan') ? strtoupper($maping->penerima ?? '-') : ($maping->penerima ?? '-') }} &bull; Lokasi: {{ $maping->lokasi->nama_lokasi ?? '-' }}</div>
                            </div>

                            {{-- Pilihan Status --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Status Kondisi Fisik:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status_device" id="modalStatusNormal" value="normal"
                                            {{ ($latestChecklist?->status_device === 'normal' || !$latestChecklist || $latestChecklist->status_device === 'belum_dicek') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-success" for="modalStatusNormal">
                                            <i class="bx bx-check-circle me-1"></i> Normal (Baik)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status_device" id="modalStatusKendala" value="ada_kendala"
                                            {{ $latestChecklist?->status_device === 'ada_kendala' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-danger" for="modalStatusKendala">
                                            <i class="bx bx-error me-1"></i> Ada Kendala
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Checkbox Items --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Item Yang Diperiksa:</label>
                                <div class="row g-2">
                                    @forelse ($masterItems as $mItem)
                                        @php
                                            $checkedItem = $latestChecklist?->items->firstWhere('nama_item', $mItem->nama_item);
                                            $isItemOk = $checkedItem ? (bool)$checkedItem->is_ok : true;
                                        @endphp
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="items[{{ $mItem->id }}]" value="1" id="mItem{{ $mItem->id }}" {{ $isItemOk ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="mItem{{ $mItem->id }}">
                                                    {{ $mItem->nama_item }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted small">Master item checklist belum diset.</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Catatan Kendala --}}
                            <div class="mb-3" id="modalCatatanKendalaBox" style="{{ $latestChecklist?->status_device === 'ada_kendala' ? '' : 'display: none;' }}">
                                <label for="modalCatatan" class="form-label fw-bold small text-danger text-uppercase">Catatan Kendala / Masalah:</label>
                                <textarea class="form-control" id="modalCatatan" name="catatan_kendala" rows="2" placeholder="Jelaskan masalah pada perangkat ini...">{{ $latestChecklist?->catatan_kendala }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                <i class="bx bx-save me-1"></i> Simpan Pengecekan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection
