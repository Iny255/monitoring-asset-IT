
<div class="row mb-4">

    {{-- Area Chart --}}
    <div class="col-lg-8">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header border-0">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-1" style="color:var(--primary-theme)">

                            <i class="bx bx-line-chart me-2"></i>

                            Aktivitas Seluruh Perusahaan

                        </h5>

                        <small class="text-muted">

                            Grafik aktivitas seluruh perusahaan tahun {{ $dashboard['grafik']['tahun'] ?? request('tahun', now()->year) }}

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

                <div id="globalChart"></div>

            </div>

        </div>

    </div>

    {{-- Donut Chart --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header border-0">

                <h5 class="fw-bold mb-1" style="color:var(--primary-theme)">

                    <i class="bx bx-pie-chart-alt-2 me-2"></i>

                    Komposisi Inventaris

                </h5>

                <small class="text-muted">

                    Status inventaris seluruh perusahaan

                </small>

            </div>

            <div class="card-body">

                <div id="activityChart"></div>

            </div>

        </div>

    </div>

</div>
