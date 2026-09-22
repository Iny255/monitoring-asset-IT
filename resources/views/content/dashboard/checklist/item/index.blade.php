@extends('layouts/contentNavbarLayout')

@section('title', 'Master Item Checklist')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ALERT NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- PAGE HEADER (CLEAN & MODERN) --}}
    <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-2 mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item text-secondary">Checklist Device</li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Master Item Cek</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">Master Item Checklist</h4>
            <p class="text-muted small mb-0">Poin-poin standar pengecekan fisik perangkat saat inspeksi.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
                <i class="bx bx-plus me-1"></i> Tambah Item Baru
            </button>
        </div>
    </div>

    {{-- FILTER PERUSAHAAN (SUPER ADMIN) --}}
    @if (auth()->user()->role === 'super_admin')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form action="{{ route('checklist.item.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="small fw-semibold text-muted">Filter Target:</span>
                    <select name="id_perusahaan" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Item (Global & Semua PT)</option>
                        <option value="global" {{ request('id_perusahaan') === 'global' ? 'selected' : '' }}>Hanya Item Global (Semua PT)</option>
                        @foreach ($perusahaans as $p)
                            <option value="{{ $p->id }}" {{ request('id_perusahaan') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_perusahaan }}
                            </option>
                        @endforeach
                    </select>
                    @if (request('id_perusahaan'))
                        <a href="{{ route('checklist.item.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
        </div>
    @endif

    {{-- TABLE ITEMS --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">Urutan</th>
                        @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                            <th>Target Perusahaan</th>
                        @endif
                        <th>Kategori</th>
                        <th>Nama Item Pemeriksaan</th>
                        <th>Keterangan / Panduan</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">
                                {{ $item->urutan }}
                            </td>
                            @if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan)
                                <td>
                                    @if ($item->perusahaan)
                                        <x-company-badge :perusahaan="$item->perusahaan" />
                                    @else
                                        <span class="badge bg-label-secondary">Global (Semua PT)</span>
                                    @endif
                                </td>
                            @endif
                            <td>
                                <span class="badge bg-label-primary">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->nama_item }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->keterangan ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                @if ($item->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-icon btn-outline-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditItem{{ $item->id }}" 
                                            title="Edit Item">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <form action="{{ route('checklist.item.destroy', $item->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus item pemeriksaan ini?');"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Item">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT ITEM --}}
                        <div class="modal fade" id="modalEditItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Item Checklist</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('checklist.item.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            @if (auth()->user()->role === 'super_admin')
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Target Perusahaan</label>
                                                    <select name="id_perusahaan" class="form-select">
                                                        <option value="">Semua Perusahaan (Global)</option>
                                                        @foreach ($perusahaans as $p)
                                                            <option value="{{ $p->id }}" {{ $item->id_perusahaan == $p->id ? 'selected' : '' }}>
                                                                🏢 {{ $p->nama_perusahaan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="form-text small">Pilih perusahaan spesifik atau jadikan Global untuk semua perusahaan.</div>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Kategori Item</label>
                                                <select name="kategori" class="form-select" required>
                                                    <option value="Kebersihan & Fisik" {{ $item->kategori === 'Kebersihan & Fisik' ? 'selected' : '' }}>Kebersihan & Fisik</option>
                                                    <option value="Kelistrikan" {{ $item->kategori === 'Kelistrikan' ? 'selected' : '' }}>Kelistrikan</option>
                                                    <option value="Performa & OS" {{ $item->kategori === 'Performa & OS' ? 'selected' : '' }}>Performa & OS</option>
                                                    <option value="Keamanan" {{ $item->kategori === 'Keamanan' ? 'selected' : '' }}>Keamanan</option>
                                                    <option value="Penyimpanan" {{ $item->kategori === 'Penyimpanan' ? 'selected' : '' }}>Penyimpanan</option>
                                                    <option value="Konektivitas" {{ $item->kategori === 'Konektivitas' ? 'selected' : '' }}>Konektivitas</option>
                                                    <option value="Hardware & Port" {{ $item->kategori === 'Hardware & Port' ? 'selected' : '' }}>Hardware & Port</option>
                                                    <option value="Umum" {{ $item->kategori === 'Umum' ? 'selected' : '' }}>Umum</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Item Pemeriksaan</label>
                                                <input type="text" name="nama_item" class="form-control" value="{{ $item->nama_item }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Keterangan / Standar Kondisi</label>
                                                <textarea name="keterangan" rows="2" class="form-control">{{ $item->keterangan }}</textarea>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Urutan</label>
                                                    <input type="number" name="urutan" class="form-control" value="{{ $item->urutan }}">
                                                </div>
                                                <div class="col-6 d-flex align-items-end">
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold" for="activeSwitch{{ $item->id }}">Aktif Digunakan</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'super_admin' ? 7 : 6 }}" class="text-center py-5 text-muted">
                                Belum ada item checklist. Klik "Tambah Item Baru" untuk membuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH ITEM --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Item Checklist Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('checklist.item.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if (auth()->user()->role === 'super_admin')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Target Perusahaan</label>
                            <select name="id_perusahaan" class="form-select">
                                <option value="">Semua Perusahaan (Global)</option>
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}" {{ (request('id_perusahaan') == $p->id) ? 'selected' : '' }}>
                                        🏢 {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Pilih perusahaan spesifik atau jadikan Global untuk semua perusahaan.</div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Item <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="Kebersihan & Fisik">Kebersihan & Fisik</option>
                            <option value="Kelistrikan">Kelistrikan</option>
                            <option value="Performa & OS">Performa & OS</option>
                            <option value="Keamanan">Keamanan</option>
                            <option value="Penyimpanan">Penyimpanan</option>
                            <option value="Konektivitas">Konektivitas</option>
                            <option value="Hardware & Port">Hardware & Port</option>
                            <option value="Umum">Umum</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Item Pemeriksaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_item" class="form-control" placeholder="Contoh: Pembersihan debu pada kipas processor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Standar Acuan</label>
                        <textarea name="keterangan" rows="2" class="form-control" placeholder="Petunjuk apa yang harus diperhatikan teknisi saat memeriksa"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Urut Tampilan</label>
                        <input type="number" name="urutan" class="form-control" value="{{ count($items) + 1 }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
