<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerLog;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BlackListExport;
use Session;
use DB;

class CustomerLogController extends Controller
{
    public function getBlackList(Request $request) {
    	$data = CustomerLog::with('customer','user');
        $data->whereHas('customer',function($q)use($request){
            $q->where('is_active',1);
        });
    	if($request->from_date != '' && $request->to_date != '')
        {    
            //$query->whereBetween('created_at', array($request->app_from_date, $request->app_to_date));  
            $data->whereDate('added_time', '>=', $request->from_date); 
            $data->whereDate('added_time', '<=', $request->to_date); 
        } else if($request->from_date != '') {
            $data->whereDate('added_time', '>=', $request->from_date);

        }else if($request->to_date != '') {
           // $data->whereDate('invoice_date', '<=', $request->inv_to_date);
            $data->whereDate('added_time', '<=', $request->to_date);
        } else {}

        if($request->customer_id != "") {
        	$data->where('customer_id',$request->customer_id);
        }

        if($request->user_id != "") {
        	$data->where('added_by',$request->user_id);
        }

        $data = $data->orderBy('customer_id')->get();
        return $data;
    }

    public function getBlackListReport(Request $request)
    {
        $data = $this->getBlackList($request);
        return response(compact('data'), 200);
    }

    public function exportBlackListReport(Request $request)
    {
        $data = $this->getBlackList($request);;
        $export = new BlackListExport($data,$request);
        $fileName = 'black_list_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }
}
