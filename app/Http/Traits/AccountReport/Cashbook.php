<?php
namespace App\Http\Traits\AccountReport;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\AccountTransition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait Cashbook{
    // public function __construct(){
        // $this->purchase_account=config('global.purchase'); 
        // $this->cash_purchase=config('global.cash_purchase'); 
        // $this->purchase_advance=config('global.purchase_advance'); 
        // $this->discount_received=config('global.discount_received'); 
        // $this->credit_payment=config('global.credit_payment'); 
    // }
    public function getDateArr_original($request){
        $from = \Carbon\Carbon::createFromFormat('Y-m-d',$request->from_date);
        if(!is_null($request->to_date)){
            $to = \Carbon\Carbon::createFromFormat('Y-m-d',$request->to_date);
        }else{
            $to = Carbon::today();
        }
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
        return $date_arr;
    }

    public function getDateArr($request){
        $st_date = $request->from_date;
        if(!is_null($request->to_date)){
          $ed_date = $request->to_date;
        } else {
          $ed_date = Carbon::now()->format('Y-m-d');  
        }
        
       $period = CarbonPeriod::create($st_date, $ed_date);

       $dates = array();
        // Iterate over the period
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }
        return $dates;
    }
    
    public function getOpening($request,$a){
        $opening=AccountTransition::whereDate('transition_date','<',$a)->where('is_cashbook',1);
        if($request->sub_account!=null){
            $opening->where('sub_account_id',$request->sub_account);
        }
        if($request->vochur_no!=null){
            $opening->where('vochur_no',$request->vochur_no);
        }
        $opening=$opening->get();
        if($opening->isNotEmpty()){
            $op_debit=$op_credit=0;
            foreach($opening as $op){
                $op_debit+=$op->debit;
                $op_credit+=$op->credit;
            }
            $opening_balance=$op_debit-$op_credit;
        }else{
            $opening_balance=0;
        }
        return $opening_balance;

    }
    public function getClosing($request,$a,$opening_balance){
        $ac=AccountTransition::where('is_cashbook',1);
        $ac->whereDate('transition_date',$a);
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
        if($closing->isNotEmpty()){
            $cl_debit=$cl_credit=0;
            foreach($closing as $c){
                $cl_debit+=$c->debit;
                $cl_credit+=$c->credit;
            }
            $closing_balance=($opening_balance+$cl_debit)-$cl_credit;
        }else{
            $closing_balance=0;
        }
        return $closing_balance;
    }
    public function getCashBookList($request){
        $date_arr=$this->getDateArr($request);
        foreach($date_arr as $key=>$d){
            $cashbook[$key]=new \stdClass();
            $account_transition=AccountTransition::whereDate('transition_date',$d)->where('is_cashbook',1);
            if($request->vochur_no!= null){
                $account_transition->where('vochur_no',$request->vochur_no);
            }if($request->sub_account!=null) {
                $account_transition->where('sub_account_id',$request->sub_account);
            }
            if($request->vochur_no !=null || $request->sub_account!=null) {
                $cashbook[$key]->hide=true;
            }
                $account_transition=$account_transition->get();
            if($account_transition->isNotEmpty()){
                $opening_balance = $this->getOpening($request,$d);
                $total_credit = 0;
                $total_debit = 0;
                foreach($account_transition as $k=>$at){
                    $total_debit += $at->debit;
                    $total_credit += $at->credit;

                }
                $cashbook[$key]->total_credit=$total_credit;
                $cashbook[$key]->total_debit=$total_debit;
                $cashbook[$key]->date=$d;
                $cashbook[$key]->opening_balance =$opening_balance;
                $cashbook[$key]->closing_balance = $this->getClosing($request,$d, $opening_balance);
                $cashbook[$key]->cashbook_list= $account_transition;
            } elseif($account_transition->isNotEmpty() && count($date_arr)>1){
                $cashbook[$key]->date=$d;
                $cashbook[$key]->opening_balance =$cashbook[$key-1]->closing_balance;
                $cashbook[$key]->closing_balance = $cashbook[$key-1]->closing_balance;
                $cashbook[$key]->cashbook_list= [];
            }else{
                // $is_from_date=AccountTransition::whereDate('transition_date','<',$d)->where('is_cashbook',1)->latest()->first();
                $opening_balance = $this->getOpening($request,$d);
                $total_debit=0;
                $total_credit=0;
                $cashbook[$key]->date=$d;
                $cashbook[$key]->opening_balance =$opening_balance;
                    $cashbook[$key]->closing_balance = $opening_balance;
                $cashbook[$key]->cashbook_list= [];
            }
        }
        return $cashbook;
    }
    // public function storePurchaseInLedger($p){
    //     $p_account=AccountTransition::create([
    //         'sub_account_id' => $this->purchase_account,
    //         'transition_date' => $p->invoice_date,
    //         'p_id' => $p->id,
    //         'supplier_id'=>$p->supplier_id,
    //         'vochur_no'=>$p->invoice_no,
    //         'description'=>'',
    //         'is_cashbook' => 0,
    //         'status'=>'purchase',
    //         'credit' => $p->pay_amount,
    //         'created_by' => Auth::user()->id,
    //         'updated_by' => Auth::user()->id,
    //     ]);
    //     if($p->payment_type=='cash' ||  ($p->payment_type=='credit' && $p->pay_amount!=0)){
    //         $p_type=$p_account->replicate()->fill([
    //             'sub_account_id' => $p->payment_type=='cash' ? $this->cash_purchase : $this->purchase_advance,
    //             'debit' => $p->pay_amount,
    //             'status'=>'purchase',
    //             'credit'=>null,
    //         ]);
    //         $p_type->save();
    //     }
    // }
    // public function updatePurchaseInLedger($p){
    //     AccountTransition::where([
    //         ['is_cashbook',0],
    //         ['purchase_id',$p->id],
    //         ['status','purchase'],
    //     ])->delete();
    //     $this->storetPurchaseInLedger($p);
    // }
    // public function storeCreditPaymentInLedger($cp,$request){
    //     $discounts=array_sum($request->discounts);
    //     $credit_payment=AccountTransition::create([
    //         'sub_account_id' => $this->credit_payment,
    //         'transition_date' => $cp->collection_date,
    //         'purchase_id' => $cp->id,
    //         'supplier_id'=>$cp->supplier_id,
    //         'vochur_no'=>$cp->collection_no,
    //         'description'=>'',
    //         'is_cashbook' => 0,
    //         'status'=>'credit_payment',
    //         'debit' => $cp->total_pay_amount,
    //         'created_by' => Auth::user()->id,
    //         'updated_by' => Auth::user()->id,
    //     ]);
    //     if($discounts!=null || $discounts!=0){
    //         $discount_acc=$credit_payment->replicate()->fill([
    //             'sub_account_id' => $this->discount_allowed,
    //             'debit' => $discounts,
    //             'status'=>'discount_received',
    //         ]);
    //         $discount_acc->save();
    //     }
    // }
    // public function updateCreditPaymentInLedger($cp,$request){
    //     AccountTransition::where([
    //         ['is_cashbook',0],
    //         ['purchase_id',$cp->id],
    //         ['status','credit_payment'],
    //     ])->delete();
    //     $this->storetCreditPaymentInLedger($cp,$request);
    // }
    
}