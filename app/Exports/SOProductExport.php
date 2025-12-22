<?php

namespace App\Exports;

use App\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class SOProductExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
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
        
        //$data  = $products->orderBy("orders.order_date", 'DESC')->get();


        return view('exports.soProduct', [
            'data' => $data,
            'request' => $request,
        ]);
    }

    public function title(): string
    {
        return 'Sale Order Product Wise Report';
    }
}
