@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                        name: 'Total Pendaftar',
                        data: [{{ $totalLaki }}]
                    },
                    {
                        name: 'Total Pendaftar',
                        data: [{{ $totalPerempuan }}]
                    }
                ],
                xaxis: {
                    categories: ['Laki-laki', 'Perempuan'],
                    title: {
                        text: 'Jenis Kelamin'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total'
                    }
                },
                title: {
                    text: 'Jumlah Balita Terdaftar Posyandu',
                    align: 'center'
                },
                colors: ['#00E396', '#FF6600']
            };

            var chart = new ApexCharts(document.querySelector("#chartTotalDaftar"), options);
            chart.render();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                        name: 'Stunting',
                        type: 'line',
                        data: @json($data['stunting'])
                    },
                    {
                        name: 'Normal/Tidak Stunting',
                        type: 'line',
                        data: @json($data['normal'])
                    }
                ],
                chart: {
                    height: 350,
                    type: 'line'
                },
                colors: ['#FF4560', '#00E396'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: @json($data['months']),
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Anak'
                    },
                    labels: {
                        formatter: function(value) {
                            return Math.round(value);
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'center'
                },
                title: {
                    text: 'Kasus Stunting Balita Cendono'
                }
            };

            var chart = new ApexCharts(document.querySelector("#chartStunting"), options);
            chart.render();
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Selamat datang {{ Str::upper(auth()->user()->username) }}
                            </h5>
                            <h6 class="mb-3">{{ $now }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div id="chartTotalDaftar"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div id="chartStunting"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
