<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SaleMan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SaleManController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 

        if($request->name != "") {
        	$data = SaleMan::where('sale_man',  'LIKE', '%'.$request->name.'%');
            $data = $data->orderBy('sale_man', 'ASC')->paginate($limit);
        } else {
        	$data = SaleMan::orderBy('sale_man', 'ASC')->paginate($limit);
        }

        //$data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allSaleMen()
    {
        $data = SaleMan::orderBy('sale_man', 'ASC')->where('is_active',1)->get();
        return response(compact('data'), 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $obj = new SaleMan;
        $obj->sale_man = $request->name;
        $obj->phone = $request->phone;
        $obj->is_active = 1;
        $obj->created_by = Auth::user()->id;      
        $obj->save();

        $status = "success";
        return compact('status');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $obj = SaleMan::find($id);
        $obj->sale_man = $request->name;
        $obj->phone = $request->phone;
        $obj->updated_by = Auth::user()->id;        
        $obj->save();

        $status = "success";
        return compact('status');       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = SaleMan::find($id);
        return compact('data');
    }

    public function updateStatus($id, $status)
    {
        $data = SaleMan::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }
    public function getSaleMan(){
        $sale_man=SaleMan::orderBy('name','asc')->get();
        return compact('sale_man');
    }
}
