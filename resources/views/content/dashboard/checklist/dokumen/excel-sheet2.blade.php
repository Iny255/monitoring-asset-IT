<table>
    <thead>
        {{-- TITLE --}}
        <tr>
            <th colspan="8" style="font-size: 14pt; font-weight: bold; text-align: center; height: 35px; vertical-align: middle;">
                KARTU HISTORY DEVICE
            </th>
        </tr>

        {{-- ROW IDENTITAS --}}
        <tr>
            <th style="font-weight: bold; width: 50px;">Kategori Device</th>
            <th colspan="7" style="font-weight: bold;">
                : {{ $isLaptop ? '[√] Laptop' : '[ ] Laptop' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $isPrinter ? '[√] Printer' : '[ ] Printer' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $isHpTablet ? '[√] HP/Tablet' : '[ ] HP/Tablet' }} {{ $kategoriLain ? '[√] ' . $kategoriLain : '' }}
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Nama Device</th>
            <th colspan="7" style="font-weight: bold;">
                : {{ $namaDevice }} ({{ $kodeAset }})
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Nama Pengguna</th>
            <th colspan="7" style="font-weight: bold;">
                : {{ $namaPengguna }}
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Divisi</th>
            <th colspan="7" style="font-weight: bold;">
                : {{ $divisi }}
            </th>
        </tr>

        {{-- HEADER TABEL HISTORY --}}
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; width: 40px; background-color: #f1f5f9; vertical-align: middle;">NO</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; width: 110px; background-color: #f1f5f9; vertical-align: middle;">TANGGAL</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; width: 320px; background-color: #f1f5f9; vertical-align: middle;">TEMUAN DAN TROUBLESHOOT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; width: 320px; background-color: #f1f5f9; vertical-align: middle;">TINDAKAN PERBAIKAN</th>
            <th colspan="2" style="font-weight: bold; text-align: center; width: 80px; background-color: #f1f5f9; vertical-align: middle;">STATUS</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; width: 160px; background-color: #f1f5f9; vertical-align: middle;">DIPERIKSA OLEH,</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; width: 160px; background-color: #f1f5f9; vertical-align: middle;">MENGETAHUI,</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; width: 40px; background-color: #f1f5f9;">OK</th>
            <th style="font-weight: bold; text-align: center; width: 40px; background-color: #f1f5f9;">NG</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($historyLogs as $idx => $log)
            <tr>
                <td style="text-align: center; vertical-align: middle; height: 26px;">
                    {{ $log['tanggal'] ? ($idx + 1) : '' }}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {{ $log['tanggal'] ? $log['tanggal']->format('d-m-Y') : '' }}
                </td>
                <td style="vertical-align: middle;">
                    {{ $log['temuan'] }}
                </td>
                <td style="vertical-align: middle;">
                    {{ $log['tindakan'] }}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {{ $log['is_ok'] ? 'v' : '' }}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {{ $log['is_ng'] ? 'v' : '' }}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {{ $log['diperiksa_oleh'] }}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {{-- Dikosongkan khusus untuk Document Control sesuai instruksi user --}}
                    {{ $log['mengetahui'] }}
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="7"></td>
            <td style="font-style: italic; font-weight: bold; text-align: right; height: 30px; vertical-align: bottom;">
                F-IT-002/00
            </td>
        </tr>
    </tbody>
</table>
