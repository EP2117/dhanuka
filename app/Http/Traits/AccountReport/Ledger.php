<?php

namespace App\Http\Traits\AccountReport;

use DateTime;
use stdClass;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\AccountTransition;
use App\SubAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait Ledger
{
    public function __construct()
    {
        //sub account for sale
        $this->sale_account = config('global.sale');
        $this->cash_sale = config('global.cash_sale');
        $this->sale_advance = config('global.sale_advance');
        $this->discount_allowed = config('global.discount_allowed');
        $this->credit_collection = config('global.credit_collection');
        // sub account for purcahse
        $this->purchase_account = config('global.purchase');
        $this->cash_purchase = config('global.cash_purchase');
        $this->purchase_advance = config('global.purchase_advance');
        $this->discount_received = config('global.discount_received');
        $this->credit_payment = config('global.credit_payment');
        $this->opening_balance=null;
    }
    public function getDateArrForLedger($request){

        // dd($request->all());
        $from = \Carbon\Carbon::createFromFormat('Y-m-d',$request->from_date);
        if(!is_null($request->to_date)){
            $to = \Carbon\Carbon::createFromFormat('Y-m-d',$request->to_date);
        }else{
            // $to = \Carbon\Carbon::createFromFormat('Y-m-d',Carbon::today());
            $to=Carbon::today();
        }
        // $from = \Carbon\Carbon::createFromFormat('Y-m-d',$request->to_date);
        $count_of_days = $to->diffInDays($from);
        $from_date=new Carbon($request->from_date);
        $to_date=new Carbon($request->to_date);
        $from_year=$from_date->format('Y');  $from_day=$from_date->format('d'); $from_month=$from_date->format('m');
        $to_year=(int)$from_date->format('Y');  $to_day=(int)$to_date->format('d'); $to_month=(int)$to_date->format('m');
        $date_arr=[];
        $t=$count_of_days+(int)$from_day;
        for($i=(int)$from_day ; $i <=$t;$i++){
            $date = $from_year . '-' . $from_month . '-' . $i;
            array_push($date_arr,$date);
        }
        // dd($date_arr);
        return $date_arr;
    }
    public function storeSaleInLedger($sale) {
        // dd($sale);
        $sale_account = AccountTransition::create([
            'sub_account_id' => $this->sale_account,
            'account_group_id' => $sale->account_group_id,
            'cash_bank_sub_account_id' => $sale->sub_account_id,
            'transition_date' => $sale->invoice_date,
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'vochur_no' => $sale->invoice_no,
            'description' => '',
            'is_cashbook' => 0,
            'status' => 'sale',
            // 'debit' => $sale->net_total,
            // change for account side 
            'credit' => $sale->net_total + $sale->tax_amount,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
        if ($sale->payment_type == 'cash' ||  ($sale->payment_type == 'credit' && $sale->pay_amount != 0)) {
            $sale_type = $sale_account->replicate()->fill([
                'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                'debit' => $sale->pay_amount,
                'account_group_id' => $sale->account_group_id,
                'cash_bank_sub_account_id' => $sale->sub_account_id,
                // 'status' => 'sale',
                'credit' => null,
            ]);
            $sale_type->save();
        }
        // if($sale->cash_discount!=null || $sale->cash_discount!=0){  
        //     $discount_acc=$sale_account->replicate()->fill([
        //         'sub_account_id' => $this->discount_allowed,
        //         'credit' => $sale->cash_discount,
        //         'status'=>'sale',
        //         'debit'=>null,
        //     ]);
        //     $discount_acc->save();
        // }
    }
    public function updateSaleInLedger($sale)
    {
        AccountTransition::where([
            ['is_cashbook', 0],
            ['sale_id', $sale->id],
            ['status', 'sale'],
        ])->delete();
        $this->storeSaleInLedger($sale);

            //    $find_sale_account=AccountTransition::where([
            //         'is_cashbook'=>0,
            //         'sale_id'=>$sale->id,
            //     ])->get();
            // foreach($find_sale_account as $k=>$f){

            //     if($f->sub_account_status=='sale_account'){
            //       $sale_account= $f->update([
            //             'sub_account_id' => $this->sale_account,
            //             'transition_date' => $sale->invoice_date,
            //             'sale_id' => $sale->id,
            //             'customer_id'=>$sale->customer_id,
            //             'vochur_no'=>$sale->invoice_no,
            //             'description'=>'',
            //             'is_cashbook' => 0,
            //             'debit' => $sale->total_amount,
            //             'created_by' => Auth::user()->id,
            //             'updated_by' => Auth::user()->id,
            //         ]);
            //     }
            //     if($f->sub_account_status=='cash_sale' || $f->sub_account_status=='sale_advance'){
            //         if($sale->payment_type=='cash' ||  ($sale->payment_type=='credit' && $sale->pay_amount!=0)){
            //             $f->update([
            //                'sub_account_id' => $sale->payment_type=='cash' ? $this->cash_sale : $this->sale_advance,
            //                'credit' => $sale->pay_amount,
            //                'sub_account_status'=>$sale->payment_type=='cash' ? 'cash_sale' : 'sale_advance',
            //            ]);
            //            // $sale_type=$sale_account->replicate()->fill([
            //            //     'sub_account_id' => $sale->payment_type=='cash' ? $sale_common_account_id : $sub_account_id,
            //            //     'credit' => $sale->pay_amount,
            //            //     'debit'=>null,
            //            // ]);
            //            // $sale_type->save();
            //        }
            //     }
            //         if($sale->cash_discount!=null || $sale->cash_discount!=0){
            //             if($f->sub_account_status=='discount_allowed'){
            //                 $f->update([
            //                     'sub_account_id' => $this->discount_allowed,
            //                     'credit' => $sale->cash_discount,
            //                     'debit'=>null,
            //                 ]);
            //             }
            //             else{
            //                 $discount_acc=AccountTransition::create([
            //                     'sub_account_id' => $this->discount_allowed,
            //                     'sub_account_status'=>'discount_allowed',
            //                     'transition_date' => $sale->invoice_date,
            //                     'sale_id' => $sale->id,
            //                     'customer_id'=>$sale->customer_id,
            //                     'vochur_no'=>$sale->invoice_no,
            //                     'description'=>'',
            //                     'is_cashbook' => 0,
            //                     'credit' => $sale->cash_discount,
            //                     'created_by' => Auth::user()->id,
            //                     'updated_by' => Auth::user()->id,
            //                 ]);
            //             }
            //         }
            // }






    }
    // ******************************End sale in  Ledger ***********************************

    // ******************************Start sale collection in  Ledger ***********************************
    public function storeSaleCollectionInLedger($credit_collection, $request)
    {
        $discounts = array_sum($request->discounts);
        $sale_account = AccountTransition::create([
            'sub_account_id' => $this->credit_collection,
            'account_group_id' => $request->account_group,
            'cash_bank_sub_account_id' => $request->cash_bank_account,
            'transition_date' => $credit_collection->collection_date,
            'sale_id' => $credit_collection->id,
            'customer_id' => $credit_collection->customer_id,
            'vochur_no' => $credit_collection->collection_no,
            'description' => '',
            'is_cashbook' => 0,
            'status' => 'credit_collection',
            'debit' => $credit_collection->total_paid_amount,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
        if ($discounts != null || $discounts != 0) {
            $discount_acc = $sale_account->replicate()->fill([
                'sub_account_id' => $this->discount_allowed,
                'account_group_id' => $request->account_group,
                'cash_bank_sub_account_id' => $request->cash_bank_account,
                'debit' => $discounts,
                'status' => 'discount_allowed',
            ]);
            $discount_acc->save();
        }
    }
    public function updateSaleCollectionInLedger($credit_collection, $request)
    {
        AccountTransition::where([
            ['is_cashbook', 0],
            ['sale_id', $credit_collection->id],
            ['status', 'credit_collection'],
        ])->delete();
        $this->storeSaleCollectionInLedger($credit_collection, $request);
    }
    // ******************************End sale collection in  Ledger ***********************************\


    // ******************************Start Purchase  in  Ledger ***********************************
    public function storePurchaseInLedger($p)
    {
      
        $p_account = AccountTransition::create([
            'sub_account_id' => $this->purchase_account,
            'account_group_id' => $p->account_group_id,
            'cash_bank_sub_account_id' => $p->sub_account_id,
            'transition_date' => $p->invoice_date,
            'purchase_id' => $p->id,
            'supplier_id' => $p->supplier_id,
            'vochur_no' => $p->invoice_no,
            'description' => '',
            'is_cashbook' => 0,
            'status' => 'purchase',
            'debit' => (int)$p->pay_amount+ (int)$p->balance_amount,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
        if ($p->payment_type == 'cash' ||  ($p->payment_type == 'credit' && $p->pay_amount != 0)) {
            $p_type = $p_account->replicate()->fill([
                'sub_account_id' => $p->payment_type == 'cash' ? $this->cash_purchase : $this->purchase_advance,
                // 'credit' => $p->payment_type == 'cash' ? $p->pay_amount:(int)$p->pay_amount+ (int)$p->balance_amount,
                'account_group_id' => $p->account_group_id,
                'cash_bank_sub_account_id' => $p->sub_account_id,
                'credit' => $p->pay_amount,
                'status' => 'purchase',
                'debit' => null,
            ]);
            $p_type->save();
        }
    }
    public function updatePurchaseInLedger($p)
    {
        AccountTransition::where([
            ['is_cashbook', 0],
            ['purchase_id', $p->id],
            ['status', 'purchase'],
        ])->delete();
        $this->storePurchaseInLedger($p);
    }
    public function storeCreditPaymentInLedger($cp, $request)
    {
        // dd($cp->total_pay_amount);
        $discounts = array_sum($request->discounts);
        $credit_payment = AccountTransition::create([
            'sub_account_id' => $this->credit_payment,
            'account_group_id'=>$request->account_group,
            'cash_bank_sub_account_id' => $cp->sub_account_id,
            'transition_date' => $cp->collection_date,
            'purchase_id' => $cp->id,
            'supplier_id' => $cp->supplier_id,
            'vochur_no' => $cp->collection_no,
            'description' => '',
            'is_cashbook' => 0,
            'status' => 'credit_payment',
            'credit' => $cp->total_paid_amount,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
        if ($discounts != null || $discounts != 0) {
            $discount_acc = $credit_payment->replicate()->fill([
                'sub_account_id' => $this->discount_received,
                'credit' => $discounts,
                'status' => 'discount_received',
            ]);
            $discount_acc->save();
        }
    }
    public function updateCreditPaymentInLedger($cp, $request)
    {
        /**AccountTransition::where([
            ['is_cashbook', 0],
            ['purchase_id', $cp->id],
        ])->orWhere('status','credit_payment')
        ->orWhere('status','discount_received')
        ->delete();**/
        AccountTransition::where('purchase_id',$cp->id)
                            ->where('is_cashbook', 0)
                            ->where(function($query) {
                                $query->orWhere('sub_account_id',7)
                                      ->orWhere('sub_account_id',15);
                            })->delete();
        $this->storeCreditPaymentInLedger($cp, $request);
    }
    public function storePaymentInLedger($payment){
        $ag = SubAccount::find($payment->credit->id);
        $ag_id = $ag->account_group_id;
        $p=AccountTransition::create([
            'sub_account_id' => $payment->debit->id,
            'account_group_id' => $ag_id,
            'cash_bank_sub_account_id' => $payment->credit->id,
            'transition_date' => $payment->date,
            'payment_id' => $payment->id,
            'vochur_no'=>$payment->cash_payment_no,
            'is_cashbook' => 0,
            'status'=>'payment',
            'debit' => $payment->amount,
            'description' => $payment->remark,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
    }
    public function updatePaymentInLedger($payment){
        AccountTransition::where([
            ['is_cashbook', 0],
            ['payment_id', $payment->id],
            ['status', 'payment'],
        ])->delete();
        $this->storePaymentInLedger($payment);
    }
    public function storeJournalEntryInLedger($entry){
        $ag = SubAccount::find($entry->credit->id);
        $ag_id = $ag->account_group_id;
        $jd=AccountTransition::create([
            'sub_account_id' => $entry->debit->id,
            'account_group_id' => $ag_id,
            'cash_bank_sub_account_id' => $entry->credit->id,
            'transition_date' => $entry->date,
            'journal_entry_id' => $entry->id,
            'vochur_no'=>$entry->journal_entry_no,
            'is_cashbook' => 0,
            'status'=>'journal_entry',
            'debit' => $entry->amount,
            'description' => $entry->remark,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        $ag1 = SubAccount::find($entry->debit->id);
        $ag_id1 = $ag1->account_group_id;
        $jc=AccountTransition::create([
            'sub_account_id' => $entry->credit->id,
            'account_group_id' => $ag_id1,
            'cash_bank_sub_account_id' => $entry->debit->id,
            'transition_date' => $entry->date,
            'journal_entry_id' => $entry->id,
            'vochur_no'=>$entry->journal_entry_no,
            'is_cashbook' => 0,
            'status'=>'journal_entry',
            'credit' => $entry->amount,
            'description' => $entry->remark,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
    }
    public function updateJournalEntryInLedger($entry){
        AccountTransition::where([
            ['is_cashbook', 0],
            ['journal_entry_id', $entry->id],
            ['status', 'journal_entry'],
        ])->delete();
        $this->storeJournalEntryInLedger($entry);
    }
    public function storeReceiptInLedger($receipt){
        $ag = SubAccount::find($receipt->debit->id);
        $ag_id = $ag->account_group_id;
        $r=AccountTransition::create([
            'sub_account_id'=>$receipt->credit->id,
            'account_group_id' => $ag_id,
            'cash_bank_sub_account_id' => $receipt->debit->id,
            'transition_date'=>$receipt->date,
            'receipt_id'=>$receipt->id,
            'vochur_no'=>$receipt->cash_receipt_no,
            'status'=>'receipt',
            'is_cashbook'=>0,
            'credit'=>$receipt->amount,
            'description'=>$receipt->remark,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        // if($r){
        //     $r1=$r->replicate()->fil([
        //     'sub_account_id'=>$receipt->debit->id,
        //     'credit'=>$receipt->amount,
        //     ]);
        //     $r->save();
        // }
    }
    public function updateReceiptInLedger($receipt){
        AccountTransition::where([
            ['is_cashbook', 0],
            ['receipt_id', $receipt->id],
            ['status', 'receipt'],
        ])->delete();
        $this->storeReceiptInLedger($receipt);
    }
    public function getOpenigBalanceForLedger($request,$date){
        // dd($date);
        $opening=AccountTransition::whereDate('transition_date','<',$date)->where('is_cashbook',0);
        // if($request->sub_account!=null){
        //     $opening->where('sub_account_id',$request->sub_account);
        // }
        if($request->type=='Other'){
            /**$opening->when(!is_null($request->sub_account),function($q){
                return  $q->where('sub_account_id',request('sub_account'));
             });**/
            $opening->whereNull('customer_id');
            $opening->whereNull('supplier_id');
            $opening->when(!is_null($request->sub_account),function($q){
                
                $sub_acc_id = request('sub_account');
                return $q->where(function($query) use ($sub_acc_id) {
                            $query->Where('sub_account_id',$sub_acc_id)
                                  ->orWhere('cash_bank_sub_account_id',$sub_acc_id);
                        });
                //return  $q->where('sub_account_id',request('sub_account'));
             });
        }
        if($request->type=='Customer'){
            $opening->whereNotNull('customer_id');
            $opening->when(!is_null($request->customer_id),function($q){
                return  $q->where('customer_id',request('customer_id'));
             });
            }
            if($request->type=='Supplier'){
                $opening->whereNotNull('supplier_id');
                $opening->when(!is_null($request->supplier_id),function($q){
                    return  $q->where('supplier_id',request('supplier_id'));
                 });
                }
        if($request->vochur_no!=null){
            $opening->where('vochur_no',$request->vochur_no);
        }
        if(!empty($request->account_group)) {
            $acc_group = explode(',', request('account_group'));
            $opening->whereIn('account_group_id',$acc_group);
        }
        $opening=$opening->get();
        if($opening->isNotEmpty()){
            $op_debit=$op_credit=0;
            foreach($opening as $op){
                // if($op->sub_account_id==$this->sale_account){
                //     $op_credit+=$op->credit;
                //     $op_debit+=$op->debit;
                // }elseif($op->sub_account_id==$this->sale_advance){
                //     $op_debit+=$op->debit;
                //     $op_credit+=$op->credit;
                // }elseif($op->sub_account_id==$this->credit_collection){
                //     $op_debit+=$op->debit;
                //     $op_credit+=$op->credit;
                // }elseif($op->sub_account_id==$this->discount_allowed){
                //     $op_debit+=$op->debit;
                // }elseif($op->sub_account_id==$this->purchase_account){
                //     $op_debit+=$op->debit;
                //     $op_credit+=$op->credit;
                // }elseif($op->sub_account_id==$this->purchase_advance){
                //     $op_credit+=$op->credit;
                //     $op_debit+=$op->debit;
                // }elseif($op->sub_account_id==$this->credit_payment){
                //     $op_credit+=$op->credit;
                //     $op_debit+=$op->debit;
                // }elseif($op->sub_account_id==$this->discount_received){
                //     $op_credit+=$op->credit;
                //     $op_debit+=$op->debit;
                // }
               
                if($request->type=="Customer"){
                    $op_debit+=abs($op->credit);
                    $op_credit+=abs($op->debit);
                }elseif($request->type=='Supplier'){
                    $op_debit+=abs($op->credit);
                    $op_credit+=abs($op->debit);
                }
                else{
                    $op_debit+=abs($op->debit);
                    $op_credit+=abs($op->credit);
                }
               
            }
            $opening_balance=$op_debit-$op_credit;
        }else{
            $opening_balance=0;
        }
        // dd($opening_balance);
        $this->opening_balance=$opening_balance;
        // return $opening_balance;
    }
    function getDatesFromRange($request,$format = 'Y-m-d') { 
        $start=$request->from_date;
        $end=$request->to_date!=null ? $request->to_date : now()->today();
        // Declare an empty array 
        $array = array(); 
        // Variable that store the date interval 
        // of period 1 day 
        $interval = new DateInterval('P1D'); 
  
        $realEnd = new DateTime($end); 
        $realEnd->add($interval); 
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
        // Use loop to store date into array 
        foreach($period as $date) {                  
            $array[] = $date->format($format);  
        }
        // dd($array);
        // Return the array elements 
        return $array; 
    } 
    public function getQuery($request,$date){
        $query=AccountTransition::with('cash_bank_subaccount')->where('is_cashbook',0);
            $query->when(!is_null($date),function($q)use($date){
               return  $q->where('transition_date','=',$date);
            });
            // $query->when(!is_null($date),function($q){
            //     return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
            //  });
            //  $today=Carbon::today();
            //  $query->when(is_null($request->to),function($q)use($today){
            //     return  $q->whereBetween('transition_date',[request('from_date'),$today]);
            //  });
            //  $query->when(!is_null($request->invoice_no),function($q){
            //     return  $q->where('vochur_no',request('invoice_no'));
            //  });
            //  $query->when(!is_null($request->invoice_no),function($q){
            //     return  $q->where('vochur_no',request('invoice_no'));
            //  });
        if(!empty($request->account_group)) {
            $acc_group = explode(',', $request->account_group);
            $query->whereIn('account_group_id',$acc_group);
        }
        if($request->type=='Other'){
            // dd($request->sub_account);
            $query->whereNull('customer_id');
            $query->whereNull('supplier_id');
            $query->when(!is_null($request->sub_account),function($q){
               // return  $q->where('sub_account_id',request('sub_account'));
                $sub_acc_id = request('sub_account');
                return $q->where(function($query) use ($sub_acc_id) {
                            $query->Where('sub_account_id',$sub_acc_id)
                                  ->orWhere('cash_bank_sub_account_id',$sub_acc_id);
                        });
             });
            //  dd($query->get());
             return $query=$query->get();
        }elseif($request->type=='Supplier'){
            $query->whereNotNull('supplier_id');
            $query->when(!is_null($request->supplier_id),function($q){
                return  $q->where('supplier_id',request('supplier_id'));
             });
             return $query=$query->get();
            //  $this->filterBySupplier($q);
        }elseif($request->type=='Customer'){
            $query->whereNotNull('customer_id');
            $query->when(!is_null($request->customer_id),function($q){
                return  $q->where('customer_id',request('customer_id'));
             });
             return $query=$query->get();
            //  $total_debit=$total_credit=0;
            //  foreach($query as $d){
            //      $total_debit+=$d->debit;
            //      $total_credit+=$d->credit;
            // }
            // $this->filterByCustomer($request,$query);
            // return response()->json([
            //     'ledger'=>$ledger,
            //     'total_debit'=>$total_debit,
            //     'total_credit'=>$total_credit,
            // ]);
        }
        // return $query;
    }
    public function getClosingForLedger($request,$a,$opening_balance){
        $ac=AccountTransition::where('is_cashbook',0);
        $ac->whereDate('transition_date',$a);

        if($request->type=='Other'){
            // dd($request->sub_account);
            $ac->whereNull('customer_id');
            $ac->whereNull('supplier_id');
            //  dd($query->get());
        }elseif($request->type=='Supplier'){
            $ac->whereNotNull('supplier_id');
            //  $this->filterBySupplier($q);
        }elseif($request->type=='Customer'){
            $ac->whereNotNull('customer_id');
        }

        $ac->when(!is_null($request->sub_account),function($q){
            //return  $q->where('sub_account_id',request('sub_account'));
            $sub_acc_id = request('sub_account');
            return $q->where(function($query) use ($sub_acc_id) {
                    $query->Where('sub_account_id',$sub_acc_id)
                          ->orWhere('cash_bank_sub_account_id',$sub_acc_id);
                });
         });
         $ac->when(!is_null($request->customer_id),function($q){
            return  $q->where('customer_id',request('customer_id'));
         });
         $ac->when(!is_null($request->supplier_id),function($q){
            return  $q->where('supplier_id',request('supplier_id'));
         });
         $ac->when(!is_null($request->account_group),function($q){
            $acc_group = explode(',', request('account_group'));
            return  $q->whereIn('account_group_id',$acc_group);
         });
//        if($request->sub_account!=null){
//            $ac->where('sub_account_id',$request->sub_account);
//        }
//        if($request->vochur_no!=null){
//            $ac->where('vochur_no',$request->vochur_no);
//        }
//        if($request->from_date!= null && $request->to_date!=null ){
//            $ac->whereDate('transition_date','>=',$request->from_date)
//                ->whereDate('transition_date','<=',$request->to_date)->where('is_cashbook',1);
//        }
        $closing=$ac->get();
        // dd($opening_balance);
        if($closing->isNotEmpty()){
            $cl_debit=$cl_credit=0;
            foreach($closing as $c){
                if($request->type=='Customer'){
                    $cl_debit+=abs($c->credit);
                    $cl_credit+=abs($c->debit);
                }elseif ($request->type=='Other'){
                    $cl_debit+=abs($c->debit);
                    $cl_credit+=abs($c->credit);
                }elseif($request->type=='Supplier'){
                    $cl_debit+=abs($c->credit);
                    $cl_credit+=abs($c->debit);
                }
            }
            $closing_balance=(($opening_balance)+$cl_debit)-$cl_credit;
        }else{
            $closing_balance=0;
        }
        return $closing_balance;
    }
    public function getLedgerList($request){
        $date_arr=$this->getDatesFromRange($request);
        $this->getOpenigBalanceForLedger($request,$date_arr[0]);
        foreach($date_arr as $key=>$date){
            $ledger[$key]=new stdClass();
            $ats=$this->getQuery($request,$date);
            if(isset($ats) && $ats->isNotEmpty()){
                $op_bal=$this->opening_balance;
                $total_credit =0;
                $total_debit  =0;
                foreach($ats as $k=>$at){
                    $total_debit += abs($at->debit);
                    $total_credit += abs($at->credit);
                }
                $ledger[$key]->total_credit=$total_credit;
                $ledger[$key]->total_debit=$total_debit;
                $ledger[$key]->date=$date;
                $ledger[$key]->opening_balance =$op_bal;
                $ledger[$key]->closing_balance = $this->getClosingForLedger($request,$date, $op_bal);
                // dd($this->getClosingForLedger($request,$date, $op_bal));
                $ledger[$key]->ledger_list= $ats;
                $this->opening_balance=$ledger[$key]->closing_balance;
            }
            elseif(isset($ats) && $ats->isNotEmpty() && count($date_arr)>1){
                // dd('Hell00');
                $ledger[$key]->date=$date;
                $ledger[$key]->opening_balance =$ledger[$key-1]->opening_balance;
                $ledger[$key]->closing_balance = $ledger[$key-1]->closing_balance;
                $ledger[$key]->ledger_list= [];
            }
            else{
                // $is_from_date=AccountTransition::whereDate('transition_date','<',$d)->where('is_cashbook',1)->latest()->first();
                $opening_balance = $this->opening_balance;
                $total_debit=0;
                $total_credit=0;
                $ledger[$key]->date=$date;
                $ledger[$key]->opening_balance =$opening_balance;
                $ledger[$key]->closing_balance = $opening_balance;
                $ledger[$key]->ledger_list= [];
            }
            // $sts=AccountTransition::whereDate('transition_date',$date)->where('is_cashbook',0);
        }
        // dd($ledger);
        return $ledger;
    }

    public function storePurchaseCreditNoteForLedger($credit_note){
        /**$description="Inv ".$credit_note->credit_note_no.",Inv Date ".$credit_note->credit_note_date." to " .$credit_note->supplier->name;
        $sub_account_id=config('global.debit_note');
        AccountTransition::create([
            'sub_account_id' => $sub_account_id,
            'transition_date' => $credit_note->credit_note_date,
            'debit_note_id' => $credit_note->id,
            'is_cashbook' => 0,
            'description'=>$description,
            'status'=>'debit_note',
            'supplier_id'=>$credit_note->supplier_id,
            'vochur_no'=>$credit_note->credit_note_no,
            'credit' => $credit_note->amount,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);**/
        $description="Inv ".$credit_note->credit_note_no.",Inv Date ".$credit_note->credit_note_date." to " .$credit_note->supplier->name;
        //$sub_account_id=config('global.debit_note');
        $sub_acc = DB::table('sub_accounts')
                    ->where('sub_account_name', 'Debit')->first();
        $sub_account_id=$sub_acc->id;
        AccountTransition::create([
            'sub_account_id' => $sub_account_id,
            'account_group_id' => $credit_note->account_group,
            'cash_bank_sub_account_id' => $credit_note->sub_account_id,
            'transition_date' => $credit_note->credit_note_date,
            'debit_note_id' => $credit_note->id,
            'is_cashbook' => 0,
            'description'=>$description,
            'status'=>'debit_note',
            'supplier_id'=>$credit_note->supplier_id,
            'vochur_no'=>$credit_note->credit_note_no,
            //'credit' => $credit_note->amount,
            'credit' => $credit_note->amount,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        //added by ep
        if($credit_note->credit_note_type_id == 0) {
            AccountTransition::create([
                'sub_account_id' => 13,
                'account_group_id' => $credit_note->account_group,
                'cash_bank_sub_account_id' => $credit_note->sub_account_id,
                'transition_date' => $credit_note->credit_note_date,
                'debit_note_id' => $credit_note->id,
                'is_cashbook' => 0,
                'description'=>$description,
                'status'=>'debit_note',
                'supplier_id'=>$credit_note->supplier_id,
                'vochur_no'=>$credit_note->credit_note_no,
                //'debit' => $credit_note->amount,
                'debit' => $credit_note->amount,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
        }
    }
    public function updatePurchaseCreditNoteForLedger($credit_note){
        /**$description="Inv ".$credit_note->credit_note_no.",Inv Date ".$credit_note->credit_note_date." to " .$credit_note->supplier->name;
        AccountTransition::where([
            ['debit_note_id',$credit_note->id],
            ['is_cashbook',0],
            ['status','debit_note'],
        ])->update([
            'transition_date'=>$credit_note->credit_note_date,
            'description'=>$description,
            'debit'=>$credit_note->amount,
        ]);**/
        $description="Inv ".$credit_note->credit_note_no.",Inv Date ".$credit_note->credit_note_date." to " .$credit_note->supplier->name;
        //$sub_account_id=config('global.debit_note');
        $sub_acc = DB::table('sub_accounts')
                    ->where('sub_account_name', 'Debit')->first();
        $sub_account_id=$sub_acc->id;
        AccountTransition::where([
            ['debit_note_id',$credit_note->id],
            ['is_cashbook',0],
            ['status','debit_note'],
            ['sub_account_id',$sub_account_id],
        ])->update([
            'transition_date'=>$credit_note->credit_note_date,
            'account_group_id' => $credit_note->account_group,
            'cash_bank_sub_account_id' => $credit_note->sub_account_id,
            'description'=>$description,
            //'credit'=>$credit_note->amount,
            'credit'=>$credit_note->amount,
        ]);
        //added by ep
        if($credit_note->credit_note_type_id == 0) {
            AccountTransition::where([
                ['debit_note_id',$credit_note->id],
                ['is_cashbook',0],
                ['status','debit_note'],
                ['sub_account_id',13],
            ])->update([
                'transition_date'=>$credit_note->credit_note_date,
                'account_group_id' => $credit_note->account_group,
                'cash_bank_sub_account_id' => $credit_note->sub_account_id,
                'description'=>$description,
               // 'debit'=>$credit_note->amount,
                'debit'=>$credit_note->amount,
            ]);
        }
    }
}

