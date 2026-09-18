@extends('layouts/contentNavbarLayout')

@section('title', 'Buat Jadwal Checklist Device')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- BREADCRUMB --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <span class="text-muted fw-light">Checklist Device /</span> Buat Jadwal Mingguan
        </h5>
        <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-label-primary me-2">
                            <i class="bx bx-calendar-plus fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Perencanaan Jadwal Checklist Device</h6>
                            <small class="text-muted">Pilih lokasi tempat/ruangan device yang akan diperiksa pada minggu tertentu</small>
                        </div>
                    </div>
                </div>

                <div class="card-body py-4">
                    <form action="{{ route('checklist.jadwal.store') }}" method="POST" id="formJadwal">
                        @csrf

                        {{-- Perusahaan (Jika Super Admin) --}}
                        @if (auth()->user()->role === 'super_admin' && $perusahaans->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="id_perusahaan" class="form-select" required>
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}" {{ old('id_perusahaan') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Lokasi / Ruangan Device --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Lokasi / Ruangan Device <span class="text-danger">*</span>
                            </label>
                            <select name="id_lokasi" class="form-select select2" required>
                                <option value="">-- Pilih Lokasi / Ruangan --</option>
                                @foreach ($lokasis as $lok)
                                    <option value="{{ $lok->id }}" {{ old('id_lokasi') == $lok->id ? 'selected' : '' }}>
                                        🏢 {{ $lok->nama_lokasi }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $lok->perusahaan) ({{ $lok->perusahaan?->nama_perusahaan }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Seluruh device yang ter-mapping aktif di lokasi ini akan otomatis masuk ke dalam daftar checklist.
                            </div>
                        </div>

                        {{-- Periode: Tahun, Bulan, Minggu Ke --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" id="inputTahun" class="form-select" required>
                                    @for ($y = $currentYear - 1; $y <= $currentYear + 2; $y++)
                                        <option value="{{ $y }}" {{ old('tahun', $currentYear) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                                <select name="bulan" id="inputBulan" class="form-select" required>
                                    @foreach ([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $mNum => $mName)
                                        <option value="{{ $mNum }}" {{ old('bulan', $currentMonth) == $mNum ? 'selected' : '' }}>
                                            {{ $mName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Minggu Ke- <span class="text-danger">*</span></label>
                                <select name="minggu_ke" id="inputMinggu" class="form-select" required>
                                    <option value="1" {{ old('minggu_ke', 1) == 1 ? 'selected' : '' }}>Minggu ke-1</option>
                                    <option value="2" {{ old('minggu_ke') == 2 ? 'selected' : '' }}>Minggu ke-2</option>
                                    <option value="3" {{ old('minggu_ke') == 3 ? 'selected' : '' }}>Minggu ke-3</option>
                                    <option value="4" {{ old('minggu_ke') == 4 ? 'selected' : '' }}>Minggu ke-4</option>
                                    <option value="5" {{ old('minggu_ke') == 5 ? 'selected' : '' }}>Minggu ke-5</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Mulai & Tanggal Selesai --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="inputTanggalMulai" class="form-control" 
                                       value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai (Batas Akhir) <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" id="inputTanggalSelesai" class="form-control" 
                                       value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+6 days'))) }}" required>
                            </div>
                        </div>

                        {{-- Petugas IT Penanggung Jawab --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Petugas IT yang Ditugaskan</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">-- Pilih Petugas IT (Opsional) --</option>
                                @foreach ($petugasList as $p)
                                    <option value="{{ $p->id }}" {{ old('assigned_to', auth()->id()) == $p->id ? 'selected' : '' }}>
                                        👤 {{ $p->name }} @if((in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) && $p->perusahaan) ({{ $p->perusahaan?->nama_perusahaan }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Teknisi/Petugas yang bertanggung jawab melakukan inspeksi di ruangan tersebut.</div>
                        </div>

                        {{-- Catatan Khusus --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan / Instruksi Khusus</label>
                            <textarea name="catatan" rows="3" class="form-control" 
                                      placeholder="Contoh: Fokuskan pada pengecekan suhu server dan update antivirus pada seluruh PC...">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('checklist.jadwal.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-check me-1"></i> Buat & Terbitkan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Auto calculate dates based on Tahun, Bulan, Minggu Ke
    function updateWeekDates() {
        const tahun = parseInt(document.getElementById('inputTahun').value);
        const bulan = parseInt(document.getElementById('inputBulan').value) - 1; // 0-indexed
        const minggu = parseInt(document.getElementById('inputMinggu').value);

        // Cari hari pertama di bulan tersebut
        const firstDay = new Date(tahun, bulan, 1);
        // Estimasi awal minggu
        let startDay = 1 + (minggu - 1) * 7;
        let startDate = new Date(tahun, bulan, startDay);
        let endDate = new Date(tahun, bulan, startDay + 6);

        // Format ke YYYY-MM-DD
        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        document.getElementById('inputTanggalMulai').value = formatDate(startDate);
        document.getElementById('inputTanggalSelesai').value = formatDate(endDate);
    }

    document.getElementById('inputTahun').addEventListener('change', updateWeekDates);
    document.getElementById('inputBulan').addEventListener('change', updateWeekDates);
    document.getElementById('inputMinggu').addEventListener('change', updateWeekDates);
</script>
@endpush
@endsection
