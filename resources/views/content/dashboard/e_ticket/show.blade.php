@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Tiket #' . $ticket->nomor_tiket)

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
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('e-ticket.index') }}" class="btn btn-icon btn-label-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold mb-0">Tiket #{{ $ticket->nomor_tiket }}</h4>
                            @switch($ticket->status)
                                @case('open')
                                    <span class="badge bg-warning text-dark">Open</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info text-dark">In Progress</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-secondary">Pending</span>
                                    @break
                                @case('resolved')
                                    <span class="badge bg-success">Resolved</span>
                                    @break
                                @case('closed')
                                    <span class="badge bg-dark">Closed</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @break
                            @endswitch

                            @switch($ticket->prioritas)
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
                        <small class="text-muted">Dibuat pada {{ $ticket->created_at->format('d F Y, H:i') }} WIB oleh {{ $ticket->user->name ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- MAIN DETAIL CONTENT --}}
        <div class="col-lg-8">
            {{-- TICKET DESCRIPTION CARD --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">{{ $ticket->judul }}</h5>
                </div>
                <div class="card-body py-4">
                    <div class="mb-4 text-dark" style="white-space: pre-line; line-height: 1.6;">{{ $ticket->deskripsi }}</div>

                    @if ($ticket->lampiran)
                        <div class="p-3 bg-light rounded border mb-3">
                            <span class="fw-semibold me-2"><i class="bi bi-paperclip me-1"></i> Lampiran Bukti:</span>
                            <a href="{{ asset('storage/' . $ticket->lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Lihat / Download Lampiran
                            </a>
                        </div>
                    @endif

                    <hr>

                    <div class="row g-3 text-muted small">
                        <div class="col-6 col-md-3">
                            <span class="d-block text-secondary">Kategori Tiket:</span>
                            <strong class="text-dark">{{ $ticket->category->nama_kategori ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="d-block text-secondary">Target SLA:</span>
                            <strong class="text-dark">{{ $ticket->category->sla_jam ?? 24 }} Jam</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="d-block text-secondary">Perusahaan:</span>
                            <strong class="text-dark">{{ $ticket->perusahaan->nama_perusahaan ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="d-block text-secondary">Lokasi:</span>
                            <strong class="text-dark">{{ $ticket->lokasi->nama_lokasi ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DISCUSSION & REPLIES THREAD --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-chat-left-text me-2 text-primary"></i> Thread Diskusi & Penanganan</h5>
                    <span class="badge bg-label-primary">{{ $ticket->replies->count() }} Balasan</span>
                </div>
                <div class="card-body py-4">
                    {{-- REPLIES LIST --}}
                    <div class="reply-list mb-4">
                        @forelse ($ticket->replies as $reply)
                            @if (!$reply->is_internal_note || in_array(auth()->user()->role, ['petugas', 'super_admin']))
                                <div class="p-3 mb-3 rounded border {{ $reply->is_internal_note ? 'bg-warning-subtle border-warning' : 'bg-light' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-primary me-2">
                                                <span class="avatar-initial rounded-circle">{{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <strong class="text-dark me-2">{{ $reply->user->name ?? 'User' }}</strong>
                                                @if ($reply->is_internal_note)
                                                    <span class="badge bg-warning text-dark">Catatan Internal IT</span>
                                                @elseif ($reply->user_id === $ticket->user_id)
                                                    <span class="badge bg-label-secondary">Pelapor</span>
                                                @else
                                                    <span class="badge bg-label-info">IT Support</span>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="text-dark" style="white-space: pre-line;">{{ $reply->pesan }}</div>
                                    @if ($reply->lampiran)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $reply->lampiran) }}" target="_blank" class="btn btn-xs btn-outline-secondary">
                                                <i class="bi bi-paperclip me-1"></i> Lampiran
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-chat-square-dots fs-2 d-block mb-1"></i>
                                Belum ada balasan pada tiket ini. Masukkan tanggapan pertama di bawah.
                            </div>
                        @endforelse
                    </div>

                    {{-- REPLY FORM --}}
                    <form action="{{ route('e-ticket.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kirim Balasan / Tanggapan</label>
                            <textarea name="pesan" class="form-control" rows="4" placeholder="Tuliskan balasan, solusi, atau update penanganan..." required></textarea>
                        </div>
                        <div class="row align-items-center g-3 mb-3">
                            <div class="col-md-7">
                                <input type="file" name="lampiran" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-5 text-end">
                                @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                                    <div class="form-check form-check-inline me-3">
                                        <input class="form-check-input" type="checkbox" name="is_internal_note" id="is_internal_note" value="1">
                                        <label class="form-check-label text-warning fw-semibold" for="is_internal_note">
                                            Catatan Internal IT
                                        </label>
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-send me-1"></i> Kirim Balasan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SIDEBAR ACTION CONTROL --}}
        <div class="col-lg-4">
            @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                {{-- STATUS & ASSIGNEE CONTROL FOR ADMIN/PETUGAS --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-sliders me-2 text-primary"></i> Kontrol Status & Petugas</h5>
                    </div>
                    <div class="card-body py-3">
                        <form action="{{ route('e-ticket.update-status', $ticket->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Update Status Tiket</label>
                                <select name="status" class="form-select">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open (Belum Ditangani)</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Dikerjakan)</option>
                                    <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending (Menunggu Sparepart/Vendor)</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved (Solusi Diberikan)</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed (Selesai & Ditutup)</option>
                                    <option value="rejected" {{ $ticket->status == 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assign ke Petugas IT</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Belum Ditugaskan --</option>
                                    @foreach ($petugasList as $ptg)
                                        <option value="{{ $ptg->id }}" {{ $ticket->assigned_to == $ptg->id ? 'selected' : '' }}>
                                            {{ $ptg->name }} ({{ strtoupper($ptg->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i> Simpan Perubahan Status
                            </button>
                        </form>
                    </div>
                </div>

                {{-- INTEGRATION TO MAINTENANCE CARD --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-wrench me-2 text-warning"></i> Integrasi Service & Maintenance</h5>
                    </div>
                    <div class="card-body py-3">
                        @if ($ticket->maintenance)
                            <div class="alert alert-success mb-0" role="alert">
                                <i class="bi bi-check-circle me-1"></i> Tiket ini telah dikonversi ke Service & Maintenance:
                                <div class="mt-2">
                                    <a href="{{ route('maintenance.index') }}" class="fw-bold text-success">
                                        #{{ $ticket->maintenance->kode_service }} (Status: {{ $ticket->maintenance->status }})
                                    </a>
                                </div>
                            </div>
                        @elseif ($ticket->inventaris)
                            <p class="small text-muted mb-3">
                                Perangkat/Aset terkait: <strong class="text-dark">{{ $ticket->inventaris->kode_aset }}</strong> ({{ $ticket->inventaris->dataAset->kategori->nama_barang ?? '' }}).
                                Dapat langsung dikonversi ke entri transaksi perbaikan fisik.
                            </p>
                            <form action="{{ route('e-ticket.convert-maintenance', $ticket->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengkonversi tiket ini ke transaksi Service & Maintenance?');">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">
                                    <i class="bx bx-wrench me-1"></i> Convert to Maintenance
                                </button>
                            </form>
                        @else
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Tidak ada aset fisik terkait pada tiket ini untuk dikonversi ke Service & Maintenance.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- READ-ONLY INFO CARD FOR USER / KARYAWAN --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-info"></i> Info Penanganan IT</h5>
                    </div>
                    <div class="card-body py-3">
                        <div class="mb-3">
                            <span class="d-block text-secondary small">Petugas IT Penanggung Jawab:</span>
                            <strong class="text-dark">{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</strong>
                        </div>
                        <div class="mb-3">
                            <span class="d-block text-secondary small">Waktu Direspon:</span>
                            <strong class="text-dark">{{ $ticket->responded_at ? $ticket->responded_at->format('d/m/Y H:i') : 'Menunggu respon' }}</strong>
                        </div>
                        <div>
                            <span class="d-block text-secondary small">Waktu Selesai:</span>
                            <strong class="text-dark">{{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-' }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ASET TERKAIT CARD --}}
            @if ($ticket->inventaris)
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-laptop me-2 text-info"></i> Detail Perangkat Terkait</h5>
                    </div>
                    <div class="card-body py-3">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Kode Aset:</td>
                                <td class="fw-bold">{{ $ticket->inventaris->kode_aset }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No Inventaris:</td>
                                <td>{{ $ticket->inventaris->no_inventaris }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kategori Barang:</td>
                                <td>{{ $ticket->inventaris->dataAset->kategori->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Perusahaan:</td>
                                <td>{{ $ticket->inventaris->perusahaan->nama_perusahaan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Aset:</td>
                                <td><span class="badge bg-label-primary">{{ $ticket->inventaris->status }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
