<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use stdClass;
use App\Sale;
use APp\User;
use App\Product;
use App\Transfer;
use Carbon\Carbon;
use App\Collection;
use App\SubAccount;
use App\ProductTransition;
use Illuminate\Http\Request;
use App\Exports\InventoryExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\Report\GetReport;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Exports\MinMaxExport;
use App\Exports\StockLedgerExport;
use App\Exports\ReorderLevelExport;

class ProductTransitionController extends Controller
{
    use GetReport;
    public function getProductsByUserWarehouse()
    {
        $data = DB::table("product_transitions")
            ->select(DB::raw("product_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.brand_id, products.category_id,products.cost_price ,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count"))
            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')
            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id", Auth::user()->warehouse_id)

            ->where('products.is_active', 1)

            ->orderBy("products.product_name")

            ->groupBy("product_id")

            ->get();
        return response(compact('data'), 200);
    }

    public function getProductsForSaleInvoice($action, $id)
    {
        if ($action == "edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id != ".$id." OR transition_sale_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code , ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,products.cost_price,products.purchase_price,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id != " . $id . " OR product_transitions.transition_sale_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        } else if ($action == "transfer_edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_transfer_id != ".$id." OR transition_transfer_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,products.purchase_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_transfer_id != " . $id . " OR product_transitions.transition_transfer_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        } else if ($action == "create_approval_invoice") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_approval_id != ".$id." OR transition_approval_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);// Main Warehouse is default for order products ***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,products.purchase_price,uom_id,products.cost_price,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (.product_transitions.transition_approval_id != " . $id . " OR product_transitions.transition_approval_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

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

        $data = $data->where("product_transitions.warehouse_id", Auth::user()->warehouse_id);

        $data  = $data->where('products.is_active', 1);
        $data  = $data->orderBy("products.product_name")

            ->groupBy("products.id")

            ->get();
        return response(compact('data'), 200);
    }
    public function getProductsForPurchaseInvoice($action, $id)
    {
        if ($action == "edit") {
            //            dd('aaaaaaaa');
            /***$data = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id != ".$id." OR transition_sale_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code , ' - ', categories.category_name) as product_name,products.selling_price,products.purchase_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id != " . $id . " OR product_transitions.transition_sale_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')
                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

                //                    ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        } else if ($action == "transfer_edit") {
            /***$data = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_transfer_id != ".$id." OR transition_transfer_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.product_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_transfer_id != " . $id . " OR product_transitions.transition_transfer_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        } else if ($action == "create_approval_invoice") {
            /***$data = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_approval_id != ".$id." OR transition_approval_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);// Main Warehouse is default for order products ***/
            $data = DB::table("products")
                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', categories.category_name) as product_name,products.product_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (.product_transitions.transition_approval_id != " . $id . " OR product_transitions.transition_approval_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))
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


        $data  = $data->where('products.is_active', 1);
        $data  = $data->orderBy("products.product_name")

            ->groupBy("products.id")

            ->get();
        return response(compact('data'), 200);
    }

    //for new version
    public function filterProductsForSaleInvoice($action, $id, Request $request)
    {
        if ($action == "edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id != ".$id." OR transition_sale_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_sale_id != " . $id . " OR product_transitions.transition_sale_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        } else if ($action == "transfer_edit") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_transfer_id != ".$id." OR transition_transfer_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id); ***/

            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_transfer_id != " . $id . " OR product_transitions.transition_transfer_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

                ->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');
        } else if ($action == "create_approval_invoice") {
            /***$data = DB::table("product_transitions")

                    ->select(DB::raw("product_id, products.product_name,products.product_price,products.retail1_price,products.retail2_price,products.wholesale_price,uom_id,uoms.uom_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' AND (transition_approval_id != ".$id." OR transition_approval_id IS NULL) THEN product_quantity  ELSE 0 END)  as out_count"))

                    ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                    ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                    ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id);// Main Warehouse is default for order products***/
            $data = DB::table("products")

                ->select(DB::raw("products.id as product_id, products.brand_id, products.category_id, CONCAT(products.product_name, ' - ', products.product_code, ' - ', brands.brand_name) as product_name,products.selling_price,uom_id,uoms.uom_name,SUM(CASE  WHEN product_transitions.transition_type = 'in' THEN product_transitions.product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN product_transitions.transition_type = 'out' AND (product_transitions.transition_approval_id != " . $id . " OR product_transitions.transition_approval_id IS NULL) THEN product_transitions.product_quantity  ELSE 0 END)  as out_count"))

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
        $data  = $data->where('products.is_active', 1);
        if ($request->brand_id != '') {
            $data->where('products.brand_id', $request->brand_id);
        }
        if ($request->cat_id != '') {
            $data->where('products.category_id', $request->cat_id);
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

        $route_name = Route::currentRouteName();

        $where = "";
        if ($request->from_date != '' && $request->to_date != '') {
            //$products->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
            $where = "product_transitions.transition_date >= '" . $request->from_date . "' AND product_transitions.transition_date <= '" . $request->to_date . "'";
        } else if ($request->from_date != '') {
            //$products->whereDate('product_transitions.transition_date', '>=', $request->from_date);
            $where = "product_transitions.transition_date >= '" . $request->from_date . "'";
        } else if ($request->to_date != '') {
            //$products->whereDate('product_transitions.transition_date', '<=', $request->to_date);
            $where = "product_transitions.transition_date <= '" . $request->to_date . "'";
        } else {
        }

        if ($request->warehouse_id != "") {
            $where .= " AND warehouse_id =" . $request->warehouse_id;
        }

        if ($request->branch_id != "") {
            $where .= " AND branch_id =" . $request->branch_id;
        }

        $products = DB::table("products")
            ->select(DB::raw("products.id as product_id, products.product_name, products.brand_id,pt.warehouse_id, products.product_code,pt.add_qty,uom_id,uoms.uom_name,brands.brand_name,categories.category_name, pt.in_qty, pt.receive_qty, IFNULL(pt.out_qty,0) as out_qty, pt.transfer_qty, pt.sale_qty, pt.branch_id, pt.transition_date, pt.approval_qty, pt.revise_qty, pt.approval_sale_qty, pt.revise_sale_qty,pp.photo_ids"))
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
                            FROM product_transitions Where " . $where . "
                            GROUP BY product_transitions.product_id

                            ) as pt"), function ($join) {

                $join->on("pt.product_id", "=", "products.id");
            })

            ->leftjoin(DB::raw("(SELECT product_photos.product_id as photo_product_id, GROUP_CONCAT(product_photos.id) as photo_ids
                            FROM product_photos 
                            GROUP BY product_photos.product_id

                            ) as pp"), function ($join) {

                $join->on("pp.photo_product_id", "=", "products.id");
            })

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

            ->leftjoin('categories', 'categories.id', '=', 'products.category_id');

        if ($request->product_name != "") {
            $products->where('products.product_name', 'LIKE', "%$request->product_name%");
        }

        if ($request->brand_id != "") {
            $products->where('products.brand_id', $request->brand_id);
        }
        // Kamlesh Start
        if ($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }


        if ($request->sort_by != "") {
            if ($request->sort_by == "name") {
                $data  =  $products->orderBy('product_name', $order)->get();
            } else if ($request->sort_by == "code") {
                $data  =  $products->orderBy('product_code', $order)->get();
            } else if ($request->sort_by == "brand") {
                $data  =  $products->orderBy('brands.brand_name', $order)->get();
            } else if ($request->sort_by == "category") {
                $data  =  $products->orderBy('categories.category_name', $order)->get();
            } else if ($request->sort_by == "uom") {
                $data  =  $products->orderBy('uoms.uom_name', $order)->get();
            } else {
            }
        } else {
            $data  = $products->orderBy("product_name")->get();
        }
        // kamlesh End

        /*if($request->warehouse_id != "") {
            $products->where('pt.warehouse_id', $request->warehouse_id);
        }*/

        // $data  = $products->orderBy("product_name")->get();
        $op_products = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name, products.brand_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_qty, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_qty, SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

            ->leftjoin('categories', 'categories.id', '=', 'products.category_id');

        $op_products->whereDate('transition_date', '<', $request->from_date);

        if ($request->warehouse_id != "") {
            $op_products->where('product_transitions.warehouse_id', $request->warehouse_id);
        }

        if ($request->product_name != "") {
            $op_products->where('products.product_name', 'LIKE', "%$request->product_name%");
        }

        if ($request->brand_id != "") {
            $op_products->where('products.brand_id', $request->brand_id);
        }

        $op_data  = $op_products->orderBy("product_name")->groupBy("product_id")->get();
        //        dd($op_data);
        //end for opening qty

        //Start for order product
        $order_products = DB::table("orders")

            ->select(DB::raw("orders.order_date, products.product_name, products.id as product_id, SUM(po.product_quantity) as order_qty"))

            ->leftjoin(DB::raw("(SELECT product_order.product_id, product_order.order_id, (CASE  WHEN suom.relation IS NULL THEN product_quantity  ELSE product_quantity * suom.relation END)  as product_quantity
                            FROM product_order LEFT JOIN product_selling_uom as suom ON suom.product_id = product_order.product_id AND suom.uom_id = product_order.uom_id
                            ) as po"), function ($join) {
                $join->on("po.order_id", "=", "orders.id");
            })

            //->leftjoin('product_order', 'product_order.order_id', '=', 'orders.id')

            ->leftjoin('products', 'products.id', '=', 'po.product_id')

            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id');


        if ($request->from_date != '' && $request->to_date != '') {
            $order_products->whereBetween('orders.order_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $order_products->whereDate('orders.order_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $order_products->whereDate('orders.order_date', '<=', $request->to_date);
        } else {
        }

        if ($request->branch_id != "") {
            $order_products->where('orders.branch_id', $request->branch_id);
        }

        if ($request->warehouse_id != "") {
            $order_products->where('orders.warehouse_id', $request->warehouse_id);
        } /*
        /*if($request->to_date != '') {
            $order_products->whereDate('orders.order_date', '<=', $request->to_date);
        }else {
           $today = Carbon::now()->format('Y-m-d');
           $order_products->whereDate('orders.order_date', '<=', $today);
        }  */

        if ($request->product_name != "") {
            $order_products->where('products.product_name', 'LIKE', "%$request->product_name%");
        }

        if ($request->brand_id != "") {
            $order_products->where('products.brand_id', $request->brand_id);
        }

        $order_data  = $order_products->orderBy("products.product_name")->groupBy("po.product_id")->get();
        //end for order prouct
        // Kamlesh Start
        if ($route_name == 'inventory_export_pdf') {
            $pdf = PDF::loadView('exports.inventoryRptPdf', compact('data', 'op_data', 'order_data'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->output();
        }
        // Kamlesh End
        return response(compact('data', 'op_data', 'order_data'), 200);
    }

    public function NEWgetStockLedger(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);
        $route_name = Route::currentRouteName();
        $start = strtotime($request->from_date); 
        $now = Carbon::now()->format('Y-m-d');
        $end = !empty($request->to_date) ? strtotime($request->to_date) : strtotime($now);
        $range = array();

        $date = strtotime("-1 day", $start);  
        while($date < $end)  { 
           $date = strtotime("+1 day", $date);
           $date_arr[] = date('Y-m-d', $date);
        }


        foreach($date_arr as $key => $d) {
             $op_products = DB::table("product_transitions")

                ->select(DB::raw("product_id, products.product_name, products.brand_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_qty, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_qty"))

                ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('categories', 'categories.id', '=', 'products.category_id');

            $op_products->whereDate('transition_date', '<', $d);

            if ($request->warehouse_id != "") {
                $op_products->where('product_transitions.warehouse_id', $request->warehouse_id);
            }

            if ($request->branch_id != "") {
                $op_products->where('product_transitions.branch_id', $request->branch_id);
            }

            if ($request->product_name != "") {
                $product_arr = explode(',', $request->product_name);
                $op_products->whereIn('product_transitions.product_id', $product_arr);
            }

            if ($request->brand_id != "") {
                $op_products->where('products.brand_id', $request->brand_id);
            }
            $op_products  = $op_products->orderBy("products.product_code")->groupBy("product_id")->get();
            
            foreach($op_products as $op) {
                $bal = $op->in_qty - $op->out_qty;

                $data = DB::table("product_transitions")

                    ->select(DB::raw("sales.reference_no as sale_reference_no, purchase_invoices.reference_no as purchase_reference_no, inventory_adjustment.reference_no as adjustment_reference_no, mainwarehouse_entries.reference_no as entry_reference_no, sc.cus_name as sale_customer_name, rc.cus_name as return_customer_name, suppliers.name as supplier_name, sale_returns.return_no as return_invoice_no, (CASE WHEN sales.invoice_no IS NOT NULL THEN sales.invoice_no WHEN purchase_invoices.invoice_no IS NOT NULL THEN purchase_invoices.invoice_no WHEN mainwarehouse_entries.entry_no IS NOT NULL THEN mainwarehouse_entries.entry_no WHEN transfers.transfer_no IS NOT NULL THEN transfers.transfer_no WHEN inventory_adjustment.invoice_no IS NOT NULL THEN inventory_adjustment.invoice_no WHEN sale_returns.return_no IS NOT NULL THEN sale_returns.return_no ELSE '' END) as invoice_no, product_transitions.transition_date, (CASE WHEN product_transitions.transition_type = 'OUT' AND transition_adjustment_id IS NOT NULL THEN product_transitions.product_quantity * -1 ELSE product_transitions.product_quantity END) as product_quantity, product_transitions.transition_sale_id, product_transitions.transition_purchase_id, product_transitions.transition_entry_id, product_transitions.transition_adjustment_id, product_transitions.transition_transfer_id, product_transitions.transition_return_id, product_transitions.transition_type, product_transitions.product_id, products.product_code, products.product_name, brands.brand_name"))

                    ->join('products','products.id','=', 'product_transitions.product_id')

                    ->leftjoin('brands','brands.id','=', 'products.brand_id')

                    ->leftjoin('sales', 'sales.id', '=', 'product_transitions.transition_sale_id')

                    ->leftjoin('customers as sc', 'sc.id', '=', 'sales.customer_id')

                    ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'product_transitions.transition_purchase_id')

                    ->leftjoin('suppliers', 'suppliers.id', '=', 'purchase_invoices.supplier_id')

                    ->leftjoin('mainwarehouse_entries', 'mainwarehouse_entries.id', '=', 'product_transitions.transition_entry_id')

                    ->leftjoin('transfers', 'transfers.id', '=', 'product_transitions.transition_transfer_id')

                    ->leftjoin('inventory_adjustment', 'inventory_adjustment.id', '=', 'product_transitions.transition_adjustment_id')

                    ->leftjoin('sale_returns', 'sale_returns.id', '=', 'product_transitions.transition_return_id')

                    ->leftjoin('customers as rc', 'rc.id', '=', 'sale_returns.customer_id');

        // if ($request->from_date != '' && $request->to_date != '') {
        //     $data->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
        // }
            if ($request->warehouse_id != "") {
                $data->where('product_transitions.warehouse_id', $request->warehouse_id);
            }

            if ($request->branch_id != "") {
                $data->where('product_transitions.branch_id', $request->branch_id);
            }

            $data->whereDate('product_transitions.transition_date', '=', $d);
            $data  = $data->where("product_transitions.product_id",$op->product_id)->orderBy('product_transitions.id', 'ASC')->get();

                if(count($data) > 0) {

                    $closing = 0;
                    $entry = 0; $sale_return =0;  $purchase = 0;  $receive = 0;
                    $sale = 0;  $adjustment =0;   $transfer=0;

                    foreach($data as $k=>$p) {
                       if(!empty($p->transition_entry_id)) {
                            $entry += $p->product_quantity;
                       }
                       else if(!empty($p->transition_return_id)) {
                            $sale_return += $p->product_quantity;
                       }
                       else if(!empty($p->transition_purchase_id)) {
                            $purchase += $p->product_quantity;
                       }
                       else if(!empty($p->transition_transfer_id) && $p->transition_type == 'out') {
                            $transfer += $p->product_quantity;
                       }
                       else if(!empty($p->transition_transfer_id) && $p->transition_type == 'in') {
                            $receive += $p->product_quantity;
                       }
                       else if(!empty($p->transition_sale_id) && empty($p->transition_return_id)) {
                            $sale += $p->product_quantity;
                       }
                       else if(!empty($p->transition_adjustment_id)) {
                            $adjustment += $p->product_quantity;
                       } else {}
                    }

                    $products[$key][$op->product_id]=new \stdClass();
                    $products[$key][$op->product_id]->date = $d;
                    $products[$key][$op->product_id]->opening = $op;
                    $products[$key][$op->product_id]->transitions = $data;
                    $products[$key][$op->product_id]->closing     = ($bal + $entry + $purchase + $sale_return + $receive + $adjustment)-($sale + $transfer);

                } else {
                    if($bal != 0) {
                        $products[$key][$op->product_id]=new \stdClass();
                        $products[$key][$op->product_id]->date = $d;
                        $products[$key][$op->product_id]->opening = $op;
                        $products[$key][$op->product_id]->transitions = [];
                        $products[$key][$op->product_id]->closing = $bal;

                    }
                }
            }

            
        }

        return compact('products');
    }

    public function getStockLedger(Request $request)
    {
        $route_name = Route::currentRouteName();

        $data = DB::table("product_transitions")

                    ->select(DB::raw("sales.reference_no as sale_reference_no, purchase_invoices.reference_no as purchase_reference_no, inventory_adjustment.reference_no as adjustment_reference_no, mainwarehouse_entries.reference_no as entry_reference_no, sc.cus_name as sale_customer_name, rc.cus_name as return_customer_name, suppliers.name as supplier_name, sale_returns.return_no as return_invoice_no, (CASE WHEN sales.invoice_no IS NOT NULL THEN sales.invoice_no WHEN purchase_invoices.invoice_no IS NOT NULL THEN purchase_invoices.invoice_no WHEN mainwarehouse_entries.entry_no IS NOT NULL THEN mainwarehouse_entries.entry_no WHEN transfers.transfer_no IS NOT NULL THEN transfers.transfer_no WHEN inventory_adjustment.invoice_no IS NOT NULL THEN inventory_adjustment.invoice_no WHEN sale_returns.return_no IS NOT NULL THEN sale_returns.return_no ELSE '' END) as invoice_no, product_transitions.transition_date, (CASE WHEN product_transitions.transition_type = 'OUT' AND transition_adjustment_id IS NOT NULL THEN product_transitions.product_quantity * -1 ELSE product_transitions.product_quantity END) as product_quantity, product_transitions.transition_sale_id, product_transitions.transition_purchase_id, product_transitions.transition_entry_id, product_transitions.transition_adjustment_id, product_transitions.transition_transfer_id, product_transitions.transition_return_id, product_transitions.transition_type, product_transitions.product_id, products.product_code, products.product_name, brands.brand_name"))

                    ->leftjoin('products','products.id','=', 'product_transitions.product_id')

                    ->leftjoin('brands','brands.id','=', 'products.brand_id')

                    ->leftjoin('sales', 'sales.id', '=', 'product_transitions.transition_sale_id')

                    ->leftjoin('customers as sc', 'sc.id', '=', 'sales.customer_id')

                    ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'product_transitions.transition_purchase_id')

                    ->leftjoin('suppliers', 'suppliers.id', '=', 'purchase_invoices.supplier_id')

                    ->leftjoin('mainwarehouse_entries', 'mainwarehouse_entries.id', '=', 'product_transitions.transition_entry_id')

                    ->leftjoin('transfers', 'transfers.id', '=', 'product_transitions.transition_transfer_id')

                    ->leftjoin('inventory_adjustment', 'inventory_adjustment.id', '=', 'product_transitions.transition_adjustment_id')

                    ->leftjoin('sale_returns', 'sale_returns.id', '=', 'product_transitions.transition_return_id')

                    ->leftjoin('customers as rc', 'rc.id', '=', 'sale_returns.customer_id');

        if ($request->from_date != '' && $request->to_date != '') {
            $data->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $data->whereDate('product_transitions.transition_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $data->whereDate('product_transitions.transition_date', '<=', $request->to_date);
        } else {
        }

        if ($request->warehouse_id != "") {
            $data->where('product_transitions.warehouse_id', $request->warehouse_id);
        }

        if ($request->branch_id != "") {
            $data->where('product_transitions.branch_id', $request->branch_id);
        }

        if ($request->product_name != "") {
            $product_arr = explode(',', $request->product_name);
            $data->whereIn('product_transitions.product_id', $product_arr);
        }

        if ($request->brand_id != "") {
            $data->where('products.brand_id', $request->brand_id);
        }

        if ($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }


        /*if ($request->sort_by != "") {
            if ($request->sort_by == "name") {
                $data  =  $data->orderBy("product_transitions.transition_date")->orderBy('products.product_code', $order)->orderBy('product_transitions.id', 'ASC')->get();
            } else if ($request->sort_by == "code") {
                $data  =  $data->orderBy("product_transitions.transition_date")->orderBy('products.product_code', $order)->orderBy('product_transitions.id', 'ASC')->get();
            } else if ($request->sort_by == "brand") {
                $data  =  $data->orderBy("product_transitions.transition_date")->orderBy('brands.brand_name', $order)->orderBy('products.product_code', $order)->orderBy('product_transitions.id', 'ASC')->get();
            } else if ($request->sort_by == "category") {
            } else if ($request->sort_by == "uom") {
            } else {
            }
        } else {
            $data  = $data->orderBy("product_transitions.transition_date")->orderBy("products.product_code")->orderBy('product_transitions.id', 'ASC')->get();
        }*/

        $data  = $data->orderBy("products.product_code")->orderBy("product_transitions.transition_date")->orderBy('product_transitions.id', 'ASC')->get();
        //dd($data);
        //start opening
         $op_products = DB::table("product_transitions")

            ->select(DB::raw("product_id, products.product_name, products.brand_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_qty, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_qty, SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

            ->leftjoin('categories', 'categories.id', '=', 'products.category_id');

        $op_products->whereDate('transition_date', '<', $request->from_date);

        if ($request->warehouse_id != "") {
            $op_products->where('product_transitions.warehouse_id', $request->warehouse_id);
        }
            
        if ($request->branch_id != "") {
            $op_products->where('product_transitions.branch_id', $request->branch_id);
        }

        if ($request->product_name != "") {
            $product_arr = explode(',', $request->product_name);
            $op_products->whereIn('product_transitions.product_id', $product_arr);
        }

        if ($request->brand_id != "") {
            $op_products->where('products.brand_id', $request->brand_id);
        }

        $op_data  = $op_products->orderBy("product_name")->groupBy("product_id")->get();
        //end opening

        if ($route_name == 'stock_ledger_export') {
            $export = new StockLedgerExport($data, $op_data, $request);
            $fileName = 'Stock Ledger Export' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }

        return compact('data','op_data');
    }


    public function getValuationReport(Request $request)
    {
        $route_name = Route::currentRouteName();
        $data = $this->getValuation($request);
        // dd($data);
        $total_valuation = 0;
        $total_adj_in = 0;
        $total_adj_out = 0;
        $total_after_valuation = 0;
        foreach ($data as $p) {
            $bal = ((int)$p->entry_qty + (int)$p->in_qty) - (int)$p->out_qty;
            $p->balance = $bal;

            $p->p_valuation_amount = $p->p_valuation_amount == null ? 0 : (int)$p->p_valuation_amount;
            $p->s_qty = $p->s_qty == null ? 0 : (int)$p->s_qty;
            // $total_valuation+=((int)$p->entry_qty * $p->purchase_price)+(((int)$p->p_valuation_amount+$p->in_cost_price)-(int)$p->cost_price);
            $total_valuation += ((int)$p->entry_qty * $p->purchase_price) + (((int)$p->p_valuation_amount + $p->in_cost_price) - (int)$p->cost_price);
            $p->t_valuation_amount = ((int)$p->entry_qty * (int)$p->purchase_price) + (int)(((int)$p->p_valuation_amount + $p->in_cost_price) - (int)$p->cost_price);

            $total_adj_in += $p->adj_in_cost_price;
            $total_adj_out += $p->adj_out_cost_price;

            $total_after_valuation += (($p->t_valuation_amount + $p->adj_out_cost_price) - $p->adj_in_cost_price);

            // $p->
            // $total_valuation+=$p->
        }
        // dd($data);
        // dd($total_valuation);
        // Kamlesh Start
        if ($route_name == 'get_valuation_export_pdf') {
            $pdf = PDF::loadView('exports.inventory_valuation_pdf', compact('data', 'total_valuation','total_adj_in','total_adj_out','total_after_valuation'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->output();
        }
        // Kamlesh End

        return compact('data', 'total_valuation', 'total_adj_in','total_adj_out','total_after_valuation');
    }

    public function exportInventoryReport(Request $request)
    {
        $export = new InventoryExport($request);
        $fileName = 'inventory_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $fileName);
    }
    // public function exportInventoryReportPdf(Request $request)
    // {
    //     $export = new InventoryExport($request);

    //     $pdf = PDF::loadView('exports.inventoryRptPdf', compact('export'));
    //     $pdf->setPaper('a4', 'portrait');

    //     return $pdf->output();
    // }

    public function checkWarehouseUom($product_id)
    {
        $wh_entry = DB::table('product_mainwarehouse_entry')
            ->select('product_id', 'uom_id')
            ->where('product_id', $product_id)
            ->first();
        $order_entry = DB::table('product_order')
            ->select('product_id', 'uom_id')
            ->where('product_id', $product_id)
            ->first();
        if ($wh_entry || $order_entry) {
            $status = "used";
        } else {
            $status = "success";
        }
        return response(['message' => $status]);
    }

    public function checkSellingUom($product_id, $uom_id)
    {
        $order_entry = DB::table('product_order')
            ->select('product_id', 'uom_id')
            ->where('product_id', $product_id)
            ->where('uom_id', $uom_id)
            ->first();

        $sale_entry = DB::table('product_sale')
            ->select('product_id', 'uom_id')
            ->where('product_id', $product_id)
            ->where('uom_id', $uom_id)
            ->first();

        $transfer_entry = DB::table('product_transfer')
            ->select('product_id', 'uom_id')
            ->where('product_id', $product_id)
            ->where('uom_id', $uom_id)
            ->first();

        if ($sale_entry || $order_entry || $transfer_entry) {
            $status = "used";
        } else {
            $status = "success";
        }
        return response(['message' => $status]);
    }

    public function test()
    {
        $date_arr = array('2023-01-01','2023-01-02');

        foreach($date_arr as $key => $d) {
             $op_products = DB::table("product_transitions")

                ->select(DB::raw("product_id, products.product_name, products.brand_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name,SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_qty, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_qty"))

                ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('categories', 'categories.id', '=', 'products.category_id');

            $op_products->whereDate('transition_date', '<', $d);
            //Kamlesh End
            $op_products  = $op_products->orderBy("products.product_code")->groupBy("product_id")->get();
            foreach($op_products as $op) {
                $bal = $op->in_qty - $op->out_qty;

                $data = DB::table("product_transitions")

                    ->select(DB::raw("sales.reference_no as sale_reference_no, purchase_invoices.reference_no as purchase_reference_no, inventory_adjustment.reference_no as adjustment_reference_no, mainwarehouse_entries.reference_no as entry_reference_no, sc.cus_name as sale_customer_name, rc.cus_name as return_customer_name, suppliers.name as supplier_name, sale_returns.return_no as return_invoice_no, (CASE WHEN sales.invoice_no IS NOT NULL THEN sales.invoice_no WHEN purchase_invoices.invoice_no IS NOT NULL THEN purchase_invoices.invoice_no WHEN mainwarehouse_entries.entry_no IS NOT NULL THEN mainwarehouse_entries.entry_no WHEN transfers.transfer_no IS NOT NULL THEN transfers.transfer_no WHEN inventory_adjustment.invoice_no IS NOT NULL THEN inventory_adjustment.invoice_no WHEN sale_returns.return_no IS NOT NULL THEN sale_returns.return_no ELSE '' END) as invoice_no, product_transitions.transition_date, (CASE WHEN product_transitions.transition_type = 'OUT' AND transition_adjustment_id IS NOT NULL THEN product_transitions.product_quantity * -1 ELSE product_transitions.product_quantity END) as product_quantity, product_transitions.transition_sale_id, product_transitions.transition_purchase_id, product_transitions.transition_entry_id, product_transitions.transition_adjustment_id, product_transitions.transition_transfer_id, product_transitions.transition_return_id, product_transitions.transition_type, product_transitions.product_id, products.product_code, products.product_name, brands.brand_name"))

                    ->join('products','products.id','=', 'product_transitions.product_id')

                    ->leftjoin('brands','brands.id','=', 'products.brand_id')

                    ->leftjoin('sales', 'sales.id', '=', 'product_transitions.transition_sale_id')

                    ->leftjoin('customers as sc', 'sc.id', '=', 'sales.customer_id')

                    ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'product_transitions.transition_purchase_id')

                    ->leftjoin('suppliers', 'suppliers.id', '=', 'purchase_invoices.supplier_id')

                    ->leftjoin('mainwarehouse_entries', 'mainwarehouse_entries.id', '=', 'product_transitions.transition_entry_id')

                    ->leftjoin('transfers', 'transfers.id', '=', 'product_transitions.transition_transfer_id')

                    ->leftjoin('inventory_adjustment', 'inventory_adjustment.id', '=', 'product_transitions.transition_adjustment_id')

                    ->leftjoin('sale_returns', 'sale_returns.id', '=', 'product_transitions.transition_return_id')

                    ->leftjoin('customers as rc', 'rc.id', '=', 'sale_returns.customer_id');

        // if ($request->from_date != '' && $request->to_date != '') {
        //     $data->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
        // }
            $data->whereDate('product_transitions.transition_date', '=', $d);
            $data  = $data->where("product_transitions.product_id",$op->product_id)->orderBy('product_transitions.id', 'ASC')->get();

                if(count($data) > 1) {
                $products[$key][$op->product_id]=new \stdClass();
                $products[$key][$op->product_id]->date = $d;
                $products[$key][$op->product_id]->opening = $op;
                $products[$key][$op->product_id]->transitions = $data;

                } else {
                    if($bal != 0) {
                        $products[$key][$op->product_id]=new \stdClass();
                        $products[$key][$op->product_id]->date = $d;
                        $products[$key][$op->product_id]->opening = $op;
                        $products[$key][$op->product_id]->transitions = [];

                    }
                }
            }

            
        }

        dd($products); dd('exit');
        $data = DB::table("product_transitions")

                    ->select(DB::raw("sales.reference_no as sale_reference_no, purchase_invoices.reference_no as purchase_reference_no, inventory_adjustment.reference_no as adjustment_reference_no, mainwarehouse_entries.reference_no as entry_reference_no, sc.cus_name as sale_customer_name, rc.cus_name as return_customer_name, suppliers.name as supplier_name, sale_returns.return_no as return_invoice_no, (CASE WHEN sales.invoice_no IS NOT NULL THEN sales.invoice_no WHEN purchase_invoices.invoice_no IS NOT NULL THEN purchase_invoices.invoice_no WHEN mainwarehouse_entries.entry_no IS NOT NULL THEN mainwarehouse_entries.entry_no WHEN transfers.transfer_no IS NOT NULL THEN transfers.transfer_no WHEN inventory_adjustment.invoice_no IS NOT NULL THEN inventory_adjustment.invoice_no WHEN sale_returns.return_no IS NOT NULL THEN sale_returns.return_no ELSE '' END) as invoice_no, product_transitions.transition_date, (CASE WHEN product_transitions.transition_type = 'OUT' AND transition_adjustment_id IS NOT NULL THEN product_transitions.product_quantity * -1 ELSE product_transitions.product_quantity END) as product_quantity, product_transitions.transition_sale_id, product_transitions.transition_purchase_id, product_transitions.transition_entry_id, product_transitions.transition_adjustment_id, product_transitions.transition_transfer_id, product_transitions.transition_return_id, product_transitions.transition_type, product_transitions.product_id, products.product_code, products.product_name, brands.brand_name"))

                    ->join('products','products.id','=', 'product_transitions.product_id')

                    ->leftjoin('brands','brands.id','=', 'products.brand_id')

                    ->leftjoin('sales', 'sales.id', '=', 'product_transitions.transition_sale_id')

                    ->leftjoin('customers as sc', 'sc.id', '=', 'sales.customer_id')

                    ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'product_transitions.transition_purchase_id')

                    ->leftjoin('suppliers', 'suppliers.id', '=', 'purchase_invoices.supplier_id')

                    ->leftjoin('mainwarehouse_entries', 'mainwarehouse_entries.id', '=', 'product_transitions.transition_entry_id')

                    ->leftjoin('transfers', 'transfers.id', '=', 'product_transitions.transition_transfer_id')

                    ->leftjoin('inventory_adjustment', 'inventory_adjustment.id', '=', 'product_transitions.transition_adjustment_id')

                    ->leftjoin('sale_returns', 'sale_returns.id', '=', 'product_transitions.transition_return_id')

                    ->leftjoin('customers as rc', 'rc.id', '=', 'sale_returns.customer_id');

        // if ($request->from_date != '' && $request->to_date != '') {
        //     $data->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
        // }
            $data->whereDate('product_transitions.transition_date', '=', '2023-01-01');
            $data  = $data->orderBy("product_transitions.transition_date")->orderBy("products.product_code")->orderBy('product_transitions.id', 'ASC')->get();
            dd($data);
       

        $start = strtotime('2023-12-01'); 
$end = strtotime('2024-03-01'); 
$range = array();

$date = strtotime("-1 day", $start);  
while($date < $end)  { 
   $date = strtotime("+1 day", $date);
   $range[] = date('Y-m-d', $date);
} print_r($range); 
    dd('exit');
        /*$r = DB::table('receipts')->get();
        $c=0;
        foreach($r as $v) {
            $c++;
            $ag = SubAccount::find($v->debit_id);
            $ag_id = $ag->account_group_id;
            DB::table('account_transitions')
                ->where('receipt_id', $v->id)
                ->update(array('account_group_id' => $ag_id,'cash_bank_sub_account_id' => $v->debit_id));
        }
        echo $c."<br />";
       $p = DB::table('payments')->get();
        $c=0;
        foreach($p as $v) {
            $c++;
            $ag = SubAccount::find($v->credit_id);
            $ag_id = $ag->account_group_id;
            DB::table('account_transitions')
                ->where('payment_id', $v->id)
                ->update(array('account_group_id' => $ag_id,'cash_bank_sub_account_id' => $v->credit_id));
        }
        dd($c);exit();*/
        //dd('test');exit();
        $data = DB::table("product_sale")

            ->select(DB::raw("product_sale.*, sales.invoice_no,products.product_code,products.product_name"))

            ->leftjoin('products', 'products.id', '=', 'product_sale.product_id')

            ->leftjoin('sales', 'sales.id', '=', 'product_sale.sale_id')

            ->where("sales.created_at", '>', '2022-11-30')

            ->get();
        foreach($data as $s) {
            $p = DB::table("product_transitions")->where('transition_product_pivot_id', $s->id)->get();
            if(count($p) == 0) {
                echo 'invoice no = ' . $s->sale_id.', product_code = '.$s->product_code.', product_name='.$s->product_name.'<br />';
            }
        }
        dd('success');

        $data = DB::table("product_transitions")
            ->select(DB::raw("product_transitions.*"))
            ->where('transition_type', 'out')
            ->where('transition_transfer_id', '>=', 157)
            ->where('transition_transfer_id', '<=', 177)
            ->get();
        foreach ($data as $d) {
            DB::table('product_transitions')
                ->where('transition_type', 'in')
                ->where('transition_product_pivot_id', $d->transition_product_pivot_id)
                ->where('product_id', $d->product_id)
                ->where('transition_transfer_id', $d->transition_transfer_id)
                ->update(array('cost_price' => $d->cost_price));
        }
        dd($data);
        exit();
        $str = 'In My Cart : 000 12 items';
        $str = preg_replace('/[^0-9.]+/', '', $str);
        echo $str + 1;
        exit();
        $products = Product::all();
        foreach ($products as $p) {
            $cost_price = $this->getCostPrice($p->id)->product_cost_price;
            // dd($cost_price);
            $store_cost_price = Product::find($p->id);
            if ($cost_price == 0) {
                $cost_price = $store_cost_price->purchase_price;
            }
            $store_cost_price->cost_price = $cost_price;
            $store_cost_price->save();
            /**DB::table('product_transitions')->join('products', 'product_transitions.product_id', '=', 'products.id')->where('product_transitions.product_id',$p->id)->whereNotNull('product_transitions.transition_sale_id')->update(['product_transitions.cost_price'=>DB::raw('product_transitions.product_quantity * products.purchase_price')]);
            DB::table('product_transitions')->join('products', 'product_transitions.product_id', '=', 'products.id')->where('product_transitions.product_id',$p->id)->whereNotNull('product_transitions.transition_adjustment_id')->update(['product_transitions.cost_price'=>DB::raw('product_transitions.product_quantity * products.purchase_price')]);**/
        }


        dd('success');

        DB::table('product_transitions')
            ->where('transition_product_pivot_id', $request->product_pivot[$i])
            ->where('transition_sale_id', $id)
            ->update(array('cost_price' => $cost_price * $request->qty[$i], 'product_uom_id' => $main_uom_id, 'product_quantity' => $product_qty, 'transition_product_uom_id' => $request->uom[$i], 'transition_date' => $request->invoice_date, 'transition_product_quantity' => $request->qty[$i]));
        dd('exit');
        $order = DB::table('order_approvals')
            ->where('order_id', 922222)->get();
        //if(count($order) > 0) { dd('Do not delte');} else { dd('can delete'); }
        if (Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            foreach (Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach ($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);
                }
            }
        }
        $orders = DB::table('orders')
            ->where('created_by', Auth::user()->id)
            ->pluck('id')->toArray();
        $data = Sale::with('products')->whereIn('order_id', $orders)->get();

        //echo Hash::make('vanillaM!22#');
        // Turn on output buffering
        ob_start();
        //Get the ipconfig details using system commond
        system('ipconfig /all');
        // Capture the output into a variable
        $mycomsys = ob_get_contents();
        // Clean (erase) the output buffer
        ob_clean();
        $find_mac = "Physical"; //find the "Physical" & Find the position of Physical text
        $pmac = strpos($mycomsys, $find_mac);
        // Get Physical Address
        $macaddress = substr($mycomsys, ($pmac + 36), 17);
        //Display Mac Address
        echo $macaddress;
        $app_total_amount = DB::table('order_approval_product')->where('approval_id', 2)->sum('total_amount');
        //dd($app_total_amount);
        $uom_relation = DB::table('product_selling_uom')
            ->select('relation')
            ->where('product_id', 1)
            ->where('uom_id', 5)
            ->first();
        if ($uom_relation) {
            $relation_val = $uom_relation->relation;
        } else {
            //for pre-defined product uom
            $relation_val = 1;
        }

        $data = DB::table("product_transitions")

            ->select(DB::raw("products.product_name,uom_id,uoms.uom_name, SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count, SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty"))

            ->leftjoin('products', 'products.id', '=', 'product_transitions.product_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

            ->where("product_transitions.product_id", 6)

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
            ->where('customer_id', '=', 1)
            ->groupBy('customer_id')
            ->first();
        // dd($chk_order->previous_balance);
    }

    public function getMinMaxReport(Request $request)
    {
        $route_name = Route::currentRouteName();

        $min_max = $this->getMinMax($request);
        if ($request->type_id == "min") {
            $type = "min";
        } else if ($request->type_id == "max") {
            $type = "max";
        } else {
            $type = "all";
        }
        if ($route_name == 'min_max_export') {
            $export = new MinMaxExport($min_max, $type);
            $fileName = 'Min Max Export' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }
        return compact('min_max', 'type');
    }
    public function getReorderLevelReport(Request $request)
    {
        $route_name = Route::currentRouteName();
        $reorder_level = $this->getReorderLevel($request);
        if ($route_name == 'reorder_level_export') {
            $export = new ReorderLevelExport($reorder_level);
            $fileName = 'Reorder Level Export' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }
        return compact('reorder_level');
    }
}
