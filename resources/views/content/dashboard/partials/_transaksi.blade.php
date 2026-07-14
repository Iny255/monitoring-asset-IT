<div class="row mb-4">

    {{-- TRANSAKSI --}}
    <div class="col-lg-8">

        <div class="card transaksi-card shadow border-0">

            <div class="card-header border-0">

                <h5 class="text-dark fw-bold mb-1">

                    <i class="bx bx-bar-chart-alt-2 me-2"></i>

                    Aktivitas Transaksi

                </h5>

                <small class="text-muted">

                    Aktivitas transaksi aset bulan ini

                </small>

            </div>

            <div class="card-body">

                <div id="transaksiChart"></div>

            </div>

        </div>

    </div>

    {{-- TIMELINE --}}
    <div class="col-lg-4">

        @include('content.dashboard.partials._timeline')

    </div>

</div>