<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchaseCreditNoteProductExport implements FromView, WithTitle
{
    protected $credit_note;

    public function __construct($credit_note)
    {
        $this->credit_note = $credit_note;
    }

    public function view(): View
    {

        return view('exports.purchase_credit_note_product_wise', [
            'credit_note' => $this->credit_note,
        ]);
    }

    public function title(): string
    {
        return 'Debit Note Product Wise Report';
    }
}
