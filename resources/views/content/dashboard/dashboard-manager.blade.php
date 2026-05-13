@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Monitoring Aset')

{{-- ================= THEME ================= --}}
@php

    $primaryColor = $theme['primary_color'] ?? '#0b2f57';

    $secondaryColor = $theme['secondary_color'] ?? '#154b87';

@endphp


{{-- ================= STYLE ================= --}}
@section('vendor-style')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">

    <style>
        :root {

            --primary-theme: {{ $primaryColor }};
            --secondary-theme: {{ $secondaryColor }};

        }

        /* =====================================
                           CARD
                        ===================================== */
        .card-stat {

            border-radius: 15px;

            border: none;

            overflow: hidden;

            position: relative;

            transition: .3s;

            background: #fff;

            height: 100%;

        }

        .card-stat:hover {

            transform: translateY(-5px);

            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);

        }

        /* =====================================
                           WAVE
                        ===================================== */
        .wave-box {

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            z-index: 0;

            opacity: .10;

            pointer-events: none;

        }

        .wave {

            position: absolute;

            bottom: -50%;
            left: -25%;

            width: 150%;
            height: 150%;

            background: currentColor;

            border-radius: 38%;

            animation: waveMove 10s infinite linear;

        }

        @keyframes waveMove {

            from {
                transform: rotate(0deg)
            }

            to {
                transform: rotate(360deg)
            }

        }

        /* =====================================
                           TEXT
                        ===================================== */
        .stat-label {

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .8px;

            font-size: .72rem;

            color: #32475c;

        }

        /* =====================================
                           ICON
                        ===================================== */
        .icon-box {

            width: 50px;

            height: 50px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            font-size: 24px;

        }

        .theme-icon-box {

            background:
                linear-gradient(135deg,
                    var(--primary-theme),
                    var(--secondary-theme));

            color: white;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, .12);

        }

        /* =====================================
                           DASHBOARD HEADER
                        ===================================== */
        .dashboard-bg {

            background:
                linear-gradient(135deg,
                    var(--primary-theme),
                    var(--secondary-theme));

            color: white;

            border-radius: 18px;

        }

        /* =====================================
                           ANIMATION
                        ===================================== */
        .fade-up {

            opacity: 0;

            transform: translateY(20px);

            transition: .5s;

        }

        .fade-up.show {

            opacity: 1;

            transform: translateY(0);

        }

        /* =====================================
                           CHART
                        ===================================== */
        #chartTransaksiAset,
        #chartDonutAset {

            width: 100% !important;

            min-height: 320px;

        }
    </style>

@endsection



{{-- ================= CONTENT ================= --}}
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">


        {{-- =====================================
         WELCOME
    ===================================== --}}
        <div class="row mb-4">

            <div class="col-12 fade-up">

                <div class="card dashboard-bg shadow-sm border-0">

                    <div class="card-body d-flex align-items-center justify-content-between">

                        <div>

                            <h4 class="text-white mb-1">

                                Selamat Datang,
                                {{ Str::upper(auth()->user()->name) }} 👋

                            </h4>

                            <p class="mb-0 opacity-75">

                                Monitoring Aset Divisi IT

                            </p>

                        </div>

                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="120">

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================
         SUMMARY
    ===================================== --}}
        @php

            $summary = [
                [
                    'title' => 'Total Aset',
                    'count' => $totalAset ?? 0,
                    'icon' => 'bx-box',
                ],

                [
                    'title' => 'Dipinjam',
                    'count' => $dipinjam ?? 0,
                    'icon' => 'bx-transfer',
                ],

                [
                    'title' => 'Kembali',
                    'count' => $dikembalikan ?? 0,
                    'icon' => 'bx-check-circle',
                ],

                [
                    'title' => 'Total Mutasi',
                    'count' => $totalMutasi ?? 0,
                    'icon' => 'bx-git-compare',
                ],
            ];

        @endphp


        <div class="row g-4 mb-4">

            @foreach ($summary as $item)
                <div class="col-xl-3 col-md-6 fade-up">

                    <div class="card card-stat shadow-sm border-0">

                        <div class="wave-box">
                            <div class="wave"></div>
                        </div>

                        <div class="card-body d-flex align-items-center">

                            <div class="icon-box me-3 theme-icon-box">

                                <i class="bx {{ $item['icon'] }}"></i>

                            </div>

                            <div>

                                <span class="stat-label">

                                    {{ $item['title'] }}

                                </span>

                                <h4>

                                    {{ number_format($item['count'], 0, ',', '.') }}

                                </h4>

                            </div>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>


        {{-- =====================================
         CHART
    ===================================== --}}
        <div class="row g-4">


            {{-- AREA CHART --}}
            <div class="col-lg-8 fade-up">

                <div class="card shadow-sm border-0" style="border-radius:15px">

                    <div class="card-header">

                        <h5 class="mb-0">

                            Grafik Transaksi Aset

                        </h5>

                    </div>

                    <div class="card-body">

                        <div id="chartTransaksiAset"></div>

                    </div>

                </div>

            </div>


            {{-- DONUT --}}
            <div class="col-lg-4 fade-up">

                <div class="card shadow-sm border-0" style="border-radius:15px">

                    <div class="card-header">

                        <h5 class="mb-0">

                            Stok Aset

                        </h5>

                    </div>

                    <div class="card-body d-flex align-items-center justify-content-center">

                        <div id="chartDonutAset"></div>

                    </div>

                </div>

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

            const isDark =
                document.documentElement.classList.contains("dark-style");

            const labelColor =
                isDark ? "#cbcbe2" : "#566a7f";


            /* =====================================
               AREA CHART
            ===================================== */

            new ApexCharts(
                document.querySelector("#chartTransaksiAset"), {

                    chart: {

                        height: 350,

                        type: 'area',

                        toolbar: {
                            show: false
                        },

                        fontFamily: 'Public Sans'

                    },

                    series: [

                        {
                            name: 'Masuk',
                            data: @json($dataMasuk ?? [])
                        },

                        {
                            name: 'Keluar',
                            data: @json($dataKeluar ?? [])
                        }

                    ],

                    xaxis: {

                        categories: @json($bulanLabel ?? []),

                        labels: {
                            style: {
                                colors: labelColor
                            }
                        }

                    },

                    yaxis: {

                        labels: {
                            style: {
                                colors: labelColor
                            }
                        }

                    },

                    legend: {

                        position: 'top',

                        horizontalAlign: 'right',

                        labels: {
                            colors: labelColor
                        }

                    },

                    colors: [
                        '{{ $primaryColor }}',
                        '{{ $secondaryColor }}'
                    ],

                    stroke: {

                        curve: 'smooth',

                        width: 3

                    },

                    fill: {

                        type: 'gradient',

                        gradient: {

                            opacityFrom: 0.6,

                            opacityTo: 0.1

                        }

                    }

                }).render();



            /* =====================================
               DONUT CHART
            ===================================== */

            const donutSeries =
                @json($komposisiAset->pluck('total'));

            const donutLabels =
                @json($komposisiAset->pluck('nama_barang'));


            if (donutSeries.length > 0) {

                new ApexCharts(
                    document.querySelector("#chartDonutAset"), {

                        chart: {

                            type: 'donut',

                            height: 350

                        },

                        series: donutSeries,

                        labels: donutLabels,

                        colors: [

                            '#696cff',
                            '#ff9f43',
                            '#28c76f',
                            '#03c3ec',
                            '#ea5455',
                            '#7367f0',
                            '#00cfe8',
                            '#ff6b6b',
                            '#1dd1a1',
                            '#f368e0'

                        ],

                        stroke: {

                            width: 2

                        },

                        plotOptions: {

                            pie: {

                                donut: {

                                    size: '75%',

                                    labels: {

                                        show: true,

                                        name: {

                                            show: true,

                                            offsetY: -10,

                                            color: labelColor

                                        },

                                        value: {

                                            show: true,

                                            offsetY: 10,

                                            color: labelColor,

                                            fontWeight: 700,

                                            formatter: function(val) {

                                                return parseInt(val);

                                            }

                                        },

                                        total: {

                                            show: true,

                                            label: 'Total Stok',

                                            color: labelColor,

                                            formatter: function(w) {

                                                return w.globals.seriesTotals
                                                    .reduce((a, b) => a + b, 0);

                                            }

                                        }

                                    }

                                }

                            }

                        },

                        legend: {

                            position: 'bottom',

                            labels: {
                                colors: labelColor
                            }

                        },

                        dataLabels: {

                            enabled: false

                        },

                        responsive: [

                            {

                                breakpoint: 480,

                                options: {

                                    chart: {
                                        height: 300
                                    },

                                    legend: {
                                        position: 'bottom'
                                    }

                                }

                            }

                        ]

                    }).render();

            } else {

                document.querySelector("#chartDonutAset").innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                Tidak ada data aset
            </div>
        `;

            }



            /* =====================================
               ANIMATION
            ===================================== */

            document.querySelectorAll(".fade-up")
                .forEach((el, i) => {

                    setTimeout(() => {

                        el.classList.add("show");

                    }, 150 * i);

                });

        });
    </script>

@endsection
