<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan History Perjalanan Aset</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h2 {
            margin: 0 0 5px 0;
            color: #154b87;
            font-size: 18px;
            text-transform: uppercase;
        }

        .title p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead th {
            background: #154b87;
            color: white;
            border: 1px solid #154b87;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 6px;
        }

        tbody td {
            border: 1px solid #bfc7d1;
            padding: 7px 6px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            background: #154b87;
            border-radius: 3px;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
            color: #555;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; display: flex; justify-content: flex-end; gap: 8px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #154b87; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak PDF / Print
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Tutup
        </button>
    </div>

    @php
        $first = $inventaris->first();
        $penggunaTerakhir = null;
        foreach ($timeline->sortByDesc('tanggal') as $t) {
            if (!empty($t['user_baru']) && $t['user_baru'] !== 'Stok Gudang') {
                $penggunaTerakhir = $t['user_baru'];
                break;
            }
            if (!empty($t['user_lama']) && $t['user_lama'] !== 'Stok Gudang') {
                $penggunaTerakhir = $t['user_lama'];
                break;
            }
        }
    @endphp

    <div class="title">
        <h2>Laporan History Perjalanan Aset</h2>
        <p>
            <strong>Kode Aset: {{ $first->kode_aset ?? '-' }}</strong> • No. Inventaris: {{ $first->no_inventaris ?? '-' }} • {{ $first->dataAset->kategori->nama_barang ?? '-' }} • {{ $first->dataAset->merek ?? '-' }} {{ $first->dataAset->type ?? '' }}
            • {{ in_array($user->role, ['super_admin', '1', 1]) || !$user->id_perusahaan ? 'SEMBILAN GROUP' : strtoupper($first?->perusahaan?->nama_perusahaan ?? $user->perusahaan?->nama_perusahaan ?? 'PERUSAHAAN') }}
            @if($penggunaTerakhir)
                • Pengguna Terakhir: <strong>{{ $penggunaTerakhir }}</strong>
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="75">TANGGAL</th>
                <th width="110">KODE ASET</th>
                <th width="105">AKTIVITAS</th>
                <th>PENGGUNA TERAKHIR</th>
                <th>LOKASI & PERUSAHAAN</th>
                <th>HAK AKSES</th>
                <th>KETERANGAN</th>
                <th width="90">PETUGAS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timeline as $item)
                <tr>
                    <td class="text-center">{{ $item['tanggal'] instanceof \Carbon\Carbon ? $item['tanggal']->format('d-m-Y') : ($item['tanggal'] ? \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') : '-') }}</td>
                    <td>
                        @if ($item['aktivitas'] == 'MUTASI' && !empty($item['kode_aset_lama']) && $item['kode_aset_lama'] != $item['kode_aset_baru'])
                            {{ $item['kode_aset_lama'] }} &rarr; {{ $item['kode_aset_baru'] }}
                        @else
                            <strong>{{ $item['kode_aset'] }}</strong>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item['aktivitas'] == 'MUTASI')
                            <span class="badge" style="background: {{ !empty($item['is_antar_perusahaan']) ? '#0284c7' : '#d97706' }};">
                                {{ !empty($item['is_antar_perusahaan']) ? 'MUTASI ANTAR PT' : 'MUTASI INTERNAL' }}
                            </span>
                        @elseif($item['aktivitas'] == 'MASUK')
                            <span class="badge" style="background: #16a34a;">MASUK</span>
                        @elseif($item['aktivitas'] == 'KELUAR')
                            <span class="badge" style="background: #2563eb;">KELUAR</span>
                        @elseif($item['aktivitas'] == 'PENCABUTAN')
                            <span class="badge" style="background: #dc2626;">PENCABUTAN</span>
                        @elseif($item['aktivitas'] == 'MAINTENANCE')
                            <span class="badge" style="background: #0891b2;">SERVIS</span>
                        @elseif($item['aktivitas'] == 'HAK AKSES')
                            <span class="badge" style="background: #334155;">HAK AKSES</span>
                        @elseif($item['aktivitas'] == 'PEMINJAMAN')
                            <span class="badge" style="background: #0284c7;">PEMINJAMAN</span>
                        @elseif($item['aktivitas'] == 'PENGEMBALIAN PINJAMAN')
                            <span class="badge" style="background: #059669;">PENGEMBALIAN</span>
                        @else
                            <span class="badge" style="background: #475569;">{{ $item['aktivitas'] }}</span>
                        @endif
                    </td>
                    <td>
                        @if(in_array($item['aktivitas'] ?? '', ['PEMINJAMAN', 'PENGEMBALIAN PINJAMAN']))
                            <div>{{ $item['user_lama'] }} &rarr; <strong>{{ $item['user_baru'] }}</strong></div>
                        @else
                            <strong>{{ $item['user_baru'] ?? $item['user_lama'] ?? '-' }}</strong>
                        @endif
                    </td>
                    <td>
                        @if(($item['aktivitas'] ?? '') == 'MUTASI' && !empty($item['is_antar_perusahaan']))
                            <div><strong>{{ $item['perusahaan_asal'] }}</strong> &rarr; <strong>{{ $item['perusahaan_tujuan'] }}</strong></div>
                            @if(!empty($item['lokasi_lama']) || !empty($item['lokasi_baru']))
                                <small style="color: #64748b;">{{ $item['lokasi_lama'] ?? '-' }} &rarr; {{ $item['lokasi_baru'] ?? '-' }}</small>
                            @endif
                        @elseif(($item['aktivitas'] ?? '') == 'MUTASI')
                            <div><strong>{{ $item['perusahaan'] ?? '-' }}</strong></div>
                            @if(!empty($item['lokasi_lama']) || !empty($item['lokasi_baru']))
                                <small style="color: #64748b;">{{ $item['lokasi_lama'] ?? '-' }} &rarr; {{ $item['lokasi_baru'] ?? '-' }}</small>
                            @endif
                        @elseif(in_array(($item['aktivitas'] ?? ''), ['PEMINJAMAN', 'PENGEMBALIAN PINJAMAN']))
                            <div><strong>{{ $item['perusahaan'] ?? '-' }}</strong></div>
                            <small style="color: #64748b;">{{ $item['lokasi_lama'] ?? '-' }} &rarr; {{ $item['lokasi_baru'] ?? '-' }}</small>
                        @else
                            <div><strong>{{ $item['perusahaan'] ?? '-' }}</strong></div>
                            <small style="color: #64748b;">{{ $item['lokasi_baru'] ?? $item['lokasi_lama'] ?? '-' }}</small>
                        @endif
                    </td>
                    <td>{{ $item['hak_akses'] ?? '-' }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td class="text-center">{{ $item['petugas'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        Tidak ada riwayat perjalanan aset.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem Monitoring Aset pada {{ date('d-m-Y H:i:s') }}
    </div>

</body>
</html>
