<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockLedgerExport implements FromView, WithTitle
{
    /**
    */
    private $data;
    private $op_data;
    private $request;
    public function __construct($data,$op_data,$request){
        $this->data=$data;
        $this->op_data=$op_data;
        $this->request = $request;
    }
    public function view(): View
    {
        // dd($this->sale_over_due);
        return view('exports.stock_ledger_export',[
            'data'=>$this->data,
            'op_data'=>$this->op_data,
            'request' => $this->request,
        ]);
    }

    public function title(): string
    {
        return 'Stock Ledger Export';
    }
}
