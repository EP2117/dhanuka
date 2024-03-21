<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DeliveryPendingExport implements FromView, WithTitle
{
    protected $delivery_pending;

    public function __construct($delivery_pending)
    {
        $this->delivery_pending = $delivery_pending;
    }
    public function view(): View
    {

        return view('exports.delivery_pending', [
            'delivery_pending' => $this->delivery_pending,
        ]);
    }

    public function title(): string
    {
        return 'Delivery Pending Report';
    }
}
