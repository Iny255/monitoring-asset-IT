@extends('layouts/contentNavbarLayout')

@section('title', 'History Pencabutan Inventaris')

@section('content')

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header border-0 text-white"
            style="background: linear-gradient(90deg,#0d3b66,#7b8dff); border-radius:10px 10px 0 0;">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="mb-0 text-white">History Pencabutan Inventaris</h4>
                    <small style="opacity:.9">
                        Riwayat barang yang sudah dicabut dari inventaris
                    </small>
                </div>

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
                            <th>Tanggal Cabut</th>
                            <th>Lokasi</th>
                            <th>Perusahaan</th>
                            <th>Karyawan</th>
                            <th>Kondisi</th>
                            <th>Alasan</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pencabutans as $p)
                            <tr>

                                <td class="text-center fw-semibold">
                                    {{ ($pencabutans->currentPage() - 1) * $pencabutans->perPage() + $loop->iteration }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-dark px-3 py-2">
                                        {{ optional($p->keluar)->kode_barang ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ optional(optional(optional($p->keluar)->masuk)->kategori)->nama_barang ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-light text-danger px-3 py-2">
                                        {{ $p->tanggal_cabut ? \Carbon\Carbon::parse($p->tanggal_cabut)->format('d M Y') : '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-dark px-3 py-2">
                                        {{ optional($p->lokasi)->nama_lokasi ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                  {{ optional($p->perusahaan)->nama_perusahaan ?? '-' }}
                                </td>

                                <td>
                                   {{ optional($p->karyawan)->nama_karyawan ?? '-' }}
                                </td>

                                <td class="text-center">

                                    @if ($p->kondisi == 'Baik')
                                        <span class="badge bg-success px-3 py-2">Baik</span>
                                    @elseif($p->kondisi == 'Rusak')
                                        <span class="badge bg-danger px-3 py-2">Rusak</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            {{ $p->kondisi }}
                                        </span>
                                    @endif

                                </td>

                                <td style="min-width:200px">
                                    {{ $p->alasan ?? '-' }}
                                </td>

                                <td class="text-center">

                                    <form action="{{ route('history.cabut.hapus', $p->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button" class="btn btn-sm btn-danger btn-hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open" style="font-size:40px"></i>
                                    <div class="mt-2">Belum ada riwayat pencabutan</div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if (method_exists($pencabutans, 'links'))
                <div class="mt-3">
                    {{ $pencabutans->links() }}
                </div>
            @endif

        </div>
    </div>

@endsection


{{-- SWEET ALERT --}}
@section('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const buttons = document.querySelectorAll(".btn-hapus");

            buttons.forEach(button => {

                button.addEventListener("click", function() {

                    let form = this.closest("form");

                    Swal.fire({
                        title: "Hapus Data?",
                        text: "History pencabutan akan dihapus!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Ya, Hapus",
                        cancelButtonText: "Batal"
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });

        });
    </script>
@endsection
