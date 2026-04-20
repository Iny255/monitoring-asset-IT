@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Super Admin')

{{-- ================= STYLE ================= --}}
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">

    <style>
        .card-stat {
            border-radius: 15px;
            border: none;
            overflow: hidden;
            transition: .3s;
            background: #fff;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 22px;
        }

        .bg-soft-primary {
            background: #e7f1ff;
            color: #696cff
        }

        .bg-soft-success {
            background: #e8f8f0;
            color: #28c76f
        }

        .bg-soft-warning {
            background: #fff4e5;
            color: #ff9f43
        }

        .bg-soft-info {
            background: #e6f7ff;
            color: #03c3ec
        }

        .dashboard-bg {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            transition: .5s;
        }

        .fade-up.show {
            opacity: 1;
            transform: translateY(0);
        }

        .table-hover tbody tr:hover {
            background: #f8fafc;
        }
    </style>
@endsection

{{-- ================= CONTENT ================= --}}
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER --}}
        <div class="card dashboard-bg mb-4 shadow-sm border-0 fade-up">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-white mb-1">
                        Super Admin Dashboard 👑
                    </h4>
                    <p class="mb-0 opacity-75">
                        Monitoring seluruh aset IT dari {{ $perusahaanCount }} perusahaan
                    </p>
                </div>
                <img src="{{ asset('assets/img/superadmin_logo.png') }}" height="110">
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="row g-4 mb-4">

            <div class="col-md-3 fade-up">
                <div class="card card-stat shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-soft-primary me-3">
                            <i class="bx bx-box"></i>
                        </div>
                        <div>
                            <small>Total Aset</small>
                            <h4>{{ number_format($totalAset) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 fade-up">
                <div class="card card-stat shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-soft-warning me-3">
                            <i class="bx bx-transfer"></i>
                        </div>
                        <div>
                            <small>Dipinjam</small>
                            <h4>{{ $dipinjam }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 fade-up">
                <div class="card card-stat shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-soft-success me-3">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div>
                            <small>Dikembalikan</small>
                            <h4>{{ $dikembalikan }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 fade-up">
                <div class="card card-stat shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-soft-info me-3">
                            <i class="bx bx-git-compare"></i>
                        </div>
                        <div>
                            <small>Mutasi</small>
                            <h4>{{ $totalMutasi }}</h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- EXTRA STAT --}}
        <div class="row g-4 mb-4">

            <div class="col-md-4 fade-up">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h6>Aset Masuk</h6>
                        <h3 class="text-success">{{ number_format($totalMasuk) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 fade-up">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h6>Aset Keluar</h6>
                        <h3 class="text-danger">{{ number_format($totalKeluar) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 fade-up">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h6>Aset Digunakan</h6>
                        <h3 class="text-primary">{{ number_format($totalDigunakan) }}</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- CHART --}}
        <div class="row g-4">

            <div class="col-lg-8 fade-up">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5>Grafik Transaksi Global</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartTransaksiAset"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 fade-up">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5>Komposisi Aset</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartDonutAset"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE PERUSAHAAN --}}
        <div class="card mt-4 shadow-sm fade-up">
            <div class="card-header">
                <h5>Ringkasan Perusahaan</h5>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover text-center">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th class="text-start">Perusahaan</th>
                            <th>Aset Masuk</th>
                            <th>Aset Keluar</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($perusahaanList as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start fw-semibold">{{ $p->nama_perusahaan }}</td>
                                <td><span class="badge bg-success">{{ $p->masuk_count ?? 0 }}</span></td>
                                <td><span class="badge bg-danger">{{ $p->keluar_count ?? 0 }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </div>
@endsection

{{-- ================= SCRIPT ================= --}}
@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            new ApexCharts(document.querySelector("#chartTransaksiAset"), {
                chart: {
                    type: 'area',
                    height: 350
                },
                series: [{
                        name: 'Masuk',
                        data: @json($dataMasuk)
                    },
                    {
                        name: 'Keluar',
                        data: @json($dataKeluar)
                    }
                ],
                xaxis: {
                    categories: @json($bulanLabel)
                },
                colors: ['#696cff', '#8592a3']
            }).render();

            new ApexCharts(document.querySelector("#chartDonutAset"), {
                chart: {
                    type: 'donut'
                },
                series: [{{ $totalLaptop }}, {{ $totalPrinter }}, {{ $totalHp }}],
                labels: ['Laptop', 'Printer', 'HP/Tablet'],
                colors: ['#696cff', '#ff9f43', '#28c76f']
            }).render();

            document.querySelectorAll(".fade-up").forEach((el, i) => {
                setTimeout(() => el.classList.add("show"), 150 * i)
            })

        });
    </script>
@endsection
