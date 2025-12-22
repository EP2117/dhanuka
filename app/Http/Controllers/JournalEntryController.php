<?php

namespace App\Http\Controllers;

use App\AccountTransition;
use App\Http\Traits\AccountReport\Ledger;
use App\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class JournalEntryController extends Controller
{
    use Ledger;
    public function getAll(Request $request){
        $obj=JournalEntry::orderBy('id','desc');
        if($request->journal_entry_no!=null){
            $obj->where('journal_entry_no',$request->journal_entry_no);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $obj->whereBetween('date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $obj->whereDate('date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $obj->whereDate('date', '<=', $request->to_date);
        }
        if($request->debit!=null){
            $obj->where('debit_id',$request->debit);
        }
        if($request->credit!=null){
            $obj->where('credit_id',$request->credit);
        }
        $entry=$obj->paginate(30);
        return response(compact('entry'));
    }
    public function store(Request  $request){
        $latest = JournalEntry::orderBy('id','desc')->first();
        if($latest==null){
            $no=0;
        }else{
            $no=$latest->id;
        }
        $invoice_no = "JE".str_pad((int)$no + 1,5,"0",STR_PAD_LEFT);

        $journal_entry_no = str_pad($invoice_no,5,"0",STR_PAD_LEFT);
        $obj=JournalEntry::create([
            'journal_entry_no'=>$journal_entry_no,
            'debit_id'=>$request->debit,
            'credit_id'=>$request->credit,
            'remark'=>$request->remark,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        if ($obj) {
            /**AccountTransition::create([
                'sub_account_id' => $payment->debit->id,
                'transition_date' => $payment->date,
                'vochur_no'=>$cash_payment_no,
                'payment_id' => $payment->id,
                'is_cashbook' => 1,
                'status'=>'payment',
                'credit' => $payment->amount,
                'description' => $payment->remark,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);**/
            $this->storeJournalEntryInLedger($obj);

        }

        return response()->json([
            'status'=>'success',
        ]);
    }
    public function edit($id){
        $entry=JournalEntry::findOrfail($id);
        return compact('entry');
    }
    public function update($id,Request $request){
//        dd($id);
//        dd($request->all());
        $validatedData = $request->validate([
            'journal_entry_no' => 'max:255|unique:journal_entries,journal_entry_no,'.$id,
        ]);
        JournalEntry::whereId($id)->update([
            'journal_entry_no'=>$request->journal_entry_no,
            'debit_id'=>$request->debit,
            'credit_id'=>$request->credit,
            'remark'=>$request->remark,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
        ]);
        $entry =JournalEntry::whereId($id)->first();
        if($entry){
            /**AccountTransition::where('payment_id',$id)
                ->where('is_cashbook',1)
                ->update([
                'sub_account_id' => $payment->debit->id,
                'vochur_no'=>$request->cash_payment_no,
                'transition_date' => $payment->date,
                'payment_id' => $payment->id,
                'is_cashbook' => 1,
                'credit' => $payment->amount,
                'description' => $payment->remark,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);**/
            $this->updateJournalEntryInLedger($entry);
        }
        return response()->json([
            'status'=>'success',
        ]);
    }
    public function destroy($id){
        JournalEntry::whereId($id)->delete();
        AccountTransition::where('journal_entry_id',$id)
        ->where('status','journal_entry')
        ->delete();

    }
}
