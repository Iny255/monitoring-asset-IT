<!DOCTYPE html>
<html>

<head>

    <title>
        Laporan Penerimaan Aset
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
    </style>

</head>

<body onload="window.print()">

    <div style="text-align:center; margin-bottom:15px;">

        <h2 style="margin:0;">
            LAPORAN PENERIMAAN ASET
        </h2>

        <h3 style="margin:5px 0;">
            {{ strtoupper($namaPerusahaan) }}
        </h3>

        @if (request('tanggal_awal') || request('tanggal_akhir'))
            <p style="margin:0;">
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
                <th>No</th>
                <th>Tanggal</th>
                @if (auth()->user()->role == 'super_admin')
                    <th>Perusahaan</th>
                @endif
                <th>Kategori</th>
                <th>Merek</th>
                <th>Type</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

            @php
                $grandTotal = 0;
            @endphp

            @foreach ($laporan as $item)
                @php
                    $grandTotal += $item->total;
                @endphp

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d-m-Y') }}
                    </td>

                    @if (auth()->user()->role == 'super_admin')
                        <td>
                            {{ $item->perusahaan->nama_perusahaan ?? '-' }}
                        </td>
                    @endif
                    <td>
                        {{ $item->kategori }}
                    </td>

                    <td>
                        {{ $item->merek }}
                    </td>

                    <td>
                        {{ $item->type }}
                    </td>

                    <td>
                        {{ $item->supplier }}
                    </td>

                    <td class="text-center">
                        {{ $item->qty }}
                    </td>

                    <td class="text-end">
                        {{ number_format($item->harga_satuan, 0, ',', '.') }}
                    </td>

                    <td class="text-end">
                        {{ number_format($item->total, 0, ',', '.') }}
                    </td>

                </tr>
            @endforeach

        </tbody>

        <tfoot>

            <tr>

                <th colspan="{{ auth()->user()->role == 'super_admin' ? 9 : 8 }}" style="text-align:right">

                    GRAND TOTAL

                </th>

                <th style="text-align:right">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </th>

            </tr>

        </tfoot>

    </table>
</body>

</html>
