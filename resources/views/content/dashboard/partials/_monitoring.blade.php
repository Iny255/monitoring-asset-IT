@php

    $mappingTotal = max($dashboard['mapping']['total'], 1);

    $mappingPercent = round(($dashboard['mapping']['aktif'] / $mappingTotal) * 100);

    $hakAksesTotal = max($dashboard['hak_akses']['total'], 1);

    $hakAksesPercent = round(($dashboard['hak_akses']['aktif'] / $hakAksesTotal) * 100);

    $maintenanceTotal = max($dashboard['maintenance']['total'], 1);

    $maintenancePercent = round(($dashboard['maintenance']['selesai'] / $maintenanceTotal) * 100);

    $peminjamanTotal = max($dashboard['peminjaman']['total'], 1);

    $peminjamanPercent = round(($dashboard['peminjaman']['dikembalikan'] / $peminjamanTotal) * 100);
@endphp
<div class="row mb-4">

    <div class="col-12">

        <div class="card monitoring-card border-0 shadow">

            <div class="card-header border-0 bg-transparent">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold text-white mb-1">
                            <i class="bx bx-radar me-2"></i>
                            Monitoring Operasional
                        </h5>

                        <small class="text-white-50">
                            Status operasional Asset Management System
                        </small>

                    </div>

                </div>

            </div>

            <div class="card-body">

                <div class="row g-4">

                    {{-- ================= MAPING ================= --}}
                    <div class="col-lg-6">

                        <div class="monitor-box">

                            <div class="row align-items-center">

                                {{-- Chart --}}
                                <div class="col-md-5 text-center">

                                    <div id="mappingChart"></div>

                                </div>

                                {{-- Statistik --}}
                                <div class="col-md-7">

                                    <h5 class="fw-bold text-white mb-3">

                                        <i class="bx bx-map me-2"></i>

                                        Mapping

                                    </h5>

                                    <div class="monitor-list">

                                        <div>

                                            <span>Aktif</span>

                                            <strong>{{ number_format($dashboard['mapping']['aktif']) }}</strong>

                                        </div>

                                        <div>

                                            <span>Service</span>

                                            <strong>{{ number_format($dashboard['mapping']['servis']) }}</strong>

                                        </div>

                                        <div>

                                            <span>Maintenance</span>

                                            <strong>{{ number_format($dashboard['mapping']['maintenance']) }}</strong>

                                        </div>

                                        <div>

                                            <span>Selesai</span>

                                            <strong>{{ number_format($dashboard['mapping']['selesai']) }}</strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ================= HAK AKSES ================= --}}
                    <div class="col-lg-6">

                        <div class="monitor-box">

                            <div class="row align-items-center">

                                <div class="col-md-4">

                                    <div id="hakAksesChart"></div>

                                </div>

                                <div class="col-md-8">

                                    <h5 class="text-white fw-bold mb-3">

                                        <i class="bx bx-lock-alt text-success"></i>

                                        Hak Akses

                                    </h5>

                                    <div class="monitor-list">

                                        <div>
                                            <span>Aktif</span>
                                            <strong>{{ $dashboard['hak_akses']['aktif'] }}</strong>
                                        </div>

                                        <div>
                                            <span>Nonaktif</span>
                                            <strong>{{ $dashboard['hak_akses']['nonaktif'] }}</strong>
                                        </div>

                                        <div>
                                            <span>Ditambah</span>
                                            <strong>{{ $dashboard['hak_akses']['ditambah'] }}</strong>
                                        </div>

                                        <div>
                                            <span>Diupdate</span>
                                            <strong>{{ $dashboard['hak_akses']['diupdate'] }}</strong>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ================= MAINTENANCE ================= --}}
                    <div class="col-lg-6">

                        <div class="monitor-box">

                            <div class="row align-items-center">

                                {{-- Radial Chart --}}
                                <div class="col-md-4 text-center">

                                    <div id="maintenanceChart"></div>

                                </div>

                                {{-- Statistik --}}
                                <div class="col-md-8">

                                    <h5 class="text-white fw-bold mb-3">

                                        <i class="bx bx-wrench text-danger me-2"></i>

                                        Maintenance

                                    </h5>

                                    <div class="monitor-list">

                                        <div>

                                            <span>Pengajuan</span>

                                            <strong>

                                                {{ $dashboard['maintenance']['pengajuan'] }}

                                            </strong>

                                        </div>

                                        <div>

                                            <span>Diproses</span>

                                            <strong>

                                                {{ $dashboard['maintenance']['diproses'] }}

                                            </strong>

                                        </div>

                                        <div>

                                            <span>Selesai</span>

                                            <strong>

                                                {{ $dashboard['maintenance']['selesai'] }}

                                            </strong>

                                        </div>

                                        <div>

                                            <span>Tidak Dapat Diperbaiki</span>

                                            <strong>

                                                {{ $dashboard['maintenance']['tidak_dapat_diperbaiki'] }}

                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- ================= PEMINJAMAN ================= --}}
                    <div class="col-lg-6">

                        <div class="monitor-box">

                            <div class="row align-items-center">

                                {{-- Radial Chart --}}
                                <div class="col-md-4 text-center">

                                    <div id="peminjamanChart"></div>

                                </div>

                                {{-- Statistik --}}
                                <div class="col-md-8">

                                    <h5 class="text-white fw-bold mb-3">

                                        <i class="bx bx-transfer-alt text-warning me-2"></i>

                                        Peminjaman

                                    </h5>

                                    <div class="monitor-list">

                                        <div>

                                            <span>Dipinjam</span>

                                            <strong>

                                                {{ $dashboard['peminjaman']['dipinjam'] }}

                                            </strong>

                                        </div>

                                        <div>

                                            <span>Dikembalikan</span>

                                            <strong>

                                                {{ $dashboard['peminjaman']['dikembalikan'] }}

                                            </strong>

                                        </div>

                                        <div>

                                            <span>Hilang</span>

                                            <strong>

                                                {{ $dashboard['peminjaman']['hilang'] }}

                                            </strong>

                                        </div>

                                        <div>

                                            <span>Total</span>

                                            <strong>

                                                {{ $dashboard['peminjaman']['total'] }}

                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@section('page-script')
    <script>
        function createRadial(selector, value) {

            var options = {

                chart: {
                    type: 'radialBar',
                    height: 140,
                    sparkline: {
                        enabled: true
                    }
                },

                series: [value],

                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '65%'
                        },

                        track: {
                            background: 'rgba(255,255,255,.15)'
                        },

                        dataLabels: {

                            name: {
                                show: false
                            },

                            value: {
                                fontSize: '28px',
                                fontWeight: 700,
                                color: '#fff'
                            }

                        }

                    }
                },

                stroke: {
                    lineCap: 'round'
                },

                colors: ['#ffffff']

            };

            new ApexCharts(
                document.querySelector(selector),
                options
            ).render();

        }

        createRadial('#mappingChart', {{ $mappingPercent }});

        createRadial(
            '#hakAksesChart',
            {{ $dashboard['hak_akses']['persentase'] }}
        );
        createRadial(
            '#maintenanceChart',
            {{ $dashboard['maintenance']['persentase'] }}
        );
        createRadial(
            '#peminjamanChart',
            {{ $dashboard['peminjaman']['persentase'] }}
        );
    </script>
@endsection
