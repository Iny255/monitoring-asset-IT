<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan History Hak Akses</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="10" style="font-weight: bold; font-size: 14px; text-align: center;">
                    LAPORAN RIWAYAT / HISTORY HAK AKSES & APLIKASI
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; text-align: center;">NO</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">PERUSAHAAN</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KODE ASET</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">NO INVENTARIS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">KATEGORI BARANG</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">USER ASSET</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">NAMA HAK AKSES / APLIKASI</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">EMAIL / AKUN</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">JENIS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">AKTIVITAS</th>
                <th style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">TANGGAL & WAKTU</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $index => $history)
                <tr>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->maping?->perusahaan?->nama_perusahaan ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->maping?->keluar?->inventaris?->kode_aset ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->maping?->keluar?->inventaris?->no_inventaris ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->maping?->keluar?->inventaris?->dataAset?->kategori?->nama_barang ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->maping?->keluar?->karyawan?->nama_karyawan ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->access?->nama_akses ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->email ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->access?->jenis ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->aksi ?? '-' }}</td>
                    <td style="border: 1px solid #000000;">{{ $history->created_at ? $history->created_at->format('d-m-Y H:i') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
