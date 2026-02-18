@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('content')

    <div class="row">

        {{-- WELCOME CARD --}}
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="d-flex align-items-end row">

                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                Selamat datang {{ Str::upper(auth()->user()->name) }} 👋
                            </h5>
                            <h6 class="mb-2">{{ $now ?? '-' }}</h6>
                            <p class="mb-0 text-muted">
                                Sistem Manajemen Aset & Peminjaman Barang
                            </p>
                        </div>
                    </div>

                    <div class="col-sm-5 text-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                alt="dashboard" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- SUMMARY CARD --}}
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Aset</span>
                    <h3 class="card-title mb-2">{{ $totalAset ?? 0 }}</h3>
                    <small class="text-muted">Semua aset terdaftar</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Dipinjam</span>
                    <h3 class="card-title text-warning mb-2">{{ $dipinjam ?? 0 }}</h3>
                    <small class="text-muted">Aset sedang dipinjam</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Dikembalikan</span>
                    <h3 class="card-title text-success mb-2">{{ $dikembalikan ?? 0 }}</h3>
                    <small class="text-muted">Aset sudah kembali</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1">Total Mutasi</span>
                    <h3 class="card-title mb-2">{{ $totalMutasi ?? 0 }}</h3>
                    <small class="text-muted">Semua aset yang dimutasi</small>
                </div>
            </div>
        </div>


        {{-- ROW CHART --}}
        <div class="row mt-4">

            {{-- CHART MUTASI (KIRI) --}}
            <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Transaksi Aset Perbulan</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartMutasi" style="min-height:350px;"></div>
                    </div>
                </div>
            </div>

            {{-- CHART ASET TYPE (KANAN) --}}
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Komposisi Aset</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="chartType" style="min-height:320px;"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- APEX CHART --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                /* ================= CHART TRANSAKSI BULANAN ================= */
                const elMutasi = document.querySelector("#chartMutasi");

                if (elMutasi) {
                    const chartMutasi = new ApexCharts(elMutasi, {
                        chart: {
                            type: 'line',
                            height: 350,
                            toolbar: {
                                show: false
                            }
                        },
                        series: [{
                                name: 'Aset Masuk',
                                data: @json($dataMasuk ?? [])
                            },
                            {
                                name: 'Aset Keluar',
                                data: @json($dataKeluar ?? [])
                            }
                        ],
                        xaxis: {
                            categories: @json($bulanLabel ?? [])
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        markers: {
                            size: 4
                        }
                    });

                    chartMutasi.render();
                }


                /* ================= CHART ASET PER TYPE ================= */
                const elType = document.querySelector("#chartType");

                const laptop = Number(@json($totalLaptop ?? 0));
                const printer = Number(@json($totalPrinter ?? 0));
                const hp = Number(@json($totalHp ?? 0));

                if (elType) {
                    const chartType = new ApexCharts(elType, {
                        chart: {
                            type: 'donut',
                            height: 320
                        },
                        series: [laptop, printer, hp],
                        labels: ['Laptop', 'Printer', 'HP / Tablet'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true
                        },
                        noData: {
                            text: 'Tidak ada data'
                        }
                    });

                    chartType.render();
                }

            });
        </script>

    @endsection
