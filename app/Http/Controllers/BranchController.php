<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }        

       //$data = Branch::with('customer_type','state','township','country');
        if($request->branch_name != "") {
        	$data = Branch::where('branch_name',  'LIKE', '%'.$request->branch_name.'%');
            $data = $data->orderBy('branch_name', 'ASC')->paginate($limit);
        } else {
        	$data = Branch::orderBy('branch_name', 'ASC')->paginate($limit);
        }

        //$data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allBranches()
    {
        $data = Branch::orderBy('branch_name', 'ASC')->where('is_active',1)->get();
        return response(compact('data'), 200);
    }

    public function getBranchByUser()
    {
        if(Auth::user()->role->role_name == "system") {
            $data = Branch::orderBy('branch_name', 'ASC')->where('is_active',1)->get();
        }
        else {
           $access_branches = array();
            foreach(Auth::user()->branches as $b) {
                array_push($access_branches, $b->id);
            }
            
            $data = Branch::where('is_active',1);
            $data->whereIn('id',$access_branches);
            $data = $data->orderBy('branch_name', 'ASC')->get();  
        }
        /*else if(Auth::user()->role->role_name == "Country Head" || Auth::user()->role->role_name == "admin") {
            $access_branches = array();
            foreach(Auth::user()->branches as $b) {
                array_push($access_branches, $b->id);
            }
            $data = Branch::where('is_active',1);
            $data->whereIn('id',$access_branches);
            $data = $data->orderBy('branch_name', 'ASC')->get(); 
        } else {
            $data = Branch::where('is_active',1);
            $data->where('id',Auth::user()->branch_id);
            $data = $data->orderBy('branch_name', 'ASC')->get(); 
        }*/
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
        $obj = new Branch;
        $obj->branch_name = $request->branch_name;
        $obj->is_active = 1;
        $obj->created_by = Auth::user()->id;
        $obj->updated_by = Auth::user()->id;        
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
        
        $obj = Branch::find($id);
        $obj->branch_name = $request->branch_name;
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
        $branch = Branch::find($id);
        return compact('branch');
    }

    public function updateStatus($id, $status)
    {
        $data = Branch::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }
}
