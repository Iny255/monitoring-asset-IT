<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tiket {{ $ticket->nomor_tiket }} - IT Helpdesk Support</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5.3 & Boxicons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #0b2f57;
            --primary-hover: #154b87;
        }

        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 24px 12px 60px;
        }

        .tracking-wrapper {
            max-width: 760px;
            margin: auto;
        }

        .success-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .success-banner {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            padding: 36px 24px;
            text-align: center;
        }

        .ticket-number-badge {
            display: inline-block;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            padding: 12px 28px;
            border-radius: 12px;
            font-family: monospace;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 1px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 14.5px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
        }

        .info-value {
            font-weight: 600;
            color: #0f172a;
            text-align: right;
        }

        .btn-whatsapp {
            background: #25d366;
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .btn-whatsapp:hover {
            background: #1eb855;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="tracking-wrapper">

        <div class="success-card">

            {{-- SUCCESS BANNER --}}
            <div class="success-banner">
                <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 64px; height: 64px;">
                    <i class="bx bx-check fs-1 fw-bold"></i>
                </div>
                <h3 class="fw-bold mb-1">Tiket Berhasil Diajukan!</h3>
                <p class="mb-0 text-white-50">Laporan kendala Anda telah diterima oleh Tim IT Helpdesk.</p>
            </div>

            <div class="card-body p-4 p-md-5">

                {{-- TICKET NUMBER --}}
                <div class="text-center mb-4">
                    <small class="text-muted d-block mb-2 text-uppercase fw-semibold">Nomor Tiket Anda</small>
                    <div class="ticket-number-badge mb-2">
                        {{ $ticket->nomor_tiket }}
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="copyTicketNumber('{{ $ticket->nomor_tiket }}')">
                            <i class="bx bx-copy me-1"></i> Salin Nomor Tiket
                        </button>
                    </div>
                </div>

                {{-- STATUS BOX --}}
                <div class="alert alert-light border rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-time-five fs-4 text-warning"></i>
                        <div>
                            <div class="fw-semibold">Status Tiket Saat Ini</div>
                            <small class="text-muted">Target SLA Respon: {{ $ticket->category->sla_jam ?? 24 }} Jam</small>
                        </div>
                    </div>
                    <div>
                        @switch($ticket->status)
                            @case('open')
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i class="bx bx-hourglass-top me-1"></i> Open (Menunggu IT)</span>
                                @break
                            @case('in_progress')
                                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill"><i class="bx bx-wrench me-1"></i> Sedang Ditangani</span>
                                @break
                            @case('pending')
                                <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill"><i class="bx bx-pause-circle me-1"></i> Pending</span>
                                @break
                            @case('resolved')
                                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="bx bx-check-circle me-1"></i> Selesai (Resolved)</span>
                                @break
                            @case('closed')
                                <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill"><i class="bx bx-lock me-1"></i> Ditutup (Closed)</span>
                                @break
                            @default
                                <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill">{{ strtoupper($ticket->status) }}</span>
                        @endswitch
                    </div>
                </div>

                {{-- DETAIL INFORMASI TIKET --}}
                <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-detail me-1"></i> Rincian Laporan</h6>
                <div class="bg-light rounded-4 p-3 mb-4">
                    <div class="info-row">
                        <span class="info-label">Nama Pelapor</span>
                        <span class="info-value">{{ $ticket->karyawan->nama_karyawan ?? $ticket->nama_pelapor ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Perusahaan & Divisi</span>
                        <span class="info-value">{{ $ticket->perusahaan->nama_perusahaan ?? '-' }} ({{ $ticket->karyawan->divisi ?? '-' }})</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nomor WhatsApp</span>
                        <span class="info-value">{{ $ticket->kontak_pelapor ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kategori Kendala</span>
                        <span class="info-value"><span class="badge bg-label-info">{{ $ticket->category->nama_kategori ?? '-' }}</span></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Perangkat Terkait</span>
                        <span class="info-value">
                            @if ($ticket->inventaris)
                                <span class="badge bg-primary">[{{ $ticket->inventaris->kode_aset }}]</span>
                                {{ $ticket->inventaris->dataAset->kategori->nama_barang ?? '' }} - {{ $ticket->inventaris->dataAset->merek ?? '' }} {{ $ticket->inventaris->dataAset->type ?? '' }}
                            @else
                                <span class="text-muted">Kendala Non-Perangkat Fisik</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Subjek Kendala</span>
                        <span class="info-value text-dark">{{ $ticket->judul }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Waktu Pengajuan</span>
                        <span class="info-value">{{ $ticket->created_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                </div>

                {{-- DESKRIPSI KENDALA --}}
                <h6 class="fw-bold mb-2 text-primary"><i class="bx bx-comment-detail me-1"></i> Deskripsi Masalah</h6>
                <div class="p-3 border rounded-3 bg-white mb-4">
                    <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $ticket->deskripsi }}</p>
                </div>

                {{-- ACTION BUTTONS --}}
                @php
                    $waText = urlencode("Halo Tim IT Support, saya " . ($ticket->karyawan->nama_karyawan ?? $ticket->nama_pelapor) . " ingin konfirmasi tiket kendala IT nomor: " . $ticket->nomor_tiket . " dengan subjek: " . $ticket->judul);
                @endphp

                <div class="d-grid gap-2">
                    <a href="https://wa.me/?text={{ $waText }}" target="_blank" class="btn btn-whatsapp d-flex align-items-center justify-content-center gap-2">
                        <i class="bx bxl-whatsapp fs-4"></i>
                        <span>Konfirmasi / Hubungi Tim IT via WhatsApp</span>
                    </a>
                    <a href="{{ route('public.ticket.create') }}" class="btn btn-outline-secondary rounded-3 py-2">
                        <i class="bx bx-plus-circle me-1"></i> Buat Laporan Tiket Lainnya
                    </a>
                </div>

            </div>

        </div>

        <div class="text-center text-muted small mt-4">
            Simpan nomor tiket Anda untuk memantau status penanganan oleh Tim IT.
        </div>

    </div>

    <script>
        function copyTicketNumber(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: `Nomor tiket ${text} berhasil disalin ke clipboard.`,
                    timer: 1800,
                    showConfirmButton: false
                });
            });
        }
    </script>

</body>

</html>
