<?php

namespace App\Http\Controllers;

use App\AccountHead;
use App\FinancialType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountHeadController extends Controller
{
    public function getAll(Request $request){
        $account_head=AccountHead::orderBy('id','desc');
        if($request->name!=null){
            $account_head->where('name', 'LIKE', '%'.$request->name.'%');
        }
        if($request->financial_type1_id!=null){
            $account_head->where('financial_type1_id',$request->financial_type1_id);
        }
        if($request->financial_type1_id!=null){
            $account_head->where('financial_type1_id',$request->financial_type1_id);
        }
        $account_head=$account_head->paginate(30);
        return response(compact('account_head'));
    }
   
    public function getFinancialType($type){
        if($type==1){
            $financial_type=FinancialType::where('type','type1')->get();
        }else{
            $financial_type=FinancialType::where('type','type2')->get();
        }
        return compact('financial_type');
    }
    public function store(Request  $request){
        AccountHead::create([
            'name'=>$request->name,
            'financial_type1_id'=>$request->financial_type1,
            'financial_type2_id'=>$request->financial_type2,
            'created_by'=>Auth::user()->id,
        ]);
        return response()->json(['status'=>'success']);
    }
    public function edit($id){
        $account_head=AccountHead::find($id);
        return compact('account_head');
    }
    public function update($id,Request $request){
        AccountHead::whereId($id)->update([
            'name'=>$request->name,
            'financial_type1_id'=>$request->financial_type1,
            'financial_type2_id'=>$request->financial_type2,
            'updated_by'=>Auth::user()->id,
        ]);
        return response()->json([
            'status'=>'success'
        ]);
    }
    public function ChangeStatus($id, $status)
    {
        $active = $status == "active" ? '1' : '0';
        $account_head=AccountHead::whereId($id)->update(['is_active'=>$active]);
        return response(compact('account_head'), 200);
    }
    public function destroy($id){
        AccountHead::whereId($id)->delete();
        return response(['message' => 'delete successful']);

    }
}
