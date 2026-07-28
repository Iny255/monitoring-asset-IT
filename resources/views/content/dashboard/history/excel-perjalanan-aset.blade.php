<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan History Perjalanan Aset</title>
</head>
<body>
    @php
        $first = $inventaris->first();
    @endphp
    <table>
        <thead>
            <tr>
                <th colspan="8" style="font-weight: bold; font-size: 14px; text-align: center;">
                    LAPORAN RIWAYAT PERJALANAN UNIT ASET (TIMELINE)
                </th>
            </tr>
            <tr>
                <th colspan="8" style="font-size: 11px; text-align: center;">
                    KODE ASET: {{ $first->kode_aset ?? '-' }} | NO INVENTARIS: {{ $first->no_inventaris ?? '-' }} | KATEGORI: {{ $first->dataAset->kategori->nama_barang ?? '-' }} | MEREK: {{ $first->dataAset->merek ?? '-' }} {{ $first->dataAset->type ?? '' }} | PERUSAHAAN: {{ $first->perusahaan->nama_perusahaan ?? '-' }}
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; text-align: center;">NO</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">TANGGAL</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">AKTIVITAS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">USER BARU</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">LOKASI BARU</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">HAK AKSES</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KETERANGAN</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">PETUGAS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timeline as $index => $item)
                <tr>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['tanggal'] ? $item['tanggal']->format('d-m-Y') : '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['aktivitas'] }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['user_baru'] ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['lokasi_baru'] ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['hak_akses'] ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['keterangan'] ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item['petugas'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
