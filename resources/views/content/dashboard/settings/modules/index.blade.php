@extends('layouts/contentNavbarLayout')

@section('title', 'Setting Modul & Hak Akses')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold py-1 mb-1">
                <span class="text-muted fw-light">Pengaturan Sistem /</span> Setting Modul & Hak Akses
            </h4>
            <p class="text-muted mb-0">Kelola aktivasi modul dan tentukan hak akses modul per role secara dinamis.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('settings.roles.index') }}" class="btn btn-outline-primary">
                <i class="bx bx-shield-quarter me-1"></i> Manajemen Role
            </a>
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

    <x-company-filter-banner />

    {{-- CARD 1: Role Pengelola Setting --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-label-primary py-3 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <i class="bx bx-lock-alt fs-4 me-2 text-primary"></i>
                <h5 class="mb-0 fw-bold text-primary">Hak Akses Kelola Pengaturan (Setting Managers)</h5>
            </div>
            <span class="badge bg-primary">Otoritas Menu Pengaturan</span>
        </div>
        <div class="card-body pt-3">
            <p class="text-muted small mb-3">
                Tentukan role apa saja yang berhak membuka menu <strong>Pengaturan Sistem</strong> (Setting Modul & Manajemen Role).
                Role <span class="badge bg-label-danger">Super Admin</span> terkunci otomatis permanen.
            </p>
            <form action="{{ route('settings.modules.managers') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-center">
                    @foreach ($roles as $r)
                        <div class="col-md-3 col-sm-6">
                            <div class="form-check form-switch p-2 border rounded bg-light">
                                <input class="form-check-input ms-0 me-2" type="checkbox"
                                       name="setting_roles[]" value="{{ $r->id }}"
                                       id="settingRole_{{ $r->id }}"
                                       {{ $r->name === 'super_admin' || $r->can_manage_settings ? 'checked' : '' }}
                                       {{ $r->name === 'super_admin' ? 'disabled' : '' }}>
                                <label class="form-check-label fw-semibold" for="settingRole_{{ $r->id }}">
                                    {{ $r->display_name }}
                                    @if ($r->name === 'super_admin')
                                        <span class="badge bg-label-secondary ms-1">Terkunci</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bx bx-save me-1"></i> Simpan Pengelola Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD 2: Matriks Perizinan Modul --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="bx bx-grid-alt me-1 text-primary"></i> Matriks Hak Akses Modul per Role
                </h5>
                <small class="text-muted">Centang kotak untuk memberikan hak akses menu/modul kepada masing-masing role.</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCheckAll">
                    <i class="bx bx-check-double me-1"></i> Centang Semua
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnUncheckAll">
                    <i class="bx bx-x me-1"></i> Hapus Semua
                </button>
            </div>
        </div>

        <form action="{{ route('settings.modules.matrix') }}" method="POST" id="matrixForm">
            @csrf
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="min-width: 250px;">Modul / Menu</th>
                            <th style="min-width: 150px;">Grup Modul</th>
                            @foreach ($roles as $role)
                                <th class="text-center" style="min-width: 120px;">
                                    <span class="d-block fw-bold {{ $role->name === 'super_admin' ? 'text-danger' : 'text-dark' }}">
                                        {{ $role->display_name }}
                                    </span>
                                    <span class="badge {{ $role->is_system ? 'bg-label-primary' : 'bg-label-info' }} rounded-pill" style="font-size: 10px;">
                                        {{ $role->is_system ? 'System' : 'Custom' }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modulesGrouped as $groupName => $modules)
                            <tr class="table-secondary">
                                <td colspan="{{ count($roles) + 3 }}" class="py-2 px-3 fw-bold text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">
                                    <i class="bx bx-folder me-1"></i> {{ $groupName }}
                                </td>
                            </tr>
                            @foreach ($modules as $module)
                                <tr>
                                    {{-- Toggle Aktif / Nonaktif Modul --}}
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input toggle-module-status" type="checkbox"
                                                   data-id="{{ $module->id }}"
                                                   data-name="{{ $module->name }}"
                                                   id="modToggle_{{ $module->id }}"
                                                   {{ $module->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    {{-- Info Modul --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-primary rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="{{ $module->icon ?? 'bx bx-cube' }} fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $module->name }}</div>
                                                <small class="text-muted">{{ $module->url ? '/' . trim($module->url, '/') : 'Menu Grup' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Grup --}}
                                    <td>
                                        <span class="badge bg-label-dark">{{ $module->group }}</span>
                                    </td>

                                    {{-- Checkboxes Per Role --}}
                                    @foreach ($roles as $role)
                                        @php
                                            $isAssigned = in_array($module->id, $roleModules[$role->id] ?? []);
                                            $isSuperAdmin = $role->name === 'super_admin';
                                        @endphp
                                        <td class="text-center">
                                            @if ($isSuperAdmin)
                                                <input type="checkbox" class="form-check-input" checked disabled title="Super Admin memiliki akses permanen">
                                                <input type="hidden" name="matrix[{{ $role->id }}][{{ $module->id }}]" value="1">
                                            @else
                                                <input type="checkbox"
                                                       class="form-check-input matrix-checkbox role-col-{{ $role->id }}"
                                                       name="matrix[{{ $role->id }}][{{ $module->id }}]"
                                                       value="1"
                                                       id="perm_{{ $role->id }}_{{ $module->id }}"
                                                       {{ $isAssigned ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Aksi Simpan --}}
            <div class="card-footer bg-light border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    <i class="bx bx-info-circle me-1"></i> Perubahan matriks akan langsung memengaruhi menu dan route masing-masing pengguna.
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> Simpan Matriks Perizinan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Toggle Status Modul AJAX & Check All --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. AJAX Toggle Status Modul
    const toggleSwitches = document.querySelectorAll('.toggle-module-status');
    toggleSwitches.forEach(switchEl => {
        switchEl.addEventListener('change', function () {
            const moduleId = this.getAttribute('data-id');
            const moduleName = this.getAttribute('data-name');
            const isChecked = this.checked;

            fetch(`{{ url('dashboard/settings/modules') }}/${moduleId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optional toast or alert notification
                } else {
                    alert('Gagal mengubah status modul: ' + (data.message || 'Terjadi kesalahan'));
                    switchEl.checked = !isChecked;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghubungi server untuk mengubah status modul.');
                switchEl.checked = !isChecked;
            });
        });
    });

    // 2. Centang Semua / Hapus Semua (Hanya kolom non-Super Admin)
    const checkBoxes = document.querySelectorAll('.matrix-checkbox');
    const btnCheckAll = document.getElementById('btnCheckAll');
    const btnUncheckAll = document.getElementById('btnUncheckAll');

    if (btnCheckAll) {
        btnCheckAll.addEventListener('click', function () {
            checkBoxes.forEach(cb => cb.checked = true);
        });
    }

    if (btnUncheckAll) {
        btnUncheckAll.addEventListener('click', function () {
            checkBoxes.forEach(cb => cb.checked = false);
        });
    }
});
</script>
@endsection
