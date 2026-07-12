<!DOCTYPE html>
<html>

<head>

    <title>
        Laporan Peminjaman Aset
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        h2,
        h3,
        p {
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>

</head>

<body onload="window.print()">

    <div class="header">

        <h2>

            LAPORAN PEMINJAMAN ASET

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

        @if (request('status'))
            <p>

                Status :

                {{ strtoupper(request('status')) }}

            </p>
        @endif

        @if (request('jenis'))
            <p>

                Jenis :

                {{ strtoupper(str_replace('_', ' ', request('jenis'))) }}

            </p>
        @endif

    </div>

    <table>

        <thead>

            <tr>

                <th>No</th>

                <th>Tanggal</th>

                @if (auth()->user()->role == 'super_admin')
                    <th>Perusahaan</th>
                @endif

                <th>Kode</th>

                <th>Inventaris</th>

                <th>Peminjam</th>

                <th>Jenis</th>

                <th>Status</th>

            </tr>

        </thead>

        <tbody>

            @forelse($laporan as $item)
                <tr>

                    <td class="text-center">

                        {{ $loop->iteration }}

                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}

                    </td>

                    @if (auth()->user()->role == 'super_admin')
                        <td>

                            {{ $item->inventaris->perusahaan->nama_perusahaan ?? '-' }}

                        </td>
                    @endif

                    <td>

                        {{ $item->kode_peminjaman }}

                    </td>

                    <td>

                        {{ $item->inventaris->kode_aset ?? '-' }}

                        <br>

                        <small>

                            {{ $item->inventaris->dataAset->nama_barang ?? '-' }}
                              {{ $item->inventaris->dataAset->merek ?? '' }}

                             {{ $item->inventaris->dataAset->type ?? '' }}

                        </small>

                    </td>

                    <td>

                        @if ($item->jenis_peminjaman == 'internal')
                            {{ $item->karyawan->nama_karyawan ?? '-' }}
                        @else
                            {{ $item->perusahaanTujuan->nama_perusahaan ?? '-' }}

                            <br>

                            <small>

                                {{ $item->karyawanTujuan->nama_karyawan ?? '-' }}

                            </small>
                        @endif

                    </td>

                    <td>

                        {{ strtoupper(str_replace('_', ' ', $item->jenis_peminjaman)) }}

                    </td>

                    <td>

                        {{ strtoupper($item->status) }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="{{ auth()->user()->role == 'super_admin' ? 8 : 7 }}" class="text-center">

                        Tidak ada data.

                    </td>

                </tr>
            @endforelse

        </tbody>

        <tfoot>

            <tr>

                <th colspan="{{ auth()->user()->role == 'super_admin' ? 7 : 6 }}" class="text-right">

                    TOTAL DATA

                </th>

                <th class="text-center">

                    {{ $laporan->count() }}

                </th>

            </tr>

        </tfoot>

    </table>

</body>

</html>
