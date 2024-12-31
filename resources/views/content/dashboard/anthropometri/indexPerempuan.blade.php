@extends('layouts/contentNavbarLayout')

@section('title', 'Anthropometri')

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

    @if (session('whatsappUrl'))
        <p id="whatsapp-message">Anda akan dialihkan ke WhatsApp...</p>
        <script>
            setTimeout(function() {
                window.open("{{ session('whatsappUrl') }}", "_blank");
            }, 1000);

            setTimeout(function() {
                var message = document.getElementById("whatsapp-message");
                if (message) {
                    message.style.display = 'none';
                }
            }, 5000);
        </script>
        {{ session()->forget('whatsappUrl') }}
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 style="color: navy">Data Anthropometri Perempuan</h5>
                <div>
                    <a href="{{ route('anthropometri.create') }}" class="btn btn-primary">Tambah Data</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ url('/dashboard/anthropometri/perempuan') }}" class="row g-3 mb-4">
                <div class="col-md-7 text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-2">
                            <input type="text" name="search" class="form-control w-100"
                                placeholder="Cari berdasarkan nama balita atau NIK" value="{{ request('search') }}">
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
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Balita</th>
                            <th>BB Lahir</th>
                            <th>Tinggi Badan</th>
                            <th>Berat Badan</th>
                            <th>Actions</th>
                            <th>Observasi</th>
                            <th>Pertumbuhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anthropometriData as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->pendaftaran->nik }}</td>
                                <td>{{ $data->pendaftaran->nama_balita }}</td>
                                <td>{{ $data->pendaftaran->bb_lahir }}</td>
                                <td>{{ $data->tinggi_badan }}</td>
                                <td>{{ $data->berat_badan }}</td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning"
                                        onclick="return confirmEdit({{ $data->id }})"><i
                                            class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-danger"
                                        onclick="confirmDelete({{ $data->id }})">
                                        <i class="bx bx-trash me-1"></i>
                                    </a>
                                    <a href="{{ route('pendaftaran.show', $data->pendaftaran_id) }}"
                                        class="btn btn-primary"><i class="bi bi-eye-fill"></i></a>
                                </td>
                                <td>
                                    <a href="{{ route('anthropometri.observasi', $data->id) }}" class="btn btn-success"><i
                                            class='bx bx-copy-alt'></i></a>
                                </td>
                                <td>
                                    <a href="{{ route('pertumbuhan.petugas', $data->id) }}" class="btn btn-info"><i
                                            class='bx bx-chart'></i></a>
                                </td>
                            </tr>
                        @empty
                            <td colspan="8" class="text-center">Tidak ada data.</td>
                        @endforelse
                    </tbody>
                </table>
                <!-- Tombol navigasi paginate -->
                 <div class="mt-4">
                {{ $anthropometriData->links('pagination::bootstrap-4') }}
            </div>
        </div>

        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/dashboard/anthropometri/delete/' + id;
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Data berhasil dihapus.",
                            icon: "success",
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            }

            function confirmEdit(id) {
                Swal.fire({
                    title: 'Edit data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/dashboard/anthropometri/edit/' + id;
                    }
                });
            }
        </script>

    @endsection
