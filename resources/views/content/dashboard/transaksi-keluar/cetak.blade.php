<!DOCTYPE html>
<html>

<head>

    <title>
        Laporan Pemakaian Aset
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .header {
            margin-bottom: 15px;
        }

        .header h2,
        .header h3,
        .header p {
            margin: 3px 0;
        }
    </style>

</head>

<body onload="window.print()">

    <div class="header text-center">

        <h2>
            LAPORAN PEMAKAIAN ASET
        </h2>

        <h3>
            {{ strtoupper($namaPerusahaan) }}
        </h3>

        @if (request('tanggal_awal') || request('tanggal_akhir'))
            <p>

                Periode :

                {{ request('tanggal_awal') ? \Carbon\Carbon::parse(request('tanggal_awal'))->format('d-m-Y') : '-' }}

                s/d

                {{ request('tanggal_akhir') ? \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d-m-Y') : '-' }}

            </p>
        @endif

    </div>

    <table>

        <thead>

            <tr>

                <th width="40">
                    No
                </th>

                <th width="90">
                    Tanggal
                </th>

                @if (auth()->user()->role == 'super_admin')
                    <th>
                        Perusahaan
                    </th>
                @endif

                <th>
                    Kode Aset
                </th>

                <th>
                    No Inventaris
                </th>

                <th>
                    Kategori
                </th>

                <th>
                    Merek
                </th>

                <th>
                    Type
                </th>

                <th>
                    Penerima
                </th>

                <th>
                    Divisi
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($keluars as $item)
                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}
                    </td>

                    @if (auth()->user()->role == 'super_admin')
                        <td>
                            {{ $item->perusahaan->nama_perusahaan ?? '-' }}
                        </td>
                    @endif

                    <td>
                        {{ $item->inventaris->kode_aset ?? '-' }}
                    </td>

                    <td>
                        {{ $item->inventaris->no_inventaris ?? '-' }}
                    </td>

                    <td>
                        {{ $item->inventaris->dataAset->kategori->nama_barang ?? '-' }}
                    </td>

                    <td>
                        {{ $item->inventaris->dataAset->merek ?? '-' }}
                    </td>

                    <td>
                        {{ $item->inventaris->dataAset->type ?? '-' }}
                    </td>

                    <td>

                        @if ($item->jenis_penerima == 'Perorangan')
                            {{ $item->karyawan->nama_karyawan ?? '-' }}
                        @else
                            DIVISI
                        @endif

                    </td>

                    <td>

                        @if ($item->jenis_penerima == 'Perorangan')
                            {{ $item->karyawan->divisi ?? '-' }}
                        @else
                            {{ $item->divisi_klr }}
                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="{{ auth()->user()->role == 'super_admin' ? 10 : 9 }}" class="text-center">

                        Tidak ada data

                    </td>

                </tr>
            @endforelse

        </tbody>

    </table>

    <br>

    <table style="width:40%; margin-left:auto;">

        <tr>

            <th>
                Total Pemakaian Aset
            </th>

            <th width="80">
                {{ $keluars->count() }}
            </th>

        </tr>

    </table>

</body>

</html>
