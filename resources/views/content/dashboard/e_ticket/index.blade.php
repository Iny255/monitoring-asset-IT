@extends('layouts/contentNavbarLayout')

@section('title', 'Helpdesk E-Ticketing IT Support')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-primary me-3">
                        <span class="avatar-initial rounded">
                            <i class="bi bi-ticket-perforated fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">Helpdesk E-Ticketing IT Support</h3>
                        <small class="text-muted">Kelola pengajuan tiket kendala IT, penanganan, dan penyelesaian masalah</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if (in_array(Auth::user()->role, ['petugas', 'super_admin']))
                        <a href="{{ route('ticket-categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-sliders me-1"></i> Kategori & SLA
                        </a>
                    @endif
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateTicket">
                        <i class="bi bi-plus-circle me-1"></i> Buat Tiket Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3 text-center">
                    <span class="text-muted small fw-semibold">TOTAL TIKET</span>
                    <h4 class="fw-bold mb-0 mt-1">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body py-3 text-center">
                    <span class="text-warning small fw-semibold">OPEN</span>
                    <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['open'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm border-start border-info border-4">
                <div class="card-body py-3 text-center">
                    <span class="text-info small fw-semibold">IN PROGRESS</span>
                    <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['in_progress'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm border-start border-secondary border-4">
                <div class="card-body py-3 text-center">
                    <span class="text-secondary small fw-semibold">PENDING</span>
                    <h4 class="fw-bold text-secondary mb-0 mt-1">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body py-3 text-center">
                    <span class="text-success small fw-semibold">RESOLVED</span>
                    <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['resolved'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm border-start border-dark border-4">
                <div class="card-body py-3 text-center">
                    <span class="text-dark small fw-semibold">CLOSED</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1">{{ $stats['closed'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & TABLE CARD --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <form action="{{ route('e-ticket.index') }}" method="GET">
                <div class="row g-2">
                    <div class="{{ auth()->user()->role === 'super_admin' ? 'col-md-3' : 'col-md-4' }}">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0" 
                                   placeholder="Cari No Tiket, Judul, Pelapor..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-md-2">
                            <select name="perusahaan_id" class="form-select bg-light">
                                <option value="">-- Semua Perusahaan --</option>
                                @foreach ($perusahaans as $pt)
                                    <option value="{{ $pt->id }}" {{ request('perusahaan_id') == $pt->id ? 'selected' : '' }}>
                                        {{ $pt->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-2">
                        <select name="status" class="form-select bg-light">
                            <option value="">-- Semua Status --</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="prioritas" class="form-select bg-light">
                            <option value="">-- Semua Prioritas --</option>
                            <option value="low" {{ request('prioritas') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('prioritas') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('prioritas') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('prioritas') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select bg-light">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100 px-2" title="Filter">
                            <i class="bi bi-funnel"></i>
                        </button>
                        <a href="{{ route('e-ticket.index') }}" class="btn btn-outline-secondary px-2" title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- DESKTOP TABLE TIKET --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>NO TIKET</th>
                        <th>TANGGAL & PELAPOR</th>
                        <th>PERUSAHAAN</th>
                        <th>JUDUL & KATEGORI</th>
                        <th>ASET TERKAIT</th>
                        <th>PRIORITAS</th>
                        <th>STATUS</th>
                        <th>PENANGGUNG JAWAB</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $tkt)
                        <tr>
                            <td>
                                <a href="{{ route('e-ticket.show', $tkt->id) }}" class="fw-bold text-primary">
                                    {{ $tkt->nomor_tiket }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $tkt->user->name ?? 'User N/A' }}</div>
                                <small class="text-muted">{{ $tkt->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $tkt->perusahaan->nama_perusahaan ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-truncate" style="max-width: 250px;" title="{{ $tkt->judul }}">
                                    {{ $tkt->judul }}
                                </div>
                                <small class="badge bg-label-info mt-1">{{ $tkt->category->nama_kategori ?? '-' }}</small>
                            </td>
                            <td>
                                @if ($tkt->inventaris)
                                    <div><span class="badge bg-label-dark">{{ $tkt->inventaris->kode_aset }}</span></div>
                                    <small class="text-muted">{{ $tkt->inventaris->dataAset->kategori->nama_barang ?? '' }}</small>
                                @else
                                    <span class="text-muted small">- Tidak ada -</span>
                                @endif
                            </td>
                            <td>
                                @switch($tkt->prioritas)
                                    @case('urgent')
                                        <span class="badge bg-danger">URGENT</span>
                                        @break
                                    @case('high')
                                        <span class="badge bg-warning text-dark">HIGH</span>
                                        @break
                                    @case('medium')
                                        <span class="badge bg-info text-dark">MEDIUM</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">LOW</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($tkt->status)
                                    @case('open')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-top me-1"></i> Open</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-info text-dark"><i class="bi bi-gear-wide-connected me-1"></i> In Progress</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i> Pending</span>
                                        @break
                                    @case('resolved')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Resolved</span>
                                        @break
                                    @case('closed')
                                        <span class="badge bg-dark"><i class="bi bi-lock me-1"></i> Closed</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if ($tkt->assignee)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs bg-label-success me-2">
                                            <span class="avatar-initial rounded-circle">{{ strtoupper(substr($tkt->assignee->name, 0, 1)) }}</span>
                                        </div>
                                        <small class="fw-semibold">{{ $tkt->assignee->name }}</small>
                                    </div>
                                @else
                                    <span class="text-muted small italic">Belum di-assign</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('e-ticket.show', $tkt->id) }}" class="btn btn-sm btn-icon btn-label-primary" title="Detail & Penanganan">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada tiket helpdesk yang terdaftar.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE TIKET CARDS (d-block d-md-none) --}}
        <div class="d-block d-md-none p-3">
            @forelse ($tickets as $tkt)
                <div class="card border shadow-none mb-3 rounded-3 bg-body">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <a href="{{ route('e-ticket.show', $tkt->id) }}" class="fw-bold text-primary fs-6">
                                    {{ $tkt->nomor_tiket }}
                                </a>
                                <h6 class="fw-bold text-dark mb-0 mt-1">{{ $tkt->judul }}</h6>
                            </div>
                            @switch($tkt->status)
                                @case('open')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-top me-1"></i> Open</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info text-dark"><i class="bi bi-gear-wide-connected me-1"></i> In Progress</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i> Pending</span>
                                    @break
                                @case('resolved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Resolved</span>
                                    @break
                                @case('closed')
                                    <span class="badge bg-dark"><i class="bi bi-lock me-1"></i> Closed</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                                    @break
                            @endswitch
                        </div>
                        <hr class="my-2">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Kategori:</small>
                                <span class="badge bg-label-info"><i class="bi bi-tag me-1"></i>{{ $tkt->category->nama_kategori ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Prioritas:</small>
                                @switch($tkt->prioritas)
                                    @case('urgent')
                                        <span class="badge bg-danger">URGENT</span>
                                        @break
                                    @case('high')
                                        <span class="badge bg-warning text-dark">HIGH</span>
                                        @break
                                    @case('medium')
                                        <span class="badge bg-info text-dark">MEDIUM</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">LOW</span>
                                @endswitch
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Tanggal:</small>
                                <small class="fw-semibold text-dark">{{ $tkt->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Petugas IT:</small>
                                <small class="fw-semibold text-dark">{{ $tkt->assignee->name ?? 'Belum di-assign' }}</small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('e-ticket.show', $tkt->id) }}" class="btn btn-primary btn-sm w-100 fw-bold py-2">
                                <i class="bi bi-eye-fill me-1"></i> Lihat Detail Tiket
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada tiket helpdesk yang terdaftar.
                </div>
            @endforelse
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center border-top">
            <small class="text-muted">Menampilkan {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} dari {{ $tickets->total() }} tiket</small>
            <div>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREATE TICKET POP-UP --}}
<div class="modal fade" id="modalCreateTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('e-ticket.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i> Pengajuan Tiket Helpdesk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Kendala / Subjek Tiket <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Komputer Layar Blue Screen / Printer Rusak" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Tiket <span class="text-danger">*</span></label>
                            <select name="ticket_category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }} (Target SLA: {{ $cat->sla_jam }} Jam)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tingkat Prioritas <span class="text-danger">*</span></label>
                            <select name="prioritas" class="form-select" required>
                                <option value="low">Low (Rendah)</option>
                                <option value="medium" selected>Medium (Sedang)</option>
                                <option value="high">High (Tinggi)</option>
                                <option value="urgent">Urgent (Darurat)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Perusahaan</label>
                                <select name="id_perusahaan" class="form-select">
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($perusahaans as $pt)
                                        <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lokasi Penempatan</label>
                                <select name="lokasi_id" class="form-select">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="id_perusahaan" value="{{ auth()->user()->id_perusahaan }}">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Lokasi Penempatan</label>
                                <select name="lokasi_id" class="form-select">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Karyawan / Pelapor (Autocomplete)</label>
                            <input type="text" id="karyawan_search_input" list="karyawan_datalist" class="form-control" placeholder="Ketik nama atau NIK karyawan..." autocomplete="off">
                            <input type="hidden" name="karyawan_id" id="karyawan_id_hidden">
                            <datalist id="karyawan_datalist">
                                @foreach ($karyawans as $kary)
                                    <option data-id="{{ $kary->id }}" value="{{ $kary->nama_karyawan }} ({{ $kary->kode_karyawan ?? 'NIK: -' }})"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Aset / Perangkat Terkait (Opsional)</label>
                            <select name="inventaris_id" class="form-select">
                                <option value="">-- Tidak Terhubung ke Aset Fisik --</option>
                                @foreach ($inventarisList as $inv)
                                    <option value="{{ $inv->id }}">
                                        [{{ $inv->kode_aset }}] {{ $inv->dataAset->kategori->nama_barang ?? '' }} - {{ $inv->perusahaan->nama_perusahaan ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih perangkat jika kendala berkaitan dengan aset tertentu</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Kendala Secara Detail <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan secara detail kronologi kendala, pesan error yang muncul..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lampiran Bukti Foto Error / Dokumen (Opsional)</label>
                        <input type="file" name="lampiran" class="form-control">
                        <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX, ZIP (Maks: 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Submit Tiket Helpdesk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('karyawan_search_input');
    const hiddenInput = document.getElementById('karyawan_id_hidden');
    const dataListOptions = document.querySelectorAll('#karyawan_datalist option');

    if (searchInput && hiddenInput) {
        searchInput.addEventListener('input', function() {
            const val = this.value;
            let matchedId = '';
            dataListOptions.forEach(opt => {
                if (opt.value === val) {
                    matchedId = opt.getAttribute('data-id');
                }
            });
            hiddenInput.value = matchedId;
        });
    }
});
</script>
@endsection
