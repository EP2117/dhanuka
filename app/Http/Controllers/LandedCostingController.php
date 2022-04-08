<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\LandedCosting;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductCostingExport;
use App\Product;
use App\User;
use DB;
use Session;

class LandedCostingController extends Controller
{
    public function index(Request $request)
    {
        $limit = 30;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }        

        $data = LandedCosting::with('products','supplier');
        if($request->costing_no != "") {
            $data->where('landed_costing_no', 'LIKE', '%'.$request->costing_no.'%');
        }
        if($request->supplier_id != "") {
            $data->where('supplier_id', $request->supplier_id);
        }
        $data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function show($id)
    {
        $costing = LandedCosting::with('products','supplier')->find($id);
        return compact('costing');
    }

    public function store(Request  $request){
        //auto generate collection no;
        DB::beginTransaction();
        try {
            $max_id = LandedCosting::max('id');
            if($max_id) {
                $max_id = $max_id + 1;
            } else {
                $max_id = 1;
            }
            $cost_no = "LC".str_pad($max_id,5,"0",STR_PAD_LEFT);

            $obj = new LandedCosting;
	        $obj->landed_costing_no = $cost_no;
	        $obj->shipping_method = $request->shipping_method;
	        $obj->supplier_id = $request->supplier_id;
	        $obj->container_no = $request->container_no;
	        if($request->shipping_method == 'container') {
	        	$obj->bill_date = $request->bill_date;
	        	$obj->bill_no = $request->bill_no;	
	        } else {
	        	$obj->bill_date = NULL;
	        	$obj->bill_no = NULL;
	        }	        
	        $obj->split_method = $request->split_method;
	        $obj->remark = $request->remark;
	        $obj->total_ctn = $request->all_total_ctn;
	        $obj->total_pcs = $request->all_total_pcs;
	        $obj->total_rmb = $request->all_total_rmb;
	        /**$obj->container_freight = $request->container_freight;
	        $obj->tax = $request->tax;
	        $obj->other_charges = $request->other_charges;**/
            $obj->container_freight = $request->container_freight;
            $obj->agent_fees = $request->agent_fees;
            $obj->bank_service_fees = $request->bank_service_fees;
            $obj->shipping_line_charges = $request->shipping_line_charges;
            $obj->port_charges = $request->port_charges;
            $obj->valuation_charges = $request->valuation_charges;
            $obj->insurance_charges = $request->insurance_charges;
            $obj->labour_charges = $request->labour_charges;
            $obj->document_charges = $request->document_charges;
            $obj->port_exam_charges = $request->port_exam_charges;
            $obj->tracking_charges = $request->tracking_charges;

	        $obj->total = $request->total;

	        $obj->save();

	        for($i=0; $i<count($request->product); $i++) {
	            //add product into pivot table	            
	            $pivot = $obj->products()->attach($request->product[$i],['total_ctn' => $request->total_ctn[$i], 'pcs_per_ctn' => $request->pcs_per_ctn[$i], 'total_pcs' => $request->total_pcs[$i], 'rmb_rate' => $request->rmb_rate[$i], 'total_rmb' => $request->total_rmb[$i], 'mmk_per_rmb' => $request->mmk_per_rmb[$i], 'mmk_rate' => $request->mmk_rate[$i], 'duty_charges' => $request->duty_charges[$i], 'landed_cost_per_product' => $request->landed_cost_per_product[$i], 'cost' => $request->cost[$i], 'total_cost' => $request->total_cost[$i]]);
	        }

            $status = "success";
            $costing_id = $obj->id;
            DB::commit();
            return compact('status','costing_id');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            $status = "fail";
            return compact('status');
            // something went wrong
        }
      
    }

    public function update(Request  $request, $id){
        //auto generate collection no;
        DB::beginTransaction();
        try {
            
            $obj = LandedCosting::find($id);
	        //$obj->landed_costing_no = $cost_no;
	        $obj->shipping_method = $request->shipping_method;
	        $obj->supplier_id = $request->supplier_id;
	        $obj->container_no = $request->container_no;
	        if($request->shipping_method == 'container') {
	        	$obj->bill_date = $request->bill_date;
	        	$obj->bill_no = $request->bill_no;	
	        } else {
	        	$obj->bill_date = NULL;
	        	$obj->bill_no = NULL;
	        }	        
	        $obj->split_method = $request->split_method;
	        $obj->remark = $request->remark;
	        $obj->total_ctn = $request->all_total_ctn;
	        $obj->total_pcs = $request->all_total_pcs;
	        $obj->total_rmb = $request->all_total_rmb;
	        /**$obj->container_freight = $request->container_freight;
	        $obj->tax = $request->tax;
	        $obj->other_charges = $request->other_charges;**/
            $obj->container_freight = $request->container_freight;
            $obj->agent_fees = $request->agent_fees;
            $obj->bank_service_fees = $request->bank_service_fees;
            $obj->shipping_line_charges = $request->shipping_line_charges;
            $obj->port_charges = $request->port_charges;
            $obj->valuation_charges = $request->valuation_charges;
            $obj->insurance_charges = $request->insurance_charges;
            $obj->labour_charges = $request->labour_charges;
            $obj->document_charges = $request->document_charges;
            $obj->port_exam_charges = $request->port_exam_charges;
            $obj->tracking_charges = $request->tracking_charges;
            
	        $obj->total = $request->total;

	        $obj->save();

	        $obj->products()->detach();

	        //dd($request->product);
	        for($i=0; $i<count($request->product); $i++) {
	            //add product into pivot table	            
	            $pivot = $obj->products()->attach($request->product[$i],['total_ctn' => $request->total_ctn[$i], 'pcs_per_ctn' => $request->pcs_per_ctn[$i], 'total_pcs' => $request->total_pcs[$i], 'rmb_rate' => $request->rmb_rate[$i], 'total_rmb' => $request->total_rmb[$i], 'mmk_per_rmb' => $request->mmk_per_rmb[$i], 'mmk_rate' => $request->mmk_rate[$i], 'duty_charges' => $request->duty_charges[$i], 'landed_cost_per_product' => $request->landed_cost_per_product[$i], 'cost' => $request->cost[$i], 'total_cost' => $request->total_cost[$i]]);
	        }

            $status = "success";
            $costing_id = $obj->id;
            DB::commit();
            return compact('status','costing_id');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            $status = "fail";
            return compact('status');
            // something went wrong
        }
      
    }

    public function getProductCosting(Request $request)
    {
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 240);

         $data = DB::table("product_landed_costing")

                    ->select(DB::raw("product_landed_costing.*, landed_costings.landed_costing_no, landed_costings.bill_date, landed_costings.bill_no, landed_costings.container_no, suppliers.name as supplier_name, products.product_code, products.product_name"))

                    ->leftjoin('landed_costings', 'landed_costings.id', '=', 'product_landed_costing.landed_costing_id')

                    ->leftjoin('products', 'products.id', '=', 'product_landed_costing.product_id')

                    ->leftjoin('suppliers', 'suppliers.id', '=', 'landed_costings.supplier_id');

        if($request->landed_cost_no != "") {
            $data->where('landed_costings.landed_costing_no', $request->landed_cost_no);
        }

        if($request->bill_no != "") {
            $data->where('landed_costings.bill_no', $request->bill_no);
        }

        if($request->container_no != "") {
            $data->where('landed_costings.container_no', $request->container_no);
        }

        if($request->bill_from_date != '' && $request->bill_to_date != '')
        {
            $data->whereBetween('landed_costings.bill_date', array($request->bill_from_date, $request->bill_to_date));
        } else if($request->bill_from_date != '') {
            $data->whereDate('landed_costings.bill_date', '>=', $request->bill_from_date);

        }else if($request->bill_to_date != '') {
            $data->whereDate('landed_costings.bill_date', '<=', $request->bill_to_date);
        } else {}

        if($request->supplier_id != "") {
            $data->where('landed_costings.supplier_id', $request->supplier_id);
        }

        if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            /**$binds = array(strtolower($request->product_name));
            $data->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);**/
            $data->where('products.product_name', 'LIKE', "%$request->product_name%");
        }

        $data    =  $data->orderBy('products.product_name', 'ASC')->get();

       // $sale_arr = $data->pluck('sale_id')->toArray();

        $html = '';

        foreach($data as $k=>$product) {  
            if($k==0 || $product->product_id != $data[$k-1]->product_id)
            {
            	$total_ctn = 0; $total_pcs = 0; $total_mmk = 0;
            	$total_rmb = 0; $total_cost = 0; $total_total_cost = 0;
            	$html .= '<tr><td colspan="16" class="text-center font-bold mm-txt"><b>'.$product->product_name.'</b></td></tr>';
            }
            $html .= '<tr>';
            $html .= '<td>'.$product->landed_costing_no.'</td>';
            $html .= '<td class="text-center mm-txt">'.$product->supplier_name.'</td>';
            $html .= '<td class="text-center">'.$product->bill_date.'</td>';
            $html .= '<td class="text-center">'.$product->bill_no.'</td>';
            $html .= '<td class="text-center">'.$product->container_no.'</td>';
            $html .= '<td class="text-right">'.floatval($product->total_ctn) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->pcs_per_ctn) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->total_pcs).'</td>';
            $html .= '<td class="text-right">'.floatval($product->rmb_rate) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->total_rmb) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->mmk_per_rmb) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->mmk_rate) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->duty_charges) .'</td>';
            $html .= '<td class="text-right">'.floatval($product->landed_cost_per_product).'</td>';
            $html .= '<td class="text-right">'.floatval($product->cost).'</td>';
            $html .= '<td class="text-right">'.floatval($product->total_cost) .'</td>';

            $html .= '</tr>';

            $total_ctn = $total_ctn + $product->total_ctn;
            $total_pcs = $total_pcs + $product->total_pcs;
            $total_rmb = $total_rmb + $product->total_rmb;
            $total_mmk = $total_mmk + $product->mmk_rate;
            $total_cost = $total_cost + $product->cost;
            $total_total_cost = $total_total_cost + $product->total_cost;

        	if(count($data) == 1 || $k == count($data) - 1 || (isset($data[$k+1]) && $product->product_id != $data[$k+1]->product_id))
            {
                $html .= '<tr>';
                $html .= '<td colspan ="5" style="text-align: right;">Total</td>';
                $html .= '<td style="text-align: right;">'.floatval($total_ctn) .'</td>';
                $html .= '<td></td>';
                $html .= '<td style="text-align: right;">'.floatval($total_pcs) .'</td>';
                $html .= '<td></td>';
                $html .= '<td style="text-align: right;">'.floatval($total_rmb) .'</td>';
                $html .= '<td></td>';
                $html .= '<td style="text-align: right;">'.floatval($total_mmk) .'</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td style="text-align: right;">'.floatval($total_cost) .'</td>';
                $html .= '<td style="text-align: right;">'.floatval($total_total_cost) .'</td>';
                $html .= '</tr>';
            } 

        }  

        //return $html;
        return array($data,$html);
    }

    public function getProductCostingReport(Request $request)
    {
        list($data,$html) = $this->getProductCosting($request);
        return response(compact('html'), 200);
    }

    public function exportProductCostingReport(Request $request)
    {
        list($data,$html) = $this->getProductCosting($request);
        $export = new ProductCostingExport($data,$request);
        $fileName = 'product_costing_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function destroy($id)
    {
        $obj = LandedCosting::find($id);

        $obj->products()->detach();

        $obj->delete();
        return response(['message' => 'delete successful']);
    }
}
