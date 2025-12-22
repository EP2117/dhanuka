<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Exports\PendingApprovalExport;
use Maatwebsite\Excel\Facades\Excel;
use App\OrderApproval;
use App\Order;
use App\ProductTransition;
use Carbon\Carbon;
use App\Product;
use DB;
use Session;

class OrderApprovalController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $obj = new OrderApproval;

        //auto generate approval no.;
        //$max_id = OrderApproval::where('order_id', $request->order_id)->max('id');
        $count = DB::table('order_approvals')->where('order_id', $request->order_id)->count();
        $max_id = $count + 1;

        $approval_no = $request->order_no.'_'.$max_id;

        $obj->approval_no = $approval_no;
        $obj->order_id = $request->order_id;
        $obj->order_no = $request->order_no;
        $obj->total_amount = $request->total_amount;
        $obj->created_by = Auth::user()->id;
        $obj->updated_by = Auth::user()->id;        
        $obj->save();

        $approval_id = $obj->id;

        
        for($i=0; $i<count($request->product_arr); $i++) {

            //get product pre-defined UOM
            $product_result = Product::select('uom_id')->find($request->product_arr[$i]);
            $main_uom_id = $product_result->uom_id;

            //add product into pivot table
            $pivot = $obj->products()->attach($request->product_arr[$i],['order_id' => $request->order_id,'uom_id' => $request->uom_arr[$i], 'approval_qty' => $request->approval_qty_arr[$i], 'price' => $request->unit_price_arr[$i], 'price_variant' => $request->price_variant_arr[$i], 'total_amount' => $request->total_amount_arr[$i], 'is_foc' => $request->is_foc_arr[$i]]);  

            //get last pivot insert id
            $last_row=DB::table('order_approval_product')->orderBy('id', 'DESC')->first();

            $pivot_id = $last_row->id; 

            //calculate quantity for product pre-defined UOM
            $uom_relation = DB::table('product_selling_uom')
                            ->select('relation')
                            ->where('product_id',$request->product_arr[$i])
                            ->where('uom_id',$request->uom_arr[$i])
                            ->first();
            if($uom_relation) {
                $relation_val = $uom_relation->relation;
            } else {
                //for pre-defined product uom
                $relation_val = 1;
            }

            //add products in transition table=> transition_type = out (for sold out/ approval)
            $obj1 = new ProductTransition;
            $obj1->product_id            = $request->product_arr[$i];
            $obj1->transition_type       = "out";
            $obj1->transition_approval_id   = $approval_id;
            $obj1->transition_product_pivot_id   = $pivot_id;
            $obj1->branch_id = $request->branch_id;
            $obj1->warehouse_id          = Auth::user()->warehouse_id; // for Main Warehouse Entry
            $obj1->transition_date       = Carbon::now()->format('Y-m-d');
            $obj1->transition_product_uom_id        = $request->uom_arr[$i];
            $obj1->transition_product_quantity      = $request->approval_qty_arr[$i];
            $obj1->product_uom_id        = $main_uom_id;
            $obj1->product_quantity      = $request->approval_qty_arr[$i] * $relation_val;
            $obj1->created_by = Auth::user()->id;
            $obj1->updated_by = Auth::user()->id;
            $obj1->save(); 

            //update approved qty in product_order table  
            $approval_qty = 0; 
            $po_result = DB::table('product_order')
                            ->select('approved_quantity')
                            ->where('id',$request->product_pivot_arr[$i])
                            ->first(); 
            if($po_result->approved_quantity == NULL) {
            	$approved_qty = $request->approval_qty_arr[$i];
            } else {
            	$approved_qty = $po_result->approved_quantity + $request->approval_qty_arr[$i];	
            } 
	

			DB::table('product_order')
                    ->where('id', $request->product_pivot_arr[$i])
                    ->update(array('approved_quantity' => $approved_qty));

           /* //get already approved total amount
            $app_total_amount = DB::table('order_approval_product')->where('order_id', $request->order_id)					->sum('total_amount');

            //get order total amount
            $order_total = Order::find($request->order_id);
            $order_total_amount = $order_total->total_amount;

            if($order_total_amount - $app_total_amount == 0) {
            	$status = "Done";
            } else {
            	$status = "Pending";
            }
            //change status in order table;
            $order_total->order_status = $status;
            $order_total->save();*/

            //Check order status is done or not
             $chk_order = DB::table("product_order")

                        ->select(DB::raw("SUM(CASE  WHEN product_quantity IS NOT NULL THEN product_quantity  ELSE 0 END)  as product_qty, SUM(CASE  WHEN approved_quantity IS NOT NULL THEN approved_quantity  ELSE 0 END)  as approved_qty"))
                        ->where('order_id','=',$request->order_id)
                        ->groupBy('order_id')
                        ->first();
            //update order status
            $order = Order::find($request->order_id);
            if($chk_order->product_qty == $chk_order->approved_qty) {
                $status = "Done";
            } else {
                $status = "Pending";
            }
            //change status in order table;
            $order->order_status = $status;
            $order->save();

        }

        $status = "success";
        return compact('status');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //CONVERT_TZ(a_ad_display.displaytime,'+00:00','+04:00');
        $approval = OrderApproval::with('products','products.uom','products.selling_uoms')->find($id);
        return compact('approval');
    }

    public function getApproval($id)
    {
        $data = OrderApproval::orderBy('id', 'DESC')
                ->where('order_id',$id)
                ->where('status',0)
                ->get();
        return response(compact('data'), 200);
    }

    public function getPendingApprovalReport(Request $request)
    {

        $data = OrderApproval::with('order','order.customer','order.sale_man', 'order.branch');

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
                /*$branch = Auth::user()->branch_id;
                //$data->where('branch_id',$branch);
                $data->whereHas('order.branch', function ($query) use ($request,$branch) {
                    $query->where('branch_id', $branch);                         
                });*/
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                //$data->whereIn('branch_id',$branch_arr);
                $data->whereHas('order.branch', function ($query) use ($request,$branch_arr) {
                    $query->whereIn('branch_id', $branch_arr);                         
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
        return response(compact('data'), 200);
    }

    public function exportPendingApprovalReport(Request $request)
    {
        $export = new PendingApprovalExport($request);
        $fileName = 'pending_approval_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }
}
