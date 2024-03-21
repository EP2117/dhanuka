<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Product;
use Carbon\Carbon;
use App\PurchaseInvoice;
use App\AccountTransition;
use App\ProductTransition;
use App\PurchaseCreditNote;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\Report\GetReport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Exports\PurchaseCreditNoteExport;
use App\Http\Traits\AccountReport\Ledger;
use App\Http\Traits\AccountReport\Cashbook;
use App\Exports\PurchaseCreditNoteProductExport;

class PurchaseCreditNoteController extends Controller
{
    use GetReport;
    use Cashbook;
    use Ledger;
    public function index(Request $request){
        $login_year = Session::get('loginYear');
        $credit_note = PurchaseCreditNote::with('supplier','branch','purchase','products');        
        if($request->credit_note_no != ""){
            $credit_note->where('credit_note_no', $request->credit_note_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $credit_note->whereBetween('credit_note_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $credit_note->whereDate('credit_note_date', '>=', $request->from_date);
        }else if($request->to_date != '') {
             $credit_note->whereDate('credit_note_date', '<=', $request->to_date);
        } else {
            $credit_note->whereBetween('credit_note_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }
        if($request->supplier_id != "") {
            $credit_note->where('supplier_id', $request->supplier_id);
        }
        $credit_note=$credit_note->paginate(30);
        return compact('credit_note');
    }
   
    public function store(Request $request){
            DB::beginTransaction();
            try {
                $max_id = PurchaseCreditNote::max('id');
                if($max_id) {
                    $max_id = $max_id + 1;
                } else {
                    $max_id = 1;
                }
                $credit_note_no = "DN".str_pad($max_id,5,"0",STR_PAD_LEFT);
                $credit_note=PurchaseCreditNote::create([
                    'credit_note_no'=>$credit_note_no,
                    'credit_note_date'=>$request->credit_note_date,
                    'branch_id'=>$request->branch_id,
                    'warehouse_id'=>$request->warehouse_id,
                    'supplier_id'=>$request->supplier_id,
                    'credit_note_type_id'=>$request->credit_note_type_id,
                    'account_group_id'=>$request->account_group,
                    'sub_account_id'=>$request->cash_bank_account,
                    'amount'=> $request->amount ,
                    'purchase_id'=>$request->purchase_id,
                    'created_by'=>Auth::user()->id,
                    'updated_by'=>Auth::user()->id
                ]);
                if($request->credit_note_type_id!=0){
                    $purchase=PurchaseInvoice::find($request->purchase_id);
                    $prev_cn_amount =  $purchase->credit_note_amount;
                    $purchase->credit_note_amount=$prev_cn_amount+$request->amount;
                    //$purchase->credit_note_amount=$request->amount;
                    $purchase->save();
                }
                if($credit_note){
                    //$this->storePurchaseCreditNoteForCashBook($credit_note);
                    //$this->storePurchaseCreditNoteForLedger($credit_note);
                    /*if($request->credit_note_type_id==0){
                        $this->storePurchaseCreditNoteForCashBook($credit_note);
                    }
                    $this->storePurchaseCreditNoteForLedger($credit_note);*/
                    if($request->credit_note_type_id==0){
                        $this->storePurchaseCreditNoteForCashBook($credit_note);
                        $this->storePurchaseCreditNoteForLedger($credit_note);
                    }
                    
                }
                if($request->credit_note_type_id!=2){
                    for($i=0;$i<count($request->products);$i++){
                        //comment by EP
                    /**
                        $get_qty=DB::table('product_purchase')->where('purchase_id',$request->purchase_id)->where('product_id',$request->products[$i])->first();
                        $credit_note_qty=DB::table('product_purchase')->where('purchase_id',$request->purchase_id)->where('product_id',$request->products[$i])
                        ->update(['debit_note_quantity'=>$get_qty->debit_note_quantity+$request->qty[$i]]);
                       
                        $pivot_cn=$credit_note->products()->attach($request->products[$i],[
                            'qty'=>$request->qty[$i],
                            'rate'=>$request->units[$i],
                            'total_amount'=>$request->total_amounts[$i],
                            'discount'=>$request->discounts[$i],
                            'remark'=>$request->remarks[$i]
                        ]);
                        DB::table('debit_note_product')
                        ->leftjoin('debit_notes', 'debit_notes.id', '=', 'debit_note_product.debit_note_id')
                        ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'debit_notes.purchase_id')
                        ->where('debit_notes.purchase_id',$request->purchase_id)
                        ->where('product_id',$request->products[$i])->update([
                            'old_qty'=>(int)$get_qty->product_quantity-($get_qty->debit_note_quantity+$request->qty[$i])
                        ]); **/

                        $get_qty = DB::table('product_purchase')->where('id', $request->product_purchase_ids[$i])->first();
                        $credit_note_qty = DB::table('product_purchase')->where('id', $request->product_purchase_ids[$i])
                            ->update(['debit_note_quantity' => $get_qty->debit_note_quantity + $request->qty[$i]]);

                        $pivot_cn = $credit_note->products()->attach($request->products[$i], [
                            'qty' => $request->qty[$i],
                            'product_purchase_pivot_id' => $request->product_purchase_ids[$i],
                            'uom_id' => $request->uoms[$i],
                            'rate' => $request->units[$i],
                            'total_amount' => $request->total_amounts[$i],
                            // 'discount'=>$request->discounts[$i],
                            'remark' => $request->remarks[$i]
                        ]);

                        $last_row=DB::table('debit_note_product')->orderBy('id', 'DESC')->first();
                        $pivot_id = $last_row->id;
                        /*$store_cost_price=Product::find($request->products[$i]);
                        $cost_price=$store_cost_price->cost_price;*/
                         $cost_price = $this->getCostPrice($request->products[$i])->product_cost_price;
                        $store_cost_price = Product::find($request->products[$i]);
                        if ($cost_price == 0) {
                            $cost_price = $store_cost_price->purchase_price;
                        }
                        $store_cost_price->cost_price = $cost_price;
                        $store_cost_price->save();

                        //calculate quantity for product pre-defined UOM
                        $main_uom_id = $store_cost_price->uom_id;
                        $uom_relation = DB::table('product_selling_uom')
                            ->select('relation')
                            ->where('product_id', $request->products[$i])
                            ->where('uom_id', $request->uoms[$i])
                            ->first();
                        if ($uom_relation) {
                            $relation_val = $uom_relation->relation;
                        } else {
                            //for pre-defined product uom
                            $relation_val = 1;
                        }

                           $obj = new ProductTransition;
                           $obj->product_id            = $request->products[$i];
                           $obj->transition_type       = "out";
                           $obj->transition_debit_note_id   = $credit_note->id;
                           $obj->transition_product_pivot_id   = $pivot_id;
                           $obj->branch_id  = $request->branch_id;
                           $obj->warehouse_id          = $request->warehouse_id;
                           $obj->transition_date       = $request->credit_note_date;
                            $obj->cost_price            = $cost_price * $request->qty[$i] * $relation_val;
                            $obj->transition_product_uom_id        = $request->uoms[$i];
                            $obj->transition_product_quantity      = $request->qty[$i];
                            $obj->product_uom_id        = $main_uom_id;
                            $obj->product_quantity      = $request->qty[$i] * $relation_val;
                           $obj->created_by = Auth::user()->id;
                           $obj->updated_by = Auth::user()->id;
                           $obj->save();
                    }
                }
               
            $status='success';
            DB::commit();
            return compact('status');
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e->getMessage());
            $status = "fail";
            return compact('status');
            throw $e;
        }
    }
    public function edit($id){
    // dd($id);
        //$credit_note=PurchaseCreditNote::with('products','supplier','branch')->find($id);
        $credit_note = PurchaseCreditNote::with('purchase','products', 'products.selling_uoms', 'supplier', 'branch')->find($id);
        foreach ($credit_note->products as $key => $cp) {
            $ps = DB::table('product_purchase')
                ->where('id', $cp->pivot->product_purchase_pivot_id)
                ->first();

            $credit_note->products[$key]->pivot->product_quantity = $ps->product_quantity;
            $credit_note->products[$key]->pivot->debit_note_quantity = $ps->debit_note_quantity;
        }
        return compact('credit_note');
    }
    public function update(Request $request,$id){
        // dd($request->all());
        DB::beginTransaction();
            try {
               $credit_note=PurchaseCreditNote::with('products')->find($id);
               $old_cn_amt = $credit_note->amount;
               $credit_note->account_group_id = $request->account_group;
               $credit_note->sub_account_id = $request->cash_bank_account;
               $credit_note->amount=$request->amount;
               $credit_note->credit_note_date=$request->credit_note_date;
               $credit_note->updated_by=Auth::user()->id;
               $credit_note->save();
               if($request->credit_note_type_id!=0){
                $purchase=PurchaseInvoice::find($request->purchase_id);
                $purchase_cn_amt = $purchase->credit_note_amount - $old_cn_amt;
                $purchase->credit_note_amount = $purchase_cn_amt + $request->amount;
                //$purchase->credit_note_amount=$request->amount;
                $purchase->save();
               }
                // $credit_note=PurchaseCreditNote::whereId($id)->update([
                //     'credit_note_date'=>$request->credit_note_date,
                //     'amount'=>$request->amount,
                //     'updated_by'=>Auth::user()->id
                // ]);
                if($credit_note){
                    /*$this->updatePurchaseCreditNoteForCashBook($credit_note);
                    $this->updatePurchaseCreditNoteForLedger($credit_note);*/
                    if($request->credit_note_type_id==0){
                        $this->updatePurchaseCreditNoteForCashBook($credit_note);
                        $this->updatePurchaseCreditNoteForLedger($credit_note);
                    }
                }
                $ex_pivot_arr = array();
                foreach ($credit_note->products as $pp) {
                    array_push($ex_pivot_arr, $pp->pivot->id);
                }
                //remove id in pivot that are removed in sale Form
                $results = array_diff($ex_pivot_arr, $request->pivots_id); //get id that are not in request pivot array

                foreach ($results as $key => $val) {
                    $get_qty = DB::table('debit_note_product')->where('id', $val)->first();

                    $credit_note_qty = DB::table('product_purchase')->where('id', $get_qty->product_purchase_pivot_id)->first();
                    $cn_res = $credit_note_qty->debit_note_quantity - $get_qty->qty;

                    DB::table('product_purchase')->where('id', $get_qty->product_purchase_pivot_id)->update(['debit_note_quantity' => $cn_res]);
                    //delete in pivot
                    DB::table('debit_note_product')->where('id', $val)->delete();
                    //delete in transition
                    DB::table('product_transitions')
                        ->where('transition_product_pivot_id', $val)
                        ->where('transition_debit_note_id', $id)
                        ->delete();
                }

                for($i=0;$i<count($request->products);$i++){
                    $old_cn_qty = DB::table('debit_note_product')->whereId($request->pivots_id[$i])->first();
                    /*$get_qty=DB::table('product_purchase')->where('purchase_id',$request->purchase_id)->where('product_id',$request->products[$i])->first();
                    $res=($get_qty->debit_note_quantity-$request->old_qty[$i])+$request->qty[$i];
                    $credit_note_qty=DB::table('product_purchase')->where('purchase_id',$request->purchase_id)->where('product_id',$request->products[$i])
                    ->update(['debit_note_quantity'=>$res]);*/

                    $get_qty = DB::table('product_purchase')->where('id', $request->product_purchase_ids[$i])->first();
                    $res = ($get_qty->debit_note_quantity - $old_cn_qty->qty) + $request->qty[$i];

                    $credit_note_qty = DB::table('product_purchase')->where('id', $request->product_purchase_ids[$i])->update(['debit_note_quantity' => $res]);

                   /** DB::table('debit_note_product')
                    ->leftjoin('debit_notes', 'debit_notes.id', '=', 'debit_note_product.debit_note_id')
                    ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'debit_notes.purchase_id')
                    ->where('debit_notes.purchase_id',$request->purchase_id)
                    ->where('product_id',$request->products[$i])->update([
                        'old_qty'=>(int)($get_qty->product_quantity)-$res
                    ]);
                    DB::table('debit_note_product')
                    ->whereId($request->pivots_id[$i])
                    ->update([
                        'qty'=>$request->qty[$i],
                        'rate'=>$request->units[$i],
                        // 'old_qty'=>(int)($get_qty->product_quantity)-$res,
                        'total_amount'=>$request->total_amounts[$i],
                        'discount'=>$request->discounts[$i]!=null ? $request->discounts[$i] : 0,
                        'remark'=>$request->remarks[$i]
                    ]);**/
                    DB::table('debit_note_product')->whereId($request->pivots_id[$i])
                    ->update([
                        'qty' => $request->qty[$i],
                        'rate' => $request->units[$i],
                        'total_amount' => $request->total_amounts[$i],
                        // 'discount'=>$request->discounts[$i]!=null ? $request->discounts[$i] : 0,
                        'remark' => $request->remarks[$i]
                    ]);
                    /*$cs=Product::find($request->products[$i]);
                    $cost_price=$cs->cost_price;*/
                    $cs = Product::find($request->products[$i]);
                    /**$cost_price=$cs->cost_price; ksk **/
                    $cost_price = $this->getCostPrice($request->products[$i])->product_cost_price;
                    $store_cost_price = Product::find($request->products[$i]);
                    if ($cost_price == 0) {
                        $cost_price = $store_cost_price->purchase_price;
                    }
                    $store_cost_price->cost_price = $cost_price;
                    $store_cost_price->save();

                    $uom_relation = DB::table('product_selling_uom')
                        ->select('relation')
                        ->where('product_id', $request->products[$i])
                        ->where('uom_id', $request->uoms[$i])
                        ->first();
                    if ($uom_relation) {
                        $relation_val = $uom_relation->relation;
                    } else {
                        //for pre-defined product uom
                        $relation_val = 1;
                    }

                   DB::table('product_transitions')
                   ->where('transition_product_pivot_id', $request->pivots_id[$i])
                   ->where('transition_debit_note_id', $id)
                   ->update(array('transition_date' => $request->credit_note_date, 'cost_price' => $cost_price * $request->qty[$i] * $relation_val, 'product_quantity' => $request->qty[$i] * $relation_val, 'transition_product_quantity' => $request->qty[$i]));
                   //->update(array('cost_price'=>$cost_price *$request->qty[$i], 'product_quantity' => $request->qty[$i], 'transition_product_quantity' => $request->qty[$i]));
                }
            $status='success';
            DB::commit();
            return compact('status');
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e->getMessage());
            $status = "fail";
            return compact('status');
            throw $e;
        }
    }
    public function destroy($id)
    {
       /* $cn = PurchaseCreditNote::with('products')->find($id);
        $cn->products()->detach();
        $cn->delete();
        DB::table('product_transitions')
        ->where('transition_debit_note_id', $id)
        ->delete();
        AccountTransition::where('debit_note_id',$id)
            ->where('status','debit_note')
            ->delete();*/
        $cn = PurchaseCreditNote::with('products')->find($id);
        foreach ($cn->products as $p) {

            $get_qty = DB::table('product_purchase')->where('id', $p->pivot->product_purchase_pivot_id)->first();
            $res = ($get_qty->debit_note_quantity - $p->pivot->qty);

            $credit_note_qty = DB::table('product_purchase')->where('id', $p->pivot->product_purchase_pivot_id)->update(['debit_note_quantity' => $res]);
        }
        //update credit note amt in sales table
        $s = PurchaseInvoice::find($cn->purchase_id);
        $cn_amount = $s->credit_note_amount - $cn->amount;
        $s->credit_note_amount = $cn_amount;
        $s->save();

        $cn->products()->detach();
        $cn->delete();
        DB::table('product_transitions')
            ->where('transition_debit_note_id', $id)
            ->delete();
        AccountTransition::where('debit_note_id', $id)
            ->where('status', 'debit_note')
            ->delete();
        return response(['message' => 'delete successful']);
    }
    public function getPurchaseCreditNoteReport(Request $request){
        $route_name=Route::currentRouteName();
        $login_year = Session::get('loginYear');
        $credit_note = PurchaseCreditNote::with('supplier','branch','purchase','products')
        ->whereHas('purchase',function($q) use($request,$login_year){
            if($request->purchase_invoice_no != "") {
                $q->where('invoice_no', $request->purchase_invoice_no);
            }
            if($request->from_date != '' && $request->to_date != '')
            {
                $q->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            } 
        });
        if($request->credit_note_no != ""){
            $credit_note->where('credit_note_no', $request->credit_note_no);
        }
        if ($request->branch_id != "") {
            $credit_note->where('branch_id', $request->branch_id);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $credit_note->whereBetween('credit_note_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $credit_note->whereDate('credit_note_date', '>=', $request->from_date);
        }else if($request->to_date != '') {
             $credit_note->whereDate('credit_note_date', '<=', $request->to_date);
        } else {
            $credit_note->whereBetween('credit_note_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if($request->state_id != "") {
            $credit_note->whereHas('supplier',function($q)use($request){
                $q->where('state_id',$request->state_id);
            });
        } 
        if($request->township_id != "") {
            $credit_note->whereHas('supplier',function($q)use($request){
                $q->where('township_id',$request->township_id);
            });
        } 

        if($request->supplier_id != "") {
            $credit_note->where('supplier_id', $request->supplier_id);
        }
        $credit_note=$credit_note->get();
        if($route_name=='debit_note_export'){
            $export=new PurchaseCreditNoteExport($credit_note);
            $fileName = 'Purchase Credit Note Report'.Carbon::now()->format('Ymd').'.xlsx';
            return Excel::download($export, $fileName);
        }
        return compact('credit_note');
    }
    public function getPurchaseCreditNoteProductWiseReport(Request $request){
        $route_name=Route::currentRouteName();
        $credit_note=DB::table("debit_note_product")
        ->select(DB::raw('debit_note_product.*,products.product_name,products.product_code,purchase_invoices.invoice_no,purchase_invoices.invoice_date,debit_notes.credit_note_no,debit_notes.credit_note_date,suppliers.name'))
        ->leftjoin('products', 'products.id', '=', 'debit_note_product.product_id')
        ->leftjoin('debit_notes', 'debit_notes.id', '=', 'debit_note_product.debit_note_id')
        ->leftjoin('purchase_invoices', 'purchase_invoices.id', '=', 'debit_notes.purchase_id')
        ->leftjoin('suppliers', 'suppliers.id', '=', 'debit_notes.supplier_id')
        ->leftjoin('states', 'states.id', '=', 'suppliers.state_id')
        ->leftjoin('townships', 'townships.id', '=', 'suppliers.township_id');
        if($request->purchase_invoice_no != "") {
            $credit_note->where('purchase_invoices.invoice_no', $request->purchase_invoice_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $credit_note->whereBetween('purchase_invoices.invoice_date', array($request->from_date, $request->to_date));
        } 
        if($request->credit_note_no != ""){
            $credit_note->where('debit_notes.credit_note_no', $request->credit_note_no);
        }
        if ($request->branch_id != "") {
            $credit_note->where('debit_notes.branch_id', $request->branch_id);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $credit_note->whereBetween('debit_notes.credit_note_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $credit_note->whereDate('debit_notes.credit_note_date', '>=', $request->from_date);
        }else if($request->to_date != '') {
             $credit_note->whereDate('debit_notes.credit_note_date', '<=', $request->to_date);
        } 
        if($request->supplier_id != "") {
            $credit_note->where('suppliers.id', $request->supplier_id);
        }
        if($request->product_code != "") {
            $credit_note->where('products.product_code','LIKE','%'.$request->product_code.'%');
        }

        if($request->state_id != "") {
            $credit_note->where('suppliers.state_id', $request->state_id);
        } 

        if($request->township_id != "") {
            $credit_note->where('suppliers.township_id', $request->township_id);
        }
        if($request->product_name != "") {
            $credit_note->where('products.product_name', 'LIKE','%'.$request->product_name.'%');
        }
        $credit_note=$credit_note->get();
        // dd($credit_note);
        if($route_name=='debit_note_product_export'){
            $credit_note_export=new PurchaseCreditNoteProductExport($credit_note);
            $fileName = 'Debit Note Product Export'.Carbon::now()->format('Ymd').'.xlsx';
            return Excel::download($credit_note_export, $fileName);
        }
        $total = 0;
        $i = 1; 
        $html = '';
        foreach($credit_note as $cn) {
            $html .= '<tr><td class="textalign">'.$i.'</td><td class="textalign">'.$cn->invoice_no.'</td><td class="textalign">'.date_format(date_create($cn->invoice_date),"d-m-Y").'</td>';
            $html .= '<td class="textalign">'.$cn->credit_note_no.'</td><td>'.date_format(date_create($cn->credit_note_date),"d-m-Y").'</td>';
            $html .= '<td class="textalign">'.$cn->product_code.'</td>';
            $html .= '<td class="textalign">'.$cn->product_name.'</td>';
            $html .= '<td class="textalign">'.$cn->name.'</td>';
            $html .= '<td class="textalign">'.$cn->qty.'</td>';
            $html .= '<td class="textalign">'.number_format($cn->rate).'</td>';
            $html .= '<td class="textalign">'.number_format($cn->total_amount).'</td>';
            $html .= '</tr>';
            $total+=$cn->total_amount;
            $i++;
        }
        if($credit_note->isNotEmpty()){
            $html .= '<tr><td colspan ="10" style="text-align: right;"><strong>Total</strong></td><td class="text-right">'.number_format($total).'</td></tr>';
        }else{
            // $html .= '<tr><td rowspans="10" style="text-align: right;"><strong>Record Not Found</strong></td></tr>';
        }
        return response(compact('html'), 200);

    }
}
