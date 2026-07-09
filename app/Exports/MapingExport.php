<?php

namespace App\Exports;

use App\Models\Maping;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MapingExport implements FromView
{
    protected $mapings;
    protected $namaPerusahaan;

    public function __construct($mapings, $namaPerusahaan)
    {
        $this->mapings = $mapings;
        $this->namaPerusahaan = $namaPerusahaan;
    }

    public function view(): View
    {
        return view('content.dashboard.maping.excel', [
            'mapings' => $this->mapings,
            'namaPerusahaan' => $this->namaPerusahaan,
        ]);
    }
}