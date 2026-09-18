<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Unit History Perjalanan Aset</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="font-weight: bold; font-size: 14px; text-align: center;">
                    LAPORAN DAFTAR UNIT & STATUS PERJALANAN ASET
                </th>
            </tr>
            <tr>
                <th colspan="7" style="font-size: 11px; text-align: center; color: #555555;">
                    TANGGAL EXPORT: {{ date('d-m-Y H:i') }}
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; text-align: center;">NO</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">PERUSAHAAN</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KODE ASET</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">NO INVENTARIS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KATEGORI & MEREK</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">STATUS SAAT INI</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">PEMAKAI SAAT INI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarisList as $index => $inv)
                <tr>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000;">{{ $inv->perusahaan?->nama_perusahaan ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $inv->kode_aset ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $inv->no_inventaris ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $inv->dataAset?->kategori?->nama_barang ?? '-' }} - {{ $inv->dataAset?->merek ?? '-' }} {{ $inv->dataAset?->type ?? '' }}</td>
                    <td style="border: 1px solid #000000;">{{ $inv->status ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">
                        @if($inv->status == 'DIPAKAI' && $inv->keluarTerakhir)
                            @php
                                $maping = $inv->keluarTerakhir->maping;
                                if ($maping && $maping->status == 'aktif') {
                                    $namaPemakai = $maping->jenis_penerima == 'Perorangan' ? ($maping->karyawan?->nama_karyawan ?? '-') : ($maping->divisi ?? '-');
                                } else {
                                    $namaPemakai = $inv->keluarTerakhir->jenis_penerima == 'Perorangan' ? ($inv->keluarTerakhir->karyawan?->nama_karyawan ?? '-') : ($inv->keluarTerakhir->divisi_klr ?? '-');
                                }
                            @endphp
                            {{ $namaPemakai }}
                        @elseif($inv->status == 'DIPINJAM' && $inv->peminjamanTerakhir)
                            {{ $inv->peminjamanTerakhir->karyawan?->nama_karyawan ?? ($inv->peminjamanTerakhir->karyawanTujuan?->nama_karyawan ?? '-') }}
                        @else
                            Belum dipakai
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
