<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>
        Data Maping
    </title>

    <style>
        @page {
            size: F4 landscape;
            margin: 8mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .container {
            width: 100%;
        }

        h3 {
            text-align: center;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        h4 {
            text-align: center;
            margin-top: 4px;
            margin-bottom: 18px;
            font-size: 15px;
            font-weight: bold;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f2f2f2;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        td {
            font-size: 10px;
        }

        td.center {
            text-align: center;
        }

        .spec-box {
            font-size: 9px;
            line-height: 1.35;
        }

        .spec-row {
            margin-bottom: 2px;
        }

        .spec-label {
            display: inline-block;
            width: 78px;
            font-weight: bold;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>

</head>

<body onload="window.print()">

    {{-- ===================================== --}}
    {{-- HEADER --}}
    {{-- ===================================== --}}

    <div class="header">

        <h3>
            DATA SPESIFIKASI DAN MAPING FILE DIVISI IT
        </h3>

        <h4>
            {{ $namaPerusahaan ?? 'SEMBILAN GROUP' }}
        </h4>

    </div>

    {{-- ===================================== --}}
    {{-- TABLE --}}
    {{-- ===================================== --}}

    <table>

        <thead>

            <tr>

                <th width="30">
                    No
                </th>

                <th width="90">
                    Kode Barang
                </th>

                <th width="160">
                    Nama Karyawan
                </th>

                <th width="90">
                    Lokasi
                </th>

                <th width="100">
                    Nama Barang
                </th>

                <th width="270">
                    Spesifikasi
                </th>

                <th width="90">
                    Garansi
                </th>

                <th width="90">
                    No Inventaris
                </th>

                <th width="90">
                    Tanggal Beli
                </th>

                <th width="180">
                    Aplikasi
                </th>

                <th width="120">
                    Data PPN
                </th>

                <th width="150">
                    Data NON PPN
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($mapings as $i => $m)
                <tr>

                    {{-- NO --}}
                    <td class="center">
                        {{ $i + 1 }}
                    </td>

                    {{-- KODE BARANG --}}
                    <td>
                        {{ $m->keluar->kode_barang ?? '-' }}
                    </td>

                    {{-- NAMA KARYAWAN --}}
                    <td>
                        {{ $m->keluar->karyawan->nama_karyawan ?? '-' }}
                    </td>

                    {{-- LOKASI --}}
                    <td>
                        {{ $m->lokasi->nama_lokasi ?? '-' }}
                    </td>

                    {{-- NAMA BARANG --}}
                    <td>
                        {{ $m->keluar->masuk->kategori->nama_barang ?? '-' }}
                    </td>

                    {{-- SPESIFIKASI --}}
                    <td>

                        <div class="spec-box">

                            <div>
                                <b>TYPE</b> :
                                {{ $m->keluar->masuk->type ?? '-' }}
                            </div>

                            <div>
                                <b>MEREK</b> :
                                {{ $m->keluar->masuk->merek ?? '-' }}
                            </div>

                            <div>
                                <b>WARNA</b> :
                                {{ $m->keluar->warna ?? '-' }}
                            </div>

                            <div>
                                <b>PROCESSOR</b> :
                                {{ $m->processor ?? '-' }}
                            </div>

                            <div>
                                <b>DEVICE ID</b> :
                                {{ $m->device_id ?? '-' }}
                            </div>

                            <div>
                                <b>PRODUCT ID</b> :
                                {{ $m->product_id ?? '-' }}
                            </div>

                            <div>
                                <b>RAM</b> :
                                {{ $m->ram ?? '-' }}
                            </div>

                            <div>
                                <b>SYSTEM</b> :
                                {{ $m->system ?? '-' }}
                            </div>

                            <div>
                                <b>VERSION</b> :
                                {{ $m->version ?? '-' }}
                            </div>

                            <div>
                                <b>INSTALL ON</b> :
                                {{ $m->install_on ?? '-' }}
                            </div>

                        </div>

                    </td>

                    {{-- GARANSI --}}
                    <td class="center">

                        {{ $m->keluar->masuk->garansi ?? '-' }}

                        Bulan

                    </td>

                    {{-- INVENTARIS --}}
                    <td class="center">
                        {{ $m->keluar->no_inventaris ?? '-' }}
                    </td>

                    {{-- TANGGAL BELI --}}
                    <td class="center nowrap">

                        {{ $m->keluar->masuk->tgl_beli ?? '-' }}

                    </td>

                    {{-- APLIKASI --}}
                    <td>
                        {{ $m->aplikasi ?: '-' }}
                    </td>

                    {{-- DATA PPN --}}
                    <td>
                        {{ $m->data_p ?: '-' }}
                    </td>

                    {{-- DATA NON --}}
                    <td>
                        {{ $m->data_n ?: '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="12" class="center">

                        Tidak ada data

                    </td>

                </tr>
            @endforelse

        </tbody>

    </table>

</body>

</html>
