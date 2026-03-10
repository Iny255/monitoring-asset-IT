@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">

    <style>
        .card-stat {
            border-radius: 12px;
            transition: 0.3s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 20px;
        }

        .bg-soft-primary {
            background: #e7f1ff;
        }

        .bg-soft-warning {
            background: #fff4e5;
        }

        .bg-soft-success {
            background: #e8f8f0;
        }

        .bg-soft-info {
            background: #e6f7ff;
        }
    </style>
@endsection


@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection


@section('content')

    <div class="row">

        {{-- WELCOME CARD --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="d-flex align-items-end row">

                    <div class="col-sm-7">
                        <div class="card-body">

                            <h5 class="card-title text-primary">
                                Selamat datang {{ Str::upper(auth()->user()->name) }} 👋
                            </h5>

                            <h6 class="mb-2">{{ $now ?? '-' }}</h6>

                            <p class="text-muted">
                                Dashboard Monitoring Aset IT
                            </p>

                        </div>
                    </div>

                    <div class="col-sm-5 text-center">
                        <div class="card-body pb-0 px-0 px-md-4">

                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                alt="dashboard">

                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- SUMMARY CARD --}}
        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box bg-soft-primary me-3">
                        <i class="bx bx-box"></i>
                    </div>

                    <div>
                        <span class="text-muted">Total Aset</span>
                        <h4 class="mb-0">{{ $totalAset ?? 0 }}</h4>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box bg-soft-warning me-3">
                        <i class="bx bx-transfer"></i>
                    </div>

                    <div>
                        <span class="text-muted">Dipinjam</span>
                        <h4 class="mb-0 text-warning">{{ $dipinjam ?? 0 }}</h4>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box bg-soft-success me-3">
                        <i class="bx bx-check-circle"></i>
                    </div>

                    <div>
                        <span class="text-muted">Dikembalikan</span>
                        <h4 class="mb-0 text-success">{{ $dikembalikan ?? 0 }}</h4>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="icon-box bg-soft-info me-3">
                        <i class="bx bx-git-compare"></i>
                    </div>

                    <div>
                        <span class="text-muted">Total Mutasi</span>
                        <h4 class="mb-0">{{ $totalMutasi ?? 0 }}</h4>
                    </div>

                </div>
            </div>
        </div>



        {{-- ROW CHART --}}
        <div class="row mt-4">

            {{-- CHART MUTASI --}}
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Grafik Transaksi Aset</h5>
                    </div>

                    <div class="card-body">
                        <div id="chartMutasi"></div>
                    </div>
                </div>
            </div>


            {{-- CHART TYPE --}}
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Komposisi Aset</h5>
                    </div>

                    <div class="card-body">
                        <div id="chartType"></div>
                    </div>
                </div>
            </div>

        </div>


        <script>
            document.addEventListener("DOMContentLoaded", function() {

                /* ================= DATA DARI CONTROLLER ================= */

                const dataMasuk = @json($dataMasuk ?? []);
                const dataKeluar = @json($dataKeluar ?? []);
                const bulanLabel = @json($bulanLabel ?? []);

                const totalLaptop = @json($totalLaptop ?? 0);
                const totalPrinter = @json($totalPrinter ?? 0);
                const totalHp = @json($totalHp ?? 0);


                /* ================= DETEKSI DARK MODE ================= */

                const isDark = document.documentElement.classList.contains("dark");

                const textColor = isDark ? "#cfd3ec" : "#566a7f";


                /* ================= CHART MUTASI ================= */

                const elMutasi = document.querySelector("#chartMutasi");

                if (elMutasi) {

                    const chartMutasi = new ApexCharts(elMutasi, {

                        chart: {
                            type: 'area',
                            height: 350,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                speed: 1000
                            }
                        },

                        colors: ['#696cff', '#ff9f43'],

                        series: [{
                                name: 'Aset Masuk',
                                data: dataMasuk
                            },
                            {
                                name: 'Aset Keluar',
                                data: dataKeluar
                            }
                        ],

                        xaxis: {
                            categories: bulanLabel,
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },

                        yaxis: {
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },

                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },

                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.1
                            }
                        },

                        markers: {
                            size: 4
                        },

                        grid: {
                            borderColor: isDark ? "#444564" : "#f1f1f1"
                        },

                        legend: {
                            labels: {
                                colors: textColor
                            }
                        },

                        tooltip: {
                            theme: isDark ? "dark" : "light"
                        }

                    });

                    chartMutasi.render();

                }


                /* ================= DONUT CHART ================= */

                const elType = document.querySelector("#chartType");

                if (elType) {

                    const chartType = new ApexCharts(elType, {

                        chart: {
                            type: 'donut',
                            height: 320,
                            foreColor: textColor
                        },

                        series: [
                            totalLaptop,
                            totalPrinter,
                            totalHp
                        ],

                        labels: [
                            'Laptop',
                            'Printer',
                            'HP / Tablet'
                        ],

                        colors: [
                            '#696cff',
                            '#ff9f43',
                            '#28c76f'
                        ],

                        legend: {
                            position: 'bottom',
                            labels: {
                                colors: textColor
                            }
                        },

                        dataLabels: {
                            enabled: true
                        },

                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '60%'
                                }
                            }
                        },

                        tooltip: {
                            theme: isDark ? "dark" : "light"
                        },

                        animations: {
                            enabled: true,
                            speed: 800
                        }

                    });

                    chartType.render();

                }

            });
        </script>

    @endsection
