<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="13" style="font-weight: bold; font-size: 14px; text-align: center;">
                    {{ strtoupper($title) }}
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; text-align: center;">NO</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">PERUSAHAAN</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KODE SERVICE</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KODE ASET</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">NO INVENTARIS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">NAMA BARANG</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">MEREK / TYPE</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">JENIS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">ASAL DATA</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">VENDOR / PENGELOLA</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">TANGGAL</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">BIAYA (RP)</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $index => $item)
                <tr>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->inventaris?->perusahaan?->nama_perusahaan ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->kode_service ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->inventaris?->kode_aset ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->inventaris?->no_inventaris ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->inventaris?->dataAset?->kategori?->nama_barang ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->inventaris?->dataAset?->merek ?? '-' }} {{ $item->inventaris?->dataAset?->type ?? '' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->jenis ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->asal ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->vendor ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->biaya ?? 0, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->status ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
