<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class MinMaxExport implements FromView, WithTitle
{
    protected $min_max;
    protected $type;

    public function __construct($min_max,$type)
    {
        $this->min_max = $min_max;
        $this->type = $type;
    }
    public function view(): View
    {

        return view('exports.min_max_export', [
            'min_max' => $this->min_max,
            'type'=>$this->type,
        ]);
    }

    public function title(): string
    {
        return 'Min Max Report';
    }
}
