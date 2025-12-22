<?php

namespace App\Exports;

use App\AraSale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;

class AraDailySaleProductExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        $sales = DB::table("ara_product_sale")

                ->select(DB::raw("ara_product_sale.quantity,ara_product_sale.rate,ara_product_sale.total_amount as product_total_amount, ara_products.ara_product_code,ara_products.ara_product_name,ara_products.ara_product_brand,ara_products.ara_product_category,ara_sales.ara_invoice_no,ara_sales.ara_invoice_date,ara_sales.customer_id,ara_sales.sale_man,ara_sales.total_amount as invoice_total_amount,customers.cus_name,customers.cus_code,customers.state_id,customers.township_id"))               

                ->leftjoin('ara_products', 'ara_products.id', '=', 'ara_product_sale.ara_product_id')

                ->leftjoin('ara_sales', 'ara_sales.id', '=', 'ara_product_sale.ara_sale_id')

                ->leftjoin('customers', 'customers.id', '=', 'ara_sales.customer_id');

        if($request->from_date != '' && $request->to_date != '')
        {            
            $sales->whereBetween('ara_sales.ara_invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $sales->whereDate('ara_sales.ara_invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $sales->whereDate('ara_sales.ara_invoice_date', '<=', $request->to_date);
        } else {}

        if($request->customer_id != "") {
            $sales->where('ara_sales.customer_id', $request->customer_id);
        }

        if($request->state_id != "") {
            $sales->where('customers.state_id', $request->state_id);
        }

        if($request->township_id != "") {
            $sales->where('customers.township_id', $request->township_id);
        }

        if($request->sale_man != "") {
            $sales->where('ara_sales.sale_man', $request->sale_man);
        }

        if($request->product_name != "") {
            $sales->where('ara_products.ara_product_name', 'LIKE', '%'.$request->product_name.'%');
        }

        if($request->brand != "") {
            $sales->where('ara_products.ara_product_brand', $request->brand);
        }

        if($request->category != "") {
            $sales->where('ara_products.ara_product_category', $request->category);
        }

        $data    =  $sales->orderBy('ara_invoice_date', 'DESC')->get();

        return view('exports.araDailySaleProductRpt', [
            'data' => $data,
            'request' => $request
        ]);
    }

    public function title(): string
    {
        return 'ERP Sale Product Wise Report';
    }
}
