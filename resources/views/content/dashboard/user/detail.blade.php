@extends('layouts/contentNavbarLayout')

@section('title', 'Detail User')

@section('content')

<style>
    .form-label {
        font-size: 14px; /* Ukuran huruf label form */
        font-weight: bold; /* Membuat huruf tebal */
    }

    .card-header h5 {
        font-size: 18px; /* Ukuran huruf judul form */
        font-weight: bold; /* Membuat huruf tebal */
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Data User</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <p>{{ $user->username }}</p>
                </div>
                 <div class="mb-3">
                    <label class="form-label" for="name">Username</label>
                    <p>{{ $user->name }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="role">Role</label>
                    <p>{{ $user->role }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <p>********</p> <!-- Tampilkan placeholder untuk password -->
                </div>
                
                <a href="/dashboard/user" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>

@endsection
