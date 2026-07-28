@extends('layouts/contentNavbarLayout')

@section('title', 'Master Perusahaan & Cabang')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-building fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Master Perusahaan & Cabang</h3>
                            <small class="text-muted">Kelola daftar perusahaan induk (Holding) dan entitas cabang (Multi-Company & Multi-Branch)</small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPerusahaan">
                            <i class="bx bx-plus me-1"></i> Tambah Perusahaan / Cabang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
            <form method="GET" action="{{ url('/dashboard/perusahaan') }}" class="row g-3 mb-4">
                <div class="col-md-7">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-2">
                            <input type="text" name="search" class="form-control w-100"
                                placeholder="Cari berdasarkan kode / nama perusahaan" value="{{ request('search') }}">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th width="90">KODE</th>
                            <th>NAMA PERUSAHAAN / CABANG</th>
                            <th width="180">TIPE & INDUK</th>
                            <th width="120">TEMA</th>
                            <th width="120">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perusahaans as $perusahaan)
                            <tr>
                                {{-- KODE --}}
                                <td>
                                    <span class="badge bg-primary font-monospace">
                                        {{ $perusahaan->kode_perusahaan }}
                                    </span>
                                </td>

                                {{-- NAMA --}}
                                <td>
                                    <strong class="text-dark">{{ $perusahaan->nama_perusahaan }}</strong>
                                    @if($perusahaan->cabangs->count() > 0)
                                        <br>
                                        <small class="text-muted">
                                            <i class="bx bx-git-branch me-1"></i>{{ $perusahaan->cabangs->count() }} Cabang Terdaftar
                                        </small>
                                    @endif
                                </td>

                                {{-- TIPE & INDUK --}}
                                <td>
                                    @if ($perusahaan->tipe === 'Cabang' || $perusahaan->parent_id !== null)
                                        <span class="badge bg-label-info">
                                            <i class="bx bx-git-branch me-1"></i> Cabang
                                        </span>
                                        <div class="small text-muted mt-1">
                                            Induk: <strong>{{ $perusahaan->parent->nama_perusahaan ?? '-' }}</strong>
                                        </div>
                                    @else
                                        <span class="badge bg-label-primary">
                                            <i class="bx bx-building-house me-1"></i> Perusahaan Induk
                                        </span>
                                    @endif
                                </td>

                                {{-- TEMA WARNA --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="width:25px; height:25px; border-radius:50%; display:inline-block; border:1px solid #ddd; background:{{ $perusahaan->primary_color }};" title="Primary Color"></span>
                                        <span style="width:25px; height:25px; border-radius:50%; display:inline-block; border:1px solid #ddd; background:{{ $perusahaan->secondary_color }};" title="Secondary Color"></span>
                                    </div>
                                </td>

                                {{-- ACTION --}}
                                <td>
                                    <div class="d-flex gap-2">
                                        {{-- EDIT --}}
                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $perusahaan->id }}"
                                            data-kode="{{ $perusahaan->kode_perusahaan }}"
                                            data-nama="{{ $perusahaan->nama_perusahaan }}"
                                            data-tipe="{{ $perusahaan->tipe ?? 'Induk' }}"
                                            data-parent-id="{{ $perusahaan->parent_id ?? '' }}"
                                            data-primary="{{ $perusahaan->primary_color }}"
                                            data-secondary="{{ $perusahaan->secondary_color }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        {{-- FORM DELETE --}}
                                        <form id="delete-form-{{ $perusahaan->id }}"
                                            action="{{ route('perusahaan.destroy', $perusahaan->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        {{-- DELETE --}}
                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $perusahaan->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="bx bx-folder-open fs-2 d-block mb-2 text-secondary"></i>
                                    Data perusahaan / cabang tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $perusahaans->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH PERUSAHAAN / CABANG -->
    <div class="modal fade" id="modalTambahPerusahaan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('perusahaan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Perusahaan / Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_perusahaan" class="form-control" value="{{ $kodePerusahaan }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan / Cabang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control" placeholder="Contoh: PT SSA CABANG SEMARANG" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Entitas <span class="text-danger">*</span></label>
                            <select name="tipe" id="tambah_tipe" class="form-select" required>
                                <option value="Induk">Perusahaan Induk (Holding)</option>
                                <option value="Cabang">Cabang Perusahaan</option>
                            </select>
                        </div>

                        <div class="mb-3" id="group_tambah_parent" style="display: none;">
                            <label class="form-label">Pilih Perusahaan Induk <span class="text-danger">*</span></label>
                            <select name="parent_id" id="tambah_parent_id" class="form-select">
                                <option value="">-- Pilih Induk Perusahaan --</option>
                                @foreach ($parentPerusahaans as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color" value="#0d6efd">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color" value="#6c757d">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo Perusahaan / Cabang</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PERUSAHAAN / CABANG -->
    <div class="modal fade" id="modalEditPerusahaan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditPerusahaan" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Perusahaan / Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" id="edit_kode_perusahaan" name="kode_perusahaan" class="form-control" required readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan / Cabang <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_perusahaan" name="nama_perusahaan" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Entitas <span class="text-danger">*</span></label>
                            <select name="tipe" id="edit_tipe" class="form-select" required>
                                <option value="Induk">Perusahaan Induk (Holding)</option>
                                <option value="Cabang">Cabang Perusahaan</option>
                            </select>
                        </div>

                        <div class="mb-3" id="group_edit_parent" style="display: none;">
                            <label class="form-label">Pilih Perusahaan Induk <span class="text-danger">*</span></label>
                            <select name="parent_id" id="edit_parent_id" class="form-select">
                                <option value="">-- Pilih Induk Perusahaan --</option>
                                @foreach ($parentPerusahaans as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" id="edit_primary_color" name="primary_color" class="form-control form-control-color">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" id="edit_secondary_color" name="secondary_color" class="form-control form-control-color">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Toggle visibility parent_id dropdown
            const tambahTipe = document.getElementById('tambah_tipe');
            const groupTambahParent = document.getElementById('group_tambah_parent');
            if (tambahTipe && groupTambahParent) {
                tambahTipe.addEventListener('change', function() {
                    groupTambahParent.style.display = (this.value === 'Cabang') ? 'block' : 'none';
                });
            }

            const editTipe = document.getElementById('edit_tipe');
            const groupEditParent = document.getElementById('group_edit_parent');
            if (editTipe && groupEditParent) {
                editTipe.addEventListener('change', function() {
                    groupEditParent.style.display = (this.value === 'Cabang') ? 'block' : 'none';
                });
            }

            // DELETE
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data perusahaan / cabang ini akan dihapus!",
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

            // EDIT POPUP
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const kode = this.dataset.kode;
                    const nama = this.dataset.nama;
                    const tipe = this.dataset.tipe || 'Induk';
                    const parentId = this.dataset.parentId || '';
                    const primary = this.dataset.primary;
                    const secondary = this.dataset.secondary;

                    document.getElementById('edit_kode_perusahaan').value = kode;
                    document.getElementById('edit_nama_perusahaan').value = nama;
                    document.getElementById('edit_tipe').value = tipe;
                    document.getElementById('edit_primary_color').value = primary;
                    document.getElementById('edit_secondary_color').value = secondary;

                    const editParentId = document.getElementById('edit_parent_id');
                    if (editParentId) {
                        editParentId.value = parentId;
                    }

                    if (groupEditParent) {
                        groupEditParent.style.display = (tipe === 'Cabang') ? 'block' : 'none';
                    }

                    document.getElementById('formEditPerusahaan').action = `/dashboard/perusahaan/${id}`;

                    var editModal = new bootstrap.Modal(document.getElementById('modalEditPerusahaan'));
                    editModal.show();
                });
            });

        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalTambahPerusahaan'));
                myModal.show();
            });
        </script>
    @endif
@endsection
