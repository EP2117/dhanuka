<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Installer;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstallerController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 

        if($request->name != "") {
        	$data = Installer::where('name',  'LIKE', '%'.$request->name.'%');
            $data = $data->orderBy('name', 'ASC')->paginate($limit);
        } else {
        	$data = Installer::orderBy('name', 'ASC')->paginate($limit);
        }

        //$data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allInstallers()
    {
        $data = Installer::orderBy('name', 'ASC')->where('is_active',1)->get();
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
        $obj = new Installer;
        $obj->name = $request->name;
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
        
        $obj = Installer::find($id);
        $obj->name = $request->name;
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
        $data = Installer::find($id);
        return compact('data');
    }

    public function updateStatus($id, $status)
    {
        $data = Installer::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }
}
