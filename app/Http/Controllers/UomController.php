<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Imports\UomImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Uom;

class UomController extends Controller
{
    public function index(Request $request)
    {
        $limit = 30;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }        

        //$data = Customer::with('customer_type','state','township','country');
        /*if($request->customer_id != "") {
            $sales->where('customer_id', $request->customer_id);
        }*/
        $data = Uom::select(['id', 'uom_name','is_active']);

        if($request->uom_name != "") {
            $data->where('uom_name', 'LIKE', '%'.$request->uom_name.'%');
        }

        if($request->status != "") {
            $data->where('is_active', $request->status);
        }

        $data = $data->orderBy('uom_name', 'ASC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allUoms()
    {
        $data = Uom::select(['id', 'uom_name'])->orderBy('id', 'DESC')->get();
        return response(compact('data'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $uom = Uom::find($id);
        return compact('uom');
    }

    public function updateStatus($id, $status)
    {
        $data = Uom::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
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
        try {
            $this->validate($request, [
                'uom_name' => 'required|max:255|unique:uoms',
            ]);

            $uom = new Uom;
            $uom->uom_name = $request->uom_name;
            $uom->created_by = Auth::user()->id;
            $uom->save();

            $status = "success";
            return compact('status');
        }
        catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error',
                'errors' => json_encode($exception->errors()),
            ], 422);
        }
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
        try {
            $this->validate($request, [
                'uom_name' => 'required|max:255|unique:uoms,uom_name,'.$id,
            ]);

            $uom = Uom::find($id);
            $uom->uom_name      = $request->uom_name;
            $uom->updated_at = time();
            $uom->updated_by = Auth::user()->id;
            $uom->save();
            $status = "success";
            return compact('status');
        }
        catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error',
                'errors' => json_encode($exception->errors()),
            ], 422);
        }

    }

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new UomImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }
}
