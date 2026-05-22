<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <title>Detail Asset</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {

            background: #f1f5f9;

            padding: 30px;

        }

        .asset-card {

            max-width: 900px;

            margin: auto;

            border: none;

            border-radius: 20px;

            overflow: hidden;

            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);

        }

        .asset-header {

            background: linear-gradient(135deg,
                    #2563eb,
                    #1d4ed8);

            color: white;

            padding: 30px;

        }

        .asset-title {

            font-size: 28px;

            font-weight: 700;

        }

        .table th {

            width: 35%;

            background: #f8fafc;

            color: #475569;

        }

        .table td {

            color: #0f172a;

            font-weight: 500;

        }
    </style>

</head>

<body>

    <div class="card asset-card">

        {{-- HEADER --}}
        <div class="asset-header">

            <div class="asset-title">

                {{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}

            </div>

            <div>

                {{ $maping->keluar->kode_barang ?? '-' }}

            </div>

        </div>

        {{-- BODY --}}
        <div class="card-body p-0">

            <table class="table table-bordered mb-0">

                <tr>
                    <th>No Inventaris</th>
                    <td>{{ $maping->keluar->no_inventaris ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Lokasi</th>
                    <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Perusahaan</th>
                    <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Warna</th>
                    <td>{{ $maping->keluar->warna ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Beli</th>
                    <td>{{ $maping->keluar->masuk->tgl_beli ?? '-' }}</td>

                <tr>
                    <th>Processor</th>
                    <td>{{ $maping->processor ?? '-' }}</td>
                </tr>

                <tr>
                    <th>RAM</th>
                    <td>{{ $maping->ram ?? '-' }} GB</td>
                </tr>

                <tr>
                    <th>System</th>
                    <td>{{ $maping->system ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>

                        @if ($maping->status == 'aktif')
                            <span class="badge bg-success">
                                Aktif
                            </span>
                        @else
                            <span class="badge bg-danger">
                                Dicabut
                            </span>
                        @endif

                    </td>
                </tr>

            </table>

        </div>

    </div>

</body>

</html>
