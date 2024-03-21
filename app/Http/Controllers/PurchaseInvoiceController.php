<?php

namespace App\Http\Controllers;

use App\AccountTransition;
use App\Http\Traits\AccountReport\Cashbook;
use App\Http\Traits\AccountReport\Ledger;
use App\Http\Traits\Report\GetReport;
use App\Product;
use App\ProductTransition;
use App\PurchaseInvoice;
use App\PurchaseAdvance;
use App\PurchaseCreditNote;
use App\User;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\DailyPurchaseProductExport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseInvoiceController extends Controller
{
    use GetReport;
    use Ledger;
    public function index(Request  $request){
        $data=PurchaseInvoice::with('currency')->where('is_opening',0);
        if($request->invoice_no != ""){
            $data->where('invoice_no', $request->invoice_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $data->whereDate('invoice_date', '<=', $request->to_date);
        } else {}

        if($request->supplier_id != "") {
            $data->where('supplier_id', $request->supplier_id);
        }

        if(isset($request->branch_id) && $request->branch_id != "") {
            $data->where('branch_id', $request->branch_id);
        }
//        if($request->office_purchase_man_id != "") {
//            $data->where('office_sale_man_id', $request->office_purchase_man_id);
//        }
        if($request->ref_no != "") {
            $data->where('reference_no','LIKE','%'.$request->ref_no.'%');
        }
        $data=$data->orderBy('id','desc')->paginate(15);
        return compact('data');
    }
    public function store(Request  $request){
        // dd($request->all());
        DB::beginTransaction();

        try {
            if(!empty($request->reference_no) && $request->duplicate_ref_no == false) {
                $validatedData = $request->validate([
                    'reference_no' => 'max:255|unique:purchase_invoices',
                ]);
            }

            foreach (Auth::user()->branches as $k => $b) {
                if ($k == 0) {
                    $branch_id = $b->id;
                }
            }
            $p = new PurchaseInvoice();
            //auto generate invoice no;
            $latest = PurchaseInvoice::orderBy('id','desc')->first();
            if($latest==null){
                $no=0;
            }else{
                $no=$latest->id;
            }
            $invoice_no = "PI".str_pad((int)$no + 1,5,"0",STR_PAD_LEFT);
            $p->invoice_no = $invoice_no;
            $p->branch_id = $branch_id;
            $p->reference_no = $request->reference_no;
            $p->invoice_date = $request->invoice_date;
            $p->warehouse_id = Auth::user()->warehouse_id;
            $p->supplier_id = $request->supplier_id;
            $p->office_purchase_man_id = Auth::user()->id;
            $p->total_amount = $request->sub_total;
            $p->discount = $request->discount;
            $p->pay_amount = $request->pay_amount;
            $p->balance_amount = $request->balance_amount;
            if($request->payment_type == 'credit') {
                $sub_account_id=config('global.purchase_advance');       /*Credit Payment Sub account ID */
                if($request->pay_amount!=0){
                    $amount=$request->pay_amount;
                }else{
                    $amount=$request->pay_amount;
                }
                $p->payment_type = 'credit';
                $p->due_date = $request->due_date;
                $p->credit_day = $request->credit_day;
            } else {
                $sub_account_id=config('global.cash_purchase');        /*Purchase Sub account ID */
                $amount=$request->pay_amount;
                $p->payment_type = 'cash';
            }
            $p->currency_id = $request->currency_id;
            if($request->currency_id != 1) {
                $p->currency_rate = $request->currency_rate;
                $p->total_amount_fx = $request->sub_total_fx;
                $p->pay_amount_fx = $request->pay_amount_fx;
                $p->discount_fx = $request->discount_fx;
                $p->balance_amount_fx = $request->balance_amount_fx;
            }

            $p->account_group_id = $request->account_group;
            $p->sub_account_id = $request->cash_bank_account;

            $p->created_by = Auth::user()->id;
            $p->updated_by = Auth::user()->id;
            $p->save();
            $description=$p->invoice_no.",Inv Date ".$p->invoice_date." to " .$p->supplier->name;

            if($p){
                //Start K
                /***if($request->payment_type =='cash' || ($request->payment_type=='credit' && $request->pay_amount!=0)){
                    AccountTransition::create([
                        'sub_account_id' => $sub_account_id,
                        'transition_date' => $p->invoice_date,
                        'purchase_id' => $p->id,
                        'supplier_id'=>$p->supplier_id,
                        'vochur_no'=>$invoice_no,
                        'description'=>$description,
                        'is_cashbook' => 1,
                        'status'=>'purchase',
                        'credit' => $amount,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                $this->storePurchaseInLedger($p);***/
                //End K

                //EP Start
                //get customer's sale advance
                $supplier_advance = 0;
                $supplier_advance_fx = 0;

                if($request->currency_id == 1) {
                    //Start For MMK Currency
                    $advance = DB::table("purchase_advances")

                                ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                                ->where('supplier_id','=',$request->supplier_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $supplier_advance = $in - $out;
                    }

                    if($supplier_advance == 0 || ($supplier_advance > 0 && $request->pay_amount == 0)) {
                        // cashbook
                        if($request->supplier_advance == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                            $this->storePurchaseInLedger($p);
                            // end ledger
                        } else {
                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'transition_date' => $p->invoice_date,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                        }
                    }
                    else if(($supplier_advance > $request->pay_amount) || $supplier_advance == $request->pay_amount) {
                        AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            $obj = new PurchaseAdvance;
                            $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->supplier_id = $request->supplier_id;
                            $obj->purchase_id = $p->id;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($supplier_advance < $request->pay_amount) {
                        $paid_amt = $request->pay_amount - $supplier_advance;
                        // add in cashbook
                            AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            //add  in ledger

                            AccountTransition::create([
                                    'sub_account_id' => $p->payment_type == 'cash' ? $this->cash_purchase : $this->purchase_advance,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                        $obj = new PurchaseAdvance;
                        $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->supplier_id = $p->supplier_id;
                        $obj->purchase_id = $p->id;
                        $obj->amount = $supplier_advance;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
                    //MMK end
                }
                else {
                    //Foreign Currency Start
                    $advance = DB::table("purchase_advances")

                                ->select(DB::raw("SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='in' THEN amount_fx ELSE 0 END)  as advance_amount_in_fx, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='out' THEN amount_fx ELSE 0 END)  as advance_amount_out_fx, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                                ->where('supplier_id','=',$request->supplier_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $supplier_advance = $in - $out;

                        $in_fx = $advance->advance_amount_in_fx == NULL ? 0 : $advance->advance_amount_in_fx;
                        $out_fx = $advance->advance_amount_out_fx == NULL ? 0 : $advance->advance_amount_out_fx;
                        $supplier_advance_fx = $in_fx - $out_fx;
                    }

                    if($supplier_advance_fx == 0 || ($supplier_advance_fx > 0 && $request->pay_amount_fx == 0)) {
                        // cashbook
                        if($request->supplier_advance_fx == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount_fx != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                            $this->storePurchaseInLedger($p);
                            // end ledger
                        } else {
                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                        }
                    }
                    else if(($supplier_advance_fx > $request->pay_amount_fx) || $supplier_advance_fx == $request->pay_amount_fx) {
                        AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);                       

                            //Start => sale advance amount first in first out process and calculate gain/loss
                            $p_adv = DB::table("purchase_advances")

                                ->select(DB::raw("purchase_advances.*"))
                                ->where('supplier_id','=',$request->supplier_id)
                                ->where('balance','!=',0)
                                ->orderBy('advance_date', 'ASC')                                
                                ->get();
                            if(!empty($p_adv)) {
                                $payAmount = $request->pay_amount_fx;
                                $payAmountFx = 0;
                                foreach($p_adv as $pa) {
                                    if($payAmount != 0) {
                                        if($pa->balance < $payAmount) {

                                            $p->advances()->attach($pa->id,['amount_fx' => $pa->balance]);
                                            $payAmountFx = $payAmountFx + $pa->balance;
                                            DB::table('purchase_advances')
                                                ->where('id', $pa->id)
                                                ->update(array('balance' => 0));
                                            if($pa->currency_rate > $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($pa->currency_rate - $request->currency_rate) * $pa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($pa->currency_rate < $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $pa->currency_rate) * $pa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = $payAmount - $pa->balance;
                                        } else {
                                            // pa->balance >= $payAmount  process
                                            $p->advances()->attach($pa->id,['amount_fx' => $payAmount]);
                                            $payAmountFx = $payAmountFx + $payAmount;
                                            DB::table('purchase_advances')
                                                ->where('id', $pa->id)
                                                ->update(array('balance' => $pa->balance - $payAmount));
                                            if($pa->currency_rate > $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($pa->currency_rate - $request->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($pa->currency_rate < $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $pa->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = 0;
                                            // end =>  pa->balance >= $payAmount  process
                                        }

                                    } else {
                                        break;
                                    }
                                }
                            }
                            //End => sale advance amount first in first out process and calculate gain/loss

                            $obj = new PurchaseAdvance;
                            $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->supplier_id = $request->supplier_id;
                            $obj->purchase_id = $p->id;
                            //$obj->amount_fx = $request->pay_amount_fx;
                            $obj->currency_rate = $request->currency_rate;
                            $obj->amount_fx = $payAmountFx;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($supplier_advance_fx < $request->pay_amount_fx) {
                        $paid_amt = $request->pay_amount - ($supplier_advance_fx * $request->currency_rate);
                        // add in cashbook
                            AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            //add  in ledger

                            AccountTransition::create([
                                    'sub_account_id' => $p->payment_type == 'cash' ? $this->cash_purchase : $this->purchase_advance,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                        //Start => sale advance amount first in first out process and calculate gain/loss
                        $p_adv = DB::table("purchase_advances")

                            ->select(DB::raw("purchase_advances.*"))
                            ->where('supplier_id','=',$request->supplier_id)
                            ->where('balance','!=',0)
                            ->orderBy('advance_date', 'ASC')                                
                            ->get();
                        if(!empty($p_adv)) {
                            $payAmount = $request->pay_amount_fx;
                            $payAmountFx = $supplier_advance_fx;
                            foreach($p_adv as $pa) {
                                if($payAmount > 0 && $payAmount != 0) {
                                    if($pa->balance < $payAmount) {
                                        $p->advances()->attach($pa->id,['amount_fx' => $pa->balance]);
                                        //$payAmountFx = $payAmountFx + $pa->balance;
                                        DB::table('purchase_advances')
                                            ->where('id', $pa->id)
                                            ->update(array('balance' => 0));
                                        if($pa->currency_rate > $request->currency_rate) {
                                            //loss
                                            $loss_amt = ($pa->currency_rate - $request->currency_rate) * $pa->balance;
                                            AccountTransition::create([
                                                'sub_account_id' => 80,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Loss Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'debit' => $loss_amt,
                                                'status'=>'loss',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]); 
                                        }
                                        if($pa->currency_rate < $request->currency_rate) {
                                            //gain
                                            $gain_amt = ($request->currency_rate - $pa->currency_rate) * $pa->balance;
                                            AccountTransition::create([
                                                'sub_account_id' => 79,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Gain Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'credit' => $gain_amt,
                                                'status'=>'gain',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }
                                        $payAmount = $payAmount - $pa->balance;
                                    } else {
                                        // pa->balance >= $payAmount  process
                                        $p->advances()->attach($pa->id,['amount_fx' => $payAmount]);
                                        //$payAmountFx = $payAmountFx + $payAmount;
                                        DB::table('purchase_advances')
                                            ->where('id', $pa->id)
                                            ->update(array('balance' => $pa->balance - $payAmount));
                                        if($pa->currency_rate > $request->currency_rate) {
                                            //loss
                                            $loss_amt = ($pa->currency_rate - $request->currency_rate) * $payAmount;
                                            AccountTransition::create([
                                                'sub_account_id' => 80,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Loss Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'debit' => $loss_amt,
                                                'status'=>'loss',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]); 
                                        }
                                        if($pa->currency_rate < $request->currency_rate) {
                                            //gain
                                            $gain_amt = ($request->currency_rate - $pa->currency_rate) * $payAmount;
                                            AccountTransition::create([
                                                'sub_account_id' => 79,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Gain Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'credit' => $gain_amt,
                                                'status'=>'gain',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }
                                        $payAmount = 0;
                                        // end =>  pa->balance >= $payAmount  process
                                    }

                                } else {
                                    break;
                                }
                            }
                        }
                        //End => sale advance amount first in first out process and calculate gain/loss

                        $obj = new PurchaseAdvance;
                        $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->supplier_id = $p->supplier_id;
                        $obj->purchase_id = $p->id;
                        $obj->currency_id = $request->currency_rate;
                        $obj->amount_fx = $payAmountFx;
                        $obj->amount = $supplier_advance_fx * $request->currency_rate;
                        //$obj->amount = $supplier_advance;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
                    //End Foreign Currency
                }
                //EP End
            }
            for($i=0; $i<count($request->product); $i++) {
                $product_result = Product::select('uom_id')->find($request->product[$i]);
                $main_uom_id = $product_result->uom_id;
                if($request->currency_id != 1) {
                    $price_fx = $request->unit_price_fx[$i];
                    $total_amount_fx = $request->total_amount_fx[$i];
                } else {
                    $price_fx = 0;
                    $total_amount_fx = 0;  
                }
                $pivot = $p->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'price' => $request->unit_price[$i],'price_fx' => $price_fx, 'price_variant' => $request->price_variants[$i], 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx]);
                //get last pivot insert id
                $last_row=DB::table('product_purchase')->orderBy('id', 'DESC')->first();
                $pivot_id = $last_row->id;
                //calculate quantity for product pre-defined UOM
    //            $uom_relation = DB::table('product_selling_uom')
    //                ->select('relation')
    //                ->where('product_id',$request->product[$i])
    //                ->where('uom_id',$request->uom[$i])
    //                ->first();
    //            if($uom_relation) {
    //                $relation_val = $uom_relation->relation;
    //            } else {
    //                //for pre-defined product uom
    //                $relation_val = 1;
    //            }
                //add products in transition table=> transition_type = out (for sold out)
                $obj = new ProductTransition;
                $obj->product_id            = $request->product[$i];
                $obj->transition_type       = "in";
                $obj->transition_purchase_id   = $p->id;
                $obj->transition_product_pivot_id = $pivot_id;
                $obj->branch_id  = $branch_id;
                $obj->warehouse_id          = Auth::user()->warehouse_id;
                $obj->transition_date       = $request->invoice_date;
                $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $request->qty[$i];
                $obj->product_uom_id        = $main_uom_id;
                $obj->product_quantity      = $request->qty[$i] ;
                $obj->created_by = Auth::user()->id;
                $obj->updated_by = Auth::user()->id;
                $obj->save();
            }
            $status = "success";
            $purchase_id = $p->id;
            DB::commit();
            return compact('status','purchase_id');
        } catch (\Throwable $e) {
            DB::rollback();
            $status=$e->getMessage();
            return compact('status');
            throw $e;
        }
      
    }
    public function edit($id){
//        $access_brands = array();
        $purchase = PurchaseInvoice::with('products','warehouse','currency','supplier','products.brand','products.category','products.uom','products.selling_uoms','office_purchase_man','branch')->find($id);
//        if(Auth::user()->role->id == 6) {
//            //for Country Head User
//            foreach(Auth::user()->brands as $brand) {
//                array_push($access_brands, $brand->id);
//            }
//        }
        $supplier_id = $purchase->supplier_id;
        $previous_balance = 0;
        $chk_balance = DB::table("purchase_invoices")
            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as previous_balance"))
            ->where('supplier_id','=',$supplier_id)
            ->where('id', '!=', $id)
            ->groupBy('supplier_id')
            ->first();
        if($chk_balance) {
            $previous_balance = $chk_balance->previous_balance;
        }
        return compact('purchase', 'previous_balance');
    }
    public function update(Request $request,$id){
//        dd($request->all());
        if(!empty($request->reference_no) && $request->duplicate_ref_no == false) {
            $validatedData = $request->validate([
                'reference_no' => 'max:255|unique:purchase_invoices,reference_no,'.$id,
            ]);
        }

        foreach (Auth::user()->branches as $k => $b) {
            if ($k == 0) {
                $branch_id = $b->id;
            }
        }

        $p = PurchaseInvoice::find($id);
        $old_sub_account_id=$p->payment_type=='credit' ? config('global.purchase_advance') :config('global.cash_purchase');
        //$p->invoice_no = $request->invoice_no;
        $p->reference_no = $request->reference_no;
        $p->invoice_date = $request->invoice_date;
        //$p->warehouse_id = Auth::user()->warehouse_id;
        $p->office_purchase_man_id = Auth::user()->id;
        $p->supplier_id = $request->supplier_id;
        $p->total_amount = $request->sub_total;
        $p->discount = $request->discount;
        $p->pay_amount = $request->pay_amount;
        $p->balance_amount = $request->balance_amount;
//        $sale->sale_type  = $request->sale_type;
        if($request->payment_type == 'credit') {
            if($request->pay_amount != 0){
                $amount=$request->pay_amount;
            }
            $sub_account_id=config('global.purchase_advance');
            $p->payment_type = 'credit';
            $p->due_date = $request->due_date;
            $p->credit_day = $request->credit_day;
        } else {
            $p->payment_type = 'cash';
            //$amount=$request->sub_total;
            $amount=$request->pay_amount;//edited by ep
            $sub_account_id=config('global.cash_purchase');        /*Purchase Sub account ID */
        }

        $p->currency_id = $request->currency_id;
        if($request->currency_id != 1) {
            $p->currency_rate = $request->currency_rate;
            $p->total_amount_fx = $request->sub_total_fx;
            $p->pay_amount_fx = $request->pay_amount_fx;
            $p->discount_fx = $request->discount_fx;
            $p->balance_amount_fx = $request->balance_amount_fx;
        }

        $p->account_group_id = $request->account_group;
        $p->sub_account_id = $request->cash_bank_account;

        $p->updated_at = time();
        $p->updated_by = Auth::user()->id;
        $p->save();
        $warehouse_id = $p->warehouse_id;
        $sup_name=Supplier::find($request->supplier_id);
        $description=$p->invoice_no.",Inv Date ".$p->invoice_date." to " .$sup_name->name;
        $invoice_no = $p->invoice_no;
        if($p){
            //K start
            /***if($request->payment_type =='cash' || ($request->payment_type=='credit' && $request->pay_amount!=0)) {
                    AccountTransition::where('purchase_id',$id)->where('sub_account_id',$old_sub_account_id)->delete();
                    AccountTransition::create([
                        'sub_account_id' => $sub_account_id,
                        'transition_date' => $p->invoice_date,
                        'purchase_id' => $p->id,
                        'is_cashbook' => 1,
                        'description'=>$description,
                        'status'=>'purchase',
                        'vochur_no'=>$request->invoice_no,
                        'credit' => $amount,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
            }elseif($request->payment_type=='credit' && $request->pay_amount==0){
                AccountTransition::where('purchase_id',$id)
                ->where('sub_account_id',$old_sub_account_id)
                ->delete();
            }
            $this->updatePurchaseInLedger($p);***/
            //K End
            //EP Start
                /**AccountTransition::where('purchase_id',$id)
                                    ->where('status','purchase')
                                    ->delete();**/
                AccountTransition::where('purchase_id',$id)
                                    ->where(function($query) {
                                            $query->where('status','purchase')
                                                  ->orWhere('status','gain')
                                                  ->orWhere('status','loss');
                                        })
                                    ->delete();

                PurchaseAdvance::where('purchase_id',$id)
                            ->where('advance_type','out')
                            ->delete();
                //get supplier's purchase advance
                $supplier_advance = 0;
                $supplier_advance_fx = 0;
                if($request->currency_id == 1) {
                    $advance = DB::table("purchase_advances")

                                ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                                ->where('supplier_id','=',$request->supplier_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $supplier_advance = $in - $out;
                    }

                    if($supplier_advance == 0 || ($supplier_advance > 0 && $request->pay_amount == 0)) {
                        // cashbook
                        if($request->supplier_advance == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                            $this->storePurchaseInLedger($p);
                            // end ledger
                        } else {
                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'transition_date' => $p->invoice_date,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                        }
                    }
                    else if(($supplier_advance > $request->pay_amount) || $supplier_advance == $request->pay_amount) {
                        AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            $obj = new PurchaseAdvance;
                            $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->supplier_id = $request->supplier_id;
                            $obj->purchase_id = $p->id;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($supplier_advance < $request->pay_amount) {
                        $paid_amt = $request->pay_amount - $supplier_advance;
                        // add in cashbook
                            AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            //add  in ledger

                            AccountTransition::create([
                                    'sub_account_id' => $p->payment_type == 'cash' ? $this->cash_purchase : $this->purchase_advance,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                        $obj = new PurchaseAdvance;
                        $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->supplier_id = $p->supplier_id;
                        $obj->purchase_id = $p->id;
                        $obj->amount = $supplier_advance;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
                } else {
                    //for foreign currency
                    //Reset advance and remove in purchase_advance_links
                    $old_advance = DB::table("purchase_advance_links")

                                ->select(DB::raw("purchase_advance_links.*, purchase_advances.balance,purchase_advances.id"))
                                ->leftjoin('purchase_advances', 'purchase_advances.id', '=', 'purchase_advance_links.advance_id')
                                ->where('purchase_advance_links.purchase_id','=',$p->id)
                                ->get();
                    if(!empty($old_advance)) {
                        foreach($old_advance as $a) {
                            $balance = $a->balance + $a->amount_fx;
                            DB::table('purchase_advances')
                                ->where('id', $a->id)
                                ->update(array('balance' => $balance));   
                        }
                    }

                    $p->advances()->detach();

                    $advance = DB::table("purchase_advances")

                                ->select(DB::raw("SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='in' THEN amount_fx ELSE 0 END)  as advance_amount_in_fx, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='out' THEN amount_fx ELSE 0 END)  as advance_amount_out_fx, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                                ->where('supplier_id','=',$request->supplier_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $supplier_advance = $in - $out;

                        $in_fx = $advance->advance_amount_in_fx == NULL ? 0 : $advance->advance_amount_in_fx;
                        $out_fx = $advance->advance_amount_out_fx == NULL ? 0 : $advance->advance_amount_out_fx;
                        $supplier_advance_fx = $in_fx - $out_fx;
                    }

                    if($supplier_advance_fx == 0 || ($supplier_advance_fx > 0 && $request->pay_amount_fx == 0)) {
                        // cashbook
                        if($request->supplier_advance_fx == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount_fx != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                            $this->storePurchaseInLedger($p);
                            // end ledger
                        } else {
                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                        }
                    }
                    else if(($supplier_advance_fx > $request->pay_amount_fx) || $supplier_advance_fx == $request->pay_amount_fx) {
                        AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);                       

                            //Start => sale advance amount first in first out process and calculate gain/loss
                            $p_adv = DB::table("purchase_advances")

                                ->select(DB::raw("purchase_advances.*"))
                                ->where('supplier_id','=',$request->supplier_id)
                                ->where('balance','!=',0)
                                ->orderBy('advance_date', 'ASC')                                
                                ->get();
                            if(!empty($p_adv)) {
                                $payAmount = $request->pay_amount_fx;
                                $payAmountFx = 0;
                                foreach($p_adv as $pa) {
                                    if($payAmount != 0) {
                                        if($pa->balance < $payAmount) {

                                            $p->advances()->attach($pa->id,['amount_fx' => $pa->balance]);
                                            $payAmountFx = $payAmountFx + $pa->balance;
                                            DB::table('purchase_advances')
                                                ->where('id', $pa->id)
                                                ->update(array('balance' => 0));
                                            if($pa->currency_rate > $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($pa->currency_rate - $request->currency_rate) * $pa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($pa->currency_rate < $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $pa->currency_rate) * $pa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = $payAmount - $pa->balance;
                                        } else {
                                            // pa->balance >= $payAmount  process
                                            $p->advances()->attach($pa->id,['amount_fx' => $payAmount]);
                                            $payAmountFx = $payAmountFx + $payAmount;
                                            DB::table('purchase_advances')
                                                ->where('id', $pa->id)
                                                ->update(array('balance' => $pa->balance - $payAmount));
                                            if($pa->currency_rate > $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($pa->currency_rate - $request->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($pa->currency_rate < $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $pa->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'account_group_id' => $p->account_group_id,
                                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                                    'transition_date' => $request->invoice_date,
                                                    'purchase_id' => $p->id,
                                                    'purchase_advance_id' => $pa->id,
                                                    'supplier_id'=>$pa->supplier_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$pa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = 0;
                                            // end =>  pa->balance >= $payAmount  process
                                        }

                                    } else {
                                        break;
                                    }
                                }
                            }
                            //End => sale advance amount first in first out process and calculate gain/loss

                            $obj = new PurchaseAdvance;
                            $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->supplier_id = $request->supplier_id;
                            $obj->purchase_id = $p->id;
                            $obj->currency_rate = $request->currency_rate;
                            $obj->amount_fx = $payAmountFx;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($supplier_advance_fx < $request->pay_amount_fx) {
                        $paid_amt = $request->pay_amount - ($supplier_advance_fx * $request->currency_rate);
                        // add in cashbook
                            AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            //add  in ledger

                            AccountTransition::create([
                                    'sub_account_id' => $p->payment_type == 'cash' ? $this->cash_purchase : $this->purchase_advance,
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'credit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            AccountTransition::create([
                                    'sub_account_id' => config('global.purchase'),
                                    'account_group_id' => $p->account_group_id,
                                    'cash_bank_sub_account_id' => $p->sub_account_id,
                                    'transition_date' => $p->invoice_date,
                                    'purchase_id' => $p->id,
                                    'supplier_id'=>$p->supplier_id,
                                    'vochur_no'=>$invoice_no,
                                    'description'=> '',
                                    'is_cashbook' => 0,
                                    'status'=>'purchase',
                                    'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                        //Start => sale advance amount first in first out process and calculate gain/loss
                        $p_adv = DB::table("purchase_advances")

                            ->select(DB::raw("purchase_advances.*"))
                            ->where('supplier_id','=',$request->supplier_id)
                            ->where('balance','!=',0)
                            ->orderBy('advance_date', 'ASC')                                
                            ->get();
                        if(!empty($p_adv)) {
                            $payAmount = $request->pay_amount_fx;
                            $payAmountFx = $supplier_advance_fx;
                            foreach($p_adv as $pa) {
                                if($payAmount > 0 && $payAmount != 0) {
                                    if($pa->balance < $payAmount) {
                                        $p->advances()->attach($pa->id,['amount_fx' => $pa->balance]);
                                        //$payAmountFx = $payAmountFx + $pa->balance;
                                        DB::table('purchase_advances')
                                            ->where('id', $pa->id)
                                            ->update(array('balance' => 0));
                                        if($pa->currency_rate > $request->currency_rate) {
                                            //loss
                                            $loss_amt = ($pa->currency_rate - $request->currency_rate) * $pa->balance;
                                            AccountTransition::create([
                                                'sub_account_id' => 80,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Loss Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'debit' => $loss_amt,
                                                'status'=>'loss',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]); 
                                        }
                                        if($pa->currency_rate < $request->currency_rate) {
                                            //gain
                                            $gain_amt = ($request->currency_rate - $pa->currency_rate) * $pa->balance;
                                            AccountTransition::create([
                                                'sub_account_id' => 79,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Gain Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'credit' => $gain_amt,
                                                'status'=>'gain',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }
                                        $payAmount = $payAmount - $pa->balance;
                                    } else {
                                        // pa->balance >= $payAmount  process
                                        $p->advances()->attach($pa->id,['amount_fx' => $payAmount]);
                                        //$payAmountFx = $payAmountFx + $payAmount;
                                        DB::table('purchase_advances')
                                            ->where('id', $pa->id)
                                            ->update(array('balance' => $pa->balance - $payAmount));
                                        if($pa->currency_rate > $request->currency_rate) {
                                            //loss
                                            $loss_amt = ($pa->currency_rate - $request->currency_rate) * $payAmount;
                                            AccountTransition::create([
                                                'sub_account_id' => 80,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Loss Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'debit' => $loss_amt,
                                                'status'=>'loss',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]); 
                                        }
                                        if($pa->currency_rate < $request->currency_rate) {
                                            //gain
                                            $gain_amt = ($request->currency_rate - $pa->currency_rate) * $payAmount;
                                            AccountTransition::create([
                                                'sub_account_id' => 79,
                                                'account_group_id' => $p->account_group_id,
                                                'cash_bank_sub_account_id' => $p->sub_account_id,
                                                'transition_date' => $request->invoice_date,
                                                'purchase_id' => $p->id,
                                                'purchase_advance_id' => $pa->id,
                                                'supplier_id'=>$pa->supplier_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Gain Amount',
                                                'vochur_no'=>$pa->advance_no,
                                                'credit' => $gain_amt,
                                                'status'=>'gain',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }
                                        $payAmount = 0;
                                        // end =>  pa->balance >= $payAmount  process
                                    }

                                } else {
                                    break;
                                }
                            }
                        }
                        //End => sale advance amount first in first out process and calculate gain/loss

                        $obj = new PurchaseAdvance;
                        $max_id = PurchaseAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->supplier_id = $p->supplier_id;
                        $obj->purchase_id = $p->id;
                        $obj->amount_fx = $payAmountFx;
                        $obj->currency_rate = $request->currency_rate;
                        $obj->amount = $supplier_advance_fx * $request->currency_rate;
                        //$obj->amount = $supplier_advance;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
                }
                //EP End

        }
        $ex_pivot_arr = $request->ex_product_pivot;
        //remove id in pivot that are removed in sale Form
        $results =array_diff($ex_pivot_arr,$request->product_pivot); //get id that are not in request pivot array
        foreach($results as $key => $val) {
            //delete in pivot
            DB::table('product_purchase')
                ->where('id', $val)
                ->delete();
            //delete in transition
            DB::table('product_transitions')
                ->where('transition_product_pivot_id', $val)
                ->where('transition_purchase_id', $id)
                ->delete();
        }

        //update in product pivot table
        for($i=0; $i<count($request->product); $i++) {

            if($request->currency_id != 1) {
                $price_fx = $request->unit_price_fx[$i];
                $total_amount_fx = $request->total_amount_fx[$i];
            } else {
                $price_fx = 0;
                $total_amount_fx = 0;  
            }
            //check product already exist or not
            if($request->product_pivot[$i] != '0' && in_array($request->product_pivot[$i], $ex_pivot_arr)) {
                //update existing product in pivot and transition tables
                DB::table('product_purchase')
                    ->where('id', $request->product_pivot[$i])
                    ->update(array('uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'price' => $request->unit_price[$i], 'price_fx' => $price_fx, 'price_variant' => $request->price_variants[$i], 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx));

                //get product pre-defined UOM
                $product_result = Product::select('uom_id')->find($request->product[$i]);
                $main_uom_id = $product_result->uom_id;
                //calculate quantity for product pre-defined UOM
//                $uom_relation = DB::table('product_selling_uom')
//                    ->select('relation')
//                    ->where('product_id',$request->product[$i])
//                    ->where('uom_id',$request->uom[$i])
//                    ->first();
//                if($uom_relation) {
//                    $relation_val = $uom_relation->relation;
//                } else {
//                    //for pre-defined product uom
//                    $relation_val = 1;
//                }
//                $product_qty = $request->qty[$i] * $relation_val;

                DB::table('product_transitions')
                    ->where('transition_product_pivot_id', $request->product_pivot[$i])
                    ->where('transition_purchase_id', $id)
                    ->update(array('product_uom_id' => $main_uom_id,'product_quantity'=>$request->qty[$i],'transition_date'=>$request->invoice_date, 'transition_product_uom_id' => $request->uom[$i], 'transition_product_quantity' => $request->qty[$i]));
            } else {
                //add product into pivot table if not exist
                //get product pre-defined UOM
                $product_result = Product::select('uom_id')->find($request->product[$i]);
                $main_uom_id = $product_result->uom_id;
                //add product into pivot table
                $pivot = $p->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'price' => $request->unit_price[$i], 'price_fx' => $price_fx, 'price_variant' => $request->price_variants[$i], 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx]);

                //get last pivot insert id
                $last_row=DB::table('product_purchase')->orderBy('id', 'DESC')->first();

                $pivot_id = $last_row->id;

                //calculate quantity for product pre-defined UOM
//                $uom_relation = DB::table('product_selling_uom')
//                    ->select('relation')
//                    ->where('product_id',$request->product[$i])
//                    ->where('uom_id',$request->uom[$i])
//                    ->first();
//                if($uom_relation) {
//                    $relation_val = $uom_relation->relation;
//                } else {
//                    //for pre-defined product uom
//                    $relation_val = 1;
//                }

                //add products in transition table=> transfer_type = out (for sold out)
                $obj = new ProductTransition;
                $obj->product_id            = $request->product[$i];
                $obj->transition_type       = "in";
                $obj->transition_purchase_id   = $id;
                $obj->transition_product_pivot_id   = $pivot_id;
                $obj->branch_id  = $branch_id;
               // $obj->warehouse_id          = Auth::user()->warehouse_id; // for Main Warehouse Entry
                $obj->warehouse_id          = $warehouse_id;
                $obj->transition_date       = $request->invoice_date;
                $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $request->qty[$i];
                $obj->product_uom_id        = $main_uom_id;
                $obj->product_quantity      = $request->qty[$i];
                $obj->created_by = Auth::user()->id;
                $obj->updated_by = Auth::user()->id;
                $obj->save();
            }
        }

        $status = "success";
        return compact('status');
    }
    public function destroy($id){
        $p = PurchaseInvoice::find($id);
        $p->products()->detach();
        DB::table('product_transitions')
            ->where('transition_type','in')
            ->where('transition_purchase_id', $id)
            ->delete();
        
        if($p->payment_type=='cash'){
            $sub_account_id=config('global.cash_purchase');
        }else{
            $sub_account_id=config('global.purchase_advance');
        }

        //for foreign currency
        //Reset advance and remove in purchase_advance_links
        $old_advance = DB::table("purchase_advance_links")

                    ->select(DB::raw("purchase_advance_links.*, purchase_advances.balance,purchase_advances.id"))
                    ->leftjoin('purchase_advances', 'purchase_advances.id', '=', 'purchase_advance_links.advance_id')
                    ->where('purchase_advance_links.purchase_id','=',$p->id)
                    ->get();
        if(!empty($old_advance)) {
            foreach($old_advance as $a) {
                $balance = $a->balance + $a->amount_fx;
                DB::table('purchase_advances')
                    ->where('id', $a->id)
                    ->update(array('balance' => $balance));   
            }
        }

        $p->advances()->detach();

        $p->delete();

        AccountTransition::where('purchase_id',$id)
                ->where(function($query) {
                        $query->where('status','purchase')
                              ->orWhere('status','gain')
                              ->orWhere('status','loss');
                    })
                ->delete();

        /**AccountTransition::where([
            ['purchase_id',$id],
            ['status','purchase']
        ])->delete();**/

        DB::table('purchase_advances')
                ->where('purchase_id', $id)
                ->delete();

        return response(['message' => 'delete successful']);
    }
    public function getPreviousBalance($id)
    {
        $previous_balance = 0;
        $chk_balance = DB::table("purchase_invoices")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as previous_balance"))
            ->where('supplier_id','=',$id)
            ->groupBy('supplier_id')
            ->first();
        if($chk_balance) {
            $previous_balance = $chk_balance->previous_balance;
        }

        //get supplier's purchase advance
        $supplier_advance = 0;
        $supplier_advance_fx = 0;
        $advance = DB::table("purchase_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='in' THEN amount_fx ELSE 0 END)  as advance_amount_in_fx, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='out' THEN amount_fx ELSE 0 END)  as advance_amount_out_fx, currency_id"))
                    ->where('supplier_id','=',$id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $supplier_advance = $in - $out;

            $in_fx = $advance->advance_amount_in_fx == NULL ? 0 : $advance->advance_amount_in_fx;
            $out_fx = $advance->advance_amount_out_fx == NULL ? 0 : $advance->advance_amount_out_fx;
            $supplier_advance_fx = $in_fx - $out_fx;

            if($advance->currency_id != 1) {
                $supplier_advance = $supplier_advance_fx;
            } 

            $currency_type = $advance->currency_id;
        }
        return compact('previous_balance','supplier_advance','currency_type');

    }
    public function getDailyPurchaseProductReport(Request $request)
    {
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 240);

        //$sales =    Sale::with('products','order','order.sale_man','customer','warehouse','products.selling_uoms','products.uom','office_sale_man');
        $purchases = DB::table("product_purchase")
            ->select(DB::raw("product_purchase.*, purchase_invoices.invoice_date, purchase_invoices.invoice_no, purchase_invoices.order_id, purchase_invoices.branch_id, purchase_invoices.warehouse_id, purchase_invoices.supplier_id, products.product_code, products.product_name, products.brand_id, brands.brand_name, suppliers.name, uoms.uom_name,branches.branch_name"))

            ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'product_purchase.purchase_id')
//            ->leftjoin('orders', 'orders.id', '=', 'sales.order_id')
            ->leftjoin('products', 'products.id', '=', 'product_purchase.product_id')

            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

            ->leftjoin('suppliers', 'suppliers.id', '=', 'purchase_invoices.supplier_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'product_purchase.uom_id')

//            ->leftjoin('users as u1', 'u1.id', '=', 'sales.office_sale_man_id')
//
//            ->leftjoin('users as u2', 'u2.id', '=', 'orders.sale_man_id')

            ->leftjoin('branches', 'branches.id', '=', 'purchase_invoices.branch_id');

        if($request->invoice_no != "") {
            $purchases->where('purchase_invoices.invoice_no', $request->invoice_no);
        }

        if($request->from_date != '' && $request->to_date != '')
        {
            $purchases->whereBetween('purchase_invoices.invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $purchases->whereDate('purchase_invoices.invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $purchases->whereDate('purchase_invoices.invoice_date', '<=', $request->to_date);
        } else {}

        if($request->warehouse_id != "") {
            $purchases->where('purchase_invoices.warehouse_id', $request->warehouse_id);
        }

        if(isset($request->branch_id) && $request->branch_id != "") {
            $purchases->where('purchase_invoices.branch_id', $request->branch_id);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $purchases->whereIn('purchase_invoices.branch_id',$branch_arr);
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                /*$branch = Auth::user()->branch_id;
                $purchases->where('purchase_invoices.branch_id',$branch);*/
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $purchases->whereIn('purchase_invoices.branch_id',$branch_arr);
            }
        }
        if($request->supplier_id != "") {
            $purchases->where('purchase_invoices.supplier_id', $request->supplier_id);
        }

        if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            $binds = array(strtolower($request->product_name));
            $purchases->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
        }

        /*if($request->brand_id != "") {
            $sales->whereHas('products', function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            });
        }*/
        if($request->brand_id != "") {
            $purchases->where('products.brand_id', $request->brand_id);
        } else {
            if(Auth::user()->role->id == 6) {
                //for Country Head User
                $access_brands = array();
                foreach(Auth::user()->brands as $brand) {
                    array_push($access_brands, $brand->id);
                }

                $purchases->whereIn('products.brand_id',$access_brands);
            }
        }
        /*if($request->sale_man_id != "") {
            $sales->whereHas('order', function ($query) use ($request) {
                $query->where('sale_man_id', $request->sale_man_id);
            });
        }*/
//        if($request->sale_man_id != "") {
//            $sales->where('orders.sale_man_id', $request->sale_man_id);
//        }
//
//        if($request->office_sale_man_id != "") {
//            $sales->where('sales.office_sale_man_id', $request->office_sale_man_id);
//        }

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
//            $office_sale_man_arr = array();
            foreach(Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);
                }
            }
//            foreach(Auth::user()->office_sale_mans as $osm) {
//                array_push($office_sale_man_arr, $osm->id);
//            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by',$access_users)
                ->pluck('id')->toArray();

            // $sales->whereIn('order_id',$orders);
//            $sales->where(function($query) use ($orders, $office_sale_man_arr) {
//                $query->whereIn('order_id',$orders)
//                    ->orWhereIn('office_sale_man_id',$office_sale_man_arr);
//            });
        }

        if(Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach(Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by',$ls_access_users)
                ->pluck('id')->toArray();

            $purchases->whereIn('order_id',$orders);
        }

        if(Auth::user()->role->id == 4) {
            //for office order user
            //get order user's order id
            $orders = DB::table('orders')
                ->where('created_by',Auth::user()->id)
                ->pluck('id')->toArray();

            $purchases->whereIn('order_id',$orders);
        }

        if($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if($request->sort_by != "") {
            if($request->sort_by == "invoice_no") {
                $purchases->orderBy('purchase_invoices.invoice_no', $order);
            }
            else {}
        } else {
            $purchases->orderBy('purchase_invoices.invoice_date', 'DESC');
        }
        // $data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        $data = $purchases->get();
//        dd($data);
        /*$access_brands = array();

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            foreach(Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }
        }

        $brands = DB::table('brands')
                    ->whereIn('id',$access_brands)
                    ->pluck('id')->toArray();*/

        $total = 0;
        $i = 1;
        $html = '';
        foreach($data as $purchase) {
            $html .= '<tr><td class="text-right"></td><td>'.$purchase->invoice_no.'</td><td>'.$purchase->invoice_date.'</td>';
            $html .= '<td class="mm-txt">'.$purchase->branch_name.'</td>';
            $html .= '<td class="mm-txt">'.$purchase->name.'</td>';
            $html .= '<td>'.$purchase->product_code.'</td>';
            $html .= '<td>'.$purchase->product_name.'</td>';
            $html .= '<td>'.$purchase->product_quantity.'</td>';
            $html .= '<td>'.$purchase->uom_name.'</td>';
            if($purchase->is_foc == 0) {
                $html .= '<td class="text-right">'.$purchase->price.'</td>';
            }
            else {
                $html .= '<td>FOC</td>';
            }

            $html .='<td class="text-right">'.$purchase->total_amount.'</td>';
            $html .= '</tr>';

            if($purchase->is_foc == 0){
                $total = $total + $purchase->total_amount;
            }

            $i++;

        }

        $html .= '<tr><td colspan ="10" style="text-align: right;"><strong>Total</strong></td><td class="text-right">'.number_format($total).'</td></tr>';

        $route_name=Route::currentRouteName();
        if($route_name=='daily_purchase_product_export'){
            $export=new DailyPurchaseProductExport($data, $request);
            $fileName = 'daily_purchase_product_'.Carbon::now()->format('Ymd').'.xlsx';
            return Excel::download($export, $fileName);
        }

        return response(compact('html'), 200);
    }
    public function getCreditPaymentReport(Request  $request){
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 240);
        $html=$this->getPaymentReport($request);
        return response(compact('html'), 200);

    }

    public function generatePurchaseInvoicePDF($purchase_id)
    {

       // $data = ['title' => ''];
       //$pdf = PDF::loadView('invoice_print', $data);

        $purchase = PurchaseInvoice::with('products','supplier','products.uom','products.selling_uoms')->find($purchase_id);        

       // $previous_balance = $previous_balance - $return_amount;

        return view('exports.purchase_invoice_print', compact('purchase'));
    }

    public function generatePurchaseInvoiceCurrencyPDF($purchase_id)
    {

       // $data = ['title' => ''];
       //$pdf = PDF::loadView('invoice_print', $data);

        $purchase = PurchaseInvoice::with('currency','products','supplier','products.uom','products.selling_uoms')->find($purchase_id);        

       // $previous_balance = $previous_balance - $return_amount;
        $currency = $purchase->currency->sign;

        return view('exports.purchase_invoice_currency_print', compact('purchase','currency'));
    }

    public function getPurchaseInvoiceByCreditNoteType($credit_type, Request $request)
    {
        ini_set('memory_limit', '-1');
        // dd('a');
        // dd($request->all());
        /*if ($request->supplier_id != null && $request->branch_id != '') {
            if ($credit_type == 0) {
                $data = PurchaseInvoice::with('products')->orderBy('invoice_date', 'ASC')
                    ->where('supplier_id', $request->supplier_id)
                    ->where('payment_type', 'cash')
                    ->where('branch_id', $request->branch_id)
                    ->get();
            } else if ($credit_type == 1 || $credit_type == 2) {
                $data = PurchaseInvoice::with('products')->orderBy('invoice_date', 'ASC')
                    ->where('supplier_id', $request->supplier_id)
                    ->where('payment_type', 'credit')
                    ->where('branch_id', $request->branch_id)
                    ->whereRaw('(total_amount-(pay_amount + collection_amount)) > 0')
                    ->get();
            }

        } else {
            $data = '';
        }
        return response(compact('data'), 200);*/
        $cn_amount = 0;
        $inv_bal = 0;
        if (isset($request->cn_id) && $request->cn_id != "") {
            $cn = PurchaseCreditNote::with('products')->find($request->cn_id);
            foreach ($cn->products as $cp) {
                $cn_amount += $cp->pivot->total_amount;
            }
            $data = PurchaseInvoice::with('products', 'products.selling_uoms')->orderBy('invoice_date', 'ASC')
                ->where('id', $cn->purchase_id)
                ->get();
            foreach ($data as $key => $s) {
                $inv_bal = $s->total_amount - ($s->discount == NULL ? 0 : $s->discount + $s->collection_amount + $s->pay_amount + $s->credit_note_amount);
            }
            $inv_bal = $inv_bal + $cn_amount;
        } else {
            if ($request->supplier_id != null && $request->branch_id != '') {
                if ($credit_type == 0) {
                    $data = PurchaseInvoice::with('products', 'products.selling_uoms')->orderBy('invoice_date', 'ASC')
                        ->where('supplier_id', $request->supplier_id)
                        ->where('payment_type', 'cash')
                        ->where('branch_id', $request->branch_id)
                        ->get();
                } else if ($credit_type == 1 || $credit_type == 2) {
                    $data = PurchaseInvoice::with('products', 'products.selling_uoms')->orderBy('invoice_date', 'ASC')
                        ->where('supplier_id', $request->supplier_id)
                        ->where('payment_type', 'credit')
                        ->where('branch_id', $request->branch_id)
                        ->whereRaw('(total_amount-(IFNULL(discount,0) + pay_amount + collection_amount + credit_note_amount)) > 0')
                        ->get();
                }
            } else {
                $data = '';
            }
        }
        // dd($data);
        return response(compact('data', 'inv_bal', 'cn_amount'), 200);
    }

}
