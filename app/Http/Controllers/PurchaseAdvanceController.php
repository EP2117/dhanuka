<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\PurchaseAdvance;
use App\AccountTransition;
use DB;

class PurchaseAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }        

        $data = PurchaseAdvance::with('supplier');

        $data->where('advance_type','in');

        if($request->advance_no != '') {
        	$data->where('advance_no','LIKE','%'.$request->advance_no.'%');
        }
        if($request->date != '') {
        	$data->whereDate('advance_date',$request->date);
        }
        if($request->supplier_id != '') {
        	$data->where('supplier_id',$request->supplier_id);
        }
        $data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function store(Request $request)
    {
        $obj = new PurchaseAdvance;
        $max_id = PurchaseAdvance::where('advance_type','in')->max('advance_no');
        if($max_id) {
        	$num = preg_replace('/[^0-9.]+/', '', $max_id ); 
            $max_id = $num + 1;
        }else {
            $max_id = 1;
        }
        $advance_no = "PA".str_pad($max_id,5,"0",STR_PAD_LEFT);

        $obj->advance_no = $advance_no;
        $obj->advance_date = $request->date;
        $obj->supplier_id = $request->supplier_id;
        $obj->amount = $request->amount;
        $obj->advance_type = 'in';
        $obj->created_by = Auth::user()->id;
        $obj->save();

        AccountTransition::create([
            'sub_account_id' => 6, //purchase advance account
            'transition_date' => $request->date,
            'purchase_advance_id' => $obj->id,
            'supplier_id'=>$request->supplier_id,
            'is_cashbook' => 0,
            'description'=>'Purchase Advance',
            'vochur_no'=>$advance_no,
            'credit' => $request->amount,
            'created_by' => Auth::user()->id,
        ]);

        AccountTransition::create([
            'sub_account_id' => 6, //purchase advance account
            'transition_date' => $request->date,
            'purchase_advance_id' => $obj->id,
            'supplier_id'=>$request->supplier_id,
            'is_cashbook' => 1,
            'description'=>'Purchase Advance',
            'vochur_no'=>$advance_no,
            'credit' => $request->amount,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json([
            'status'=>'success',
        ]);
    }

    public function edit($id){
        $data=PurchaseAdvance::find($id);
        return compact('data');
    }

    public function getAdvanceBalance ($supplier_id){
        //get supplier's purchase advance
        $advance_balance= 0;
        $advance = DB::table("purchase_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                    ->where('supplier_id','=',$supplier_id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $advance_balance = $in - $out;
        }

        return $advance_balance;
    }

    public function show($id){
        $data=PurchaseAdvance::find($id);
        return compact('data');
    }

    public function update(Request $request, $id)
    {
        $obj = PurchaseAdvance::find($id);
        $old_amount = $obj->amount;
        $amount =  $request->amount;
        //$advance_balance = $this->getAdvanceBalance($obj->supplier_id);
        $advance = DB::table("purchase_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                    ->where('supplier_id','=',$obj->supplier_id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $advance_balance = $in - $out;
        }
        //echo (($in - $old_amount) + $amount);
        //dd($advance_balance);
        if((($in - $old_amount) + $amount) < $out) {
        //if($amount < $advance_balance) {
            return response()->json([
                'status'=>'invalid',
            ]);
        } else {
            $obj->advance_date = $request->date;
            $obj->supplier_id = $request->supplier_id;
            $obj->amount = $request->amount;
            $obj->advance_type = 'in';
            $obj->updated_by = Auth::user()->id;
            $obj->save();

            AccountTransition::where([
                ['purchase_advance_id',$id],
                ['is_cashbook',0]])->update([
                'transition_date' => $request->date,
                'supplier_id'=>$request->supplier_id,
                'credit' => $request->amount,
                'updated_by' => Auth::user()->id,
            ]);

            AccountTransition::where([
                ['purchase_advance_id',$id],
                ['is_cashbook',1]])->update([
                'transition_date' => $request->date,
                'supplier_id'=>$request->supplier_id,
                'credit' => $request->amount,
                'updated_by' => Auth::user()->id,
            ]);

            return response()->json([
                'status'=>'success',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = PurchaseAdvance::find($id);
        $amount = $obj->amount;
       // $advance_balance = $this->getAdvanceBalance($obj->supplier_id);
        $advance = DB::table("purchase_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                    ->where('supplier_id','=',$obj->supplier_id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $advance_balance = $in - $out;
        }

        if(($in - $amount) < $out) {
        //if($amount > $advance_balance) {
            return response(['message' => 'invalid']);
        } else {
            $obj->delete();
            AccountTransition::where('purchase_advance_id',$id)->delete();
            return response(['message' => 'delete successful']);
        }
    }
}