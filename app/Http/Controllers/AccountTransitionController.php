<?php

namespace App\Http\Controllers;

use stdClass;
use PDF;
use App\AccountHead;
use App\SaleReturn;
use App\CustomerReturn;
use App\AccountTransition;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\Report\GetReport;
use Illuminate\Support\Facades\Route;
use App\Http\Traits\AccountReport\Ledger;
use App\Http\Traits\AccountReport\Cashbook;

class AccountTransitionController extends Controller
{
    use Cashbook;
    use Ledger;
    use GetReport;
    public function __construct(){
        $this->total_revenue=0;
        $this->total_cor=0;
        $this->account_head_name=null;
    }
    public function getAllCashbook(Request $request) {
        $date_arr=$this->getDateArr($request);
        $cashbook=$this->getCashBookList($request);
        return response(compact('cashbook'));
    }
    public function getAllLedger(Request $request){
        $ledger=$this->getLedgerList($request);
        return response()->json([
            'ledger'=>$ledger,
            'account_type'=>$request->type,
        ]);
    }
    public function getProfitAndLossReport(Request $request){
        $month = Carbon::now()
        ->year($request->year)
        ->month($request->month);
        // dd($targetDate);
        // dd($request->all());
        // $profit_and_loss=$this->getProfitAndLoss($request);
        $account_head=AccountHead::where('name','Revenue')
        ->orWhere('name','Cost Of Revenue')->get();
        // whereHas('financial_type1',function($q){
        //     $q->where('name','P&L');
        // })->get();               
       foreach($account_head as $key=>$ah) {
                    //kaung (comment by ep)
                   $pl=AccountTransition::where('is_cashbook',0);
                   $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                   $pl->when(!is_null($request->from_date),function($q)use($request){
                       return  $q->whereDate('transition_date','>=',$request->from_date);
                   });
                   $pl->when(!is_null($request->from_date),function($q){
                       return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
                   });
                   if($request->month){
                    $pl->whereMonth('transition_date','=',$request->month);
                }
                $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                if($request->year){
                    $pl->whereYear('transition_date','=',$request->year);
                }
                $pl->where('sub_account_id',8);

                 $pl_sr=SaleReturn::whereNotNull('return_no');
                   $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                   $pl_sr->when(!is_null($request->from_date),function($q)use($request){
                       return  $q->whereDate('return_date','>=',$request->from_date);
                   });
                   $pl_sr->when(!is_null($request->from_date),function($q){
                       return  $q->whereBetween('return_date',[request('from_date'),request('to_date')]);
                   });
                   $pl_sr->where('return_method','=','with invoice');
                   if($request->month){
                    $pl_sr->whereMonth('return_date','=',$request->month);
                }
                $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                if($request->year){
                    $pl_sr->whereYear('return_date','=',$request->year);
                }

                $pl_cr=CustomerReturn::whereNotNull('customer_return_no');
                   $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                   $pl_cr->when(!is_null($request->from_date),function($q)use($request){
                       return  $q->whereDate('return_date','>=',$request->from_date);
                   });
                   $pl_cr->when(!is_null($request->from_date),function($q){
                       return  $q->whereBetween('return_date',[request('from_date'),request('to_date')]);
                   });
                   if($request->month){
                    $pl_cr->whereMonth('return_date','=',$request->month);
                }
                $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                if($request->year){
                    $pl_cr->whereYear('return_date','=',$request->year);
                }
                /**$pl_sr->whereNotNull('return_id');
                $pl_sr->where(function($query) {
                        $query->where('sub_account_id',77)
                              ->orwhere('sub_account_id',78);
                    });**/
               /** $pl_sr->whereNull('return_payment_id');
                $pl_sr->where(function($query) {
                       $query->whereNotNull('return_id')
                              ->orwhereNotNull('customer_return_id');
                    });**/
                $profitAndLoss[$ah->name]=new stdClass();
               if($ah->name=='Revenue'){
                   $pl=$pl->whereHas('sub_account.account_head',function($q)use($ah){
                        $q->whereId($ah->id);
                    })->selectRaw('*, sum(credit) as sale_amount')->groupBy('sub_account_id')->get();
                   if($pl->isNotEmpty()){
                    //$profitAndLoss[$ah->name]=new stdClass();

                       foreach($pl as $k=>$p){
                           $revenue=new stdClass();
                           $revenue->name=$p->sub_account->sub_account_name;
                           $revenue->amount=(int)$p->sale_amount;
                           $this->total_revenue= $p->sale_amount;
                       }
                       $profitAndLoss[$ah->name]->revenue=$revenue;
                   }

                   /**$pl_sr=$pl_sr->whereHas('sub_account.account_head',function($q)use($ah){
                        $q->whereId($ah->id);
                    })->selectRaw('*, sum(debit) as sale_return_amount')->groupBy('sub_account_id')->get();**/
                    $pl_sr=$pl_sr->selectRaw('*, sum(total_amount) as sale_return_amount')->get();

                    $pl_cr=$pl_cr->selectRaw('*, sum(return_amount) as customer_return_amount')->get();
                    $saleReturnAmount  = 0;
                   if($pl_sr->isNotEmpty()){
                   // $profitAndLoss[$ah->name]=new stdClass();

                       foreach($pl_sr as $k=>$p){
                          // $revenue=new stdClass();
                         //  $revenue->name=$p->sub_account->sub_account_name;
                          // $revenue->name="Sale Return Account";
                          // $revenue->amount=(int)$p->sale_return_amount;
                           //$this->total_revenue = $this->total_revenue - $p->sale_return_amount;
                        $saleReturnAmount += $p->sale_return_amount;
                       }
                       
                   }

                   if($pl_cr->isNotEmpty()) {
                       foreach($pl_cr as $k=>$cp){
                        $saleReturnAmount += $cp->customer_return_amount;
                       }
                       
                   }

                  if($pl_sr->isNotEmpty() || $pl_cr->isNotEmpty()){
                     $revenue=new stdClass();
                   //  $revenue->name=$p->sub_account->sub_account_name;
                     $revenue->name="Sale Return Account";
                     $revenue->amount=(int)$saleReturnAmount;
                     $this->total_revenue = $this->total_revenue - $saleReturnAmount;
                     $profitAndLoss[$ah->name]->sale_return=$revenue;
                  }
                   //dd($profitAndLoss);
                //    else{
                //     $profitAndLoss=null;
                //    }
                   // *********** for cost of revenue *********
               }
               else if($ah->name=='Cost of Revenue') 
               {
                   $profitAndLoss[$ah->name]=new stdClass();
                // $pl->selectRaw('*, sum(credit) as sale_amount')->groupBy('sub_account_id')->get();
                   $sale=config('global.sale');
                   $pl=AccountTransition::where('sub_account_id',$sale)->where('is_cashbook',0);
                   //ep
                    /**$pl->where(function($query) {
                        $query->whereNotNull('sale_id')
                              ->orwhereNotNull('return_id');
                    });**/
                    $pl->whereNotNull('sale_id');
                    $pl->whereNull('return_id');
                   //end ep
                   // $pl->whereHas('sale.products',function($q){
                   //     $q->
                   // });
                   $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                  /* $pl->when(!is_null($request->from_date),function($q)use($request){
                       return  $q->whereDate('transition_date','>=',$request->from_date);
                   });*/
                   $pl->when(!is_null($request->from_date),function($q){
                    return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
                });
                   if($request->month){
                    // $pla->whereIn(DB::raw('MONTH(transition_date)'),[$request->month]);
                    $pl->whereMonth('transition_date','=',$request->month);
                }
                 $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                if($request->year){
                    $pl->whereYear('transition_date','=',$request->year);
                }
                
                   $pl=$pl->get();
                //    dd($pl);
                   $amount=0;
                   if($pl->isNotEmpty()){
                    //$profitAndLoss[$ah->name]=new stdClass();
                    //dd($pl);
                    foreach($pl as $p){
                        // $product_sale=DB::table('product_sale')->where('sale_id',$p->sale_id)->get();
                        /**$product_sale=DB::table('product_transitions')
                        ->where('transition_sale_id',$p->sale_id)->get();**/ 

                        //ep 
                        $product_sale=DB::table('product_transitions')
                                    ->where('transition_sale_id',$p->sale_id)
                                    ->whereNull('transition_return_id');

                            $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                                /**$product_sale->when(!is_null($request->from_date),function($q)use($request){
                                   return  $q->whereDate('transition_date','>=',$request->from_date);
                               });**/
                               $product_sale->when(!is_null($request->from_date),function($q){
                                return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
                            });
                               if($request->month){
                                // $pla->whereIn(DB::raw('MONTH(transition_date)'),[$request->month]);
                                $product_sale->whereMonth('transition_date','=',$request->month);
                            }
                             $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                            if($request->year){
                                $product_sale->whereYear('transition_date','=',$request->year);
                            }

                            $product_sale = $product_sale->get();

                        //end

                        foreach($product_sale as $ps){
                            // $product=Product::find($ps->product_id);
                            // $amount+=(int)$product->cost_price* (int)$ps->product_quantity;
                            $amount+=(int)$ps->cost_price;
                        }
                    }
                    /**$cost_of_revenue=new stdClass();
                    $cost_of_revenue->name='Cost Of Good Sold';
                    $cost_of_revenue->amount=$amount;
                    $this->total_cor=$amount;
                    $profitAndLoss[$ah->name]->cost_of_revenue=$cost_of_revenue;**/
                   }

                   //EP Sale Return Start
                  // $pl_sr=AccountTransition::where('sub_account_id',77)->where('is_cashbook',0);
                   //ep
                    $pl_sr=SaleReturn::whereNotNull('return_no');
                   //end ep
                   // $pl->whereHas('sale.products',function($q){
                   //     $q->
                   // });
                   $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                  /* $pl->when(!is_null($request->from_date),function($q)use($request){
                       return  $q->whereDate('transition_date','>=',$request->from_date);
                   });*/
                   $pl_sr->when(!is_null($request->from_date),function($q){
                    return  $q->whereBetween('return_date',[request('from_date'),request('to_date')]);
                });
                   if($request->month){
                    // $pla->whereIn(DB::raw('MONTH(transition_date)'),[$request->month]);
                    $pl_sr->whereMonth('return_date','=',$request->month);
                }
                 $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                if($request->year){
                    $pl_sr->whereYear('return_date','=',$request->year);
                }
                
                   $pl_sr=$pl_sr->get();
                //    dd($pl);
                   $sr_amount=0;
                   if($pl_sr->isNotEmpty()){
                    //$profitAndLoss[$ah->name]=new stdClass();
                    //dd($pl);
                    foreach($pl_sr as $p){
                        // $product_sale=DB::table('product_sale')->where('sale_id',$p->sale_id)->get();
                        /**$product_sale=DB::table('product_transitions')
                        ->where('transition_sale_id',$p->sale_id)->get();**/ 

                        //ep 
                        $product_sale=DB::table('product_transitions')
                                    ->where('transition_return_id',$p->id);

                            $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
                                /**$product_sale->when(!is_null($request->from_date),function($q)use($request){
                                   return  $q->whereDate('transition_date','>=',$request->from_date);
                               });**/
                               $product_sale->when(!is_null($request->from_date),function($q){
                                return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
                            });
                               if($request->month){
                                // $pla->whereIn(DB::raw('MONTH(transition_date)'),[$request->month]);
                                $product_sale->whereMonth('transition_date','=',$request->month);
                            }
                             $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
                            if($request->year){
                                $product_sale->whereYear('transition_date','=',$request->year);
                            }

                            $product_sale = $product_sale->get();

                        //end

                        foreach($product_sale as $ps){
                            // $product=Product::find($ps->product_id);
                            // $amount+=(int)$product->cost_price* (int)$ps->product_quantity;
                            $sr_amount+=(int)$ps->cost_price;
                        }
                    }
                    /**$cost_of_revenue=new stdClass();
                    $cost_of_revenue->name='Cost Of Good Sold';
                    $cost_of_revenue->amount=$amount;
                    $this->total_cor=$amount;
                    $profitAndLoss[$ah->name]->cost_of_revenue=$cost_of_revenue;**/
                   } 
                    $cost_of_revenue=new stdClass();
                    $cost_of_revenue->name='Cost Of Good Sold';
                    $cost_of_revenue->amount=$amount-$sr_amount;
                    $this->total_cor=$amount-$sr_amount;
                    $profitAndLoss[$ah->name]->cost_of_revenue=$cost_of_revenue;
                   //EP Sale Return END
                //    else{
                //     $profitAndLoss=null;
                //    }
                  
               }//end of cost of revenue
        }

        $total_income=$total_expense=0;
        $ah_income= $account_head=AccountHead::whereHas('financial_type2',function($q){
            $q->where('name','Income');
            //    ->orWhere('name','Expense');
        })
        // ->orderBy('id','desc')
        ->get();
        $ah_expense= $account_head=AccountHead::whereHas('financial_type2',function($q){
            $q->where('name','Expense');
        })->get();
        foreach($ah_income as $key=>$ai){
            $pla=AccountTransition::where('is_cashbook',0)->where('sub_account_id','!=',79);
            $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
            $pla->when(!is_null($request->from_date),function($q)use($request){
                return  $q->whereDate('transition_date','>=',$request->from_date);
            });
            $pla->when(!is_null($request->from_date),function($q){
                return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
            });
            if($request->month){
                // $pla->whereIn(DB::raw('MONTH(transition_date)'),[$request->month]);
                $pla->whereMonth('transition_date','=',$request->month);
            }
            $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
            if($request->year){
                $pla->whereYear('transition_date','=',$request->year);
            }
          
            $pla->whereHas('sub_account.account_head',function($q)use($ai){
                $q->where('id',$ai->id);
            });
            $pla->selectRaw('*, sum(IFNULL(credit,0)) as amount, sum(IFNULL(debit,0)) as deb_amount')->groupBy('sub_account_id');
            $pla=$pla->get();
            // dd($pla);
            // if($ai->name==='Other Income'){
                $total=0;
                if($pla->isNotEmpty()){
                    foreach($pla as $k=>$p){
                        $index_income[$key]=new stdClass();
                        // if($pla[$k]->sub_account->account_head->id==$ai->id){
                            $income[$key][$k]=new stdClass();
                            $income[$key][$k]->sub_account_name=$pla[$k]->sub_account->sub_account_name;
                            $income[$key][$k]->sub_account_id=$pla[$k]->sub_account_id;
                            /*if($pla[$k]->sub_account_id == 79 && !empty($pla[$k]->sale_id)) {
                              $income[$key][$k]->amount=$pla[$k]->deb_amount;
                              $total+=$p->deb_amount;
                            } else {
                              $income[$key][$k]->amount=$pla[$k]->amount;
                              $total+=$p->amount;
                            }*/
                            $income[$key][$k]->amount=$pla[$k]->amount;
                            $total+=$p->amount;
                            
                            $this->account_head_name=$pla[$k]->sub_account->account_head->name;
                            //$total+=$p->amount;
                        // }
                    }
                    // if($this->account_head_name==$ai->name){
                        $index_income[$key]->account_head_name=$ai->name;
                        $index_income[$key]->income=$income[$key];
                        $index_income[$key]->total=$total;
                        $total_income+=$total;
                    // }
                }
                // else{
                //     $index_income[$key]=null;
                //     // $index_income[$key]=new stdClass();
                // }
        }
      
        foreach($ah_expense as $key=>$ae){
            $ple=AccountTransition::where('is_cashbook',0)->where('sub_account_id','!=',80);
            $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
            $ple->when(!is_null($request->from_date),function($q)use($request){
                return  $q->whereDate('transition_date','>=',$request->from_date);
            });
            $ple->when(!is_null($request->from_date),function($q){
                return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
            });
            if($request->month){
                $ple->whereMonth('transition_date','=',$request->month);
            }
            $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
            if($request->year){
                $ple->whereYear('transition_date','=',$request->year);
            }
            $ple->whereHas('sub_account.account_head',function($q)use($ae){
                $q->whereId($ae->id);
            })->selectRaw('*, sum(debit) as amount, sum(credit) as cr_amount')->groupBy('sub_account_id');
            $ple=$ple->get();
            // if($ai->name==='Other Income'){
                if($ple->isNotEmpty()){
                    $total=0;
                    foreach($ple as $k=>$p){
                        $index_expense[$key]=new stdClass();
                        if($p->sub_account->account_head->name==$ae->name){
                            $expense[$key][$k]=new stdClass();
                            $expense[$key][$k]->sub_account_name=$p->sub_account->sub_account_name;
                            /**if($p->sub_account_id == 80 && !empty($p->sale_id)){
                              $expense[$key][$k]->amount=$p->cr_amount;
                              $total_expense+=$p->cr_amount;
                              $total+=$p->cr_amount;
                            } else {
                              $expense[$key][$k]->amount=$p->amount;
                              $total_expense+=$p->amount;
                              $total+=$p->amount;
                            }**/
                            $expense[$key][$k]->amount=$p->amount;
                            $total_expense+=$p->amount;
                            $total+=$p->amount;
                            
                            $this->account_head_name=$p->sub_account->account_head->name;
                            //$total_expense+=$p->amount;
                           // $total+=$p->amount;
                        }
                    }
                    // if($this->account_head_name==$ae->name){
                        $index_expense[$key]->account_head_name=$ae->name;
                        $index_expense[$key]->expense=$expense[$key];
                        $index_expense[$key]->total=$total;
                    // }
                }
                // else{
                //     $index_expense=null;
                //     // $index_expense=new stdClass();
                // }
        }

      /** currency gain **/
      $c_gain=AccountTransition::where('is_cashbook',0)->where('sub_account_id',79);
      $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
      $c_gain->when(!is_null($request->from_date),function($q)use($request){
          return  $q->whereDate('transition_date','>=',$request->from_date);
      });
      $c_gain->when(!is_null($request->from_date),function($q){
          return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
      });
      if($request->month){
          $c_gain->whereMonth('transition_date','=',$request->month);
      }
      $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
      if($request->year){
          $c_gain->whereYear('transition_date','=',$request->year);
      }
      /*$c_gain->whereHas('sub_account.account_head',function($q)use($ae){
          $q->whereId($ae->id);
      })->selectRaw('*, sum(debit) as amount, sum(credit) as cr_amount')->groupBy('sub_account_id');*/
      $c_gain->selectRaw('*, sum(abs(credit)) as amount, sum(abs(debit)) as db_amount')->groupBy('sub_account_id');
      $c_gain=$c_gain->first();
        /** end currency gain **/

        /** currency Loss **/
      $c_loss=AccountTransition::where('is_cashbook',0)->where('sub_account_id',80);
      $request->to_date= $request->to_date !=null ? $request->to_date : now()->today();
      $c_loss->when(!is_null($request->from_date),function($q)use($request){
          return  $q->whereDate('transition_date','>=',$request->from_date);
      });
      $c_loss->when(!is_null($request->from_date),function($q){
          return  $q->whereBetween('transition_date',[request('from_date'),request('to_date')]);
      });
      if($request->month){
          $c_loss->whereMonth('transition_date','=',$request->month);
      }
      $request->year=$request->year==null ? Carbon::now()->year :  $request->year;
      if($request->year){
          $c_loss->whereYear('transition_date','=',$request->year);
      }
      /*$c_gain->whereHas('sub_account.account_head',function($q)use($ae){
          $q->whereId($ae->id);
      })->selectRaw('*, sum(debit) as amount, sum(credit) as cr_amount')->groupBy('sub_account_id');*/
      $c_loss->selectRaw('*, sum(abs(debit)) as amount, sum(abs(credit)) as cr_amount')->groupBy('sub_account_id');
      $c_loss=$c_loss->first();
        /** end currency loss **/
        
        $gross_profit=$this->total_revenue-$this->total_cor;
        $net_profit=($gross_profit+$total_income)- $total_expense;

        //ep
        $route_name=Route::currentRouteName();
        
        $profit_and_loss=isset($profitAndLoss) ? $profitAndLoss : '';
        $gross_profit=$gross_profit;
        $total_income=$total_income;
        $income=isset($index_income) ? $index_income :'';
        $expense=isset($index_expense) ? $index_expense :'';
        $total_expense=$total_expense;
        $net_profit=$net_profit;

        if($route_name=='export_p_and_l_pdf'){
            $pdf = PDF::loadView('exports.profit_and_loss', compact('profit_and_loss','income','gross_profit','total_income','expense','total_expense','net_profit','c_gain','c_loss'));
            $pdf->setPaper('a4' , 'portrait');
           // $output = $pdf->output();
            /*  return new Response($output, 200, [
               'Content-Type' => 'application/pdf',
                'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
            ]);*/
            return $pdf->output();
        }
        //end ep
        // return compact('index_income');
        return response()->json([
            'profit_and_loss'=>isset($profitAndLoss) ? $profitAndLoss : '',
            'gross_profit'=>$gross_profit,
            'total_income'=>$total_income,
            'income'=>isset($index_income) ? $index_income :'',
            'expense'=>isset($index_expense) ? $index_expense :'',
            'total_expense'=>$total_expense,
            'total_revenue'=>$this->total_revenue,
            'net_profit'=>$net_profit,
            'c_gain'=>$c_gain,
            'c_loss'=>$c_loss,
            'status'=>200,
        ]);
    }
}
