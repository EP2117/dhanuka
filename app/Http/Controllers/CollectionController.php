<?php

namespace App\Http\Controllers;

//use Session;
use PDF;
use App\Sale;
use Carbon\Carbon;
use App\Collection;
use App\AccountTransition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\SaleOverDueExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Report\GetReport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\SaleOutstandingExport;
use App\Exports\CreditCollectionExport;
use App\Http\Traits\AccountReport\Ledger;
use Illuminate\Validation\ValidationException;
use App\Exports\SaleCurrencyGainLossExport;
use Illuminate\Support\Facades\Session;

class CollectionController extends Controller
{
    use GetReport;
    use Ledger;
    public function index(Request $request)
    {
        // dd($request->all());
        $login_year = Session::get('loginYear');

        $data = Collection::with('customer', 'branch');

        $now = Carbon::now()->format('Y-m-d');

        if ($request->from_date == "" && $request->to_date == "" && $request->collection_no == "" && $request->branch_id == "" && $request->customer_id == "" && $request->state_id == "") {
            $data->whereDate('collection_date', $now);
        }

        if ($request->collection_no != "") {
            $data->where('collection_no',  $request->collection_no);
        }
        //for Country Head and Admin roles (can access multiple branch)
        if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            if (Auth::user()->role->id != 1) { //system can access all branches
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach ($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id', $branch_arr);
            }
        } else {
            //other roles can access only one branch
            if (Auth::user()->role->id != 1) { //system can access all branches
               /* $branch = Auth::user()->branch_id;
                $data->where('branch_id', $branch);*/
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach ($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id', $branch_arr);
            }
        }
        if ($request->branch_id != "") {
            $data->where('branch_id',  $request->branch_id);
        }
        if ($request->state_id != "") {
            $data->whereHas('customer', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }
        if ($request->from_date != '' && $request->to_date != '') {
            $data->whereBetween('collection_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $data->whereDate('collection_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $data->whereDate('collection_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('collection_date', array($login_year . '-01-01', $login_year . '-12-31'));
        }

        if ($request->customer_id != "") {
            $data->where('customer_id', $request->customer_id);
        }

        $data = $data->orderBy('id', 'DESC')->paginate(15);
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
            if ($max_id) {
                $max_id = $max_id + 1;
            } else {
                $max_id = 1;
            }
            $collection_no = "C" . str_pad($max_id, 5, "0", STR_PAD_LEFT);

            $collection->collection_no         = $collection_no;
            $collection->collection_date     = $request->collection_date;
            $collection->customer_id        = $request->customer_id;
            $collection->collect_type       = $request->collect_type;
            $collection->branch_id = $request->branch_id;

            if ($request->is_auto == true) {
                $collection->auto_payment  = 1;
                $collection->total_paid_amount  = $request->pay_amount;
                $total_paid_amount_fx  = 0;
                $collection->total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $collection->total_paid_amount_fx   = $request->pay_amount;
                    $collection->total_paid_amount = round($request->pay_amount * $request->currency_rate);
                }
            } else {
                $collection->auto_payment  = 0;
                $collection->total_paid_amount  = $request->total_pay;
                $collection->total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $collection->total_paid_amount_fx  = $request->total_pay_fx;
                }
            }
            $collection->currency_id = $request->currency_id;
            $collection->currency_rate = $request->currency_rate;
            $collection->account_group_id = $request->account_group;
            $collection->sub_account_id = $request->cash_bank_account;
            $collection->created_by = Auth::user()->id;
            //$collection->updated_by = Auth::user()->id;
            $collection->save();
            $description = "Inv " . $collection->collection_no . ",Inv Date " . $collection->collection_date . " to " . $collection->customer->cus_name;
            $sub_account_id = config('global.credit_collection');   /*sub account  id for sale collection*/
            if ($collection) {
                AccountTransition::create([
                    'sub_account_id' => $sub_account_id,
                    'account_group_id' => $request->account_group,
                    'cash_bank_sub_account_id' => $request->cash_bank_account,
                    'transition_date' => $request->collection_date,
                    'sale_id' => $collection->id,
                    'is_cashbook' => 1,
                    'description' => $description,
                    'status' => 'credit_collection',
                    'customer_id' => $collection->customer_id,
                    'vochur_no' => $collection_no,
                    'debit' => $collection->total_paid_amount,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
                $this->storeSaleCollectionInLedger($collection, $request);
            }
            for ($i = 0; $i < count($request->invoices); $i++) {
                /*** //add invoices into pivot table
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
                $sale->save();**/

                if ($request->discounts[$i] == null) {
                    $dsc = 0;
                } else {
                    $dsc = $request->discounts[$i];
                }
                //add invoices into pivot table
                if ($request->currency_id == 1) {
                    $paid_amount_fx = 0;
                    $discount_fx = 0;
                    $gain = 0;
                    $loss = 0;
                } else {
                    $paid_amount_fx = $request->payments_fx[$i] == '' ? 0 : $request->payments_fx[$i];
                    $discount_fx = $request->discounts_fx[$i] == null ? 0 : $request->discounts_fx[$i];
                    $gain = $request->gain[$i];
                    $loss = $request->loss[$i];
                }

                $paid_amt = $request->payments[$i] == '' ? 0 : $request->payments[$i];

                $pivot = $collection->sales()->attach($request->invoices[$i], ['paid_amount' => $paid_amt, 'paid_amount_fx' => $paid_amount_fx, 'discount' => $dsc, 'discount_fx' => $discount_fx, 'gain_amount' => $gain, 'loss_amount' => $loss]);


                //Get all collection amount and update collection_amount in each sale invoice
                if ($request->currency_id == 1) {
                    //for MMK
                    $collect_qry = DB::table("collection_sale")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                        ->where('sale_id', $request->invoices[$i])
                        ->groupBy('sale_id')
                        ->first();
                    //                dd($collect_qry);
                    if ($collect_qry) {
                        if ($collect_qry->total_discount == null) {
                            $collect_qry->total_discount = 0;
                        }
                        $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                    } else {
                        $collection_amount = 0;
                    }
                    $s = Sale::find($request->invoices[$i]);
                    $s->collection_amount = $collection_amount;
                    $s->save();
                } else {
                    //for foreign currency
                    $collect_qry = DB::table("collection_sale")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(paid_amount_fx)  as total_paid_fx, SUM(discount)  as total_discount, SUM(discount_fx)  as total_discount_fx"))
                        ->where('sale_id', $request->invoices[$i])
                        ->groupBy('sale_id')
                        ->first();
                    //                dd($collect_qry);
                    if ($collect_qry) {
                        if ($collect_qry->total_discount == null) {
                            $collect_qry->total_discount = 0;
                        }
                        $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                        $collection_amount_fx = $collect_qry->total_paid_fx + $collect_qry->total_discount_fx;
                    } else {
                        $collection_amount = 0;
                        $collection_amount_fx = 0;
                    }
                    $s = Sale::find($request->invoices[$i]);
                    $s->collection_amount = $collection_amount;
                    $s->collection_amount_fx = $collection_amount_fx;
                    $s->save();
                }

                //add gain/loss amount to ledger
                if ($gain != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 79,
                        'account_group_id' => $request->account_group,
                        'cash_bank_sub_account_id' => $request->cash_bank_account,
                        'transition_date' => $request->collection_date,
                        'sale_id' => $collection->id,
                        'customer_id'=>$collection->customer_id,
                        'is_cashbook' => 0,
                        'description' => 'Gain Amount',
                        'vochur_no' => $s->invoice_no,
                        //'debit' => $gain,
                        'credit' => $gain,
                        'status' => 'gain',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                if ($loss != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 80,
                        'account_group_id' => $request->account_group,
                        'cash_bank_sub_account_id' => $request->cash_bank_account,
                        'transition_date' => $request->collection_date,
                        'sale_id' => $collection->id,
                        'customer_id' => $collection->customer_id,
                        'is_cashbook' => 0,
                        'description' => 'Loss Amount',
                        'vochur_no' => $s->invoice_no,
                        //'credit' => abs($loss),
                        'debit' => abs($loss),
                        'status' => 'loss',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            $status = "success";
            $collection_id = $collection->id;
            DB::commit();

            return compact('status', 'collection_id');
        } catch (\Throwable $e) {
            DB::rollback();
            $status = $e->getMessage();
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
            $collection->collection_date     = $request->collection_date;
            $collection->collect_type       = $request->collect_type;
            if ($request->is_auto == true) {
                $collection->auto_payment  = 1;
                $collection->total_paid_amount  = $request->pay_amount;
                $total_paid_amount_fx  = 0;
                $collection->total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $collection->total_paid_amount_fx   = $request->pay_amount;
                    $collection->total_paid_amount = round($request->pay_amount * $request->currency_rate);
                }
            } else {
                $collection->auto_payment  = 0;
                $collection->total_paid_amount  = $request->total_pay;
                $collection->total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $collection->total_paid_amount_fx  = $request->total_pay_fx;
                }
            }
            $collection->currency_id = $request->currency_id;
            $collection->currency_rate = $request->currency_rate;

            $collection->branch_id = $request->branch_id;

            $collection->account_group_id = $request->account_group;
            $collection->sub_account_id = $request->cash_bank_account;

            $collection->updated_at = time();
            $collection->updated_by = Auth::user()->id;
            $collection->save();
            $sub_account_id = config('global.credit_collection');    /*sub account id for credit payment */
            $description = $collection->collection_no . ",Date " . $collection->collection_date . " to " . $collection->customer->cus_name;
            if ($collection) {
                AccountTransition::where('sale_id', $id)
                    ->where(function ($query) {
                        $query->orwhere('status', 'credit_collection')
                            ->orwhere('status', 'discount_allowed')
                            ->orwhere('sub_account_id', 79) //for loss account
                            ->orwhere('sub_account_id', 80); //for gain account
                    })->delete();
                if ($collection->total_paid_amount != 0) {

                    /**AccountTransition::where([
                        ['sale_id',$id],
                        ['is_cashbook',1],
                        ['status','credit_collection'],
                    ])->delete();**/
                    AccountTransition::create([
                        'sub_account_id' => $sub_account_id,
                        'account_group_id' => $request->account_group,
                        'cash_bank_sub_account_id' => $request->cash_bank_account,
                        'transition_date' => $request->collection_date,
                        'sale_id' => $collection->id,
                        'vochur_no' => $request->collection_no,
                        'description' => $description,
                        'status' => 'credit_collection',
                        'is_cashbook' => 1,
                        'customer_id' => $collection->customer_id,
                        'debit' => $collection->total_paid_amount,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                    // update sale collection in ledger 
                    $this->updateSaleCollectionInLedger($collection, $request);
                    // update sale collection in ledger 

                } elseif ($collection->total_paid_amount == 0) {
                    /**AccountTransition::
                    where(['sale_id'=>$id,'status'=>'sale_collection','is_cashbook'=>1])->delete();
                    AccountTransition::
                    where(['sale_id'=>$id,'status'=>'sale_collection','is_cashbook'=>0])->delete();**/
                    AccountTransition::where('sale_id', $id)
                        ->where(function ($query) {
                            $query->orwhere('status', 'credit_collection')
                                ->orwhere('status', 'discount_allowed');
                        })->delete();
                }
            }
            //update collection amount for removed sales
            foreach ($request->remove_pivot_id as $key => $val) {
                //get paid amount and discount value before delete
                $relation = DB::table('collection_sale')
                    ->select('sale_id', 'collection_id', 'paid_amount', 'paid_amount_fx', 'discount', 'discount_fx')
                    ->where('id', $val)
                    ->first();
                if ($relation->discount == NULL) {
                    $discount = 0;
                } else {
                    $discount = $relation->discount;
                }

                $discount_fx = $relation->discount_fx;

                $sale_col_amt = $relation->paid_amount + $discount;
                $sale_col_amt_fx = $relation->paid_amount_fx + $discount_fx;

                //update collection amount in sale
                $sale = Sale::find($relation->sale_id);
                $inv_no = $sale->invoice_no;
                $collection_amount = $sale->collection_amount - $sale_col_amt;
                $collection_amount_fx = $sale->collection_amount_fx - $sale_col_amt_fx;
                $sale->collection_amount = $collection_amount;
                $sale->collection_amount_fx = $collection_amount_fx;
                $sale->save();

                //remove gain/loss transition
                AccountTransition::where('sale_id', $collection->id)
                    ->where('vochur_no', $inv_no)
                    ->where(function ($query) {
                        $query->orwhere('sub_account_id', 79) //for loss account
                            ->orwhere('sub_account_id', 80); //for gain account
                    })->delete();
            }

            $collection->sales()->detach();

            for ($i = 0; $i < count($request->invoices); $i++) {

                if ($request->discounts[$i] == null) {
                    $dsc = 0;
                } else {
                    $dsc = $request->discounts[$i];
                }
                //add invoices into pivot table
                if ($request->currency_id == 1) {
                    $paid_amount_fx = 0;
                    $discount_fx = 0;
                    $gain = 0;
                    $loss = 0;
                } else {
                    $paid_amount_fx = $request->payments_fx[$i] == '' ? 0 : $request->payments_fx[$i];
                    $discount_fx = $request->discounts_fx[$i] == null ? 0 : $request->discounts_fx[$i];
                    $gain = $request->gain[$i];
                    $loss = $request->loss[$i];
                }

                $paid_amt = $request->payments[$i] == '' ? 0 : $request->payments[$i];
                //add invoices into pivot table
                $pivot = $collection->sales()->attach($request->invoices[$i], ['paid_amount' => $paid_amt, 'paid_amount_fx' => $paid_amount_fx, 'discount' => $dsc, 'discount_fx' => $discount_fx, 'gain_amount' => $gain, 'loss_amount' => $loss]);
                //$pivot = $collection->sales()->attach($request->invoices[$i],['paid_amount' => $request->payments[$i], 'discount' => $request->discounts[$i]]);

                //Get all collection amount and update collection_amount in each sale invoice
                if ($request->currency_id == 1) {
                    $collect_qry = DB::table("collection_sale")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                        ->where('sale_id', $request->invoices[$i])
                        ->groupBy('sale_id')
                        ->first();
                    if ($collect_qry) {
                        $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                    } else {
                        $collection_amount = 0;
                    }
                    $sale = Sale::find($request->invoices[$i]);
                    $sale->collection_amount = $collection_amount;
                    $sale->save();
                } else {
                    //for foreign currency
                    $collect_qry = DB::table("collection_sale")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(paid_amount_fx)  as total_paid_fx, SUM(discount)  as total_discount, SUM(discount_fx)  as total_discount_fx"))
                        ->where('sale_id', $request->invoices[$i])
                        ->groupBy('sale_id')
                        ->first();
                    //                dd($collect_qry);
                    if ($collect_qry) {
                        if ($collect_qry->total_discount == null) {
                            $collect_qry->total_discount = 0;
                        }
                        $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                        $collection_amount_fx = $collect_qry->total_paid_fx + $collect_qry->total_discount_fx;
                    } else {
                        $collection_amount = 0;
                        $collection_amount_fx = 0;
                    }
                    $sale = Sale::find($request->invoices[$i]);
                    $sale->collection_amount = $collection_amount;
                    $sale->collection_amount_fx = $collection_amount_fx;
                    $sale->save();
                }

                //add gain/loss amount to ledger
                if ($gain != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 79,
                        'account_group_id' => $request->account_group,
                        'cash_bank_sub_account_id' => $request->cash_bank_account,
                        'transition_date' => $request->collection_date,
                        'sale_id' => $collection->id,
                        'customer_id'=>$collection->customer_id,
                        'is_cashbook' => 0,
                        'description' => 'Gain Amount',
                        'vochur_no' => $sale->invoice_no,
                        //'debit' => $gain,
                        'credit' => $gain,
                        'status' => 'gain',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                if ($loss != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 80,
                        'account_group_id' => $request->account_group,
                        'cash_bank_sub_account_id' => $request->cash_bank_account,
                        'transition_date' => $request->collection_date,
                        'sale_id' => $collection->id,
                        'customer_id' => $collection->customer_id,
                        'is_cashbook' => 0,
                        'description' => 'Loss Amount',
                        'vochur_no' => $sale->invoice_no,
                        //'credit' => abs($loss),
                        'debit' => abs($loss),
                        'status' => 'loss',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            $status = "success";
            $collection_id = $collection->id;
            DB::commit();
            return compact('status', 'collection_id');
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
        $collection = Collection::with('sales', 'currency', 'customer', 'branch')->find($id);
        $customer_id = $collection->customer_id;
        $branch_id = $collection->branch_id;
        $col_sales = array();
        foreach ($collection->sales as $sale) {
            array_push($col_sales, $sale->id);

            foreach ($collection->sales as $key => $val) {
                $gain_amt = 0;
                $loss_amt = 0;
                if (!empty($val->collections)) {
                    foreach ($val->collections as $c) {
                        $gain_amt += $c->pivot->gain_amount;
                        $loss_amt += abs($c->pivot->loss_amount);
                    }
                }

                $collection->sales[$key]->gain_amount = $gain_amt;
                $collection->sales[$key]->loss_amount = $loss_amt;
            }
        }
        if ($collection->currency_id != 1) {
            $cus_invoices = Sale::orderBy('invoice_date', 'ASC')
                ->where('customer_id', $customer_id)
                ->where('branch_id', $branch_id)
                ->where('payment_type', 'credit')
                ->where('currency_id', $collection->currency_id)
                ->where(function ($query) use ($col_sales) {
                    $query->whereRaw('((total_amount_fx + IFNULL(tax_amount_fx,0))-(IFNULL(cash_discount_fx,0) + pay_amount_fx + collection_amount_fx)) > 0')
                        ->orWhereIn('id', $col_sales);
                })->get();
            /**$query->whereRaw('(total_amount_fx-(pay_amount_fx + collection_amount_fx)) > 0')
                        ->orWhereIn('id', $col_sales);
                });**/

            foreach ($cus_invoices as $key => $val) {
                $gain_amt = 0;
                $loss_amt = 0;
                if (!empty($val->collections)) {
                    foreach ($val->collections as $c) {
                        $gain_amt += $c->pivot->gain_amount;
                        $loss_amt += abs($c->pivot->loss_amount);
                    }
                }

                $cus_invoices[$key]->gain_amount = $gain_amt;
                $cus_invoices[$key]->loss_amount = $loss_amt;
            }
        } else {
            $cus_invoices = Sale::orderBy('invoice_date', 'ASC')
                ->where('customer_id', $customer_id)
                ->where('branch_id', $branch_id)
                ->where('payment_type', 'credit')
                ->where('currency_id', 1)
                ->where(function ($query) use ($col_sales) {
                    $query->whereRaw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0')
                        ->orWhereIn('id', $col_sales);
                })
                ->get();
            foreach ($cus_invoices as $key => $val) {
                $gain_amt = 0;
                $loss_amt = 0;
                if (!empty($val->collections)) {
                    foreach ($val->collections as $c) {
                        $gain_amt += $c->pivot->gain_amount;
                        $loss_amt += abs($c->pivot->loss_amount);
                    }
                }

                $cus_invoices[$key]->gain_amount = $gain_amt;
                $cus_invoices[$key]->loss_amount = $loss_amt;
            }
        }
        return compact('collection', 'cus_invoices');
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
        foreach ($collection->sales as $sale) {
            $relation = DB::table('collection_sale')
                ->select('sale_id', 'collection_id', 'paid_amount', 'paid_amount_fx', 'discount', 'discount_fx')
                ->where('id', $sale->pivot->id)
                ->first();
            if ($relation->discount == NULL) {
                $discount = 0;
            } else {
                $discount = $relation->discount;
            }

            $discount_fx = $relation->discount_fx;

            $sale_col_amt = $relation->paid_amount + $discount;
            $sale_col_amt_fx = $relation->paid_amount_fx + $discount_fx;

            //update collection amount in sale
            $sale = Sale::find($relation->sale_id);
            $collection_amount = $sale->collection_amount - $sale_col_amt;
            $collection_amount_fx = $sale->collection_amount_fx - $sale_col_amt_fx;
            $sale->collection_amount = $collection_amount;
            $sale->collection_amount_fx = $collection_amount_fx;
            AccountTransition::where('sale_id', $id)->where('sub_account_id', 7)->delete();
            $sale->save();
        }

        $collection->sales()->detach();

        $collection->delete();
        /**AccountTransition::where('sale_id',$id)
            ->where('status','credit_collection')
            ->delete();**/
        AccountTransition::where('sale_id', $id)
            ->where(function ($query) {
                $query->orwhere('status', 'credit_collection')
                    ->orwhere('status', 'discount_allowed')
                    ->orwhere('sub_account_id', 79) //for loss account
                    ->orwhere('sub_account_id', 80); //for gain account
            })->delete();
        return response(['message' => 'delete successful']);
    }
    public function getSaleOutstanding(Request $request)
    {
        $route_name = Route::currentRouteName();
        $sale_outstandings = $this->getSaleOutstandingReport($request);
        $net_gain_loss_amt = $net_inv_amt = $net_paid_amt = $net_balance_amt = 0;
        foreach ($sale_outstandings as $po) {
            foreach ($po->out_list as $i) {
                if ($i->type == 'paid') {
                    if ($i->currency_id == 1) {
                        if ($i->is_opening == 1) {
                            $net_inv_amt += $i->total_amount;
                        } else {
                            $taxAmt = $i->tax_amount == NULL ? 0 : $i->tax_amount;
                            $net_inv_amt = $net_inv_amt + $i->net_total + $taxAmt;
                        }
                    } else {
                        $net_inv_amt = $net_inv_amt + $i->net_total_fx + $i->tax_amount_fx;
                    }
                    // $net_inv_amt+=$i->total_amount; 
                    $net_paid_amt += $i->t_paid_amount;
                    $net_balance_amt += $i->t_balance_amount;
                    $net_gain_loss_amt += $i->t_gain_loss_amount;
                }
                // dd($i);

            }
        }
        if ($route_name == 'sale_outstanding_export') {
            $export = new SaleOutstandingExport($sale_outstandings, $net_paid_amt, $net_balance_amt, $net_inv_amt, $net_gain_loss_amt, $request);
            $fileName = 'Sale Outstanding Export' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }
        // Kamlesh Start
        if ($route_name == 'sale_outstanding_export_pdf') {
            $pdf = PDF::loadView('exports.sale_outstanding_report_pdf', compact('sale_outstandings', 'net_paid_amt', 'net_balance_amt', 'net_inv_amt', 'net_gain_loss_amt', 'request'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->output();
        }
        // Kamlesh End
        return compact('sale_outstandings', 'net_paid_amt', 'net_balance_amt', 'net_inv_amt', 'net_gain_loss_amt');
    }
    public function getCreditCollectionReport(Request $request)
    {
        $route_name = Route::currentRouteName();
        $html = $this->getCreditCollection($request);
        if ($route_name == 'credit_collection_export') {
            $export = new CreditCollectionExport($html, $request);
            $fileName = 'Credit Collection Export' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }
        return response(compact('html'), 200);
        // return $credit_collection;
    }

    public function getCurrencyGainLossOld(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 240);

        $data = Collection::with('sales', 'customer', 'currency');

        if ($request->invoice_no != "") {
            $data->whereHas('sales', function ($q) use ($request) {
                $q->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
            });
        }

        $from_date = $to_date = "";
        if ($request->from_date != '' && $request->to_date != '') {
            $data->whereHas('sales', function ($q) use ($request) {
                $q->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            });
            // $data->whereBetween('landed_costings.bill_date', array($request->bill_from_date, $request->bill_to_date));
        } else if ($request->from_date != '') {
            $data->whereHas('sales', function ($q) use ($request) {
                $q->whereDate('invoice_date', '>=', $request->from_date);
            });
            //$data->whereDate('landed_costings.bill_date', '>=', $request->bill_from_date);

        } else if ($request->to_date != '') {
            $data->whereHas('sales', function ($q) use ($request) {
                $q->whereDate('invoice_date', '<=', $request->to_date);
            });
            //$data->whereDate('landed_costings.bill_date', '<=', $request->bill_to_date);
        } else {
        }

        if ($request->c_from_date != '' && $request->c_to_date != '') {
            $data->whereBetween('collection_date', array($request->c_from_date, $request->c_to_date));
        } else if ($request->c_from_date != '') {
            $data->whereDate('collection_date', '>=', $request->c_from_date);
        } else if ($request->c_to_date != '') {
            $data->whereDate('collection_date', '<=', $request->c_to_date);
        } else {
            //$data->whereBetween('collection_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if (isset($request->collection_no) && $request->collection_no != "") {
            $data->where('collection_no', 'LIKE', '%' . $request->collection_no . '%');
        }

        $data->where('currency_id', '!=', 1);
        /**if($request->supplier_id != "") {
            $data->where('landed_costings.supplier_id', $request->supplier_id);
        }**/

        /**if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            //$binds = array(strtolower($request->product_name));
            $data->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
            $data->where('products.product_name', 'LIKE', "%$request->product_name%");
        }**/

        $data    =  $data->orderBy('collection_date', 'DESC')->get();

        // $sale_arr = $data->pluck('sale_id')->toArray();

        $html = '';
        $i = 0;
        $gain = $loss = 0;
        foreach ($data as $collection) {
            $rowspan = count($collection->sales);
            foreach ($collection->sales as $k => $p) {
                $i++;
                $html .= '<tr>';
                $html .= '<td>' . $i . '</td>';
                $html .= '<td class="text-center">' . $p->invoice_no . '</td>';
                $html .= '<td class="text-center">' . $p->invoice_date . '</td>';
                $html .= '<td class="text-center">1' . $collection->currency->sign . ' = ' . floatval($p->currency_rate) . 'MMK</td>';
                if ($k == 0) {
                    $html .= '<td rowspan="' . $rowspan . '" style="text-align:center; vertical-align:middle;">' . $collection->collection_no . '</td>';
                    $html .= '<td rowspan="' . $rowspan . '" style="text-align:center; vertical-align:middle;">' . $collection->collection_date . '</td>';
                    $html .= '<td rowspan="' . $rowspan . '" style="text-align:center; vertical-align:middle;">1' . $collection->currency->sign . ' = ' . floatval($collection->currency_rate) . 'MMK</td>';
                    $html .= '<td rowspan="' . $rowspan . '" style="text-align:center; vertical-align:middle;">' . $collection->currency->name . '</td>';
                }

                $gain_amount = $p->pivot->gain_amount == 0 ? '' : floatval($p->pivot->gain_amount);
                $loss_amount = abs($p->pivot->loss_amount) == 0 ? '' : floatval(abs($p->pivot->loss_amount));
                $html .= '<td class="text-right">' . $gain_amount . '</td>';
                $html .= '<td class="text-right">' . $loss_amount . '</td>';

                $html .= '</tr>';

                $gain += abs($p->pivot->gain_amount);
                $loss += abs($p->pivot->loss_amount);
            }
        }
        $net_gain = $net_loss = 0;
        if (!empty($data)) {
            $html .= '<tr><td colspan="8" class="text-right">Total</td><td class="text-right">' . floatval($gain) . '</td><td class="text-right">' . floatval($loss) . '</td></tr>';

            if ($gain > $loss) {
                $net_gain = $gain - $loss;
            } else {
                $net_loss = $loss - $gain;
            }
            $net_gain = $net_gain == 0 ? '' : floatval($net_gain);
            $net_loss = $net_loss == 0 ? '' : floatval($net_loss);
            $html .= '<tr><td colspan="8" class="text-right">Net Total</td><td class="text-right">' . $net_gain . '</td><td class="text-right">' . $net_loss . '</td></tr>';
        }

        //return $html;
        return array($data, $html);
    }


    public function getCurrencyGainLoss(Request $request)
    {
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 240);
        //$data = PurchaseCollection::with('purchases','supplier','currency');
        $data = DB::table("account_transitions")
                ->select(DB::raw("account_transitions.*,sales.invoice_no,sales.invoice_date,sc.sale_invoice_no,sc.sale_invoice_date,sc.sale_currency_rate,s_col_currency.sign as sale_currency_sign,sc.collection_date,sc.collection_no,customers.cus_name as customer_name,sales.currency_rate as inv_currency_rate,inv_currency.sign as inv_currency_sign,inv_currency.name as inv_currency_name,adv_currency.sign as adv_currency_sign,adv_currency.name as adv_currency_name,col_currency.name as col_currency_name,col_currency.sign as col_currency_sign,sale_advances.currency_rate as adv_currency_rate,sc.currency_rate as col_currency_rate,sale_advances.advance_no,sale_advances.advance_date"))
                
                ->leftjoin('sales', 'sales.id', '=', 'account_transitions.sale_id')
                //->leftjoin('collection_purchase', 'collection_purchase.purchase_collection_id', '=', 'account_transitions.purchase_id')
                //->leftjoin('credit_purchase_collections', 'credit_purchase_collections.id', '=', 'account_transitions.purchase_id')
                ->leftjoin(DB::raw("(SELECT account_transitions.id as transition_id, collections.*,sales.invoice_no as sale_invoice_no, sales.invoice_date as sale_invoice_date, sales.currency_id as sale_currency_id, sales.currency_rate as sale_currency_rate FROM `account_transitions` JOIN collection_sale ON collection_sale.collection_id = account_transitions.sale_id AND ABS(collection_sale.gain_amount) = ABS(coalesce(account_transitions.credit,0.000)) AND ABS(collection_sale.loss_amount) = ABS(coalesce(account_transitions.debit,0.000)) LEFT JOIN collections ON collections.id = account_transitions.sale_id LEFT JOIN sales ON sales.id = collection_sale.sale_id) as sc"),function($join){

                            $join->on("sc.transition_id","=","account_transitions.id");

                        })
                ->leftjoin('sale_advances', 'sale_advances.id', '=', 'account_transitions.sale_advance_id')
                ->leftjoin('customers', 'customers.id', '=', 'account_transitions.customer_id')
                ->leftjoin('currencies as inv_currency', 'inv_currency.id', '=', 'sale_advances.currency_id')
                ->leftjoin('currencies as adv_currency', 'adv_currency.id', '=', 'sale_advances.currency_id')
                ->leftjoin('currencies as col_currency', 'col_currency.id', '=', 'sc.currency_id')
                ->leftjoin('currencies as s_col_currency', 's_col_currency.id', '=', 'sc.sale_currency_id');

        $data = $data->where(function($query) {
                        $query->where('account_transitions.status','gain')
                              ->orWhere('account_transitions.status','loss');
                    });
        $data = $data->whereNotNull('account_transitions.sale_id');

        if($request->invoice_no != "") {
            // $data->where('invoice_no', 'LIKE','%'.$request->invoice_no.'%');
            $data->where(function($query) use($request) {
                        $query->where('invoice_no', 'LIKE','%'.$request->invoice_no.'%')
                              ->orWhere('sale_invoice_no', 'LIKE','%'.$request->invoice_no.'%');
                    });
        }
        $from_date=$to_date="";
        if($request->from_date != '' && $request->to_date != '')
        {
            //$data->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            $data->where(function($query) use($request) {
                        $query->whereBetween('invoice_date', array($request->from_date, $request->to_date))
                              ->orWhereBetween('sale_invoice_date', array($request->from_date, $request->to_date));
                    });

        } else if($request->from_date != '') {
            //$data->whereDate('invoice_date', '>=', $request->from_date);
            $data->where(function($query) use($request) {
                        $query->whereDate('invoice_date', '>=', $request->from_date)
                              ->orWhereDate('sale_invoice_date', '>=', $request->from_date);
                    });

        }else if($request->to_date != '') {
            //$data->whereDate('invoice_date', '<=', $request->to_date);
            $data->where(function($query) use($request) {
                        $query->whereDate('invoice_date', '<=', $request->to_date)
                              ->orWhereDate('sale_invoice_date', '<=', $request->to_date);
                    });
        } else {}

        if($request->c_from_date != '' && $request->c_to_date != '')
        {            
            $data->whereBetween('collection_date', array($request->c_from_date, $request->c_to_date));
        } else if($request->c_from_date != '') {
            $data->whereDate('collection_date', '>=', $request->c_from_date);

        }else if($request->c_to_date != '') {
             $data->whereDate('collection_date', '<=', $request->c_to_date);
        } else {
            //$data->whereBetween('collection_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if(isset($request->collection_no) && $request->collection_no != "") {
            $data->where('collection_no','LIKE','%'.$request->collection_no.'%');
        }
        /**if($request->supplier_id != "") {
            $data->where('landed_costings.supplier_id', $request->supplier_id);
        }**/

        /**if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            //$binds = array(strtolower($request->product_name));
            $data->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
            $data->where('products.product_name', 'LIKE', "%$request->product_name%");
        }**/

        $data    =  $data->orderBy('transition_date', 'DESC')->get();
        //dd($data);
       // $sale_arr = $data->pluck('sale_id')->toArray();

        $html = ''; $i=0;$gain =$loss=0;
        foreach($data as $p) {
            $i++;
            $html .= '<tr>';
            $html .= '<td>'.$i.'</td>';
            $html .= '<td class="text-center">'.$p->advance_no.'</td>';
            $html .= '<td class="text-center">'.$p->advance_date.'</td>';
            if(!empty($p->advance_no)) {
                $html .= '<td class="text-center">1'.$p->adv_currency_sign.' = '.floatval($p->adv_currency_rate).'MMK</td>'; 
            }else {
                $html .= '<td class="text-center"></td>'; 
            }           
            if(empty($p->advance_no)) {
                $html .= '<td class="text-center">'.$p->sale_invoice_no.'</td>';
                $html .= '<td class="text-center">'.$p->sale_invoice_date.'</td>';
                $html .= '<td class="text-center">1'.$p->sale_currency_sign.' = '.floatval($p->sale_currency_rate).'MMK</td>';
            }
            else {
                $html .= '<td class="text-center">'.$p->invoice_no.'</td>';
                $html .= '<td class="text-center">'.$p->invoice_date.'</td>';
                $html .= '<td class="text-center">1'.$p->inv_currency_sign.' = '.floatval($p->inv_currency_rate).'MMK</td>';
            }

            $html .= '<td style="text-align:center;">'.$p->collection_no.'</td>';
            $html .= '<td style="text-align:center; ">'.$p->collection_date.'</td>';
            if(empty($p->advance_no)) {
                $html .= '<td style="text-align:center;">1'.$p->col_currency_sign.' = '.floatval($p->col_currency_rate).'MMK</td>';
            } else {
                $html .= '<td></td>';
            }
            $html .= '<td style="text-align:center;">'.$p->col_currency_name.'</td>';

            $gain_amount = empty($p->credit) ? '' : floatval(abs($p->credit));
            $loss_amount = empty($p->debit) ? '' : floatval(abs($p->debit));
            $html .= '<td class="text-right">'.$gain_amount .'</td>';
            $html .= '<td class="text-right">'.$loss_amount .'</td>';

            $html .= '</tr>';
            $credit = !empty($p->credit) ? $p->credit : 0;
            $debit = !empty($p->debit) ? $p->debit : 0;
            $gain += abs($credit);
            $loss += abs($debit);
        } 
        $net_gain = $net_loss = 0;
        if(!empty($data)) {
            $html .= '<tr><td colspan="11" class="text-right">Total</td><td class="text-right">'.floatval($gain) .'</td><td class="text-right">'.floatval($loss) .'</td></tr>';

            if($gain > $loss) {
                $net_gain = $gain - $loss;
            } else {
                $net_loss = $loss - $gain;
            }
            $net_gain = $net_gain == 0 ? '' : floatval($net_gain);
            $net_loss = $net_loss == 0 ? '' : floatval($net_loss);
            $html .= '<tr><td colspan="11" class="text-right">Net Total</td><td class="text-right">'.$net_gain.'</td><td class="text-right">'.$net_loss.'</td></tr>';

        }

        //return $html;
        return array($data,$html);
    }

    public function getCurrencyGainLossReport(Request $request)
    {
        list($data, $html) = $this->getCurrencyGainLoss($request);
        return response(compact('html'), 200);
    }

    public function exportCurrencyGainLossReport(Request $request)
    {
        list($data, $html) = $this->getCurrencyGainLoss($request);
        $export = new SaleCurrencyGainLossExport($data, $request);
        $fileName = 'sale_currency_gain_loss_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function getSaleOverDue(Request $request){
        $route_name=Route::currentRouteName();
        $sale_over_due=$this->saleOverDue($request);
        $net_inv_amt=$net_paid_amt=$net_balance_amt=0;
        foreach($sale_over_due as $po){
            foreach($po->out_list as $i){
                if($i->type=='paid'){
                    $net_inv_amt+=$i->total_amount; 
                    $net_paid_amt+=$i->t_paid_amount;
                    $net_balance_amt+=$i->t_balance_amount;
                }
            }
        }
        if($route_name=='sale_over_due_export'){
            $export=new SaleOverDueExport($sale_over_due,$net_paid_amt,$net_balance_amt,$net_inv_amt,$request);
            $fileName = 'Sale Over Due Export'.Carbon::now()->format('Ymd').'.xlsx';
            return Excel::download($export, $fileName);
        }
        return compact('sale_over_due','net_paid_amt','net_balance_amt','net_inv_amt');
    }
}
