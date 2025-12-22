<?php

namespace App\Exports;

use App\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class DailySaleExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        $sales =    Sale::with('customer','order','sale_man','installer','customer.township','customer.customer_type','warehouse','branch');


        if($request->invoice_no != "") {
            $sales->where('invoice_no', $request->invoice_no);
        }

        if($request->ref_no != "") {
            $sales->where('reference_no', $request->ref_no);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $sales->whereIn('branch_id',$branch_arr);
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $sales->where('branch_id',$branch);
            }
        }

        if(isset($request->branch_id) && $request->branch_id != "") {
            $sales->where('branch_id', $request->branch_id);
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $sales->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $sales->whereDate('invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $sales->whereDate('invoice_date', '<=', $request->to_date);
        } else {}

        if($request->warehouse_id != "") {
            $sales->where('warehouse_id', $request->warehouse_id);
        }

        if($request->customer_id != "") {
            $sales->where('customer_id', $request->customer_id);
        }

        if($request->cus_type != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('customer_type_id', $request->cus_type);                         
            });
        }

        if($request->township_id != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('township_id', $request->township_id);                         
            });
        }

        if($request->sale_man_id != "") {
            $sales->where('office_sale_man_id', $request->sale_man_id);
        }

        if ($request->installer_id != "") {
            $sales->where('installer_id', $request->installer_id);
        }

        if ($request->remark != "") {
            $sales->where('remark', 'LIKE', '%'.$request->remark.'%');
        }

        /**if($request->office_sale_man_id != "") {
            $sales->where('office_sale_man_id', $request->office_sale_man_id);
        }**/

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            $office_sale_man_arr = array();
            foreach(Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);                
                }
            }

            foreach(Auth::user()->office_sale_mans as $osm) {
                array_push($office_sale_man_arr, $osm->id);
            }
            
            //get order user's order id
            $orders = DB::table('orders')
                            ->whereIn('created_by',$access_users)
                            ->pluck('id')->toArray();

           // $sales->whereIn('order_id',$orders);
            $sales->where(function($query) use ($orders, $office_sale_man_arr) {
                $query->whereIn('order_id',$orders)
                      ->orWhereIn('office_sale_man_id',$office_sale_man_arr);
            });
        }
    
        if(Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach(Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                            ->whereIn('created_by',$ls_access_users)
                            ->pluck('id')->toArray();

            $sales->whereIn('order_id',$orders);
        }

        if(Auth::user()->role->id == 4) {
            //for office order user
            //get order user's order id
            $orders = DB::table('orders')
                            ->where('created_by',Auth::user()->id)
                            ->pluck('id')->toArray();

            $sales->whereIn('order_id',$orders);
        }

        if($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if($request->sort_by != "") {
            if($request->sort_by == "invoice_no") {
                $sales->orderBy('invoice_no', $order);
            }
            else {}
        } else {
            $sales->orderBy('invoice_date', 'DESC'); 
        }

       //$data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        $data = $sales->get(); 

        //$data    =  $sales->orderBy('invoice_date', 'DESC')->get(); 

        return view('exports.dailySaleRpt', [
            'data' => $data,
            'request' => $request
        ]);
    }

    public function title(): string
    {
        return 'Daily Sale Report';
    }
}
