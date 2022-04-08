<?php

namespace App\Http\Controllers;

use Session;
use App\Sale;
use App\User;
use App\Product;
use App\Customer;
use App\SaleReturn;
use Carbon\Carbon;
use App\AccountTransition;
use App\ProductTransition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use App\Http\Traits\Report\GetReport;
use App\Http\Traits\AccountReport\Ledger;
use App\Exports\SaleReturnExport;
use App\Exports\SaleReturnProductExport;
use App\Exports\SaleReturnOSExport;
use DB;

class SaleReturnController extends Controller
{
	use GetReport;
    use Ledger;

    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 

        $login_year = Session::get('loginYear');

        $data = SaleReturn::with('office_sale_man','warehouse','customer','branch');

        if($request->from_date != '' && $request->to_date != '')
        {            
            $data->whereBetween('return_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('return_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $data->whereDate('return_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('return_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if(isset($request->return_no) && $request->return_no != "") {
            $data->where('return_no','LIKE','%'.$request->return_no.'%');
        }
        if(isset($request->sale_man_id) && $request->sale_man_id != "") {
            $data->where('office_sale_man_id', $request->sale_man_id);
        }
        if(isset($request->customer_id) && $request->customer_id != "") {
            $data->where('customer_id', $request->customer_id);
        } 

        $data = $data->orderBy('id', 'DESC')->paginate($limit); 
        return response(compact('data'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = SaleReturn::with('products','office_sale_man','warehouse','customer','branch')->find($id);

        if($data->return_method == 'with invoice') {
        	foreach($data->products as $key=>$product) {
        		$product_sale = DB::table("product_sale")
        			 ->select(DB::raw("product_sale.return_quantity,product_sale.product_quantity as sale_product_quantity"))
        			 ->where('product_sale.id',$product->pivot->sale_product_pivot_id)->first();
                     //$data->products[0]->pp = $product_sale->sale_product_quantity;
                     //dd($data->products[0]->pp);
        		$data->products[$key]->sale_product_quantity = $product_sale->sale_product_quantity;
        		$data->products[$key]->return_quantity = $product_sale->return_quantity;                
            
        	}
        	//dd($data->products);
        }

        $customer_id = $data->customer_id;

        $previous_balance = 0;
        $return_amount = 0;
        $sale = '';
        if($data->return_method == "without invoice") {
            $chk_balance = DB::table("sales")

                    ->select(DB::raw("SUM(CASE  WHEN collection_amount IS NOT NULL THEN collection_amount  ELSE 0 END)  as total_collection_amount, SUM(CASE  WHEN discount IS NOT NULL THEN discount  ELSE 0 END)  as total_discount, SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as total_balance"))
                    ->where('customer_id','=',$customer_id)
                    ->where('payment_type', '=', 'credit')
                    ->groupBy('customer_id')
                    ->first();
        if($chk_balance) {
            //$previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount + $chk_balance->total_discount);
            $previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount);
        }

        $chk_return = DB::table("sale_returns")

            ->select(DB::raw("SUM(sale_returns.total_payment_amount)  as return_payment"))
            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->where('sale_returns.customer_id','=',$customer_id)
            ->where('sale_returns.id','!=',$id)
            ->where('sales.payment_type','!=','cash')
            ->groupBy('sale_returns.customer_id')
            ->first();
        if($chk_return) {
            $return_amount = $chk_return->return_payment;
        }


        $cus_return_amount = 0;
        $chk_cus_return = DB::table("return_invoices")

            ->select(DB::raw("SUM(return_invoices.return_amount)  as cus_return_amount"))
            ->leftjoin('customer_returns', 'customer_returns.id', '=', 'return_invoices.customer_return_id')
            ->where('customer_returns.customer_id','=',$customer_id)
            ->groupBy('customer_returns.customer_id')
            ->first();
        if($chk_cus_return) {
            $cus_return_amount = $chk_cus_return->cus_return_amount;
        }

        $previous_balance = ($previous_balance - $return_amount) - $cus_return_amount;
	        /**$chk_balance = DB::table("sales")

	            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount ELSE 0 END)  as previous_balance"))
	            ->where('customer_id','=',$customer_id)
	            ->groupBy('customer_id')
	            ->first();
	        if($chk_balance) {
	            $previous_balance  = $chk_balance->previous_balance;
	        }

	        $chk_return = DB::table("sale_returns")

	            ->select(DB::raw("SUM(sale_returns.total_payment_amount)  as return_payment"))
	            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
	            ->where('sale_returns.customer_id','=',$customer_id)
	            ->where('sale_returns.id','!=',$id)
	            ->where('sales.payment_type','!=','cash')
	            ->groupBy('sale_returns.customer_id')
	            ->first();
	        if($chk_return) {
	            $return_amount = $chk_return->return_payment;
	        }

	        $previous_balance = $previous_balance - $return_amount;**/
	    } else {
	    	$sale = Sale::orderBy('invoice_date', 'DESC')
                ->where('is_opening','!=',1)
                ->where('customer_id',$data->customer_id)
                ->get();
	    }
        return compact('data', 'previous_balance', 'sale');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $return = new SaleReturn;
            //auto generate invoice no;
            $max_id = SaleReturn::max('id');
            if($max_id) {
                $max_id = $max_id + 1;
            }else {
                $max_id = 1;
            }
            $return_no = "RN".str_pad($max_id,5,"0",STR_PAD_LEFT);
            $return->return_no = $return_no;
            if($request->return_method == 'with invoice') {
            	$branch_id = $request->branch_id;
            	$return->branch_id = $branch_id;
            	$warehouse_id = $request->warehouse_id;
            	$return->warehouse_id = $warehouse_id;	
            	$sale_id = $request->sale_id;
            	$return->sale_id = $sale_id;
            } else {
            	$branch_id = Auth::user()->branch_id;
            	$return->branch_id = $branch_id;
            	$warehouse_id = Auth::user()->warehouse_id;
            	$return->warehouse_id = $warehouse_id;
            	$sale_id = NULL;	
            }
            $return->return_method = $request->return_method;
            $return->reference_no = $request->reference_no;
            $return->return_date = $request->return_date;
            $return->customer_id = $request->customer_id;
            //$sale->delivery_approve = 0;
            $return->office_sale_man_id = $request->office_sale_man_id;
            $return->return_type  = $request->payment_type;
            $return->total_amount = $request->all_total_amount;
            $return->payment_amount = $request->return_amount;
            $return->balance_amount = $request->balance_amount;
            $return->total_payment_amount = $request->return_amount;
            $return->created_by = Auth::user()->id;
            $return->updated_by = Auth::user()->id;
            $return->save();

            //update in sale table
            if($request->return_method=="with invoice") {
            	$sale=Sale::find($request->sale_id);
            	if($sale->payment_type == 'credit') {
            		$sale->return_amount = $request->return_amount + $sale->return_amount;
            	} else {
            		$sale->cash_return_amount = $request->return_amount + $sale->cash_return_amount;
            	}
            	$sale->save();
            } else {
                
            }

            $description=$return->return_no.", Date ".$return->return_date." by " .$return->customer->cus_name;
            /* Cash Book  for sale*/
            if($return) {
            	if($request->return_method=="with invoice") {
            		if($sale->payment_type == "cash") {
            			//for cash invoice
            			AccountTransition::create([
	                        //'sub_account_id' => 8,
                            'sub_account_id' => 77,
	                        'transition_date' => $request->return_date,
	                        'sale_id' => $sale->id,
	                        'customer_id' => $request->customer_id,
	                        'return_id' => $return->id,
	                        'status'=>'return',
	                        'vochur_no'=>$return_no,
	                        'description'=>'',
	                        'is_cashbook' => 0,
	                        'debit' => $request->all_total_amount,
	                        'created_by' => Auth::user()->id,
	                        'updated_by' => Auth::user()->id,
	                    ]);
            			if(!empty($request->return_amount)) {
		                    AccountTransition::create([
		                        //'sub_account_id' => 77,
                                'sub_account_id' => 78,
		                        'transition_date' => $request->return_date,
		                        'sale_id' => $sale->id,
		                        'customer_id' => $request->customer_id,
		                        'return_id' => $return->id,
		                        'status'=>'return',
		                        'vochur_no'=>$return_no,
		                        'description'=>'',
		                        'is_cashbook' => 0,
		                        'credit' => $request->return_amount,
		                        'created_by' => Auth::user()->id,
		                        'updated_by' => Auth::user()->id,
		                    ]);

		                    AccountTransition::create([
		                        //'sub_account_id' => 77,
                                'sub_account_id' => 78,
		                        'transition_date' => $request->return_date,
		                        'customer_id' => $request->customer_id,
		                        'sale_id' => $sale->id,
		                        'return_id' => $return->id,
		                        'status'=>'return',
		                        'vochur_no'=>$return_no,
		                        'description'=>$description,
		                        'is_cashbook' => 1,
		                        'credit' => $request->return_amount,
		                        'created_by' => Auth::user()->id,
		                        'updated_by' => Auth::user()->id,
		                    ]);
		                }
            		} else {
            			//for credit invoice
            			if(!empty($request->return_amount)) {
	            			AccountTransition::create([
		                        //'sub_account_id' => 77,
                                'sub_account_id' => 78,
		                        'transition_date' => $request->return_date,
		                        'customer_id' => $request->customer_id,
		                        'sale_id' => $sale->id,
		                        'return_id' => $return->id,
		                        'status'=>'return',
		                        'vochur_no'=>$return_no,
		                        'description'=>'',
		                        'is_cashbook' => 0,
		                        'debit' => $request->return_amount,
		                        'created_by' => Auth::user()->id,
		                        'updated_by' => Auth::user()->id,
		                    ]);
		                }
            		}
            	} else {
            		//for without invoice Return
            		/***if(!empty($request->return_amount)) {
	            		AccountTransition::create([
	                        'sub_account_id' => 77,
	                        'transition_date' => $request->return_date,
	                        'customer_id' => $request->customer_id,
	                        'return_id' => $return->id,
	                        'status'=>'return',
	                        'vochur_no'=>$return_no,
	                        'description'=>'',
	                        'is_cashbook' => 0,
	                        'debit' => $request->return_amount,
	                        'created_by' => Auth::user()->id,
	                        'updated_by' => Auth::user()->id,
	                    ]);
	                }***/
            	}
            	
                // cashbook
                /**if ($request->payment_type == 'cash') {
                    AccountTransition::create([
                        'sub_account_id' => $sub_account_id,
                        'transition_date' => $sale->invoice_date,
                        'sale_id' => $sale->id,
                        'status'=>'sale',
                        'vochur_no'=>$invoice_no,
                        'description'=>$description,
                        'is_cashbook' => 1,
                        'debit' => $amount,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    // end cashbook 

	                // for ledger 
	               $this->storeSaleInLedger($sale);
	                // end ledger
	                } **/               
            }

            $return_id = $return->id;
            for($i=0; $i<count($request->product); $i++) {
                //update return value in pivot table
                if($request->return_method=="with invoice") {
	            	/**$product_sale_id=DB::table('product_sale')->where('id', $request->product_sale_id[$i])->first();
	            	$return_quantity = $product_sale_id->return_quantity + $request->qty[$i];
	            	$product_sale_id->return_quantity = $return_quantity;
	            	$product_sale_id->save();**/

	            	DB::table('product_sale')->where('id', $request->product_sale_id[$i])->update(['return_quantity' =>  DB::raw( 'return_quantity + '.$request->qty[$i])]);
	                $sale_product_pivot_id = $request->product_sale_id[$i];
	            } else {
	            	$sale_product_pivot_id = NULL;
	            }

            	//get product pre-defined UOM
                $product_result = Product::select('uom_id')->find($request->product[$i]);
                $main_uom_id = $product_result->uom_id;
                $pivot = $return->products()->attach($request->product[$i],['sale_product_pivot_id' => $sale_product_pivot_id, 'uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'actual_rate' => $request->actual_rate[$i], 'discount' => $request->discount[$i], 'other_discount' => $request->other_discount[$i], 'total_amount' => $request->total_amount[$i], 'is_foc' => $request->is_foc[$i]]);

                //get last pivot insert id
                $last_row=DB::table('product_return')->orderBy('id', 'DESC')->first();

                $pivot_id = $last_row->id;

                //calculate quantity for product pre-defined UOM
                $uom_relation = DB::table('product_selling_uom')
                                ->select('relation')
                                ->where('product_id',$request->product[$i])
                                ->where('uom_id',$request->uom[$i])
                                ->first();
                if($uom_relation) {
                    $relation_val = $uom_relation->relation;
                } else {
                    //for pre-defined product uom
                    $relation_val = 1;
                }
                // $pp=DB::table('product_purchase')->where('product_id',$request->product[$i])->get();
            // $q=$m=0;
             $cost_price=$this->getCostPrice($request->product[$i])->product_cost_price;
            // dd($cost_price);
             $store_cost_price=Product::find($request->product[$i]);
             if($cost_price==0){
                 $cost_price=$store_cost_price->purchase_price;
             }
             $store_cost_price->cost_price=$cost_price;
             $store_cost_price->save();
                //add products in transition table=> transition_type = out (for sold out)
                $obj = new ProductTransition;
                $obj->product_id            = $request->product[$i];
                $obj->transition_type       = "in";
                $obj->transition_sale_id   = $sale_id;
                $obj->transition_return_id   = $return_id;
                $obj->transition_product_pivot_id   = $pivot_id;
                $obj->branch_id  = $branch_id;
                $obj->warehouse_id          = $warehouse_id;
                $obj->transition_date       = $request->return_date;
                $obj->cost_price            =$cost_price *$request->qty[$i] * $relation_val;
                $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $request->qty[$i];
                $obj->product_uom_id        = $main_uom_id;
                $obj->product_quantity      = $request->qty[$i] * $relation_val;
                $obj->created_by = Auth::user()->id;
                $obj->updated_by = Auth::user()->id;
                $obj->save();
            }
            $status = "success";
            DB::commit();
            return compact('status','return_id');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
            
        }
      
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $return = SaleReturn::find($id);

            $old_return_amount = $return->payment_amount;

            if($request->return_method == 'with invoice') {
            	$branch_id = $request->branch_id;
            	$return->branch_id = $branch_id;
            	$warehouse_id = $request->warehouse_id;
            	$return->warehouse_id = $warehouse_id;	
            	$sale_id = $request->sale_id;
            	$return->sale_id = $sale_id;
            } else {
            	//$branch_id = Auth::user()->branch_id;
            	//$return->branch_id = $branch_id;
            	//$warehouse_id = Auth::user()->warehouse_id;
            	//$return->warehouse_id = $warehouse_id;
            	$branch_id = $request->branch_id;
            	$return->branch_id = $branch_id;
            	$warehouse_id = $request->warehouse_id;
            	$return->warehouse_id = $warehouse_id;
            	$sale_id = NULL;	
            }
            $return->return_method = $request->return_method;
            //$return->reference_no = $request->reference_no;
            $return->return_date = $request->return_date;
            $return->customer_id = $request->customer_id;
            //$sale->delivery_approve = 0;
            $return->office_sale_man_id = $request->office_sale_man_id;
            $return->return_type  = $request->payment_type;
            $return->total_amount = $request->all_total_amount;
            $return->payment_amount = $request->return_amount;
            $return->balance_amount = $request->balance_amount;
            $return->total_payment_amount = $request->return_amount;
            //$return->created_by = Auth::user()->id;
            $return->updated_by = Auth::user()->id;
            $return->save();
            $return_no = $return->return_no;
            //update in sale table
            if($request->return_method=="with invoice") {
            	$sale=Sale::find($request->sale_id);
            	if($sale->payment_type == 'credit') {
            		$sale->return_amount = ($request->return_amount + $sale->return_amount) - $old_return_amount;
            	} else {
            		$sale->cash_return_amount = ($request->return_amount + $sale->cash_return_amount) - $old_return_amount;
            	}
            	$sale->save();
            }

            $return->products()->detach();

            DB::table('product_transitions')
                ->where('transition_return_id', $id)
                ->delete();

            AccountTransition::where('return_id',$id)->delete();

            $description=$return->return_no.", Date ".$return->return_date." by " .$return->customer->cus_name;
            /* Cash Book  for sale*/
            if($return) {
            	if($request->return_method=="with invoice") {
            		if($sale->payment_type == "cash") {
            			//for cash invoice
            			AccountTransition::create([
	                        //'sub_account_id' => 8,
                            'sub_account_id' => 77,
	                        'transition_date' => $request->return_date,
	                        'sale_id' => $sale->id,
	                        'customer_id' => $request->customer_id,
	                        'return_id' => $return->id,
	                        'status'=>'return',
	                        'vochur_no'=>$return_no,
	                        'description'=>'',
	                        'is_cashbook' => 0,
	                        'debit' => $request->all_total_amount,
	                        'created_by' => Auth::user()->id,
	                        'updated_by' => Auth::user()->id,
	                    ]);
            			if(!empty($request->return_amount)) {
		                    AccountTransition::create([
		                        //'sub_account_id' => 77,
                                'sub_account_id' => 78,
		                        'transition_date' => $request->return_date,
		                        'sale_id' => $sale->id,
		                        'customer_id' => $request->customer_id,
		                        'return_id' => $return->id,
		                        'status'=>'return',
		                        'vochur_no'=>$return_no,
		                        'description'=>'',
		                        'is_cashbook' => 0,
		                        'credit' => $request->return_amount,
		                        'created_by' => Auth::user()->id,
		                        'updated_by' => Auth::user()->id,
		                    ]);

		                    AccountTransition::create([
		                        //'sub_account_id' => 77,
                                'sub_account_id' => 78,
		                        'transition_date' => $request->return_date,
		                        'customer_id' => $request->customer_id,
		                        'sale_id' => $sale->id,
		                        'return_id' => $return->id,
		                        'status'=>'return',
		                        'vochur_no'=>$return_no,
		                        'description'=>$description,
		                        'is_cashbook' => 1,
		                        'credit' => $request->return_amount,
		                        'created_by' => Auth::user()->id,
		                        'updated_by' => Auth::user()->id,
		                    ]);
		                }
            		} else {
            			//for credit invoice
            			if(!empty($request->return_amount)) {
	            			AccountTransition::create([
		                        //'sub_account_id' => 77,
                                'sub_account_id' => 78,
		                        'transition_date' => $request->return_date,
		                        'customer_id' => $request->customer_id,
		                        'sale_id' => $sale->id,
		                        'return_id' => $return->id,
		                        'status'=>'return',
		                        'vochur_no'=>$return_no,
		                        'description'=>'',
		                        'is_cashbook' => 0,
		                        'debit' => $request->return_amount,
		                        'created_by' => Auth::user()->id,
		                        'updated_by' => Auth::user()->id,
		                    ]);
		                }
            		}
            	} else {
            		//for without invoice Return
            		/***if(!empty($request->return_amount)) {
	            		AccountTransition::create([
	                        'sub_account_id' => 77,
	                        'transition_date' => $request->return_date,
	                        'customer_id' => $request->customer_id,
	                        'return_id' => $return->id,
	                        'status'=>'return',
	                        'vochur_no'=>$return_no,
	                        'description'=>'',
	                        'is_cashbook' => 0,
	                        'debit' => $request->return_amount,
	                        'created_by' => Auth::user()->id,
	                        'updated_by' => Auth::user()->id,
	                    ]);
	                }***/
            	}
            	              
            }

            $return_id = $return->id;
            for($i=0; $i<count($request->product); $i++) {                

            	if($request->return_method=="with invoice") {
	                $sale_product_pivot_id = $request->product_sale_id[$i];
	            } else {
	            	$sale_product_pivot_id = NULL;
	            }
            	//get product pre-defined UOM
                $product_result = Product::select('uom_id')->find($request->product[$i]);
                $main_uom_id = $product_result->uom_id;
                $pivot = $return->products()->attach($request->product[$i],['sale_product_pivot_id' => $sale_product_pivot_id, 'uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'actual_rate' => $request->actual_rate[$i], 'discount' => $request->discount[$i], 'other_discount' => $request->other_discount[$i], 'total_amount' => $request->total_amount[$i], 'is_foc' => $request->is_foc[$i]]);

                //get last pivot insert id
                $last_row=DB::table('product_return')->orderBy('id', 'DESC')->first();

                $pivot_id = $last_row->id;

                //update return value in pivot table
                if($request->return_method=="with invoice") {
	            	/**$product_sale_id=DB::table('product_sale')->where('id', $request->product_sale_id[$i])->first();
	            	$return_quantity = $product_sale_id->return_quantity + $request->qty[$i];
	            	$product_sale_id->return_quantity = $return_quantity;
	            	$product_sale_id->save();**/
	            	$ps_return = DB::table("product_return")

						            ->select(DB::raw("SUM(product_quantity)  as ps_return_quantity"))
						            ->where('sale_product_pivot_id','=', $request->product_sale_id[$i])
						            ->groupBy('sale_product_pivot_id')->first();

	            	/**DB::table('product_sale')->where('id', $request->product_sale_id[$i])->update(['return_quantity' =>  DB::raw( 'return_quantity + '.$ps_return->ps_return_quantity)]);**/
	            	DB::table('product_sale')->where('id', $request->product_sale_id[$i])->update(['return_quantity' => $ps_return->ps_return_quantity]);
	            } else {}

                //calculate quantity for product pre-defined UOM
                $uom_relation = DB::table('product_selling_uom')
                                ->select('relation')
                                ->where('product_id',$request->product[$i])
                                ->where('uom_id',$request->uom[$i])
                                ->first();
                if($uom_relation) {
                    $relation_val = $uom_relation->relation;
                } else {
                    //for pre-defined product uom
                    $relation_val = 1;
                }
                // $pp=DB::table('product_purchase')->where('product_id',$request->product[$i])->get();
            // $q=$m=0;
             $cost_price=$this->getCostPrice($request->product[$i])->product_cost_price;
            // dd($cost_price);
             $store_cost_price=Product::find($request->product[$i]);
             if($cost_price==0){
                 $cost_price=$store_cost_price->purchase_price;
             }
             $store_cost_price->cost_price=$cost_price;
             $store_cost_price->save();
                //add products in transition table=> transition_type = out (for sold out)
                $obj = new ProductTransition;
                $obj->product_id            = $request->product[$i];
                $obj->transition_type       = "in";
                $obj->transition_sale_id   = $sale_id;
                $obj->transition_return_id   = $return_id;
                $obj->transition_product_pivot_id   = $pivot_id;
                $obj->branch_id  = $branch_id;
                $obj->warehouse_id          = $warehouse_id;
                $obj->transition_date       = $request->return_date;
                $obj->cost_price            =$cost_price *$request->qty[$i] * $relation_val;
                $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $request->qty[$i];
                $obj->product_uom_id        = $main_uom_id;
                $obj->product_quantity      = $request->qty[$i] * $relation_val;
                $obj->created_by = Auth::user()->id;
                $obj->updated_by = Auth::user()->id;
                $obj->save();
            }
            $status = "success";
            DB::commit();
            return compact('status','return_id');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
            
        }
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $return = SaleReturn::with('products')->find($id);
        
        //update return amount and cash return amount  in sale table
        if($return->return_method=="with invoice") {
        	$sale=Sale::find($return->sale_id);
        	if($sale->payment_type == 'credit') {
        		$sale->return_amount = $sale->return_amount - $return->payment_amount;
        	} else {
        		$sale->cash_return_amount = $sale->cash_return_amount - $return->payment_amount;
        	}
        	$sale->save();

        	//update return quantity in product_sale table
	        foreach($return->products as $prod) {

            	DB::table('product_sale')->where('id', $prod->pivot->sale_product_pivot_id )->update(['return_quantity' =>  DB::raw( 'return_quantity - '.$prod->pivot->product_quantity)]);
	        }
        }        

        //remove return products
        $return->products()->detach();

        DB::table('product_transitions')
                ->where('transition_return_id', $id)
                ->delete();

        AccountTransition::where('return_id',$id)->delete();

        $return->delete();

        return response(['message' => 'delete successful']);
    }

    public function getSaleReturnByCustomer($cus_id, Request $request)
    {
        $data = SaleReturn::orderBy('id', 'DESC')
                ->where('customer_id',$cus_id)
                ->where('return_type', 'credit')
                ->whereRaw('(total_amount - total_payment_amount) > 0')
                ->get();
        // dd($data);
        return response(compact('data'), 200);
    }

    public function getSaleReturn(Request $request)
    {

        $data = SaleReturn::with('sale','customer');
        /**$from_date=$to_date="";
        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereHas('sales',function($q)use($request){
                    $q->whereBetween('invoice_date', array($request->from_date, $request->to_date));
                }); 
           // $data->whereBetween('landed_costings.bill_date', array($request->bill_from_date, $request->bill_to_date));
        } else if($request->from_date != '') {
            $data->whereHas('sales',function($q)use($request){
                    $q->whereDate('invoice_date', '>=', $request->from_date);
                });
            //$data->whereDate('landed_costings.bill_date', '>=', $request->bill_from_date);

        }else if($request->to_date != '') {
            $data->whereHas('sales',function($q)use($request){
                    $q->whereDate('invoice_date', '<=', $request->to_date);
                });
            //$data->whereDate('landed_costings.bill_date', '<=', $request->bill_to_date);
        } else {}**/
        /**if($request->supplier_id != "") {
            $data->where('landed_costings.supplier_id', $request->supplier_id);
        }**/

        /**if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            //$binds = array(strtolower($request->product_name));
            $data->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
            $data->where('products.product_name', 'LIKE', "%$request->product_name%");
        }**/
        if($request->invoice_date != '') {
            $data->whereHas('sale',function($q)use($request){
                    $q->whereDate('invoice_date', $request->invoice_date);
                });
        }
        if($request->invoice_no != '') {
            $data->whereHas('sale',function($q)use($request){
                    $q->where('invoice_no', 'LIKE', '%'.$request->invoice_no.'%');
                });
        }
        if($request->return_no != '') {
            $data->where('return_no', 'LIKE', '%'.$request->return_no.'%');
        }
        if($request->return_method != '') {
            $data->where('return_method', $request->return_method);
        }
        if($request->customer_id != '') {
            $data->where('customer_id', $request->customer_id);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('return_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('return_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $data->whereDate('return_date', '<=', $request->to_date);
        } else {}

        $data    =  $data->orderBy('return_date', 'DESC')->get();

        $html = ''; $i=0;$total=0;
        foreach($data as $r) {
            $i++;
            $html .= '<tr>';
            $html .= '<td>'.$i.'</td>';
            if(!empty($r->sale)) {
                $html .= '<td class="text-center">'.$r->sale->invoice_no.'</td>';
                $html .= '<td class="text-center">'.$r->sale->invoice_date.'</td>';
            } else {
               $html .= '<td class="text-center"></td>';
               $html .= '<td class="text-center"></td>';  
            }

            $html .= '<td class="text-center">'.$r->return_no.'</td>';
            $html .= '<td class="text-center">'.$r->return_date.'</td>';

            $html .= '<td class="text-center mm-txt">'.$r->customer->cus_name.'</td>';
            $html .= '<td class="text-right">'.number_format($r->total_amount).'</td>';
            $total += $r->total_amount;
            $html .= '</tr>';

        } 
        if(!empty($data)) {
            $html .= '<tr><td colspan="6" class="text-right">Return Total</td><td class="text-right">'.number_format($total).'</td></tr>';

        }

        //return $html;
        return array($data,$html);
    }

    public function getSaleReturnReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturn($request);
        return response(compact('html'), 200);
    }

    public function exportSaleReturnReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturn($request);
        $export = new SaleReturnExport($data,$request);
        $fileName = 'sale_return_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function getSaleReturnProduct(Request $request)
    {

        $data = DB::table("product_return")

                    ->select(DB::raw("product_return.*, sales.invoice_date, sales.invoice_no, sale_returns.customer_id, sale_returns.return_no, sale_returns.return_date, products.product_code, products.product_name, customers.cus_name"))

                    ->leftjoin('sale_returns', 'sale_returns.id', '=', 'product_return.return_id')

                    ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')

                    ->leftjoin('products', 'products.id', '=', 'product_return.product_id')

                    ->leftjoin('customers', 'customers.id', '=', 'sale_returns.customer_id');

        if($request->invoice_date != '') {
            $data->whereDate('sales.invoice_date', $request->invoice_date);
        }

        if($request->invoice_no != '') {
            $data->where('sales.invoice_no', 'LIKE', '%'.$request->invoice_no.'%');
        }

        if($request->return_no != '') {
            $data->where('sale_returns.return_no', 'LIKE', '%'.$request->return_no.'%');
        }

        if($request->return_method != '') {
            $data->where('sale_returns.return_method', $request->return_method);
        }
        if($request->customer_id != '') {
            $data->where('sale_returns.customer_id', $request->customer_id);
        }

        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('sale_returns.return_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('sale_returns.return_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $data->whereDate('sale_returns.return_date', '<=', $request->to_date);
        } else {}

        if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            //$binds = array(strtolower($request->product_name));
            $data->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
            //$data->where('products.product_name', 'LIKE', "%$request->product_name%");
        }
        if($request->product_code != "") {
            $data->where('products.product_code', 'LIKE', '%'.$request->product_code.'%');
        }

        $data    =  $data->orderBy('product_return.return_id', 'ASC')->get();

        $html = ''; $i=0;$total=0;$total_qty=0;
        foreach($data as $r) {
            $i++;
            $html .= '<tr>';
            $html .= '<td class="text-right">'.$i.'</td>';
            if(!empty($r->invoice_no)) {
                $html .= '<td class="text-center">'.$r->invoice_no.'</td>';
                $html .= '<td class="text-center">'.$r->invoice_date.'</td>';
            } else {
               $html .= '<td class="text-center"></td>';
               $html .= '<td class="text-center"></td>';  
            }

            $html .= '<td class="text-center">'.$r->return_no.'</td>';
            $html .= '<td class="text-center">'.$r->return_date.'</td>';

            $html .= '<td class="text-center mm-txt">'.$r->cus_name.'</td>';
            $html .= '<td class="text-center mm-txt">'.$r->product_code.'</td>';
            $html .= '<td class="text-center mm-txt">'.$r->product_name.'</td>';
            $html .= '<td class="text-right mm-txt">'.(float)$r->product_quantity.'</td>';
            $html .= '<td class="text-right mm-txt">'.$r->actual_rate.'</td>';
            $html .= '<td class="text-right">'.number_format($r->total_amount).'</td>';
            $total += $r->total_amount;
            $total_qty += $r->product_quantity;
            $html .= '</tr>';

        } 
        if(!empty($data)) {
            $html .= '<tr><td colspan="8" class="text-right">Total</td><td class="text-right">'.number_format($total_qty).'</td><td></td><td class="text-right">'.number_format($total).'</td></tr>';

        }

        //return $html;
        return array($data,$html);
    }

    public function getSaleReturnProductReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturnProduct($request);
        return response(compact('html'), 200);
    }

    public function exportSaleReturnProductReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturnProduct($request);
        $export = new SaleReturnProductExport($data,$request);
        $fileName = 'sale_return_product_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }
    public function getSaleReturnOS(Request $request)
    {

        $data = SaleReturn::with('customer')->whereRaw('(total_amount - total_payment_amount) > 0');

        if($request->return_no != '') {
            $data->where('return_no', 'LIKE', '%'.$request->return_no.'%');
        }
       /** if($request->return_method != '') {
            $data->where('return_method', $request->return_method);
        }**/
        if($request->customer_id != '') {
            $data->where('customer_id', $request->customer_id);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('return_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('return_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $data->whereDate('return_date', '<=', $request->to_date);
        } else {}

        $data    =  $data->orderBy('return_date', 'DESC')->get();

        $html = ''; $i=0;$total=0;
        foreach($data as $r) {
            $i++;
            $html .= '<tr>';
            $html .= '<td class="text-right">'.$i.'</td>';

            $html .= '<td class="text-center">'.$r->return_no.'</td>';
            $html .= '<td class="text-center">'.$r->return_date.'</td>';

            $html .= '<td class="text-center mm-txt">'.$r->customer->cus_name.'</td>';
            $html .= '<td class="text-right">'.number_format($r->total_amount).'</td>';
            $html .= '<td class="text-right">'.number_format($r->payment_amount + $r->total_payment_amount).'</td>';
            $html .= '<td class="text-right">'.number_format($r->total_amount - $r->total_payment_amount).'</td>';
            $html .= '</tr>';

        } 
        /**if(!empty($data)) {
            $html .= '<tr><td colspan="6" class="text-right">Return Total</td><td class="text-right">'.number_format($total).'</td></tr>';

        }**/

        //return $html;
        return array($data,$html);
    }

    public function getSaleReturnOSReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturnOS($request);
        return response(compact('html'), 200);
    }

    public function exportSaleReturnOSReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturnOS($request);
        $export = new SaleReturnOSExport($data,$request);
        $fileName = 'return_os_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }
}
