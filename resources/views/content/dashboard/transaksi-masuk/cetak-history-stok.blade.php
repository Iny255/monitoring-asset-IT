<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        .title {
            text-align: center;
            margin-bottom: 12px;
        }

        .title h2 {
            margin: 0;
            padding: 0;
        }

        .small {
            font-size: 10px;
            color: #555;
        }

        .info {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .info table {
            width: 100%;
            border: none;
        }

        .info td {
            border: none;
            padding: 2px 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #154b87;
            color: white;
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }

        td {
            border: 1px solid #999;
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>

</head>

<body>

    @php
        $asset = $inventaris->first();
    @endphp

    <div class="title">

        <h2>LAPORAN HISTORY PERJALANAN ASET</h2>

        <div class="small">
            Monitoring Asset System
        </div>

        <div class="small">
            Tanggal Cetak :
            {{ now()->format('d-m-Y H:i') }}
        </div>

    </div>

    {{-- INFORMASI ASSET --}}
    <div class="info">

        <table>

            <tr>

                <td width="18%"><strong>Nama Asset</strong></td>

                <td width="2%">:</td>

                <td>{{ $asset->dataAset->kategori->nama_barang ?? '-' }}</td>

            </tr>

            <tr>

                <td><strong>Merk</strong></td>

                <td>:</td>

                <td>{{ $asset->dataAset->merek ?? '-' }}</td>

            </tr>

            <tr>

                <td><strong>Type</strong></td>

                <td>:</td>

                <td>{{ $asset->dataAset->type ?? '-' }}</td>

            </tr>

            <tr>

                <td><strong>Total Unit</strong></td>

                <td>:</td>

                <td>{{ $inventaris->count() }}</td>

            </tr>

        </table>

    </div>

    {{-- TIMELINE --}}
    <table>

        <thead>

            <tr>

                <th width="5%">No</th>

                <th width="12%">Tanggal</th>

                <th width="10%">Aktivitas</th>

                <th width="15%">Kode Asset</th>

                <th width="15%">Inventaris</th>

                <th width="15%">User</th>

                <th width="12%">Lokasi</th>

                <th>Keterangan</th>

                <th width="12%">Petugas</th>

            </tr>

        </thead>


        <tbody>

            @forelse($timeline as $i => $item)
                <tr>

                    <td class="center">
                        {{ $i + 1 }}
                    </td>

                    <td class="center">
                        {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}
                    </td>

                    <td class="center">
                        {{ $item['aktivitas'] }}
                    </td>

                    <td>
                        {{ $item['kode_aset'] }}
                    </td>

                    <td>
                        {{ $item['inventaris'] }}
                    </td>

                    {{-- USER --}}
                    <td>

                        @if ($item['aktivitas'] == 'MUTASI')
                            <strong>{{ $item['user_lama'] }}</strong>

                            <br>

                            ↓

                            <br>

                            <strong>{{ $item['user_baru'] }}</strong>
                        @elseif ($item['aktivitas'] == 'KELUAR')
                            {{ $item['user_baru'] ?? '-' }}
                        @elseif ($item['aktivitas'] == 'PENCABUTAN')
                            {{ $item['user_lama'] ?? '-' }}
                        @else
                            -
                        @endif

                    </td>

                    {{-- LOKASI --}}
                    <td>

                        @if ($item['aktivitas'] == 'MUTASI')
                            {{ $item['lokasi_lama'] }}

                            <br>

                            ↓

                            <br>

                            {{ $item['lokasi_baru'] }}
                        @elseif ($item['aktivitas'] == 'KELUAR')
                            {{ $item['lokasi_baru'] ?? '-' }}
                        @elseif ($item['aktivitas'] == 'PENCABUTAN')
                            {{ $item['lokasi_baru'] ?? '-' }}
                        @else
                            -
                        @endif

                    </td>

                    {{-- KETERANGAN --}}
                    <td>

                        {{ $item['keterangan'] }}

                        @if ($item['aktivitas'] == 'MUTASI')
                            <br>

                            <small>
                                Hak Akses :
                                {{ strtoupper($item['hak_akses']) }}
                            </small>
                        @endif

                    </td>

                    <td>

                        {{ $item['petugas'] ?? '-' }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9" class="center">
                        Tidak ada riwayat asset.
                    </td>

                </tr>
            @endforelse
            <script>
                window.onload = function() {
                    window.print();
                }
            </script>

        </tbody>


    </table>

    <div class="footer">

        Dicetak oleh :

        <strong>{{ auth()->user()->name }}</strong>

        <br>

        {{ now()->format('d-m-Y H:i') }}

    </div>

</body>

</html>
