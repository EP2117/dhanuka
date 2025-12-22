<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\SaleDelivery;
use App\Sale;
use Carbon\Carbon;
use App\Product;
use DB;
use Session;

class SaleDeliveryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $obj = new SaleDelivery;

        //auto generate delivery no.;
        //$max_id = OrderApproval::where('order_id', $request->order_id)->max('id');
        $count = DB::table('sale_deliveries')->where('sale_id', $request->sale_id)->count();
        $max_id = $count + 1;

        $delivery_no = 'DO'.$request->invoice_no.'_'.$max_id;

        $obj->delivery_no = $delivery_no;
        $obj->sale_id = $request->sale_id;
        $obj->invoice_no = $request->invoice_no;
        $obj->delivery_date = $request->delivery_date;
        //$obj->total_amount = $request->total_amount;
        $obj->created_by = Auth::user()->id;
        //$obj->updated_by = Auth::user()->id;        
        $obj->save();

        $delivery_id = $obj->id;

        
        for($i=0; $i<count($request->product_arr); $i++) {

            //get product pre-defined UOM
            $product_result = Product::select('uom_id')->find($request->product_arr[$i]);
            $main_uom_id = $product_result->uom_id;

            //add product into pivot table
            $pivot = $obj->products()->attach($request->product_arr[$i],['sale_id' => $request->sale_id,'uom_id' => $request->uom_arr[$i], 'delivery_qty' => $request->delivery_qty_arr[$i], 'price' => $request->unit_price_arr[$i], 'price_variant' => $request->price_variant_arr[$i], 'is_foc' => $request->is_foc_arr[$i]]); 
 

            //update delivered qty in product_sale table  
            $delivered_qty = 0; 
            $ps_result = DB::table('product_sale')
                            ->select('delivered_quantity')
                            ->where('id',$request->product_pivot_arr[$i])
                            ->first(); 
            if($ps_result->delivered_quantity == NULL) {
            	$delivered_qty = $request->delivery_qty_arr[$i];
            } else {
            	$delivered_qty = $ps_result->delivered_quantity + $request->delivery_qty_arr[$i];	
            } 
	

			DB::table('product_sale')
                    ->where('id', $request->product_pivot_arr[$i])
                    ->update(array('delivered_quantity' => $delivered_qty));

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

            //Check delivery status is done or not
             $chk_sale = DB::table("product_sale")

                        ->select(DB::raw("SUM(CASE  WHEN product_quantity IS NOT NULL THEN product_quantity  ELSE 0 END)  as product_qty, SUM(CASE  WHEN delivered_quantity IS NOT NULL THEN delivered_quantity  ELSE 0 END)  as delivered_qty"))
                        ->where('sale_id','=',$request->sale_id)
                        ->groupBy('sale_id')
                        ->first();
            //update sale delivery status
            $sale = Sale::find($request->sale_id);
            if($chk_sale->product_qty == $chk_sale->delivered_qty) {
                $status = "Done";
            } else {
                $status = "Pending";
            }
            //change status in sale table;
            $sale->delivery_status = $status;
            $sale->save();

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
        $delivery = SaleDelivery::with('products','products.uom','products.selling_uoms')->find($id);
        return compact('delivery');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        DB::table('sale_deliveries')
                ->where('sale_id', $id)
                ->delete();

        DB::table('sale_delivery_product')
                ->where('sale_id', $id)
                ->delete();

        DB::table('product_sale')
            ->where('sale_id', $id)
            ->update(array('delivered_quantity' => NULL));

        //change status in sale table;
        $sale->delivery_status = 'Draft';
        $sale->save();

        return response(['message' => 'delete successful']);
    }
}
