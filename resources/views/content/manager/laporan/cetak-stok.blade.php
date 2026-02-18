<!DOCTYPE html>
<html>

<head>
    <title>Cetak Stok Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }

        th {
            background: #eee;
        }

        h3 {
            text-align: center;
        }
    </style>
</head>

<body onload="window.print()">

    <h3>LAPORAN STOK BARANG</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Type</th>
                <th>Merek</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stoks as $i => $stok)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td>{{ $stok->kategori->nama_barang ?? '-' }}</td>
                    <td>{{ $stok->type }}</td>
                    <td>{{ $stok->merek }}</td>
                    <td align="center">{{ $stok->stok }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>

    <table width="100%" style="border:0">
        <tr style="border:0">
            <td style="border:0"></td>
            <td style="border:0; text-align:center;">
                Mengetahui,<br>
                Manager<br><br><br><br>
                ( __________________ )
            </td>
        </tr>
    </table>

</body>

</html>
