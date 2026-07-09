<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>

        Laporan History Mutasi

    </title>

    <style>
        body {

            font-family: Arial;

            font-size: 12px;

            margin: 30px;

        }

        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 20px;

        }

        th {

            background: #ececec;

        }

        th,
        td {

            border: 1px solid #444;

            padding: 7px;

        }

        .text-center {

            text-align: center;

        }
    </style>

</head>

<body onload="window.print()">

    <h2 class="text-center">

        LAPORAN HISTORY MUTASI ASSET

    </h2>

    <p class="text-center">

        Tanggal Cetak :

        {{ now()->format('d-m-Y H:i') }}

    </p>

    <table>

        <thead>

            <tr>

                <th>No</th>

                <th>Tanggal</th>

                <th>Asset</th>

                <th>User Lama</th>

                <th>User Baru</th>

                <th>Lokasi Lama</th>

                <th>Lokasi Baru</th>

                <th>Hak Akses</th>

                <th>Petugas</th>

            </tr>

        </thead>

        <tbody>

            @foreach ($mutasis as $item)
                <tr>

                    <td class="text-center">

                        {{ $loop->iteration }}

                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse($item->tanggal_mutasi)->format('d-m-Y') }}

                    </td>

                    <td>

                        {{ $item->nama_aset }}

                        <br>

                        <small>

                            {{ $item->kode_aset_baru }}

                        </small>

                    </td>

                    <td>

                        {{ $item->user_lama }}

                    </td>

                    <td>

                        {{ $item->user_baru }}

                    </td>

                    <td>

                        {{ $item->lokasi_lama }}

                    </td>

                    <td>

                        {{ $item->lokasi_baru }}

                    </td>

                    <td>

                        {{ $item->opsi_hak_akses == 'copy' ? 'Disalin' : 'Manual' }}

                    </td>

                    <td>

                        {{ optional($item->creator)->name }}

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
