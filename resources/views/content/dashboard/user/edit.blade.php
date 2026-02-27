@extends('layouts/contentNavbarLayout')

@section('title', 'edit-user')

@section('content')

    <style>
        .form-label {
            font-size: 14px;
            /* Ukuran huruf label form */
            font-weight: bold;
            /* Membuat huruf tebal */
        }

        .card-header h5 {
            font-size: 18px;
            /* Ukuran huruf judul form */
            font-weight: bold;
            /* Membuat huruf tebal */
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <form method="post" action="/dashboard/user/update/{{ $user->id }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <a href="/dashboard/user" class="btn btn-secondary mb-3">Kembali</a>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Edit Data User</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="username">Username</label>
                                    <input type="text" id="username" name="username" class="form-control"
                                        value="{{ old('username', $user->username) }}">
                                    @error('username')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="name">Nama</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}">
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="text" id="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}">
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <input type="password" id="password" name="password" class="form-control">
                                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah
                                        password.</small>
                                    @error('password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="role">Role</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="">Pilih Role</option>
                                        <option value="petugas"
                                            {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>petugas</option>
                                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>
                                            manager</option>
                                    </select>
                                    @error('role')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
