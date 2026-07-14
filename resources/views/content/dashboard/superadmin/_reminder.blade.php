<div class="card shadow-sm border-0">

    <div class="card-header border-0">

        <h5 class="fw-bold mb-1" style="color:var(--primary-theme)">

            <i class="bx bx-bell me-2"></i>

            Pusat Notifikasi

        </h5>

        <small class="text-muted">

            Monitoring kondisi seluruh perusahaan

        </small>

    </div>

    <div class="card-body">

        @php

            $alerts = [
                [
                    'icon' => 'bx bx-wrench',
                    'color' => 'warning',
                    'title' => 'Maintenance Pengajuan',
                    'value' => $dashboard['reminder']['maintenance_pengajuan'],
                ],

                [
                    'icon' => 'bx bx-loader-circle',
                    'color' => 'danger',
                    'title' => 'Maintenance Diproses',
                    'value' => $dashboard['reminder']['maintenance_diproses'],
                ],

                [
                    'icon' => 'bx bx-transfer',
                    'color' => 'info',
                    'title' => 'Peminjaman Aktif',
                    'value' => $dashboard['reminder']['peminjaman_aktif'],
                ],

                [
                    'icon' => 'bx bx-git-branch',
                    'color' => 'primary',
                    'title' => 'Mapping Service',
                    'value' => $dashboard['reminder']['mapping_servis'],
                ],

                [
                    'icon' => 'bx bx-error-circle',
                    'color' => 'danger',
                    'title' => 'Inventaris Rusak',
                    'value' => $dashboard['reminder']['inventaris_rusak'],
                ],
                [
                    'icon' => 'bx bx-archive',
                    'color' => 'dark',
                    'title' => 'Inventaris Afkir',
                    'value' => $dashboard['reminder']['inventaris_afkir'],
                ],
            ];

        @endphp

        @foreach ($alerts as $alert)
            <div class="d-flex align-items-center justify-content-between mb-3">

                <div class="d-flex align-items-center">

                    <div class="avatar me-3">

                        <span class="avatar-initial rounded bg-label-{{ $alert['color'] }}">

                            <i class="{{ $alert['icon'] }}"></i>

                        </span>

                    </div>

                    <div>

                        <h6 class="mb-0">

                            {{ $alert['title'] }}

                        </h6>

                    </div>

                </div>

                @if ($alert['value'] > 0)
                    <span class="badge bg-{{ $alert['color'] }}">

                        {{ $alert['value'] }}

                    </span>
                @else
                    <i class="bx bx-check-circle text-success fs-4"></i>
                @endif

            </div>
        @endforeach

    </div>

</div>
