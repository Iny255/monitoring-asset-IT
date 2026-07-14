<div class="card shadow-sm border-0 mb-4">

    <div class="card-header border-0">

        <h5 class="fw-bold mb-1" style="color: var(--primary-theme);">

            <i class="bx bx-bell me-2"></i>

            Reminder

        </h5>

        <small class="text-muted">

            Aktivitas yang memerlukan perhatian.

        </small>

    </div>

    <div class="card-body">

        @php
            $adaReminder = false;
        @endphp

        <div class="list-group list-group-flush">

            {{-- Maintenance Pengajuan --}}
            @if ($dashboard['reminder']['maintenance_pengajuan'] > 0)
                @php $adaReminder = true; @endphp

                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bx bx-time-five text-warning me-2"></i>

                        Maintenance menunggu diproses

                    </div>

                    <span class="badge bg-warning">

                        {{ $dashboard['reminder']['maintenance_pengajuan'] }}

                    </span>

                </div>
            @endif

            {{-- Maintenance Diproses --}}
            @if ($dashboard['reminder']['maintenance_diproses'] > 0)
                @php $adaReminder = true; @endphp

                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bx bx-wrench text-info me-2"></i>

                        Maintenance sedang diproses

                    </div>

                    <span class="badge bg-info">

                        {{ $dashboard['reminder']['maintenance_diproses'] }}

                    </span>

                </div>
            @endif

            {{-- Peminjaman --}}
            @if ($dashboard['reminder']['peminjaman_aktif'] > 0)
                @php $adaReminder = true; @endphp

                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bx bx-transfer text-primary me-2"></i>

                        Aset masih dipinjam

                    </div>

                    <span class="badge bg-primary">

                        {{ $dashboard['reminder']['peminjaman_aktif'] }}

                    </span>

                </div>
            @endif

            {{-- Mapping Servis --}}
            @if ($dashboard['reminder']['mapping_servis'] > 0)
                @php $adaReminder = true; @endphp

                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bx bx-cog text-warning me-2"></i>

                        Aset status servis

                    </div>

                    <span class="badge bg-warning">

                        {{ $dashboard['reminder']['mapping_servis'] }}

                    </span>

                </div>
            @endif

            {{-- Mapping Maintenance --}}
            @if ($dashboard['reminder']['mapping_maintenance'] > 0)
                @php $adaReminder = true; @endphp

                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bx bx-wrench text-danger me-2"></i>

                        Aset status maintenance

                    </div>

                    <span class="badge bg-danger">

                        {{ $dashboard['reminder']['mapping_maintenance'] }}

                    </span>

                </div>
            @endif

            {{-- Inventaris Rusak --}}
            @if ($dashboard['reminder']['inventaris_rusak'] > 0)
                @php $adaReminder = true; @endphp

                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bx bx-error-circle text-danger me-2"></i>

                        Inventaris rusak

                    </div>

                    <span class="badge bg-danger">

                        {{ $dashboard['reminder']['inventaris_rusak'] }}

                    </span>

                </div>
            @endif

        </div>

        @if (!$adaReminder)
            <div class="text-center py-4">

                <i class="bx bx-check-circle text-success" style="font-size:60px"></i>

                <h5 class="mt-3 mb-1">

                    Tidak Ada Reminder

                </h5>

                <small class="text-muted">

                    Seluruh aktivitas berjalan normal.

                </small>

            </div>
        @endif

    </div>

</div>
