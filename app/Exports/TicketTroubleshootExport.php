<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketTroubleshootExport implements FromView, ShouldAutoSize, WithTitle, WithStyles
{
    protected $tickets;
    protected $companyName;
    protected $divisionName;
    protected $periodText;
    protected $title;

    public function __construct($tickets, $companyName = 'PT. SEMBILAN MATAHARI SAKTI', $divisionName = 'IT Sembilan', $periodText = null, $title = 'Checklist Temuan & Tindakan Troubleshoot')
    {
        $this->tickets = $tickets;
        $this->companyName = $companyName ?: 'PT. SEMBILAN MATAHARI SAKTI';
        $this->divisionName = $divisionName ?: 'IT Sembilan';
        $this->periodText = $periodText ?: \Carbon\Carbon::now()->translatedFormat('F Y');
        $this->title = $title;
    }

    public function view(): View
    {
        return view('content.dashboard.e_ticket.excel', [
            'tickets' => $this->tickets,
            'companyName' => $this->companyName,
            'divisionName' => $this->divisionName,
            'periodText' => $this->periodText,
            'title' => $this->title,
        ]);
    }

    public function title(): string
    {
        return 'Troubleshoot';
    }

    public function styles(Worksheet $sheet)
    {
        // Styling tambahan jika dibutuhkan oleh PhpSpreadsheet
        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
        ];
    }
}
