<div>

    @php
        $lastDate = null;
    @endphp

    @forelse($histories as $h)
        {{-- HEADER TANGGAL --}}
        @if ($lastDate != $h->tanggal_mutasi)
            <div class="mt-4 mb-2">
                <span class="badge bg-primary px-3 py-2 shadow-sm">
                    {{ \Carbon\Carbon::parse($h->tanggal_mutasi)->format('d M Y') }}
                </span>
            </div>

            @php $lastDate = $h->tanggal_mutasi; @endphp
        @endif

        {{-- CARD --}}
        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary history-card">

            <div class="card-body py-3">

                <div class="d-flex justify-content-between">

                    {{-- ISI --}}
                    <div class="row w-100 small">

                        <div class="col-md-6 mb-2">
                            <b>Lokasi</b><br>
                            {{ optional($h->dariLokasi)->nama_lokasi ?? '-' }}
                            →
                            <span class="text-primary fw-semibold">
                                {{ optional($h->keLokasi)->nama_lokasi ?? '-' }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <b>Perusahaan</b><br>
                            {{ optional($h->dariPerusahaan)->nama_perusahaan ?? '-' }}
                            →
                            <span class="text-primary fw-semibold">
                                {{ optional($h->kePerusahaan)->nama_perusahaan ?? '-' }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <b>Karyawan</b><br>
                            {{ optional($h->dariKaryawan)->nama_karyawan ?? '-' }}
                            →
                            <span class="text-primary fw-semibold">
                                {{ optional($h->keKaryawan)->nama_karyawan ?? '-' }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <b>Inventaris</b><br>
                            <span class="badge bg-secondary">
                                {{ $h->dari_no_inventaris ?? '-' }}
                            </span>
                            →
                            <span class="badge bg-success">
                                {{ $h->ke_no_inventaris ?? '-' }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <b>Aplikasi</b><br>
                            {{ $h->dari_aplikasi ?? '-' }}
                            →
                            <span class="text-primary fw-semibold">
                                {{ $h->ke_aplikasi ?? '-' }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <b>Data PPN</b><br>
                            {{ $h->dari_data_ppn ?? '-' }}
                            →
                            <span class="text-primary fw-semibold">
                                {{ $h->ke_data_ppn ?? '-' }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <b>Data Non PPN</b><br>
                            {{ $h->dari_data_non_ppn ?? '-' }}
                            →
                            <span class="text-primary fw-semibold">
                                {{ $h->ke_data_non_ppn ?? '-' }}
                            </span>
                        </div>

                    </div>

                    {{-- TOMBOL HAPUS --}}
                    <div class="ms-3 text-end">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="{{ $h->id }}">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>

                </div>

            </div>
        </div>

    @empty
        <div class="text-center text-muted py-4">
            Belum ada history mutasi
        </div>
    @endforelse

</div>

{{-- SCRIPT HAPUS AJAX --}}

