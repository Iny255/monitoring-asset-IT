{{-- =====================================
    DASHBOARD HEADER
===================================== --}}
<div class="row mb-4">

    <div class="col-12 fade-up">

        <div class="card dashboard-bg shadow-sm border-0">


            <div class="card-body py-4">

                <div class="row align-items-center">

                    {{-- LEFT --}}
                    <div class="col-lg-8">

                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3">
                            <i class="bx bx-bar-chart-alt-2 me-1"></i>
                            Dashboard Monitoring Asset
                        </span>

                        <h3 class="text-white fw-bold mb-2">
                            Selamat Datang,
                            {{ Str::upper(auth()->user()->name) }} 👋
                        </h3>

                        <p class="mb-3 text-white opacity-75">

                            @if (auth()->user()->role == 'super_admin')
                                Monitoring Seluruh Perusahaan
                            @else
                                Monitoring Asset
                                <strong>
                                    {{ auth()->user()->perusahaan->nama_perusahaan ?? '-' }}
                                </strong>
                            @endif

                        </p>

                        <div class="d-flex flex-wrap gap-3">

                            <div>

                                <small class="text-white opacity-75">
                                    Role
                                </small>

                                <div class="fw-semibold">

                                    {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}

                                </div>

                            </div>

                            <div>

                                <small class="text-white opacity-75">
                                    Login
                                </small>

                                <div class="fw-semibold">

                                    {{ now()->translatedFormat('l, d F Y') }}

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-lg-4 text-end d-none d-lg-block">

                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" class="img-fluid"
                            style="max-height:160px">

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
