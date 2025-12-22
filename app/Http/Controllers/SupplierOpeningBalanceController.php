<?php

namespace App\Http\Controllers;

use App\AccountTransition;
use App\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierOpeningBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $supplier_ob=PurchaseInvoice::where('is_opening',1);
        if($request->opening_date){
            $supplier_ob->where('invoice_date',$request->opening_date);
        }
        if($request->supplier_id){
            $supplier_ob->where('supplier_id',$request->supplier_id);
        }
        if($request->invoice_no){
            $supplier_ob->where('invoice_date',$request->invoice_no);
        }
        $supplier_ob=$supplier_ob->paginate(30);
        return response(compact('supplier_ob'));
    }
    public function edit($id){
        $supplier_ob=PurchaseInvoice::find($id);
        return compact('supplier_ob');
    }
    public function store(Request $request)
    {
        $latest = PurchaseInvoice::orderBy('id','desc')->first();
        if($latest==null){
            $no=0;
        }else{
            $no=$latest->id;
        }
        foreach (Auth::user()->branches as $k => $b) {
            if ($k == 0) {
                $branch_id = $b->id;
            }
        }
        $invoice_no = "OBP".str_pad((int)$no + 1,5,"0",STR_PAD_LEFT);
        $supplier_ob= new PurchaseInvoice();
        $supplier_ob->invoice_no=$invoice_no;
        $supplier_ob->branch_id = $branch_id;
        $supplier_ob->warehouse_id = Auth::user()->warehouse_id;
        $supplier_ob->invoice_date=$request->opening_date;
        $supplier_ob->is_opening=1;
        $supplier_ob->payment_type='credit';
        $supplier_ob->supplier_id=$request->supplier_id;
        $supplier_ob->total_amount=$request->amount;
        $supplier_ob->pay_amount=0;
        $supplier_ob->balance_amount=$request->amount;
        $supplier_ob->created_by = Auth::user()->id;
        $supplier_ob->updated_by = Auth::user()->id;
        $supplier_ob->save();

        AccountTransition::create([
            'sub_account_id' => 5,
            'transition_date' => $request->opening_date,
            'purchase_id' => $supplier_ob->id,
            'supplier_id'=>$request->supplier_id,
            'is_cashbook' => 0,
            'description'=>'Supplier Opening Balance',
            'vochur_no'=>$invoice_no,
            'debit' => $request->amount,
            'status'=>'supplier_opening',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        return response()->json([
            'status'=>'success',
        ]);
    }

    public function show($id)
    {

    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'invoice_no' => 'max:255|unique:purchase_invoices,invoice_no,' . $id,
        ]);
        foreach (Auth::user()->branches as $k => $b) {
            if ($k == 0) {
                $branch_id = $b->id;
            }
        }
        $supplier_ob=PurchaseInvoice::find($id);
        $supplier_ob->invoice_no=$request->invoice_no;
        $supplier_ob->branch_id = $branch_id;
        $supplier_ob->warehouse_id = Auth::user()->warehouse_id;
        $supplier_ob->invoice_date=$request->opening_date;
        $supplier_ob->is_opening=1;
        $supplier_ob->payment_type='credit';
        $supplier_ob->supplier_id=$request->supplier_id;
        $supplier_ob->total_amount=$request->amount;
        $supplier_ob->pay_amount=0;
        $supplier_ob->balance_amount=$request->amount;
        $supplier_ob->created_by = Auth::user()->id;
        $supplier_ob->updated_by = Auth::user()->id;
        $supplier_ob->save();

        $at_result = AccountTransition::where([
            ['purchase_id',$id],
            ['is_cashbook',0],
            ['status','supplier_opening']])->first();
        if($at_result) {
            AccountTransition::where([
                ['purchase_id',$id],
                ['is_cashbook',0],
                ['status','supplier_opening']])->update([
                'sub_account_id' => 5,
                'transition_date' => $request->opening_date,
                'vochur_no'=>$request->invoice_no,
                'supplier_id'=>$request->supplier_id,
                'is_cashbook' => 0,
                'debit' => $request->amount,
                'updated_by' => Auth::user()->id,
            ]);
        } else {
            AccountTransition::create([
                'sub_account_id' => 5,
                'transition_date' => $request->opening_date,
                'purchase_id' => $supplier_ob->id,
                'supplier_id'=>$request->supplier_id,
                'is_cashbook' => 0,
                'description'=>'Supplier Opening Balance',
                'vochur_no'=>$request->invoice_no,
                'debit' => $request->amount,
                'status'=>'supplier_opening',
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);  
        }
        return response()->json([
            'status'=>'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PurchaseInvoice::whereId($id)->delete();
        AccountTransition::where([
            ['purchase_id',$id],
            ['is_cashbook',0],
            ['status','supplier_opening']])->delete();
        return response(['message' => 'delete successful']);

//            AccountTransition::where('purchase_id', $id)
//                ->where('sub_account_id', config('global.credit_payment'))
//                ->delete();
    }
}
