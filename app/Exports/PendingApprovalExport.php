<?php

namespace App\Exports;

use App\OrderApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class PendingApprovalExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        $data = OrderApproval::with('order','order.customer','order.sale_man','order.branch');

        if($request->approval_no != "") {
            $data->where('approval_no',  $request->approval_no);
        }

        if($request->order_no != '')
        {            
           // $data->whereBetween('invoice_date', array($request->inv_from_date, $request->inv_to_date));
            $data->whereHas('order', function ($query) use ($request) {
                $query->where('order_no', $request->order_no);                      
            });
        }

        if($request->customer_id != '')
        {            
           // $data->whereBetween('invoice_date', array($request->inv_from_date, $request->inv_to_date));
            $data->whereHas('order.customer', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);                         
            });
        }

        if($request->branch_id != '')
        {            
           // $data->whereBetween('invoice_date', array($request->inv_from_date, $request->inv_to_date));
            $data->whereHas('order.branch', function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);                         
            });
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            //$data->whereIn('branch_id',$branch_arr);
            $data->whereHas('order.branch', function ($query) use ($request,$branch_arr) {
                $query->whereIn('branch_id', $branch_arr);                         
            });
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                //$data->where('branch_id',$branch);
                $data->whereHas('order.branch', function ($query) use ($request,$branch) {
                    $query->where('branch_id', $branch);                         
                });
            }
        }

        if($request->from_date != '' && $request->to_date != '')
        {    
            //$query->whereBetween('created_at', array($request->app_from_date, $request->app_to_date));  
            $data->whereDate('created_at', '>=', $request->from_date); 
            $data->whereDate('created_at', '<=', $request->to_date); 
        } else if($request->from_date != '') {
            $data->whereDate('created_at', '>=', $request->from_date);

        }else if($request->to_date != '') {
           // $data->whereDate('invoice_date', '<=', $request->inv_to_date);
            $data->whereDate('created_at', '<=', $request->to_date);
        } else {}

        $data->where('status',0);

        $data = $data->orderBy('approval_no', 'DESC')->get();


        return view('exports.pendingApproval', [
            'data' => $data,
            'request' => $request,
        ]);
    }

    public function title(): string
    {
        return 'Pending Approval Report';
    }
}
