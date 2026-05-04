@extends('layouts/contentNavbarLayout')

@section('title', 'User')

@section('content')

    <!-- BOXICONS (WAJIB UNTUK ICON) -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

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
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="text-primary mb-0">Data User</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                Tambah User
            </button>
        </div>

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
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->perusahaan->nama_perusahaan ?? '-' }}</td>

                                <td>
                                    <!-- EDIT -->
                                    <button class="btn btn-warning btn-sm"
                                        onclick='openEditModal(
                                    {{ $user->id }},
                                    @json($user->username),
                                    @json($user->name),
                                    @json($user->email),
                                    @json($user->role),
                                    {{ $user->id_perusahaan ?? 'null' }}
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
                        <h5>Tambah User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama</label>
                                <input type="text" name="name" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control">
                                    <option value="">Pilih Role</option>
                                    <option value="petugas">Petugas</option>
                                    <option value="manager">Manager</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Perusahaan</label>
                                <select name="id_perusahaan" class="form-control">
                                    <option value="">Pilih Perusahaan</option>
                                    @foreach ($perusahaans as $p)
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
                        <button class="btn btn-primary">Simpan</button>
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
                        <h5>Edit User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" id="editUsername" name="username" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama</label>
                                <input type="text" id="editName" name="name" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" id="editEmail" name="email" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Role</label>
                                <select id="editRole" name="role" class="form-control">
                                    <option value="petugas">Petugas</option>
                                    <option value="manager">Manager</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Perusahaan</label>
                                <select id="editPerusahaan" name="id_perusahaan" class="form-control">
                                    @foreach ($perusahaans as $p)
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
                        <button class="btn btn-primary">Update</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ================= SCRIPT ================= -->
    <script>
        function openEditModal(id, username, name, email, role, perusahaan_id) {

            document.getElementById('editForm').action = '/dashboard/user/' + id;

            document.getElementById('editUsername').value = username;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;

            if (perusahaan_id) {
                document.getElementById('editPerusahaan').value = perusahaan_id;
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
