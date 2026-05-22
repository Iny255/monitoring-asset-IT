@extends('layouts/contentNavbarLayout')

@section('title', 'Data Mapping')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0">Data Mapping</h5>

                {{-- HANYA PETUGAS BISA TAMBAH --}}
                @if (auth()->user()->role == 'petugas' || auth()->user()->role == 'super_admin')
                    <a href="/dashboard/maping/create" class="btn btn-primary">
                        Tambah Data Mapping
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            {{-- 🔔 INDIKATOR FILTER --}}
            @if (request()->query())
                <div class="alert alert-info">
                    🔎 Filter aktif
                </div>
            @endif
            {{-- SEARCH --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                {{-- KIRI: FILTER & RESET --}}
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                        🔍 Filter
                    </button>

                    @if (auth()->user()->role === 'manager')
                        <a href="{{ route('manager.maping.index') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    @else
                        <a href="{{ route('maping.index') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    @endif
                </div>

                {{-- KANAN: CETAK --}}
                <div>
                    @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
                        <a href="{{ route('maping.print', request()->query()) }}" target="_blank" class="btn btn-success">
                            🖨️ Cetak
                        </a>
                    @elseif (auth()->user()->role === 'manager')
                        <a href="{{ route('manager.maping.cetak', request()->query()) }}" target="_blank"
                            class="btn btn-success">
                            🖨️ Cetak
                        </a>
                    @endif
                </div>

            </div>

            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Nama Karyawan</th>
                            <th>Lokasi</th>
                            <th>Perusahaan</th>
                            <th>Processor</th>
                            <th>RAM</th>
                            <th>QR</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapings as $index => $maping)
                            <tr>
                                <td class="text-center">
                                    {{ $mapings->firstItem() + $index }}
                                </td>
                                <td>{{ $maping->keluar->kode_barang }}</td>
                                <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                                <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
                                <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                                <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $maping->processor ?? '-' }}</td>
                                <td>{{ $maping->ram }} GB</td>
                                <td class="text-center">

                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate(route('maping.public_show', $maping->id)) !!}

                                </td>
                                <td class="text-center">

                                    @if ($maping->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Dicabut</span>
                                    @endif

                                </td>
                                <td class="text-center">

                                    {{-- ================= PETUGAS ================= --}}
                                    @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
                                        {{-- SHOW --}}
                                        <a href="{{ route('maping.show', $maping->id) }}" class="btn btn-info btn-sm">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $maping->id }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        {{-- DELETE --}}
                                        <form id="delete-form-{{ $maping->id }}"
                                            action="{{ route('maping.destroy', $maping->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $maping->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                        {{-- MUTASI --}}
                                        <button class="btn btn-success btn-sm btn-mutasi" data-id="{{ $maping->id }}">
                                            <i class="bx bx-transfer"></i>
                                        </button>
                                        {{-- CABUT INVENTARIS --}}
                                        <form id="cabut-form-{{ $maping->id }}"
                                            action="{{ route('maping.cabut', $maping->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                        </form>

                                        <button class="btn btn-dark btn-sm btn-cabut" data-id="{{ $maping->id }}"
                                            data-kode="{{ $maping->keluar->kode_barang }}"
                                            data-nama="{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}">
                                            <i class="bx bx-power-off"></i>
                                        </button>

                                        {{-- ================= MANAGER ================= --}}
                                    @elseif(auth()->user()->role === 'manager')
                                        <a href="{{ route('manager.maping.show', $maping->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bx bx-show"></i> Detail
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Data belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $mapings->links() }}
            </div>

        </div>
    </div>
    <!-- MODAL PENCABUTAN -->
    <div class="modal fade" id="modalCabut" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formCabut" method="POST">
                @csrf

                <div class="modal-content">

                    <div class="modal-header text-white modal-theme-header">
                        <h5 class="mb-0" style="color:white;">Form Pencabutan Inventaris</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Kode Barang</label>
                                <input type="text" id="cabut_kode" class="form-control" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama Barang</label>
                                <input type="text" id="cabut_nama" class="form-control" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Pencabutan</label>
                                <input type="date" name="tanggal_cabut" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Kondisi Barang</label>

                                <select name="kondisi" class="form-control" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak">Rusak</option>

                                </select>
                            </div>

                            {{-- ALASAN --}}
                            <div class="col-md-12 mb-3">

                                <label>
                                    Alasan Pencabutan
                                </label>

                                <select name="alasan" id="alasan_select" class="form-control" required>

                                    <option value="">
                                        -- Pilih Alasan --
                                    </option>

                                    <option value="RESIGN">
                                        RESIGN
                                    </option>

                                    <option value="MUTASI KARYAWAN">
                                        MUTASI KARYAWAN
                                    </option>

                                    <option value="LAIN-LAIN">
                                        LAIN-LAIN
                                    </option>

                                </select>

                            </div>

                            {{-- INPUT LAIN-LAIN --}}
                            <div class="col-md-12 mb-3 d-none" id="lainnya_wrapper">

                                <label>
                                    Tulis Alasan Lainnya
                                </label>

                                <textarea name="alasan_lainnya" id="alasan_lainnya" class="form-control" rows="3"
                                    placeholder="Tuliskan alasan pencabutan..."></textarea>


                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button class="btn btn-primary">
                            Simpan Pencabutan
                        </button>

                    </div>

                </div>

            </form>
        </div>
    </div>
    <!-- MODAL FILTER -->
    <div class="modal fade" id="modalFilter" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="GET">
                <div class="modal-content">

                    <div class="modal-header text-white"
                        style="
                            background: linear-gradient(
                                90deg,
                                var(--theme-primary),
                                var(--theme-secondary)
                            );
                        ">
                        <h5 class="mb-0 text-white">Filter Data Mapping</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            {{-- ========================================= --}}
                            {{-- SUPER ADMIN --}}
                            {{-- ========================================= --}}
                            @if (auth()->user()->role === 'super_admin')

                                {{-- PERUSAHAAN --}}
                                <div class="col-md-6 mb-3">

                                    <label>
                                        Perusahaan
                                    </label>

                                    <select name="perusahaan" id="filter_perusahaan" class="form-select">

                                        <option value="">
                                            -- Pilih Perusahaan --
                                        </option>

                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}"
                                                {{ request('perusahaan') == $p->id ? 'selected' : '' }}>

                                                {{ $p->nama_perusahaan }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- LOKASI --}}
                                <div class="col-md-6 mb-3">

                                    <label>
                                        Lokasi
                                    </label>

                                    <select name="lokasi" id="filter_lokasi" class="form-select">

                                        <option value="">
                                            -- Pilih Lokasi --
                                        </option>

                                    </select>

                                </div>
                            @else
                                {{-- USER BIASA --}}
                                <div class="col-md-6 mb-3">

                                    <label>
                                        Lokasi
                                    </label>

                                    <select name="lokasi" class="form-select">

                                        <option value="">
                                            -- Semua --
                                        </option>

                                        @foreach ($lokasis as $l)
                                            <option value="{{ $l->id }}"
                                                {{ request('lokasi') == $l->id ? 'selected' : '' }}>

                                                {{ $l->nama_lokasi }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            @endif

                            <div class="col-md-6 mb-3">
                                <label>Tahun</label>
                                <select name="tahun" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @for ($i = date('Y'); $i >= 2018; $i--)
                                        <option value="{{ $i }}"
                                            {{ request('tahun') == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-select">
                                    <option value="aktif"
                                        {{ request()->get('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="dicabut" {{ request('status') == 'dicabut' ? 'selected' : '' }}>Dicabut
                                    </option>
                                </select>
                            </div>

                            {{-- 🔥 TAMBAHAN BARU --}}
                            <div class="col-md-6 mb-3">
                                <label>Merek</label>
                                <input type="text" name="merek" class="form-control"
                                    value="{{ request('merek') }}" placeholder="Contoh: Lenovo">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Type</label>
                                <input type="text" name="type" class="form-control" value="{{ request('type') }}"
                                    placeholder="Contoh: Ideapad">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control"
                                    value="{{ request('search') }}" placeholder="Nama / Device / Barang / Processor">
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Terapkan Filter</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection


@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // DELETE
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data mapping ini akan dihapus!",
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
                            window.location.href = `/dashboard/maping/${id}/edit`;
                        }
                    });
                });
            });

            // MUTASI
            document.querySelectorAll('.btn-mutasi').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const url = `/dashboard/maping/mutasi/${id}`;

                    Swal.fire({
                        title: 'Mutasi data ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Mutasi',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });

                });
            });
            // CABUT INVENTARIS
            document.querySelectorAll('.btn-cabut').forEach(btn => {

                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const kode = this.dataset.kode;
                    const nama = this.dataset.nama;

                    document.getElementById('cabut_kode').value = kode;
                    document.getElementById('cabut_nama').value = nama;

                    const url = "{{ route('maping.cabut', ':id') }}".replace(':id', id);
                    document.getElementById('formCabut').action = url;

                    let modal = new bootstrap.Modal(document.getElementById('modalCabut'));
                    modal.show();

                });

            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            const perusahaanSelect =
                document.getElementById('filter_perusahaan');

            const lokasiSelect =
                document.getElementById('filter_lokasi');

            // khusus super admin
            if (perusahaanSelect && lokasiSelect) {

                // load lokasi jika perusahaan sudah dipilih
                if (perusahaanSelect.value) {

                    loadLokasi(
                        perusahaanSelect.value,
                        "{{ request('lokasi') }}"
                    );

                }

                perusahaanSelect.addEventListener('change', function() {

                    lokasiSelect.innerHTML =
                        '<option value="">-- Pilih Lokasi --</option>';

                    if (!this.value) {
                        return;
                    }

                    loadLokasi(this.value);

                });

            }

            function loadLokasi(perusahaanId, selectedLokasi = null) {

                fetch(`/maping/lokasi-by-perusahaan/${perusahaanId}`)

                    .then(response => response.json())

                    .then(data => {

                        lokasiSelect.innerHTML =
                            '<option value="">-- Semua --</option>';

                        data.forEach(lokasi => {

                            lokasiSelect.innerHTML += `
                        <option value="${lokasi.id}"
                            ${selectedLokasi == lokasi.id ? 'selected' : ''}>
                            ${lokasi.nama_lokasi}
                        </option>
                    `;

                        });

                    })

                    .catch(error => {

                        console.log(error);

                    });

            }

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const alasanSelect =
                document.getElementById('alasan_select');

            const lainnyaWrapper =
                document.getElementById('lainnya_wrapper');

            const alasanLainnya =
                document.getElementById('alasan_lainnya');

            alasanSelect.addEventListener('change', function() {

                if (this.value === 'LAIN-LAIN') {

                    lainnyaWrapper.classList.remove('d-none');

                    alasanLainnya.setAttribute('required', true);

                } else {

                    lainnyaWrapper.classList.add('d-none');

                    alasanLainnya.removeAttribute('required');

                    alasanLainnya.value = '';

                }

            });

        });
    </script>


@endsection
