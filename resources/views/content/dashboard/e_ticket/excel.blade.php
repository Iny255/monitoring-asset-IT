<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Troubleshoot</title>
</head>
<body>
    <table>
        <thead>
            {{-- JUDUL LAPORAN --}}
            <tr>
                <th colspan="13" style="font-weight: bold; font-size: 13pt; text-align: center; font-family: 'Calibri', Arial, sans-serif;">
                    CHECKLIST TEMUAN &amp; TINDAKAN TROUBLESHOOT
                </th>
            </tr>
            <tr>
                <th colspan="13" style="font-weight: bold; font-size: 11pt; text-align: center; font-family: 'Calibri', Arial, sans-serif;">
                    {{ strtoupper($companyName) }}
                </th>
            </tr>
            <tr>
                <th colspan="13"></th>
            </tr>
            {{-- META INFORMASI --}}
            <tr>
                <th colspan="4" style="font-weight: bold; font-size: 10pt; text-align: left; font-family: 'Calibri', Arial, sans-serif;">
                    Divisi : {{ $divisionName }}
                </th>
                <th colspan="9"></th>
            </tr>
            <tr>
                <th colspan="4" style="font-weight: bold; font-size: 10pt; text-align: left; font-family: 'Calibri', Arial, sans-serif;">
                    Bulan : {{ $periodText }}
                </th>
                <th colspan="9"></th>
            </tr>
            <tr>
                <th colspan="13"></th>
            </tr>

            {{-- HEADER TABEL (BERTINGKAT) --}}
            <tr>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    No.
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Tanggal Masuk<br>Troubleshooting
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Tanggal Respont/Tindakan
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Nama Perangkat
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Nama Pengguna
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Divisi
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Temuan dan Trouble
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Tindakan Perbaikan
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Tindakan Pencegahan
                </th>
                <th colspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Status
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Verifikasi &amp; Validasi Tindakan Perbaikan &amp;<br>Pencegahan
                </th>
                <th rowspan="2" style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9.5pt;">
                    Tanggal<br>Penyelesaian
                </th>
            </tr>
            <tr>
                <th style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9pt;">
                    OK
                </th>
                <th style="background-color: #99cad6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 9pt;">
                    NG
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $index => $tkt)
                @php
                    $isOk = $tkt->isStatusOk();
                    $tglMasuk = $tkt->created_at ? $tkt->created_at->format('d-m-y') : '-';
                    $tglRespon = ($tkt->responded_at ?? $tkt->created_at) ? ($tkt->responded_at ?? $tkt->created_at)->format('d-m-y') : '-';
                    $tglSelesai = ($tkt->resolved_at ?? $tkt->closed_at) ? ($tkt->resolved_at ?? $tkt->closed_at)->format('d-m-y') : '-';
                @endphp
                <tr>
                    {{-- 1. NO --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $index + 1 }}
                    </td>
                    {{-- 2. TANGGAL MASUK --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tglMasuk }}
                    </td>
                    {{-- 3. TANGGAL RESPON/TINDAKAN --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tglRespon }}
                    </td>
                    {{-- 4. NAMA PERANGKAT --}}
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->perangkat_name }}
                    </td>
                    {{-- 5. NAMA PENGGUNA --}}
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->pelapor_name }}
                    </td>
                    {{-- 6. DIVISI --}}
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->divisi_pelapor }}
                    </td>
                    {{-- 7. TEMUAN DAN TROUBLE --}}
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->judul }}
                    </td>
                    {{-- 8. TINDAKAN PERBAIKAN --}}
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->tindakan_perbaikan_formatted }}
                    </td>
                    {{-- 9. TINDAKAN PENCEGAHAN --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->tindakan_pencegahan_formatted }}
                    </td>
                    {{-- 10. STATUS OK --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 11pt;">
                        {{ $isOk ? '✓' : '' }}
                    </td>
                    {{-- 10. STATUS NG --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif; font-size: 11pt;">
                        {{ !$isOk ? '✓' : '' }}
                    </td>
                    {{-- 11. VERIFIKASI & VALIDASI --}}
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tkt->verifikasi_formatted }}
                    </td>
                    {{-- 12. TANGGAL PENYELESAIAN --}}
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-family: 'Calibri', Arial, sans-serif;">
                        {{ $tglSelesai }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="border: 1px solid #000000; text-align: center; padding: 15px; color: #64748b; font-family: 'Calibri', Arial, sans-serif;">
                        Tidak ada data tiket troubleshooting untuk periode yang dipilih.
                    </td>
                </tr>
            @endforelse

            {{-- BARIS KOSONG --}}
            <tr>
                <td colspan="13" style="height: 20px;"></td>
            </tr>

            {{-- TANDA TANGAN --}}
            <tr>
                <td colspan="4" style="text-align: center; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    Dilaporkan Oleh,
                </td>
                <td colspan="5" style="text-align: center; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    Mengetahui,
                </td>
                <td colspan="4" style="text-align: center; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    Disetujui Oleh,
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center; font-weight: bold; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    Staff IT
                </td>
                <td colspan="5" style="text-align: center; font-weight: bold; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    Asmen IT
                </td>
                <td colspan="4" style="text-align: center; font-weight: bold; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    MR
                </td>
            </tr>
            <tr>
                <td colspan="4" style="height: 50px;"></td>
                <td colspan="5" style="height: 50px;"></td>
                <td colspan="4" style="height: 50px;"></td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center; font-weight: bold; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    ( {{ auth()->user()->name ?? 'Nama Staff' }} )
                </td>
                <td colspan="5" style="text-align: center; font-weight: bold; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                </td>
                <td colspan="4" style="text-align: center; font-weight: bold; font-family: 'Calibri', Arial, sans-serif; font-size: 10pt;">
                    ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
