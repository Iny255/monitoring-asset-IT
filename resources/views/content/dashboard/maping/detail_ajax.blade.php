<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle mb-0">

        <tbody>

            <tr>
                <th width="30%" class="detail-label">Kode Barang</th>
                <td>{{ $maping->keluar->kode_barang ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Nama Barang</th>
                <td>{{ $maping->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Type</th>
                <td>{{ $maping->keluar->masuk->type ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Merek</th>
                <td>{{ $maping->keluar->masuk->merek ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Warna</th>
                <td>{{ $maping->keluar->warna ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Garansi</th>
                <td>{{ $maping->keluar->masuk->garansi ?? '-' }} Bulan</td>
            </tr>

            <tr>
                <th class="detail-label">No Inventaris</th>
                <td>{{ $maping->keluar->no_inventaris ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Tanggal Beli</th>
                <td>{{ $maping->keluar->masuk->tgl_beli ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Nama Karyawan</th>
                <td>{{ $maping->keluar->karyawan->nama_karyawan ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Lokasi</th>
                <td>{{ $maping->lokasi->nama_lokasi ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Perusahaan</th>
                <td>{{ $maping->perusahaan->nama_perusahaan ?? '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Processor</th>
                <td>{{ $maping->processor ?: '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Device ID</th>
                <td style="word-break: break-all;">
                    {{ $maping->device_id ?: '-' }}
                </td>
            </tr>

            <tr>
                <th class="detail-label">Produk ID</th>
                <td style="word-break: break-all;">
                    {{ $maping->produk_id ?: '-' }}
                </td>
            </tr>

            <tr>
                <th class="detail-label">RAM</th>
                <td>{{ $maping->ram ?: '-' }} GB</td>
            </tr>

            <tr>
                <th class="detail-label">System</th>
                <td>{{ $maping->system ?: '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Version</th>
                <td>{{ $maping->version ?: '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Install On</th>
                <td>{{ $maping->instal_on ?: '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Aplikasi</th>
                <td>{{ $maping->aplikasi ?: '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Hak Akses Data PPN</th>
                <td>{{ $maping->data_p ?: '-' }}</td>
            </tr>

            <tr>
                <th class="detail-label">Hak Akses Data Non PPN</th>
                <td>{{ $maping->data_n ?: '-' }}</td>
            </tr>

        </tbody>

    </table>

</div>
