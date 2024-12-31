@extends('layouts/contentNavbarLayout')

@section('title', 'Laporan  Posyandu')

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

    <div class="card">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-header" style="color: navy;">Data Laporan Posyandu</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url('/dashboard/laporan/pendaftar-posyandu') }}" class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex">
                            <div class="me-2 col-md-9">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari berdasarkan Nama Balita atau NIK" value="{{ request('search') }}">
                            </div>
                            <div class="me-2 col-md-6">
                                <select name="filter_posyandu" class="form-select">
                                    <option value="" disabled>Filter berdasarkan Posyandu</option>
                                    <option value="">Semua Posyandu</option>
                                    @foreach ($posyandu as $item)
                                        <option value="{{ $item->nama_posyandu }}"
                                            {{ request('filter_posyandu') == $item->nama_posyandu ? 'selected' : '' }}>
                                            {{ $item->nama_posyandu }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </div>

                        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#pdfModal"
                            data-export="excel">Download PDF</button>

                        {{-- <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Download Excel/Pdf
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#excelModal" data-export="excel">Download Excel</button>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#pdfModal" data-export="excel">Download PDF</button>
                                </li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
            </form>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr class="text-center">
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Balita</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Nama Orang Tua</th>
                            <th>Nama Posyandu</th>
                            <th>Dukuh</th>
                            {{-- <th>Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pendaftarans->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        @else
                            @foreach ($pendaftarans as $pendaftaran)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pendaftaran->nik }}</td>
                                    <td>{{ $pendaftaran->nama_balita }}</td>
                                    <td>{{ $pendaftaran->jenis_kelamin }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->format('d-m-Y') }}</td>
                                    <td>{{ $pendaftaran->nama_ortu }}</td>
                                    <td>{{ $pendaftaran->nama_posyandu }}</td>
                                    <td>{{ $pendaftaran->dukuh }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <!-- Tombol navigasi paginate -->
                <div class="mt-4">
                    {{ $pendaftarans->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <!-- Modal Excel Export -->
        <div class="modal fade" id="excelModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dateRangeModalLabel">Pilih Rentang Tanggal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="exportForm" action="{{ route('pendaftaran.export.excel') }}" method="GET">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"
                            onclick="document.getElementById('exportForm').submit();">Download</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pdf-->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateRangeModalLabel">Pilih Bulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="dateRangeForm" method="GET" action="{{ route('pendaftaran.export.pdf') }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="month" class="form-label">Bulan</label>
                            <input type="month" class="form-control" id="month" name="month" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Download PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        const today = new Date();
        const currentMonth = today.toISOString().slice(0, 7);
        document.getElementById('month').value = currentMonth;

        function confirmDelete() {
            return confirm('Apakah Anda yakin ingin menghapus data ini?');
        }

        function confirmEdit() {
            return confirm('Apakah Anda yakin ingin mengedit data ini?');
        }
    </script>
@endsection
