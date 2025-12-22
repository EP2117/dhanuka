<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AraSale;
use App\Imports\AraSaleImport;
use App\Imports\AraSaleProductImport;
use App\Exports\AraDailySaleExport;
use App\Exports\AraDailySaleProductExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;


class AraSaleController extends Controller
{
    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new AraSaleImport();
        Excel::import($import,$path);
        return ["message" => "success"];
    }

    public function saleProductImport() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new AraSaleProductImport();
        Excel::import($import,$path);
        return ["message" => "success"];
    }

    //Daily Sale Report
    public function getDailySaleReport(Request $request) 
    {

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

        return response(compact('data'), 200);
    }

    public function exportAraDailySaleReport(Request $request)
    {
        $export = new AraDailySaleExport($request);
        $fileName = 'erp_daily_sale_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function getDailySaleProductReport(Request $request) 
    {
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

        return response(compact('data'), 200);
    }

    public function exportAraDailySaleProductReport(Request $request)
    {
        $export = new AraDailySaleProductExport($request);
        $fileName = 'erp_daily_sale_product_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

}
