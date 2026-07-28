<?php

namespace App\Exports;

use App\Models\Masuk;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasukExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $user = auth()->user();

        $query = Masuk::with([
            'perusahaan',
            'supplier',
            'perusahaanAsal',
            'dataAset.kategori'
        ]);

        if ($user->role != 'super_admin') {
            $query->where('perusahaan_id', $user->id_perusahaan);
        } elseif ($this->request->perusahaan_id) {
            $query->where('perusahaan_id', $this->request->perusahaan_id);
        }

        if ($this->request->tanggal_awal) {
            $query->whereDate(
                'tanggal_pembelian',
                '>=',
                $this->request->tanggal_awal
            );
        }

        if ($this->request->tanggal_akhir) {
            $query->whereDate(
                'tanggal_pembelian',
                '<=',
                $this->request->tanggal_akhir
            );
        }

        if ($this->request->supplier_id) {
            $query->where('supplier_id', $this->request->supplier_id);
        }

        if ($this->request->jenis_masuk) {
            $query->where('jenis_masuk', $this->request->jenis_masuk);
        }

        if ($this->request->search) {

            $search = $this->request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('dataAset.kategori', function ($sub) use ($search) {

                    $sub->where('nama_barang', 'like', "%{$search}%");

                })->orWhereHas('dataAset', function ($sub) use ($search) {

                    $sub->where('merek', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");

                });

            });

        }

        return $query->get()->map(function ($item) {

            return [

                'Perusahaan' =>
                    $item->perusahaan->nama_perusahaan ?? '-',

                'Kategori' =>
                    $item->dataAset->kategori->nama_barang ?? '-',

                'Merek' =>
                    $item->dataAset->merek ?? '-',

                'Type' =>
                    $item->dataAset->type ?? '-',

                'Jenis Masuk' =>
                    $item->jenis_masuk,

                'Asal' =>
                    $item->jenis_masuk == 'Pembelian'
                        ? ($item->supplier->nama_supplier ?? '-')
                        : ($item->perusahaanAsal->nama_perusahaan ?? '-'),

                'Tanggal' =>
                    $item->tanggal_pembelian,

                'Jumlah' =>
                    $item->jumlah,

                'Harga Satuan' =>
                    $item->harga_satuan,

                'Garansi' =>
                    $item->garansi,

                'Keterangan' =>
                    $item->ket_penerimaan,

            ];

        });

    }

    public function headings(): array
    {
        return [
            'PERUSAHAAN',
            'KATEGORI',
            'MEREK',
            'TYPE',
            'JENIS MASUK',
            'ASAL',
            'TANGGAL',
            'JUMLAH',
            'HARGA SATUAN',
            'GARANSI',
            'KETERANGAN',
        ];
    }
}