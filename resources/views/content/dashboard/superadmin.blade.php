@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Super Admin')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">

    <style>
        .card-stat {
            border: none;
            border-radius: 18px;
            transition: .3s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
        }

        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .bg-soft-primary {
            background: #e7f1ff;
            color: #696cff;
        }

        .bg-soft-success {
            background: #e8f8f0;
            color: #28c76f;
        }

        .bg-soft-danger {
            background: #ffe9e9;
            color: #ea5455;
        }

        .bg-soft-warning {
            background: #fff4e5;
            color: #ff9f43;
        }

        .bg-soft-info {
            background: #e6f7ff;
            color: #03c3ec;
        }

        .dashboard-bg {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border-radius: 18px;
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
    </style>
@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER --}}
        <div class="card dashboard-bg shadow-sm border-0 mb-4 fade-up">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>

                    <h3 class="text-white fw-bold mb-1">
                        Super Admin Dashboard 👑
                    </h3>

                    <p class="mb-0 opacity-75">

                        Monitoring seluruh aset IT dari
                        {{ $perusahaanCount }} perusahaan

                    </p>

                </div>

                <img src="{{ asset('assets/img/superadmin_logo.png') }}" height="100">

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
                            <h4 class="mb-0 fw-bold">
                                {{ number_format($totalAset) }}
                            </h4>
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
                            <h4 class="mb-0 fw-bold">
                                {{ $dipinjam }}
                            </h4>
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
                            <h4 class="mb-0 fw-bold">
                                {{ $dikembalikan }}
                            </h4>
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
                            <h4 class="mb-0 fw-bold">
                                {{ $totalMutasi }}
                            </h4>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- EXTRA --}}
        <div class="row g-4 mb-4">

            <div class="col-md-4 fade-up">

                <div class="card shadow-sm border-0 text-center">

                    <div class="card-body py-4">

                        <h6 class="text-muted">
                            Aset Masuk
                        </h6>

                        <h2 class="fw-bold text-success">

                            {{ number_format($totalStok) }}

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-4 fade-up">

                <div class="card shadow-sm border-0 text-center">

                    <div class="card-body py-4">

                        <h6 class="text-muted">
                            Aset Keluar
                        </h6>

                        <h2 class="fw-bold text-danger">

                            {{ number_format($totalKeluar) }}

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-4 fade-up">

                <div class="card shadow-sm border-0 text-center">

                    <div class="card-body py-4">

                        <h6 class="text-muted">
                            Aset Digunakan
                        </h6>

                        <h2 class="fw-bold text-primary">

                            {{ number_format($totalDigunakan) }}

                        </h2>

                    </div>

                </div>

            </div>

        </div>

        {{-- CHART --}}
        <div class="row g-4">

            <div class="col-lg-8 fade-up">

                <div class="card shadow-sm border-0">

                    <div class="card-header border-0">

                        <h5 class="mb-0">
                            Grafik Transaksi Global
                        </h5>

                    </div>

                    <div class="card-body">

                        <div id="chartTransaksiAset"></div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 fade-up">

                <div class="card shadow-sm border-0">

                    <div class="card-header border-0">

                        <h5 class="mb-0">
                            Komposisi Aset
                        </h5>

                    </div>

                    <div class="card-body">

                        <div id="chartDonutAset"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            new ApexCharts(
                document.querySelector("#chartTransaksiAset"), {
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

                    colors: [
                        '#696cff',
                        '#ff3e1d'
                    ],

                    stroke: {
                        curve: 'smooth'
                    }
                }
            ).render();

            new ApexCharts(
                document.querySelector("#chartDonutAset"), {
                    chart: {
                        type: 'donut'
                    },

                    series: [
                        {{ $totalLaptop }},
                        {{ $totalPrinter }},
                        {{ $totalHp }}
                    ],

                    labels: [
                        'Laptop',
                        'Printer',
                        'HP/Tablet'
                    ],

                    colors: [
                        '#696cff',
                        '#ff9f43',
                        '#28c76f'
                    ]
                }
            ).render();

            document.querySelectorAll('.fade-up')
                .forEach((el, i) => {

                    setTimeout(() => {

                        el.classList.add('show')

                    }, 120 * i)

                });

        });
    </script>

@endsection
