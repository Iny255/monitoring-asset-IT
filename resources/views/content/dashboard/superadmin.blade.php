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

        #chartTransaksiAset,
        #chartDonutAset {
            width: 100% !important;
            min-height: 320px;
        }

        .company-filter {
            border-radius: 12px;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }
    </style>
@endsection


{{-- ================= CONTENT ================= --}}
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- COMPANY FILTER --}}
        <div class="row mb-4">
            <div class="col-12 fade-up">
                <form method="GET" class="company-filter shadow-sm p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Filter Perusahaan</label>
                            <select name="perusahaan_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Perusahaan</option>
                                @foreach($perusahaanList as $perusahaan)
                                    <option value="{{ $perusahaan->id }}" {{ $perusahaanId == $perusahaan->id ? 'selected' : '' }}>
                                        {{ $perusahaan->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small">
                                Menampilkan data {{ $perusahaanId ? 'Perusahaan ' . $perusahaanList->where('id', $perusahaanId)->first()->nama_perusahaan : 'SELAYA PERUSAHAAN' }}
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- WELCOME --}}
        <div class="row mb-4">
            <div class="col-12 fade-up">
                <div class="card dashboard-bg shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="text-white mb-1">
                                Selamat Datang, {{ Str::upper(auth()->user()->name) }}! 👋
                            </h4>
                            <p class="mb-0 opacity-75">
                                Dashboard Super Admin - Lihat semua data aset dari seluruh perusahaan
                            </p>
                        </div>
                        <img src="{{ asset('assets/img/illustrations/superhero-light.png') }}" height="120" alt="Super Admin">
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        @php
            $summary = [
                [
                    'title' => 'Total Aset',
                    'count' => $totalAset,
                    'icon' => 'bx-box',
                    'color' => '#696cff',
                    'bg' => 'bg-soft-primary',
                ],
                [
                    'title' => 'Dipinjam',
                    'count' => $dipinjam,
                    'icon' => 'bx-transfer',
                    'color' => '#ff9f43',
                    'bg' => 'bg-soft-warning',
                ],
                [
                    'title' => 'Kembali',
                    'count' => $dikembalikan,
                    'icon' => 'bx-check-circle',
                    'color' => '#28c76f',
                    'bg' => 'bg-soft-success',
                ],
                [
                    'title' => 'Total Mutasi',
                    'count' => $totalMutasi,
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
                                <span class="stat-label">{{ $item['title'] }}</span>
                                <h4>{{ number_format($item['count'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- GLOBAL STATS --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6 fade-up">
                <div class="card shadow-sm border-0" style="border-radius:15px">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Total Perusahaan</h5>
                        <span class="badge badge-light-primary ms-auto">{{ $perusahaanCount }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <h3>{{ $petugasCount }}</h3>
                                <small class="text-muted">Petugas</small>
                            </div>
                            <div class="col-6">
                                <h3>{{ $managerCount }}</h3>
                                <small class="text-muted">Manager</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 fade-up">
                <div class="card shadow-sm border-0" style="border-radius:15px; background: linear-gradient(135deg, #f8fafc, #f1f5f9)">
                    <div class="card-body text-center p-4">
                        <h4 class="mb-1">{{ $perusahaanCount }}</h4>
                        <span class="text-muted">Perusahaan Aktif</span>
                        <div class="mt-3">
                            <small class="text-success">✅ Semua perusahaan dapat diakses</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHARTS --}}
        <div class="row g-4">
            {{-- AREA CHART --}}
            <div class="col-lg-8 fade-up">
                <div class="card shadow-sm border-0" style="border-radius:15px">
                    <div class="card-header">
                        <h5 class="mb-0">Grafik Transaksi {{ $perusahaanId ? 'Perusahaan' : 'Global' }}</h5>
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
                        <h5 class="mb-0">Stok Aset</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartDonutAset"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


{{-- ================= SCRIPTS ================= --}}
@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const isDark = document.documentElement.classList.contains("dark-style")
            const labelColor = isDark ? "#cbcbe2" : "#566a7f"

            /* AREA CHART */
            new ApexCharts(document.querySelector("#chartTransaksiAset"), {
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: { show: false },
                    fontFamily: 'Public Sans'
                },
                series: [{
                    name: 'Masuk',
                    data: @json($dataMasuk)
                }, {
                    name: 'Keluar',
                    data: @json($dataKeluar)
                }],
                xaxis: {
                    categories: @json($bulanLabel),
                    labels: { style: { colors: labelColor } }
                },
                yaxis: { labels: { style: { colors: labelColor } } },
                legend: {
                    labels: { colors: labelColor },
                    position: 'top',
                    horizontalAlign: 'right'
                },
                colors: ['#696cff', '#8592a3'],
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: { opacityFrom: 0.6, opacityTo: 0.1 }
                }
            }).render()

            /* DONUT CHART */
            new ApexCharts(document.querySelector("#chartDonutAset"), {
                chart: { type: 'donut', height: 320 },
                series: [{{ $totalLaptop }}, {{ $totalPrinter }}, {{ $totalHp }}],
                labels: ['Laptop', 'Printer', 'HP/Tablet'],
                colors: ['#696cff', '#ff9f43', '#28c76f'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: true, color: labelColor, offsetY: -10 },
                                value: { show: true, color: labelColor, offsetY: 10, fontWeight: 700 },
                                total: { show: true, label: 'Total Aset', color: labelColor }
                            }
                        }
                    }
                },
                legend: { position: 'bottom', labels: { colors: labelColor } },
                dataLabels: { enabled: false }
            }).render()

            /* ANIMATION */
            document.querySelectorAll(".fade-up").forEach((el, i) => {
                setTimeout(() => el.classList.add("show"), 150 * i)
            })
        })
    </script>
@endsection

