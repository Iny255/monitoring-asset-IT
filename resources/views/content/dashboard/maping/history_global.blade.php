@extends('layouts/contentNavbarLayout')

@section('title', 'History Mutasi Barang')

@section('content')

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header border-0 text-white"
            style="background: linear-gradient(90deg,#0d3b66,#7b8dff); border-radius:10px 10px 0 0;">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 text-white">History Mutasi Barang</h4>
                    <small style="opacity:.9">
                        Riwayat perpindahan lokasi, perusahaan, dan karyawan
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
                            <th>Jenis Barang</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mapings as $m)
                            <tr>

                                {{-- NOMOR --}}
                                <td class="text-center fw-semibold">
                                    {{ ($mapings->currentPage() - 1) * $mapings->perPage() + $loop->iteration }}
                                </td>

                                {{-- KODE BARANG --}}
                                <td class="text-center">
                                    <span class="badge bg-dark px-3 py-2">
                                        {{ optional($m->keluar)->kode_barang ?? '-' }}
                                    </span>
                                </td>

                                {{-- JENIS BARANG --}}
                                <td>
                                    {{ optional(optional($m->keluar)->masuk)->kategori->nama_barang ?? '-' }}
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <button class="btn btn-info btn-sm btn-show-history" data-id="{{ $m->id }}">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $mapings->links() }}
            </div>

        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalHistory" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="mb-0">History Mutasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="historyContent">
                    <div class="text-center">Loading...</div>
                </div>

            </div>
        </div>
    </div>

@endsection

{{-- SCRIPT --}}
<script>
    document.addEventListener('click', function(e) {

        let btn = e.target.closest('.btn-show-history');
        if (!btn) return;

        let id = btn.dataset.id;

        document.getElementById('historyContent').innerHTML = 'Loading...';

        fetch(`/maping/${id}/history-user`)
            .then(res => res.text())
            .then(html => {

                document.getElementById('historyContent').innerHTML = html;

                let modal = new bootstrap.Modal(document.getElementById('modalHistory'));
                modal.show();

            })
            .catch(err => {
                console.error(err);
                document.getElementById('historyContent').innerHTML = 'Gagal load data';
            });

    });
</script>

<script>
    document.addEventListener('click', function(e) {

        if (e.target.closest('.btn-hapus')) {

            let btn = e.target.closest('.btn-hapus');
            let id = btn.dataset.id;
            let card = btn.closest('.history-card');

            Swal.fire({
                title: 'Hapus transaksi?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {

                    fetch(`/mutasi/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest' // 🔥 WAJIB
                            }
                        })
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Response gagal');
                            }
                            return res.json();
                        })
                        .then(data => {

                            if (data.success) {

                                Swal.fire('Berhasil!', 'Data dihapus', 'success');

                                card.style.transition = "0.3s";
                                card.style.opacity = "0";
                                setTimeout(() => card.remove(), 300);

                            } else {
                                Swal.fire('Gagal!', 'Tidak bisa hapus', 'error');
                            }

                        })
                        .catch(err => {
                            console.error('ERROR:', err); // 🔥 biar keliatan di console
                            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                        });

                }

            });
        }

    });
</script>
