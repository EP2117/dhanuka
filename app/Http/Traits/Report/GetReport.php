<?php
namespace App\Http\Traits\Report;
use App\Sale;
use stdClass;
use App\Product;
use Carbon\Carbon;
use App\SubAccount;
use App\AccountHead;
use App\PurchaseInvoice;
use App\AccountTransition;
use App\CollectionPurchase;
use App\PurchaseCollection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

trait GetReport{
   
    public function getPaymentReport($request){
        $route_name=Route::currentRouteName();
//        dd($request->all());
        $payments =DB::table('collection_purchase')
            ->select(DB::raw("collection_purchase.*, credit_purchase_collections.collection_no,credit_purchase_collections.collection_date,purchase_invoices.invoice_date, purchase_invoices.invoice_no,credit_purchase_collections.supplier_id, purchase_invoices.branch_id,suppliers.state_id,suppliers.township_id,suppliers.name as supplier_name,branches.branch_name"))
            ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'collection_purchase.purchase_id')
//            ->leftjoin('products', 'products.id', '=', 'product_purchase.product_id')
            ->leftjoin('credit_purchase_collections', 'credit_purchase_collections.id', '=', 'collection_purchase.purchase_collection_id')
//            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftjoin('suppliers', 'suppliers.id', '=', 'credit_purchase_collections.supplier_id')
            ->leftjoin('states', 'states.id', '=', 'suppliers.state_id')
            ->leftjoin('townships', 'townships.id', '=', 'suppliers.township_id')
//            ->leftjoin('uoms', 'uoms.id', '=', 'product_purchase.uom_id')
            ->leftjoin('branches', 'branches.id', '=', 'credit_purchase_collections.branch_id')->orderBy('credit_purchase_collections.collection_date','asc');
//        $payment=CollectionPurchase::orderBy('id','asc')->get();
//        dd($payments);
        if($request->payment_no!=null){
            $payments->where('credit_purchase_collections.collection_no',$request->payment_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $payments->whereBetween('credit_purchase_collections.collection_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $payments->whereDate('credit_purchase_collections.collection_date', '>=', $request->from_date);
        }else if($request->to_date != '') {
            $payments->whereDate('credit_purchase_collections.collection_date', '<=', $request->to_date);
        }
        if($request->branch_id!=null){
            $payments->where('credit_purchase_collections.branch_id',$request->branch_id);
        }
        if($request->supplier_id!=null){
            $payments->where('credit_purchase_collections.supplier_id',$request->supplier_id);
        }
        if($request->currency_id!=1 && $request->currency_id!=""){
            $payments->where('credit_purchase_collections.currency_id',$request->currency_id);
        }
        if($request->state_id!=null){
            $payments->where('suppliers.state_id',$request->state_id);
        }
        if($request->township_id!=null){
            $payments->where('suppliers.township_id',$request->township_id);
        }
        $payments=$payments->get();
        if($route_name=='credit_payment_export'){
            return $payments;
        }
        $total_paid=0;
        $total_discount=0;
        $html="";
        foreach($payments as $payment) {
            if($payment->discount==null){
                $payment->discount=0;
            }
            if($payment->paid_amount==null){
                $payment->padi_amount=0;
            }
            
            $html .= '<tr><td class="text-center"></td><td class="text-center">'.$payment->collection_date.'</td><td class="text-center">'.$payment->collection_no.'</td>';
            $html .= '<td class="text-center">'.$payment->invoice_date.'</td><td>'.$payment->invoice_no.'</td>';
            $html .= '<td class="mm-txt">'.$payment->supplier_name.'</td>';
            if($request->currency_id!=1 && $request->currency_id!="")
            {
                $html .= '<td class="text-center">'. (float)$payment->paid_amount_fx .'</td>';
                $html .= '<td class="mm-txt">'.(float)$payment->discount_fx .'</td>';
                $total_discount+=(float)$payment->discount_fx;
                $total_paid+=(float)$payment->paid_amount_fx; 
            } else {
                $html .= '<td class="text-center">'.$payment->paid_amount.'</td>';
                $html .= '<td class="mm-txt">'.$payment->discount.'</td>';

                $total_discount+=$payment->discount;
                $total_paid+=$payment->paid_amount;   
            }
            $html .= '</tr>';
        }


        $html .= '<tr><td colspan ="6" style="text-align: right;"><strong>Total</strong></td><td class="text-center">'.$total_paid.'</td><td>'.$total_discount.'</td></tr>';
        return $html;
//
    }
//     public function getDateArr($request){
//         $to = \Carbon\Carbon::createFromFormat('Y-m-d',$request->from_date);
//         $from = \Carbon\Carbon::createFromFormat('Y-m-d',$request->to_date);

//         $count_of_days = $to->diffInDays($from);

//         $from_date=new Carbon($request->from_date);
//         $to_date=new Carbon($request->to_date);
//         $from_year=$from_date->format('Y');  $from_day=$from_date->format('d'); $from_month=$from_date->format('m');
//         $to_year=(int)$from_date->format('Y');  $to_day=(int)$to_date->format('d'); $to_month=(int)$to_date->format('m');
//         $date_arr=[];
//         $t=$count_of_days+(int)$from_day;
//         for($i=(int)$from_day ; $i <=$t;$i++){
//             $date = $from_year . '-' . $from_month . '-' . $i;
//             array_push($date_arr,$date);
//         }
//         return $date_arr;
//     }
//     public function getOpening($request,$a){
//         $opening=AccountTransition::whereDate('transition_date','<',$a)->where('is_cashbook',1);
//         if($request->sub_account!=null){
//             $opening->where('sub_account_id',$request->sub_account);
//         }
//         if($request->vochur_no!=null){
//             $opening->where('vochur_no',$request->vochur_no);
//         }
//         $opening=$opening->get();
//         if($opening->isNotEmpty()){
//             $op_debit=$op_credit=0;
//             foreach($opening as $op){
//                 $op_debit+=$op->debit;
//                 $op_credit+=$op->credit;
//             }
//             $opening_balance=$op_debit-$op_credit;
//         }else{
//             $opening_balance=0;
//         }
//         return $opening_balance;

//     }
//     public function getClosing($request,$a,$opening_balance){
//         $ac=AccountTransition::where('is_cashbook',1);
//         $ac->whereDate('transition_date',$a);

// //        if($request->sub_account!=null){
// //            $ac->where('sub_account_id',$request->sub_account);
// //        }
// //        if($request->vochur_no!=null){
// //            $ac->where('vochur_no',$request->vochur_no);
// //        }
// //        if($request->from_date!= null && $request->to_date!=null ){
// //            $ac->whereDate('transition_date','>=',$request->from_date)
// //                ->whereDate('transition_date','<=',$request->to_date)->where('is_cashbook',1);
// //        }
//         $closing=$ac->get();
//         if($closing->isNotEmpty()){
//             $cl_debit=$cl_credit=0;
//             foreach($closing as $c){
//                 $cl_debit+=$c->debit;
//                 $cl_credit+=$c->credit;
//             }
//             $closing_balance=($opening_balance+$cl_debit)-$cl_credit;
//         }else{
//             $closing_balance=0;
//         }
//         return $closing_balance;
//     }
//     public function getCashBookList($request){
//         $date_arr=$this->getDateArr($request);
//         foreach($date_arr as $key=>$d){
//             $cashbook[$key]=new \stdClass();
//             $account_transition=AccountTransition::whereDate('transition_date',$d)->where('is_cashbook',1);
//             if($request->vochur_no!= null){
//                 $account_transition->where('vochur_no',$request->vochur_no);
//             }if($request->sub_account!=null) {
//                 $account_transition->where('sub_account_id',$request->sub_account);
//             }
//             if($request->vochur_no !=null || $request->sub_account!=null) {
//                 $cashbook[$key]->hide=true;
//             }
//                 $account_transition=$account_transition->get();
//             if($account_transition->isNotEmpty()){
//                 $opening_balance = $this->getOpening($request,$d);
//                 $total_credit = 0;
//                 $total_debit = 0;
//                 foreach($account_transition as $k=>$at){
//                     $total_debit += $at->debit;
//                     $total_credit += $at->credit;

//                 }
//                 $cashbook[$key]->total_credit=$total_credit;
//                 $cashbook[$key]->total_debit=$total_debit;
//                 $cashbook[$key]->date=$d;
//                 $cashbook[$key]->opening_balance =$opening_balance;
//                 $cashbook[$key]->closing_balance = $this->getClosing($request,$d, $opening_balance);
//                 $cashbook[$key]->cashbook_list= $account_transition;
//             } elseif($account_transition->isNotEmpty() && count($date_arr)>1){
//                 $cashbook[$key]->date=$d;
//                 $cashbook[$key]->opening_balance =$cashbook[$key-1]->closing_balance;
//                 $cashbook[$key]->closing_balance = $cashbook[$key-1]->closing_balance;
//                 $cashbook[$key]->cashbook_list= [];
//             }else{
                
//                 // $is_from_date=AccountTransition::whereDate('transition_date','<',$d)->where('is_cashbook',1)->latest()->first();
//                 $opening_balance = $this->getOpening($request,$d);
//                 $total_debit=0;
//                 $total_credit=0;
//                 $cashbook[$key]->date=$d;
//                 $cashbook[$key]->opening_balance =$opening_balance;
//                     $cashbook[$key]->closing_balance = $opening_balance;
//                 $cashbook[$key]->cashbook_list= [];
//             }
//         }
//         return $cashbook;
//     }
    public function getPurchaseOutStandingReport_sc($request){
        $sup=PurchaseInvoice::where('payment_type','credit');
        if($request->supplier_id!=null){
            $sup->where('supplier_id',$request->supplier_id);
        }
        if($request->invoice_no!=null){
            $sup->where('invoice_no',$request->invoice_no);
        }
        if($request->currency_id!=1 && $request->currency_id != ""){           
            $sup->where('currency_id',$request->currency_id);
        }
        if($request->branch_id!=null){
            $sup->where('branch_id',$request->branch_id);
        }
        if($request->state_id){
            $state=$request->state_id;
            $sup->whereHas('supplier',function($q)use($state){
                $q->where('state_id',$state);
            });
        }
        if($request->township_id){
            $town=$request->township_id;
            $sup->whereHas('supplier',function($q)use($town){
                $q->where('township_id',$town);
            });
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $sup->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $sup->whereDate('invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $sup->whereDate('invoice_date', '<=', $request->to_date);
        } else {
            // $data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }
        $sup=$sup->groupBy('supplier_id')->get();
        if($sup->isNotEmpty()){
            foreach($sup as $key=>$s){
                // dd($s);
                $per_gain_loss_amt=$per_inv_amt=$per_paid_amt=$per_bal_amt=0;
                $p_outstandings[$key]=new \stdClass();
                $invoices=PurchaseInvoice::with('payments')->where('supplier_id',$s->supplier_id)->where('payment_type','credit');
                // dd($invoices);
                if($request->from_date != '' && $request->to_date != '')
                {
                    $invoices->whereBetween('invoice_date', array($request->from_date, $request->to_date));
                } else if($request->from_date != '') {
                    $invoices->whereDate('invoice_date', '>=', $request->from_date);

                }else if($request->to_date != '') {
                    $invoices->whereDate('invoice_date', '<=', $request->to_date);
                } else {
                    // $data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
                }
                if($request->invoice_no!=null){
                    $invoices->where('invoice_no',$request->invoice_no);
                }

                if($request->currency_id!=1 && $request->currency_id != ""){
                    $invoices->where('currency_id',$request->currency_id);
                }
                $invoices=$invoices->get();
                // dd($invoices);
                // if($invoices->isNotEmpty()){
                    foreach($invoices as $k=>$i){
                        // dd($invoices[1]);
                        // dd($i); 
                        $gain_loss = 0;
                        $gain_amt = 0;
                        $loss_amt = 0;
                        if(!empty($i->payments)) {
                           // dd($i->payments);
                            $gain = 0;
                            $loss = 0;
                            foreach($i->payments as $pay) {
                                $gain += $pay->pivot->gain_amount; 
                                $gain_amt += $pay->pivot->gain_amount; 
                                $loss += $pay->pivot->loss_amount; 
                                $loss_amt += abs($pay->pivot->loss_amount); 
                            }
                            $gain_loss = $gain + $loss;
                        }

                        //$gain_loss = $gain_loss == 0 ? '' : $gain_loss;
                        /**if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                                $invoices[$k]->type="paid";
                        }**/
                        if($i->currency_id == 1) {
                            if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                                $invoices[$k]->type="paid";
                            }
                        } else {
                            if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                $invoices[$k]->type="paid";
                            }  
                        }

                        if($request->currency_id!=1 && $request->currency_id != ""){

                            $i->t_paid_amount=$i->pay_amount_fx+$i->collection_amount_fx;
                            $i->t_balance_amount=$i->balance_amount_fx-$i->collection_amount_fx;
                            $i->t_balance_amount = (round($i->t_balance_amount, 3) - $gain_amt) + $loss_amt;
                            $i->t_balance_amount = round($i->t_balance_amount, 3);
                            $i->t_gain_loss_amount=0;
                              if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                $per_gain_loss_amt =0; 
                                $t_amount = $i->total_amount_fx - $i->discount_fx;
                                $per_inv_amt+=$t_amount; 
                                $per_paid_amt+=$i->pay_amount_fx+$i->collection_amount_fx;$per_bal_amt+=$i->balance_amount_fx-$i->collection_amount_fx;
                            } 
                        } else {
                            $i->t_paid_amount=$i->pay_amount+$i->collection_amount;
                            //$i->t_balance_amount=$i->balance_amount-$i->collection_amount;
                            $i->t_gain_loss_amount=$gain_loss;

                            if($i->currency_id == 1) {
                                $i->t_balance_amount=$i->balance_amount-$i->collection_amount;                              
                                if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                                    $per_gain_loss_amt+=$gain_loss; 
                                    $t_amount = $i->total_amount - $i->discount;
                                    $per_inv_amt+=$t_amount; 
                                    $per_paid_amt+=$i->pay_amount+$i->collection_amount;
                                    //$per_bal_amt+=$i->balance_amount-$i->collection_amount;
                                    $per_bal_amt+=$i->t_balance_amount;
                                }
                            } else {
                                //$i->t_balance_amount=(abs($i->t_gain_loss_amount) + ($i->total_amount - $i->discount) ) - $i->t_paid_amount;
                                //$i->t_balance_amount = round($i->t_balance_amount, 2);
                                $i->t_balance_amount=(($i->total_amount - $i->discount) ) - $i->t_paid_amount;
                                $i->t_balance_amount = (round($i->t_balance_amount, 3) - $gain_amt) + $loss_amt;
                                $i->t_balance_amount = round($i->t_balance_amount, 3);
                              if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                    $per_gain_loss_amt+=$gain_loss; 
                                    $t_amount = $i->total_amount - $i->discount;
                                    $per_inv_amt+=$t_amount; 
                                    $per_paid_amt+=$i->pay_amount+$i->collection_amount;
                                   // $per_bal_amt+=$i->balance_amount-$i->collection_amount;
                                    $per_bal_amt+=$i->t_balance_amount;
                                }  
                            }
                        }
                        // }
                        // $net_inv_amt+=$i->total_amount; $net_paid_amt+=$i->paid_amount;$net_balance_amt+=$i->balance_amount;
                    }

                    $p_outstandings[$key]->out_list=$invoices;
                    // if($per_bal_amt!=0){
                        $p_outstandings[$key]->total_inv_amt=$per_inv_amt;
                        $p_outstandings[$key]->total_paid_amt=$per_paid_amt;
                        $p_outstandings[$key]->total_bal_amt=round($per_bal_amt,3);
                        $p_outstandings[$key]->total_gain_loss_amt=$per_gain_loss_amt;
                // }
                
            }
            // dd($p_outstandings);
            // return $p_outstandings;
        }else{
           $p_outstandings=[];
        }
        return $p_outstandings;
       
    }

    public function getPurchaseOutStandingReport($request){
        $sup=PurchaseInvoice::where('payment_type','credit');
        if($request->supplier_id!=null){
            $sup->where('supplier_id',$request->supplier_id);
        }
        if($request->invoice_no!=null){
            $sup->where('invoice_no',$request->invoice_no);
        }
        if($request->currency_id!=1 && $request->currency_id != ""){           
            $sup->where('currency_id',$request->currency_id);
        }
        if($request->branch_id!=null){
            $sup->where('branch_id',$request->branch_id);
        }
        if($request->state_id){
            $state=$request->state_id;
            $sup->whereHas('supplier',function($q)use($state){
                $q->where('state_id',$state);
            });
        }
        if($request->township_id){
            $town=$request->township_id;
            $sup->whereHas('supplier',function($q)use($town){
                $q->where('township_id',$town);
            });
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $sup->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $sup->whereDate('invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $sup->whereDate('invoice_date', '<=', $request->to_date);
        } else {
            // $data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }
        $sup=$sup->groupBy('supplier_id')->get();
        if($sup->isNotEmpty()){
            foreach($sup as $key=>$s){
                // dd($s);
                $per_gain_loss_amt=$per_inv_amt=$per_paid_amt=$per_bal_amt=0;
                $p_outstandings[$key]=new \stdClass();
                $invoices=PurchaseInvoice::with('payments')->where('supplier_id',$s->supplier_id)->where('payment_type','credit');
                // dd($invoices);
                if($request->from_date != '' && $request->to_date != '')
                {
                    $invoices->whereBetween('invoice_date', array($request->from_date, $request->to_date));
                } else if($request->from_date != '') {
                    $invoices->whereDate('invoice_date', '>=', $request->from_date);

                }else if($request->to_date != '') {
                    $invoices->whereDate('invoice_date', '<=', $request->to_date);
                } else {
                    // $data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
                }
                if($request->invoice_no!=null){
                    $invoices->where('invoice_no',$request->invoice_no);
                }

                if($request->currency_id!=1 && $request->currency_id != ""){
                    $invoices->where('currency_id',$request->currency_id);
                }
                $invoices=$invoices->get();
                // dd($invoices);
                // if($invoices->isNotEmpty()){
                    foreach($invoices as $k=>$i){
                        // dd($invoices[1]);
                        // dd($i); 
                        $gain_loss = 0;
                        $gain_amt = 0;
                        $loss_amt = 0;
                        if(!empty($i->payments)) {
                           // dd($i->payments);
                            $gain = 0;
                            $loss = 0;
                            foreach($i->payments as $pay) {
                                $gain += $pay->pivot->gain_amount; 
                                $gain_amt += $pay->pivot->gain_amount; 
                                $loss += $pay->pivot->loss_amount; 
                                $loss_amt += abs($pay->pivot->loss_amount); 
                            }
                            $gain_loss = $gain + $loss;
                        }

                        //$gain_loss = $gain_loss == 0 ? '' : $gain_loss;
                        /**if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                                $invoices[$k]->type="paid";
                        }**/
                        if($i->currency_id == 1) {
                            if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                                $invoices[$k]->type="paid";
                            }
                        } else {
                            if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                $invoices[$k]->type="paid";
                            }  
                        }

                        if($request->currency_id!=1 && $request->currency_id != ""){

                            $i->t_paid_amount=$i->pay_amount_fx+$i->collection_amount_fx;
                            $i->t_balance_amount=$i->balance_amount_fx-$i->collection_amount_fx;
                           // $i->t_balance_amount = (round($i->t_balance_amount, 3) + $gain_amt) - $loss_amt;
                            $i->t_balance_amount = round($i->t_balance_amount, 3);
                            $i->t_gain_loss_amount=0;
                              if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                $per_gain_loss_amt =0; 
                                $t_amount = $i->total_amount_fx - $i->discount_fx;
                                $per_inv_amt+=$t_amount; 
                                $per_paid_amt+=$i->pay_amount_fx+$i->collection_amount_fx;$per_bal_amt+=$i->balance_amount_fx-$i->collection_amount_fx;
                            } 
                        } else {
                            $i->t_paid_amount=$i->pay_amount+$i->collection_amount;
                            //$i->t_balance_amount=$i->balance_amount-$i->collection_amount;
                            $i->t_gain_loss_amount=$gain_loss;

                            if($i->currency_id == 1) {
                                $i->t_balance_amount=$i->balance_amount-$i->collection_amount;                              
                                if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                                    $per_gain_loss_amt+=$gain_loss; 
                                    $t_amount = $i->total_amount - $i->discount;
                                    $per_inv_amt+=$t_amount; 
                                    $per_paid_amt+=$i->pay_amount+$i->collection_amount;
                                    //$per_bal_amt+=$i->balance_amount-$i->collection_amount;
                                    $per_bal_amt+=$i->t_balance_amount;
                                }
                            } else {
                                //$i->t_balance_amount=(abs($i->t_gain_loss_amount) + ($i->total_amount - $i->discount) ) - $i->t_paid_amount;
                                //$i->t_balance_amount = round($i->t_balance_amount, 2);
                                $i->t_balance_amount=(($i->total_amount - $i->discount) ) - $i->t_paid_amount;
                                $i->t_balance_amount = (round($i->t_balance_amount, 3) + $gain_amt) - $loss_amt;
                                $i->t_balance_amount = round($i->t_balance_amount, 3);
                              if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                    $per_gain_loss_amt+=$gain_loss; 
                                    $t_amount = $i->total_amount - $i->discount;
                                    $per_inv_amt+=$t_amount; 
                                    $per_paid_amt+=$i->pay_amount+$i->collection_amount;
                                   // $per_bal_amt+=$i->balance_amount-$i->collection_amount;
                                    $per_bal_amt+=$i->t_balance_amount;
                                }  
                            }
                        }
                        // }
                        // $net_inv_amt+=$i->total_amount; $net_paid_amt+=$i->paid_amount;$net_balance_amt+=$i->balance_amount;
                    }

                    $p_outstandings[$key]->out_list=$invoices;
                    // if($per_bal_amt!=0){
                        $p_outstandings[$key]->total_inv_amt=$per_inv_amt;
                        $p_outstandings[$key]->total_paid_amt=$per_paid_amt;
                        $p_outstandings[$key]->total_bal_amt=round($per_bal_amt,3);
                        $p_outstandings[$key]->total_gain_loss_amt=$per_gain_loss_amt;
                // }
                
            }
            // dd($p_outstandings);
            // return $p_outstandings;
        }else{
           $p_outstandings=[];
        }
        return $p_outstandings;
       
    }
    public function getSaleOutstandingReport($request){
        ini_set('memory_limit','512M');
        // dd($request->all());
        //$cus=Sale::where('payment_type','credit')->whereRaw('(total_amount-(IFNULL(cash_discount,0) + pay_amount + collection_amount)) > 0');
        if(isset($request->currency_id) && $request->currency_id!=1){ 
            $cus=Sale::with('products')->where('payment_type','credit')->whereRaw('(total_amount_fx-(IFNULL(cash_discount_fx,0) + pay_amount_fx + collection_amount_fx)) > 0');
        } else {
           $cus=Sale::where('payment_type','credit')->whereRaw('(total_amount-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0'); 
        }
        $cus->leftjoin('customers', 'customers.id', '=', 'sales.customer_id');
        $cus->leftjoin('states', 'states.id', '=', 'customers.state_id');
        $cus->leftjoin('townships', 'townships.id', '=', 'customers.township_id');
       // $cus=Sale::where('payment_type','credit');
        if($request->customer_id!=null){
            $cus->where('customer_id',$request->customer_id);
        }
        if($request->sale_man_id!=null){
            $cus->where('office_sale_man_id',$request->sale_man_id);
        }
        if($request->currency_id!=1 && $request->currency_id != ""){           
            $cus->where('currency_id',$request->currency_id);
        }
        if($request->invoice_no!=null){
            $cus->where('invoice_no',$request->invoice_no);
        }
        if($request->invoice_type!=''){
            $cus->where('invoice_type',$request->invoice_type);
        }
        if($request->due_date!=''){
            $cus->whereDate('due_date',$request->due_date);
        }
        if($request->sale_man_id != "") {
            $cus->where('office_sale_man_id', $request->sale_man_id);
        }
        if($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            $cus->whereHas('products',function($q)use($request){
               $binds = array(strtolower($request->product_name));
               $q->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
            });
            
        }
        if($request->branch_id!=null){
            $cus->where('branch_id',$request->branch_id);
        }
        if($request->state_id){
            $state=$request->state_id;
            $cus->whereHas('customer',function($q)use($state){
                $q->where('state_id',$state);
            });
        }
        if($request->township_id){
            $town=$request->township_id;
            $cus->whereHas('customer',function($q)use($town){
                $q->where('township_id',$town);
            });
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $cus->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $cus->whereDate('invoice_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $cus->whereDate('invoice_date', '<=', $request->to_date);
        } else {
            // $data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }
        //$cus=$cus->groupBy('customer_id')->toSql();
        $cus=$cus->groupBy('customer_id')->orderBy('states.state_name')->orderBy('townships.township_name')->get();
       // dd($cus);
        if($cus->isNotEmpty()){
            foreach($cus as $key=>$s){
                // dd($s);
                $per_gain_loss_amt=$per_inv_amt=$per_paid_amt=$per_bal_amt=0;
                $p_outstandings[$key]=new \stdClass();
                $invoices=Sale::with('collections','customer.township','products')->where('customer_id',$s->customer_id)->where('payment_type','credit');
                if($request->invoice_no!=null){
                    $invoices->where('invoice_no',$request->invoice_no);
                }
                /**if($request->sale_man_id!=null){
                    $invoices->where('office_sale_man_id',$request->sale_man_id);
                }**/

                if($request->invoice_type!=''){
                    $invoices->where('invoice_type',$request->invoice_type);
                }
                if($request->due_date!=''){
                    $invoices->whereDate('due_date',$request->due_date);
                }
                if($request->sale_man_id != "") {
                    $invoices->where('office_sale_man_id', $request->sale_man_id);
                }
                if($request->product_name != "") {
                    //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
                    $invoices->whereHas('products',function($q)use($request){
                       $binds = array(strtolower($request->product_name));
                       $q->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
                    });
                    
                }
                if($request->from_date != '' && $request->to_date != '')
                {
                    $invoices->whereBetween('invoice_date', array($request->from_date, $request->to_date));
                } else if($request->from_date != '') {
                    $invoices->whereDate('invoice_date', '>=', $request->from_date);

                }else if($request->to_date != '') {
                    $invoices->whereDate('invoice_date', '<=', $request->to_date);
                } else {
                    // $data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
                }
                if($request->currency_id!=1 && $request->currency_id != ""){
                    $invoices->where('currency_id',$request->currency_id);
                }
                $invoices=$invoices->get();
                foreach($invoices as $k=>$i){
                    /***kaung (commenty by ep)
                    if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                        $invoices[$k]->type="paid";
                    }
                    
                    $i->t_paid_amount=$i->pay_amount+$i->collection_amount;
                    $i->t_balance_amount=$i->balance_amount-$i->collection_amount;
                    if(($i->collection_amount+$i->discount)!=$i->balance_amount){
                        if($i->is_opening == 1) {
                            $per_inv_amt+=$i->total_amount; 
                        } else {
                            $per_inv_amt+=$i->net_total;     
                        }
                        
                        $per_paid_amt+=$i->pay_amount+$i->collection_amount;$per_bal_amt+=$i->balance_amount-$i->collection_amount;
                    }****/

                    $today = Carbon::now()->format('Y-m-d');
                    $invoices[$k]->current_date = $today;
                    $gain_loss = 0;
                    $gain_amt = 0;
                    $loss_amt = 0;
                    if(!empty($i->collections)) {
                       // dd($i->payments);
                        $gain = 0;
                        $loss = 0;
                        foreach($i->collections as $pay) {
                            $gain += $pay->pivot->gain_amount; 
                            $gain_amt += $pay->pivot->gain_amount; 
                            $loss += $pay->pivot->loss_amount; 
                            $loss_amt += abs($pay->pivot->loss_amount); 
                        }
                        $gain_loss = $gain + $loss;
                    }

                     //EP Start
                    if($i->currency_id == 1) {
                        if(($i->collection_amount+$i->return_amount+$i->customer_return_amount+$i->discount)!=$i->balance_amount){
                            $invoices[$k]->type="paid";
                        }
                    } else {

                        if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                            $invoices[$k]->type="paid";
                        }

                        /***if($i->total_amount_fx-($i->cash_discount_fx + $i->pay_amount_fx + $i->collection_amount_fx) != 0){
                            $invoices[$k]->type="paid";
                        }***/
                    }
                    if($invoices[$k]->type=="paid") {
                    if($request->currency_id!=1 && $request->currency_id != ""){
                        $i->t_paid_amount=$i->pay_amount_fx+$i->collection_amount_fx;
                        $i->t_balance_amount=$i->balance_amount_fx-$i->collection_amount_fx;
                        $i->t_gain_loss_amount=0;
                        if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                            /**if($i->is_opening == 1) {
                                $per_inv_amt+=$i->total_amount; 
                            } else {
                                $per_inv_amt+=$i->net_total;     
                            }**/
                            $per_gain_loss_amt =0; 
                            $per_inv_amt= $per_inv_amt + $i->net_total_fx + $i->tax_amount_fx;
                            
                            $per_paid_amt+=$i->pay_amount_fx+$i->collection_amount_fx;
                            $per_bal_amt+=$i->balance_amount_fx-$i->collection_amount_fx;
                        }
                    } else {
                        $i->t_paid_amount=$i->pay_amount+$i->collection_amount+$i->return_amount+$i->customer_return_amount;
                        $i->t_gain_loss_amount=$gain_loss;

                        if($i->currency_id == 1) {
                            $i->t_balance_amount=$i->balance_amount-($i->collection_amount+$i->return_amount+$i->customer_return_amount);
                            if(($i->collection_amount+$i->return_amount+$i->customer_return_amount+$i->discount)!=$i->balance_amount){
                                if($i->is_opening == 1) {
                                    $per_inv_amt+=$i->total_amount; 
                                } else {
                                    $taxAmt = $i->tax_amount == NULL ? 0 : $i->tax_amount;
                                    $per_inv_amt = $per_inv_amt + $i->net_total + $taxAmt;     
                                }
                                $per_gain_loss_amt+=$gain_loss;
                                $per_paid_amt+=$i->pay_amount+$i->collection_amount+$i->return_amount+$i->customer_return_amount;
                                $per_bal_amt+=$i->balance_amount-($i->collection_amount+$i->return_amount+$i->customer_return_amount);
                            }
                        } 
                        else {
                            $i->t_balance_amount=$i->balance_amount-($i->collection_amount+$i->return_amount+$i->customer_return_amount);
                            $i->t_balance_amount = (round($i->t_balance_amount, 3) + $gain_amt) - $loss_amt;
                            $i->t_balance_amount = round($i->t_balance_amount, 3);
                            if(($i->collection_amount_fx+$i->discount_fx)!=$i->balance_amount_fx){
                                $per_gain_loss_amt+=$gain_loss; 
                                if($i->is_opening == 1) {
                                    $per_inv_amt+=$i->total_amount; 
                                } else {
                                    //$per_inv_amt+=$i->net_total; 
                                    $taxAmt = $i->tax_amount == NULL ? 0 : $i->tax_amount;
                                    $per_inv_amt = $per_inv_amt + $i->net_total + $taxAmt;    
                                }
                                //$per_gain_loss_amt+=$gain_loss;
                                $per_paid_amt+=$i->pay_amount+$i->collection_amount+$i->return_amount+$i->customer_return_amount;
                                $per_bal_amt+=$i->balance_amount-($i->collection_amount+$i->return_amount+$i->customer_return_amount);
                            }
                        } 
                    }
                }
                    //EP End

                    // $net_inv_amt+=$i->total_amount; $net_paid_amt+=$i->paid_amount;$net_balance_amt+=$i->balance_amount;
                }
                // $invoices[$key]->per_inv_amt=$per_inv_amt;
                // $invoices[$key]->per_paid_amt=$per_paid_amt;
                // $invoices[$key]->per_bal_amt=$per_bal_amt;
                $p_outstandings[$key]->state_id=$s->state_id;
                $p_outstandings[$key]->state_name=$s->customer->state->state_name;
                $p_outstandings[$key]->township_id=$s->township_id;
                $p_outstandings[$key]->township_name=$s->customer->township->township_name;
                $p_outstandings[$key]->out_list=$invoices;
                $p_outstandings[$key]->total_inv_amt=$per_inv_amt;
                $p_outstandings[$key]->total_paid_amt=$per_paid_amt;
                $p_outstandings[$key]->total_bal_amt=$per_bal_amt;
                $p_outstandings[$key]->total_gain_loss_amt=$per_gain_loss_amt;
            }
            // dd($p_outstandings);
            // return $p_outstandings;
        }else{
            $p_outstandings=[];
        }
        return $p_outstandings;
    }
    public function getCreditCollection($request){
        $route_name=Route::currentRouteName();
                $collections =DB::table('collection_sale')
                    ->select(DB::raw("collection_sale.*, collections.collection_no,collections.collection_date,sales.invoice_date,collections.collect_type, sales.invoice_no,collections.customer_id, sales.branch_id,customers.state_id,customers.township_id,customers.cus_name as customer_name,branches.branch_name,sale_men.sale_man"))
                    ->leftjoin('sales', 'sales.id', '=', 'collection_sale.sale_id')
        //            ->leftjoin('products', 'products.id', '=', 'product_purchase.product_id')
                    ->leftjoin('collections', 'collections.id', '=', 'collection_sale.collection_id')
        //            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')
                    ->leftjoin('customers', 'customers.id', '=', 'collections.customer_id')
                    ->leftjoin('sale_men', 'sale_men.id', '=', 'sales.office_sale_man_id')
                    ->leftjoin('states', 'states.id', '=', 'customers.state_id')
                    ->leftjoin('townships', 'townships.id', '=', 'customers.township_id')
        //            ->leftjoin('uoms', 'uoms.id', '=', 'product_purchase.uom_id')
                    ->leftjoin('branches', 'branches.id', '=', 'collections.branch_id')->orderBy('collections.collection_date','asc');
        //        $payment=CollectionPurchase::orderBy('id','asc')->get();
            //    dd($collections->get());
                if($request->collection_no!=null){
                    $collections->where('collections.collection_no',$request->collection_no);
                }
                if($request->from_date != '' && $request->to_date != '')
                {
                    $collections->whereBetween('collections.collection_date', array($request->from_date, $request->to_date));
                } else if($request->from_date != '') {
                    $collections->whereDate('collections.collection_date', '>=', $request->from_date);
                }else if($request->to_date != '') {
                    $collections->whereDate('collections.collection_date', '<=', $request->to_date);
                }
                if($request->sale_man_id!=null){
                    $collections->where('sales.office_sale_man_id',$request->sale_man_id);
                }
                if($request->collect_type!=null){
                    $collections->where('collections.collect_type',$request->collect_type);
                }
                if($request->branch_id!=null){
                    $collections->where('collections.branch_id',$request->branch_id);
                }
                if($request->customer_id!=null){
                    $collections->where('collections.customer_id',$request->customer_id);
                }
                if($request->state_id!=null){
                    $collections->where('customers.state_id',$request->state_id);
                }
                if($request->township_id!=null){
                    $collections->where('customers.township_id',$request->township_id);
                }
                if($request->currency_id!=1 && $request->currency_id!=""){
                    $collections->where('collections.currency_id',$request->currency_id);
                }
                $collections=$collections->get();
                if($route_name=='credit_collection_export'){
                    return $collections;
                }
                // dd($collections);
        //        if($request->supplier_id!=null){
        //            $payments->where('collection_no',$request->payment_no);
        //        }
        //        if($request->invoice_no!=null){
        //            $payments->where('collection_no',$request->payment_no);
        //        }
                $total_paid=0;
                $total_discount=0;
                $html="";
                foreach($collections as $c) {
                    if($c->discount==null){
                        $c->discount=0;
                    }
                    if($c->paid_amount==null){
                        $c->padi_amount=0;
                    }
                    //$total_discount+=$c->discount;
                    //$total_paid+=$c->paid_amount;
                    $html .= '<tr><td class="text-center"></td><td class="text-center">'.$c->collection_date.'</td><td class="text-center">'.$c->collection_no.'</td>';
                    $html .= '<td class="text-center">'.$c->invoice_date.'</td><td>'.$c->invoice_no.'</td>';
                    $html .= '<td class="mm-txt">'.$c->customer_name.'</td>';
                    $html .= '<td class="mm-txt">'.$c->sale_man.'</td>';
                    if($request->currency_id!=1 && $request->currency_id!="") {
                        $html .= '<td class="text-center">'.(float)$c->paid_amount_fx.'</td>';
                        $html .= '<td class=" text-center mm-txt">'.(float)$c->discount_fx.'</td>';
                        $total_discount+=(float)$c->discount_fx;
                        $total_paid+=(float)$c->paid_amount_fx; 
                    } else {
                        $html .= '<td class="text-center">'.$c->paid_amount.'</td>';
                        $html .= '<td class=" text-center mm-txt">'.$c->discount.'</td>'; 
                        $total_discount+=$c->discount;
                        $total_paid+=$c->paid_amount;  
                    }
                    $html .= '<td class=" text-center mm-txt">'.$c->collect_type.'</td>';
                    $html .= '</tr>';
                }
        
        
                $html .= '<tr><td colspan ="7" style="text-align: right;"><strong>Total</strong></td><td class="text-center">'.$total_paid.'</td><td class="text-center">'.$total_discount.'</td><td></td></tr>';
                return $html;
            }
    public function getValuation($request){
        $where="";
        $p_where="";
        $s_where="";
        if($request->date != '') {
            $where.= "product_transitions.transition_date <= '".$request->date."'";
            $p_where.= "purchase_invoices.invoice_date <= '".$request->date."'"; 
            $s_where.= "sales.invoice_date <= '".$request->date."'"; 
        } else{
            $today=Carbon::now()->today();
            $where= "product_transitions.transition_date <= '".$today."'";
            $p_where= "purchase_invoices.invoice_date <= '".$today."'";
            $s_where= "sales.invoice_date <= '".$today."'";
        }
        $products = DB::table("products")
        ->where('products.is_active',1)
        ->select(DB::raw("products.id as product_id,pt.transition_date,ps.s_valuation_amount,pt.cost_price,pt.in_cost_price,products.purchase_price,ps.s_qty,pp.p_valuation_amount,pt.entry_qty,products.product_name,products.minimum_qty, products.brand_id,pt.warehouse_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name, pt.in_qty,pt.out_qty"))
        ->leftjoin(DB::raw("(SELECT product_id,product_quantity,transition_type,transition_purchase_id, warehouse_id, transition_date, branch_id,
                         SUM(CASE  WHEN transition_type = 'in'  AND transition_entry_id IS NOT NULL THEN product_quantity  ELSE 0 END) as entry_qty,
                         SUM(CASE  WHEN transition_type = 'in' AND transition_entry_id IS NULL THEN product_quantity  ELSE 0 END) as in_qty,
                         SUM(CASE  WHEN transition_type = 'out' AND ( transition_sale_id || transition_adjustment_id || transition_transfer_id)  IS NOT NULL THEN cost_price  ELSE 0 END) as cost_price,
                         SUM(CASE  WHEN transition_type = 'in'  AND (transition_adjustment_id IS NOT NULL OR transition_transfer_id IS NOT NULL OR transition_sale_id IS NOT NULL OR transition_return_id IS NOT NULL) THEN cost_price ELSE 0 END ) as in_cost_price,
                         SUM(CASE  WHEN transition_type = 'out'  THEN product_quantity  ELSE 0 END) as out_qty
                         FROM product_transitions Where ".$where." 
                          GROUP BY product_transitions.product_id
                           )as pt"),function($join){
                        $join->on("pt.product_id","=","products.id");
                   })
                  /** ->leftjoin(DB::raw("(SELECT product_id,SUM(product_purchase.total_amount) as p_valuation_amount
                    FROM product_purchase LEFT JOIN purchase_invoices ON purchase_invoices.id = product_purchase.product_id WHERE ".$p_where."
                   GROUP BY product_purchase.product_id
                   ) as pp"),function($join){
                       $join->on("pp.product_id","=","products.id");
                   })**/
                    ->leftjoin(DB::raw("(SELECT product_purchase.product_id,SUM(product_purchase.total_amount) as p_valuation_amount
                    FROM purchase_invoices LEFT JOIN product_purchase ON product_purchase.purchase_id = purchase_invoices.id WHERE ".$p_where."
                   GROUP BY product_purchase.product_id
                   ) as pp"),function($join){
                       $join->on("pp.product_id","=","products.id");
                   })
                    ->leftjoin(DB::raw("(SELECT product_id,SUM(total_amount) as s_valuation_amount,product_quantity as s_qty
                       FROM product_sale 
                      GROUP BY product_sale.product_id
                      ) as ps"),function($join){
                          $join->on("ps.product_id","=","products.id");
                      })
                    /**->leftjoin(DB::raw("(SELECT product_sale.product_id,SUM(product_sale.total_amount) as s_valuation_amount,product_quantity as s_qty
                    FROM sales LEFT JOIN product_sale ON product_sale.sale_id = sales.id WHERE ".$s_where."
                   GROUP BY product_sale.product_id
                   ) as ps"),function($join){
                       $join->on("ps.product_id","=","products.id");
                   })**/
	    		->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')
	    		->leftjoin('brands', 'brands.id', '=', 'products.brand_id')
                ->leftjoin('categories', 'categories.id', '=', 'products.category_id');
        if($request->product_name != "") {
            $products->where('products.product_name', 'LIKE', "%$request->product_name%");
        }
      
        if($request->brand_id != "") {
            $products->where('products.brand_id', $request->brand_id);
        }
        if($request->product_code != "") {
            $products->where('products.product_code', $request->product_code);
        }
        if($request->category_id != "") {
            $products->where('products.category_id', $request->category_id);
        }
        /*if($request->warehouse_id != "") {
            $products->where('pt.warehouse_id', $request->warehouse_id);
        }*/
        $data  = $products->orderBy("product_name")->get();
        return $data;
    }
    public function getCostPrice($p_id){
        // dd($p_id);
        $p = DB::table("products")
        ->select(DB::raw("products.id as product_id,ps.s_valuation_amount,pt.cost_price,pt.in_cost_price,products.purchase_price,ps.s_qty, pp.p_valuation_amount,pt.entry_qty,products.product_name,products.minimum_qty, products.brand_id,pt.warehouse_id, products.product_code,uom_id,uoms.uom_name,brands.brand_name,categories.category_name, pt.in_qty,pt.out_qty"))
        ->leftjoin(DB::raw("(SELECT product_id,product_quantity,transition_type,transition_purchase_id, warehouse_id, transition_date, branch_id,
                         SUM(CASE  WHEN transition_type = 'in'  AND transition_entry_id IS NOT NULL THEN product_quantity  ELSE 0 END) as entry_qty,
                         SUM(CASE  WHEN transition_type = 'in' AND transition_entry_id IS NULL THEN product_quantity  ELSE 0 END) as in_qty,
                         SUM(CASE  WHEN transition_type = 'out' AND (transition_sale_id || transition_adjustment_id || transition_transfer_id) IS NOT NULL THEN cost_price  ELSE 0 END) as cost_price,
                         SUM(CASE  WHEN transition_type = 'in'  AND (transition_adjustment_id IS NOT NULL OR transition_transfer_id IS NOT NULL OR transition_sale_id IS NOT NULL OR transition_return_id IS NOT NULL) THEN cost_price ELSE 0 END ) as in_cost_price,
                         SUM(CASE  WHEN transition_type = 'out'  THEN product_quantity  ELSE 0 END) as out_qty
                         FROM product_transitions
                          GROUP BY product_transitions.product_id
                           )as pt"),function($join){
                        $join->on("pt.product_id","=","products.id");
                   })
                   ->leftjoin(DB::raw("(SELECT product_id,SUM(total_amount) as p_valuation_amount
                    FROM product_purchase 
                   GROUP BY product_purchase.product_id
                   ) as pp"),function($join){
                       $join->on("pp.product_id","=","products.id");
                   })
                       ->leftjoin(DB::raw("(SELECT product_id,SUM(total_amount) as s_valuation_amount,product_quantity as s_qty
                       FROM product_sale 
                      GROUP BY product_sale.product_id
                      ) as ps"),function($join){
                          $join->on("ps.product_id","=","products.id");
                      })
                    //   ->leftjoin(DB::raw("(SELECT product_id,product_quantity as pm_qty
                    //   FROM product_mainwarehouse_entry 
                    //  GROUP BY product_mainwarehouse_entry.product_id
                    //  ) as pm"),function($join){
                    //      $join->on("ps.product_id","=","products.id");
                    //  })
	    		->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')
	    		->leftjoin('brands', 'brands.id', '=', 'products.brand_id')
                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')
                ->where('products.id',$p_id)->first();
                // dd($products);
                // dd($p);
                $total_valuation=0;
                // foreach($products as $p){
                    // dd($p->entry_qty);
                    $bal=($p->entry_qty+(int)$p->in_qty)-(int)$p->out_qty;
                
                    $p->balance=$bal;
                    // $p->s_valuation_amount=$p->s_valuation_amount==null ? 0 : (int)$p->s_valuation_amount;
                    $p->p_valuation_amount=$p->p_valuation_amount==null ? 0 :(int)$p->p_valuation_amount;
                    $p->s_qty=$p->s_qty==null?0 :(int)$p->s_qty;
                    // $p->cost_price=$p->cost_price==null?0 :(int)$p->cost_price;
                    $total_valuation+=($p->entry_qty * $p->purchase_price)+(((int)$p->p_valuation_amount+$p->in_cost_price)-(int)$p->cost_price);
                    $p->t_valuation_amount=((int)$p->entry_qty * (int)$p->purchase_price)+(int)(((int)$p->p_valuation_amount+$p->in_cost_price)-(int)$p->cost_price);
                    if($p->t_valuation_amount==0 && $p->balance==0){
                        $p->product_cost_price=0;
                    }else{
                        $p->product_cost_price=(int)$p->t_valuation_amount/$p->balance;
                    }
                    // $p->product_cost_price=(int)$p->t_valuation_amount/$p->balance;

                // }
                return $p;
                // dd($products);
                // $products->where('products.id',7);
        // $data  = $products->orderBy("product_name")->get();
        // return $data;
    }
   
}
