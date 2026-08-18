@extends('layouts/contentNavbarLayout')

@section('title', 'Master Kategori Tiket & SLA')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('e-ticket.index') }}" class="btn btn-icon btn-label-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="fw-bold mb-0">Master Kategori Tiket & Target SLA</h3>
                        <small class="text-muted">Kelola jenis kategori masalah IT dan batas target waktu penanganan Service Level Agreement (SLA)</small>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE KATEGORI --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">NO</th>
                        <th>NAMA KATEGORI</th>
                        <th>DESKRIPSI</th>
                        <th>TARGET SLA (JAM)</th>
                        <th>JUMLAH TIKET</th>
                        <th class="text-center" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $key => $cat)
                        <tr>
                            <td>{{ $categories->firstItem() + $key }}</td>
                            <td class="fw-bold text-dark">{{ $cat->nama_kategori }}</td>
                            <td>{{ $cat->deskripsi ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-primary"><i class="bi bi-clock me-1"></i> {{ $cat->sla_jam }} Jam</span>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ $cat->tickets_count ?? $cat->tickets()->count() }} Tiket</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-icon btn-label-primary me-1" 
                                        data-bs-toggle="modal" data-bs-target="#modalEditCategory{{ $cat->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('ticket-categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDIT CATEGORY --}}
                        <div class="modal fade" id="modalEditCategory{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('ticket-categories.update', $cat->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title fw-bold">Edit Kategori Tiket</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_kategori" class="form-control" value="{{ $cat->nama_kategori }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Target SLA (Dalam Jam) <span class="text-danger">*</span></label>
                                                <input type="number" name="sla_jam" class="form-control" value="{{ $cat->sla_jam }}" min="1" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="3">{{ $cat->deskripsi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-sliders fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada kategori tiket helpdesk yang ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $categories->links() }}
        </div>
    </div>
</div>

{{-- MODAL CREATE CATEGORY --}}
<div class="modal fade" id="modalCreateCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ticket-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tambah Kategori Tiket Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Hardware / Software / Jaringan / Hak Akses" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target SLA (Dalam Jam) <span class="text-danger">*</span></label>
                        <input type="number" name="sla_jam" class="form-control" value="24" min="1" required>
                        <small class="text-muted">Batas waktu maksimal penyelesaian tiket untuk kategori ini</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan mengenai cakupan masalah pada kategori ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
