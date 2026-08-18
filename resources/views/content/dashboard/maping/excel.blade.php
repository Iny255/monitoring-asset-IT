<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Mapping Inventaris</title>
</head>

<body>

    @php
        $hasLaptop = $mapings->contains(function ($m) {
            $kategori = strtolower($m->keluar?->inventaris?->dataAset?->kategori?->nama_barang ?? '');
            return str_contains($kategori, 'laptop');
        });
    @endphp

    <table>

        <tr>
            <td colspan="{{ $hasLaptop ? '6' : '5' }}" align="center">
                <strong style="font-size:18px">
                    LAPORAN MAPPING INVENTARIS
                </strong>
            </td>
        </tr>

        <tr>
            <td colspan="{{ $hasLaptop ? '6' : '5' }}" align="center">
                <strong>{{ strtoupper($namaPerusahaan) }}</strong>
            </td>
        </tr>

        <tr></tr>

    </table>

    <table border="1">

        <thead>

            <tr>

                <th>No</th>

                <th>Kode Aset</th>

                <th>Nama Pengguna</th>

                <th>Spesifikasi</th>

                <th>Data Aset</th>

                @if($hasLaptop)
                <th>Hak Akses</th>
                @endif

            </tr>

        </thead>

        <tbody>

            @forelse($mapings as $i => $m)

                <tr>

                    {{-- ===================== --}}
                    {{-- NO --}}
                    {{-- ===================== --}}
                    <td>
                        {{ sprintf('%02d', $i + 1) }}
                    </td>

                    {{-- ===================== --}}
                    {{-- KODE ASET --}}
                    {{-- ===================== --}}
                    <td>

                        Kode Aset :
                        {{ $m->keluar->inventaris->kode_aset ?? '-' }}

                        <br>

                        No Inventaris :
                        {{ $m->keluar->inventaris->no_inventaris ?? '-' }}

                    </td>

                    {{-- ===================== --}}
                    {{-- USER --}}
                    {{-- ===================== --}}
                    <td>

                        Nama :
                        {{ strtoupper($m->penerima ?? '-') }}

                        <br>

                        Lokasi :
                        {{ strtoupper($m->lokasi->nama_lokasi ?? '-') }}

                    </td>

                    {{-- ===================== --}}
                    {{-- SPESIFIKASI --}}
                    {{-- ===================== --}}
                    <td>

                        Processor :
                        {{ $m->processor ?? '-' }}

                        <br>

                        RAM :
                        {{ $m->ram ? $m->ram . ' GB' : '-' }}

                        <br>

                        Operating System :
                        {{ $m->system ?? '-' }}

                        <br>

                        Version :
                        {{ $m->version ?? '-' }}

                        <br>

                        Device ID :
                        {{ $m->device_id ?? '-' }}

                        <br>

                        Product ID :
                        {{ $m->produk_id ?? '-' }}

                    </td>

                    {{-- ===================== --}}
                    {{-- DATA ASET --}}
                    {{-- ===================== --}}
                    <td>

                        Kategori :
                        {{ $m->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}

                        <br>

                        Merek :
                        {{ $m->keluar->inventaris->dataAset->merek ?? '-' }}

                        <br>

                        Type :
                        {{ $m->keluar->inventaris->dataAset->type ?? '-' }}

                        <br>

                        Warna :
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
                                Email: {{ $email }}<br>
                            @endif
                            @foreach($items as $akses)
                                • {{ $akses->nama_akses }}<br>
                            @endforeach
                        @empty
                            -
                        @endforelse

                        <br><br>

                        <strong>Hak Akses PPN</strong>

                        <br>

                        @php
                            $ppnGroups = $m->hakAksesPPN->groupBy(function($item) { return $item->email ?? ''; });
                        @endphp
                        @forelse($ppnGroups as $email => $items)
                            @if(!empty($email))
                                Email: {{ $email }}<br>
                            @endif
                            @foreach($items as $akses)
                                • {{ $akses->nama_akses }}<br>
                            @endforeach
                        @empty
                            -
                        @endforelse

                        <br><br>

                        <strong>Hak Akses NON PPN</strong>

                        <br>

                        @php
                            $nonPpnGroups = $m->hakAksesNonPPN->groupBy(function($item) { return $item->email ?? ''; });
                        @endphp
                        @forelse($nonPpnGroups as $email => $items)
                            @if(!empty($email))
                                Email: {{ $email }}<br>
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

                    <td colspan="{{ $hasLaptop ? '6' : '5' }}" align="center">

                        Tidak ada data

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</body>

</html>
