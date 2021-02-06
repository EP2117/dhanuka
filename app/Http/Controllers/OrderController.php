<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\User;
use Carbon\Carbon;
use App\Product;
use PDF;
use DB;
use Session;
use App\Exports\SOProductExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 

        $login_year = Session::get('loginYear');

        $data = Order::with('sale_man','approvals','products','products.uom', 'warehouse','customer','products.selling_uoms','branch');

        if($request->order_no != "") {
            $data->where('order_no',  $request->order_no);
        }

        if($request->sale_man_id != "") {
            $data->where('sale_man_id',  $request->sale_man_id);
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $data->whereBetween('order_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('order_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $data->whereDate('order_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('order_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if($request->order_status != "") {
            $data->where('order_status', $request->order_status);
        }

        if($request->customer_id != "") {
            $data->where('customer_id', $request->customer_id);
        }
        if(isset($request->branch_id) && $request->branch_id != "") {
            $data->where('branch_id', $request->branch_id);
        } else {
            //for Country Head and Admin roles (can access multiple branch)
            if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id',$branch_arr);
            } else {
                //other roles can access only one branch
                if(Auth::user()->role->id != 1) { //system can access all branches
                    $branch = Auth::user()->branch_id;
                    $data->where('branch_id',$branch);
                }
            }
        }
        if(Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            foreach(Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);                
                }
            }

            $data->whereIn('sale_man_id', $access_users);
        }
        else if(Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach(Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            $data->whereIn('sale_man_id', $ls_access_users);
        }
        else if(Auth::user()->role->id == 4) {
            //except admin and systme roles
            $data->where('sale_man_id', Auth::user()->id);

        }
        /*else if(Auth::user()->role->id != 1 && Auth::user()->role->id != 2) {
            //except admin and systme roles
            $data->where('sale_man_id', Auth::user()->id);

        } */
        else {

        }

        $data = $data->orderBy('id', 'DESC')->paginate($limit); 
        return response(compact('data'), 200);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new Order;

        //auto generate invoice no;
        $max_id = Order::max('id');
        if($max_id) {
            $max_id = $max_id + 1;
        } else {
            $max_id = 1;
        }
            
        $order_no = "SO".str_pad($max_id,5,"0",STR_PAD_LEFT);

        $order->order_no = $order_no;
        $order->order_date = $request->order_date;
        //$order->warehouse_id = Auth::user()->warehouse_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->customer_id = $request->customer_id;
        $order->remark = $request->remark;

        if(!empty($request->sale_man_id)) {
            $order->sale_man_id = $request->sale_man_id;
        }

        $order->total_amount = $request->sub_total;
        $order->cash_discount = $request->cash_discount;
        $order->net_total = $request->net_total;
        $order->tax = $request->tax;
        $order->tax_amount = $request->tax_amount;
        $order->balance_amount = $request->balance_amount;
        $order->created_by = Auth::user()->id;
        $order->updated_by = Auth::user()->id;        
        $order->save();

        
        for($i=0; $i<count($request->product); $i++) {

            //add product into pivot table
            $pivot = $order->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'actual_rate' => $request->actual_rate[$i], 'discount' => $request->discount[$i], 'other_discount' => $request->other_discount[$i], 'total_amount' => $request->total_amount[$i], 'is_foc' => $request->is_foc[$i]]);  

                    
        }

        $status = "success";
        $order_id = $order->id;
        return compact('status','order_id');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $order = Order::find($id);
        $order->order_no = $request->order_no;
        $order->order_date = $request->order_date;
        //$order->warehouse_id = Auth::user()->warehouse_id;
        $order->customer_id = $request->customer_id;

        $order->total_amount = $request->sub_total;
        $order->cash_discount = $request->cash_discount;
        $order->net_total = $request->net_total;
        $order->tax = $request->tax;
        $order->tax_amount = $request->tax_amount;
        $order->balance_amount = $request->balance_amount;

        $order->updated_at = time();
        $order->updated_by = Auth::user()->id;  
        $order->remark = $request->remark;      
        $order->save();
        
        $ex_pivot_arr = $request->ex_product_pivot;

        //remove id in pivot that are removed in Transfer Form
        $results =array_diff($ex_pivot_arr,$request->product_pivot); //get id that are not in request pivot array
        foreach($results as $key => $val) {
            //delete in pivot
            DB::table('product_order')
                ->where('id', $val)
                ->delete();
        }

        //update in product pivot table
        for($i=0; $i<count($request->product); $i++) {

            //check product already exist or not
            if($request->product_pivot[$i] != '0' && in_array($request->product_pivot[$i], $ex_pivot_arr)) {
                //update existing product in pivot and transition tables
                DB::table('product_order')
                    ->where('id', $request->product_pivot[$i])
                    ->update(array('uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'actual_rate' => $request->actual_rate[$i], 'discount' => $request->discount[$i], 'other_discount' => $request->other_discount[$i], 'total_amount' => $request->total_amount[$i], 'is_foc' => $request->is_foc[$i]));

                
            } else {

                //add product into pivot table if not exist
                /**$pivot = $order->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'price' => $request->unit_price[$i], 'price_variant' => $request->price_variants[$i], 'total_amount' => $request->total_amount[$i], 'is_foc' => $request->is_foc[$i]]);**/ 

                //add product into pivot table
                $pivot = $order->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'actual_rate' => $request->actual_rate[$i], 'discount' => $request->discount[$i], 'other_discount' => $request->other_discount[$i], 'total_amount' => $request->total_amount[$i], 'is_foc' => $request->is_foc[$i]]);

                
            }
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
        $access_brands = array();
        $order = Order::with('products','sale_man','warehouse','customer','products.uom','products.selling_uoms','branch')->find($id);
        if(Auth::user()->role->id == 6) {
            //for Country Head User
            foreach(Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }
        }

        $customer_id = $order->customer_id;
        $previous_balance = 0;
        $chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as previous_balance"))
            ->where('customer_id','=',$customer_id)
            ->groupBy('customer_id')
            ->first();
        if($chk_balance) {
            $previous_balance = $chk_balance->previous_balance;
        }
        return compact('order','access_brands','previous_balance');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //first check approval exit or not
        $approval = DB::table('order_approvals')
                                ->where('order_id',$id)->get();
        if(count($approval) > 0)
        {
            return response(['message' => 'Cannot delete! Already approved this order.']);
        } else {
            $order = Order::find($id);
            $order->products()->detach();
            $order->delete();
            return response(['message' => 'Success! Sale Order has been deleted.']);    
        }
    }

    public function getSaleOrders($cus_id)
    {
        $login_year = Session::get('loginYear');
        //can access all for system role
        $data = Order::with('sale_man')->orderBy('id', 'DESC')
                ->where('order_status','=','Draft')
                ->where('customer_id', $cus_id);
        $data->whereBetween('order_date', array($login_year.'-01-01', $login_year.'-12-31'));
        $data = $data->get();
        return response(compact('data'), 200);
    }

    public function generateOrderPDF($order_id)
    {

       // $data = ['title' => ''];
       // $pdf = PDF::loadView('invoice_print', $data);
       // if(Auth::user()->role->id == "1" || Auth::user()->role->id == "2") {
            $order = Order::with('products','warehouse','customer','products.uom','products.selling_uoms')->find($order_id);  
            return view('exports.order_print', compact('order'));

            $pdf = PDF::loadView('exports.order_print', compact('order'));
            $output = $pdf->output();

            return new Response($output, 200, [
               'Content-Type' => 'application/pdf',
               'Content-Disposition' =>  'inline; filename="order.pdf"',
            ]);
        //}
  
        //return $pdf->stream();

    }

    
    public function getOrderProducts()
    {
        //Start for order product
        $order_products = DB::table("orders")

                ->select(DB::raw("orders.order_date, products.product_name, products.id as product_id, SUM(po.product_quantity) as order_qty"))

                ->leftjoin(DB::raw("(SELECT product_order.product_id, product_order.order_id, (CASE  WHEN suom.relation IS NULL THEN product_quantity  ELSE product_quantity * suom.relation END)  as product_quantity

                            FROM product_order LEFT JOIN product_selling_uom as suom ON suom.product_id = product_order.product_id AND suom.uom_id = product_order.uom_id
                            ) as po"),function($join){

                            $join->on("po.order_id","=","orders.id");

                        }) 

                //->leftjoin('product_order', 'product_order.order_id', '=', 'orders.id')

                ->leftjoin('products', 'products.id', '=', 'po.product_id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id');
        
        $order_data  = $order_products->orderBy("products.product_name")->groupBy("po.product_id")->get();

        return response(compact('order_data'), 200);
        //end for order prouct
    }

    public function getEditOrderProducts($id)
    {
        //Start for order product
        $order_products = DB::table("orders")

                ->select(DB::raw("orders.order_date, products.product_name, products.id as product_id, SUM(po.product_quantity) as order_qty"))

                ->leftjoin(DB::raw("(SELECT product_order.product_id, product_order.order_id, (CASE  WHEN suom.relation IS NULL THEN product_quantity  ELSE product_quantity * suom.relation END)  as product_quantity

                            FROM product_order LEFT JOIN product_selling_uom as suom ON suom.product_id = product_order.product_id AND suom.uom_id = product_order.uom_id WHERE product_order.order_id != ".$id."
                            ) as po"),function($join){

                            $join->on("po.order_id","=","orders.id");

                        }) 

                //->leftjoin('product_order', 'product_order.order_id', '=', 'orders.id')

                ->leftjoin('products', 'products.id', '=', 'po.product_id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id');
        
        $order_data  = $order_products->orderBy("products.product_name")->groupBy("po.product_id")->get();

        return response(compact('order_data'), 200);
        //end for order prouct
    }

    //Sale order product wise report
    public function getSOProductReport(Request $request)
    {
        $products = DB::table("product_order")

                    ->select(DB::raw("product_order.*, orders.order_date, orders.order_no, orders.customer_id, orders.branch_id,orders.sale_man_id, products.product_code, products.product_name, products.brand_id, brands.brand_name, users.name as sale_man, customers.cus_name, uoms.uom_name,branches.branch_name"))

                    ->leftjoin('orders', 'orders.id', '=', 'product_order.order_id')

                    ->leftjoin('products', 'products.id', '=', 'product_order.product_id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'product_order.uom_id')

                    ->leftjoin('branches', 'branches.id', '=', 'orders.branch_id')

                    ->leftjoin('users', 'users.id', '=', 'orders.sale_man_id');

        if($request->order_no != "") {
            $products->where('orders.order_no', $request->order_no);
        }

        if($request->product_code != "") {
            $products->where('products.product_code', $request->product_code);
        }

        if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%"); 
            $binds = array(strtolower($request->product_name));
            $products->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
        }

        if($request->brand_id != "") {
            $products->where('products.brand_id', $request->brand_id); 
        } else {
            if(Auth::user()->role->id == 6) {
                //for Country Head User
                $access_brands = array();
                foreach(Auth::user()->brands as $brand) {
                    array_push($access_brands, $brand->id);
                }
                
                $products->whereIn('products.brand_id',$access_brands);
            }
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $products->whereBetween('orders.order_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $products->whereDate('orders.order_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $products->whereDate('orders.order_date', '<=', $request->to_date);
        } else {}

        if($request->customer_id != "") {
            $products->where('orders.customer_id', $request->customer_id);
        }

        if($request->warehouse_id != "") {
            $products->where('orders.warehouse_id', $request->warehouse_id);
        }

        if($request->branch_id != "") {
            $products->where('orders.branch_id', $request->branch_id);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $products->whereIn('orders.branch_id',$branch_arr);
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $products->where('orders.branch_id',$branch);
            }
        }

        if($request->sale_man_id != "") {
            $products->where('orders.sale_man_id', $request->sale_man_id);   
        } else {
            if(Auth::user()->role->id == 6) {
            //for Country Head User
                $access_users = array();
                foreach(Auth::user()->country_head_children as $ls) {
                    array_push($access_users, $ls->id);
                    $ls_query = User::with('local_supervisor_children')->find($ls->id);
                    foreach($ls_query->local_supervisor_children as $sm) {
                        array_push($access_users, $sm->id);                
                    }
                }

                $products->whereIn('orders.sale_man_id',$access_users);
            }

            if(Auth::user()->role->id == 7) {
                //for Local Supervisor user
                $ls_access_users = array();
                array_push($ls_access_users, Auth::user()->id);
                foreach(Auth::user()->local_supervisor_children as $sm) {
                    array_push($ls_access_users, $sm->id);
                }

                $products->whereIn('orders.sale_man_id',$ls_access_users);
            }

        }

        if($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if($request->sort_by != "") {
            if($request->sort_by == "order_no") {
                $products->orderBy('orders.order_no', $order);
            }
            else {}
        } else {
            $products->orderBy('orders.order_date', 'DESC'); 
        }
        
        //$data  = $products ->orderBy("orders.order_date", 'DESC')->get();
        $data  = $products->get();

        return response(compact('data'), 200);
    }

    public function exportSOProductReport(Request $request)
    {
        $export = new SOProductExport($request);
        $fileName = 'sale_order_product_wise_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }
}
