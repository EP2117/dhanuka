<?php

namespace App\Exports;

use App\AraSale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class AraDailySaleExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        $sales =    AraSale::with('customer','customer.township','customer.state');


        if($request->invoice_no != "") {
            $sales->where('ara_invoice_no', $request->invoice_no);
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $sales->whereBetween('ara_invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $sales->whereDate('ara_invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $sales->whereDate('ara_invoice_date', '<=', $request->to_date);
        } else {}

        if($request->customer_id != "") {
            $sales->where('customer_id', $request->customer_id);
        }

        if($request->state_id != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('state_id', $request->state_id);                         
            });
        }

        if($request->township_id != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('township_id', $request->township_id);                         
            });
        }

        if($request->sale_man != "") {
            $sales->where('sale_man', $request->sale_man);
        }

        $data    =  $sales->orderBy('ara_invoice_date', 'DESC')->get(); 

        return view('exports.araDailySaleRpt', [
            'data' => $data,
            'request' => $request
        ]);
    }

    public function title(): string
    {
        return 'ERP Daily Sale Report';
    }
}
