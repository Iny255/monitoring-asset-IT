@extends('layouts/contentNavbarLayout')

@section('title', 'Pemakaian Aset')

@section('content')

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Tambah Pemakaian Aset
            </h5>
        </div>

        <div class="card-body">

            <form action="{{ route('transaksi-keluar.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="row">

                    @if (auth()->user()->role == 'super_admin')

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Perusahaan
                            </label>

                            <select name="perusahaan_id" id="perusahaan_id" class="form-select" required>

                                <option value="">
                                    Pilih Perusahaan
                                </option>

                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    @endif

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kategori Aset
                        </label>

                        <select id="kategori_id" class="form-select" required>

                            <option value="">
                                Pilih Kategori
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kode Aset
                        </label>

                        <select name="inventaris_id" id="inventaris_id" class="form-select" required>

                            <option value="">
                                Pilih Kode Aset
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nama Barang
                        </label>

                        <input type="text" id="nama_barang" class="form-control" readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Merek
                        </label>

                        <input type="text" id="merek" class="form-control" readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Type
                        </label>

                        <input type="text" id="type" class="form-control" readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            No Inventaris
                        </label>

                        <input type="text" id="no_inventaris" class="form-control" readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Jenis Penerima
                        </label>

                        <select name="jenis_penerima" id="jenis_penerima" class="form-select" required>

                            <option value="">
                                Pilih
                            </option>

                            <option value="Perorangan">
                                Perorangan
                            </option>

                            <option value="Perdivisi">
                                Perdivisi
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tanggal Keluar
                        </label>

                        <input type="date" name="tgl_keluar" class="form-control"
                            value="{{ old('tgl_keluar', date('Y-m-d')) }}" required>

                    </div>
                </div>

                <div id="group_karyawan">

                    <input type="hidden" name="karyawan_id" id="karyawan_id">

                    <div class="mb-3">

                        <label class="form-label">
                            User Aset
                        </label>

                        <input type="text" id="search_karyawan" class="form-control"
                            placeholder="Cari UID / Nama Karyawan">

                    </div>

                    <div id="result_karyawan" class="list-group">
                    </div>

                </div>

                <div id="group_divisi" style="display:none;">

                    <div class="mb-3">

                        <label class="form-label">
                            Divisi
                        </label>

                        <input type="text" name="divisi_klr" class="form-control">

                    </div>

                </div>
                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Lokasi Penempatan
                        </label>

                        <select name="lokasi_id" id="lokasi_id" class="form-select" required>
                            <option value="">Pilih Lokasi</option>
                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Foto Aset
                        </label>

                        <input type="file" name="gambar" class="form-control">

                    </div>

                </div>


                <div class="text-end">

                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">

                        Kembali

                    </a>

                    <button type="submit" class="btn btn-primary">

                        Simpan

                    </button>

                </div>

            </form>

        </div>


    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function() {

            loadKategori();
            loadLokasi();

            function loadKategori() {
                let perusahaanId = null;

                @if (auth()->user()->role == 'super_admin')

                    perusahaanId = $('#perusahaan_id').val();

                    if (!perusahaanId) {
                        return;
                    }
                @else

                    perusahaanId = {{ auth()->user()->id_perusahaan }};
                @endif

                $.get(
                    '/dashboard/get-kategori/' + perusahaanId,
                    function(data) {

                        let html =
                            '<option value="">Pilih Kategori</option>';

                        data.forEach(function(item) {

                            html += `
                        <option value="${item.id}">
                            ${item.nama_barang}
                        </option>
                    `;

                        });

                        $('#kategori_id').html(html);

                    }
                );
            }

            @if (auth()->user()->role == 'super_admin')

                $('#perusahaan_id').change(function() {

                    $('#kategori_id').html(
                        '<option value="">Pilih Kategori</option>'
                    );

                    $('#inventaris_id').html(
                        '<option value="">Pilih Kode Aset</option>'
                    );

                    $('#lokasi_id').html(
                        '<option value="">Pilih Lokasi</option>'
                    );

                    clearDetail();

                    loadKategori();
                    loadLokasi();

                });
            @endif

            $('#kategori_id').change(function() {

                let kategoriId = $(this).val();

                let perusahaanId =
                    @if (auth()->user()->role == 'super_admin')
                        $('#perusahaan_id').val();
                    @else
                        {{ auth()->user()->id_perusahaan }};
                    @endif

                if (!kategoriId) {
                    return;
                }

                $.post(
                    '/dashboard/get-inventaris', {
                        kategori_id: kategoriId,
                        perusahaan_id: perusahaanId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    function(data) {

                        let html =
                            '<option value="">Pilih Kode Aset</option>';

                        data.forEach(function(row) {

                            html += `
                        <option value="${row.id}">
                            ${row.kode_aset}
                        </option>
                    `;

                        });

                        $('#inventaris_id').html(html);

                    }
                );

            });

            $('#inventaris_id').change(function() {

                let id = $(this).val();

                if (!id) {
                    clearDetail();
                    return;
                }

                $.get(
                    '/dashboard/get-inventaris-detail/' + id,
                    function(res) {

                        $('#nama_barang').val(
                            res.kategori
                        );

                        $('#merek').val(
                            res.merek
                        );

                        $('#type').val(
                            res.type
                        );

                        $('#no_inventaris').val(
                            res.no_inventaris
                        );

                    }
                );

            });

            function clearDetail() {
                $('#nama_barang').val('');
                $('#merek').val('');
                $('#type').val('');
                $('#no_inventaris').val('');
            }

            $('#jenis_penerima').change(function() {

                let jenis = $(this).val();

                if (jenis === 'Perorangan') {

                    $('#group_karyawan').show();
                    $('#group_divisi').hide();

                } else if (jenis === 'Perdivisi') {

                    $('#group_karyawan').hide();
                    $('#group_divisi').show();

                } else {

                    $('#group_karyawan').hide();
                    $('#group_divisi').hide();

                }

            });

            $('#group_karyawan').hide();

            $('#search_karyawan').keyup(function() {

                let keyword = $(this).val();

                if (keyword.length < 2) {

                    $('#result_karyawan').html('');
                    return;

                }

                let perusahaanId =
                    @if (auth()->user()->role == 'super_admin')
                        $('#perusahaan_id').val();
                    @else
                        {{ auth()->user()->id_perusahaan }};
                    @endif

                $.post(
                    '/dashboard/search-karyawan', {
                        keyword: keyword,
                        perusahaan_id: perusahaanId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    function(data) {

                        console.log(data);

                        let rows = data.data ?? data;

                        let html = '';

                        rows.forEach(function(row) {

                            html += `
                            <a href="#"
                            class="list-group-item list-group-item-action pilih-karyawan"
                            data-id="${row.id}"
                            data-nama="${row.nama_karyawan}">

                                <strong>${row.kode_karyawan}</strong>
                                |
                                ${row.nama_karyawan}
                                |
                                ${row.divisi}

                            </a>
                            `;

                        });

                        $('#result_karyawan').html(html);

                    }
                );

            });

            $(document).on(
                'click',
                '.pilih-karyawan',
                function(e) {

                    e.preventDefault();

                    $('#karyawan_id').val(
                        $(this).data('id')
                    );

                    $('#search_karyawan').val(
                        $(this).data('nama')
                    );

                    $('#result_karyawan').html('');

                }
            );

        });

        function loadLokasi() {

            let perusahaanId = null;

            @if (auth()->user()->role == 'super_admin')

                perusahaanId = $('#perusahaan_id').val();

                if (!perusahaanId) {
                    return;
                }
            @else

                perusahaanId = {{ auth()->user()->id_perusahaan }};
            @endif

            $.get('/dashboard/get-lokasi/' + perusahaanId, function(data) {

                let html = '<option value="">Pilih Lokasi</option>';

                data.forEach(function(item) {

                    html += `
                <option value="${item.id}">
                    ${item.nama_lokasi}
                </option>
            `;

                });

                $('#lokasi_id').html(html);

            });

        }
    </script>

@endsection
