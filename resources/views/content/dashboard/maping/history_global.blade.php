@extends('layouts/contentNavbarLayout')

@section('title', 'History Mutasi Barang')

@section('content')

<div class="card shadow-sm border-0">

    {{-- HEADER --}}
    <div class="card-header border-0 text-white"
        style="background: linear-gradient(90deg,#5b6cff,#7b8dff); border-radius:10px 10px 0 0;">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h4 class="mb-0 text-white">History Mutasi Barang</h4>
                <small style="opacity:.9">
                    Riwayat perpindahan lokasi, perusahaan, dan karyawan
                </small>
            </div>

            <!-- <a href="{{ route('maping.index') }}" class="btn btn-light btn-sm shadow-sm">
                ← Kembali
            </a> -->

        </div>
    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light text-center">
                    <tr>
                        <th width="60">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Tanggal</th>
                        <th>Dari Lokasi</th>
                        <th>Ke Lokasi</th>
                        <th>Dari Perusahaan</th>
                        <th>Ke Perusahaan</th>
                        <th>Dari Karyawan</th>
                        <th>Ke Karyawan</th>
                        <th>Inv Lama</th>
                        <th>Inv Baru</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($mutasis as $m)
                    <tr>

                        {{-- NOMOR URUT PAGINATION AMAN --}}
                        <td class="text-center fw-semibold">
                            {{ ($mutasis->currentPage() - 1) * $mutasis->perPage() + $loop->iteration }}
                        </td>
                        {{-- KODE BARANG --}}
                        <td class="text-center">
                            <span class="badge bg-dark px-3 py-2">
                                {{ optional(optional($m->maping)->keluar)->kode_barang ?? '-' }}
                            </span>
                        </td>

                        {{-- NAMA BARANG --}}
                        <td>
                            {{ optional(optional(optional($m->maping)->keluar)->masuk)->kategori->nama_barang ?? '-' }}
                        </td>

                        {{-- TANGGAL --}}
                        <td>
                            <span class="badge bg-light text-primary px-3 py-2">
                                {{ $m->tanggal_mutasi ? \Carbon\Carbon::parse($m->tanggal_mutasi)->format('d M Y') : '-' }}
                            </span>
                        </td>

                        {{-- DARI LOKASI --}}
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-dark px-3 py-2">
                                {{ optional($m->dariLokasi)->nama_lokasi ?? '-' }}
                            </span>
                        </td>

                        {{-- KE LOKASI --}}
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info px-3 py-2">
                                {{ optional($m->keLokasi)->nama_lokasi ?? '-' }}
                            </span>
                        </td>

                        {{-- DARI PERUSAHAAN --}}
                        <td style="min-width:180px">
                            {{ optional($m->dariPerusahaan)->nama_perusahaan ?? '-' }}
                        </td>

                        {{-- KE PERUSAHAAN --}}
                        <td style="min-width:180px">
                            {{ optional($m->kePerusahaan)->nama_perusahaan ?? '-' }}
                        </td>

                        {{-- DARI KARYAWAN --}}
                        <td>
                            {{ optional($m->dariKaryawan)->nama_karyawan ?? '-' }}
                        </td>

                        {{-- KE KARYAWAN --}}
                        <td>
                            {{ optional($m->keKaryawan)->nama_karyawan ?? '-' }}
                        </td>

                        {{-- INV LAMA --}}
                        <td class="text-center">
                            <span class="badge bg-secondary text-white px-3 py-2">
                                {{ $m->dari_no_inventaris ?? '-' }}
                            </span>
                        </td>

                        {{-- INV BARU --}}
                        <td class="text-center">
                            <span class="badge bg-success px-3 py-2">
                                {{ $m->ke_no_inventaris ?? '-' }}
                            </span>
                        </td>

                        <td class="text-center">

                            <form action="{{ route('maping.mutasi.destroy', $m->id) }}"
                                method="POST"
                                class="form-hapus d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="button"
                                    class="btn btn-sm btn-danger rounded-circle btn-hapus"
                                    style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"
                                    title="Hapus">

                                    <i class="bx bx-trash" style="font-size:16px"></i>

                                </button>

                            </form>

                        </td>


                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bx bx-folder-open" style="font-size:40px"></i>
                            <div class="mt-2">Belum ada riwayat mutasi</div>
                        </td>
                    </tr>
                    @endforelse

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                </tbody>
            </table>

        </div>

        {{-- PAGINATION --}}
        @if(method_exists($mutasis,'links'))
        <div class="mt-3">
            {{ $mutasis->links() }}
        </div>
        @endif

    </div>
</div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.btn-hapus').forEach(function(button) {

            button.addEventListener('click', function() {

                let form = this.closest('.form-hapus');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data mutasi akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

        });

    });
</script>