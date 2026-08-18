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
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            font-size: 10px;
        }

        th {
            background: #efefef;
            text-align: center;
            font-weight: bold;
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

    @php
        $hasLaptop = $mapings->contains(function ($m) {
            $kategori = strtolower($m->keluar?->inventaris?->dataAset?->kategori?->nama_barang ?? '');
            return str_contains($kategori, 'laptop');
        });
    @endphp

    {{-- ===================================== --}}
    {{-- TABLE --}}
    {{-- ===================================== --}}

    <table>

        <thead>

            <tr>

                <th style="width:{{ $hasLaptop ? '4%' : '5%' }}">
                    No
                </th>

                <th style="width:{{ $hasLaptop ? '12%' : '15%' }}">
                    Kode Aset
                </th>

                <th style="width:{{ $hasLaptop ? '16%' : '20%' }}">
                    Nama Pengguna
                </th>

                <th style="width:{{ $hasLaptop ? '26%' : '35%' }}">
                    Spesifikasi
                </th>

                <th style="width:{{ $hasLaptop ? '18%' : '25%' }}">
                    Data Aset
                </th>

                @if($hasLaptop)
                <th style="width:24%">
                    Hak Akses
                </th>
                @endif

            </tr>

        </thead>

        <tbody>

            @forelse($mapings as $i => $m)

                <tr>

                    {{-- ===================== --}}
                    {{-- NO --}}
                    {{-- ===================== --}}
                    <td class="center">
                        {{ sprintf('%02d', $i + 1) }}
                    </td>

                    {{-- ===================== --}}
                    {{-- KODE ASET --}}
                    {{-- ===================== --}}
                    <td>

                        <strong>
                            {{ $m->keluar->inventaris->kode_aset ?? '-' }}
                        </strong>

                        <br>

                        {{ $m->keluar->inventaris->no_inventaris ?? '-' }}

                    </td>

                    {{-- ===================== --}}
                    {{-- NAMA PENGGUNA --}}
                    {{-- ===================== --}}
                    <td>

                        <strong>
                            {{ strtoupper($m->penerima ?? '-') }}
                        </strong>

                        <br>

                        {{ $m->lokasi->nama_lokasi ?? '-' }}

                    </td>

                    {{-- ===================== --}}
                    {{-- SPESIFIKASI --}}
                    {{-- ===================== --}}
                    <td>

                        <strong>Processor</strong> :
                        {{ $m->processor ?? '-' }}

                        <br>

                        <strong>RAM</strong> :
                        {{ $m->ram ? $m->ram . ' GB' : '-' }}

                        <br>

                        <strong>Operating System</strong> :
                        {{ $m->system ?? '-' }}

                        <br>

                        <strong>Version</strong> :
                        {{ $m->version ?? '-' }}

                        <br>

                        <strong>Device ID</strong> :
                        {{ $m->device_id ?? '-' }}

                        <br>

                        <strong>Product ID</strong> :
                        {{ $m->produk_id ?? '-' }}

                    </td>

                    {{-- ===================== --}}
                    {{-- DATA ASET --}}
                    {{-- ===================== --}}
                    <td>

                        <strong>Kategori</strong> :
                        {{ $m->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                        <br>

                        <strong>Merek</strong> :
                        {{ $m->keluar->inventaris->dataAset->merek ?? '-' }}

                        <br>

                        <strong>Type</strong> :
                        {{ $m->keluar->inventaris->dataAset->type ?? '-' }}

                        <br>

                        <strong>Warna</strong> :
                        {{ $m->keluar->inventaris->dataAset->warna ?? '-' }}

                    </td>

                    {{-- ===================== --}}
                    {{-- HAK AKSES --}}
                    {{-- ===================== --}}
                    @if($hasLaptop)
                    <td>

                        <strong>Aplikasi</strong>

                        <br>

                        @php
                            $appGroups = $m->aplikasis->groupBy(function($item) { return $item->email ?? ''; });
                        @endphp
                        @forelse($appGroups as $email => $items)
                            @if(!empty($email))
                                <span class="nowrap">Email: {{ $email }}</span><br>
                            @endif
                            @foreach($items as $akses)
                                • {{ $akses->nama_akses }}<br>
                            @endforeach
                        @empty
                            -<br>
                        @endforelse

                        <br>

                        <strong>Hak Akses PPN</strong>

                        <br>

                        @php
                            $ppnGroups = $m->hakAksesPPN->groupBy(function($item) { return $item->email ?? ''; });
                        @endphp
                        @forelse($ppnGroups as $email => $items)
                            @if(!empty($email))
                                <span class="nowrap">Email: {{ $email }}</span><br>
                            @endif
                            @foreach($items as $akses)
                                • {{ $akses->nama_akses }}<br>
                            @endforeach
                        @empty
                            -<br>
                        @endforelse

                        <br>

                        <strong>Hak Akses NON PPN</strong>

                        <br>

                        @php
                            $nonPpnGroups = $m->hakAksesNonPPN->groupBy(function($item) { return $item->email ?? ''; });
                        @endphp
                        @forelse($nonPpnGroups as $email => $items)
                            @if(!empty($email))
                                <span class="nowrap">Email: {{ $email }}</span><br>
                            @endif
                            @foreach($items as $akses)
                                • {{ $akses->nama_akses }}<br>
                            @endforeach
                        @empty
                            -
                        @endforelse

                    </td>
                    @endif

                </tr>

            @empty

                <tr>

                    <td colspan="{{ $hasLaptop ? '6' : '5' }}" class="center">

                        Tidak ada data

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</body>

</html>
