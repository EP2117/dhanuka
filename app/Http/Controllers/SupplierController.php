<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::orderBy('id', 'ASC')->where('is_active', 1)->get();
        return compact('data');
    }
    public function getSupplier(Request $request){
//        dd($request->all());
        $data = Supplier::orderBy('id', 'ASC')->paginate(30);
        if($request->sort_by!=null && $request->order!=null){
            $data = Supplier::orderBy($request->sort_by, $request->order)->where('is_active',1)->paginate(30);
        }
        if($request->supplier_code){
            $data = Supplier::where('supplier_code',$request->supplier_code)->paginate(30);
        } if($request->supplier_name){
            $data=Supplier::where('name', 'LIKE', '%'.$request->supplier_name.'%')->paginate(30);
        }  if($request->state_id!=null){
            $data=Supplier::whereHas('state',function ($q)use($request){
                $q->where('id',$request->state_id);
            })->paginate(30);
        }if($request->township_id!=null){
            $township=$request->township_id;
            $data=Supplier::whereHas('township',function ($q)use($township){
                $q->where('id',$township);
            })->paginate(30);
//            dd($data);
        }
        if($request->status!=null){
            $data = Supplier::orderBy('id', 'ASC')->where('is_active',$request->status)->paginate(30);
        }
        return compact('data');


    }
    public function store(Request  $request){
        $latest = Supplier::latest()->first();
        if($latest) {
            $latest_code=$latest->supplier_code;
            $num = substr($latest_code, -5);
            $result=(int)$num+1;
        } else {
          $result = 1;
        }
        $supplier_code = "S".str_pad($result,5,"0",STR_PAD_LEFT);
        Supplier::create([
            'name'=>$request->supplier_name,
            'supplier_code'=>$supplier_code,
            'phone_number'=>$request->supplier_phone_number,
            'country_id'=>$request->country_id,
            'state_id'=>$request->state_id,
            'township_id'=>$request->township_id,
            'supplier_shipping_address'=>$request->shipping_address,
            'supplier_billing_address'=>$request->billing_address,
            'is_active' =>1,
            'created_by' =>Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
        return response()->json([
            'status'=>'success',
        ]);
    }
    public function edit($id){
//        dd('hello world');
        $supplier=Supplier::find($id);
//        return compact('supplier');
        return response()->json([
            'supplier'=>$supplier,
        ]);
    }
    public function update(Request  $request,$id){
        Supplier::whereId($id)->update([
            'name'=>$request->supplier_name,
            'supplier_code'=>$request->supplier_code,
            'phone_number'=>$request->supplier_phone_number,
            'country_id'=>$request->country_id,
            'state_id'=>$request->state_id,
            'township_id'=>$request->township_id,
            'supplier_shipping_address'=>$request->shipping_address,
            'supplier_billing_address'=>$request->billing_address,
            'is_active' =>1,
            'created_by' =>Auth::user()->id,
            'updated_by' => Auth::user()->id,
            ]);
        return response()->json([
            'status'=>'success',
        ]);
    }
    public function changeStatus(Request  $request){
        $status=$request->status=='active' ? '1' :'0';
        $data=Supplier::whereId($request->id)->update([
            'is_active'=>$status,
        ]);
        return compact('data');
//        return response()->json([
//            'status'=>'success',
//        ]);
    }
}
