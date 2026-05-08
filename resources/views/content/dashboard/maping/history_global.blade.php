@extends('layouts/contentNavbarLayout')

@section('title', 'History Mutasi Barang')

@section('content')

    <div class="card shadow-sm border-0">

        {{-- ========================================= --}}
        {{-- HEADER --}}
        {{-- ========================================= --}}
        <div class="card-header border-0 text-white py-4"
            style="background: linear-gradient(90deg,#0d3b66,#7b8dff); border-radius:12px 12px 0 0;">

            <div class="row">

                {{-- TITLE --}}
                <div class="col-12 mb-3">

                    <h3 class="mb-1 text-white fw-bold">
                        History Mutasi Barang
                    </h3>

                    <small style="opacity:.9; font-size:14px;">
                        Riwayat perpindahan lokasi, perusahaan, dan karyawan
                    </small>

                </div>

                {{-- FILTER --}}
                @if (auth()->user()->role === 'super_admin')

                    <div class="col-md-4">

                        <form method="GET">

                            <label class="form-label text-white fw-semibold mb-2">
                                Filter Perusahaan
                            </label>

                            <div class="input-group">

                                <select name="perusahaan"
                                    class="form-select border-0 shadow-sm"
                                    onchange="this.form.submit()"
                                    style="height:45px;">

                                    <option value="">
                                        -- Semua Perusahaan --
                                    </option>

                                    @foreach ($perusahaans as $p)

                                        <option value="{{ $p->id }}"
                                            {{ request('perusahaan') == $p->id ? 'selected' : '' }}>

                                            {{ $p->nama_perusahaan }}

                                        </option>

                                    @endforeach

                                </select>

                                @if (request('perusahaan'))

                                    <a href="{{ route('maping.historyGlobal') }}"
                                        class="btn btn-light border-0 shadow-sm">

                                        Reset

                                    </a>

                                @endif

                            </div>

                        </form>

                    </div>

                @endif

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- BODY --}}
        {{-- ========================================= --}}
        <div class="card-body">

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light text-center">

                        <tr>

                            <th width="70">
                                No
                            </th>

                            {{-- SUPER ADMIN --}}
                            @if (auth()->user()->role === 'super_admin')

                                <th>
                                    Perusahaan
                                </th>

                            @endif

                            <th width="180">
                                Kode Barang
                            </th>

                            <th>
                                Jenis Barang
                            </th>

                            <th width="120">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($mapings as $m)

                            <tr>

                                {{-- NOMOR --}}
                                <td class="text-center fw-semibold">

                                    {{ ($mapings->currentPage() - 1) * $mapings->perPage() + $loop->iteration }}

                                </td>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role === 'super_admin')

                                    <td class="text-center">

                                        <span class="badge rounded-pill bg-primary px-3 py-2"
                                            style="font-size:13px;">

                                            {{ optional($m->perusahaan)->nama_perusahaan ?? '-' }}

                                        </span>

                                    </td>

                                @endif

                                {{-- KODE BARANG --}}
                                <td class="text-center">

                                    <button type="button"
                                        class="badge bg-dark border-0 px-3 py-2 btn-show-detail"
                                        data-id="{{ $m->id }}"
                                        style="cursor:pointer; font-size:13px;">

                                        {{ optional($m->keluar)->kode_barang ?? '-' }}

                                    </button>

                                </td>

                                {{-- JENIS BARANG --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ optional(optional($m->keluar)->masuk)->kategori->nama_barang ?? '-' }}

                                    </div>

                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">

                                    <button type="button"
                                        class="btn btn-info btn-sm shadow-sm btn-show-history"
                                        data-id="{{ $m->id }}">

                                        <i class="bx bx-show"></i>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role === 'super_admin' ? 5 : 4 }}"
                                    class="text-center py-5 text-muted">

                                    <i class="bx bx-data fs-1 d-block mb-2"></i>

                                    Belum ada data mutasi

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- ========================================= --}}
            {{-- PAGINATION --}}
            {{-- ========================================= --}}
            @if ($mapings->hasPages())

                <div class="d-flex justify-content-end align-items-center mt-4">

                    {{ $mapings->onEachSide(1)->links() }}

                </div>

            @endif

        </div>

    </div>

    {{-- ========================================= --}}
    {{-- MODAL HISTORY --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalHistory" tabindex="-1">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content border-0 shadow">

                <div class="modal-header text-white"
                    style="background: linear-gradient(90deg,#0d3b66,#7b8dff);">

                    <h5 class="mb-0 text-white fw-semibold">
                        History Mutasi
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body" id="historyContent">

                    <div class="text-center py-4">

                        <div class="spinner-border text-primary mb-3"></div>

                        <div>
                            Loading...
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ========================================= --}}
    {{-- MODAL DETAIL --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content border-0 shadow">

                <div class="modal-header text-white"
                    style="background: linear-gradient(90deg,#0d3b66,#7b8dff);">

                    <h5 class="mb-0 text-white fw-semibold">
                        Informasi Barang
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body" id="detailContent">

                    <div class="text-center py-4">

                        <div class="spinner-border text-primary mb-3"></div>

                        <div>
                            Loading...
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

{{-- ========================================= --}}
{{-- STYLE --}}
{{-- ========================================= --}}
@section('page-style')

<style>

    .pagination{
        margin-bottom:0;
    }

    .pagination .page-link{
        border-radius:8px;
        margin:0 2px;
    }

</style>

@endsection

{{-- ========================================= --}}
{{-- SCRIPT --}}
{{-- ========================================= --}}
@section('scripts')

    {{-- HISTORY --}}
    <script>

        document.addEventListener('click', function(e) {

            let btn = e.target.closest('.btn-show-history');

            if (!btn) return;

            let id = btn.dataset.id;

            let content =
                document.getElementById('historyContent');

            content.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3"></div>
                    <div>Loading...</div>
                </div>
            `;

            fetch(`/maping/${id}/history-user`, {

                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }

            })

            .then(res => {

                if (!res.ok) {

                    if (res.status === 403) {
                        throw new Error('Akses ditolak (403)');
                    }

                    if (res.status === 404) {
                        throw new Error('Data tidak ditemukan (404)');
                    }

                    throw new Error('Terjadi kesalahan server');

                }

                return res.text();

            })

            .then(html => {

                content.innerHTML = html;

                let modal =
                    new bootstrap.Modal(
                        document.getElementById('modalHistory')
                    );

                modal.show();

            })

            .catch(err => {

                console.error(err);

                content.innerHTML = `
                    <div class="alert alert-danger text-center">
                        ${err.message}
                    </div>
                `;

            });

        });

    </script>

    {{-- DETAIL --}}
    <script>

        document.addEventListener('click', function(e) {

            let btn = e.target.closest('.btn-show-detail');

            if (!btn) return;

            let id = btn.dataset.id;

            document.getElementById('detailContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3"></div>
                    <div>Loading...</div>
                </div>
            `;

            fetch(`/maping/${id}/detail-ajax`)

            .then(res => res.text())

            .then(html => {

                document.getElementById('detailContent').innerHTML =
                    html;

                let modal =
                    new bootstrap.Modal(
                        document.getElementById('modalDetail')
                    );

                modal.show();

            })

            .catch(err => {

                console.error(err);

                document.getElementById('detailContent').innerHTML = `
                    <div class="alert alert-danger text-center">
                        Gagal load data
                    </div>
                `;

            });

        });

    </script>

@endsection