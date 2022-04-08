<?php

namespace App\Exports;

use App\ProductTransition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;

class InventoryExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;

        $where = "";
        if($request->from_date != '' && $request->to_date != '')
        {            
            //$products->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
            $where = "product_transitions.transition_date >= '".$request->from_date."' AND product_transitions.transition_date <= '".$request->to_date."'";
        } else if($request->from_date != '') {
            //$products->whereDate('product_transitions.transition_date', '>=', $request->from_date);
            $where = "product_transitions.transition_date >= '".$request->from_date."'";

        }else if($request->to_date != '') {
            //$products->whereDate('product_transitions.transition_date', '<=', $request->to_date);
            $where = "product_transitions.transition_date <= '".$request->to_date."'";
        } else {}

        if($request->warehouse_id != "") {
            $where .= " AND warehouse_id =".$request->warehouse_id;
        }

        if($request->branch_id != "") {
            $where .= " AND branch_id =".$request->branch_id;
        }

        $products = DB::table("products")
                ->select(DB::raw("products.id as product_id, products.product_name, products.brand_id,pt.warehouse_id, products.product_code,pt.add_qty,uom_id,uoms.uom_name,brands.brand_name,categories.category_name, pt.in_qty, pt.receive_qty, IFNULL(pt.out_qty,0) as out_qty, pt.transfer_qty, pt.sale_qty, pt.branch_id, pt.transition_date, pt.approval_qty, pt.revise_qty, pt.approval_sale_qty, pt.revise_sale_qty"))
                  ->leftjoin(DB::raw("(SELECT product_id, warehouse_id, transition_date, branch_id,
                            SUM(CASE  WHEN transition_type = 'in' AND (transition_entry_id IS NOT NULL OR transition_purchase_id IS NOT NULL OR transition_adjustment_id IS NOT NULL OR transition_sale_id IS NOT NULL OR transition_return_id IS NOT NULL)  THEN product_quantity  ELSE 0 END) as in_qty,
                             SUM(CASE  WHEN transition_type = 'in' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END) as receive_qty,
                              SUM(CASE  WHEN product_transitions.transition_type = 'out' THEN product_quantity  ELSE 0 END) as out_qty,
                               SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty,
                                SUM(CASE  WHEN transition_type = 'in' AND transition_approval_id IS NOT NULL THEN product_quantity  ELSE 0 END) as revise_qty,
                                SUM(CASE  WHEN product_transitions.transition_type = 'in' AND transition_adjustment_id IS NOT NULL THEN product_quantity  ELSE 0 END) as add_qty, 
                                 SUM(CASE  WHEN transition_type = 'out' AND transition_approval_id IS NOT NULL AND is_revise IS NULL THEN product_quantity  ELSE 0 END)  as approval_qty, 
                                 SUM(CASE  WHEN transition_type = 'out' AND transition_approval_id IS NOT NULL AND transition_sale_id IS NOT NULL AND is_revise IS NULL THEN product_quantity  ELSE 0 END)  as approval_sale_qty,
                                  SUM(CASE  WHEN transition_type = 'out' AND transition_approval_id IS NOT NULL AND transition_sale_id IS NOT NULL AND is_revise IS NOT NULL THEN product_quantity  ELSE 0 END)  as revise_sale_qty, 
                                  SUM(CASE  WHEN transition_type = 'out' AND transition_sale_id IS NOT NULL AND transition_approval_id IS NULL THEN product_quantity  ELSE 0 END)  as sale_qty
                            FROM product_transitions Where ".$where."
                            GROUP BY product_transitions.product_id

                            ) as pt"),function($join){

                            $join->on("pt.product_id","=","products.id");

                        })  

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('categories', 'categories.id', '=', 'products.category_id');  

        if($request->product_name != "") {
            $products->where('products.product_name', 'LIKE', "%$request->product_name%"); 
        }

        if($request->brand_id != "") {
            $products->where('products.brand_id', $request->brand_id); 
        }

        /*if($request->warehouse_id != "") {
            $products->where('pt.warehouse_id', $request->warehouse_id);
        }*/
        
        $data  = $products->orderBy("product_name")->get();
        

        //for opening qty
        $op_products = DB::table("product_transitions")

                ->select(DB::raw("product_id, products.product_name, products.brand_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_qty, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_qty, SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty"))

                ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('categories', 'categories.id', '=', 'products.category_id');

        $op_products->whereDate('transition_date', '<', $request->from_date);

        if($request->warehouse_id != "") {
            $op_products->where('product_transitions.warehouse_id', $request->warehouse_id);
        }

        if($request->product_name != "") {
            $op_products->where('products.product_name', 'LIKE', "%$request->product_name%"); 
        }

        if($request->brand_id != "") {
            $op_products->where('products.brand_id', $request->brand_id); 
        }
        
        $op_data  = $op_products ->orderBy("product_name")->groupBy("product_id")->get();
        //end for opening qty

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


        if($request->from_date != '' && $request->to_date != '')
        {            
            $order_products->whereBetween('orders.order_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $order_products->whereDate('orders.order_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $order_products->whereDate('orders.order_date', '<=', $request->to_date);
        }else {}

        if($request->branch_id != "") {
            $order_products->where('orders.branch_id', $request->branch_id);
        }

        if($request->warehouse_id != "") {
            $order_products->where('orders.warehouse_id', $request->warehouse_id);
        } /*
        /*if($request->to_date != '') {
            $order_products->whereDate('orders.order_date', '<=', $request->to_date);
        }else {
           $today = Carbon::now()->format('Y-m-d');
           $order_products->whereDate('orders.order_date', '<=', $today);
        }  */      

        if($request->product_name != "") {
            $order_products->where('products.product_name', 'LIKE', "%$request->product_name%"); 
        }

        if($request->brand_id != "") {
            $order_products->where('products.brand_id', $request->brand_id); 
        }
        
        $order_data  = $order_products->orderBy("products.product_name")->groupBy("po.product_id")->get();
        //end for order prouct 

        $access_brands = array();        

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            foreach(Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }
        }

        $brands = DB::table('brands')
                    ->whereIn('id',$access_brands)
                    ->pluck('id')->toArray();

        return view('exports.inventoryRpt', [
            'data' => $data,
            'op_data' => $op_data,
            'request' => $request,
            'order_data' => $order_data,
            'brands' => $brands,
            'user_role' => Auth::user()->role->id
        ]);
    }

    public function title(): string
    {
        return 'Inventory Report';
    }
}
