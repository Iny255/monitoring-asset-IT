@extends('layouts/contentNavbarLayout')


@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('title', 'Pertumbuhan')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <a href="/dashboard/pertumbuhan" class="btn btn-secondary mb-3">Kembali</a>
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h4 class="card-title">Pertumbuhan Balita</h4>
                {{-- <a href="{{ route('anthropometri.observasi', $data->id) }}" class="btn btn-primary">Detail Observasi</a> --}}
            </div>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-md-6 mb-1">
                    <p><strong>Nama Balita:</strong>
                        {{ $data->pendaftaran->nama_balita ?? 'Tidak diketahui' }}
                    </p>
                </div>
                <div class="col-md-6 mb-1">
                        <p><strong>Usia saat ini:</strong> {{$lastUsia ?? $data->usia ?? 'Tidak diketahui' }} bulan</p>
                    </div>
                    <div class="col-md-6 mb-1">
                        <p><strong>Jenis Kelamin:</strong>
                            {{ $data->jenis_kelamin === 'perempuan' ? 'Perempuan' : ($data->jenis_kelamin === 'laki-laki' ? 'Laki-Laki' : 'Tidak diketahui') }}
                        </p>

                    </div>
                    <div class="col-md-6 mb-1">
                        <p><strong>BB/TB terakhir:</strong>
                            {{ $lastBbTb->berat_badan ?? 'Tidak diketahui' }}kg/{{ $lastBbTb->tinggi_badan ?? 'Tidak diketahui' }}cm
                        </p>
                    </div>
            </div>
            <hr>

            @if ($pertumbuhanRecords)
                {{-- <div class="container">
                        <div class="row my-3">
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('pertumbuhan.ortu', $data->id) }}"
                                    class="d-flex align-items-center">
                                    <div class="me-2">
                                        <h6 class="mb-0">Pilih Bulan</h6>
                                    </div>
                                    <div class="me-2 flex-grow-1">
                                        <select name="month" class="form-select" id="month-select">
                                            @foreach ($getAllMonthsBased as $month)
                                                <option value="{{ $month }}"
                                                    {{ $month == $selectedMonth ? 'selected' : '' }}>
                                                    {{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                <div class="col-md-12 mb-3" id="chartStat"></div>

                {{-- <div id="description">
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead class="table-secondary">
                                    <tr>
                                        <th colspan="2" class="text-center"><strong>Kategori Rentang Ambang Batas
                                                (Z-Score)
                                                :</strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <td id="tinggi" class="color-tall">Tinggi (+3 SD)</td>
                                    </tr>
                                    <tr>
                                        <td id="normal" class="color-normal">Normal (-2 SD sd +3 SD)</td>
                                    </tr>
                                    <tr>
                                        <td id="stunted" class="color-stunted">Pendek/stunted (-3 SD sd < -2 SD)</td>
                                    </tr>

                                    <tr>
                                        <td id="severely-stunted" class="color-severely-stunted">Sangat pendek/severely
                                            stunted ( < -3 SD)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                       <div class="col-md-12 mb-3" id="chartStat"></div>
                    @if ($status_stunting == 'normal')
                        <div class="alert alert-info">
                            <h2 class="text-center p-0 m-0 text-info">Tidak Stunting / Normal</h2>
                        </div>
                    @elseif($status_stunting == 'tinggi')
                        <div class="alert alert-warning">
                            <h2 class="text-center p-0 m-0 text-warning">Tinggi / Melebihi Batas Normal</h2>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h2 class="text-center p-0 m-0 text-danger">Stunting</h2>
                        </div>
                    @endif
            @else
                <div class="text-center my-3">
                    <h5>Grafik Pertumbuhan tidak ditemukan.</h5>
                    <p>Usia anak melebihi batas maksimal untuk kategori balita (60 bulan), silahkan lanjutkan pemantauan
        melalui layanan kesehatan anak usia sekolah.</p>
                </div>
            @endif


            {{-- <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var zScores = @json($zScore);

                        var element;

                        if (zScores > -2 && zScores <= 3) {
                            element = document.getElementById('normal');
                            element.style.border = '2px solid blue';
                            element.style.fontWeight = '900';
                            element.style.backgroundColor = 'rgba(0, 0, 255, 0.2)';
                        } else if (zScores >= 3) {
                            element = document.getElementById('tinggi');
                            element.style.border = '2px solid orange';
                            element.style.fontWeight = '900';
                            element.style.backgroundColor = 'rgba(250, 196, 24, 0.5)';
                        } else if (zScores <= -2 && zScores > -3) {
                            element = document.getElementById('stunted');
                            element.style.border = '2px solid red';
                            element.style.fontWeight = '900';
                            element.style.backgroundColor = 'rgba(255, 0, 0, 0.2)';
                        } else if (zScores <= -3) {
                            element = document.getElementById('severely-stunted');
                            element.style.border = '2px solid darkred';
                            element.style.fontWeight = '900';
                            element.style.color = 'red';
                            element.style.backgroundColor = 'rgba(139, 0, 0, 0.3)';
                        }
                    });
                </script> --}}
           <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var zScoreLimits = @json($zScoreLimits);
                        var growthData = @json($growthData);
                        var last12Months = @json($last12Months);

                        var zScores = last12Months.map(month => {
                            var record = growthData.find(data => data.month === month);
                            return record ? parseFloat(parseFloat(record.z_score).toFixed(2)) : null;
                        });

                        var isStunting = zScores.some(score => score < -2);

                        var lineColor = zScores.some(score => score < -2) ? '#f54242' : '#0a48cc';

                        var options = {
                            chart: {
                                height: 400,
                                type: "line",
                            },
                            markers: {
                                size: 5,
                                colors: ['#ffffff'],
                                strokeColors: lineColor,
                                strokeWidth: 3,
                                hover: {
                                    size: 7
                                }
                            },
                            series: [{
                                name: "Z-Score",
                                data: zScores,
                                color: lineColor,
                            }],
                            stroke: {
                                curve: 'straight',
                                width: 4,
                                colors: [lineColor]
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                xaxis: {
                                    lines: {
                                        show: false
                                    }
                                },
                                yaxis: {
                                    lines: {
                                        show: true
                                    }
                                }
                            },
                            title: {
                                text: 'Grafik Pertumbuhan Balita 12 Bulan Terakhir',
                                align: 'left',
                                style: {
                                    fontSize: '16px',
                                    fontWeight: 'bold'
                                }
                            },
                            xaxis: {
                                categories: last12Months,
                                title: {
                                    text: "Bulan",
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 'bold'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: '12px',
                                    }
                                }
                            },
                            yaxis: {
                                min: -10,
                                max: 10,
                                labels: {
                                    formatter: function(value) {
                                        if (value > 3) return "Tinggi";
                                        if (value > -2 && value <= 3) return "Normal";
                                        return "Stunting";
                                    },
                                    style: {
                                        fontSize: '12px',
                                    }
                                },
                                title: {
                                    text: "Status Gizi",
                                    align: 'left',
                                    style: {
                                        fontSize: '13px',
                                        fontWeight: 'bold'
                                    }
                                }
                            },
                            annotations: {
                                yaxis: [{
                                    y: -2,
                                    borderColor: '#f54242',
                                    label: {
                                        text: 'Batas Stunting',
                                        style: {
                                            color: '#fff',
                                            background: '#f54242'
                                        },
                                        position: 'right'
                                    }
                                }, {
                                    y: 3,
                                    borderColor: '#f54242',
                                    label: {
                                        text: 'Batas Normal',
                                        style: {
                                            color: '#fff',
                                            background: '#f54242'
                                        },
                                        position: 'right'
                                    }
                                }]
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#chartStat"), options);
                        chart.render();
                    });
                </script>
        </div>
    </div>
@endsection
