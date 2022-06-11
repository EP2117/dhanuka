<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DailyPurchaseProductExport implements FromView, WithTitle
{
    protected $data;
    protected $request;

    public function __construct($data,$request)
    {
        $this->data = $data;
        $this->request = $request;
    }

    public function view(): View
    {

        return view('exports.daily_purchase_product_report', [
            'data' => $this->data,
            'request' => $this->request,
        ]);
    }

    public function title(): string
    {
        return 'Daily Purchase Product Report';
    }
}
