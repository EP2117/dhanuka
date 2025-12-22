<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Township;
use App\Imports\TownshipImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class TownshipController extends Controller
{
    public function index()
    {
        $data = Township::with('state')->where('is_active',1)->orderBy('township_name', 'ASC')->get();
        return response(compact('data'), 200);
    }

    public function getTownship(Request $request){
//        dd($request->all());
        $data = Township::with('state');

        if(!empty($request->state_id)){
            $data->where('state_id',$request->state_id);
        } 

        if(!empty($request->township_name)){
            $data->where('township_name', 'LIKE', '%'.$request->township_name.'%');
        }
        
        $data = $data->orderBy('township_name', 'ASC')->paginate(30);
        return compact('data');


    }

	public function townshipByState($id)
    {
        $data = Township::where('is_active',1)->orderBy('township_name', 'DESC')
                ->where('state_id',$id)
                ->get();
        return response(compact('data'), 200);
    }

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new TownshipImport();
        Excel::import($import,$path);
        return ["message" => "success"];
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'township_name' => 'required|max:255|unique:townships',
        ]);
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
        {
            $obj = new Township;
            $obj->township_name = $request->township_name;
            $obj->state_id = $request->state_id;
            $obj->created_by = Auth::user()->id;
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            return response()->json([
                'status'=>'success',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $township = Township::findOrFail($id);
        return response(compact('township'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validatedData = $request->validate([
            'township_name' =>
            'required|max:255|unique:townships,township_name,' . $id,
        ]);
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
        {
            $obj = Township::find($id);;
            $obj->township_name = $request->township_name;
            $obj->state_id = $request->state_id;
            $obj->updated_by = Auth::user()->id;
            $obj->save();

            return response()->json([
                'status'=>'success',
            ]);
        }
    }

    public function changeStatus(Request  $request){
        $status=$request->status=='active' ? '1' :'0';
        $data=Township::whereId($request->id)->update([
            'is_active'=>$status,
        ]);
        return compact('data');
//        return response()->json([
//            'status'=>'success',
//        ]);
    }
}
