<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Lapor Kendala IT - Helpdesk Support Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap 5.3 & Boxicons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #0b2f57;
            --primary-hover: #154b87;
            --accent-color: #2563eb;
        }

        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 20px 10px 60px;
        }

        .portal-wrapper {
            max-width: 860px;
            margin: auto;
        }

        .portal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border-radius: 20px;
            color: #fff;
            padding: 32px 28px;
            box-shadow: 0 10px 25px rgba(11, 47, 87, 0.18);
            position: relative;
            overflow: hidden;
        }

        .portal-header::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .card-custom {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-top: 20px;
            position: relative;
            /* Do not use overflow: hidden so autocomplete suggestions can float above other cards */
        }

        #karyawanSectionCard {
            z-index: 30;
        }

        #deviceSectionCard {
            z-index: 20;
        }

        #problemSectionCard {
            z-index: 10;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 24px;
            background: #fafafa;
            border-bottom: 1px solid #f0f0f0;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .section-number {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 0;
            font-size: 16px;
            color: #0f172a;
        }

        /* Search Suggestion Dropdown */
        .search-results-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.16);
            max-height: 320px;
            overflow-y: auto;
            margin-top: 6px;
            display: none;
        }

        .search-result-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.15s;
        }

        .search-result-item:hover {
            background: #f8fafc;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        /* Device Selection Cards */
        .device-card {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            background: #fff;
            height: 100%;
        }

        .device-card:hover {
            border-color: #94a3b8;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.05);
        }

        .device-card.selected {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.15);
        }

        .device-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #f1f5f9;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .device-card.selected .device-icon-box {
            background: #2563eb;
            color: #fff;
        }

        .device-check-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #cbd5e1;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
        }

        .device-card.selected .device-check-badge {
            background: #2563eb;
        }

        .filter-btn {
            border-radius: 20px;
            font-size: 13px;
            padding: 5px 14px;
            font-weight: 500;
        }

        .btn-submit-ticket {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 17px;
            padding: 14px 28px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(11, 47, 87, 0.22);
            transition: all 0.25s;
        }

        .btn-submit-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(11, 47, 87, 0.3);
            color: #fff;
        }

        .badge-karyawan-selected {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 14px 18px;
        }
    </style>
</head>

<body>

    <div class="portal-wrapper">

        {{-- PORTAL HEADER --}}
        <div class="portal-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white p-2 rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bx bx-support fs-1"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1">Helpdesk & IT Support</h3>
                        <p class="mb-0 text-white-50 small">Formulir Laporan Kendala Perangkat & Layanan IT</p>
                    </div>
                </div>
                <div class="text-end d-none d-sm-block">
                    <span class="badge bg-white text-dark px-3 py-2 rounded-pill shadow-sm">
                        <i class="bx bx-bolt-circle text-warning me-1"></i> Respon Cepat IT
                    </span>
                </div>
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('error'))
            <div class="alert alert-danger mt-3 rounded-4 shadow-sm border-0 d-flex align-items-center">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <form action="{{ route('public.ticket.store') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
            @csrf
            <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ old('karyawan_id') }}" required>
            <input type="hidden" name="inventaris_id" id="inventaris_id" value="{{ old('inventaris_id') }}">

            {{-- SECTION 1: IDENTITAS PELAPOR --}}
            <div class="card card-custom" id="karyawanSectionCard">
                <div class="section-header">
                    <div class="section-number">1</div>
                    <div>
                        <h6 class="section-title">Data Pelapor (Karyawan)</h6>
                        <small class="text-muted">Ketik nama lengkap Anda untuk mendeteksi perangkat yang dipakai</small>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- Search Input Box --}}
                    <div id="searchKaryawanBox">
                        <label class="form-label fw-semibold">Cari Nama Karyawan <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bx bx-search fs-4"></i>
                                </span>
                                <input type="text" id="inputSearchKaryawan" class="form-control border-start-0 ps-0 @error('karyawan_id') is-invalid @enderror"
                                       placeholder="Ketik minimal 2 huruf nama Anda (contoh: Budi, Agus, Siti)..."
                                       autocomplete="off">
                            </div>
                            <div class="search-results-box" id="searchResultsBox"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bx bx-info-circle me-1"></i> Sistem akan mencocokkan data karyawan dan memuat perangkat yang saat ini Anda gunakan.
                        </small>
                        @error('karyawan_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Selected Employee Badge (Hidden by default, shown after select) --}}
                    <div id="selectedKaryawanCard" class="badge-karyawan-selected" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bx bx-user-check fs-3"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6" id="labelKaryawanNama">-</div>
                                    <div class="text-muted small">
                                        <span id="labelKaryawanDivisi">-</span> • <span id="labelKaryawanPerusahaan" class="fw-semibold text-primary">-</span>
                                        <span id="labelKaryawanKode" class="badge bg-light text-secondary ms-1">-</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="btnChangeKaryawan">
                                    <i class="bx bx-refresh me-1"></i> Ganti Nama
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- SECTION 2: PILIH PERANGKAT TERKAIT --}}
            <div class="card card-custom" id="deviceSectionCard">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <div>
                        <h6 class="section-title">Pilih Perangkat yang Bermasalah</h6>
                        <small class="text-muted">Pilih perangkat yang Anda gunakan atau pilih kendala umum/non-perangkat</small>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- Info State: Karyawan belum dipilih --}}
                    <div id="deviceStateEmpty" class="text-center py-4 text-muted">
                        <i class="bx bx-laptop fs-1 text-secondary opacity-50 d-block mb-2"></i>
                        <p class="mb-0">Silakan pilih nama karyawan di atas terlebih dahulu untuk menampilkan perangkat yang sedang Anda pakai.</p>
                    </div>

                    {{-- Loading State --}}
                    <div id="deviceStateLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="text-muted small mt-2">Memuat perangkat yang Anda gunakan...</div>
                    </div>

                    {{-- Device Content Box --}}
                    <div id="deviceContentBox" style="display: none;">

                        {{-- Device Category Filter Tabs --}}
                        <div class="d-flex align-items-center gap-1 flex-wrap mb-3" id="deviceFilterButtons">
                            <button type="button" class="btn btn-primary filter-btn active" data-filter="all">Semua</button>
                            <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="laptop"><i class="bx bx-laptop me-1"></i> Laptop</button>
                            <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="pc"><i class="bx bx-desktop me-1"></i> PC</button>
                            <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="printer"><i class="bx bx-printer me-1"></i> Printer</button>
                            <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="smartphone"><i class="bx bx-mobile-alt me-1"></i> HP</button>
                            <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="other">Lainnya</button>
                        </div>

                        {{-- Device Cards Grid --}}
                        <div class="row g-3 mb-3" id="deviceCardsContainer">
                            {{-- Dynamically populated via JavaScript --}}
                        </div>

                        {{-- Special Card: Non-Device / Masalah Umum --}}
                        <div class="device-card selected" id="cardNonDevice" onclick="selectDevice(null, this)">
                            <div class="device-check-badge"><i class="bx bx-check"></i></div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="device-icon-box">
                                    <i class="bx bx-network-chart"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Kendala Non-Perangkat Fisik / Perangkat Lain</div>
                                    <small class="text-muted d-block">Pilih opsi ini untuk kendala internet/Wi-Fi, akun email, aplikasi software, hak akses, atau jika perangkat Anda tidak tercantum di atas.</small>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- SECTION 3: DETAIL KENDALA & KONTAK --}}
            <div class="card card-custom" id="problemSectionCard">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <div>
                        <h6 class="section-title">Detail Kendala & Kontak Pelapor</h6>
                        <small class="text-muted">Jelaskan kendala yang dialami dan cantumkan kontak yang dapat dihubungi</small>
                    </div>
                </div>
                <div class="card-body p-4">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Kendala <span class="text-danger">*</span></label>
                            <select name="ticket_category_id" class="form-select form-select-lg @error('ticket_category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori Kendala --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('ticket_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }} (Target SLA: {{ $cat->sla_jam }} Jam)
                                    </option>
                                @endforeach
                            </select>
                            @error('ticket_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tingkat Urgensi / Prioritas <span class="text-danger">*</span></label>
                            <select name="prioritas" class="form-select form-select-lg @error('prioritas') is-invalid @enderror" required>
                                <option value="low" {{ old('prioritas') == 'low' ? 'selected' : '' }}>Low (Rendah / Tidak Mendesak)</option>
                                <option value="medium" {{ old('prioritas', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Standar)</option>
                                <option value="high" {{ old('prioritas') == 'high' ? 'selected' : '' }}>High (Penting / Mengganggu Pekerjaan)</option>
                                <option value="urgent" {{ old('prioritas') == 'urgent' ? 'selected' : '' }}>Urgent (Sangat Mendesak / Operasional Terhenti)</option>
                            </select>
                            @error('prioritas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Kendala / Subjek Singkat <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control form-control-lg @error('judul') is-invalid @enderror"
                               placeholder="Contoh: Layar Laptop Bergaris / Tidak Bisa Cetak ke Printer / Jaringan Putus"
                               value="{{ old('judul') }}" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penjelasan Lengkap Kendala <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror"
                                  placeholder="Ceritakan detail kendala yang dialami, pesan error yang muncul, atau sejak kapan kendala terjadi..." required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor WhatsApp / HP Aktif <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bx bxl-whatsapp text-success fs-5"></i></span>
                                <input type="text" name="kontak_pelapor" class="form-control @error('kontak_pelapor') is-invalid @enderror"
                                       placeholder="Contoh: 081234567890" value="{{ old('kontak_pelapor') }}" required>
                            </div>
                            <small class="text-muted">Tim IT akan mengonfirmasi dan menghubungi nomor ini.</small>
                            @error('kontak_pelapor')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bx bx-envelope text-secondary fs-5"></i></span>
                                <input type="email" name="email_pelapor" class="form-control @error('email_pelapor') is-invalid @enderror"
                                       placeholder="email.anda@perusahaan.com" value="{{ old('email_pelapor') }}">
                            </div>
                            @error('email_pelapor')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto / Bukti Kendala (Opsional)</label>
                        <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror"
                               accept="image/*,.pdf,.doc,.docx,.zip">
                        <small class="text-muted">Bisa langsung ambil foto dari kamera HP atau screenshot layar (Maks. 5MB, format: JPG, PNG, PDF).</small>
                        @error('lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 pt-2">
                        <button type="submit" class="btn btn-submit-ticket w-100 d-flex align-items-center justify-content-center gap-2" id="btnSubmit">
                            <i class="bx bx-send fs-4"></i>
                            <span>Kirim Laporan Tiket ke IT Support</span>
                        </button>
                    </div>

                </div>
            </div>

        </form>

        <div class="text-center text-muted small mt-4">
            &copy; {{ date('Y') }} IT Asset Management & Helpdesk Support System
        </div>

    </div>

    {{-- SCRIPTS --}}
    <script>
        const searchInput = document.getElementById('inputSearchKaryawan');
        const resultsBox = document.getElementById('searchResultsBox');
        const karyawanIdInput = document.getElementById('karyawan_id');
        const inventarisIdInput = document.getElementById('inventaris_id');

        const searchBoxContainer = document.getElementById('searchKaryawanBox');
        const selectedKaryawanCard = document.getElementById('selectedKaryawanCard');
        const btnChangeKaryawan = document.getElementById('btnChangeKaryawan');

        const labelNama = document.getElementById('labelKaryawanNama');
        const labelDivisi = document.getElementById('labelKaryawanDivisi');
        const labelPerusahaan = document.getElementById('labelKaryawanPerusahaan');
        const labelKode = document.getElementById('labelKaryawanKode');

        const deviceStateEmpty = document.getElementById('deviceStateEmpty');
        const deviceStateLoading = document.getElementById('deviceStateLoading');
        const deviceContentBox = document.getElementById('deviceContentBox');
        const deviceCardsContainer = document.getElementById('deviceCardsContainer');
        const cardNonDevice = document.getElementById('cardNonDevice');

        let searchDebounceTimer = null;
        let cachedAssets = [];

        // 1. Live search karyawan
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchDebounceTimer);

            if (query.length < 2) {
                resultsBox.style.display = 'none';
                resultsBox.innerHTML = '';
                return;
            }

            searchDebounceTimer = setTimeout(() => {
                fetch(`{{ route('public.ticket.api.search_karyawan') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsBox.innerHTML = '';
                        if (data.length === 0) {
                            resultsBox.innerHTML = '<div class="p-3 text-muted text-center"><i class="bx bx-user-x me-1"></i> Nama karyawan tidak ditemukan.</div>';
                        } else {
                            data.forEach(item => {
                                const row = document.createElement('div');
                                row.className = 'search-result-item';
                                row.innerHTML = `
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="fw-bold text-dark fs-6 text-truncate">${item.nama_karyawan}</div>
                                            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                <span class="badge bg-primary text-white px-2 py-1"><i class="bx bx-buildings me-1"></i>${item.divisi || '-'}</span>
                                                <small class="text-muted"><i class="bx bx-id-card me-1"></i>${item.jabatan || '-'}</small>
                                            </div>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            <span class="badge bg-light text-primary border px-2 py-1">${item.nama_perusahaan}</span>
                                            ${item.kode_karyawan ? `<div class="text-muted font-monospace mt-1" style="font-size: 11px;">#${item.kode_karyawan}</div>` : ''}
                                        </div>
                                    </div>
                                `;
                                row.onclick = () => selectKaryawan(item);
                                resultsBox.appendChild(row);
                            });
                        }
                        resultsBox.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Error searching karyawan:', err);
                    });
            }, 250);
        });

        // Close search suggestion when click outside
        document.addEventListener('click', function(e) {
            if (!searchBoxContainer.contains(e.target)) {
                resultsBox.style.display = 'none';
            }
        });

        // 2. Pilih Karyawan
        function selectKaryawan(karyawan) {
            karyawanIdInput.value = karyawan.id;
            labelNama.innerText = karyawan.nama_karyawan;
            labelDivisi.innerText = karyawan.divisi;
            labelPerusahaan.innerText = karyawan.nama_perusahaan;
            labelKode.innerText = karyawan.kode_karyawan || '';

            resultsBox.style.display = 'none';
            searchBoxContainer.style.display = 'none';
            selectedKaryawanCard.style.display = 'block';

            // Ambil perangkat milik karyawan
            loadKaryawanAssets(karyawan.id);
        }

        // Reset ganti karyawan
        btnChangeKaryawan.addEventListener('click', function() {
            karyawanIdInput.value = '';
            inventarisIdInput.value = '';
            searchInput.value = '';
            selectedKaryawanCard.style.display = 'none';
            searchBoxContainer.style.display = 'block';
            searchInput.focus();

            deviceStateEmpty.style.display = 'block';
            deviceStateLoading.style.display = 'none';
            deviceContentBox.style.display = 'none';
            deviceCardsContainer.innerHTML = '';
            cachedAssets = [];
            selectDevice(null, cardNonDevice);
        });

        // 3. Load Perangkat Karyawan
        function loadKaryawanAssets(karyawanId) {
            deviceStateEmpty.style.display = 'none';
            deviceStateLoading.style.display = 'block';
            deviceContentBox.style.display = 'none';

            fetch(`{{ url('tiket/api/karyawan') }}/${karyawanId}/assets`)
                .then(res => res.json())
                .then(data => {
                    deviceStateLoading.style.display = 'none';
                    deviceContentBox.style.display = 'block';

                    cachedAssets = data.assets || [];
                    renderDeviceCards(cachedAssets);

                    // Default to non-device unless an old asset is selected
                    const oldInventarisId = '{{ old('inventaris_id') }}';
                    if (oldInventarisId) {
                        const targetCard = document.querySelector(`.device-card[data-id="${oldInventarisId}"]`);
                        if (targetCard) {
                            selectDevice(oldInventarisId, targetCard);
                            return;
                        }
                    }
                    selectDevice(null, cardNonDevice);
                })
                .catch(err => {
                    console.error('Error fetching assets:', err);
                    deviceStateLoading.style.display = 'none';
                    deviceContentBox.style.display = 'block';
                });
        }

        // 4. Render Kartu Perangkat
        function renderDeviceCards(assets, filter = 'all') {
            deviceCardsContainer.innerHTML = '';

            const filtered = filter === 'all' 
                ? assets 
                : assets.filter(a => a.category_group === filter);

            if (filtered.length === 0) {
                if (assets.length === 0) {
                    deviceCardsContainer.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-light border text-muted py-3 text-center mb-0">
                                <i class="bx bx-info-circle me-1"></i> Tidak ada perangkat fisik yang terdaftar atas nama Anda saat ini. Anda dapat memilih opsi kendala non-perangkat di bawah.
                            </div>
                        </div>
                    `;
                } else {
                    deviceCardsContainer.innerHTML = `
                        <div class="col-12">
                            <div class="text-muted small py-2 text-center">Tidak ada perangkat dalam kategori filter ini.</div>
                        </div>
                    `;
                }
                return;
            }

            filtered.forEach(asset => {
                const col = document.createElement('div');
                col.className = 'col-md-6';
                col.innerHTML = `
                    <div class="device-card" data-id="${asset.inventaris_id}" data-group="${asset.category_group}" onclick="selectDevice(${asset.inventaris_id}, this)">
                        <div class="device-check-badge"><i class="bx bx-check"></i></div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="device-icon-box">
                                <i class="${asset.icon}"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge ${asset.badge_class} small">${asset.kategori}</span>
                                    <small class="text-muted fw-semibold font-monospace">${asset.kode_aset}</small>
                                </div>
                                <div class="fw-bold text-dark text-truncate">${asset.merek} ${asset.type}</div>
                                <small class="text-muted d-block text-truncate">No. Inv: ${asset.no_inventaris} • Lokasi: ${asset.lokasi}</small>
                            </div>
                        </div>
                    </div>
                `;
                deviceCardsContainer.appendChild(col);
            });
        }

        // 5. Select Device Handler
        function selectDevice(id, element) {
            inventarisIdInput.value = id || '';

            // Hapus class selected dari semua kartu
            document.querySelectorAll('.device-card').forEach(c => c.classList.remove('selected'));

            // Tambahkan class selected ke kartu yang diklik
            if (element) {
                element.classList.add('selected');
            }
        }

        // 6. Filter Buttons Handler
        document.querySelectorAll('#deviceFilterButtons .filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#deviceFilterButtons .filter-btn').forEach(b => {
                    b.classList.remove('btn-primary', 'active');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary', 'active');

                const filter = this.getAttribute('data-filter');
                renderDeviceCards(cachedAssets, filter);

                // Preserve selection if still visible
                const currentId = inventarisIdInput.value;
                if (currentId) {
                    const match = document.querySelector(`.device-card[data-id="${currentId}"]`);
                    if (match) match.classList.add('selected');
                } else {
                    cardNonDevice.classList.add('selected');
                }
            });
        });

        // 7. Form Submit Protection
        document.getElementById('ticketForm').addEventListener('submit', function(e) {
            if (!karyawanIdInput.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Nama Karyawan',
                    text: 'Silakan cari dan pilih nama karyawan pelapor terlebih dahulu pada kolom nomor 1.',
                    confirmButtonColor: '#0b2f57'
                });
                searchInput.focus();
                return false;
            }

            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Mengirim Tiket...';
        });

        // Rehydrate old state if validation errors occurred
        @if(old('karyawan_id'))
            fetch(`{{ route('public.ticket.api.search_karyawan') }}?q={{ old('karyawan_id') }}`)
                .then(res => res.json())
                .then(data => {
                    const found = data.find(k => k.id == '{{ old('karyawan_id') }}');
                    if (found) selectKaryawan(found);
                });
        @endif
    </script>

</body>

</html>
