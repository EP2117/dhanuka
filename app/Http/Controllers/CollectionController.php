<?php

namespace App\Http\Controllers;

use Session;
use App\Sale;
use Carbon\Carbon;
use App\Collection;
use App\AccountTransition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Report\GetReport;
use App\Http\Traits\AccountReport\Ledger;
use Illuminate\Validation\ValidationException;

class CollectionController extends Controller
{
    use GetReport;
    use Ledger;
	public function index(Request $request)
    {
        // dd($request->all());
        $login_year = Session::get('loginYear');

        $data = Collection::with('customer','branch');

        if($request->collection_no != "") {
            $data->where('collection_no',  $request->collection_no);
        }
     //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id',$branch_arr);
            }
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $data->where('branch_id',$branch);
            }
        }
        if($request->branch_id != "") {
            $data->where('branch_id',  $request->branch_id);
        }
        if($request->state_id != "") {
            $data->whereHas('customer',function($q)use($request){
                $q->where('state_id',$request->state_id);
            });   
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('collection_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('collection_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $data->whereDate('collection_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('collection_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if($request->customer_id != "") {
            $data->where('customer_id', $request->customer_id);
        }

        $data = $data->orderBy('id', 'DESC')->get();
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
        // dd($request->all());
        DB::beginTransaction();

        try {
            $collection = new Collection;

            //auto generate collection no;
            $max_id = Collection::max('id');
            if($max_id) {
                $max_id = $max_id + 1;
            } else {
                $max_id = 1;
            }
            $collection_no = "C".str_pad($max_id,5,"0",STR_PAD_LEFT);

            $collection->collection_no 		= $collection_no;
            $collection->collection_date 	= $request->collection_date;
            $collection->customer_id		= $request->customer_id;
            $collection->collect_type       = $request->collect_type;
            $collection->branch_id = $request->branch_id;
            if($request->is_auto == true) {
                $collection->auto_payment	= 1;
                $collection->total_paid_amount	= $request->pay_amount;
            } else {
                $collection->auto_payment	= 0;
                $collection->total_paid_amount	= $request->total_pay;
            }

            $collection->created_by = Auth::user()->id;
            //$collection->updated_by = Auth::user()->id;
            $collection->save();
            $description="Inv ".$collection->collection_no.",Inv Date ".$collection->collection_date." to " .$collection->customer->cus_name;
            $sub_account_id=config('global.credit_collection');   /*sub account  id for sale collection*/
            if($collection){
                AccountTransition::create([
                    'sub_account_id' => $sub_account_id,
                    'transition_date' => $request->collection_date,
                    'sale_id' => $collection->id,
                    'is_cashbook' => 1,
                    'description'=>$description,
                    'status'=>'credit_collection',
                    'customer_id'=>$collection->customer_id,
                    'vochur_no'=>$collection_no,
                    'debit' => $collection->total_paid_amount,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
                $this->storeSaleCollectionInLedger($collection,$request);
            }
            for($i=0; $i<count($request->invoices); $i++) {
                //add invoices into pivot table
                $pivot = $collection->sales()->attach($request->invoices[$i],['paid_amount' => $request->payments[$i], 'discount' => $request->discounts[$i]]);

                //Get all collection amount and update collection_amount in each sale invoice

                $collect_qry = DB::table("collection_sale")
                                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                                        ->where('sale_id', $request->invoices[$i])
                                        ->groupBy('sale_id')
                                        ->first();
                if($collect_qry) {
                    $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                } else {
                    $collection_amount = 0;
                }
                $sale = Sale::find($request->invoices[$i]);
                $sale->collection_amount = $collection_amount;
                $sale->save();
            }

            $status = "success";
            $collection_id = $collection->id;
            DB::commit();

            return compact('status','collection_id');
        } catch (\Throwable $e) {
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
        }
       

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
        DB::beginTransaction();
        try {
            $collection = Collection::find($id);
            $collection->collection_date 	= $request->collection_date;
            $collection->collect_type       = $request->collect_type;
            if($request->is_auto == true) {
                $collection->auto_payment	= 1;
                $collection->total_paid_amount	= $request->pay_amount;
            } else {
                $collection->auto_payment	= 0;
                $collection->total_paid_amount	= $request->total_pay;
            }

            $collection->branch_id = $request->branch_id;

            $collection->updated_at = time();
            $collection->updated_by = Auth::user()->id;
            $collection->save();
            $sub_account_id=config('global.credit_collection');    /*sub account id for credit payment */
            $description=$collection->collection_no.",Date ".$collection->collection_date." to " .$collection->customer->cus_name;
            if($collection){
                if($collection->total_paid_amount!=0){
                    AccountTransition::where([
                        ['sale_id',$id],
                        ['is_cashbook',1],
                        ['status','credit_collection'],
                    ])->delete();
                        AccountTransition::create([
                        'sub_account_id' => $sub_account_id,
                        'transition_date' => $request->collection_date,
                        'sale_id' => $collection->id,
                        'vochur_no'=>$request->collection_no,
                        'description'=>$description,
                        'is_cashbook' => 1,
                        'customer_id'=>$collection->customer_id,
                        'debit' => $collection->total_paid_amount,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                    // update sale collection in ledger 
                    $this->updateSaleCollectionInLedger($collection,$request);
                    // update sale collection in ledger 

                }elseif($collection->total_paid_amount==0){
                    AccountTransition::
                    where(['sale_id'=>$id,'status'=>'sale_collection','is_cashbook'=>1])->delete();
                    AccountTransition::
                    where(['sale_id'=>$id,'status'=>'sale_collection','is_cashbook'=>0])->delete();
                }
            }
            //update collection amount for removed sales
            foreach($request->remove_pivot_id as $key => $val) {
                //get paid amount and discount value before delete
                $relation = DB::table('collection_sale')
                                ->select('sale_id','collection_id','paid_amount','discount')
                                ->where('id',$val)
                                ->first();
                if($relation->discount == NULL) {
                    $discount = 0;
                } else {
                    $discount = $relation->discount;
                }

                $sale_col_amt = $relation->paid_amount + $discount;

                //update collection amount in sale
                $sale = Sale::find($relation->sale_id);
                $collection_amount = $sale->collection_amount - $sale_col_amt;
                $sale->collection_amount = $collection_amount;
                $sale->save();
            }

            $collection->sales()->detach();

            for($i=0; $i<count($request->invoices); $i++) {
                //add invoices into pivot table
                $pivot = $collection->sales()->attach($request->invoices[$i],['paid_amount' => $request->payments[$i], 'discount' => $request->discounts[$i]]);

                //Get all collection amount and update collection_amount in each sale invoice
                $collect_qry = DB::table("collection_sale")
                                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                                        ->where('sale_id', $request->invoices[$i])
                                        ->groupBy('sale_id')
                                        ->first();
                if($collect_qry) {
                    $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                } else {
                    $collection_amount = 0;
                }
                $sale = Sale::find($request->invoices[$i]);
                $sale->collection_amount = $collection_amount;
                $sale->save();

            }

            $status = "success";
            $collection_id = $collection->id;
            return compact('status','collection_id');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
        }
       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $collection = Collection::with('sales','customer','branch')->find($id);
        $customer_id = $collection->customer_id;
        $branch_id = $collection->branch_id;
        $col_sales = array();
        foreach($collection->sales as $sale) {
        	array_push($col_sales, $sale->id);
        }

        $cus_invoices = Sale::orderBy('invoice_date', 'ASC')
                ->where('customer_id',$customer_id)
                ->where('branch_id', $branch_id)
                ->where('payment_type', 'credit')
                ->where(function ($query) use ($col_sales){
	    				$query->whereRaw('(total_amount-(pay_amount + collection_amount)) > 0')
	          				  ->orWhereIn('id', $col_sales);
						})
        		->get();
        return compact('collection','cus_invoices');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $collection = Collection::with('sales')->find($id);
        foreach($collection->sales as $sale) {
        	$relation = DB::table('collection_sale')
                            ->select('sale_id','collection_id','paid_amount','discount')
                            ->where('id',$sale->pivot->id)
                            ->first();
            if($relation->discount == NULL) {
            	$discount = 0;
            } else {
            	$discount = $relation->discount;
            }

            $sale_col_amt = $relation->paid_amount + $discount;

            //update collection amount in sale
            $sale = Sale::find($relation->sale_id);
            $collection_amount = $sale->collection_amount - $sale_col_amt;
        	$sale->collection_amount = $collection_amount;
            AccountTransition::where('sale_id',$id)->where('sub_account_id',7)->delete();
            $sale->save();
        }

        $collection->sales()->detach();

        $collection->delete();
        AccountTransition::where('sale_id',$id)
            ->where('status','credit_collection')
            ->delete();
        return response(['message' => 'delete successful']);
    }
    public function getSaleOutstanding(Request $request){
        $sale_outstandings=$this->getSaleOutstandingReport($request);
        $net_inv_amt=$net_paid_amt=$net_balance_amt=0;
        foreach($sale_outstandings as $po){
            foreach($po->out_list as $i){
                if($i->type=='paid'){
                    $net_inv_amt+=$i->total_amount; 
                    $net_paid_amt+=$i->t_paid_amount;
                    $net_balance_amt+=$i->t_balance_amount;
                }
                // dd($i);
              
            }
        }
        return compact('sale_outstandings','net_paid_amt','net_balance_amt','net_inv_amt');
    }
    public function getCreditCollectionReport(Request $request){
        $html=$this->getCreditCollection($request);
        return response(compact('html'), 200);
        // return $credit_collection;
    }
}
