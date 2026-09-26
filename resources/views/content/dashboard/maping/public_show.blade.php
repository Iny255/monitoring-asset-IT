<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Informasi Asset & Pengecekan Realtime - {{ $maping->keluar->inventaris->kode_aset ?? 'Perangkat' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5.3 & Boxicons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 12px 6px 36px;
            font-size: 13px;
            line-height: 1.5;
        }

        .asset-card {
            max-width: 900px;
            margin: auto;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .05);
            background: #fff;
        }

        .asset-header {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: white;
            padding: 16px 20px;
        }

        .asset-title {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.2px;
        }

        .asset-image {
            width: 100%;
            aspect-ratio: 3 / 4;
            max-height: 380px;
            object-fit: contain;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: block;
            margin: 0 auto;
        }

        .table th {
            width: 35%;
            background: #f8fafc;
            color: #475569;
            font-size: 11.5px;
            font-weight: 600;
            padding: 6px 10px;
            vertical-align: middle;
        }

        .table td {
            color: #0f172a;
            font-weight: 500;
            font-size: 12px;
            padding: 6px 10px;
            vertical-align: middle;
        }

        .section-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Checklist Status Box */
        .checklist-banner {
            border-radius: 12px;
            padding: 14px 16px;
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 18px;
        }

        .checklist-banner.status-normal {
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            border-left: 5px solid #10b981;
        }

        .checklist-banner.status-kendala {
            background: linear-gradient(135deg, #fef2f2, #fff1f2);
            border-left: 5px solid #ef4444;
        }

        .checklist-banner.status-belum {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-left: 5px solid #94a3b8;
        }

        .item-chip {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 14px;
            font-size: 11px;
            font-weight: 600;
            margin: 2px;
        }

        .item-chip.ok {
            background: #dcfce7;
            color: #15803d;
        }

        .item-chip.fail {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* IT Form Section */
        .it-check-box {
            background: #f8fafc;
            border: 1.5px dashed #93c5fd;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .btn-quick-ok {
            background: #10b981;
            color: white;
            font-weight: 600;
            font-size: 12px;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all .2s;
        }
        .btn-quick-ok:hover {
            background: #059669;
            color: white;
            transform: translateY(-1px);
        }

        .custom-check-pill {
            cursor: pointer;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 11.5px;
            transition: all .2s;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .custom-check-pill:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
        }
        .custom-check-pill input:checked ~ .check-label {
            color: #2563eb;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container py-2">

        <div class="card asset-card">

            {{-- HEADER PORTAL --}}
            <div class="asset-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-1.5 mb-1 flex-wrap">
                            <span class="badge px-2 py-0.5" style="background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35); font-size: 11px;">
                                <i class="bx bx-qr me-1"></i> QR Barcode Terpadu
                            </span>
                            @if ($maping->perusahaan)
                                <span class="badge px-2 py-0.5" style="background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35); font-size: 11px;">
                                    {{ strtoupper($maping->perusahaan->nama_perusahaan) }}
                                </span>
                            @endif
                        </div>
                        <h1 class="asset-title mb-0.5 text-white">
                            {{ strtoupper($maping->keluar->inventaris->dataAset->kategori->nama_barang ?? 'Perangkat IT') }}
                            <small class="fw-normal opacity-75" style="font-size: 12.5px;">({{ $maping->keluar->inventaris->dataAset->merek ?? '' }} {{ $maping->keluar->inventaris->dataAset->type ?? '' }})</small>
                        </h1>
                        <div class="text-white-50" style="font-size: 11.5px;">
                            Monitoring Asset & Real-Time Inspection System
                        </div>
                    </div>

                    <div class="text-md-end d-flex flex-column align-items-md-end gap-1.5">
                        @if (auth()->check())
                            <div class="d-flex align-items-center gap-1.5 flex-wrap justify-content-end">
                                @if (isset($checklistRuangan) && $checklistRuangan)
                                    <a href="{{ route('checklist.pemeriksaan.show', $checklistRuangan->id) }}" class="btn btn-sm btn-warning text-dark fw-bold shadow-xs py-1.5 px-3 d-inline-flex align-items-center" style="font-size: 12px; border-radius: 8px;">
                                        <i class="bx bx-arrow-back me-1 fs-6"></i> Kembali ke Ruangan ({{ $checklistRuangan->lokasi->nama_lokasi ?? 'Checklist' }})
                                    </a>
                                @else
                                    <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-sm btn-warning text-dark fw-bold shadow-xs py-1.5 px-3 d-inline-flex align-items-center" style="font-size: 12px; border-radius: 8px;">
                                        <i class="bx bx-arrow-back me-1 fs-6"></i> Kembali ke Menu Checklist
                                    </a>
                                @endif
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light text-primary fw-semibold shadow-xs py-1 px-2.5" style="font-size: 11.5px;">
                                    <i class="bx bx-home-alt me-1"></i> Dashboard
                                </a>
                            </div>
                        @else
                            <button type="button" onclick="window.history.back()" class="btn btn-sm btn-light text-primary fw-semibold shadow-xs py-1 px-2.5" style="font-size: 11.5px;">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Badges Identitas Ringkas --}}
                <div class="d-flex flex-wrap gap-2 mt-2 pt-2 border-top border-white border-opacity-25">
                    <span class="badge bg-light text-dark px-2.5 py-1" style="font-size: 12px;">
                        <i class="bx bx-barcode me-1 text-primary"></i> Kode Aset: <strong>{{ $maping->keluar->inventaris->kode_aset ?? '-' }}</strong>
                    </span>
                    @if ($maping->keluar?->inventaris?->no_inventaris)
                        <span class="badge bg-warning text-dark px-2.5 py-1" style="font-size: 12px;">
                            No. Inv: <strong>{{ $maping->keluar->inventaris->no_inventaris }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-3 p-sm-4">

                {{-- ALERT PESAN FEEDBACK --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bx bx-x-circle me-1 fs-5 align-middle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- BANNER INFORMASI STATUS PEMINJAMAN AKTIF --}}
                @if (isset($activeLoan) && $activeLoan)
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-3 mb-3 p-3" style="border-radius: 12px; background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #1e40af;">
                        <div class="rounded-circle bg-primary bg-opacity-20 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="bx bx-time-five text-primary fs-3"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge bg-primary text-white px-2 py-0.5" style="font-size: 11px;">
                                    <i class="bx bx-transfer-alt me-1"></i> STATUS: SEDANG DIPINJAM
                                </span>
                                <span class="text-muted small">No. Pinjam: <strong>{{ $activeLoan->kode_peminjaman ?? '-' }}</strong></span>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                Peminjam: {{ $activeLoan->peminjam_nama }}
                            </div>
                            <div class="text-secondary small mt-1 d-flex flex-wrap gap-x-3 gap-y-1">
                                <span><i class="bx bx-calendar me-1"></i>Tgl Pinjam: {{ $activeLoan->tanggal_pinjam ? \Carbon\Carbon::parse($activeLoan->tanggal_pinjam)->translatedFormat('d M Y') : '-' }}</span>
                                @if ($activeLoan->tanggal_rencana_kembali)
                                    <span><i class="bx bx-calendar-check me-1"></i>Rencana Kembali: {{ \Carbon\Carbon::parse($activeLoan->tanggal_rencana_kembali)->translatedFormat('d M Y') }}</span>
                                @endif
                                @if ($activeLoan->keperluan)
                                    <span><i class="bx bx-detail me-1"></i>Keperluan: {{ $activeLoan->keperluan }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ========================================================================= --}}
                {{-- 1. INFORMASI CHECKLIST & PENGECEKAN SECARA REALTIME --}}
                {{-- ========================================================================= --}}
                @php
                    $isNormal = $latestChecklist && $latestChecklist->status_device === 'normal';
                    $hasKendala = $latestChecklist && $latestChecklist->status_device === 'ada_kendala';
                    $statusClass = $isNormal ? 'status-normal' : ($hasKendala ? 'status-kendala' : 'status-belum');
                @endphp

                <div class="checklist-banner {{ $statusClass }}">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="text-uppercase fw-bold text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                <i class="bx bx-broadcast text-primary"></i> Status Pengecekan Device Real-Time
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @if ($isNormal)
                                    <h5 class="fw-bold text-success mb-0 d-flex align-items-center" style="font-size: 15.5px;">
                                        <i class="bx bxs-check-circle me-1" style="font-size: 20px;"></i> NORMAL (Kondisi Baik)
                                    </h5>
                                @elseif ($hasKendala)
                                    <h5 class="fw-bold text-danger mb-0 d-flex align-items-center" style="font-size: 15.5px;">
                                        <i class="bx bxs-error-circle me-1" style="font-size: 20px;"></i> ADA KENDALA
                                    </h5>
                                @else
                                    <h5 class="fw-bold text-secondary mb-0 d-flex align-items-center" style="font-size: 15.5px;">
                                        <i class="bx bx-time-five me-1" style="font-size: 20px;"></i> BELUM DICEK PERIODE INI
                                    </h5>
                                @endif
                            </div>
                        </div>

                        <div class="text-md-end">
                            @if ($latestChecklist && $latestChecklist->checked_at)
                                <div class="text-muted" style="font-size: 11px;">Pemeriksaan Terakhir:</div>
                                <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                    {{ $latestChecklist->checked_at->translatedFormat('d F Y, H:i') }} WIB
                                </div>
                                <div class="text-muted mt-1" style="font-size: 11px;">
                                    Oleh: <span class="badge bg-primary text-white fw-semibold px-2 py-0.5" style="font-size: 11px;">{{ $latestChecklist->checkedBy->name ?? 'Petugas IT' }}</span>
                                </div>
                            @else
                                <span class="badge bg-secondary" style="font-size: 11px;">Belum ada riwayat cek</span>
                            @endif
                        </div>
                    </div>

                    {{-- Catatan Kendala jika ada --}}
                    @if ($hasKendala && !empty($latestChecklist->catatan_kendala))
                        <div class="mt-3 p-3 bg-white rounded border border-danger border-opacity-25">
                            <strong class="text-danger d-block mb-1">
                                <i class="bx bx-message-error me-1"></i> Catatan Kendala dari Petugas IT:
                            </strong>
                            <p class="mb-0 text-dark small" style="white-space: pre-wrap;">{{ $latestChecklist->catatan_kendala }}</p>
                        </div>
                    @endif

                    {{-- Rincian Item Checklist Terakhir --}}
                    @if ($latestChecklist && $latestChecklist->items->isNotEmpty())
                        <div class="mt-3 pt-3 border-top border-secondary border-opacity-10">
                            <div class="small fw-bold text-muted mb-2">Hasil Kondisi Komponen yang Diperiksa:</div>
                            <div class="d-flex flex-wrap">
                                @foreach ($latestChecklist->items as $item)
                                    @if ($item->is_ok)
                                        <span class="item-chip ok" title="Kondisi Baik">
                                            <i class="bx bx-check me-1"></i> {{ $item->nama_item }}
                                        </span>
                                    @else
                                        <span class="item-chip fail" title="Ada Masalah">
                                            <i class="bx bx-x me-1"></i> {{ $item->nama_item }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Accordion Riwayat Pengecekan Terakhir --}}
                    @if ($checklistHistory->isNotEmpty())
                        <div class="mt-3 text-end">
                            <button class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory">
                                <i class="bx bx-history me-1"></i> Lihat 5 Riwayat Pengecekan Sebelumnya <i class="bx bx-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse mt-2" id="collapseHistory">
                            <div class="bg-white p-3 rounded border">
                                <h6 class="fw-bold mb-2 small text-muted text-uppercase">Log Riwayat Pemeriksaan</h6>
                                <div class="list-group list-group-flush small">
                                    @foreach ($checklistHistory as $h)
                                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <span class="fw-bold text-dark">{{ $h->checked_at ? $h->checked_at->format('d/m/Y H:i') : '-' }}</span>
                                                &bull; Oleh: <strong>{{ $h->checkedBy->name ?? 'Petugas IT' }}</strong>
                                                @if ($h->catatan_kendala)
                                                    <div class="text-danger small mt-1"><em>"{{ $h->catatan_kendala }}"</em></div>
                                                @endif
                                            </div>
                                            <div>
                                                @if ($h->status_device === 'normal')
                                                    <span class="badge bg-success">NORMAL</span>
                                                @elseif ($h->status_device === 'ada_kendala')
                                                    <span class="badge bg-danger">KENDALA</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $h->status_device }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ========================================================================= --}}
                {{-- 2. FORM PENGISIAN CHECKLIST PETUGAS IT (DARI SCAN BARCODE DI LAPANGAN) --}}
                {{-- ========================================================================= --}}
                @php
                    $isItOfficer = auth()->check() && in_array(auth()->user()->role, ['petugas', 'teknisi', 'super_admin', '1', '2']);

                    // Cek apakah hari ini perangkat ini sudah selesai diperiksa
                    $todayChecked = $todayChecklist && in_array($todayChecklist->status_device, ['normal', 'ada_kendala']);
                    if (!$todayChecked && $latestChecklist && in_array($latestChecklist->status_device, ['normal', 'ada_kendala']) && $latestChecklist->checked_at) {
                        $todayChecked = $latestChecklist->checked_at->isToday();
                    }
                    $activeTodayChecklist = $todayChecked ? ($todayChecklist ?: $latestChecklist) : null;
                @endphp

                @if ($isItOfficer)
                    {{-- Kartu Kunci Form Sekali Pakai (Jika sudah dicek hari ini) --}}
                    @if ($todayChecked)
                        <div class="card border-0 shadow-xs mb-3" id="lockedChecklistCard" 
                             style="background: linear-gradient(135deg, #f0fdf4, #f8fafc); border: 1px solid #bbf7d0 !important; border-left: 5px solid {{ $activeTodayChecklist->status_device === 'normal' ? '#10b981' : '#ef4444' }} !important; border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 38px; height: 38px; min-width: 38px; background: {{ $activeTodayChecklist->status_device === 'normal' ? '#10b981' : '#ef4444' }}; font-size: 18px;">
                                            <i class="bx {{ $activeTodayChecklist->status_device === 'normal' ? 'bx-check-double' : 'bx-error-circle' }}"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                                                <span class="badge bg-success text-white px-2 py-0.5" style="font-size: 10.5px;">
                                                    <i class="bx bx-lock-alt me-1"></i> Form Selesai Digunakan Hari Ini
                                                </span>
                                                @if ($activeTodayChecklist->status_device === 'normal')
                                                    <span class="badge bg-success px-2 py-0.5" style="font-size: 10.5px;">NORMAL (Kondisi Baik)</span>
                                                @else
                                                    <span class="badge bg-danger px-2 py-0.5" style="font-size: 10.5px;">ADA KENDALA</span>
                                                @endif
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0.5" style="font-size: 13px;">Pemeriksaan perangkat telah selesai disimpan hari ini</h6>
                                            <small class="text-muted" style="font-size: 11px;">
                                                Diperiksa oleh: <strong>{{ $activeTodayChecklist->checkedBy->name ?? 'Petugas IT' }}</strong> &bull; 
                                                Waktu: <strong>{{ $activeTodayChecklist->checked_at ? $activeTodayChecklist->checked_at->format('H:i') : '-' }} WIB</strong>
                                            </small>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center shadow-xs" id="btnBukaRevisi" style="font-size: 11.5px; padding: 4px 10px;">
                                            <i class="bx bx-edit-alt me-1" style="font-size: 14px;"></i> Buka Kunci / Revisi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Form Interaktif Petugas IT (Terkunci jika sudah dicek hari ini, terbuka jika belum atau saat revisi) --}}
                    <div class="it-check-box shadow-xs" id="itChecklistSection" style="{{ $todayChecked ? 'display: none;' : '' }}">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2.5">
                            <div>
                                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                                    <span class="badge bg-primary text-white px-2 py-0.5" style="font-size: 10.5px;">
                                        <i class="bx bx-shield-quarter me-1"></i> Mode Petugas IT Lapangan
                                    </span>
                                    @if ($todayChecked)
                                        <span class="badge bg-warning text-dark px-2 py-0.5" id="badgeModeRevisi" style="font-size: 10.5px;">
                                            <i class="bx bx-edit me-1"></i> Mode Revisi Pemeriksaan
                                        </span>
                                    @endif
                                </div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">Input Pengecekan Device (Real-Time)</h6>
                                <small class="text-muted" style="font-size: 11px;">Masuk sebagai: <strong>{{ auth()->user()->name }}</strong> &bull; Lokasi: <strong>{{ $maping->lokasi->nama_lokasi ?? '-' }}</strong></small>
                            </div>

                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                {{-- Tombol Kunci Kembali jika dalam mode revisi --}}
                                @if ($todayChecked)
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center shadow-xs" id="btnTutupRevisi" style="font-size: 11.5px; padding: 4px 10px;">
                                        <i class="bx bx-x me-1"></i> Kunci Kembali
                                    </button>
                                @endif

                                {{-- Tombol Cepat 1-Klik "Tandai Semua Normal" --}}
                                <form action="{{ route('maping.checklist.submit', $maping->uuid ?? $maping->id) }}" method="POST" id="formQuickNormal">
                                    @csrf
                                    <input type="hidden" name="status_device" value="normal">
                                    <button type="submit" class="btn btn-quick-ok d-inline-flex align-items-center shadow-sm">
                                        <i class="bx bx-check-double me-1" style="font-size: 15px;"></i> 1-Klik: Tandai Semua Normal
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Formulir Rinci Pengecekan Device --}}
                        <div class="card border-0 shadow-xs">
                            <div class="card-body p-3">
                                <form action="{{ route('maping.checklist.submit', $maping->uuid ?? $maping->id) }}" method="POST" id="formDetailedChecklist">
                                    @csrf

                                    {{-- Radio Status Perangkat --}}
                                    <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 11px;">Pilih Status Kondisi Perangkat:</label>
                                    <div class="d-flex gap-3 mb-2.5">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status_device" id="statusNormal" value="normal" 
                                                {{ (($activeTodayChecklist ?? $latestChecklist)?->status_device === 'normal' || !($activeTodayChecklist ?? $latestChecklist) || ($activeTodayChecklist ?? $latestChecklist)->status_device === 'belum_dicek') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-success" for="statusNormal" style="font-size: 12px;">
                                                <i class="bx bx-check-circle me-1"></i> NORMAL (Kondisi Baik)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status_device" id="statusKendala" value="ada_kendala"
                                                {{ ($activeTodayChecklist ?? $latestChecklist)?->status_device === 'ada_kendala' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger" for="statusKendala" style="font-size: 12px;">
                                                <i class="bx bx-error me-1"></i> ADA KENDALA
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Checklist Items Master --}}
                                    <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 11px;">Item Komponen Yang Diperiksa:</label>
                                    <div class="row g-2 mb-3">
                                        @forelse ($masterItems as $mItem)
                                            @php
                                                // Ambil status dari previous/today checklist jika ada
                                                $refChecklist = $activeTodayChecklist ?? $latestChecklist;
                                                $checkedItem = $refChecklist?->items->firstWhere('nama_item', $mItem->nama_item);
                                                $isItemOk = $checkedItem ? (bool)$checkedItem->is_ok : true;
                                            @endphp
                                            <div class="col-6 col-md-4">
                                                <label class="custom-check-pill w-100">
                                                    <span class="check-label">{{ $mItem->nama_item }}</span>
                                                    <input type="checkbox" name="items[{{ $mItem->id }}]" value="1" class="form-check-input ms-2" {{ $isItemOk ? 'checked' : '' }}>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="col-12 text-muted" style="font-size: 11px;">
                                                Item standar: Fisik, Layar, Keyboard/Mouse, Jaringan, OS & Software.
                                            </div>
                                        @endforelse
                                    </div>

                                    {{-- Catatan Kendala --}}
                                    <div class="mb-3" id="catatanKendalaContainer" style="{{ ($activeTodayChecklist ?? $latestChecklist)?->status_device === 'ada_kendala' ? '' : 'display: none;' }}">
                                        <label for="catatanKendala" class="form-label fw-bold text-danger text-uppercase mb-1" style="font-size: 11px;">
                                            <i class="bx bx-edit me-1"></i> Rincian Masalah / Catatan Kendala:
                                        </label>
                                        <textarea class="form-control" id="catatanKendala" name="catatan_kendala" rows="2" style="font-size: 12px;"
                                            placeholder="Contoh: Kipas pendingin bising, port USB samping rusak, dll.">{{ ($activeTodayChecklist ?? $latestChecklist)?->catatan_kendala }}</textarea>
                                    </div>

                                    {{-- Tombol Submit Form --}}
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-primary fw-semibold shadow-xs" id="btnSubmitChecklist" style="font-size: 12px; padding: 6px 16px;">
                                            <i class="bx bx-save me-1"></i> {{ $todayChecked ? 'Simpan Revisi Pengecekan' : 'Simpan Hasil Pengecekan' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Banner Ajakan Login untuk Petugas IT yang belum login --}}
                    <div class="card border-0 bg-light p-2.5 rounded-3 mb-3 d-flex flex-row align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-shield-quarter text-primary" style="font-size: 24px;"></i>
                            <div>
                                <strong class="text-dark d-block" style="font-size: 12.5px;">Petugas IT yang sedang bertugas?</strong>
                                <small class="text-muted" style="font-size: 11px;">Masuk ke sistem untuk mengisi atau memperbarui checklist perangkat ini langsung di lapangan.</small>
                            </div>
                        </div>
                        <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="btn btn-sm btn-primary fw-semibold px-2.5 py-1.5 shadow-xs" style="font-size: 11.5px;">
                            <i class="bx bx-log-in-circle me-1"></i> Login Petugas IT
                        </a>
                    </div>
                @endif

                {{-- ========================================================================= --}}
                {{-- 3. INFORMASI SPESIFIKASI & FOTO ASET --}}
                {{-- ========================================================================= --}}
                <div class="row g-4 align-items-start mt-1">

                    {{-- FOTO ASET (PORTRAIT VIEW) --}}
                    <div class="col-lg-4">
                        <div class="card shadow-xs border">
                            <div class="card-header bg-light py-2">
                                <strong class="small text-muted text-uppercase" style="font-size: 11px;">Foto Perangkat</strong>
                            </div>
                            <div class="card-body p-2.5 text-center">
                                @php
                                    $gambarPath = $maping->keluar?->gambar;
                                    $hasGambar = $gambarPath && (file_exists(public_path('storage/' . $gambarPath)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarPath));
                                @endphp

                                @if ($hasGambar)
                                    <a href="{{ asset('storage/' . $gambarPath) }}" target="_blank" title="Klik untuk memperbesar foto">
                                        <img src="{{ asset('storage/' . $gambarPath) }}" alt="Foto Asset" class="asset-image img-fluid">
                                    </a>
                                    <small class="text-muted d-block mt-1.5" style="font-size: 10.5px;">
                                        <i class="bx bx-zoom-in me-1"></i> Klik foto untuk memperbesar
                                    </small>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center bg-light rounded py-4 px-2 text-muted" style="aspect-ratio: 3 / 4; max-height: 380px; border: 2px dashed #cbd5e1;">
                                        <i class="bx bx-image-alt fs-1 text-secondary mb-1"></i>
                                        <span class="fw-semibold small">Tidak Ada Foto Perangkat</span>
                                        <small class="text-muted text-center" style="font-size: 11px;">Foto belum diunggah saat serah terima aset</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- IDENTITAS ASET --}}
                    <div class="col-lg-8">
                        <div class="section-title">
                            <i class="bx bx-info-circle text-primary"></i> Identitas Perangkat
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th>Kode Aset</th>
                                    <td><strong>{{ $maping->keluar->inventaris->kode_aset ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>No Inventaris</th>
                                    <td>{{ $maping->keluar->inventaris->no_inventaris ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori & Merek</th>
                                    <td>{{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }} - {{ $maping->keluar->inventaris->dataAset->merek ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Type & Model</th>
                                    <td>{{ $maping->keluar->inventaris->dataAset->type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Pengguna / User Aset</th>
                                    <td>
                                        @if (in_array($maping->jenis_penerima, ['Per Divisi', 'Perdivisi']) || $maping->jenis_penerima !== 'Perorangan')
                                            {{ strtoupper($maping->penerima ?? '-') }}
                                        @else
                                            {{ $maping->penerima ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lokasi Penempatan</th>
                                    <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Perusahaan</th>
                                    <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pembelian</th>
                                    <td>
                                        @php
                                            $tglPembelian = $maping->keluar?->inventaris?->masuk?->tanggal_pembelian;
                                        @endphp
                                        <strong>{{ $tglPembelian ? \Carbon\Carbon::parse($tglPembelian)->format('d-m-Y') : '-' }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Digunakan</th>
                                    <td>{{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d-m-Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status Asset</th>
                                    <td>
                                        @if ($maping->status == 'servis')
                                            <span class="badge bg-secondary" style="font-size: 11px; padding: 3px 8px;">SERVIS</span>
                                        @elseif($maping->status == 'dipinjam')
                                            <span class="badge bg-warning text-dark" style="font-size: 11px; padding: 3px 8px;">DIPINJAM</span>
                                        @elseif($maping->status == 'selesai')
                                            <span class="badge bg-danger" style="font-size: 11px; padding: 3px 8px;">NON AKTIF</span>
                                        @elseif($maping->status == 'maintenance')
                                            <span class="badge bg-info" style="font-size: 11px; padding: 3px 8px;">MAINTENANCE</span>
                                        @else
                                            <span class="badge bg-success" style="font-size: 11px; padding: 3px 8px;">AKTIF</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ========================================================================= --}}
                {{-- 4. SPESIFIKASI HARDWARE --}}
                {{-- ========================================================================= --}}
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="section-title">
                            <i class="bx bx-chip text-primary"></i> Spesifikasi Hardware
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th>Processor</th>
                                    <td>{{ $maping->processor ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>RAM</th>
                                    <td>{{ $maping->ram ? $maping->ram . ' GB' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Sistem Operasi</th>
                                    <td>{{ $maping->system ?? '-' }} {{ $maping->version ? '(' . $maping->version . ')' : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Device ID</th>
                                    <td><small class="font-monospace" style="font-size: 11px;">{{ $maping->device_id ?? '-' }}</small></td>
                                </tr>
                                <tr>
                                    <th>Produk ID</th>
                                    <td><small class="font-monospace" style="font-size: 11px;">{{ $maping->produk_id ?? '-' }}</small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                {{-- BOTTOM ACTION NAVIGATION / KEMBALI KE CHECKLIST RUANGAN --}}
                <div class="card border-0 shadow-xs mt-4" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        @if (auth()->check())
                            @if (isset($checklistRuangan) && $checklistRuangan)
                                <a href="{{ route('checklist.pemeriksaan.show', $checklistRuangan->id) }}" class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center shadow-xs">
                                    <i class="bx bx-arrow-back me-1.5 fs-5"></i> Kembali ke Ruangan Pelaksanaan Checklist ({{ $checklistRuangan->lokasi->nama_lokasi ?? 'Ruangan' }})
                                </a>
                            @else
                                <a href="{{ route('checklist.pemeriksaan.index') }}" class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center shadow-xs">
                                    <i class="bx bx-arrow-back me-1.5 fs-5"></i> Kembali ke Menu Checklist Ruangan
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="bx bx-home-alt me-1"></i> Buka Dashboard
                            </a>
                        @else
                            <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </button>
                        @endif
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="card-footer bg-light text-center py-2.5 text-muted" style="font-size: 11px;">
                &copy; {{ date('Y') }} Sembilan Group &bull; Sistem Monitoring & Checklist Device IT Real-Time
            </div>

        </div>

    </div>

    {{-- Script Interaksi --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle visibility catatan kendala
            const radioNormal = document.getElementById('statusNormal');
            const radioKendala = document.getElementById('statusKendala');
            const catatanContainer = document.getElementById('catatanKendalaContainer');
            const catatanInput = document.getElementById('catatanKendala');

            function toggleCatatan() {
                if (radioKendala && radioKendala.checked) {
                    catatanContainer.style.display = 'block';
                    if (catatanInput) catatanInput.focus();
                } else if (catatanContainer) {
                    catatanContainer.style.display = 'none';
                }
            }

            if (radioNormal) radioNormal.addEventListener('change', toggleCatatan);
            if (radioKendala) radioKendala.addEventListener('change', toggleCatatan);

            // Validasi sebelum submit kendala wajib mengisi catatan
            const formDetail = document.getElementById('formDetailedChecklist');
            if (formDetail) {
                formDetail.addEventListener('submit', function (e) {
                    if (radioKendala && radioKendala.checked && catatanInput && !catatanInput.value.trim()) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Perhatian', 'Silakan tuliskan catatan kendala atau masalah yang dialami perangkat ini.', 'warning');
                        } else {
                            alert('Silakan tuliskan catatan kendala atau masalah yang dialami perangkat ini.');
                        }
                        catatanInput.focus();
                    }
                });
            }

            // Logika Tombol Buka Kunci / Revisi Form Sekali Pakai
            const btnBukaRevisi = document.getElementById('btnBukaRevisi');
            const btnTutupRevisi = document.getElementById('btnTutupRevisi');
            const itChecklistSection = document.getElementById('itChecklistSection');
            const lockedChecklistCard = document.getElementById('lockedChecklistCard');

            function bukaFormRevisi() {
                if (itChecklistSection) {
                    itChecklistSection.style.display = 'block';
                    itChecklistSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                if (lockedChecklistCard) {
                    lockedChecklistCard.style.display = 'none';
                }
            }

            if (btnBukaRevisi) {
                btnBukaRevisi.addEventListener('click', function () {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Buka Form Revisi?',
                            text: 'Pemeriksaan hari ini sudah tersimpan. Buka kunci formulir untuk memperbarui atau merevisi hasil pengecekan?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#2563eb',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: '<i class="bx bx-edit me-1"></i> Ya, Buka Form Revisi',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                bukaFormRevisi();
                            }
                        });
                    } else {
                        if (confirm('Buka kunci formulir untuk merevisi hasil checklist hari ini?')) {
                            bukaFormRevisi();
                        }
                    }
                });
            }

            if (btnTutupRevisi) {
                btnTutupRevisi.addEventListener('click', function () {
                    if (itChecklistSection) itChecklistSection.style.display = 'none';
                    if (lockedChecklistCard) lockedChecklistCard.style.display = 'block';
                });
            }
        });
    </script>

</body>

</html>
