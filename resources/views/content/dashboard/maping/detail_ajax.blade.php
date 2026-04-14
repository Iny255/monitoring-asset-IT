<div class="row">

    <div class="col-md-12">


            <div class="card-body p-0">
                <table class="table table-bordered mb-0">

                    <tr>
                        <th width="35%">Kode Barang</th>
                        <td>{{ $maping->keluar->kode_barang ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Type</th>
                        <td>{{ $maping->keluar->masuk->type ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Merek</th>
                        <td>{{ $maping->keluar->masuk->merek ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Warna</th>
                        <td>{{ $maping->keluar->warna ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Garansi</th>
                        <td>{{ $maping->keluar->masuk->garansi ?? '-' }} Bulan</td>
                    </tr>

                    <tr>
                        <th>No Inventaris</th>
                        <td>{{ $maping->keluar->no_inventaris ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Tanggal Beli</th>
                        <td>{{ $maping->keluar->masuk->tgl_beli ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nama Karyawan</th>
                        <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Perusahaan</th>
                        <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Processor</th>
                        <td>{{ $maping->processor ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Device ID</th>
                        <td>{{ $maping->device_id ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Produk ID</th>
                        <td>{{ $maping->produk_id ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>RAM</th>
                        <td>{{ $maping->ram ?? '-' }} GB</td>
                    </tr>

                    <tr>
                        <th>System</th>
                        <td>{{ $maping->system ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Version</th>
                        <td>{{ $maping->version ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Instal On</th>
                        <td>{{ $maping->instal_on ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Aplikasi</th>
                        <td>{{ $maping->aplikasi ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Hak Akses Data PPN</th>
                        <td>{{ $maping->data_p ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Hak Akses Data Non PPN</th>
                        <td>{{ $maping->data_n ?? '-' }}</td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

</div>
