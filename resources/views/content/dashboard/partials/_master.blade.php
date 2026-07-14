<div class="row mb-4">

    <div class="col-lg-12">

        <div class="card border-0 shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-1 fw-bold">
                        <i class="bx bx-data me-2 text-primary"></i>
                        Master Data
                    </h5>

                    <small class="text-muted">
                        Informasi data master sistem Asset Management
                    </small>

                </div>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <div class="master-item">

                            <div class="master-icon bg-label-primary">

                                <i class="bx bx-package"></i>

                            </div>

                            <div>

                                <small>Data Aset</small>

                                <h5>

                                    {{ number_format($dashboard['master']['data_aset']) }}

                                </h5>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="master-item">

                            <div class="master-icon bg-label-success">

                                <i class="bx bx-category"></i>

                            </div>

                            <div>

                                <small>Kategori</small>

                                <h5>

                                    {{ number_format($dashboard['master']['kategori']) }}

                                </h5>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="master-item">

                            <div class="master-icon bg-label-info">

                                <i class="bx bx-map"></i>

                            </div>

                            <div>

                                <small>Lokasi</small>

                                <h5>

                                    {{ number_format($dashboard['master']['lokasi']) }}

                                </h5>

                            </div>

                        </div>

                    </div>

                </div>

                <hr>

                <div class="row">

                    <div class="col-md-4">

                        <div class="master-item">

                            <div class="master-icon bg-label-warning">

                                <i class="bx bx-store"></i>

                            </div>

                            <div>

                                <small>Supplier</small>

                                <h5>

                                    {{ number_format($dashboard['master']['supplier']) }}

                                </h5>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="master-item">

                            <div class="master-icon bg-label-danger">

                                <i class="bx bx-user"></i>

                            </div>

                            <div>

                                <small>User Aset</small>

                                <h5>

                                    {{ number_format($dashboard['master']['karyawan']) }}

                                </h5>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="master-item">

                            <div class="master-icon bg-label-secondary">

                                <i class="bx bx-lock-alt"></i>

                            </div>

                            <div>

                                <small>Hak Akses</small>

                                <h5>

                                    {{ number_format($dashboard['master']['hak_akses']) }}

                                </h5>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>