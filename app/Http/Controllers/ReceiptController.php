<?php

namespace App\Http\Controllers;

use App\AccountTransition;
use App\SubAccount;
use App\Http\Traits\AccountReport\Ledger;
use App\Recepit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    use Ledger;
    public function getAll(Request $request){
        $receipt=Recepit::orderBy('id','asc');
        if($request->cash_receipt_no!=null){
            $receipt->where('cash_receipt_no',$request->cash_receipt_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $receipt->whereBetween('date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $receipt->whereDate('date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $receipt->whereDate('date', '<=', $request->to_date);
        }
        if($request->debit!=null){
            $receipt->where('debit_id',$request->debit);
        }
        if($request->credit!=null){
            $receipt->where('credit_id',$request->credit);
        }
        $receipt=$receipt->paginate(30);
        return response(compact('receipt'));
    }
    public function store(Request  $request){
        $latest = Recepit::orderBy('id','desc')->first();
        if($latest==null){
            $no=0;
        }else{
            $no=$latest->id;
        }
        $invoice_no = "CR".str_pad((int)$no + 1,5,"0",STR_PAD_LEFT);
        $cash_receipt_no = str_pad($invoice_no,5,"0",STR_PAD_LEFT);
        $receipt=Recepit::create([
            'cash_receipt_no'=>$cash_receipt_no,
            'debit_id'=>$request->debit,
            'credit_id'=>$request->credit,
            'remark'=>$request->remark,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        $ag = SubAccount::find($request->debit);
        $ag_id = $ag->account_group_id;
        if($receipt){
            AccountTransition::create([
                'sub_account_id'=>$receipt->credit->id,
                'account_group_id' => $ag_id,
                'cash_bank_sub_account_id' => $request->debit,
                'transition_date'=>$receipt->date,
                'receipt_id'=>$receipt->id,
                'is_cashbook'=>1,
                'vochur_no'=>$cash_receipt_no,
                'debit'=>$receipt->amount,
                'status'=>'receipt',
                'description'=>$receipt->remark,
                'created_by'=>Auth::user()->id,
                'updated_by'=>Auth::user()->id,
            ]);
            $this->storeReceiptInLedger($receipt);
            // AccountTransition::create([
            //     'sub_account_id'=>$receipt->debit->id,
            //     'transition_date'=>$receipt->date,
            //     'receipt_id'=>$receipt->id,
            //     'vochur_no'=>$cash_receipt_no,
            //     'is_cashbook'=>0,
            //     'debit'=>$receipt->amount,
            //     'description'=>$receipt->remark,
            //     'created_by'=>Auth::user()->id,
            //     'updated_by'=>Auth::user()->id,
            // ]);
            // AccountTransition::create([
            //     'sub_account_id'=>$receipt->credit->id,
            //     'transition_date'=>$receipt->date,
            //     'receipt_id'=>$receipt->id,
            //     'vochur_no'=>$cash_receipt_no,
            //     'is_cashbook'=>0,
            //     'credit'=>$receipt->amount,
            //     'description'=>$receipt->remark,
            //     'created_by'=>Auth::user()->id,
            //     'updated_by'=>Auth::user()->id,
            // ]);
        }

        return response()->json([
            'status'=>'success',
        ]);
    }
    public function edit($id){
        $receipt=Recepit::findOrfail($id);
        return compact('receipt');
    }
    public function update($id,Request $request){
//        dd($id);
//        dd($request->all());
        $validatedData = $request->validate([
            'cash_receipt_no' => 'max:255|unique:receipts,cash_receipt_no,'.$id,
        ]);
        $receipt=Recepit::whereId($id)->update([
            'cash_receipt_no'=>$request->cash_receipt_no,
            'debit_id'=>$request->debit,
            'credit_id'=>$request->credit,
            'remark'=>$request->remark,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        $receipt=Recepit::whereId($id)->first();
        if($receipt){
            $ag = SubAccount::find($receipt->debit->id);
            $ag_id = $ag->account_group_id;
            AccountTransition::where('receipt_id',$id)
                ->where('is_cashbook',1)
                ->update([
                'sub_account_id'=>$receipt->credit->id,
                'account_group_id' => $ag_id,
                'cash_bank_sub_account_id' => $receipt->debit->id,
                'transition_date'=>$receipt->date,
                'vochur_no'=>$receipt->cash_receipt_no,
                'receipt_id'=>$receipt->id,
                'is_cashbook'=>1,
                'debit'=>$receipt->amount,
                'description'=>$receipt->remark,
                'created_by'=>Auth::user()->id,
                'updated_by'=>Auth::user()->id,
            ]);
            /* For leger*/
            $this->updateReceiptInLedger($receipt);
        //    AccountTransition::where('receipt_id',$id)->update([
        //        'sub_account_id'=>$receipt->debit->id,
        //        'transition_date'=>$receipt->date,
        //        'receipt_id'=>$receipt->id,
        //        'vochur_no'=>$receipt->cash_receipt_no,
        //        'is_cashbook'=>0,
        //        'debit'=>$receipt->amount,
        //        'description'=>$receipt->remark,
        //        'created_by'=>Auth::user()->id,
        //        'updated_by'=>Auth::user()->id,
        //    ]);
        //    AccountTransition::where('receipt_id',$id)->update([
        //        'sub_account_id'=>$receipt->credit->id,
        //        'transition_date'=>$receipt->date,
        //        'receipt_id'=>$receipt->id,
        //        'is_cashbook'=>0,
        //        'credit'=>$receipt->amount,
        //        'vochur_no'=>$receipt->cash_receipt_no,
        //        'description'=>$receipt->remark,
        //        'created_by'=>Auth::user()->id,
        //        'updated_by'=>Auth::user()->id,
        //    ]);
        }
        return response()->json([
            'status'=>'success',
        ]);
    }
    public function destroy($id){
        Recepit::whereId($id)->delete();
        AccountTransition::
        where('receipt_id',$id)
        ->where('status','receipt')
        ->delete();

    }
}
