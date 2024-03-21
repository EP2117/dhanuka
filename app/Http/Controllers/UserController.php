<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Brand;
use DB;
use Session;

class UserController extends Controller
{
    public function index(Request $request)
    {
        
        $data = User::with('role');
        if($request->role_id != "") {
            $data->where('role_id',  $request->role_id);
        }
        if($request->status != "") {
            $data->where('is_active',  $request->status);
        }
        $data = $data->orderBy('name', 'ASC')->get(); 
        return response(compact('data'), 200);
    }

    public function getSaleMan()
    {
        $data = User::with('role')->where('is_active',1);

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            foreach(Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);                
                }
            }

            $data->whereIn('id', $access_users);
        }
        elseif(Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach(Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            $data->whereIn('id', $ls_access_users);
        }
        elseif (Auth::user()->role->id == 2 || Auth::user()->role->id == 3) {
            //admin and office order
            $data->where(function ($query){
                        $query->where('role_id', 7)
                              ->orWhere('role_id',4);
                        });

        }
        elseif(Auth::user()->role->id == 1) {
            //admin and office order
            $data->where(function ($query){
                        $query->where('role_id', 7)
                              ->orWhere('role_id',4)
                              ->orWhere('role_id',1);
                        });
        }
        else {

        }

        $data = $data->orderBy('name', 'ASC')->get();
        return response(compact('data'), 200);
    }

    public function getOfficeSaleMan()
    {
        $data = User::with('role')->where('is_active',1);

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            foreach(Auth::user()->office_sale_mans as $osm) {
                array_push($access_users, $osm->id);
            }

            $data->whereIn('id', $access_users);
        }
        elseif (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3) {
            //system, admin and office user
            $data->where('role_id', 3);

        }
        else {

        }

        $data = $data->orderBy('name', 'ASC')->get();
        return response(compact('data'), 200);
    }

    public function getAllUsers()
    {
        $data = User::with('role')->where('role_id','!=',1)->where('is_active',1);

        $data = $data->orderBy('name', 'ASC')->get();
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
        $user = User::with('local_supervisor_children','country_head_children','brands','office_sale_mans','branch','branches')->find($id);
        return compact('user');
    }

    public function getByRole($id)
    {
        
        $data = User::where('role_id', $id)->where('is_active',1);
        if($id == 7) {
        	$data->whereNull('country_head_id');
        }
        if($id == 4) {
        	$data->whereNull('local_supervisor_id');
        }
        $data = $data->orderBy('name', 'ASC')->get(); 
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
        $validatedData = $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
        ]);
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->warehouse_id = $request->warehouse_id;
        $user->role_id = $request->role_id;
        $user->password = Hash::make($request->password); 
        /*if(!empty($request->branch_id)) {
            $user->branch_id = $request->branch_id;
        } else {
            $user->branch_id = NULL;
        }*/
        $user->save();

         //for country head role
        if($request->role_id == 6) {

        	for($i=0; $i<count($request->local_supervisors); $i++) {
        		$user1 = User::find($request->local_supervisors[$i]);
        		$user1->country_head_id = $user->id;	
        		$user1->save();	
        	}

        	for($j=0; $j<count($request->brands); $j++) {
        		$brand = Brand::find($request->brands[$j]);
        		$brand->country_head_id = $user->id;
        		$brand->save();
        	}

            for($i=0; $i<count($request->office_sale_man); $i++) {                

                $user->office_sale_mans()->attach($request->office_sale_man[$i]);
            }
        }

        //for local supervisor role
        if($request->role_id == 7) {
        	for($i=0; $i<count($request->so_man); $i++) {
        		$user1 = User::find($request->so_man[$i]);
        		$user1->local_supervisor_id = $user->id;
        		$user1->save();		
        	}
        }

       /* if($request->role_id == 6 || $request->role_id == 2) {
            //for country head and admin
            if(count($request->branches) > 0) {
                for($i=0; $i<count($request->branches); $i++) {
                    $user->branches()->attach($request->branches[$i]);
                }
            }
        } */
        if(!empty($request->branch_id)) {
            $user->branches()->attach($request->branch_id);
        } else {

            if(count($request->branches) > 0) {
                for($i=0; $i<count($request->branches); $i++) {
                    $user->branches()->attach($request->branches[$i]);
                }
            }
        }

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
    	$validatedData = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'password' => 'confirmed',
        ]);
        
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->warehouse_id = $request->warehouse_id;
        $user->role_id = $request->role_id;

        if(!empty($request->password)) {
        	$user->password = Hash::make($request->password); 
        }

        /*if(!empty($request->branch_id)) {
            $user->branch_id = $request->branch_id;
        } else {
            $user->branch_id = NULL;
        }*/

        $user->save();

        //reset crountry head and its local supervisor relations;
    	DB::table('users')
                ->where('country_head_id', $user->id)
                ->update(array('country_head_id' => null));
        //reset crountry head and brands relations;
    	DB::table('brands')
                ->where('country_head_id', $user->id)
                ->update(array('country_head_id' => null));
        //reset local supervisor and its sale order man relations;
    	DB::table('users')
                ->where('local_supervisor_id', $user->id)
                ->update(array('local_supervisor_id' => null));
        //remove previous office sale man relations
        $user->office_sale_mans()->detach();
         //for country head role
        if($request->role_id == 6) {        	

        	for($i=0; $i<count($request->local_supervisors); $i++) {
        		$user1 = User::find($request->local_supervisors[$i]);
        		$user1->country_head_id = $user->id;	
        		$user1->save();	
        	}        	

        	for($j=0; $j<count($request->brands); $j++) {
        		$brand = Brand::find($request->brands[$j]);
        		$brand->country_head_id = $user->id;
        		$brand->save();
        	}

            for($i=0; $i<count($request->office_sale_man); $i++) {                

                $user->office_sale_mans()->attach($request->office_sale_man[$i]);
            }
        }

        //for local supervisor role
        if($request->role_id == 7) {        	

        	for($i=0; $i<count($request->so_man); $i++) {
        		$user1 = User::find($request->so_man[$i]);
        		$user1->local_supervisor_id = $user->id;
        		$user1->save();		
        	}
        }

        //remove previous branch relations
        $user->branches()->detach();

        /*if($request->role_id == 6 || $request->role_id == 2) {
            //for country head and admin
            if(count($request->branches) > 0) {
                for($i=0; $i<count($request->branches); $i++) {
                    $user->branches()->attach($request->branches[$i]);
                }
            }
        } */
        if(!empty($request->branch_id)) {
            $user->branches()->attach($request->branch_id);
        } else {

            if(count($request->branches) > 0) {
                for($i=0; $i<count($request->branches); $i++) {
                    $user->branches()->attach($request->branches[$i]);
                }
            }
        }

        $status = "success";
        return compact('status');       

    }

    //change user online status to offline
    public function offUser($id) {
    	DB::table('users')
                ->where('id', $id)
                ->update(array('online_status' => 0));
        $status = "success";
        return compact('status'); 
    }
    
    public function getBrands()
    {
        $data = Brand::select(['id', 'brand_name'])->whereNull('country_head_id');

        $data = $data->orderBy('id', 'DESC')->get();
        return response(compact('data'), 200);
    }
    public function updateStatus($id, $status)
    {
        $data = User::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }
}
