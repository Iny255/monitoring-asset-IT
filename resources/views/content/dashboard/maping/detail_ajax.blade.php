<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle mb-0">

        <tbody>

            <tr>
                <th width="30%" class="bg-light">Kode Barang</th>
                <td>{{ $maping->keluar->kode_barang ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Nama Barang</th>
                <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Type</th>
                <td>{{ $maping->keluar->masuk->type ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Merek</th>
                <td>{{ $maping->keluar->masuk->merek ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Warna</th>
                <td>{{ $maping->keluar->warna ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Garansi</th>
                <td>{{ $maping->keluar->masuk->garansi ?? '-' }} Bulan</td>
            </tr>

            <tr>
                <th class="bg-light">No Inventaris</th>
                <td>{{ $maping->keluar->no_inventaris ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Tanggal Beli</th>
                <td>{{ $maping->keluar->masuk->tgl_beli ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Nama Karyawan</th>
                <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Lokasi</th>
                <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Perusahaan</th>
                <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Processor</th>
                <td>{{ $maping->processor ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Device ID</th>
                <td style="word-break: break-all;">
                    {{ $maping->device_id ?? '-' }}
                </td>
            </tr>

            <tr>
                <th class="bg-light">Produk ID</th>
                <td style="word-break: break-all;">
                    {{ $maping->produk_id ?? '-' }}
                </td>
            </tr>

            <tr>
                <th class="bg-light">RAM</th>
                <td>{{ $maping->ram ?? '-' }} GB</td>
            </tr>

            <tr>
                <th class="bg-light">System</th>
                <td>{{ $maping->system ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Version</th>
                <td>{{ $maping->version ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Install On</th>
                <td>{{ $maping->instal_on ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Aplikasi</th>
                <td>{{ $maping->aplikasi ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Hak Akses Data PPN</th>
                <td>{{ $maping->data_p ?? '-' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Hak Akses Data Non PPN</th>
                <td>{{ $maping->data_n ?? '-' }}</td>
            </tr>

        </tbody>

    </table>

</div>
