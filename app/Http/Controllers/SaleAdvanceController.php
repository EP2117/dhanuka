<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\SaleAdvance;
use App\AccountTransition;
use DB;

class SaleAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }        

        $data = SaleAdvance::with('customer');

        $data->where('advance_type','in');

        if($request->advance_no != '') {
        	$data->where('advance_no','LIKE','%'.$request->advance_no.'%');
        }
        if($request->date != '') {
        	$data->whereDate('advance_date',$request->date);
        }
        if($request->customer_id != '') {
        	$data->where('customer_id',$request->customer_id);
        }
        $data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function store(Request $request)
    {
        $obj = new SaleAdvance;
        $max_id = SaleAdvance::where('advance_type','in')->max('advance_no');
        if($max_id) {
        	$num = preg_replace('/[^0-9.]+/', '', $max_id ); 
            $max_id = $num + 1;
        }else {
            $max_id = 1;
        }
        $advance_no = "SA".str_pad($max_id,5,"0",STR_PAD_LEFT);

        $obj->advance_no = $advance_no;
        $obj->advance_date = $request->date;
        $obj->customer_id = $request->customer_id;
        $obj->amount = $request->amount;
        $obj->advance_type = 'in';
        $obj->created_by = Auth::user()->id;
        $obj->save();

        AccountTransition::create([
            'sub_account_id' => 9, //sale advance account
            'transition_date' => $request->date,
            'sale_advance_id' => $obj->id,
            'customer_id'=>$request->customer_id,
            'is_cashbook' => 0,
            'description'=>'Sale Advance',
            'vochur_no'=>$advance_no,
            'debit' => $request->amount,
            'created_by' => Auth::user()->id,
        ]);

        AccountTransition::create([
            'sub_account_id' => 9, //sale advance account
            'transition_date' => $request->date,
            'sale_advance_id' => $obj->id,
            'customer_id'=>$request->customer_id,
            'is_cashbook' => 1,
            'description'=>'Sale Advance',
            'vochur_no'=>$advance_no,
            'debit' => $request->amount,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json([
            'status'=>'success',
        ]);
    }

    public function edit($id){
        $data=SaleAdvance::find($id);
        return compact('data');
    }

    public function getAdvanceBalance ($customer_id){
        //get customer's sale advance
        $advance_balance= 0;
        $advance = DB::table("sale_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                    ->where('customer_id','=',$customer_id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $advance_balance = $in - $out;
        }

        return $advance_balance;
    }

    public function show($id){
        $data=SaleAdvance::find($id);
        return compact('data');
    }

    public function update(Request $request, $id)
    {
        $obj = SaleAdvance::find($id);
        $old_amount = $obj->amount;
        $advance = DB::table("sale_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                    ->where('customer_id','=',$obj->customer_id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $advance_balance = $in - $out;
        }
        
        $amount =  $request->amount;
        //$advance_balance = $this->getAdvanceBalance($obj->customer_id);
       // if($amount < $advance_balance) {
        if((($in - $old_amount) + $amount) < $out) {
            return response()->json([
                'status'=>'invalid',
            ]);
        } else {
            $obj->advance_date = $request->date;
            $obj->customer_id = $request->customer_id;
            $obj->amount = $request->amount;
            $obj->advance_type = 'in';
            $obj->updated_by = Auth::user()->id;
            $obj->save();

            AccountTransition::where([
                ['sale_advance_id',$id],
                ['is_cashbook',0]])->update([
                'transition_date' => $request->date,
                'customer_id'=>$request->customer_id,
                'debit' => $request->amount,
                'updated_by' => Auth::user()->id,
            ]);

            AccountTransition::where([
                ['sale_advance_id',$id],
                ['is_cashbook',1]])->update([
                'transition_date' => $request->date,
                'customer_id'=>$request->customer_id,
                'debit' => $request->amount,
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
        $obj = SaleAdvance::find($id);
        $amount = $obj->amount;
       // $advance_balance = $this->getAdvanceBalance($obj->customer_id);
        $advance = DB::table("sale_advances")

                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                    ->where('customer_id','=',$obj->customer_id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $advance_balance = $in - $out;
        }
        //$advance_balance = $this->getAdvanceBalance($obj->customer_id);
       // if($amount < $advance_balance) {
        if(($in - $amount) < $out) {
       // if($amount > $advance_balance) {
            return response(['message' => 'invalid']);
        } else {
            $obj->delete();
            AccountTransition::where('sale_advance_id',$id)->delete();
            return response(['message' => 'delete successful']);
        }
    }
}
