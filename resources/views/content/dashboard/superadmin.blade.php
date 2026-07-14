@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Super Admin')


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

        .company-card {

            border-radius: 18px;

            overflow: hidden;

            background: #fff;

            transition: .3s;

            border: 1px solid #eef2f7;

            height: 100%;

        }

        .company-card:hover {

            transform: translateY(-6px);

            box-shadow: 0 18px 35px rgba(0, 0, 0, .08);

        }

        .company-header {

            padding: 20px;

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .company-icon {

            width: 55px;

            height: 55px;

            border-radius: 14px;

            background: rgba(255, 255, 255, .18);

            display: flex;

            align-items: center;

            justify-content: center;

            color: #fff;

            font-size: 28px;

        }

        .company-body {

            padding: 18px;

        }

        .company-item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 10px 0;

            border-bottom: 1px dashed #ececec;

        }

        .company-item:last-child {

            border-bottom: none;

        }

        .timeline-scroll {
            max-height: 420px;
            overflow-y: auto;
        }

        .timeline-mini {
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .timeline-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            flex-shrink: 0;
        }

        .timeline-info {
            flex: 1;
        }
    </style>

@endsection



@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Executive Header --}}
        @include('content.dashboard.superadmin._header')

        {{-- Executive Summary --}}
        @include('content.dashboard.superadmin._summary')
        {{-- Analisis Global --}}
        @include('content.dashboard.superadmin._chart')

        {{-- Monitoring Perusahaan --}}
        @include('content.dashboard.superadmin._company')

        <div class="row mb-4">

            <div class="col-lg-8">

                @include('content.dashboard.superadmin._timeline')

            </div>

            <div class="col-lg-4">

                @include('content.dashboard.superadmin._transaksi')

            </div>

        </div>


                {{-- Reminder --}}
                @include('content.dashboard.superadmin._reminder')

         



        </div>

    </div>

@endsection
@section('scripts')
    @php

        $kategori = collect($dashboard['komposisi'])->pluck('kategori');

        $totalKategori = collect($dashboard['komposisi'])->pluck('total');

    @endphp

    <script>
        const globalChart = new ApexCharts(
            document.querySelector("#globalChart"), {

                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },

                stroke: {
                    curve: 'smooth',
                    width: 3
                },

                dataLabels: {
                    enabled: false
                },

                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: .35,
                        opacityTo: .05
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
                    },

                    {
                        name: 'Mutasi',
                        data: @json($dashboard['grafik']['mutasi'])
                    },

                    {
                        name: 'Maintenance',
                        data: @json($dashboard['grafik']['maintenance'])
                    },

                    {
                        name: 'Peminjaman',
                        data: @json($dashboard['grafik']['peminjaman'])
                    }

                ],

                xaxis: {

                    categories: @json($dashboard['grafik']['bulan'])

                },

                legend: {

                    position: 'top'

                },

                colors: [

                    '#696cff',

                    '#71dd37',

                    '#03c3ec',

                    '#ff3e1d',

                    '#ffab00'

                ]

            });

        globalChart.render();
        const activityChart = new ApexCharts(
            document.querySelector("#activityChart"), {

                chart: {
                    type: 'donut',
                    height: 320
                },

                labels: @json($kategori),

                series: @json($totalKategori),

                legend: {
                    position: 'bottom'
                },

                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%'
                        }
                    }
                },

                dataLabels: {
                    enabled: true
                },

                colors: [
                    '#696cff',
                    '#71dd37',
                    '#03c3ec',
                    '#ffab00',
                    '#ff3e1d',
                    '#8592a3',
                    '#0d6efd',
                    '#20c997'
                ]

            });

        activityChart.render();
    </script>

@endsection
