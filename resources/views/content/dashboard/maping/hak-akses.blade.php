@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Hak Akses')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-md bg-label-primary me-3">
                                <span class="avatar-initial rounded">
                                    <i class="bx bx-lock-alt fs-3"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">Kelola Hak Akses & Akun Kredensial</h3>
                                <small class="text-muted">Kelola Hak Akses, Aplikasi, serta Kredensial Email & Password per Unit Aset</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="badge bg-label-primary me-2">{{ $maping->keluar->inventaris->kode_aset ?? '-' }}</span>
                            <span class="badge bg-label-success me-2">{{ $maping->keluar->inventaris->no_inventaris ?? '-' }}</span>
                            <span class="badge bg-label-info">{{ strtoupper($maping->penerima ?? '-') }}</span>
                        </div>

                        <div class="mt-3">
                            <h5 class="mb-1 fw-bold">
                                {{ strtoupper($maping->keluar->inventaris->dataAset->merek ?? '-') }}
                                {{ strtoupper($maping->keluar->inventaris->dataAset->type ?? '-') }}
                            </h5>
                            <span class="text-muted">
                                {{ $maping->keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }} • {{ $maping->perusahaan->nama_perusahaan ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('maping.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFORMASI ASSET --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">
                    <i class="bx bx-desktop me-2 text-primary"></i> Informasi Asset
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <small class="text-muted">Kode Asset</small>
                        <h6 class="fw-bold mt-1">{{ $maping->keluar->inventaris->kode_aset ?? '-' }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <small class="text-muted">No Inventaris</small>
                        <h6 class="fw-bold mt-1">{{ $maping->keluar->inventaris->no_inventaris ?? '-' }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <small class="text-muted">Penerima</small>
                        <h6 class="fw-bold mt-1">{{ strtoupper($maping->penerima ?? '-') }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <small class="text-muted">Lokasi</small>
                        <h6 class="fw-bold mt-1">{{ $maping->lokasi->nama_lokasi ?? '-' }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT MESSAGES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- DAFTAR HAK AKSES TERPASANG (TABEL KOMPAK) --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <h5 class="fw-bold mb-0">
                    <i class="bx bx-key me-2 text-primary"></i> Hak Akses & Aplikasi Terpasang
                </h5>
                <div class="d-flex gap-2 flex-wrap">
                    @if($maping->mapingAccesses->count() > 0)
                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalBulkUpdateAccess">
                            <i class="bx bx-edit me-1"></i> Update Massal Kredensial
                        </button>
                    @endif
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAccess">
                        <i class="bx bx-plus me-1"></i> Tambah Hak Akses / Aplikasi
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">NO</th>
                                <th>NAMA HAK AKSES / APLIKASI</th>
                                <th width="180">KATEGORI & JENIS</th>
                                <th>EMAIL / AKUN</th>
                                <th width="220">PASSWORD</th>
                                <th width="150" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maping->mapingAccesses as $index => $item)
                                @php
                                    $decryptedPass = null;
                                    if(!empty($item->password)) {
                                        try {
                                            $decryptedPass = \Illuminate\Support\Facades\Crypt::decryptString($item->password);
                                        } catch(\Exception $e) {
                                            $decryptedPass = $item->password;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ strtoupper($item->nama_akses) }}</div>
                                        <small class="text-muted">Ditambahkan: {{ $item->created_at->format('d-m-Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if ($item->kategori == 'Aplikasi')
                                            <span class="badge bg-label-primary">APLIKASI</span>
                                        @else
                                            <span class="badge bg-label-success">HAK AKSES</span>
                                        @endif

                                        @switch($item->jenis)
                                            @case('Software')
                                                <span class="badge bg-label-info">SOFTWARE</span>
                                                @break
                                            @case('PPN')
                                                <span class="badge bg-label-warning">PPN</span>
                                                @break
                                            @case('NON PPN')
                                                <span class="badge bg-label-secondary">NON PPN</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark font-monospace">{{ $item->email ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($decryptedPass)
                                            <div class="input-group input-group-sm">
                                                <input type="password" class="form-control bg-white font-monospace" value="{{ $decryptedPass }}" id="pass_val_{{ $item->id }}" readonly>
                                                <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="pass_val_{{ $item->id }}" title="Intip Password">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted small">Tanpa Password</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-access"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama_akses }}"
                                                    data-kategori="{{ $item->kategori }}"
                                                    data-jenis="{{ $item->jenis }}"
                                                    data-email="{{ $item->email ?? '' }}"
                                                    data-password="{{ $decryptedPass ?? '' }}"
                                                    data-url="{{ route('maping.hak-akses.update', ['maping' => $maping->id, 'access' => $item->id]) }}"
                                                    title="Edit Hak Akses">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>

                                            <form action="{{ route('maping.hak-akses.destroy', ['maping' => $maping->id, 'access' => $item->id]) }}"
                                                  method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Hak Akses">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                                        Belum ada Hak Akses maupun Aplikasi yang terdaftar pada asset ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @php
            $lastAccessWithEmail = $maping->mapingAccesses->first(function($item) {
                return !empty($item->email);
            });
            $defaultEmail = $lastAccessWithEmail ? $lastAccessWithEmail->email : '';
            $defaultPassword = '';
            if ($lastAccessWithEmail && !empty($lastAccessWithEmail->password)) {
                try {
                    $defaultPassword = \Illuminate\Support\Facades\Crypt::decryptString($lastAccessWithEmail->password);
                } catch (\Exception $e) {
                    $defaultPassword = $lastAccessWithEmail->password;
                }
            }
        @endphp

        {{-- MODAL TAMBAH HAK AKSES --}}
        <div class="modal fade" id="modalTambahAccess" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('maping.hak-akses.store', $maping->id) }}" method="POST">
                    @csrf
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bx bx-plus-circle me-2 text-primary"></i> Tambah Hak Akses / Aplikasi
                            </h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            @if(!empty($defaultEmail))
                                <div class="alert alert-primary py-2 px-3 mb-3 small d-flex align-items-center">
                                    <i class="bx bx-check-shield me-2 fs-5"></i>
                                    <div>
                                        <strong>Auto-Fill Kredensial Unit:</strong> Email & Password telah otomatis diisi dari data unit ini (dapat disesuaikan jika berbeda).
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Hak Akses / Aplikasi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_akses" class="form-control text-uppercase" placeholder="Contoh: MICROSOFT OFFICE 365, SAP ERP, EMAIL PERUSAHAAN" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" id="add_kategori" class="form-select" required>
                                        <option value="Hak Akses">Hak Akses</option>
                                        <option value="Aplikasi">Aplikasi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                                    <select name="jenis" id="add_jenis" class="form-select" required>
                                        <option value="NON PPN">NON PPN</option>
                                        <option value="PPN">PPN</option>
                                        <option value="Software">Software</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email / Akun (Opsional)</label>
                                <input type="text" name="email" class="form-control font-monospace" value="{{ $defaultEmail }}" placeholder="Contoh: user@perusahaan.com">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Akun (Opsional)</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="add_password" class="form-control font-monospace" value="{{ $defaultPassword }}" placeholder="Masukkan Password Akun">
                                    <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="add_password" title="Intip Password">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Hak Akses
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT HAK AKSES --}}
        <div class="modal fade" id="modalEditAccess" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formEditAccess" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bx bx-edit-alt me-2 text-primary"></i> Edit Hak Akses & Kredensial
                            </h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Hak Akses / Aplikasi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_akses" id="edit_nama_akses_input" class="form-control text-uppercase" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" id="edit_kategori" class="form-select" required>
                                        <option value="Hak Akses">Hak Akses</option>
                                        <option value="Aplikasi">Aplikasi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                                    <select name="jenis" id="edit_jenis" class="form-select" required>
                                        <option value="NON PPN">NON PPN</option>
                                        <option value="PPN">PPN</option>
                                        <option value="Software">Software</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email / Username Akun</label>
                                <input type="text" name="email" id="edit_email" class="form-control" placeholder="Masukkan Email / Username">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Akun (Biarkan kosong jika tidak ingin mengubah)</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="edit_password" class="form-control" placeholder="Masukkan Password Baru">
                                    <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="edit_password" title="Intip Password">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL BULK UPDATE KREDENSIAL --}}
        <div class="modal fade" id="modalBulkUpdateAccess" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('maping.hak-akses.bulk-update', $maping->id) }}" method="POST">
                    @csrf
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-label-warning">
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="bx bx-edit me-2"></i> Update Massal Email & Password
                            </h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Kelompok Akses Target:</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="target_group" id="grp_ppn" value="ppn" checked>
                                    <label class="form-check-label fw-bold text-warning" for="grp_ppn">
                                        <i class="bx bx-folder me-1"></i> Seluruh Hak Akses PPN
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="target_group" id="grp_nonppn" value="nonppn">
                                    <label class="form-check-label fw-bold text-success" for="grp_nonppn">
                                        <i class="bx bx-folder-open me-1"></i> Seluruh Hak Akses NON PPN
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="target_group" id="grp_app" value="aplikasi">
                                    <label class="form-check-label fw-bold text-primary" for="grp_app">
                                        <i class="bx bx-desktop me-1"></i> Seluruh Aplikasi
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_group" id="grp_all" value="all">
                                    <label class="form-check-label fw-bold text-dark" for="grp_all">
                                        <i class="bx bx-key me-1"></i> Semua Hak Akses & Aplikasi Terpasang
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email / Username Baru (Opsional)</label>
                                <input type="text" name="email" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah email">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Baru (Opsional)</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="bulk_password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                                    <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="bulk_password" title="Intip Password">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 mb-0 small">
                                <i class="bx bx-info-circle me-1"></i> Perubahan di atas akan memperbarui kredensial seluruh hak akses terpasang dalam kelompok yang Anda pilih sekaligus.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-check-circle me-1"></i> Update Semua Sekaligus
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. CONFIRM DELETE
            document.querySelectorAll('.form-delete').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus Hak Akses?',
                            text: 'Hak akses akan dihapus dari asset ini.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#696cff',
                            cancelButtonColor: '#8592a3',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else if (confirm('Hapus hak akses dari asset ini?')) {
                        form.submit();
                    }
                });
            });

            // 2. TOGGLE INTIP PASSWORD (SHOW/HIDE)
            document.querySelectorAll('.btn-toggle-pass').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (input) {
                        const icon = this.querySelector('i');
                        if (input.type === 'password') {
                            input.type = 'text';
                            if (icon) {
                                icon.classList.remove('bx-show');
                                icon.classList.add('bx-hide');
                            }
                        } else {
                            input.type = 'password';
                            if (icon) {
                                icon.classList.remove('bx-hide');
                                icon.classList.add('bx-show');
                            }
                        }
                    }
                });
            });

            // 3. AUTO SWITCH JENIS DEPENDING ON KATEGORI
            const addKategori = document.getElementById('add_kategori');
            const addJenis = document.getElementById('add_jenis');
            if (addKategori && addJenis) {
                addKategori.addEventListener('change', function() {
                    if (this.value === 'Aplikasi') {
                        addJenis.value = 'Software';
                    } else if (addJenis.value === 'Software') {
                        addJenis.value = 'NON PPN';
                    }
                });
            }

            const editKategori = document.getElementById('edit_kategori');
            const editJenis = document.getElementById('edit_jenis');
            if (editKategori && editJenis) {
                editKategori.addEventListener('change', function() {
                    if (this.value === 'Aplikasi') {
                        editJenis.value = 'Software';
                    } else if (editJenis.value === 'Software') {
                        editJenis.value = 'NON PPN';
                    }
                });
            }

            // 4. MODAL EDIT HAK AKSES
            document.querySelectorAll('.btn-edit-access').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const nama = this.getAttribute('data-nama');
                    const kategori = this.getAttribute('data-kategori');
                    const jenis = this.getAttribute('data-jenis');
                    const email = this.getAttribute('data-email');
                    const password = this.getAttribute('data-password');
                    const url = this.getAttribute('data-url');

                    document.getElementById('edit_nama_akses_input').value = nama;
                    document.getElementById('edit_kategori').value = kategori;
                    document.getElementById('edit_jenis').value = jenis;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_password').value = password;
                    document.getElementById('formEditAccess').action = url;

                    const modal = new bootstrap.Modal(document.getElementById('modalEditAccess'));
                    modal.show();
                });
            });

        });
    </script>
@endsection
