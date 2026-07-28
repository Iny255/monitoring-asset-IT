<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistoryPerjalananIndexExport implements FromView, ShouldAutoSize
{
    protected $inventarisList;
    protected $user;

    public function __construct($inventarisList, $user)
    {
        $this->inventarisList = $inventarisList;
        $this->user = $user;
    }

    public function view(): View
    {
        return view('content.dashboard.history.excel-perjalanan-index', [
            'inventarisList' => $this->inventarisList,
            'user' => $this->user,
        ]);
    }
}
