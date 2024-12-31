<!DOCTYPE html>
<html>

<head>
    <title>Laporan Posyandu {{ strtoupper($month) }} - PDF</title>
    <style>
        /* Add your styling here */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
            font-size: 10px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        #infoCetak {
            font-size: 9px;
            color: grey;
        }

        #judul {
            text-align: center;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="text-center">
        {{-- <img src="{{ public_path('assets/img/logo-posyandu.png') }}" width="80" alt=""> --}}

        <h5 id="judul">POSYANDU DESA CENDONO KABUPATEN KUDUS <br> <br>
            <span>LAPORAN KEGIATAN POSYANDU PERIODE {{ strtoupper($month) }}
            </span>
        </h5>
    </div>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NIK</th>
                <th>NAMA</th>
                <th>JK</th>
                <th>TGL LAHIR</th>
                <th>BB LAHIR</th>
                <th>NAMA ORTU</th>
                <th>POSYANDU</th>
                <th>RT</th>
                <th>RW</th>
                <th>ALAMAT</th>
                <th>TB(CM)</th>
                <th>BB(KG)</th>
                <th>Z-SOCRE</th>
                <th>USIA <br>(BULAN)</th>
                <th>STATUS <br> GIZI</th>
                <th>RUJUKAN KEBIJAKAN PEMERINTAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                use Illuminate\Support\Carbon;
            @endphp
            @forelse ($pendaftarans as $index => $pendaftaran)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pendaftaran->nik }}</td>
                    <td>{{ strtoupper($pendaftaran->nama_balita) }}</td>
                    <td>{{ $pendaftaran->jenis_kelamin === 'laki-laki' ? 'LK' : ($pendaftaran->jenis_kelamin === 'perempuan' ? 'PR' : strtoupper($pendaftaran->jenis_kelamin)) }}
                    </td>
                    <td>{{ Carbon::parse($pendaftaran->tanggal_lahir)->format('d/m/Y') }}</td>
                    <td>{{ strtoupper($pendaftaran->bb_lahir) }}</td>
                    <td>{{ strtoupper($pendaftaran->nama_ortu) }}</td>
                    <td>{{ strtoupper($pendaftaran->lokasi->nama_posyandu ?? 'N/A') }}</td>
                    <td>{{ strtoupper($pendaftaran->rt) }}</td>
                    <td>{{ strtoupper($pendaftaran->rw) }}</td>
                    <td>{{ strtoupper($pendaftaran->dukuh) }}</td>
                    <td>{{ $pendaftaran->berat_badan ?? 'N/A' }}</td>
                    <td>{{ $pendaftaran->tinggi_badan ?? 'N/A' }}</td>
                    <td>{{ number_format($pendaftaran->z_score, 2, '.', '') }}</td>
                    <td>{{ $pendaftaran->usia }}</td>
                    <td>{{ strtoupper($pendaftaran->status_gizi) }}</td>
                    <td>
                        {{ $pendaftaran->status_gizi === 'normal'
                            ? 'Pemberian makanan tambahan (PMT) dengan protein hewani'
                            : ($pendaftaran->status_gizi === 'pendek (stunted)'
                                ? 'Rujuk Puskesmas untuk konfirmasi stunting dan terapi'
                                : ($pendaftaran->status_gizi === 'sangat pendek (severely stunted)'
                                    ? 'Rujuk ke Puskesmas untuk mendapatkan rujukan evaluasi stunting ke RSUD'
                                    : 'Status gizi tidak diketahui')) }}
                    </td>



                </tr>
            @empty
                <tr>
                    <td colspan="17" style="text-align: center; font-weight: bold;">
                        Tidak ada data pada periode {{ strtoupper($month) }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="position: relative; width: 100%; font-family: Arial, sans-serif; font-size: 12px;">
        <p id="infoCetak" style="position: absolute; top: 10px; right: 10px; text-align: right; margin: 0;">
            Dicetak pada {{ $infoCetak }}, oleh {{ auth()->user()->username }}
        </p>
        <div style="text-align: right; margin-top: 80px; margin-right: 10px;">
            <P style="margin: 0;">Kudus, {{ $tglSignature }}</P>
            <p>Bidan Posyandu Cendono</p>
            <br><br><br><br>
            <strong>Dian Kurnia Ariyani, Amd. Keb</strong><br>
            NIP. 19870116 201704 2 003
        </div>
    </div>

</body>

</html>
