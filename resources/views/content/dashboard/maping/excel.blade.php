<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Mapping Inventaris</title>
</head>

<body>

    <table>

        <tr>
            <td colspan="6" align="center">
                <strong style="font-size:18px">
                    LAPORAN MAPPING INVENTARIS
                </strong>
            </td>
        </tr>

        <tr>
            <td colspan="6" align="center">
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

                <th>Hak Akses</th>

            </tr>

        </thead>

        <tbody>

            @forelse($mapings as $i => $m)

                <tr>

                    {{-- ===================== --}}
                    {{-- NO --}}
                    {{-- ===================== --}}
                    <td>
                        {{ $i + 1 }}
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
                    <td>

                        <strong>Aplikasi</strong>

                        <br>

                        @forelse($m->aplikasis as $akses)
                            • {{ $akses->access->nama_akses }}<br>
                        @empty
                            -
                        @endforelse

                        <br><br>

                        <strong>Hak Akses PPN</strong>

                        <br>

                        @forelse($m->hakAksesPPN as $akses)
                            • {{ $akses->access->nama_akses }}<br>
                        @empty
                            -
                        @endforelse

                        <br><br>

                        <strong>Hak Akses NON PPN</strong>

                        <br>

                        @forelse($m->hakAksesNonPPN as $akses)
                            • {{ $akses->access->nama_akses }}<br>
                        @empty
                            -
                        @endforelse

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" align="center">

                        Tidak ada data

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</body>

</html>
