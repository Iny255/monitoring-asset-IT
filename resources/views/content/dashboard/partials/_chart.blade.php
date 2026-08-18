<div class="row mb-4">

    {{-- Aktivitas Bulanan --}}
    <div class="col-lg-8">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header border-0 pb-0">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-1" style="color:var(--primary-theme)">

                            <i class="bx bx-line-chart me-2"></i>

                            Aktivitas Bulanan

                        </h5>

                        <small class="text-muted">

                            Perbandingan penerimaan dan pemakaian aset selama 12 bulan.

                        </small>

                    </div>

                    <form method="GET" action="" class="d-inline-block mb-0">
                        @foreach(request()->except('tahun') as $key => $val)
                            @if(is_array($val))
                                @foreach($val as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @elseif($val !== null)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endif
                        @endforeach
                        <select name="tahun" onchange="this.form.submit()" class="form-select form-select-sm fw-bold border-primary text-primary">
                            @php
                                $selectedTahun = $dashboard['grafik']['tahun'] ?? request('tahun', now()->year);
                                $years = $dashboard['grafik']['available_years'] ?? [now()->year];
                            @endphp
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                </div>

            </div>
            <div class="card-body">

                <div id="grafikBulanan"></div>

            </div>

        </div>

    </div>

    {{-- Komposisi Inventaris --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-1" style="color: var(--primary-theme);">

                    <i class="bx bx-pie-chart-alt-2 me-2"></i>

                    Komposisi Inventaris

                </h5>

                <small class="text-muted">

                    Aset tersedia berdasarkan kategori

                </small>

            </div>

            <div class="card-body">

                <div id="komposisiChart"></div>

            </div>

        </div>

    </div>

</div>
