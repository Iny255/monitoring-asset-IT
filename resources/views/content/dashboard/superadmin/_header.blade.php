<div class="row mb-4">

    <div class="col-12">

        <div class="card dashboard-bg shadow-sm border-0">

            <div class="card-body py-4">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <span class="badge bg-white text-primary fw-semibold mb-2">

                            Executive Dashboard

                        </span>

                        <h3 class="text-white fw-bold mb-2">

                            Selamat Datang,
                            {{ Str::upper(auth()->user()->name) }} 👋

                        </h3>

                        <p class="text-white opacity-75 mb-3">

                            Monitoring Seluruh Perusahaan
                            dalam satu dashboard.

                        </p>

                        <div class="row">

                            <div class="col-auto">

                                <small class="text-white opacity-75">

                                    Total Perusahaan

                                </small>

                                <h5 class="text-white mb-0">

                                    {{ $dashboard['executive']['perusahaan'] }}

                                </h5>

                            </div>

                            <div class="col-auto">

                                <small class="text-white opacity-75">

                                    Login

                                </small>

                                <h5 class="text-white mb-0">

                                    {{ now()->translatedFormat('d F Y') }}

                                </h5>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 text-end d-none d-lg-block">

                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
                             class="img-fluid"
                             style="max-height:160px">

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>