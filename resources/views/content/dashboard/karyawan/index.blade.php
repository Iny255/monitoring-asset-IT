@extends('layouts/contentNavbarLayout')

@section('title', 'Karyawan')

@section('content')
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
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="">
                <h5 style="color: navy">Data Karyawan</h5>
            </div>
            <div class="">
                <a href="/dashboard/karyawan/create" class="btn btn-primary">Tambah Data Karyawan</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ url('/dashboard/karyawan') }}" class="row g-3 mb-4">
            <div class="col-md-7 text">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1 me-2">
                        <input type="text" name="search" class="form-control w-100"
                            placeholder="Cari berdasarkan nama karyawan" value="{{ request('search') }}">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr class="center">
                        <th width=>KODE KARYAWAN</th>
                        <th>NAMA KARYAWAN</th>
                        <th>JABATAN</th>
                        <th>DIVISI</th>
                        <th>PERUSAHAAN</th>
                        <th width=>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawans as $karyawan)
                    <tr>
                        <td>{{ $karyawan->kode_karyawan }}</td>
                        <td>{{ $karyawan->nama_karyawan }}</td>
                        <td>{{ $karyawan->jabatan }}</td>
                        <td>{{ $karyawan->divisi }}</td>
                        <td>{{ $karyawan->perusahaan }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit"
                                data-id="{{ $karyawan->id }}">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <form id="delete-form-{{ $karyawan->id }}"
                                action="{{ route('karyawan.destroy', $karyawan->id) }}"
                                method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm btn-delete"
                                data-id="{{ $karyawan->id }}">
                                <i class="bx bx-trash"></i>
                            </button>

                        </td>
                        @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $karyawans->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('js/karyawan.js') }}"></script>
@endpush
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // DELETE
         document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data karyawan ini akan dihapus!",
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
                        window.location.href = `/dashboard/karyawan/${id}/edit`;
                    }
                });
            });
        });

    });
</script>
@endsection