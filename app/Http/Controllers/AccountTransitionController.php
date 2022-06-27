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

    public function getValuation($year){
        $where = "1=1";
        $p_where = "1=1";
        $s_where = "1=1";

        /*$where .= " YEAR(product_transitions.transition_date) = " . $year;
        $p_where .= " YEAR(purchase_invoices.invoice_date) = " . $year;
        $s_where .= " YEAR(sales.invoice_date) = " . $year;*/
        

        $products = DB::table("products")
            ->where('products.is_active', 1)
            ->select(DB::raw("products.id as product_id,pt.transition_date,ps.s_valuation_amount,pt.cost_price,pt.in_cost_price,products.purchase_price,ps.s_qty,pp.p_valuation_amount,pt.entry_qty,products.product_name,products.minimum_qty, products.brand_id,pt.warehouse_id, products.product_code,uom_id,pt.in_qty,pt.out_qty"))
            ->leftjoin(DB::raw("(SELECT product_id,product_quantity,transition_type,transition_purchase_id, warehouse_id, transition_date, branch_id,
                         SUM(CASE  WHEN transition_type = 'in'  AND transition_entry_id IS NOT NULL THEN product_quantity  ELSE 0 END) as entry_qty,
                         SUM(CASE  WHEN transition_type = 'in' AND transition_entry_id IS NULL THEN product_quantity  ELSE 0 END) as in_qty,
                         SUM(CASE  WHEN transition_type = 'out' AND ( transition_sale_id || transition_adjustment_id || transition_transfer_id)  IS NOT NULL THEN cost_price  ELSE 0 END) as cost_price,
                         SUM(CASE  WHEN transition_type = 'in'  AND (transition_adjustment_id IS NOT NULL OR transition_transfer_id IS NOT NULL OR transition_sale_id IS NOT NULL OR transition_return_id IS NOT NULL) THEN cost_price ELSE 0 END ) as in_cost_price,
                         SUM(CASE  WHEN transition_type = 'out'  THEN product_quantity  ELSE 0 END) as out_qty
                         FROM product_transitions Where " . $where . " 
                          GROUP BY product_transitions.product_id
                           )as pt"), function ($join) {
                $join->on("pt.product_id", "=", "products.id");
            })
            /** ->leftjoin(DB::raw("(SELECT product_id,SUM(product_purchase.total_amount) as p_valuation_amount
                    FROM product_purchase LEFT JOIN purchase_invoices ON purchase_invoices.id = product_purchase.product_id WHERE ".$p_where."
                   GROUP BY product_purchase.product_id
                   ) as pp"),function($join){
                       $join->on("pp.product_id","=","products.id");
                   })**/
            ->leftjoin(DB::raw("(SELECT product_purchase.product_id,SUM(product_purchase.total_amount) as p_valuation_amount
                    FROM purchase_invoices LEFT JOIN product_purchase ON product_purchase.purchase_id = purchase_invoices.id WHERE " . $p_where . "
                   GROUP BY product_purchase.product_id
                   ) as pp"), function ($join) {
                $join->on("pp.product_id", "=", "products.id");
            })
            ->leftjoin(DB::raw("(SELECT product_id,SUM(total_amount) as s_valuation_amount,product_quantity as s_qty
                       FROM product_sale 
                      GROUP BY product_sale.product_id
                      ) as ps"), function ($join) {
                $join->on("ps.product_id", "=", "products.id");
            });
            /**->leftjoin(DB::raw("(SELECT product_sale.product_id,SUM(product_sale.total_amount) as s_valuation_amount,product_quantity as s_qty
                    FROM sales LEFT JOIN product_sale ON product_sale.sale_id = sales.id WHERE ".$s_where."
                   GROUP BY product_sale.product_id
                   ) as ps"),function($join){
                       $join->on("ps.product_id","=","products.id");
                   })**/
        $data  = $products->orderBy("product_name")->get();
        return $data;
    }

    public function getBalSheetProfitAndLoss(Request $request){
        $year = !empty($request->year) ? $request->year : Carbon::now()->year;
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
                    $pl->whereYear('transition_date','=',$year);
                  $pl->where('sub_account_id',8);

                  $pl_sr=SaleReturn::whereNotNull('return_no');
                   $pl_sr->where('return_method','=','with invoice');
                    $pl_sr->whereYear('return_date','=',$year);

                $pl_cr=CustomerReturn::whereNotNull('customer_return_no');
                    $pl_cr->whereYear('return_date','=',$year);
                
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
                    $pl->whereYear('transition_date','=',$year);
                
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
                                /**$product_sale->when(!is_null($request->from_date),function($q)use($request){
                                   return  $q->whereDate('transition_date','>=',$request->from_date);
                               });**/
                                $product_sale->whereYear('transition_date','=',$year);

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
                    $pl_sr->whereYear('return_date','=',$year);
                
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
                            $product_sale->whereYear('transition_date','=',$year);

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
            $pla->whereYear('transition_date','=',$year);
          
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
            $ple->whereYear('transition_date','=',$year);

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
      $c_gain->whereYear('transition_date','=',$year);
      /*$c_gain->whereHas('sub_account.account_head',function($q)use($ae){
          $q->whereId($ae->id);
      })->selectRaw('*, sum(debit) as amount, sum(credit) as cr_amount')->groupBy('sub_account_id');*/
      $c_gain->selectRaw('*, sum(abs(credit)) as amount, sum(abs(debit)) as db_amount')->groupBy('sub_account_id');
      $c_gain=$c_gain->first();
        /** end currency gain **/

        /** currency Loss **/
      $c_loss=AccountTransition::where('is_cashbook',0)->where('sub_account_id',80);
      $c_loss->whereYear('transition_date','=',$year);
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
        return compact('net_profit','c_gain','c_loss');
        /**return response()->json([            
            'net_profit'=>$net_profit,
            'c_gain'=>$c_gain,
            'c_loss'=>$c_loss,
            'profit_and_loss'=>isset($profitAndLoss) ? $profitAndLoss : '',
            'gross_profit'=>$gross_profit,
            'total_income'=>$total_income,
            'income'=>isset($index_income) ? $index_income :'',
            'expense'=>isset($index_expense) ? $index_expense :'',
            'total_expense'=>$total_expense,
            'total_revenue'=>$this->total_revenue,
            'status'=>200,
        ]);**/
    }

    public function getBalanceSheetReport(Request $request){

        $year = !empty($request->year) ? $request->year : Carbon::now()->year;

        //start inventory valuation
        $total_valuation = 0;
        $data = $this->getValuation($year);
        foreach ($data as $p) {
            $total_valuation += ((int)$p->entry_qty * $p->purchase_price) + (((int)$p->p_valuation_amount + $p->in_cost_price) - (int)$p->cost_price);
        }
        //end inventory valuation  

        //sale os amount  balance_amount - (collection_amount + return_amount + customer_return_amount); 
        $os_amount = 0;
        //$os_result = DB::SELECT(DB::raw("Select SUM(balance_amount - (collection_amount + return_amount + customer_return_amount)) as os_amount FROM sales WHERE payment_type = 'credit' AND YEAR(invoice_date) = ".$year)); 

        $os_result = DB::SELECT(DB::raw("Select SUM(balance_amount - (collection_amount + return_amount + customer_return_amount)) as os_amount FROM sales WHERE payment_type = 'credit' AND CASE  WHEN currency_id = 1 THEN (total_amount-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0 ELSE  (total_amount_fx-(IFNULL(cash_discount_fx,0) + pay_amount_fx + collection_amount_fx)) > 0 END AND YEAR(invoice_date) = ".$year)); 

        $gain_loss = DB::SELECT(DB::raw("Select SUM(ABS(gain_amount)) as gain_amount, SUM(ABS(loss_amount)) as loss_amount FROM collection_sale LEFT JOIN sales ON sales.id = collection_sale.sale_id WHERE sales.payment_type='credit' AND CASE  WHEN currency_id = 1 THEN (sales.total_amount-(IFNULL(sales.cash_discount,0) + sales.pay_amount + sales.collection_amount + sales.return_amount + sales.customer_return_amount)) > 0 ELSE  (sales.total_amount_fx-(IFNULL(sales.cash_discount_fx,0) + sales.pay_amount_fx + sales.collection_amount_fx)) > 0 END AND YEAR(sales.invoice_date) = ".$year));
        //dd($gain_loss);
        $os_amount = (round($os_result[0]->os_amount, 3) + $gain_loss[0]->gain_amount) - $gain_loss[0]->loss_amount;  
        //end sale os amount


        $asset_account_head=AccountHead::with('sub_accounts')->where('name','Non-Current Asset')->orWhere('name','Current Asset')->get();
        //$profitAndLoss[$ah->name]=new stdClass();
        $c_asset = '';
        $nc_asset = '';        
        $total_eq = 0;
        $total_cl = 0;
        $total_lt = 0;
        foreach($asset_account_head as $key=>$aah) {
          //start Asset
          //start current asset                   
          if($aah->name == 'Current Asset') {
            $ca_arr = array();
            $ca_str = '';
            foreach($aah->sub_accounts as $ca) {
                array_push($ca_arr, $ca->id);
                if($ca_str == '') {
                  $ca_str = $ca->id;
                } else {
                  $ca_str .=",".$ca->id;
                }
            }
           // $c_asset=AccountTransition::whereIn('sub_account_id',$ca_arr)->where('is_cashbook',0)->selectRaw('*, sum(credit) as credit_amount')->groupBy('sub_account_id')->get();
            /**$c_asset = DB::SELECT(DB::raw("Select t.*, COALESCE(op.opening_amount,0) as op_amount FROM (Select account_transitions.sub_account_id, sub_account_name, SUM(CASE  WHEN sub_accounts.account_type_id = 1 OR sub_accounts.account_type_id = 2 THEN COALESCE(debit,credit) ELSE COALESCE(credit,0) END) as credit_amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND sub_account_id IN (".$ca_str.") AND is_cashbook=0 GROUP BY sub_account_id) as t LEFT JOIN (Select account_transitions.sub_account_id, (SUM(CASE  WHEN sub_accounts.account_type_id = 1 OR sub_accounts.account_type_id = 2 THEN COALESCE(credit,0) ELSE COALESCE(debit,0) END) - SUM(CASE  WHEN sub_accounts.account_type_id = 1 OR sub_accounts.account_type_id = 2 THEN COALESCE(debit,0) ELSE COALESCE(credit,0) END)) as opening_amount From account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) < ".$year." AND  sub_account_id IN (".$ca_str.") AND is_cashbook=0 GROUP BY sub_account_id) as op ON t.sub_account_id=op.sub_account_id WHERE t.credit_amount != 0"));**/
            $c_asset = DB::SELECT(DB::raw("Select t.*, COALESCE(op.opening_amount,0) as op_amount FROM (Select account_transitions.sub_account_id, sub_account_name, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND sub_account_id IN (".$ca_str.") AND is_cashbook=0 GROUP BY sub_account_id) as t LEFT JOIN (Select account_transitions.sub_account_id, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as opening_amount From account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) < ".$year." AND  sub_account_id IN (".$ca_str.") AND is_cashbook=0 GROUP BY sub_account_id) as op ON t.sub_account_id=op.sub_account_id WHERE t.amount != 0"));
          }
          //end current asset

          //start non current asset
          if($aah->name == 'Non-Current Asset') {
            $nca_arr = array();
            $nca_str = '';
            foreach($aah->sub_accounts as $nca) {
                array_push($nca_arr, $nca->id);
                if($nca_str == '') {
                  $nca_str = $nca->id;
                } else {
                  $nca_str .=",".$nca->id;
                }
            }
            //$nc_asset=AccountTransition::whereIn('sub_account_id',$nca_arr)->where('is_cashbook',0)->selectRaw('*, sum(credit) as credit_amount')->groupBy('sub_account_id')->get();
            $nc_asset = DB::SELECT(DB::raw("Select t.*, COALESCE(op.opening_amount,0) as op_amount FROM (Select account_transitions.sub_account_id, sub_account_name, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND sub_account_id IN (".$nca_str.") AND is_cashbook=0 GROUP BY sub_account_id) as t LEFT JOIN (Select account_transitions.sub_account_id, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as opening_amount From account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) < ".$year." AND  sub_account_id IN (".$nca_str.") AND is_cashbook=0 GROUP BY sub_account_id) as op ON t.sub_account_id=op.sub_account_id WHERE t.amount != 0 AND t.sub_account_name != 'Prepaid Expense'"));
          }
          //end non current asset
          //end Asset
        }

        //start profit and loss
        $pl = $this->getBalSheetProfitAndLoss($request);
        $c_gain = empty($pl['c_gain']) ? 0 : $pl['c_gain']->amount;
        $c_loss = empty($pl['c_loss']) ? 0 : $pl['c_loss']->amount;
        $net_profit= empty($pl['net_profit']) ? 0 : $pl['net_profit'];
        $gain_loss_profit = ($net_profit + $c_gain)  - $c_loss;
        //end profit and loss

        // purchase os amount  total_amount - (discount + pay_amount + collection_amount)

        $pos_amount = 0;
        $pos_result = DB::SELECT(DB::raw("Select SUM(balance_amount - collection_amount) as pos_amount FROM purchase_invoices WHERE payment_type = 'credit' AND CASE  WHEN currency_id = 1 THEN (balance_amount - collection_amount) >= 0 ELSE (balance_amount_fx - collection_amount_fx) > 0 END AND YEAR(invoice_date) = ".$year)); 

        $p_gain_loss = DB::SELECT(DB::raw("Select SUM(ABS(gain_amount)) as gain_amount, SUM(ABS(loss_amount)) as loss_amount FROM collection_purchase LEFT JOIN purchase_invoices ON purchase_invoices.id = collection_purchase.purchase_id WHERE purchase_invoices.payment_type = 'credit' AND CASE  WHEN currency_id = 1 THEN (purchase_invoices.balance_amount - purchase_invoices.collection_amount) > 0 ELSE (purchase_invoices.balance_amount_fx - purchase_invoices.collection_amount_fx) > 0 END AND YEAR(purchase_invoices.invoice_date) = ".$year));
        //dd($gain_loss);
        $pos_amount = (round($pos_result[0]->pos_amount, 3) + $p_gain_loss[0]->gain_amount) - $p_gain_loss[0]->loss_amount; 
        //end sale os amount

        //purcahse advance for prepaid expense
        $prepaid_amt = 0;
        $prepaid = DB::SELECT(DB::raw("Select account_transitions.sub_account_id, sub_account_name, CASE WHEN sub_accounts.sub_account_name='Prepaid Expense' THEN (ABS(SUM(ABS(COALESCE(credit,0))) - SUM(ABS(COALESCE(debit,0))))) ELSE 0 END as pp_amount, CASE WHEN sub_accounts.sub_account_name='Purchase Advance' THEN  SUM(COALESCE(credit,0))  ELSE 0 END as pa_amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND (sub_accounts.sub_account_name='Purchase Advance' OR sub_accounts.sub_account_name='Prepaid Expense') AND is_cashbook=0 GROUP BY sub_account_id"));
        if(!empty($prepaid)) {
          foreach ($prepaid as $pp) {
            if($pp->sub_account_name == 'Prepaid Expense') {
              $prepaid_amt += $pp->pp_amount;
            } else {
              $prepaid_amt += $pp->pa_amount;
            }
          }
        } 
        //end purcahse advance for prepaid expense

        $html = '';
        $html.='<tr><td colspan="2"><h2>Asset</h2></td></tr>';

        $html.='<tr><td colspan="2"><b>Non-Current Asset</b></td><tr>';
        $total_nc = 0;
        $total_c = 0;
        if(!empty($nc_asset)) {            
          foreach($nc_asset as $ncasset) {
            $nc_amt = (float)$ncasset->op_amount + (float)$ncasset->amount;
            $html.='<tr><td>'.$ncasset->sub_account_name.'</td><td>'.$nc_amt.'</td></tr>';
            $total_nc+=$nc_amt;
          }
        }

        $html.='<tr><td class="text-right"><b>Total</b></td><td>'.$total_nc.'</td><tr>';

        $html.='<tr><td colspan="2"><b>Current Asset</b></td><tr>';
        $html.='<tr><td>Inventory</td><td>'.$total_valuation.'</td></tr>';
        $html.='<tr><td>Trade Receiable</td><td>'.$os_amount.'</td></tr>';
        if(!empty($c_asset)) {
            
          foreach($c_asset as $casset) {            
            $amt = (float)$casset->op_amount + (float)$casset->amount;
            $html.='<tr><td>'.$casset->sub_account_name.'</td><td>'.$amt.'</td></tr>';
            $total_c+=$amt;
          }
        }
        if(!empty($prepaid_amt)) {
          $total_c+=$prepaid_amt;
          $html.='<tr><td>Prepaid Expense</td><td>'.$prepaid_amt.'</td></tr>';
        }

        $html.='<tr><td class="text-right"><b>Total</b></td><td>'.$total_c.'</td><tr>';
        $total_asset = $total_c + $total_nc;
        $html.='<tr><td class="text-right"><b>Total Assets</b></td><td>'.$total_asset.'</td><tr>';

        //Equity & Liablity
        $total_eq_li = 0;
        $el_account_head=AccountHead::with('sub_accounts')->where('name','Equity')->orWhere('name','Current Liability')->orWhere('name','Long Term Liability')->get();
        
        $html.='<tr><td colspan="2"><h2>Equity & Liablity</h2></td></tr>';

        foreach($el_account_head as $key=>$el) {
          //start equity              
          if($el->name == 'Equity') {
            $html.='<tr><td colspan="2"><b>Equity</b></td><tr>';
            $html.='<tr><td>Retaing earning</td><td>'.$gain_loss_profit.'</td></tr>';
            
            $eq_arr = array();
            $eq_str = '';
            foreach($el->sub_accounts as $eq) {
                array_push($eq_arr, $eq->id);
                if($eq_str == '') {
                  $eq_str = $eq->id;
                } else {
                  $eq_str .=",".$eq->id;
                }
            }
            $eq_query = DB::SELECT(DB::raw("Select t.*, COALESCE(op.opening_amount,0) as op_amount FROM (Select account_transitions.sub_account_id, sub_account_name, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND sub_account_id IN (".$eq_str.") AND is_cashbook=0 GROUP BY sub_account_id) as t LEFT JOIN (Select account_transitions.sub_account_id, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as opening_amount From account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) < ".$year." AND  sub_account_id IN (".$eq_str.") AND is_cashbook=0 GROUP BY sub_account_id) as op ON t.sub_account_id=op.sub_account_id WHERE t.amount != 0"));

            $total_eq = 0;
            $total_eq += $gain_loss_profit;

            if(!empty($eq_query)) {            
              foreach($eq_query as $obj) {
                if($obj->sub_account_name == 'Drawing') {
                  $eq_amt = (float)$obj->amount;
                } else {
                  $eq_amt = (float)$obj->op_amount + (float)$obj->amount;
                }

                $html.='<tr><td>'.$obj->sub_account_name.'</td><td>'.$eq_amt.'</td></tr>';
                $total_eq+=$eq_amt;
              }
            }

            $html.='<tr><td class="text-right"><b>Total</b></td><td>'.$total_eq.'</td><tr>';
          }
        //end equity

        //start long term liability
        
        if($el->name == 'Long Term Liability') {
          $html.='<tr><td colspan="2"><b>Long Term Liability</b></td><tr>';
          $lt_arr = array();
          $lt_str = '';
          foreach($el->sub_accounts as $lt) {
              array_push($lt_arr, $lt->id);
              if($lt_str == '') {
                $lt_str = $lt->id;
              } else {
                $lt_str .=",".$lt->id;
              }
          }

          $lt_query = DB::SELECT(DB::raw("Select t.*, COALESCE(op.opening_amount,0) as op_amount FROM (Select account_transitions.sub_account_id, sub_account_name, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND sub_account_id IN (".$lt_str.") AND is_cashbook=0 GROUP BY sub_account_id) as t LEFT JOIN (Select account_transitions.sub_account_id, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as opening_amount From account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) < ".$year." AND  sub_account_id IN (".$lt_str.") AND is_cashbook=0 GROUP BY sub_account_id) as op ON t.sub_account_id=op.sub_account_id WHERE t.amount != 0"));

            $total_lt = 0;
            if(!empty($lt_query)) {            
              foreach($lt_query as $obj) {
                $lt_amt = (float)$obj->op_amount + (float)$obj->amount;
                $html.='<tr><td>'.$obj->sub_account_name.'</td><td>'.$lt_amt.'</td></tr>';
                $total_lt+=$lt_amt;
              }
             
            }

            $html.='<tr><td class="text-right"><b>Total</b></td><td>'.$total_lt.'</td><tr>';
          
        }        
        //end lont term liability

        //start current liability
        
         if($el->name == 'Current Liability') {  
          $html.='<tr><td colspan="2"><b>Current Liablity</b></td><tr>';        
          $html.='<tr><td>Trade Payable</td><td>'.$pos_amount.'</td></tr>';
          
          $cl_arr = array();
          $cl_str = '';
          foreach($el->sub_accounts as $cl) {
              array_push($cl_arr, $cl->id);
              if($cl_str == '') {
                $cl_str = $cl->id;
              } else {
                $cl_str .=",".$cl->id;
              }
          }
          $cl_query = DB::SELECT(DB::raw("Select t.*, COALESCE(op.opening_amount,0) as op_amount FROM (Select account_transitions.sub_account_id, sub_account_name, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as amount FROM account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) = ".$year. " AND sub_account_id IN (".$cl_str.") AND is_cashbook=0 GROUP BY sub_account_id) as t LEFT JOIN (Select account_transitions.sub_account_id, (ABS(SUM(COALESCE(debit,0)) - SUM(COALESCE(credit,0)))) as opening_amount From account_transitions LEFT JOIN sub_accounts ON sub_accounts.id = account_transitions.sub_account_id WHERE YEAR(transition_date) < ".$year." AND  sub_account_id IN (".$cl_str.") AND is_cashbook=0 GROUP BY sub_account_id) as op ON t.sub_account_id=op.sub_account_id WHERE t.amount != 0"));

            $total_cl = 0;
            $total_cl += $pos_amount;
            if(!empty($cl_query)) {            
              foreach($cl_query as $obj) {
                $cl_amt = (float)$obj->op_amount + (float)$obj->amount;
                $html.='<tr><td>'.$obj->sub_account_name.'</td><td>'.$cl_amt.'</td></tr>';
                $total_cl+=$cl_amt;
              }
            }

            $html.='<tr><td class="text-right"><b>Total</b></td><td>'.$total_cl.'</td><tr>';
          
        } 
        
        //end current liability        
    }
    $total_eq_li = $total_eq + $total_cl + $total_lt;
    $html.='<tr><td class="text-right"><b>Total Equity & Liability</b></td><td>'.$total_eq_li.'</td><tr>';
    return compact('html');
  }
}
