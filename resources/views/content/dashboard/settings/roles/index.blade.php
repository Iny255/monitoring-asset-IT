@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Role')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold py-1 mb-1">
                <span class="text-muted fw-light">Pengaturan Sistem /</span> Manajemen Role
            </h4>
            <p class="text-muted mb-0">Kelola role pengguna, hak kelola pengaturan, dan tambahkan role baru secara dinamis.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('settings.modules.index') }}" class="btn btn-outline-primary">
                <i class="bx bx-slider me-1"></i> Setting Modul & Hak Akses
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateRole">
                <i class="bx bx-plus me-1"></i> Tambah Role Baru
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1"><i class="bx bx-error me-1"></i> Terjadi kesalahan validasi:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-company-filter-banner />

    {{-- Tabel Daftar Role --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bx bx-shield-quarter me-1 text-primary"></i> Daftar Role Sistem & Kustom
            </h5>
            <span class="badge bg-label-primary">{{ count($roles) }} Total Role</span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">#</th>
                        <th>Kode Role</th>
                        <th>Nama Tampilan</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Izin Setting</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Pengguna</th>
                        <th class="text-center">Akses Modul</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $index => $role)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <code>{{ $role->name }}</code>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $role->display_name }}</span>
                            </td>
                            <td>
                                <small class="text-muted text-wrap" style="max-width: 250px; display: inline-block;">
                                    {{ $role->description ?? '-' }}
                                </small>
                            </td>
                            {{-- Toggle Izin Setting --}}
                            <td class="text-center">
                                @if ($role->name === 'super_admin')
                                    <span class="badge bg-success">
                                        <i class="bx bx-check-double me-1"></i> Permanen
                                    </span>
                                @else
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input toggle-setting-access" type="checkbox"
                                               data-id="{{ $role->id }}"
                                               data-name="{{ $role->display_name }}"
                                               {{ $role->can_manage_settings ? 'checked' : '' }}>
                                    </div>
                                @endif
                            </td>
                            {{-- Tipe Sistem / Kustom --}}
                            <td class="text-center">
                                @if ($role->is_system)
                                    <span class="badge bg-label-primary">
                                        <i class="bx bx-lock-alt me-1"></i> Sistem
                                    </span>
                                @else
                                    <span class="badge bg-label-info">
                                        <i class="bx bx-customize me-1"></i> Kustom
                                    </span>
                                @endif
                            </td>
                            {{-- Jumlah Pengguna --}}
                            <td class="text-center">
                                <span class="badge bg-label-secondary rounded-pill">
                                    <i class="bx bx-user me-1"></i> {{ $role->users_count }}
                                </span>
                            </td>
                            {{-- Total Akses Modul --}}
                            <td class="text-center">
                                @if ($role->name === 'super_admin')
                                    <span class="badge bg-label-success">Semua Modul</span>
                                @else
                                    <span class="badge bg-label-dark">
                                        {{ $role->modules->count() }} Modul
                                    </span>
                                @endif
                            </td>
                            {{-- Aksi --}}
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    {{-- Tombol Edit --}}
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit-role"
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}"
                                            data-display="{{ $role->display_name }}"
                                            data-desc="{{ $role->description }}"
                                            data-setting="{{ $role->can_manage_settings ? '1' : '0' }}"
                                            data-system="{{ $role->is_system ? '1' : '0' }}"
                                            title="Edit Role">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus (Hanya untuk non-sistem) --}}
                                    @if (!$role->is_system)
                                        <form action="{{ route('settings.roles.destroy', $role->id) }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus role \'{{ $role->display_name }}\'?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Role">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" disabled title="Role sistem tidak dapat dihapus">
                                            <i class="bx bx-lock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL 1: Tambah Role Baru --}}
<div class="modal fade" id="modalCreateRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('settings.roles.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bx bx-plus-circle me-1 text-primary"></i> Tambah Role Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Role (Slug) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="contoh: auditor, supervisor, teknisi" required pattern="[a-zA-Z0-9_\-]+" title="Hanya huruf, angka, strip, dan underscore">
                            <small class="text-muted">Kode unik sistem (huruf kecil tanpa spasi).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Tampilan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="display_name" placeholder="contoh: Auditor Internal, Supervisor IT" required>
                            <small class="text-muted">Nama role yang tampil di halaman antarmuka.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi Role</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Jelaskan ruang lingkup dan tanggung jawab role ini..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch p-2 border rounded bg-light">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="can_manage_settings" value="1" id="createCanManageSettings">
                                <label class="form-check-label fw-semibold" for="createCanManageSettings">
                                    Beri Izin Kelola Pengaturan Sistem
                                </label>
                                <div class="text-muted small ms-4">Role ini akan dapat membuka menu Pengaturan Sistem, mengubah hak akses modul, dan mengelola role.</div>
                            </div>
                        </div>

                        {{-- Pilihan Modul Awal --}}
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold border-bottom pb-2 d-block">
                                <i class="bx bx-grid-alt me-1 text-primary"></i> Berikan Akses Modul Awal:
                            </label>
                            <div class="row g-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach ($modules as $groupName => $modList)
                                    <div class="col-12">
                                        <div class="fw-semibold text-primary small mt-2 mb-1">
                                            <i class="bx bx-chevron-right"></i> {{ $groupName }}
                                        </div>
                                    </div>
                                    @foreach ($modList as $mod)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $mod->id }}" id="createMod_{{ $mod->id }}">
                                                <label class="form-check-label small" for="createMod_{{ $mod->id }}">
                                                    {{ $mod->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: Edit Role --}}
<div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditRole" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bx bx-edit me-1 text-warning"></i> Edit Data Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Kode Role</label>
                            <input type="text" class="form-control bg-light" id="editName" readonly>
                            <small class="text-muted">Kode role sistem tidak dapat diubah.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Tampilan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="display_name" id="editDisplayName" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi Role</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="2"></textarea>
                        </div>
                        <div class="col-12" id="editSettingContainer">
                            <div class="form-check form-switch p-2 border rounded bg-light">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="can_manage_settings" value="1" id="editCanManageSettings">
                                <label class="form-check-label fw-semibold" for="editCanManageSettings">
                                    Izin Kelola Pengaturan Sistem
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi Modal Edit Role
    const modalEditEl = document.getElementById('modalEditRole');
    const modalEdit = new bootstrap.Modal(modalEditEl);
    const formEdit = document.getElementById('formEditRole');
    const editName = document.getElementById('editName');
    const editDisplayName = document.getElementById('editDisplayName');
    const editDescription = document.getElementById('editDescription');
    const editCanManageSettings = document.getElementById('editCanManageSettings');

    document.querySelectorAll('.btn-edit-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const roleId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const display = this.getAttribute('data-display');
            const desc = this.getAttribute('data-desc');
            const setting = this.getAttribute('data-setting') === '1';

            formEdit.action = `{{ url('dashboard/settings/roles') }}/${roleId}`;
            editName.value = name;
            editDisplayName.value = display;
            editDescription.value = desc || '';
            editCanManageSettings.checked = setting;

            if (name === 'super_admin') {
                editCanManageSettings.checked = true;
                editCanManageSettings.disabled = true;
            } else {
                editCanManageSettings.disabled = false;
            }

            modalEdit.show();
        });
    });

    // 2. AJAX Toggle Setting Access dari Tabel
    document.querySelectorAll('.toggle-setting-access').forEach(switchEl => {
        switchEl.addEventListener('change', function () {
            const roleId = this.getAttribute('data-id');
            const isChecked = this.checked;

            fetch(`{{ url('dashboard/settings/roles') }}/${roleId}/toggle-setting`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Gagal mengubah izin pengaturan');
                    switchEl.checked = !isChecked;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghubungi server.');
                switchEl.checked = !isChecked;
            });
        });
    });
});
</script>
@endsection
