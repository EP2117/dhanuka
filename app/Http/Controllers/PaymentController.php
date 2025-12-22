<?php

namespace App\Http\Controllers;

use App\AccountTransition;
use App\Http\Traits\AccountReport\Ledger;
use App\Payment;
use App\SubAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PaymentController extends Controller
{
    use Ledger;
    public function getAll(Request $request){
        $payment=Payment::orderBy('id','asc');
        if($request->cash_payment_no!=null){
            $payment->where('cash_payment_no',$request->cash_payment_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $payment->whereBetween('date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $payment->whereDate('date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $payment->whereDate('date', '<=', $request->to_date);
        }
        if($request->debit!=null){
            $payment->where('debit_id',$request->debit);
        }
        if($request->credit!=null){
            $payment->where('credit_id',$request->credit);
        }
        $payment=$payment->paginate(30);
        return response(compact('payment'));
    }
    public function store(Request  $request){
        $latest = Payment::orderBy('id','desc')->first();
        if($latest==null){
            $no=0;
        }else{
            $no=$latest->id;
        }
        $invoice_no = "CP".str_pad((int)$no + 1,5,"0",STR_PAD_LEFT);

        $cash_payment_no = str_pad($invoice_no,5,"0",STR_PAD_LEFT);
        $payment=Payment::create([
            'cash_payment_no'=>$cash_payment_no,
            'debit_id'=>$request->debit,
            'credit_id'=>$request->credit,
            'remark'=>$request->remark,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        if ($payment) {
            $ag = SubAccount::find($payment->credit->id);
            $ag_id = $ag->account_group_id;
            AccountTransition::create([
                'sub_account_id' => $payment->debit->id,
                'account_group_id' => $ag_id,
                'cash_bank_sub_account_id' => $payment->credit->id,
                'transition_date' => $payment->date,
                'vochur_no'=>$cash_payment_no,
                'payment_id' => $payment->id,
                'is_cashbook' => 1,
                'status'=>'payment',
                'credit' => $payment->amount,
                'description' => $payment->remark,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
            $this->storePaymentInLedger($payment);
            // AccountTransition::create([
            //     'sub_account_id' => $payment->debit->id,
            //     'transition_date' => $payment->date,
            //     'payment_id' => $payment->id,
            //     'vochur_no'=>$cash_payment_no,
            //     'is_cashbook' => 0,
            //     'debit' => $payment->amount,
            //     'description' => $payment->remark,
            //     'created_by' => Auth::user()->id,
            //     'updated_by' => Auth::user()->id,
            // ]);
            // AccountTransition::create([
            //     'sub_account_id' => $payment->credit->id,
            //     'transition_date' => $payment->date,
            //     'payment_id' => $payment->id,
            //     'vochur_no'=>$cash_payment_no,
            //     'is_cashbook' => 0,
            //     'credit' => $payment->amount,
            //     'description' => $payment->remark,
            //     'created_by' => Auth::user()->id,
            //     'updated_by' => Auth::user()->id,
            // ]);

        }

        return response()->json([
            'status'=>'success',
        ]);
    }
    public function edit($id){
        $payment=Payment::findOrfail($id);
        return compact('payment');
    }
    public function update($id,Request $request){
//        dd($id);
//        dd($request->all());
        $validatedData = $request->validate([
            'cash_payment_no' => 'max:255|unique:payments,cash_payment_no,'.$id,
        ]);
        Payment::whereId($id)->update([
            'cash_payment_no'=>$request->cash_payment_no,
            'debit_id'=>$request->debit,
            'credit_id'=>$request->credit,
            'remark'=>$request->remark,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        $payment =Payment::whereId($id)->first();
        if($payment){
            $ag = SubAccount::find($payment->credit->id);
            $ag_id = $ag->account_group_id;
            AccountTransition::where('payment_id',$id)
                ->where('is_cashbook',1)
                ->update([
                'sub_account_id' => $payment->debit->id,
                'account_group_id' => $ag_id,
                'cash_bank_sub_account_id' => $payment->credit->id,
                'vochur_no'=>$request->cash_payment_no,
                'transition_date' => $payment->date,
                'payment_id' => $payment->id,
                'is_cashbook' => 1,
                'credit' => $payment->amount,
                'description' => $payment->remark,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
            $this->updatePaymentInLedger($payment);
//              For leger
//            AccountTransition::where('payment_id',$id)->update([
//                'sub_account_id' => $payment->debit->id,
//                'transition_date' => $payment->date,
//                'vochur_no'=>$payment->cash_receipt_no,
//                'payment_id' => $payment->id,
//                'is_cashbook' => 0,
//                'debit' => $payment->amount,
//                'description' => $payment->remark,
//                'created_by' => Auth::user()->id,
//                'updated_by' => Auth::user()->id,
//            ]);
//            AccountTransition::where('payment_id',$id)->update([
//                'sub_account_id' => $payment->credit->id,
//                'transition_date' => $payment->date,
//                'payment_id' => $payment->id,
//                'vochur_no'=>$payment->cash_receipt_no,
//                'is_cashbook' => 0,
//                'credit' => $payment->amount,
//                'description' => $payment->remark,
//                'created_by' => Auth::user()->id,
//                'updated_by' => Auth::user()->id,
//            ]);
        }
        return response()->json([
            'status'=>'success',
        ]);
    }
    public function destroy($id){
        Payment::whereId($id)->delete();
        AccountTransition::where('payment_id',$id)
        ->where('status','payment')
        ->delete();

    }
}
