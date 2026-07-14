

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-white border-0">

                <h5 class="fw-bold mb-1"
                    style="color:var(--primary-theme)">

                    <i class="bx bx-history me-2"></i>

                    Aktivitas Terbaru

                </h5>

                <small class="text-muted">

                    Aktivitas terakhir sistem

                </small>

            </div>

            <div class="card-body timeline-scroll">

                @forelse($dashboard['timeline'] as $item)

                    <div class="timeline-mini">

                        <div class="timeline-icon bg-{{ $item['color'] }}">

                            <i class="bx {{ $item['icon'] }}"></i>

                        </div>

                        <div class="timeline-info">

                            <div class="fw-semibold">

                                {{ $item['deskripsi'] }}

                            </div>

                            <small class="text-muted">

                                {{ $item['judul'] }}

                                •

                                {{ $item['tanggal']->diffForHumans() }}

                            </small>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-5">

                        <i class="bx bx-history bx-lg text-muted"></i>

                        <p class="text-muted mt-3">

                            Belum ada aktivitas.

                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>