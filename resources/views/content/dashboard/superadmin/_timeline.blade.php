<div class="card shadow-sm border-0 h-100">

    <div class="card-header border-0">

        <h5 class="fw-bold mb-1" style="color:var(--primary-theme)">

            <i class="bx bx-history me-2"></i>

            Aktivitas Terbaru

        </h5>

        <small class="text-muted">

            Aktivitas terbaru seluruh perusahaan

        </small>

    </div>

    <div class="card-body p-0">

        <div class="timeline-scroll">

            @forelse($dashboard['timeline'] as $item)
                <div class="timeline-mini px-4">

                    <div class="timeline-icon bg-{{ $item['color'] }}">

                        <i class="{{ $item['icon'] }}"></i>

                    </div>

                    <div class="timeline-info">

                        <div class="fw-semibold">

                            {{ $item['judul'] }}

                        </div>

                        <small class="text-muted">

                            {{ $item['perusahaan'] }}

                            •

                            {{ $item['waktu']->diffForHumans() }}

                        </small>

                    </div>

                </div>

            @empty

                <div class="text-center py-5 text-muted">

                    Belum ada aktivitas.

                </div>
            @endforelse

        </div>

    </div>

</div>
