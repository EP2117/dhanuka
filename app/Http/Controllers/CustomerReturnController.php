<?php

namespace App\Http\Controllers;

use Session;
use App\Sale;
use App\User;
use App\Customer;
use App\CustomerReturn;
use Carbon\Carbon;
use App\AccountTransition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use DB;

class CustomerReturnController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 

        $login_year = Session::get('loginYear');

        $data = CustomerReturn::with('customer','sales');

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
            $data->where('customer_return_no','LIKE','%'.$request->return_no.'%');
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
        $data = CustomerReturn::with('sales','customer')->find($id);
        $customer_id = $data->customer_id;
        $cus_sales = array();
        foreach($data->sales as $sale) {
        	array_push($cus_sales, $sale->id);
        }

        $cus_invoices = Sale::orderBy('invoice_date', 'ASC')
        		->selectRaw('id,invoice_no,(total_amount-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) AS balance')
                ->where('customer_id',$customer_id)
                ->where('payment_type', 'credit')
                ->where(function ($query) use ($cus_sales){
	    				$query->whereRaw('(total_amount-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0')
	          				  ->orWhereIn('id', $cus_sales);
						})
        		->get();
        return compact('data','cus_invoices');
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
            $return = new CustomerReturn;
            //auto generate invoice no;
            $max_id = CustomerReturn::max('id');
            if($max_id) {
                $max_id = $max_id + 1;
            }else {
                $max_id = 1;
            }
            $return_no = "CR".str_pad($max_id,5,"0",STR_PAD_LEFT);
            $return->customer_return_no = $return_no;
            $return->return_date = $request->return_date;
            $return->customer_id = $request->customer_id;
            $return->return_amount = $request->pay_amount;
            //$return->updated_by = Auth::user()->id;
            $return->save();
            //dd($request->sale_bals);
			$return_amt = $request->pay_amount;
            for($i=0; $i<count($request->invoices); $i++) {
            	//update customer_return_amount in sales
            	if($return_amt > 0) {
	            	$sale = Sale::find($request->invoices[$i]);

	            	if($return_amt <= $request->sale_bals[$i]) {
	            		$sale->customer_return_amount = $sale->customer_return_amount + $return_amt;
	            		$return->sales()->attach($request->invoices[$i],['return_amount' => $return_amt]);
	            		$return_amt = 0;
	            	} else {
	            		$sale->customer_return_amount = $sale->customer_return_amount + $request->sale_bals[$i];
	            		$return->sales()->attach($request->invoices[$i],['return_amount' => $request->sale_bals[$i]]);
	            		$return_amt = $return_amt - $request->sale_bals[$i];
	            	}

	            	$sale->save();
	            }
				
			}
			if(!empty($request->pay_amount)) {
    			AccountTransition::create([
                    'sub_account_id' => 78,
                    'transition_date' => $request->return_date,
                    'customer_id' => $request->customer_id,
                    'customer_return_id' => $return->id,
                    'status'=>'customer return',
                    'vochur_no'=>$return_no,
                    'description'=>'',
                    'is_cashbook' => 0,
                    'debit' => $request->pay_amount,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }
            $status = "success";
            DB::commit();
            return compact('status');
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
            $return = CustomerReturn::with('sales')->find($id);
            //$return->customer_return_no = $return_no;
            $return->return_date = $request->return_date;
            $return->customer_id = $request->customer_id;
            $return->return_amount = $request->pay_amount;
            //$return->updated_by = Auth::user()->id;
            $return->save();
            $return_no = $return->customer_return_no;
            //dd($request->sale_bals);
            foreach($return->sales as $sale) {
            	//update customer_return_amount in sales
            	$sobj = Sale::find($sale->id);
        		$sobj->customer_return_amount = $sobj->customer_return_amount - $sale->pivot['return_amount'];
            	$sobj->save();
				
			}
			AccountTransition::where('customer_return_id',$id)->delete();
            $return->sales()->detach();
			$return_amt = $request->pay_amount;
            for($i=0; $i<count($request->invoices); $i++) {
            	//update customer_return_amount in sales
            	if($return_amt > 0) {
	            	$sale_obj = Sale::find($request->invoices[$i]);

	            	if($return_amt <= $request->sale_bals[$i]) {
	            		$sale_obj->customer_return_amount = $sale_obj->customer_return_amount + $return_amt;
	            		$return->sales()->attach($request->invoices[$i],['return_amount' => $return_amt]);
	            		$return_amt = 0;
	            	} else {
	            		$sale_obj->customer_return_amount = $sale_obj->customer_return_amount + $request->sale_bals[$i];
	            		$return->sales()->attach($request->invoices[$i],['return_amount' => $request->sale_bals[$i]]);
	            		$return_amt = $return_amt - $request->sale_bals[$i];
	            	}

	            	$sale_obj->save();
	            }
				
			}
			if(!empty($request->pay_amount)) {
    			AccountTransition::create([
                    'sub_account_id' => 78,
                    'transition_date' => $request->return_date,
                    'customer_id' => $request->customer_id,
                    'customer_return_id' => $return->id,
                    'status'=>'customer return',
                    'vochur_no'=>$return_no,
                    'description'=>'',
                    'is_cashbook' => 0,
                    'debit' => $request->pay_amount,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }
            $status = "success";
            DB::commit();
            return compact('status');
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
        $return = CustomerReturn::with('sales')->find($id);

        foreach($return->sales as $sale) {
        	//update customer_return_amount in sales
        	$sobj = Sale::find($sale->id);
    		$sobj->customer_return_amount = $sobj->customer_return_amount - $sale->pivot['return_amount'];
        	$sobj->save();
			
		}
		AccountTransition::where('customer_return_id',$id)->delete();
        $return->sales()->detach();

        $return->delete();

        return response(['message' => 'delete successful']);
    }

    public function getSaleReturnByCustomer($cus_id, Request $request)
    {
        $data = SaleReturn::orderBy('id', 'DESC')
                ->where('customer_id',$cus_id)
                ->where('return_type', 'credit')
                ->whereRaw('(balance_amount - total_payment_amount) > 0')
                ->get();
        // dd($data);
        return response(compact('data'), 200);
    }
}
