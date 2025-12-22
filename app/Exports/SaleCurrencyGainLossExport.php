<?php

namespace App\Exports;

use App\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class SaleCurrencyGainLossExport implements FromView, WithTitle
{
    protected $data;

    public function __construct($data,$request)
    {
        $this->data = $data;
        $this->request = $request;
    }
    public function view(): View
    {

        return view('exports.sale_currency_gain_loss_export', [
            'data' => $this->data,
            'request' => $this->request,
        ]);
    }

    public function title(): string
    {
        return 'Sale Currency Gain Loss Report';
    }
}
