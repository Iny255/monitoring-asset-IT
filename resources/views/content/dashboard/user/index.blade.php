@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen User Sistem')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-user-circle fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Manajemen User Sistem</h3>
                            <small class="text-muted">Kelola akun pengguna dan hak akses peran (Super Admin, Petugas, User/Karyawan)</small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="bx bx-user-plus me-1"></i> Tambah User
                        </button>
                    </div>
                </div>
            </div>
        </div>

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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">

            <!-- SEARCH -->
            <form method="GET" action="{{ url('/dashboard/user') }}" class="row mb-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Cari username/email"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Cari</button>
                </div>
            </form>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Profil Karyawan</th>
                            <th>Perusahaan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @switch($user->role)
                                        @case('super_admin')
                                            <span class="badge bg-danger">Super Admin</span>
                                            @break
                                        @case('petugas')
                                            <span class="badge bg-primary">Petugas IT</span>
                                            @break
                                        @default
                                            <span class="badge bg-info">User / Karyawan</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if ($user->karyawan)
                                        <strong>{{ $user->karyawan->nama_karyawan }}</strong>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->role === 'super_admin')
                                        <span class="badge bg-label-primary">Semua Perusahaan</span>
                                    @elseif ($user->perusahaan)
                                        <strong>{{ $user->perusahaan->nama_perusahaan }}</strong>
                                        @if ($user->perusahaan->tipe === 'Cabang' || $user->perusahaan->parent_id)
                                            <br>
                                            <small class="text-muted"><i class="bx bx-git-branch me-1"></i>Cabang {{ $user->perusahaan->parent->nama_perusahaan ?? '' }}</small>
                                        @else
                                            <br>
                                            <small class="text-primary"><i class="bx bx-building-house me-1"></i>Holding / Induk</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <!-- EDIT -->
                                    <button class="btn btn-warning btn-sm"
                                        onclick='openEditModal(
                                    {{ $user->id }},
                                    @json($user->username),
                                    @json($user->name),
                                    @json($user->email),
                                    @json($user->role),
                                    {{ $user->id_perusahaan ?? 'null' }},
                                    {{ $user->karyawan_id ?? 'null' }}
                                )'>
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <!-- DELETE -->
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <!-- DETAIL -->
                                    @if (auth()->user()->role === 'super_admin')
                                        <a href="{{ url('/dashboard/user/' . $user->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links('pagination::bootstrap-4') }}

        </div>
    </div>

    <!-- ================= CREATE MODAL ================= -->
    <div class="modal fade" id="createUserModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" action="{{ url('/dashboard/user') }}">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah User Baru</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Role Access <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="">Pilih Role</option>
                                    <option value="user">User / Karyawan (Pelapor Tiket)</option>
                                    <option value="petugas">Petugas IT Support</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Profil Karyawan (Opsional)</label>
                                <select name="karyawan_id" class="form-select">
                                    <option value="">-- Tidak Terhubung ke Karyawan --</option>
                                    @foreach ($karyawans as $kary)
                                        <option value="{{ $kary->id }}">{{ $kary->nama_karyawan }} ({{ $kary->kode_karyawan ?? 'NIK: -' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Perusahaan / Cabang</label>
                                <select name="id_perusahaan" class="form-select">
                                    <option value="">Pilih Perusahaan / Cabang</option>
                                    @foreach ($parentPerusahaans as $induk)
                                        <optgroup label="{{ $induk->nama_perusahaan }} (Holding)">
                                            <option value="{{ $induk->id }}">{{ $induk->nama_perusahaan }} (Induk / HO)</option>
                                            @foreach ($induk->cabangs as $cabang)
                                                <option value="{{ $cabang->id }}">&nbsp;&nbsp;↳ {{ $cabang->nama_perusahaan }} (Cabang)</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                    @foreach ($perusahaans->whereNull('parent_id')->where('cabangs', '==', collect()) as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary">Simpan User</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT MODAL ================= -->
    <div class="modal fade" id="editUserModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" id="editUsername" name="username" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama</label>
                                <input type="text" id="editName" name="name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" id="editEmail" name="email" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Password (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Role Access</label>
                                <select id="editRole" name="role" class="form-select" required>
                                    <option value="user">User / Karyawan (Pelapor Tiket)</option>
                                    <option value="petugas">Petugas IT Support</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Profil Karyawan (Opsional)</label>
                                <select id="editKaryawan" name="karyawan_id" class="form-select">
                                    <option value="">-- Tidak Terhubung ke Karyawan --</option>
                                    @foreach ($karyawans as $kary)
                                        <option value="{{ $kary->id }}">{{ $kary->nama_karyawan }} ({{ $kary->kode_karyawan ?? 'NIK: -' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Perusahaan / Cabang</label>
                                <select id="editPerusahaan" name="id_perusahaan" class="form-select">
                                    <option value="">Pilih Perusahaan / Cabang</option>
                                    @foreach ($parentPerusahaans as $induk)
                                        <optgroup label="{{ $induk->nama_perusahaan }} (Holding)">
                                            <option value="{{ $induk->id }}">{{ $induk->nama_perusahaan }} (Induk / HO)</option>
                                            @foreach ($induk->cabangs as $cabang)
                                                <option value="{{ $cabang->id }}">&nbsp;&nbsp;↳ {{ $cabang->nama_perusahaan }} (Cabang)</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary">Update User</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ================= SCRIPT ================= -->
    <script>
        function openEditModal(id, username, name, email, role, perusahaan_id, karyawan_id) {

            document.getElementById('editForm').action = '/dashboard/user/' + id;

            document.getElementById('editUsername').value = username;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;

            if (perusahaan_id) {
                document.getElementById('editPerusahaan').value = perusahaan_id;
            } else {
                document.getElementById('editPerusahaan').value = '';
            }

            if (karyawan_id) {
                document.getElementById('editKaryawan').value = karyawan_id;
            } else {
                document.getElementById('editKaryawan').value = '';
            }

            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        function confirmDelete(id) {

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
                    window.location.href = '/dashboard/hapususer/' + id;
                }

            });
        }
    </script>

@endsection
