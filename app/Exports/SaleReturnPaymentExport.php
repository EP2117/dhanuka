<?php

namespace App\Exports;

use App\SaleReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class SaleReturnPaymentExport implements FromView, WithTitle
{
    protected $data;

    public function __construct($data,$request)
    {
        $this->data = $data;
        $this->request = $request;
    }
    public function view(): View
    {

        return view('exports.sale_return_payment_export', [
            'data' => $this->data,
            'request' => $this->request,
        ]);
    }

    public function title(): string
    {
        return 'Return Payment Report';
    }
}
