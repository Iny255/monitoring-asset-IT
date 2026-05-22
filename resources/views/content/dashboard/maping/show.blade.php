@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Data Mapping')

@section('content')

    <style>
        /* =====================================
                               CARD
                            ===================================== */
        .detail-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            background: #fff;
        }

        .detail-header {
            padding: 22px 28px;
            border-bottom: 1px solid #eef2f7;
            background: #fff;
        }

        .detail-title {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        /* =====================================
                               BADGE
                            ===================================== */
        .badge-inv {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        /* =====================================
                               TABLE
                            ===================================== */
        .detail-table {
            margin: 0;
        }

        .detail-table th {
            width: 18%;
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            padding: 16px 18px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .detail-table td {
            width: 32%;
            background: #fff;
            color: #1e293b;
            font-size: 14px;
            font-weight: 500;
            padding: 16px 18px;
            vertical-align: middle;
            word-break: break-word;
        }

        .detail-table tr:hover td {
            background: #f8fbff;
        }

        /* =====================================
                               QR CARD
                            ===================================== */
        .qr-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .05);
            overflow: hidden;
            height: 100%;
        }

        .qr-card-body {
            padding: 30px 20px;
            text-align: center;
        }

        .qr-wrapper {
            background: #fff;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            margin-bottom: 16px;
        }

        .qr-title {
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .qr-desc {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        /* =====================================
                               BUTTON
                            ===================================== */
        .btn-download {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 12px;
            padding: 12px 22px;
            font-weight: 600;
            color: #fff;
            transition: .25s;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        .btn-kembali {
            padding: 11px 26px;
            border-radius: 12px;
            font-weight: 600;
        }

        /* =====================================
                               MOBILE
                            ===================================== */
        @media(max-width:992px) {

            .detail-title {
                font-size: 24px;
            }

            .detail-table th,
            .detail-table td {
                font-size: 13px;
                padding: 14px;
            }

            .qr-title {
                font-size: 24px;
            }

        }
    </style>

    <div class="container-fluid px-0">

        <div class="card detail-card">

            {{-- HEADER --}}
            <div class="detail-header d-flex justify-content-between align-items-center">

                <h4 class="detail-title">
                    Detail Mapping Asset
                </h4>

                <span class="badge-inv">
                    INV-{{ $maping->keluar->no_inventaris ?? '-' }}
                </span>

            </div>

            {{-- BODY --}}
            <div class="card-body p-4">

                <div class="row g-4 align-items-start">

                    {{-- DETAIL TABLE --}}
                    <div class="col-xl-8">

                        <div class="table-responsive">

                            <table class="table table-bordered detail-table align-middle">

                                <tr>
                                    <th>Kode Barang</th>
                                    <td>{{ $maping->keluar->kode_barang }}</td>

                                    <th>Nama Barang</th>
                                    <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Type</th>
                                    <td>{{ $maping->keluar->masuk->type ?? '-' }}</td>

                                    <th>Merek</th>
                                    <td>{{ $maping->keluar->masuk->merek ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Warna</th>
                                    <td>{{ $maping->keluar->warna ?? '-' }}</td>

                                    <th>Garansi</th>
                                    <td>{{ $maping->keluar->masuk->garansi ?? '-' }} BULAN</td>
                                </tr>

                                <tr>
                                    <th>No Inventaris</th>
                                    <td>{{ $maping->keluar->no_inventaris ?? '-' }}</td>

                                    <th>Tanggal Beli</th>
                                    <td>{{ $maping->keluar->masuk->tgl_beli ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Nama Karyawan</th>
                                    <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>

                                    <th>Lokasi</th>
                                    <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Perusahaan</th>
                                    <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>

                                    <th>Processor</th>
                                    <td>{{ $maping->processor ?: '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Device ID</th>
                                    <td>{{ $maping->device_id ?: '-' }}</td>

                                    <th>Produk ID</th>
                                    <td>{{ $maping->produk_id ?: '-' }}</td>
                                </tr>

                                <tr>
                                    <th>RAM</th>
                                    <td>{{ $maping->ram ?: '-' }} GB</td>

                                    <th>System</th>
                                    <td>{{ $maping->system ?: '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Version</th>
                                    <td>{{ $maping->version ?: '-' }}</td>

                                    <th>Install On</th>
                                    <td>{{ $maping->instal_on ?: '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Aplikasi</th>
                                    <td>{{ $maping->aplikasi ?: '-' }}</td>

                                    <th>Hak Akses</th>
                                    <td>{{ $maping->data_p ?: '-' }}</td>
                                </tr>

                            </table>

                        </div>

                    </div>

                    {{-- QR CODE --}}
                    <div class="col-xl-4">

                        <div class="card qr-card">

                            <div class="qr-card-body">

                                {{-- DOWNLOAD QR --}}
                                <div id="qr-code">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate(route('maping.public_show', $maping->id)) !!}
                                </div>


                                <div class="qr-title">
                                    {{ $maping->keluar->kode_barang }}
                                </div>

                                <div class="qr-desc">
                                    Scan QR untuk melihat detail asset
                                </div>
                                <button type="button" class="btn btn-download" onclick="downloadQR()">

                                    Download QR

                                </button>


                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- BUTTON --}}
                <div class="mt-4">

                    @auth

                        @if (auth()->user()->role === 'manager')
                            <a href="{{ route('manager.maping.index') }}" class="btn btn-secondary btn-kembali">

                                ← Kembali

                            </a>
                        @else
                            <a href="{{ route('maping.index') }}" class="btn btn-secondary btn-kembali">

                                ← Kembali

                            </a>
                        @endif

                    @endauth

                </div>

            </div>

        </div>

    </div>
    <script>
        function downloadQR() {

            // ambil svg
            const svg = document.querySelector('#qr-code svg');

            // convert svg
            const serializer = new XMLSerializer();

            const source = serializer.serializeToString(svg);

            // buat image
            const image = new Image();

            image.src =
                'data:image/svg+xml;base64,' +
                btoa(unescape(encodeURIComponent(source)));

            image.onload = function() {

                // canvas
                const canvas = document.createElement('canvas');

                canvas.width = image.width;

                canvas.height = image.height;

                const ctx = canvas.getContext('2d');

                ctx.drawImage(image, 0, 0);

                // convert png
                const pngFile = canvas.toDataURL('image/png');

                // download
                const downloadLink = document.createElement('a');

                downloadLink.download =
                    'QR-{{ $maping->keluar->kode_barang }}.png';

                downloadLink.href = pngFile;

                downloadLink.click();
            };
        }
    </script>
@endsection
