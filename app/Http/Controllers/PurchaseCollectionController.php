<?php

namespace App\Http\Controllers;

use PDF;
use App\AccountTransition;
use App\Http\Traits\AccountReport\Ledger;
use App\Http\Traits\Report\GetReport;
use App\Exports\PurchaseOutstandingExport;
use App\Exports\PurchaseOverDueExport;
use App\PurchaseCollection;
use App\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\CreditPaymentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Exports\CurrencyGainLossExport;

class PurchaseCollectionController extends Controller
{
    use GetReport;
    use Ledger;
    public function getPurchaseCollection(Request  $request)
    {
        $login_year = Session::get('loginYear');
        $data = PurchaseCollection::with('branch');
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
                $branch = Auth::user()->branch_id;
                $data->where('branch_id', $branch);
            }
        }

        if ($request->branch_id != "") {
            $data->where('branch_id',  $request->branch_id);
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
        if ($request->supplier_id != "") {
            $data->where('supplier_id', $request->supplier_id);
        }

        $data = $data->orderBy('id', 'DESC')->get();
        //        dd($data);
        return response(compact('data'), 200);
    }
    public function getSupplierCreditPurchase($supplier_id, Request $request)
    {
        if ($request->branch_id && $request->branch_id != '') {
            if (isset($request->currency_id) && $request->currency_id != 1) {
                $data = PurchaseInvoice::with('payments')->orderBy('invoice_date', 'ASC')
                    ->where('supplier_id', $supplier_id)
                    ->where('payment_type', 'credit')
                    ->where('branch_id', $request->branch_id)
                    ->where('currency_id', $request->currency_id)
                    ->whereRaw('(total_amount_fx-(discount_fx+pay_amount_fx+collection_amount_fx)) > 0');
            } else {
                $data = PurchaseInvoice::with('payments')->orderBy('invoice_date', 'ASC')
                    ->where('supplier_id', $supplier_id)
                    ->where('payment_type', 'credit')
                    ->where('branch_id', $request->branch_id)
                    ->where('currency_id', 1)
                    ->whereRaw('(total_amount-(discount+pay_amount+collection_amount)) > 0');
            }
            /**if(isset($request->currency_id) && $request->currency_id != '') {
                $data->where('currency_id',$request->currency_id);
            }**/
            $data = $data->get();

            foreach ($data as $key => $val) {
                $gain_amt = 0;
                $loss_amt = 0;
                if (!empty($val->payments)) {
                    foreach ($val->payments as $c) {
                        $gain_amt += $c->pivot->gain_amount;
                        $loss_amt += abs($c->pivot->loss_amount);
                    }
                }

                $data[$key]->gain_amount = $gain_amt;
                $data[$key]->loss_amount = $loss_amt;
            }
        } else {
            $data = '';
        }
        //        dd($data);
        return response(compact('data'), 200);
    }
    public function store(Request  $request)
    {
        //    dd($request->all());
        //auto generate collection no;
        DB::beginTransaction();
        try {
            $max_id = PurchaseCollection::max('id');
            if ($max_id) {
                $max_id = $max_id + 1;
            } else {
                $max_id = 1;
            }
            $collection_no = "P" . str_pad($max_id, 5, "0", STR_PAD_LEFT);
            if ($request->is_auto == true) {
                $auto_payment    = 1;
                $total_paid_amount    = $request->pay_amount;
                $total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $total_paid_amount_fx  = $request->pay_amount;
                    $total_paid_amount = round($request->pay_amount * $request->currency_rate);
                }
            } else {
                $auto_payment    = 0;
                $total_paid_amount    = $request->total_pay;
                $total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $total_paid_amount_fx  = $request->total_pay_fx;
                }
            }
            $p_collection = PurchaseCollection::create([
                'supplier_id' => $request->supplier_id,
                'branch_id' => $request->branch_id,
                'collection_no' => $collection_no,
                'collection_date' => $request->collection_date,
                'auto_payment' => $auto_payment,
                'currency_id' => $request->currency_id,
                'currency_rate' => $request->currency_rate,
                'total_paid_amount' => $total_paid_amount,
                'total_paid_amount_fx' => $total_paid_amount_fx,
                'created_by' => Auth::user()->id,
            ]);
            $description = $p_collection->collection_no . ",Date " . $p_collection->collection_date . " by " . $p_collection->supplier->name;

            $sub_account_id = config('global.credit_payment');    /*sub account id for credit payment */
            if ($p_collection) {
                AccountTransition::create([
                    'sub_account_id' => $sub_account_id,
                    'transition_date' => $request->collection_date,
                    'purchase_id' => $p_collection->id,
                    'supplier_id' => $p_collection->supplier_id,
                    'is_cashbook' => 1,
                    'description' => $description,
                    'vochur_no' => $p_collection->collection_no,
                    'credit' => $total_paid_amount,
                    'status' => 'credit_payment',
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
                $this->storeCreditPaymentInLedger($p_collection, $request);
            }
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

                $pivot = $p_collection->purchases()->attach($request->invoices[$i], ['paid_amount' => $paid_amt, 'paid_amount_fx' => $paid_amount_fx, 'discount' => $dsc, 'discount_fx' => $discount_fx, 'gain_amount' => $gain, 'loss_amount' => $loss]);


                //Get all collection amount and update collection_amount in each sale invoice
                if ($request->currency_id == 1) {
                    //for MMK
                    $collect_qry = DB::table("collection_purchase")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                        ->where('purchase_id', $request->invoices[$i])
                        ->groupBy('purchase_id')
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
                    $pu = PurchaseInvoice::find($request->invoices[$i]);
                    $pu->collection_amount = $collection_amount;
                    $pu->save();
                } else {
                    //for foreign currency
                    $collect_qry = DB::table("collection_purchase")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(paid_amount_fx)  as total_paid_fx, SUM(discount)  as total_discount, SUM(discount_fx)  as total_discount_fx"))
                        ->where('purchase_id', $request->invoices[$i])
                        ->groupBy('purchase_id')
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
                    $pu = PurchaseInvoice::find($request->invoices[$i]);
                    $pu->collection_amount = $collection_amount;
                    $pu->collection_amount_fx = $collection_amount_fx;
                    $pu->save();
                }

                //add gain/loss amount to ledger
                if ($gain != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 79,
                        'transition_date' => $request->collection_date,
                        'purchase_id' => $p_collection->id,
                        'supplier_id' => $p_collection->supplier_id,
                        'is_cashbook' => 0,
                        'description' => 'Gain Amount',
                        'vochur_no' => $pu->invoice_no,
                        'credit' => $gain,
                        'status' => 'gain',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                if ($loss != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 80,
                        'transition_date' => $request->collection_date,
                        'purchase_id' => $p_collection->id,
                        'supplier_id' => $p_collection->supplier_id,
                        'is_cashbook' => 0,
                        'description' => 'Loss Amount',
                        'vochur_no' => $pu->invoice_no,
                        'debit' => abs($loss),
                        'status' => 'loss',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            $status = "success";
            $collection_id = $p_collection->id;
            DB::commit();
            return compact('status', 'collection_id');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            $status = $e->getMessage();
            return compact('status');
            // something went wrong
        }
    }
    public function edit(Request  $request, $c_id)
    {
        $collection = PurchaseCollection::with('purchases', 'currency', 'supplier', 'branch')->find($c_id);
        $supplier_id = $collection->supplier_id;
        $branch_id = $collection->branch_id;
        $col_sales = array();
        //            dd($collection->purchases);
        foreach ($collection->purchases as $p) {
            array_push($col_sales, $p->id);

            foreach ($collection->purchases as $key => $val) {
                $gain_amt = 0;
                $loss_amt = 0;
                if (!empty($val->payments)) {
                    foreach ($val->payments as $c) {
                        $gain_amt += $c->pivot->gain_amount;
                        $loss_amt += abs($c->pivot->loss_amount);
                    }
                }

                $collection->purchases[$key]->gain_amount = $gain_amt;
                $collection->purchases[$key]->loss_amount = $loss_amt;
            }
        }
        //            dd($col_sales);

        if ($collection->currency_id != 1) {
            $sup_invoices = PurchaseInvoice::orderBy('invoice_date', 'ASC')
                ->where('supplier_id', $supplier_id)
                ->where('branch_id', $branch_id)
                ->where('payment_type', 'credit')
                ->where('currency_id', $collection->currency_id)
                ->where(function ($query) use ($col_sales) {
                    $query->whereRaw('(total_amount_fx-(pay_amount_fx + collection_amount_fx)) > 0')
                        ->orWhereIn('id', $col_sales);
                });
        } else {
            $sup_invoices = PurchaseInvoice::orderBy('invoice_date', 'ASC')
                ->where('supplier_id', $supplier_id)
                ->where('branch_id', $branch_id)
                ->where('payment_type', 'credit')
                ->where('currency_id', 1)
                ->where(function ($query) use ($col_sales) {
                    $query->whereRaw('(total_amount-(pay_amount + collection_amount)) > 0')
                        ->orWhereIn('id', $col_sales);
                });
        }

        $sup_invoices = $sup_invoices->get();

        foreach ($sup_invoices as $key => $val) {
            $gain_amt = 0;
            $loss_amt = 0;
            if (!empty($val->payments)) {
                foreach ($val->payments as $c) {
                    $gain_amt += $c->pivot->gain_amount;
                    $loss_amt += abs($c->pivot->loss_amount);
                }
            }

            $sup_invoices[$key]->gain_amount = $gain_amt;
            $sup_invoices[$key]->loss_amount = $loss_amt;
        }
        //            dd($cus_invoices);
        return compact('collection', 'sup_invoices');
    }
    public function update(Request  $request, $c_id)
    {
        DB::beginTransaction();
        try {

            $collection = PurchaseCollection::find($c_id);
            $collection->collection_date     = $request->collection_date;

            if ($request->is_auto == true) {
                $collection->auto_payment    = 1;

                $total_paid_amount  = $request->pay_amount;
                $total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $total_paid_amount_fx  = $request->pay_amount;
                    $total_paid_amount = round($request->pay_amount * $request->currency_rate);
                }

                $collection->total_paid_amount    = $total_paid_amount;
                $collection->total_paid_amount_fx  = $total_paid_amount_fx;
            } else {
                $collection->auto_payment    = 0;

                $total_paid_amount  = $request->total_pay;
                $total_paid_amount_fx  = 0;
                if ($request->currency_id != 1) {
                    $total_paid_amount_fx  = $request->total_pay_fx;
                }

                $collection->total_paid_amount    = $total_paid_amount;
                $collection->total_paid_amount_fx = $total_paid_amount_fx;
            }
            $collection->branch_id = $request->branch_id;
            $collection->currency_id = $request->currency_id;
            $collection->currency_rate = $request->currency_rate;
            $collection->updated_at = time();
            $collection->updated_by = Auth::user()->id;
            $collection->save();
            $sub_account_id = config('global.credit_payment');    /*sub account id for credit payment */
            $description = $collection->collection_no . ", Date " . $collection->collection_date . " to " . $collection->supplier->name;
            if ($collection) {
                AccountTransition::where('purchase_id', $c_id)
                    ->where(function ($query) {
                        $query->orwhere('sub_account_id', 7)
                            ->orwhere('sub_account_id', 15)
                            ->orwhere('sub_account_id', 79) //for loss account
                            ->orwhere('sub_account_id', 80); //for gain account
                    })->delete();
                if ($collection->total_paid_amount != 0) {
                    /**AccountTransition::where([
                            ['purchase_id',$c_id],
                            ['is_cashbook',0],
                            ['status','credit_payment']])->update([
                            'sub_account_id' => $sub_account_id,
                            'transition_date' => $request->collection_date, 
                            'purchase_id' => $collection->id,
                            'vochur_no'=>$collection->collection_no,
                            'supplier_id'=>$collection->supplier,
                            'is_cashbook' => 1,
                            'description'=>$description,
                            'credit' => $collection->total_paid_amount,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ]);**/
                    AccountTransition::create([
                        'sub_account_id' => $sub_account_id,
                        'transition_date' => $request->collection_date,
                        'purchase_id' => $collection->id,
                        'supplier_id' => $collection->supplier_id,
                        'is_cashbook' => 1,
                        'description' => $description,
                        'vochur_no' => $collection->collection_no,
                        'credit' => $collection->total_paid_amount,
                        'status' => 'credit_payment',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                } elseif ($collection->total_paid_amount == 0) {
                    //AccountTransition::where('purchase_id',$c_id)->delete();
                    AccountTransition::where('purchase_id', $c_id)
                        ->where(function ($query) {
                            $query->orWhere('sub_account_id', 7)
                                ->orWhere('sub_account_id', 15);
                        })->delete();
                }
                $this->updateCreditPaymentInLedger($collection, $request);
            }

            //update collection amount for removed sales
            foreach ($request->remove_pivot_id as $key => $val) {
                //get paid amount and discount value before delete
                $relation = DB::table('collection_purchase')
                    ->select('purchase_id', 'purchase_collection_id', 'paid_amount', 'paid_amount_fx', 'discount', 'discount_fx')
                    ->where('id', $val)
                    ->first();
                if ($relation->discount == NULL) {
                    $discount = 0;
                } else {
                    $discount = $relation->discount;
                }
                $discount_fx = $relation->discount_fx;
                $cm = $relation->paid_amount + $discount;
                $cm_fx = $relation->paid_amount_fx + $discount_fx;

                //update collection amount in sale
                $p = PurchaseInvoice::find($relation->purchase_id);
                $inv_no = $p->invoice_no;
                $collection_amount = $p->collection_amount - $cm;
                $collection_amount_fx = $p->collection_amount_fx - $cm_fx;
                $p->collection_amount = $collection_amount;
                $p->collection_amount_fx = $collection_amount_fx;
                $p->save();

                //remove gain/loss transition
                AccountTransition::where('purchase_id', $c_id)
                    ->where('vochur_no', $inv_no)
                    ->where(function ($query) {
                        $query->orwhere('sub_account_id', 79) //for loss account
                            ->orwhere('sub_account_id', 80); //for gain account
                    })->delete();
            }

            $collection->purchases()->detach();

            for ($i = 0; $i < count($request->invoices); $i++) {
                //add invoices into pivot table
                if ($request->discounts[$i] == null) {
                    $dsc = 0;
                } else {
                    $dsc = $request->discounts[$i];
                }

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
                $pivot = $collection->purchases()->attach($request->invoices[$i], ['paid_amount' => $paid_amt, 'paid_amount_fx' => $paid_amount_fx, 'discount' => $dsc, 'discount_fx' => $discount_fx, 'gain_amount' => $gain, 'loss_amount' => $loss]);

                // $pivot = $collection->purchases()->attach($request->invoices[$i],['paid_amount' => $request->payments[$i], 'discount' => $request->discounts[$i]]);

                //Get all collection amount and update collection_amount in each sale invoice
                /**$collect_qry = DB::table("collection_purchase")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                        ->where('purchase_id', $request->invoices[$i])
                        ->groupBy('purchase_id')
                        ->first();
                    if($collect_qry) {
                        $collection_amount = $collect_qry->total_paid + $collect_qry->total_discount;
                    } else {
                        $collection_amount = 0;
                    }
                    $p = PurchaseInvoice::find($request->invoices[$i]);
                    $p->collection_amount = $collection_amount;
                    $p->save();**/

                if ($request->currency_id == 1) {
                    //for MMK
                    $collect_qry = DB::table("collection_purchase")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(discount)  as total_discount"))
                        ->where('purchase_id', $request->invoices[$i])
                        ->groupBy('purchase_id')
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
                    $pu = PurchaseInvoice::find($request->invoices[$i]);
                    $pu->collection_amount = $collection_amount;
                    $pu->save();
                } else {
                    //for foreign currency
                    $collect_qry = DB::table("collection_purchase")
                        ->select(DB::raw("SUM(paid_amount)  as total_paid, SUM(paid_amount_fx)  as total_paid_fx, SUM(discount)  as total_discount, SUM(discount_fx)  as total_discount_fx"))
                        ->where('purchase_id', $request->invoices[$i])
                        ->groupBy('purchase_id')
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
                    $pu = PurchaseInvoice::find($request->invoices[$i]);
                    $pu->collection_amount = $collection_amount;
                    $pu->collection_amount_fx = $collection_amount_fx;
                    $pu->save();
                }

                //add gain/loss amount to ledger
                if ($gain != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 79,
                        'transition_date' => $request->collection_date,
                        'purchase_id' => $collection->id,
                        'supplier_id' => $collection->supplier_id,
                        'is_cashbook' => 0,
                        'description' => 'Gain Amount',
                        'vochur_no' => $pu->invoice_no,
                        'credit' => $gain,
                        'status' => 'gain',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                if ($loss != 0) {
                    AccountTransition::create([
                        'sub_account_id' => 80,
                        'transition_date' => $request->collection_date,
                        'purchase_id' => $collection->id,
                        'supplier_id' => $collection->supplier_id,
                        'is_cashbook' => 0,
                        'description' => 'Loss Amount',
                        'vochur_no' => $pu->invoice_no,
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
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            $status = $e->getMessage();
            return compact('status');
        }
    }
    public function destroy($id)
    {
        $collection = PurchaseCollection::with('purchases')->find($id);
        foreach ($collection->purchases as $p) {
            //            dd($p);
            $relation    = DB::table('collection_purchase')
                ->select('purchase_id', 'purchase_collection_id', 'paid_amount', 'paid_amount_fx', 'discount', 'discount_fx')
                ->where('id', $p->pivot->id)
                ->first();
            if ($relation->discount == NULL) {
                $discount = 0;
            } else {
                $discount = $relation->discount;
            }

            $discount_fx = $relation->discount_fx;

            $p_cm = $relation->paid_amount + $discount;
            $p_cm_fx = $relation->paid_amount_fx + $discount_fx;

            //update collection amount in sale
            $p = PurchaseInvoice::find($relation->purchase_id);
            $collection_amount = $p->collection_amount - $p_cm;
            $collection_amount_fx = $p->collection_amount_fx - $p_cm_fx;
            $p->collection_amount = $collection_amount;
            $p->collection_amount_fx = $collection_amount_fx;
            $p->save();
        }
        $collection->purchases()->detach();
        $collection->delete();
        /**AccountTransition::where([
            ['purchase_id',$id],
            ['status','credit_payment']
        ])->delete();**/
        AccountTransition::where('purchase_id', $id)
            ->where(function ($query) {
                $query->orWhere('sub_account_id', 7)
                    ->orWhere('sub_account_id', 15)
                    ->orwhere('sub_account_id', 79) //for loss account
                    ->orwhere('sub_account_id', 80); //for gain account
            })->delete();

        return response(['message' => 'delete successful']);
    }
    public function getCreditPaymentReport(Request  $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 240);
        $html = $this->getPaymentReport($request);
        $route_name = Route::currentRouteName();
        if ($route_name == 'credit_payment_export') {
            $export = new CreditPaymentExport($html, $request);
            $fileName = 'Credit Payment Controller' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }
        return response(compact('html'), 200);
    }
    public function getPurchaseOutstanding(Request $request)
    {
        // dd($request->all());
        $route_name = Route::currentRouteName();
        $purchase_outstandings = $this->getPurchaseOutStandingReport($request);
        $net_gain_loss_amt = $net_inv_amt = $net_paid_amt = $net_balance_amt = 0;
        foreach ($purchase_outstandings as $po) {
            foreach ($po->out_list as $i) {
                // dd($i);
                if ($i->type == 'paid') {
                    if ($request->currency_id == 1 || $request->currency_id == '') {
                        $t_amount = $i->total_amount - $i->discount;
                    } else {
                        $t_amount = $i->total_amount_fx - $i->discount_fx;
                    }

                    $net_inv_amt += $t_amount;
                    //$net_inv_amt+=$i->total_amount; 
                    $net_paid_amt += $i->t_paid_amount;
                    $net_balance_amt += $i->t_balance_amount;
                    $net_gain_loss_amt += $i->t_gain_loss_amount;
                }
            }
        }
        if ($route_name == 'purchase_outstanding_export') {
            //$export=new PurchaseOverDueExport($purchase_outstandings,$net_paid_amt,$net_balance_amt,$net_inv_amt,$net_gain_loss_amt);
            $export = new PurchaseOutstandingExport($purchase_outstandings, $net_paid_amt, $net_balance_amt, $net_inv_amt, $net_gain_loss_amt, $request);
            $fileName = 'Purchase Outstanding Export' . Carbon::now()->format('Ymd') . '.xlsx';
            return Excel::download($export, $fileName);
        }
        // Kamlesh Start
        if ($route_name == 'purchase_outstanding_export_pdf') {
            $pdf = PDF::loadView('exports.purchase_outstanding_rpt_pdf', compact('purchase_outstandings', 'net_paid_amt', 'net_balance_amt', 'net_inv_amt', 'net_gain_loss_amt', 'request'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->output();
        }
        // Kamlesh End
        return compact('purchase_outstandings', 'net_paid_amt', 'net_balance_amt', 'net_inv_amt', 'net_gain_loss_amt');
    }

    public function dateIsBetween($d, $sd, $ed)
    {
        $d = date($d);
        $d = date('Y-m-d', strtotime($d));

        $sd = date('Y-m-d', strtotime($sd));

        $ed = date('Y-m-d', strtotime($ed));

        if (($d >= $sd) && ($d <= $ed)) {

            return true;
        } else {

            return false;
        }
    }

    public function getCurrencyGainLossOld(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 240);
         $data = PurchaseCollection::with('purchases','supplier','currency');
         $data = $data->selectRaw('credit_purchase_collections.*');
         $data = $data->leftjoin(DB::raw("(SELECT * FROM account_transitions ) as advance"),function($join){

                            $join->on("adv.transition purchase_id","=","credit_purchase_collections.id");

                        });

        if ($request->invoice_no != "") {
            $data->whereHas('purchases', function ($q) use ($request) {
                $q->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
            });
        }
        $from_date = $to_date = "";
        if ($request->from_date != '' && $request->to_date != '') {
            $data->whereHas('purchases', function ($q) use ($request) {
                $q->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            });
            // $data->whereBetween('landed_costings.bill_date', array($request->bill_from_date, $request->bill_to_date));
        } else if ($request->from_date != '') {
            $data->whereHas('purchases', function ($q) use ($request) {
                $q->whereDate('invoice_date', '>=', $request->from_date);
            });
            //$data->whereDate('landed_costings.bill_date', '>=', $request->bill_from_date);

        } else if ($request->to_date != '') {
            $data->whereHas('purchases', function ($q) use ($request) {
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
            $rowspan = count($collection->purchases);
            foreach ($collection->purchases as $k => $p) {
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
                ->select(DB::raw("account_transitions.*,purchase_invoices.invoice_no,purchase_invoices.invoice_date,pc.purchase_invoice_no,pc.purchase_invoice_date,pc.purchase_currency_rate,p_col_currency.sign as purchase_currency_sign,pc.collection_date,pc.collection_no,suppliers.name as supplier_name,purchase_invoices.currency_rate as inv_currency_rate,inv_currency.sign as inv_currency_sign,inv_currency.name as inv_currency_name,adv_currency.sign as adv_currency_sign,adv_currency.name as adv_currency_name,col_currency.name as col_currency_name,col_currency.sign as col_currency_sign,purchase_advances.currency_rate as adv_currency_rate,pc.currency_rate as col_currency_rate,purchase_advances.advance_no,purchase_advances.advance_date"))
                ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'account_transitions.purchase_id')
                //->leftjoin('collection_purchase', 'collection_purchase.purchase_collection_id', '=', 'account_transitions.purchase_id')
                //->leftjoin('credit_purchase_collections', 'credit_purchase_collections.id', '=', 'account_transitions.purchase_id')
                ->leftjoin(DB::raw("(SELECT account_transitions.id as transition_id, credit_purchase_collections.*,purchase_invoices.invoice_no as purchase_invoice_no, purchase_invoices.invoice_date as purchase_invoice_date, purchase_invoices.currency_id as purchase_currency_id, purchase_invoices.currency_rate as purchase_currency_rate FROM `account_transitions` JOIN collection_purchase ON collection_purchase.purchase_collection_id = account_transitions.purchase_id AND collection_purchase.gain_amount = coalesce(account_transitions.credit,0.000)AND collection_purchase.loss_amount = coalesce(account_transitions.debit,0.000) LEFT JOIN credit_purchase_collections ON credit_purchase_collections.id = account_transitions.purchase_id LEFT JOIN purchase_invoices ON purchase_invoices.id = collection_purchase.purchase_id GROUP BY collection_purchase.purchase_id) as pc"),function($join){

                            $join->on("pc.transition_id","=","account_transitions.id");

                        })
                /*->leftjoin(DB::raw("(SELECT credit_purchase_collections.*, purchase_invoices.invoice_no as purchase_invoice_no, purchase_invoices.invoice_date as purchase_invoice_date, purchase_invoices.currency_id as purchase_currency_id, purchase_invoices.currency_rate as purchase_currency_rate FROM collection_purchase LEFT JOIN credit_purchase_collections ON credit_purchase_collections.id = collection_purchase.purchase_collection_id LEFT JOIN purchase_invoices ON purchase_invoices.id = collection_purchase.purchase_id WHERE purchase_invoices.currency_id != 1 GROUP BY credit_purchase_collections.id 
                            ) as pc"),function($join){

                            $join->on("pc.id","=","account_transitions.purchase_id");

                        }) */
                ->leftjoin('purchase_advances', 'purchase_advances.id', '=', 'account_transitions.purchase_advance_id')
                ->leftjoin('suppliers', 'suppliers.id', '=', 'account_transitions.supplier_id')
                ->leftjoin('currencies as inv_currency', 'inv_currency.id', '=', 'purchase_advances.currency_id')
                ->leftjoin('currencies as adv_currency', 'adv_currency.id', '=', 'purchase_advances.currency_id')
                ->leftjoin('currencies as col_currency', 'col_currency.id', '=', 'pc.currency_id')
                ->leftjoin('currencies as p_col_currency', 'p_col_currency.id', '=', 'pc.purchase_currency_id');

        $data = $data->where(function($query) {
                        $query->where('account_transitions.status','gain')
                              ->orWhere('account_transitions.status','loss');
                    });
        $data = $data->whereNotNull('account_transitions.purchase_id');

        if($request->invoice_no != "") {
            // $data->where('invoice_no', 'LIKE','%'.$request->invoice_no.'%');
            $data->where(function($query) use($request) {
                        $query->where('invoice_no', 'LIKE','%'.$request->invoice_no.'%')
                              ->orWhere('purchase_invoice_no', 'LIKE','%'.$request->invoice_no.'%');
                    });
        }
        $from_date=$to_date="";
        if($request->from_date != '' && $request->to_date != '')
        {
            //$data->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            $data->where(function($query) use($request) {
                        $query->whereBetween('invoice_date', array($request->from_date, $request->to_date))
                              ->orWhereBetween('purchase_invoice_date', array($request->from_date, $request->to_date));
                    });

        } else if($request->from_date != '') {
            //$data->whereDate('invoice_date', '>=', $request->from_date);
            $data->where(function($query) use($request) {
                        $query->whereDate('invoice_date', '>=', $request->from_date)
                              ->orWhereDate('purchase_invoice_date', '>=', $request->from_date);
                    });

        }else if($request->to_date != '') {
            //$data->whereDate('invoice_date', '<=', $request->to_date);
            $data->where(function($query) use($request) {
                        $query->whereDate('invoice_date', '<=', $request->to_date)
                              ->orWhereDate('purchase_invoice_date', '<=', $request->to_date);
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
                $html .= '<td class="text-center">'.$p->purchase_invoice_no.'</td>';
                $html .= '<td class="text-center">'.$p->purchase_invoice_date.'</td>';
                $html .= '<td class="text-center">1'.$p->purchase_currency_sign.' = '.floatval($p->purchase_currency_rate).'MMK</td>';
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
        $export = new CurrencyGainLossExport($data, $request);
        $fileName = 'currency_gain_loss_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $fileName);
    }
}
