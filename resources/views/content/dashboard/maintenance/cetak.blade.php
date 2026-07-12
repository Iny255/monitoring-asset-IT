<!DOCTYPE html>
<html>

<head>

    <title>
        Laporan Service & Maintenance
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

            LAPORAN SERVICE & MAINTENANCE

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

                {{ strtoupper(request('jenis')) }}

            </p>
        @endif

    </div>

    <table>

        <thead>

            <tr>

                <th width="40">No</th>

                <th width="80">Tanggal</th>

                @if (auth()->user()->role == 'super_admin')
                    <th>Perusahaan</th>
                @endif

                <th width="130">Kode Service</th>

                <th>Kode Aset</th>

                <th>Nama Barang</th>

                <th width="90">Jenis</th>

                <th width="120">Status</th>

                <th>Vendor</th>

                <th width="90">Biaya</th>

            </tr>

        </thead>

        <tbody>

            @forelse($laporan as $item)
                <tr>

                    <td class="text-center">

                        {{ $loop->iteration }}

                    </td>

                    <td>

                        {{ $item->tanggal->format('d-m-Y') }}

                    </td>

                    @if (auth()->user()->role == 'super_admin')
                        <td>

                            {{ $item->inventaris->perusahaan->nama_perusahaan ?? '-' }}

                        </td>
                    @endif

                    <td>

                        {{ $item->kode_service }}

                    </td>

                    <td>

                        {{ $item->inventaris->kode_aset }}

                    </td>

                    <td>

                        {{ $item->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                        <br>

                        <small>

                            {{ $item->inventaris->dataAset->merek ?? '' }}

                            {{ $item->inventaris->dataAset->type ?? '' }}

                        </small>

                    </td>

                    <td class="text-center">

                        {{ strtoupper($item->jenis) }}

                    </td>

                    <td class="text-center">

                        {{ strtoupper($item->status) }}

                    </td>

                    <td>

                        {{ $item->vendor ?: '-' }}

                    </td>

                    <td class="text-right">

                        {{ number_format($item->biaya, 0, ',', '.') }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="{{ auth()->user()->role == 'super_admin' ? 10 : 9 }}" class="text-center">

                        Tidak ada data.

                    </td>

                </tr>
            @endforelse

        </tbody>

        <tfoot>

            <tr>

                <th colspan="{{ auth()->user()->role == 'super_admin' ? 9 : 8 }}" class="text-right">

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
