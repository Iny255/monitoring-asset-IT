<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>

        Cetak History Mutasi

    </title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 35px;
        }

        h2,
        h4 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 7px;
            vertical-align: top;
        }

        .section {
            margin-top: 25px;
        }

        .title {
            background: #f2f2f2;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #0d6efd;
            margin-bottom: 10px;
        }

        .border {
            border: 1px solid #ddd;
        }

        .center {
            text-align: center;
        }
    </style>

</head>

<body onload="window.print()">

    <div class="center">

        <h2>

            HISTORY MUTASI ASSET

        </h2>

        <small>

            Monitoring Asset System

        </small>

        <hr>

    </div>

    <div class="section">

        <div class="title">

            Ringkasan Asset

        </div>

        <table class="border">

            <tr>

                <td width="220">Nama Asset</td>

                <td>{{ $historyMutasi->nama_aset }}</td>

            </tr>

            <tr>

                <td>Kode Asset</td>

                <td>{{ $historyMutasi->kode_aset_baru }}</td>

            </tr>

            <tr>

                <td>No Inventaris</td>

                <td>{{ $historyMutasi->no_inventaris_baru }}</td>

            </tr>

        </table>

    </div>

    <div class="section">

        <div class="title">

            Detail Mutasi

        </div>

        <table class="border">

            <tr>

                <td width="220">

                    User Lama

                </td>

                <td>

                    {{ $historyMutasi->user_lama }}

                </td>

            </tr>

            <tr>

                <td>

                    User Baru

                </td>

                <td>

                    {{ $historyMutasi->user_baru }}

                </td>

            </tr>

            <tr>

                <td>

                    Lokasi Lama

                </td>

                <td>

                    {{ $historyMutasi->lokasi_lama }}

                </td>

            </tr>

            <tr>

                <td>

                    Lokasi Baru

                </td>

                <td>

                    {{ $historyMutasi->lokasi_baru }}

                </td>

            </tr>

            <tr>

                <td>

                    Jenis Mutasi

                </td>

                <td>

                    {{ strtoupper(str_replace('_', ' ', $historyMutasi->jenis_mutasi)) }}

                </td>

            </tr>

            <tr>

                <td>

                    Perlakuan Hak Akses

                </td>

                <td>

                    {{ $historyMutasi->opsi_hak_akses == 'copy' ? 'Hak akses disalin' : 'Diatur manual' }}

                </td>

            </tr>

            <tr>

                <td>

                    Catatan

                </td>

                <td>

                    {{ $historyMutasi->catatan ?: '-' }}

                </td>

            </tr>

            <tr>

                <td>

                    Petugas

                </td>

                <td>

                    {{ optional($historyMutasi->creator)->name }}

                </td>

            </tr>

            <tr>

                <td>

                    Tanggal Mutasi

                </td>

                <td>

                    {{ \Carbon\Carbon::parse($historyMutasi->tanggal_mutasi)->format('d F Y') }}

                </td>

            </tr>

        </table>

    </div>

</body>

</html>
