<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistoryPerjalananExport implements FromView, ShouldAutoSize
{
    protected $inventaris;
    protected $timeline;
    protected $user;

    public function __construct($inventaris, $timeline, $user)
    {
        $this->inventaris = $inventaris;
        $this->timeline = $timeline;
        $this->user = $user;
    }

    public function view(): View
    {
        return view('content.dashboard.history.excel-perjalanan-aset', [
            'inventaris' => $this->inventaris,
            'timeline' => $this->timeline,
            'user' => $this->user,
        ]);
    }
}
