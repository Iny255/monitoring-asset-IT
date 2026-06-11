@extends('layouts/contentNavbarLayout')

@section('title', 'Lokasi')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0">Data Lokasi Aset</h5>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                    Tambah Data Lokasi
                </button>
            </div>

            {{-- BODY --}}
            <div class="card-body">

                {{-- FILTER + SEARCH --}}
                <form method="GET" class="row g-3 align-items-end mb-4">


                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-md-4">
                            <label class="form-label">Perusahaan</label>
                            <select name="perusahaan_id" class="form-select">
                                <option value="">Semua Perusahaan</option>
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}"
                                        {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-5">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama lokasi..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary w-100">
                            Reset
                        </a>
                    </div>

                </form>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th width="60">NO</th>

                                <th>NAMA LOKASI</th>

                                @if (auth()->user()->role === 'super_admin')
                                    <th>PERUSAHAAN</th>
                                @endif

                                <th width="120">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($lokasis as $index => $lokasi)
                                <tr>
                                    <td class="text-center">
                                        {{ $lokasis->firstItem() + $index }}
                                    </td>
                                    <td>{{ $lokasi->nama_lokasi }}</td>

                                    @if (auth()->user()->role === 'super_admin')
                                        <td>
                                            {{ $lokasi->perusahaan->nama_perusahaan ?? '-' }}
                                        </td>
                                    @endif

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">

                                            {{-- EDIT --}}
                                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $lokasi->id }}"
                                                data-kode="{{ $lokasi->kode_lokasi }}"
                                                data-nama="{{ $lokasi->nama_lokasi }}"
                                                data-perusahaan_id="{{ $lokasi->id_perusahaan }}">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>

                                            {{-- DELETE --}}

                                            <form id="delete-form-{{ $lokasi->id }}"
                                                action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $lokasi->id }}">
                                                <i class="bx bx-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- MODAL TAMBAH --}}
                <div class="modal fade" id="modalTambahLokasi">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form action="{{ route('lokasi.store') }}" method="POST">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Lokasi</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- SUPER ADMIN --}}
                                    @if (auth()->user()->role === 'super_admin')
                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <select name="id_perusahaan" id="perusahaanSelect" class="form-select" required>
                                                <option value="">-- pilih perusahaan --</option>
                                                @foreach ($perusahaans as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif



                                    <div class="mb-3">
                                        <label>Nama Lokasi</label>
                                        <input type="text" name="nama_lokasi" class="form-control" required>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary">Simpan</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                {{-- MODAL EDIT --}}
                <div class="modal fade" id="modalEditLokasi">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form id="formEditLokasi" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Lokasi</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- SUPER ADMIN --}}
                                    @if (auth()->user()->role === 'super_admin')
                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <select name="id_perusahaan" id="edit_perusahaan" class="form-select">
                                                @foreach ($perusahaans as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif



                                    <div class="mb-3">
                                        <label>Nama Lokasi</label>
                                        <input type="text" name="nama_lokasi" id="edit_nama" class="form-control"
                                            required>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-warning">Update</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $lokasis->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        // DELETE
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.onclick = function() {

                let id = this.dataset.id;

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });

            }
        });


        // EDIT
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.onclick = function() {

                let id = this.dataset.id;

                document.getElementById('edit_nama').value = this.dataset.nama;


                @if (auth()->user()->role === 'super_admin')
                    document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                @endif

                document.getElementById('formEditLokasi').action =
                    `/dashboard/lokasi/${id}`;

                new bootstrap.Modal(document.getElementById('modalEditLokasi')).show();
            }
        });
    </script>
@endsection
