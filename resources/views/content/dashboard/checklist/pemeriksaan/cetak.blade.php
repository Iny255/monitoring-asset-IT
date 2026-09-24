<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Checklist Device - {{ $ruangan->lokasi->nama_lokasi ?? 'Lokasi' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
            padding: 20px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-sm th, .table-sm td {
            padding: 6px 8px;
            font-size: 11px;
        }
        .badge-ok {
            background-color: #dcfce7;
            color: #166534;
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
        .badge-issue {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
        .signature-box {
            height: 70px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    {{-- TOMBOL PRINT (NO PRINT) --}}
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <strong>Lembar Hasil Pengecekan Device Mingguan</strong> &bull; Siap dicetak atau disimpan sebagai PDF.
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                🖨️ Cetak Dokumen / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm ms-1">
                Tutup
            </button>
        </div>
    </div>

    {{-- KOP DOKUMEN --}}
    <div class="text-center border-bottom pb-3 mb-3">
        <h4 class="fw-bold mb-0 text-uppercase">
            {{ $ruangan->perusahaan?->nama_perusahaan ?? 'PT SEMBILAN GROUP' }}
        </h4>
        <div class="header-title mt-1">LEMBAR HASIL CHECKLIST & PERAWATAN DEVICE (PREVENTIVE MAINTENANCE)</div>
        <small class="text-muted">Divisi IT Support & Infrastructure &bull; Sistem Monitoring Aset</small>
    </div>

    {{-- INFORMASI PENGECEKAN --}}
    <div class="row g-2 mb-3">
        <div class="col-6">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted fw-semibold" style="width: 140px;">Perusahaan:</td>
                    <td class="fw-bold">{{ $ruangan->perusahaan?->nama_perusahaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold" style="width: 140px;">Lokasi / Ruangan:</td>
                    <td class="fw-bold">{{ $ruangan->lokasi->nama_lokasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Jadwal Rutin:</td>
                    <td>
                        @if ($ruangan->jadwalRutin)
                            📅 Rutin Hari {{ ucfirst($ruangan->jadwalRutin->hari) }} (Mingguan)
                        @elseif ($ruangan->jadwal)
                            {{ $ruangan->jadwal->kode_jadwal }} ({{ $ruangan->jadwal->periode_label }})
                        @else
                            Jadwal Rutin Mingguan
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Hari & Tanggal:</td>
                    <td class="fw-semibold">
                        {{ $ruangan->nama_hari ?: ucfirst($ruangan->hari ?: 'Hari Rutin') }}, 
                        {{ $ruangan->tanggal_pemeriksaan ? $ruangan->tanggal_pemeriksaan->translatedFormat('d F Y') : ($ruangan->tanggal_cek ? $ruangan->tanggal_cek->translatedFormat('d F Y') : date('d F Y')) }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted fw-semibold" style="width: 140px;">Petugas IT:</td>
                    <td class="fw-bold">{{ $ruangan->petugas->name ?? ($ruangan->jadwalRutin->assignedTo->name ?? ($ruangan->jadwal->assignedTo->name ?? 'Belum Ditugaskan')) }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Tanggal Pemeriksaan:</td>
                    <td>{{ $ruangan->tanggal_cek ? $ruangan->tanggal_cek->format('d F Y, H:i') : now()->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Status Pengecekan:</td>
                    <td>
                        @if ($ruangan->kondisi_ruangan === 'ada_kendala')
                            <span class="badge-issue">ADA KENDALA</span>
                        @else
                            <span class="badge-ok">SEMUA NORMAL (OK)</span>
                        @endif
                        ({{ $ruangan->total_checked }}/{{ $ruangan->total_device }} Device Selesai)
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- TABEL DAFTAR DEVICE & HASIL CHECKLIST --}}
    <table class="table table-bordered table-sm align-middle mb-4">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 65px;">Bukti QR</th>
                <th style="width: 120px;">Kode Aset</th>
                <th>Nama / Tipe Device</th>
                <th style="width: 90px;">Kategori</th>
                <th>Pengguna Device</th>
                <th style="width: 105px;">Kondisi Akhir</th>
                <th style="width: 130px;">Waktu & Petugas</th>
                <th>Catatan / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ruangan->checklistDevices as $idx => $dev)
                @php
                    $inv = $dev->inventaris;
                    $dAset = $inv?->dataAset;
                    $jenisAset = $dAset?->kategori?->nama_barang ?? ($dAset?->kategori?->nama_kategori ?? 'Perangkat IT');
                    $spekAset = trim(($dAset?->merek ?? '') . ' ' . ($dAset?->type ?? '') . ' ' . ($dAset?->warna ?? ''));
                    $mapping = $dev->maping;
                    $qrUrl = $mapping ? route('maping.public_show', $mapping->uuid ?? $mapping->id) : null;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center py-1">
                        @if ($qrUrl)
                            <div style="width: 46px; height: 46px; margin: 0 auto;">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(46)->generate($qrUrl) !!}
                            </div>
                        @else
                            <span class="text-muted" style="font-size: 9px;">-</span>
                        @endif
                    <td class="font-monospace text-center">
                        {{ $inv->kode_aset ?? '-' }}
                        @if ($dev->is_pinjaman)
                            <div style="font-size: 8px; color: #0288d1; font-weight: bold;">[PINJAMAN]</div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $spekAset ?: ($jenisAset ?? '-') }}</td>
                    <td class="text-center">{{ $jenisAset }}</td>
                    <td>{{ $dev->nama_pengguna ?? '-' }}</td>
                    <td class="text-center">
                        @if ($dev->status_device === 'normal')
                            <span class="badge-ok">✔ NORMAL</span>
                        @elseif ($dev->status_device === 'ada_kendala')
                            <span class="badge-issue">✖ KENDALA</span>
                        @else
                            <span class="text-muted">Belum Dicek</span>
                        @endif
                    </td>
                    <td style="font-size: 10px;">
                        @if ($dev->checked_at)
                            <div class="fw-semibold">{{ $dev->checked_at->format('d/m/Y H:i') }}</div>
                            <small class="text-muted">{{ $dev->checkedBy?->name ?? ($ruangan->petugas?->name ?? '-') }}</small>
                        @else
                            <span class="text-muted">Belum diperiksa</span>
                        @endif
                    </td>
                    <td>{{ $dev->catatan_kendala ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-3 text-muted">
                        Tidak ada device terdaftar di ruangan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CATATAN TAMBAHAN --}}
    @if ($ruangan->jadwal && $ruangan->jadwal->catatan)
        <div class="p-2 border rounded bg-light mb-4 small">
            <strong>Instruksi / Catatan Jadwal:</strong> {{ $ruangan->jadwal->catatan }}
        </div>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="row text-center mt-5">
        <div class="col-6">
            <div class="text-muted small mb-1">Pemeriksaan Dilakukan Oleh,</div>
            <div class="fw-semibold">Petugas IT Support</div>
            <div class="signature-box"></div>
            <div class="fw-bold text-decoration-underline">
                {{ $ruangan->petugas->name ?? ($ruangan->jadwal->assignedTo->name ?? '( ................................ )') }}
            </div>
            <small class="text-muted">Staff / Teknisi IT</small>
        </div>
        <div class="col-6">
            <div class="text-muted small mb-1">Mengetahui,</div>
            <div class="fw-semibold">Penanggung Jawab Ruangan / User</div>
            <div class="signature-box"></div>
            <div class="fw-bold text-decoration-underline">( ........................................ )</div>
            <small class="text-muted">PIC Ruangan / User Aset</small>
        </div>
    </div>

</body>
</html>
