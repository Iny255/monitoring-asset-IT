<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold mb-1" style="color: var(--primary-theme)">

            <i class="bx bx-buildings me-2"></i>

            Monitoring Perusahaan

        </h5>

        <small class="text-muted">

            Ringkasan aktivitas seluruh perusahaan.

        </small>

    </div>

    <div class="card-body">

        <div class="row g-4">

            @foreach($dashboard['company'] as $company)

                <div class="col-xl-3 col-lg-4 col-md-6">

                    <div class="company-card">

                        {{-- Header Card --}}
                        <div class="company-header"
                             style="background: linear-gradient(135deg,
                                {{ $company['primary'] }},
                                {{ $company['secondary'] }})">

                            <div class="company-icon">

                                <i class="bx bx-buildings"></i>

                            </div>

                            <div>

                                <h6 class="mb-0 text-white">

                                    {{ $company['nama'] }}

                                </h6>

                                <small class="text-white opacity-75">

                                    Monitoring Asset

                                </small>

                            </div>

                        </div>

                        <div class="company-body">

                            <div class="company-item">

                                <span><i class="bx bx-package me-1"></i> Inventaris</span>

                                <strong>{{ $company['aset'] }}</strong>

                            </div>

                            <div class="company-item">

                                <span><i class="bx bx-git-branch me-1"></i> Mapping</span>

                                <strong>{{ $company['mapping'] }}</strong>

                            </div>

                            <div class="company-item">

                                <span><i class="bx bx-user me-1"></i> User</span>

                                <strong>{{ $company['user'] }}</strong>

                            </div>

                            <div class="company-item">

                                <span><i class="bx bx-wrench me-1"></i> Maintenance</span>

                                <strong>{{ $company['maintenance'] }}</strong>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>