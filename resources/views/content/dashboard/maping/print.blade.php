<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Maping</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        td.center {
            text-align: center;
        }
    </style>
</head>

<body onload="window.print()">

    <h3 style="margin-bottom:5px;">
        DATA SPESIFIKASI DAN MAPING FILE DIVISI IT
    </h3>

    <h4 style="text-align:center; margin-top:0;">
        {{ $namaPerusahaan ?? 'SEMBILAN GROUP' }}
    </h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Karyawan</th>
                <th>Lokasi</th>
                <th>Nama Barang</th>
                <th>Type</th>
                <th>Merek</th>
                <th>Warna</th>
                <th>Garansi</th>
                <th>No Inventaris</th>
                <th>Tanggal Beli</th>
                <th>Aplikasi</th>
                <th>Data PPN</th>
                <th>Data NON</th>
            </tr>
        </thead>

        <tbody>
            @forelse($mapings as $i => $m)
            <tr>
                <td class="center">{{ $i + 1 }}</td>

                <td>{{ $m->keluar->kode_barang ?: '-' }}</td>
                <td>
                    {{ $m->keluar->karyawan->nama_karyawan ?? '-' }}
                </td>
                <td>{{ $m->lokasi->nama_lokasi ?? '-' }}</td>
                <td>
                    {{ $m->keluar->masuk->kategori->nama_barang ?? '-' }}
                </td>
                <td>{{ $m->keluar->masuk->type?? '-' }}</td>
                <td>{{ $m->keluar->masuk->merek?? '-' }}</td>
                <td>{{ $m->keluar->warna?? '-' }}</td>
                <td>{{ $m->keluar->masuk->garansi?? '-' }} Bulan</td>
                <td>{{ $m->keluar->no_inventaris?? '-' }}</td>
                <td>{{ $m->keluar->masuk->tgl_beli?? '-' }}</td>
                <td>{{ $m->aplikasi ?: '-' }}</td>
                <td>{{ $m->data_p ?: '-' }}</td>
                <td>{{ $m->data_n ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>