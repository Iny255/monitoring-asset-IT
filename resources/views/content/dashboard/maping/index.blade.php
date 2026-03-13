@extends('layouts/contentNavbarLayout')

@section('title', 'Data Mapping')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0">Data Mapping</h5>

                {{-- HANYA PETUGAS BISA TAMBAH --}}
                @if (auth()->user()->role == 'petugas')
                    <a href="/dashboard/maping/create" class="btn btn-primary">
                        Tambah Data Mapping
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">

            {{-- SEARCH --}}
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-2">
                    <select name="lokasi" class="form-control">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasis as $l)
                            <option value="{{ $l->id }}" {{ request('lokasi') == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="perusahaan" class="form-control">
                        <option value="">Semua Perusahaan</option>
                        @foreach ($perusahaans as $p)
                            <option value="{{ $p->id }}" {{ request('perusahaan') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_perusahaan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="barang" class="form-control">
                        <option value="">Semua Barang</option>
                        @foreach ($barangs as $b)
                            <option value="{{ $b->id }}" {{ request('barang') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        @for ($i = date('Y'); $i >= 2018; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="Search bebas..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>

                    {{-- PETUGAS & MANAGER BISA CETAK --}}
                    @if (auth()->user()->role === 'petugas')
                        <a href="{{ route('maping.print', request()->query()) }}" target="_blank"
                            class="btn btn-success w-100">
                            Cetak
                        </a>
                    @elseif (auth()->user()->role === 'manager')
                        <a href="{{ route('manager.maping.cetak', request()->query()) }}" target="_blank"
                            class="btn btn-success w-100">
                            Cetak
                        </a>
                    @endif
                </div>

            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Nama Karyawan</th>
                            <th>Lokasi</th>
                            <th>Perusahaan</th>
                            <th>Processor</th>
                            <th>RAM</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapings as $index => $maping)
                            <tr>
                                <td class="text-center">
                                    {{ $mapings->firstItem() + $index }}
                                </td>
                                <td>{{ $maping->keluar->kode_barang }}</td>
                                <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                                <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
                                <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                                <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $maping->processor }}</td>
                                <td>{{ $maping->ram }} GB</td>

                                <td class="text-center">

                                    {{-- ================= PETUGAS ================= --}}
                                    @if (auth()->user()->role === 'petugas')
                                        {{-- SHOW --}}
                                        <a href="{{ route('maping.show', $maping->id) }}" class="btn btn-info btn-sm">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $maping->id }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        {{-- DELETE --}}
                                        <form id="delete-form-{{ $maping->id }}"
                                            action="{{ route('maping.destroy', $maping->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $maping->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                        {{-- MUTASI --}}
                                        <button class="btn btn-success btn-sm btn-mutasi" data-id="{{ $maping->id }}">
                                            <i class="bx bx-transfer"></i>
                                        </button>
                                        {{-- CABUT INVENTARIS --}}
                                        <form id="cabut-form-{{ $maping->id }}"
                                            action="{{ route('maping.cabut', $maping->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                        </form>

                                        <button class="btn btn-dark btn-sm btn-cabut" data-id="{{ $maping->id }}"
                                            data-kode="{{ $maping->keluar->kode_barang }}"
                                            data-nama="{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}">
                                            <i class="bx bx-power-off"></i>
                                        </button>

                                        {{-- ================= MANAGER ================= --}}
                                    @elseif(auth()->user()->role === 'manager')
                                        <a href="{{ route('manager.maping.show', $maping->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bx bx-show"></i> Detail
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Data belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $mapings->links() }}
            </div>

        </div>
    </div>
    <!-- MODAL PENCABUTAN -->
    <div class="modal fade" id="modalCabut" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formCabut" method="POST">
                @csrf

                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="mb-0" style="color:white;">Form Pencabutan Inventaris</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Kode Barang</label>
                                <input type="text" id="cabut_kode" class="form-control" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama Barang</label>
                                <input type="text" id="cabut_nama" class="form-control" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Pencabutan</label>
                                <input type="date" name="tanggal_cabut" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Kondisi Barang</label>

                                <select name="kondisi" class="form-control" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak">Rusak</option>
                        
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Alasan Pencabutan</label>

                                <textarea name="alasan" class="form-control" rows="3"
                                    placeholder="Contoh: perangkat rusak / karyawan resign"></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button class="btn btn-primary">
                            Simpan Pencabutan
                        </button>

                    </div>

                </div>

            </form>
        </div>
    </div>
@endsection


@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // DELETE
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data mapping ini akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });

            // EDIT
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Edit data ini?',
                        text: 'Kamu akan diarahkan ke halaman edit',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Edit',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/dashboard/maping/${id}/edit`;
                        }
                    });
                });
            });

            // MUTASI
            document.querySelectorAll('.btn-mutasi').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const url = "{{ route('maping.mutasi', ':id') }}".replace(':id', id);

                    Swal.fire({
                        title: 'Mutasi data ini?',
                        text: 'Data akan dipindahkan & history tersimpan',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Mutasi',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });

                });
            });

        });
        // CABUT INVENTARIS
        document.querySelectorAll('.btn-cabut').forEach(btn => {

            btn.addEventListener('click', function() {

                const id = this.dataset.id;
                const kode = this.dataset.kode;
                const nama = this.dataset.nama;

                document.getElementById('cabut_kode').value = kode;
                document.getElementById('cabut_nama').value = nama;

                const url = "{{ route('maping.cabut', ':id') }}".replace(':id', id);
                document.getElementById('formCabut').action = url;

                let modal = new bootstrap.Modal(document.getElementById('modalCabut'));
                modal.show();

            });

        });
    </script>

@endsection
