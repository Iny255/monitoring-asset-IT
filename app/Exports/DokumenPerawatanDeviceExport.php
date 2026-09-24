<?php

namespace App\Exports;

use App\Exports\Sheets\ChecklistPerawatanSheet;
use App\Exports\Sheets\KartuHistoryDeviceSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DokumenPerawatanDeviceExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new ChecklistPerawatanSheet($this->data),
            new KartuHistoryDeviceSheet($this->data),
        ];
    }
}
