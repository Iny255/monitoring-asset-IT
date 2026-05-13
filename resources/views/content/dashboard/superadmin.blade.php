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
            position: relative;
            transition: .3s;
            background: #fff;
            height: 100%;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
        }

        /* WAVE EFFECT */
        .wave-box {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: .12;
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

        .stat-label {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            font-size: .72rem;
            color: #32475c;
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
            background: linear-gradient(135deg, #0d3b66, #1d5fa3);
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

        .table-hover tbody tr:hover {
            background: #f8fafc;
        }

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

        {{-- HEADER --}}
        <div class="card dashboard-bg shadow-sm border-0 mb-4 fade-up">

            <div class="card-body d-flex align-items-center justify-content-between">

                <div>

                    <h3 class="text-white fw-bold mb-1">
                        Super Admin Dashboard 👑
                    </h3>

                    <p class="mb-0 opacity-75">
                        Monitoring seluruh aset IT dari
                        {{ $perusahaanCount }} perusahaan
                    </p>

                </div>

                <img src="{{ asset('assets/img/superadmin_logo.png') }}" height="120">

            </div>

        </div>


        {{-- SUMMARY --}}
        @php
            $summary = [
                [
                    'title' => 'Total Aset',
                    'count' => $totalAset ?? 0,
                    'icon' => 'bx-box',
                    'color' => '#696cff',
                    'bg' => 'bg-soft-primary',
                ],
                [
                    'title' => 'Dipinjam',
                    'count' => $dipinjam ?? 0,
                    'icon' => 'bx-transfer',
                    'color' => '#ff9f43',
                    'bg' => 'bg-soft-warning',
                ],
                [
                    'title' => 'Kembali',
                    'count' => $dikembalikan ?? 0,
                    'icon' => 'bx-check-circle',
                    'color' => '#28c76f',
                    'bg' => 'bg-soft-success',
                ],
                [
                    'title' => 'Total Mutasi',
                    'count' => $totalMutasi ?? 0,
                    'icon' => 'bx-git-compare',
                    'color' => '#03c3ec',
                    'bg' => 'bg-soft-info',
                ],
            ];
        @endphp


        <div class="row g-4 mb-4">

            @foreach ($summary as $item)
                <div class="col-xl-3 col-md-6 fade-up">

                    <div class="card card-stat shadow-sm border-0" style="color:{{ $item['color'] }}">

                        <div class="wave-box">
                            <div class="wave"></div>
                        </div>

                        <div class="card-body d-flex align-items-center">

                            <div class="icon-box {{ $item['bg'] }} me-3">
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


        {{-- CHART --}}
        <div class="row g-4">

            <div class="col-lg-8 fade-up">

                <div class="card shadow-sm border-0" style="border-radius:15px">

                    <div class="card-header">
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

                <div class="card shadow-sm border-0" style="border-radius:15px">

                    <div class="card-header">
                        <h5 class="mb-0">
                            Komposisi Aset
                        </h5>
                    </div>

                    <div class="card-body d-flex align-items-center justify-content-center">

                        <div id="chartDonutAset"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    {{-- ========================================= --}}
    {{-- KALKULASI ASET PERUSAHAAN --}}
    {{-- ========================================= --}}
    <div class="row mt-4">

        <div class="col-12 fade-up">

            <div class="card shadow-sm border-0" style="border-radius:15px">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="mb-0 fw-bold">
                            Kalkulasi Aset Perusahaan
                        </h5>

                        <small class="text-muted">
                            Monitoring aset seluruh perusahaan
                        </small>

                    </div>

                    <span class="badge bg-primary">
                        {{ $perusahaanCount }} Perusahaan
                    </span>

                </div>

                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr class="text-center">

                                <th width="60">
                                    No
                                </th>

                                <th class="text-start">
                                    Perusahaan
                                </th>

                                <th>
                                    Aset Masuk
                                </th>

                                <th>
                                    Aset Keluar
                                </th>

                                <th>
                                    Digunakan
                                </th>

                                <th>
                                    Total Aset
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($perusahaanList as $p)
                                <tr>

                                    <td class="text-center fw-semibold">

                                        {{ $loop->iteration }}

                                    </td>

                                    <td class="fw-semibold">

                                        {{ $p->nama_perusahaan }}

                                    </td>

                                    <td class="text-center">

                                        <span class="badge bg-success px-3 py-2">

                                            {{ number_format($p->aset_masuk) }}

                                        </span>

                                    </td>

                                    <td class="text-center">

                                        <span class="badge bg-danger px-3 py-2">

                                            {{ number_format($p->aset_keluar) }}

                                        </span>

                                    </td>

                                    <td class="text-center">

                                        <span class="badge bg-info px-3 py-2">

                                            {{ number_format($p->aset_digunakan) }}

                                        </span>

                                    </td>

                                    <td class="text-center">

                                        <span class="badge bg-primary px-3 py-2">

                                            {{ number_format($p->total_aset) }}

                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-4 text-muted">

                                        Belum ada data perusahaan

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

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
                document.documentElement.classList.contains("dark-style")

            const labelColor =
                isDark ? "#cbcbe2" : "#566a7f"


            /* AREA CHART */

            new ApexCharts(document.querySelector("#chartTransaksiAset"), {

                chart: {
                    height: 350,
                    type: 'area',

                    toolbar: {
                        show: false
                    },

                    fontFamily: 'Public Sans'
                },

                series: [{
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
                    labels: {
                        colors: labelColor
                    },
                    position: 'top',
                    horizontalAlign: 'right'
                },

                colors: [
                    '#696cff',
                    '#8592a3'
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

            }).render()



            /* DONUT */

            new ApexCharts(document.querySelector("#chartDonutAset"), {

                chart: {
                    type: 'donut',
                    height: 350
                },

                series: @json($komposisiAset->pluck('total')),

                labels: @json($komposisiAset->pluck('nama_barang')),

                colors: [
                    '#696cff',
                    '#ff9f43',
                    '#28c76f'
                ],

                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',

                            labels: {
                                show: true,

                                name: {
                                    show: true,
                                    color: labelColor,
                                    offsetY: -10
                                },

                                value: {
                                    show: true,
                                    color: labelColor,
                                    offsetY: 10,
                                    fontWeight: 700,

                                    formatter: function(val) {

                                        return parseInt(val)

                                    }
                                },

                                total: {
                                    show: true,
                                    label: 'Total Stok',
                                    color: labelColor,

                                    formatter: function(w) {

                                        return Number(
                                            w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                        );

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
                }

            }).render()



            /* ANIMATION */

            document.querySelectorAll(".fade-up")
                .forEach((el, i) => {

                    setTimeout(() => {
                        el.classList.add("show")
                    }, 150 * i)

                })

        })
    </script>

@endsection
