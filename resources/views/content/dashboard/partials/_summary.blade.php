<div class="row g-3 mb-4">

    {{-- TOTAL ASET --}}
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="dashboard-label">
                            Total Aset
                        </span>
                        <h3 class="dashboard-number">
                            {{ number_format($dashboard['inventaris']['total']) }}
                        </h3>
                        <small class="text-muted">
                            Seluruh Inventaris
                        </small>
                    </div>
                    <div class="dashboard-icon bg-primary">
                        <i class="bx bx-desktop"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TERSEDIA --}}
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="dashboard-label">
                            Tersedia
                        </span>
                        <h3 class="dashboard-number text-success">
                            {{ number_format($dashboard['inventaris']['tersedia']) }}
                        </h3>
                        <small class="text-muted">
                            Siap Digunakan
                        </small>
                    </div>
                    <div class="dashboard-icon bg-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DIPAKAI --}}
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="dashboard-label">
                            Dipakai
                        </span>
                        <h3 class="dashboard-number text-warning">
                            {{ number_format($dashboard['inventaris']['dipakai']) }}
                        </h3>
                        <small class="text-muted">
                            Sedang Digunakan
                        </small>
                    </div>
                    <div class="dashboard-icon bg-warning">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DIPINJAM --}}
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="dashboard-label">
                            Dipinjam
                        </span>
                        <h3 class="dashboard-number text-info">
                            {{ number_format($dashboard['inventaris']['dipinjam']) }}
                        </h3>
                        <small class="text-muted">
                            Sedang Dipinjam
                        </small>
                    </div>
                    <div class="dashboard-icon bg-info">
                        <i class="bx bx-transfer"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RUSAK --}}
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="dashboard-label">
                            Rusak
                        </span>
                        <h3 class="dashboard-number text-danger">
                            {{ number_format($dashboard['inventaris']['rusak']) }}
                        </h3>
                        <small class="text-muted">
                            Kondisi Rusak
                        </small>
                    </div>
                    <div class="dashboard-icon bg-danger">
                        <i class="bx bx-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AFKIR --}}
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="dashboard-label">
                            Aset Afkir
                        </span>
                        <h3 class="dashboard-number text-secondary">
                            {{ number_format($dashboard['inventaris']['afkir']) }}
                        </h3>
                        <small class="text-muted">
                            Tidak Layak Pakai
                        </small>
                    </div>
                    <div class="dashboard-icon bg-secondary">
                        <i class="bx bx-trash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>