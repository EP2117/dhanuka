<?php

namespace App\Http\Controllers;

use DB;
use App\Sale;
use APp\User;
use App\Product;
use App\Transfer;
use Carbon\Carbon;
use App\Collection;
use App\ProductTransition;
use Illuminate\Http\Request;
use App\Exports\InventoryExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\Report\GetReport;
use Illuminate\Validation\ValidationException;

class ProductTransitionController extends Controller
{
    use GetReport;
	public function getProductsByUserWarehouse() {
		$data = DB::table("product_transitions")
	    		->select(DB::raw("product_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.brand_id, products.category_id,products.cost_price ,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count"))
	    		->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')
                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

	    		->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

	    		->where("product_transitions.warehouse_id",Auth::user()->warehouse_id)

                ->where('products.is_active',1)

	    		->orderBy("products.product_name")

	    		->groupBy("product_id")

                   ->get();
        return response(compact('data'), 200);
	}

    public function getProductsForSaleInvoice($action,$id) {
        if($action == "edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id != ".$id." OR transition_sale_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);***/
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code , ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,products.cost_price,products.purchase_price,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id != ".$id." OR product_transitions.transition_sale_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        else if($action == "transfer_edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_transfer_id != ".$id." OR transition_transfer_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,products.purchase_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_transfer_id != ".$id." OR product_transitions.transition_transfer_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        else if($action == "create_approval_invoice") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_approval_id != ".$id." OR transition_approval_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);// Main Warehouse is default for order products ***/
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,products.purchase_price,uom_id,products.cost_price,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (.product_transitions.transition_approval_id != ".$id." OR product_transitions.transition_approval_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');

        } else {
            /*$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count"))
                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')
                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')
                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);  */
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.cost_price,products.selling_price,products.purchase_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }


        $data  = $data->where('products.is_active',1);
        $data  = $data->orderBy("products.product_name")

                ->groupBy("products.id")

                ->get();
        return response(compact('data'), 200);
    }
    public function getProductsForPurchaseInvoice($action,$id) {
        if($action == "edit") {
//            dd('aaaaaaaa');
            /***$data = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id != ".$id." OR transition_sale_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code , ' - ', categories.category_name) as product_name,products.selling_price,products.purchase_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id != ".$id." OR product_transitions.transition_sale_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')
                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

//                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        else if($action == "transfer_edit") {
            /***$data = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_transfer_id != ".$id." OR transition_transfer_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.product_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_transfer_id != ".$id." OR product_transitions.transition_transfer_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        else if($action == "create_approval_invoice") {
            /***$data = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_approval_id != ".$id." OR transition_approval_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);// Main Warehouse is default for order products ***/
            $data = DB::table("products")
                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', categories.category_name) as product_name,products.product_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (.product_transitions.transition_approval_id != ".$id." OR product_transitions.transition_approval_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))
                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')
                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

//                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');

        } else {
            /*$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);  */
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.category_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', categories.category_name) as product_name,products.selling_price,products.purchase_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

//                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')
                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }


        $data  = $data->where('products.is_active',1);
        $data  = $data->orderBy("products.product_name")

            ->groupBy("products.id")

            ->get();
        return response(compact('data'), 200);
    }

    //for new version
    public function filterProductsForSaleInvoice($action,$id,Request $request) {
        if($action == "edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id != ".$id." OR transition_sale_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id != ".$id." OR product_transitions.transition_sale_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        else if($action == "transfer_edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_transfer_id != ".$id." OR transition_transfer_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/

            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_transfer_id != ".$id." OR product_transitions.transition_transfer_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        else if($action == "create_approval_invoice") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_approval_id != ".$id." OR transition_approval_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);// Main Warehouse is default for order products***/
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_approval_id != ".$id." OR product_transitions.transition_approval_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');

        } else {
            /*** $data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                    ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        }
        $data  = $data->where('products.is_active',1);
        if($request->brand_id != '') {
            $data->where('products.brand_id',$request->brand_id);
        }
        if($request->cat_id != '') {
            $data->where('products.category_id',$request->cat_id);
        }
        $data  = $data->orderBy("products.product_name")

                ->groupBy("products.id")

                ->get();
        return response(compact('data'), 200);
    }

	//get Inventory Report
	public function getInventoryReport(Request $request)
	{
		/*$products = DB::table("products")

	    		->select(DB::raw("pt.product_id, products.product_name, products.brand_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name,SUM(CASE  WHEN pt.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_qty, SUM(CASE  WHEN product_transitions.transition_type = 'out' THEN product_transitions.product_quantity  ELSE 0 END)  as out_qty, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND product_transitions.transition_transfer_id IS NOT NULL THEN product_transitions.product_quantity  ELSE 0 END)  as transfer_qty, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id IS NOT NULL OR product_transitions.transition_approval_id IS NOT NULL)  THEN product_transitions.product_quantity  ELSE 0 END)  as sale_qty"))*/

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
                ->select(DB::raw("products.id as product_id, products.product_name, products.brand_id,pt.warehouse_id, products.product_code,pt.add_qty,uom_id,uoms.uom_name,brands.brand_name,categories.category_name, pt.in_qty, pt.receive_qty, pt.out_qty, pt.transfer_qty, pt.sale_qty, pt.branch_id, pt.transition_date, pt.approval_qty, pt.revise_qty, pt.approval_sale_qty, pt.revise_sale_qty"))
                  ->leftjoin(DB::raw("(SELECT product_id, warehouse_id, transition_date, branch_id,
                            SUM(CASE  WHEN transition_type = 'in' AND (transition_entry_id IS NOT NULL OR transition_purchase_id IS NOT NULL OR transition_adjustment_id IS NOT NULL)  THEN product_quantity  ELSE 0 END) as in_qty,
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
//        dd($op_data);
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
		return response(compact('data','op_data','order_data'), 200);
    }
    public function getValuationReport(Request $request){
        $data=$this->getValuation($request);
        // dd($data);
        $total_valuation=0;
        foreach($data as $p){
            $bal=((int)$p->entry_qty+(int)$p->in_qty)-(int)$p->out_qty;
            $p->balance=$bal;

            $p->p_valuation_amount=$p->p_valuation_amount==null ? 0 :(int)$p->p_valuation_amount;
            $p->s_qty=$p->s_qty==null ? 0 :(int)$p->s_qty;
            // $total_valuation+=((int)$p->entry_qty * $p->purchase_price)+(((int)$p->p_valuation_amount+$p->in_cost_price)-(int)$p->cost_price);
            $total_valuation+=((int)$p->entry_qty * $p->purchase_price)+(((int)$p->p_valuation_amount+$p->in_cost_price)-(int)$p->cost_price);
            $p->t_valuation_amount=((int)$p->entry_qty * (int)$p->purchase_price)+(int)(((int)$p->p_valuation_amount+$p->in_cost_price)-(int)$p->cost_price);
            // $p->
            // $total_valuation+=$p->
        }
        // dd($data);
        // dd($total_valuation);
        return compact('data','total_valuation');
    }

	public function exportInventoryReport(Request $request)
    {
        $export = new InventoryExport($request);
        $fileName = 'inventory_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function checkWarehouseUom($product_id)
    {
        $wh_entry = DB::table('product_mainwarehouse_entry')
                            ->select('product_id','uom_id')
                            ->where('product_id',$product_id)
                            ->first();
        $order_entry = DB::table('product_order')
                            ->select('product_id','uom_id')
                            ->where('product_id',$product_id)
                            ->first();
        if($wh_entry || $order_entry) {
            $status = "used";
        } else {
            $status = "success";
        }
        return response(['message' => $status]);
    }

    public function checkSellingUom($product_id,$uom_id)
    {
        $order_entry = DB::table('product_order')
                            ->select('product_id','uom_id')
                            ->where('product_id',$product_id)
                            ->where('uom_id',$uom_id)
                            ->first();

        $sale_entry = DB::table('product_sale')
                            ->select('product_id','uom_id')
                            ->where('product_id',$product_id)
                            ->where('uom_id',$uom_id)
                            ->first();

        $transfer_entry = DB::table('product_transfer')
                            ->select('product_id','uom_id')
                            ->where('product_id',$product_id)
                            ->where('uom_id',$uom_id)
                            ->first();

        if($sale_entry || $order_entry || $transfer_entry) {
            $status = "used";
        } else {
            $status = "success";
        }
        return response(['message' => $status]);
    }

    public function test()
    {
            $order = DB::table('order_approvals')
                                ->where('order_id',922222)->get();
            //if(count($order) > 0) { dd('Do not delte');} else { dd('can delete'); }
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
        }
       $orders = DB::table('orders')
                                ->where('created_by',Auth::user()->id)
                                ->pluck('id')->toArray();
        $data = Sale::with('products')->whereIn('order_id',$orders)->get();

    	//echo Hash::make('vanillaM!22#');
		// Turn on output buffering
   ob_start();
   //Get the ipconfig details using system commond
   system('ipconfig /all');
   // Capture the output into a variable
   $mycomsys=ob_get_contents();
   // Clean (erase) the output buffer
   ob_clean();
   $find_mac = "Physical"; //find the "Physical" & Find the position of Physical text
   $pmac = strpos($mycomsys, $find_mac);
   // Get Physical Address
   $macaddress=substr($mycomsys,($pmac+36),17);
   //Display Mac Address
   echo $macaddress;
    	 $app_total_amount = DB::table('order_approval_product')->where('approval_id', 2)					->sum('total_amount');
    	//dd($app_total_amount);
    	$uom_relation = DB::table('product_selling_uom')
    			   			->select('relation')
    			   			->where('product_id',1)
    			   			->where('uom_id',5)
    			   			->first();
    		if($uom_relation) {
    			$relation_val = $uom_relation->relation;
    		} else {
    			//for pre-defined product uom
    			$relation_val = 1;
    		}

    	$data = DB::table("product_transitions")

	    		->select(DB::raw("products.product_name,uom_id,uoms.uom_name, SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count, SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty"))

	    		->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

	    		->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

	    		->where("product_transitions.product_id",6)

	    		->orderBy("product_id")

	    		->groupBy("product_id")

	   			->get();
	    /*$data = ProductTransition::with('products','products.selling_uoms')->get();
	    $data_uom = array();
	    foreach($data as $obj) {
	    	foreach($obj->products as $product) {
	    		array_push($data_uom, $product->selling_uoms);
	    	}
	    }*/

	    /*$data_uom = array();
	    foreach($data as $obj) {
	    	foreach($obj->products as $product) {
	    		array_push($data_uom, $product->selling_uoms);
	    	}
	    }*/
	    $chk_order = DB::table("sales")

                        ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as previous_balance"))
                        ->where('customer_id','=',1)
                        ->groupBy('customer_id')
                        ->first();
	    // dd($chk_order->previous_balance);
    }
}
