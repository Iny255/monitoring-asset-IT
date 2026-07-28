<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistoryHakAksesExport implements FromView, ShouldAutoSize
{
    protected $histories;

    public function __construct($histories)
    {
        $this->histories = $histories;
    }

    public function view(): View
    {
        return view('content.dashboard.history.excel-hak-akses', [
            'histories' => $this->histories,
        ]);
    }
}
