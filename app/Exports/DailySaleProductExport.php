<?php

namespace App\Exports;

use App\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class DailySaleProductExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        ini_set('memory_limit','512M');
        $request = $this->request;
        $sales = DB::table("product_sale")

                    ->select(DB::raw("product_sale.*, sales.invoice_date, sales.invoice_no, sales.order_id, sales.branch_id, sales.warehouse_id, sales.customer_id, sales.office_sale_man_id, products.product_code, products.product_name, products.brand_id, brands.brand_name, customers.cus_name, uoms.uom_name, sale_men.sale_man,orders.sale_man_id,branches.branch_name"))

                    ->leftjoin('sales', 'sales.id', '=', 'product_sale.sale_id')

                    ->leftjoin('orders', 'orders.id', '=', 'sales.order_id')

                    ->leftjoin('products', 'products.id', '=', 'product_sale.product_id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

                    ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'product_sale.uom_id')

                    ->leftjoin('sale_men', 'sale_men.id', '=', 'sales.office_sale_man_id')

                    //->leftjoin('users as u1', 'u1.id', '=', 'sales.office_sale_man_id')

                    //->leftjoin('users as u2', 'u2.id', '=', 'orders.sale_man_id')

                    ->leftjoin('branches', 'branches.id', '=', 'sales.branch_id');

        if($request->invoice_no != "") {
            $sales->where('sales.invoice_no', $request->invoice_no);
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $sales->whereBetween('sales.invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $sales->whereDate('sales.invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $sales->whereDate('sales.invoice_date', '<=', $request->to_date);
        } else {}

        if($request->warehouse_id != "") {
            $sales->where('sales.warehouse_id', $request->warehouse_id);
        }

        if(isset($request->branch_id) && $request->branch_id != "") {
            $sales->where('sales.branch_id', $request->branch_id);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $sales->whereIn('sales.branch_id',$branch_arr);
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $sales->where('sales.branch_id',$branch);
            }
        }
        if($request->customer_id != "") {
            $sales->where('sales.customer_id', $request->customer_id);
        }

        if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%"); 
            $binds = array(strtolower($request->product_name));
            $sales->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
        }

        /*if($request->brand_id != "") {
            $sales->whereHas('products', function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);                         
            });
        }*/
        if($request->brand_id != "") {
            $sales->where('products.brand_id', $request->brand_id); 
        } else {
            if(Auth::user()->role->id == 6) {
                //for Country Head User
                $access_brands = array();
                foreach(Auth::user()->brands as $brand) {
                    array_push($access_brands, $brand->id);
                }
                
                $sales->whereIn('products.brand_id',$access_brands);
            }
        }

        /*if($request->sale_man_id != "") {
            $sales->whereHas('order', function ($query) use ($request) {
                $query->where('sale_man_id', $request->sale_man_id);                         
            });   
        }*/
        if($request->sale_man_id != "") {
            $sales->where('sales.office_sale_man_id', $request->sale_man_id);
        }

        if($request->category_id != "") {
            $sales->where('products.category_id', $request->category_id);
        }

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
                $sales->orderBy('sales.invoice_no', $order);
                $sales->orderBy('sales.id','DESC');
            }
            else {}
        } else {
            $sales->orderBy('sales.id', 'DESC'); 
        }

       // $data    =  $sales->orderBy('invoice_date', 'DESC')->get(); 
        $data = $sales->get();
        $sale_arr = $data->pluck('sale_id')->toArray();
        /*$access_brands = array();        

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            foreach(Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }
        }

        $brands = DB::table('brands')
                    ->whereIn('id',$access_brands)
                    ->pluck('id')->toArray(); */


        return view('exports.dailySaleProduct', [
            'data' => $data,
            'sale_arr' => $sale_arr,
            'request' => $request,
            'user_role' => Auth::user()->role->id
        ]);
    }

    public function title(): string
    {
        return 'Daily Sale Product Wise Report';
    }
}
