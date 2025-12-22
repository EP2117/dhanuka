<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SaleOrderPendingExport implements FromView, WithTitle
{
    protected $sale_order_pending;

    public function __construct($sale_order_pending)
    {
        $this->sale_order_pending = $sale_order_pending;
    }
    public function view(): View
    {

        return view('exports.sale_order_pending', [
            'sale_order_pending' => $this->sale_order_pending,
        ]);
    }

    public function title(): string
    {
        return 'Sale Order Pending';
    }
}
