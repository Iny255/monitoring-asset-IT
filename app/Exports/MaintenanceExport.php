<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaintenanceExport implements FromView, ShouldAutoSize
{
    protected $maintenances;
    protected $title;

    public function __construct($maintenances, $title = 'Laporan Servis & Maintenance')
    {
        $this->maintenances = $maintenances;
        $this->title = $title;
    }

    public function view(): View
    {
        return view('content.dashboard.maintenance.excel', [
            'maintenances' => $this->maintenances,
            'title' => $this->title,
        ]);
    }
}
