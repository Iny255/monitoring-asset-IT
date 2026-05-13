@extends('layouts/contentNavbarLayout')

@section('title', 'History Pencabutan Inventaris')

@section('content')

<div class="card shadow-sm border-0">

    {{-- ========================================= --}}
    {{-- HEADER --}}
    {{-- ========================================= --}}
    <div class="card-header border-0 text-white py-4"
       style="
                    background: linear-gradient(
                    90deg,
                    var(--theme-primary),
                    var(--theme-secondary)
                    );
                    ">

        {{-- TITLE --}}
        <div class="mb-3">

            <h3 class="mb-1 text-white fw-bold">
                History Pencabutan Inventaris
            </h3>

            <small style="opacity:.9; font-size:14px;">
                Riwayat barang yang sudah dicabut dari inventaris
            </small>

        </div>

        {{-- FILTER --}}
        <form method="GET"
            action="{{ route('maping.historyCabut') }}"
            class="row g-2 align-items-end">

            {{-- FILTER PERUSAHAAN --}}
            @if (auth()->user()->role === 'super_admin')

                <div class="col-md-3">

                    <label class="form-label text-white mb-1"
                        style="font-size:11px; letter-spacing:.5px;">

                        PERUSAHAAN

                    </label>

                    <select name="perusahaan"
                        class="form-select border-0 shadow-sm"
                        style="height:38px; font-size:13px;">

                        <option value="">
                            -- Semua Perusahaan --
                        </option>

                        @foreach ($perusahaans as $perusahaan)

                            <option value="{{ $perusahaan->id }}"
                                {{ request('perusahaan') == $perusahaan->id ? 'selected' : '' }}>

                                {{ $perusahaan->nama_perusahaan }}

                            </option>

                        @endforeach

                    </select>

                </div>

            @endif


            {{-- SEARCH --}}
            <div class="{{ auth()->user()->role === 'super_admin' ? 'col-md-5' : 'col-md-7' }}">

                <label class="form-label text-white mb-1"
                    style="font-size:11px; letter-spacing:.5px;">

                    CARI BARANG

                </label>

                <input type="text"
                    name="search"
                    class="form-control border-0 shadow-sm"
                    placeholder="Cari kode / nama barang..."
                    value="{{ request('search') }}"
                    style="height:38px; font-size:13px;">

            </div>


            {{-- BUTTON FILTER --}}
            <div class="col-md-2">

                <button class="btn btn-light shadow-sm w-100"
                    style="height:38px; font-size:13px;">

                    Filter

                </button>

            </div>


            {{-- BUTTON RESET --}}
            @if (request('search') || request('perusahaan'))

                <div class="col-md-2">

                    <a href="{{ route('maping.historyCabut') }}"
                        class="btn btn-outline-light w-100"
                        style="height:38px; font-size:13px;">

                        Reset

                    </a>

                </div>

            @endif

        </form>

    </div>

    {{-- ========================================= --}}
    {{-- BODY --}}
    {{-- ========================================= --}}
   <div class="card-body p-4">


        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light text-center">

                    <tr>

                        <th width="60">
                            No
                        </th>

                        <th>
                            Kode Barang
                        </th>

                        <th>
                            Nama Barang
                        </th>

                        <th>
                            Tanggal Cabut
                        </th>

                        <th>
                            Lokasi
                        </th>

                        <th>
                            Perusahaan
                        </th>

                        <th>
                            Karyawan
                        </th>

                        <th>
                            Kondisi
                        </th>

                        <th>
                            Alasan
                        </th>

                        <th width="100">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($pencabutans as $p)

                        <tr>

                            {{-- NOMOR --}}
                            <td class="text-center fw-semibold">

                                {{ ($pencabutans->currentPage() - 1) * $pencabutans->perPage() + $loop->iteration }}

                            </td>


                            {{-- KODE BARANG --}}
                            <td class="text-center">

                                <button class="badge bg-dark px-3 py-2 border-0 btn-detail"
                                    style="cursor:pointer"
                                    data-url="{{ route('maping.detail', $p->id_maping) }}">

                                    {{ optional($p->keluar)->kode_barang ?? '-' }}

                                </button>

                            </td>


                            {{-- NAMA BARANG --}}
                            <td>

                                {{ optional(optional(optional($p->keluar)->masuk)->kategori)->nama_barang ?? '-' }}

                            </td>


                            {{-- TANGGAL --}}
                            <td class="text-center">

                                <span class="badge bg-light text-danger px-3 py-2">

                                    {{ $p->tanggal_cabut
                                        ? \Carbon\Carbon::parse($p->tanggal_cabut)->format('d M Y')
                                        : '-' }}

                                </span>

                            </td>


                            {{-- LOKASI --}}
                            <td class="text-center">

                                <span class="badge bg-secondary-subtle text-dark px-3 py-2">

                                    {{ optional($p->lokasi)->nama_lokasi ?? '-' }}

                                </span>

                            </td>


                            {{-- PERUSAHAAN --}}
                            <td>

                                <span class="badge bg-primary px-3 py-2">

                                    {{ optional($p->perusahaan)->nama_perusahaan ?? '-' }}

                                </span>

                            </td>


                            {{-- KARYAWAN --}}
                            <td>

                                {{ optional($p->karyawan)->nama_karyawan ?? '-' }}

                            </td>


                            {{-- KONDISI --}}
                            <td class="text-center">

                                @if ($p->kondisi == 'Baik')

                                    <span class="badge bg-success px-3 py-2">
                                        Baik
                                    </span>

                                @elseif($p->kondisi == 'Rusak')

                                    <span class="badge bg-danger px-3 py-2">
                                        Rusak
                                    </span>

                                @else

                                    <span class="badge bg-secondary px-3 py-2">

                                        {{ $p->kondisi }}

                                    </span>

                                @endif

                            </td>


                            {{-- ALASAN --}}
                            <td style="min-width:200px;">

                                {{ $p->alasan ?? '-' }}

                            </td>


                            {{-- AKSI --}}
                            <td class="text-center">

                                <form action="{{ route('history.cabut.hapus', $p->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        class="btn btn-sm btn-danger btn-hapus">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10"
                                class="text-center py-5 text-muted">

                                <i class="bx bx-folder-open"
                                    style="font-size:40px"></i>

                                <div class="mt-2">
                                    Belum ada riwayat pencabutan
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if (method_exists($pencabutans, 'links'))

            <div class="mt-3">

                {{ $pencabutans->appends(request()->query())->links() }}

            </div>

        @endif

    </div>

</div>


{{-- ========================================= --}}
{{-- MODAL DETAIL --}}
{{-- ========================================= --}}
<div class="modal fade" id="modalDetail" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header text-white"
                style="background: linear-gradient(
                    90deg,
                    var(--theme-primary),
                    var(--theme-secondary)
                    );
                    ">

                <h5 class="mb-0 text-white">

                    Informasi Barang

                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">

                </button>

            </div>

            <div class="modal-body" id="detailContent">

                <div class="text-center py-5">

                    <span class="spinner-border"></span>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


{{-- ========================================= --}}
{{-- SCRIPT --}}
{{-- ========================================= --}}
@section('scripts')

<script>

document.addEventListener("DOMContentLoaded", function() {

    // =========================================
    // HAPUS DATA
    // =========================================
    document.querySelectorAll(".btn-hapus").forEach(button => {

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


    // =========================================
    // DETAIL BARANG
    // =========================================
    document.querySelectorAll('.btn-detail').forEach(btn => {

        btn.addEventListener('click', function() {

            let url = this.dataset.url;

            let modalEl = document.getElementById('modalDetail');

            let modal = new bootstrap.Modal(modalEl);

            modal.show();

            document.getElementById('detailContent').innerHTML = `
                <div class="text-center py-5">
                    <span class="spinner-border"></span>
                </div>
            `;

            fetch(url)

                .then(res => {

                    if (!res.ok)
                        throw new Error('404');

                    return res.text();

                })

                .then(html => {

                    document.getElementById('detailContent').innerHTML = html;

                })

                .catch(err => {

                    document.getElementById('detailContent').innerHTML = `
                        <div class="text-danger text-center">
                            Gagal memuat data
                        </div>
                    `;

                    console.error(err);

                });

        });

    });

});

</script>

@endsection