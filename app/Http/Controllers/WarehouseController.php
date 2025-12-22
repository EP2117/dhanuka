<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse;
use App\ProductTransition;
use App\Imports\WarehouseImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }        

       $data = Warehouse::with('branch');
        if($request->warehouse_name != "") {
            $data->where('warehouse_name',  'LIKE', '%'.$request->warehouse_name.'%');
        }

        if($request->branch_id != "") {
            $data->where('branch_id', $request->branch_id);
        }

        $data = $data->orderBy('warehouse_name', 'ASC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allWarehouses(Request $request)
    {
        if(isset($request->branch)) {
            $branch_arr = explode(',', $request->branch);
            if(count($branch_arr) > 1) {

                $data = Warehouse::whereIn('branch_id',$branch_arr);
                if($request->role_id == 3) {
                    $data->where('warehouse_type', 1);
                }
                $data = $data->where('is_active',1)->orderBy('warehouse_name', 'ASC')->get();

            } else {
                $data = Warehouse::where('branch_id',$branch_arr[0]);
                if($request->role_id == 3) {
                    $data->where('warehouse_type', 1);
                }
                $data = $data->where('is_active',1)->orderBy('warehouse_name', 'ASC')->get();
            }
        } else {
            $data = Warehouse::where('is_active',1)->orderBy('warehouse_name', 'ASC')->get();
        }
        
        return response(compact('data'), 200);
    }

    public function warehouseByBranch($id)
    {
        if($id != "null") {
            $data = Warehouse::where('is_active',1)->orderBy('warehouse_name', 'DESC')
                ->where('branch_id',$id)
                ->get();
        } else {
            $data = Warehouse::where('is_active',1)->orderBy('warehouse_name', 'ASC')->get();
        }
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
        $obj = new Warehouse;
        $obj->warehouse_name = $request->warehouse_name;
        $obj->branch_id = $request->branch_id;
        $obj->warehouse_type = $request->wh_type;
        $obj->is_active = 1;
        $obj->created_by = Auth::user()->id;
        //$obj->updated_by = Auth::user()->id;        
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
        
        $obj = Warehouse::find($id);
        $obj->warehouse_name = $request->warehouse_name;
        $obj->branch_id = $request->branch_id;
        $obj->warehouse_type = $request->wh_type;
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
        $warehouse = Warehouse::find($id);
        return compact('warehouse');
    }

    public function updateStatus($id, $status)
    {
        $data = Warehouse::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new WarehouseImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }
}
