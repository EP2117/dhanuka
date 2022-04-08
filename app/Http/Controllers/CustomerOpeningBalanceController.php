<?php

namespace App\Http\Controllers;

use App\Sale;
use App\AccountTransition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOpeningBalanceController extends Controller
{
    public function index(Request $request)
    {
        $customer_ob=Sale::where('is_opening',1);
        if($request->opening_date){
            $customer_ob->where('invoice_date',$request->opening_date);
        }
        if($request->customer_id){
            $customer_ob->where('customer_id',$request->customer_id);
        }
        if($request->invoice_no){
            $customer_ob->where('invoice_no',$request->invoice_no);
        }
        $customer_ob=$customer_ob->paginate(30);
        return response(compact('customer_ob'));
    }
    public function edit($id){
        $customer_ob=Sale::find($id);
        return compact('customer_ob');
    }
    public function store(Request $request)
    {
        $latest = Sale::orderBy('id','desc')->first();
        if($latest==null){
            $no=0;
        }else{
            $no=$latest->id;
        }
        $invoice_no = "OBP".str_pad((int)$no + 1,5,"0",STR_PAD_LEFT);
        $customer_ob= new Sale();
        $customer_ob->invoice_no=$invoice_no;
        $customer_ob->branch_id = Auth::user()->branch_id;
        $customer_ob->warehouse_id = Auth::user()->warehouse_id;
        $customer_ob->invoice_date=$request->opening_date;
        $customer_ob->is_opening=1;
        $customer_ob->sale_type=1;
        $customer_ob->payment_type='credit';
        $customer_ob->customer_id=$request->customer_id;
        $customer_ob->total_amount=$request->amount;
        $customer_ob->pay_amount=0;
        $customer_ob->balance_amount=$request->amount;
        $customer_ob->created_by = Auth::user()->id;
        $customer_ob->updated_by = Auth::user()->id;
        $customer_ob->save();

        AccountTransition::create([
            'sub_account_id' => 8,
            'transition_date' => $request->opening_date,
            'sale_id' => $customer_ob->id,
            'customer_id'=>$request->customer_id,
            'is_cashbook' => 0,
            'description'=>'Customer Opening Balance',
            'vochur_no'=>$invoice_no,
            'credit' => $request->amount,
            'status'=>'customer_opening',
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
            'invoice_no' => 'max:255|unique:sales,invoice_no,' . $id,
        ]);
        $customer_ob=Sale::find($id);
        $customer_ob->invoice_no=$request->invoice_no;
        $customer_ob->branch_id = Auth::user()->branch_id;
        $customer_ob->warehouse_id = Auth::user()->warehouse_id;
        $customer_ob->invoice_date=$request->opening_date;
        $customer_ob->is_opening=1;
        $customer_ob->payment_type='credit';
        $customer_ob->customer_id=$request->customer_id;
        $customer_ob->total_amount=$request->amount;
        $customer_ob->pay_amount=0;
        $customer_ob->balance_amount=$request->amount;
        $customer_ob->created_by = Auth::user()->id;
        $customer_ob->updated_by = Auth::user()->id;
        $customer_ob->save();

        $at_result = AccountTransition::where([
            ['sale_id',$id],
            ['is_cashbook',0],
            ['status','customer_opening']])->first();
        if($at_result) {
            AccountTransition::where([
                ['sale_id',$id],
                ['is_cashbook',0],
                ['status','customer_opening']])->update([
                'sub_account_id' => 8,
                'transition_date' => $request->opening_date,
                'vochur_no'=>$request->invoice_no,
                'customer_id'=>$request->customer_id,
                'is_cashbook' => 0,
                'credit' => $request->amount,
                'updated_by' => Auth::user()->id,
            ]);
        } else {
            AccountTransition::create([
                'sub_account_id' => 8,
                'transition_date' => $request->opening_date,
                'sale_id' => $customer_ob->id,
                'customer_id'=>$request->customer_id,
                'is_cashbook' => 0,
                'description'=>'Customer Opening Balance',
                'vochur_no'=>$request->invoice_no,
                'credit' => $request->amount,
                'status'=>'customer_opening',
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
        Sale::whereId($id)->delete();
         AccountTransition::where([
            ['sale_id',$id],
            ['is_cashbook',0],
            ['status','customer_opening']])->delete();
        return response(['message' => 'delete successful']);

//            AccountTransition::where('purchase_id', $id)
//                ->where('sub_account_id', config('global.credit_payment'))
//                ->delete();
    }}
