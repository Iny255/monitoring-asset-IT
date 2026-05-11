<div>

    @php
        $lastDate = null;
    @endphp

    @forelse($histories as $h)
        {{-- ========================================= --}}
        {{-- HEADER TANGGAL --}}
        {{-- ========================================= --}}
        @if ($lastDate != $h->tanggal_mutasi)
            <div class="mt-4 mb-2">

                <span class="badge bg-primary px-3 py-2 shadow-sm">

                    {{ \Carbon\Carbon::parse($h->tanggal_mutasi)->format('d M Y') }}

                </span>

            </div>

            @php
                $lastDate = $h->tanggal_mutasi;
            @endphp
        @endif


        {{-- ========================================= --}}
        {{-- CARD --}}
        {{-- ========================================= --}}
        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">

            <div class="card-body py-3">

                {{-- ========================================= --}}
                {{-- HEADER CARD --}}
                {{-- ========================================= --}}
                <div class="d-flex justify-content-end mb-3">

                    <button type="button" class="btn btn-danger btn-sm shadow position-absolute"
                        onclick="hapusHistory({{ $h->id }})"
                        style="
                        top:15px;
                        right:15px;
                        z-index:999999;
                        cursor:pointer;
                    ">

                        <i class="bx bx-trash"></i>

                    </button>

                </div>


                {{-- ========================================= --}}
                {{-- CONTENT --}}
                {{-- ========================================= --}}
                <div class="row small">

                    {{-- LOKASI --}}
                    <div class="col-md-6 mb-3">

                        <div class="fw-bold text-dark mb-1">
                            Lokasi
                        </div>

                        <div>

                            {{ optional($h->dariLokasi)->nama_lokasi ?? '-' }}

                            <span class="mx-1">→</span>

                            <span class="text-primary fw-semibold">

                                {{ optional($h->keLokasi)->nama_lokasi ?? '-' }}

                            </span>

                        </div>

                    </div>


                    {{-- PERUSAHAAN --}}
                    <div class="col-md-6 mb-3">

                        <div class="fw-bold text-dark mb-1">
                            Perusahaan
                        </div>

                        <div>

                            {{ optional($h->dariPerusahaan)->nama_perusahaan ?? '-' }}

                            <span class="mx-1">→</span>

                            <span class="text-primary fw-semibold">

                                {{ optional($h->kePerusahaan)->nama_perusahaan ?? '-' }}

                            </span>

                        </div>

                    </div>


                    {{-- KARYAWAN --}}
                    <div class="col-md-6 mb-3">

                        <div class="fw-bold text-dark mb-1">
                            Karyawan
                        </div>

                        <div>

                            {{ optional($h->dariKaryawan)->nama_karyawan ?? '-' }}

                            <span class="mx-1">→</span>

                            <span class="text-primary fw-semibold">

                                {{ optional($h->keKaryawan)->nama_karyawan ?? '-' }}

                            </span>

                        </div>

                    </div>


                    {{-- INVENTARIS --}}
                    <div class="col-md-6 mb-3">

                        <div class="fw-bold text-dark mb-1">
                            Inventaris
                        </div>

                        <div>

                            <span class="badge bg-secondary">

                                {{ $h->dari_no_inventaris ?? '-' }}

                            </span>

                            <span class="mx-1">→</span>

                            <span class="badge bg-success">

                                {{ $h->ke_no_inventaris ?? '-' }}

                            </span>

                        </div>

                    </div>


                    {{-- APLIKASI --}}
                    <div class="col-md-6 mb-3">

                        <div class="fw-bold text-dark mb-1">
                            Aplikasi
                        </div>

                        <div>

                            {{ $h->dari_aplikasi ?? '-' }}

                            <span class="mx-1">→</span>

                            <span class="text-primary fw-semibold">

                                {{ $h->ke_aplikasi ?? '-' }}

                            </span>

                        </div>

                    </div>


                    {{-- DATA PPN --}}
                    <div class="col-md-6 mb-3">

                        <div class="fw-bold text-dark mb-1">
                            Data PPN
                        </div>

                        <div>

                            {{ $h->dari_data_ppn ?? '-' }}

                            <span class="mx-1">→</span>

                            <span class="text-primary fw-semibold">

                                {{ $h->ke_data_ppn ?? '-' }}

                            </span>

                        </div>

                    </div>


                    {{-- DATA NON PPN --}}
                    <div class="col-md-6 mb-2">

                        <div class="fw-bold text-dark mb-1">
                            Data Non PPN
                        </div>

                        <div>

                            {{ $h->dari_data_non_ppn ?? '-' }}

                            <span class="mx-1">→</span>

                            <span class="text-primary fw-semibold">

                                {{ $h->ke_data_non_ppn ?? '-' }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @empty

        <div class="text-center text-muted py-5">

            <i class="bx bx-data fs-1 d-block mb-2"></i>

            Belum ada history mutasi

        </div>
    @endforelse

</div>
