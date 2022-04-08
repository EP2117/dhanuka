<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CreditPaymentExport implements FromView, WithTitle
{
    protected $credit_payment;
    protected $request;

    public function __construct($credit_payment,$request)
    {
        $this->credit_payment = $credit_payment;
        $this->request = $request;
    }

    public function view(): View
    {

        return view('exports.credit_payment_report', [
            'payments' => $this->credit_payment,
            'request' => $this->request,
        ]);
    }

    public function title(): string
    {
        return 'Credit Payment Report';
    }
}
