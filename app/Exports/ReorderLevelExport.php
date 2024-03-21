<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReorderLevelExport implements FromView, WithTitle
{
    protected $reorder_level;

    public function __construct($reorder_level)
    {
        $this->reorder_level = $reorder_level;
    }
    public function view(): View
    {

        return view('exports.reorder_level_export', [
            'reorder_level' => $this->reorder_level,
        ]);
    }

    public function title(): string
    {
        return 'Reorder Level Report';
    }
}
