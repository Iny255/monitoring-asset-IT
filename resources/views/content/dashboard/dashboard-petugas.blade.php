@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection
@section('vendor-style')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">

    <style>
        .dashboard-bg {

            background:
                linear-gradient(135deg,
                    var(--primary-theme),
                    var(--secondary-theme));

            border-radius: 18px;

            overflow: hidden;

            position: relative;

        }

        .dashboard-bg::before {

            content: '';

            position: absolute;

            right: -70px;

            top: -70px;

            width: 220px;

            height: 220px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .08);

        }

        .dashboard-bg::after {

            content: '';

            position: absolute;

            left: -60px;

            bottom: -60px;

            width: 180px;

            height: 180px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .05);

        }

        .dashboard-card {

            border: none;

            border-radius: 18px;

            transition: .25s;

            overflow: hidden;

        }

        .dashboard-card:hover {

            transform: translateY(-5px);

            box-shadow: 0 15px 35px rgba(0, 0, 0, .08);

        }

        .dashboard-label {

            font-size: .80rem;

            color: #7a7a7a;

            font-weight: 600;

        }

        .dashboard-number {

            font-size: 2rem;

            font-weight: 700;

            margin: 8px 0;

        }

        .dashboard-icon {

            width: 60px;

            height: 60px;

            border-radius: 16px;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #fff;

            font-size: 1.6rem;

            flex-shrink: 0;

        }

        .master-item {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 15px;

            border-radius: 14px;

            transition: .25s;

        }

        .master-item:hover {

            background: #f8f9fa;

        }

        .master-icon {

            width: 52px;

            height: 52px;

            border-radius: 14px;

            display: flex;

            justify-content: center;

            align-items: center;

            font-size: 24px;

        }



        .monitoring-card {

            background:
                linear-gradient(135deg,
                    var(--primary-theme),
                    var(--secondary-theme));

            border-radius: 20px;

            overflow: hidden;

            position: relative;

        }

        /* ==========================================
                                                                                   MONITORING CARD
                                                                                ========================================== */

        .monitor-box {

            background: rgba(255, 255, 255, .12);

            border: 1px solid rgba(255, 255, 255, .15);

            border-radius: 18px;

            padding: 22px;

            transition: .3s;

            height: 100%;
            position: relative;

            overflow: hidden;


        }

        .monitor-box::after {

            content: '';

            position: absolute;

            right: -40px;

            top: -40px;

            width: 120px;

            height: 120px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .05);

        }

        .monitor-box:hover {

            transform: translateY(-6px);

            background: rgba(255, 255, 255, .18);

        }

        .monitor-box h5 {

            color: #fff;

            font-weight: 700;

        }

        .monitor-list {

            margin-top: 10px;

        }

        .monitor-list div {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 8px 0;

            border-bottom: 1px dashed rgba(255, 255, 255, .15);

        }

        .monitor-list div:last-child {

            border-bottom: none;

        }

        .monitor-list span {

            color: rgba(255, 255, 255, .85);

        }

        .monitor-list strong {

            color: #fff;

            font-weight: 700;

            font-size: 15px;

        }

        #mappingChart,
        {
        min-height: 140px;
        }

        #hakAksesChart,
        {
        min-height: 140px;

        }

        #maintenanceChart,
        {
        min-height: 140px;
        }

        #peminjamanChart {

            min-height: 140px;

        }

        #transaksiChart {

            min-height: 320px;

        }

        #grafikBulanan {

            min-height: 350px;

        }

        #komposisiChart {

            min-height: 350px;

        }

        .timeline-custom {

            position: relative;

        }

        .timeline-custom::before {

            content: '';

            position: absolute;

            left: 18px;

            top: 0;

            bottom: 0;

            width: 2px;

            background: #e5e7eb;

        }

        .timeline-item {

            display: flex;

            margin-bottom: 30px;

            position: relative;

        }

        .timeline-dot {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #fff;

            z-index: 2;

            flex-shrink: 0;

        }

        .timeline-content {

            margin-left: 20px;

            width: 100%;

            background: #fff;

            border-radius: 12px;

            padding: 14px 18px;

            border: 1px solid #eef2f7;

            transition: .25s;

        }

        .timeline-content:hover {

            transform: translateX(5px);

            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);

        }

        .timeline-scroll {

            max-height: 360px;

            overflow-y: auto;

        }

        .timeline-mini {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 14px 0;

            border-bottom: 1px solid #eef2f7;

        }

        .timeline-mini:last-child {

            border-bottom: none;

        }

        .timeline-icon {

            width: 42px;

            height: 42px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #fff;

            flex-shrink: 0;

        }

        .timeline-info {

            flex: 1;

        }
    </style>

@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        @include('content.dashboard.partials._header')

        @include('content.dashboard.partials._summary')

        {{-- @include('content.dashboard.partials._master') --}}
        @include('content.dashboard.partials._chart')
        @include('content.dashboard.partials._monitoring')

        @include('content.dashboard.partials._reminder')
        @include('content.dashboard.partials._transaksi')




    </div>

@endsection
@section('scripts')
    @php

        $kategori = collect($dashboard['komposisi'])->pluck('nama_barang');

        $totalKategori = collect($dashboard['komposisi'])->pluck('total');

    @endphp
    <script>
        var options = {

            chart: {

                type: 'bar',

                height: 330,

                toolbar: {
                    show: false
                }

            },

            series: [{

                name: 'Transaksi',

                data: @json($dashboard['transaksi_chart']['data'])

            }],

            plotOptions: {

                bar: {

                    horizontal: true,

                    borderRadius: 8,

                    barHeight: '55%',

                    distributed: true

                }

            },

            dataLabels: {

                enabled: true

            },

            xaxis: {

                categories: @json($dashboard['transaksi_chart']['label'])

            },

            legend: {

                show: false

            },

            grid: {

                borderColor: '#e9ecef'

            }

        };

        new ApexCharts(
            document.querySelector("#transaksiChart"),
            options
        ).render();
        var bulananOptions = {

            chart: {

                type: 'area',

                height: 360,

                toolbar: {
                    show: false
                },

                zoom: {
                    enabled: false
                }

            },

            series: [

                {

                    name: 'Penerimaan',

                    data: @json($dashboard['grafik']['masuk'])

                },

                {

                    name: 'Pemakaian',

                    data: @json($dashboard['grafik']['keluar'])

                }

            ],

            colors: [

                '#28C76F',

                '#FF9F43'

            ],

            stroke: {

                curve: 'smooth',

                width: 3

            },

            fill: {

                type: 'gradient',

                gradient: {

                    shadeIntensity: 1,

                    opacityFrom: .45,

                    opacityTo: .05,

                    stops: [0, 90, 100]

                }

            },

            dataLabels: {

                enabled: false

            },

            markers: {

                size: 5,

                strokeWidth: 2,

                hover: {
                    size: 7
                }

            },

            grid: {

                borderColor: '#ebeef2',

                strokeDashArray: 5

            },

            xaxis: {

                categories: @json($dashboard['grafik']['label']),

                axisBorder: {
                    show: false
                },

                axisTicks: {
                    show: false
                }

            },

            yaxis: {

                min: 0

            },

            legend: {

                position: 'top',

                horizontalAlign: 'right'

            },

            tooltip: {

                shared: true,

                intersect: false

            }

        };


        new ApexCharts(
            document.querySelector("#grafikBulanan"),
            bulananOptions
        ).render();

        function hexToRgb(hex) {

            hex = hex.replace('#', '');

            let bigint = parseInt(hex, 16);

            return {
                r: (bigint >> 16) & 255,
                g: (bigint >> 8) & 255,
                b: bigint & 255
            };
        }

        function lightenColor(hex, percent) {

            const rgb = hexToRgb(hex);

            const r = Math.min(255, Math.round(rgb.r + (255 - rgb.r) * percent));
            const g = Math.min(255, Math.round(rgb.g + (255 - rgb.g) * percent));
            const b = Math.min(255, Math.round(rgb.b + (255 - rgb.b) * percent));

            return `rgb(${r}, ${g}, ${b})`;
        }
        const rootStyle = getComputedStyle(document.documentElement);

        const primaryTheme = rootStyle
            .getPropertyValue('--primary-theme')
            .trim();

        const secondaryTheme = rootStyle
            .getPropertyValue('--secondary-theme')
            .trim();
        var donutOptions = {

            chart: {

                type: 'donut',

                height: 360

            },

            series: @json($totalKategori),

            labels: @json($kategori),
            colors: [

                primaryTheme,

                secondaryTheme,

                lightenColor(primaryTheme, 0.15),

                lightenColor(primaryTheme, 0.30),

                lightenColor(primaryTheme, 0.45),

                lightenColor(primaryTheme, 0.60),

                lightenColor(primaryTheme, 0.75),

            ],

            legend: {

                position: 'bottom'

            },

            dataLabels: {

                enabled: true

            },

            stroke: {

                width: 2

            },

            plotOptions: {

                pie: {

                    donut: {

                        size: '68%',

                        labels: {

                            show: true,

                            total: {

                                show: true,

                                label: 'Total',

                                formatter: function() {

                                    return {{ collect($dashboard['komposisi'])->sum('total') }};

                                }

                            }

                        }

                    }

                }

            },

            tooltip: {

                y: {

                    formatter: function(val) {

                        return val + " Unit";

                    }

                }

            }

        };

        new ApexCharts(
            document.querySelector("#komposisiChart"),
            donutOptions
        ).render();
    </script>
@endsection
