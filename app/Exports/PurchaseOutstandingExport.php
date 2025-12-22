<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchaseOutstandingExport implements FromView, WithTitle
{
    /**
    */
    private $purchase_outstandings;
    private $net_paid_amt;
    private $net_balance_amt;
    private $net_inv_amt;
    private $net_gain_loss_amt;
    private $request;
    public function __construct($purchase_outstandings,$net_paid_amt,$net_balance_amt,$net_inv_amt,$net_gain_loss_amt,$request){
        $this->purchase_outstandings=$purchase_outstandings;
        $this->net_paid_amt=$net_paid_amt;
        $this->net_balance_amt=$net_balance_amt;
        $this->net_inv_amt=$net_inv_amt;
        $this->net_gain_loss_amt=$net_gain_loss_amt;
        $this->request = $request;
    }
    public function view(): View
    {
        // dd($this->purchase_outstandings);
        return view('exports.purchase_outstanding_report',[
            'purchase_outstandings'=>$this->purchase_outstandings,
            'net_paid_amt'=>$this->net_paid_amt,
            'net_bal_amt'=>$this->net_balance_amt,
            'net_inv_amt'=>$this->net_inv_amt,
            'net_gain_loss_amt'=>$this->net_gain_loss_amt,
            'request' => $this->request,
        ]);
    }
    // public function headings(): array     {
    //    return [
    //     'စဥ်.','နေ့စွဲ','နိုင်ငံသားအမျိုးအစား','ခရိုင်	','မြို့နယ်အမည်	','စာမျက်နှာနံပါတ်	','မှ','ထိ','ဦးရေ	'
    //     ];
    // }
    public function title(): string
    {
        return 'Purchase Outstanding Export';
    }
}
