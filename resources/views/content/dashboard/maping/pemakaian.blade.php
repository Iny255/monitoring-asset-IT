@extends('layouts/contentNavbarLayout')

@section('title', 'Pemakaian Aset - Stok Operasional')

@section('content')
    <div class="container-fluid">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-laptop fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Pemakaian Aset</h3>
                            <small class="text-muted">Daftar ketersediaan stok operasional unit aset yang dapat dialokasikan / dimapping</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali ke Mapping
                        </a>
                        <a href="{{ route('maping.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Form Mapping Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER FORM --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('maping.pemakaian') }}">
                    <div class="row g-3">
                        @if ($user->role == 'super_admin')
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Perusahaan</label>
                                <select name="perusahaan_id" class="form-select">
                                    <option value="">-- Semua Perusahaan --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori Aset</label>
                            <select name="kategori_id" class="form-select">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($kategoris as $k)
                                    <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pencarian Barang / Merk / Tipe</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama barang, merek, type..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-1 d-flex align-items-end gap-1">
                            <button type="submit" class="btn btn-primary w-100" title="Cari">
                                <i class="bx bx-search"></i>
                            </button>
                            <a href="{{ route('maping.pemakaian') }}" class="btn btn-label-secondary" title="Reset">
                                <i class="bx bx-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE STOK PEMAKAIAN --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">NO</th>
                                @if ($user->role == 'super_admin')
                                    <th>PERUSAHAAN</th>
                                @endif
                                <th>KATEGORI</th>
                                <th>MEREK & TYPE</th>
                                <th class="text-center">RINCIAN STOK</th>
                                <th>PEMAKAI AKTIF Saat Ini</th>
                                <th width="140" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stoks as $index => $stok)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    @if ($user->role == 'super_admin')
                                        <td>
                                            <span class="fw-bold text-dark">
                                                {{ $stok->perusahaan?->nama_perusahaan ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td>
                                        <span class="badge bg-label-info fs-6 fw-semibold">
                                            {{ $stok->kategori?->nama_barang ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">
                                            {{ $stok->merek ?? '-' }}
                                        </span>
                                        <small class="text-muted">{{ $stok->type ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <span class="badge bg-primary" title="Total Unit">Total: {{ $stok->total_aset }}</span>
                                            <span class="badge bg-success" title="Tersedia di Gudang">Tersedia: {{ $stok->tersedia }}</span>
                                            <span class="badge bg-info" title="Sedang Dipakai">Dipakai: {{ $stok->dipakai }}</span>
                                            @if($stok->dipinjam > 0)
                                                <span class="badge bg-warning" title="Sedang Dipinjam">Dipinjam: {{ $stok->dipinjam }}</span>
                                            @endif
                                            @if($stok->rusak > 0)
                                                <span class="badge bg-danger" title="Kondisi Rusak">Rusak: {{ $stok->rusak }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @forelse($stok->pemakai as $nama)
                                            <span class="badge bg-label-primary mb-1 me-1">
                                                <i class="bx bx-user me-1"></i>{{ $nama }}
                                            </span>
                                        @empty
                                            <span class="text-muted small">Belum ada pemakai aktif</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @if($stok->tersedia > 0)
                                            <a href="{{ route('maping.create', ['data_aset_id' => $stok->data_aset_id, 'perusahaan_id' => $stok->perusahaan?->id]) }}" class="btn btn-sm btn-success">
                                                <i class="bx bx-plus-circle me-1"></i> Gunakan Aset
                                            </a>
                                        @else
                                            <span class="badge bg-label-secondary px-3 py-2">
                                                <i class="bx bx-x-circle me-1"></i> Stok Habis
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $user->role == 'super_admin' ? 7 : 6 }}" class="text-center py-5 text-muted">
                                        <i class="bx bx-package fs-1 d-block mb-2 text-secondary"></i>
                                        Belum ada data stok aset operasional yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const perusahaanSelect = document.querySelector('form[action="{{ route('maping.pemakaian') }}"] select[name="perusahaan_id"]');
            const kategoriSelect = document.querySelector('form[action="{{ route('maping.pemakaian') }}"] select[name="kategori_id"]');

            if (perusahaanSelect && kategoriSelect) {
                perusahaanSelect.addEventListener('change', function() {
                    const id = this.value;
                    if (!id) {
                        kategoriSelect.innerHTML = '<option value="">-- Semua Kategori --</option>';
                        return;
                    }

                    fetch('/dashboard/get-kategori/' + id)
                        .then(res => res.json())
                        .then(data => {
                            let html = '<option value="">-- Semua Kategori --</option>';
                            data.forEach(item => {
                                html += `<option value="${item.id}">${item.nama_barang}</option>`;
                            });
                            kategoriSelect.innerHTML = html;
                        });
                });
            }
        });
    </script>
@endsection
