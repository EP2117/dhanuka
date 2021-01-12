<?php

namespace App\Http\Controllers;

use App\Http\Traits\Report\GetReport;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Transfer;
use App\ProductTransition;
use Carbon\Carbon;
use App\Product;
use Session;
use Illuminate\Support\Facades\DB;


class TransferController extends Controller
{
    use GetReport;
    public function index(Request $request)
    {
        $login_year = Session::get('loginYear');

        if(Auth::user()->role->role_name == "admin" || Auth::user()->role->role_name == "system") {
       	    $data = Transfer::with('from_warehouse','to_warehouse','branch');
            $data->whereBetween('transfer_date', array($login_year.'-01-01', $login_year.'-12-31'));
           // $data = $data->orderBy('id', 'DESC')->get();
        } else {
            $data = Transfer::with('from_warehouse','to_warehouse','branch')
                        ->where('from_warehouse_id',Auth::user()->warehouse_id);
            $data->whereBetween('transfer_date', array($login_year.'-01-01', $login_year.'-12-31'));
           // $data = $data->orderBy('id', 'DESC')->get(); 
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id',$branch_arr);
            }
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $data->where('branch_id',$branch);
            }
        }

        $data = $data->orderBy('id', 'DESC')->get();

        return response(compact('data'), 200);
    }


    public function getReceive()
    {
        $login_year = Session::get('loginYear');

        if(Auth::user()->role->role_name == "admin" || Auth::user()->role->role_name == "system") {
            $data = Transfer::with('from_warehouse','to_warehouse','branch');
            $data->whereBetween('transfer_date', array($login_year.'-01-01', $login_year.'-12-31'));
           // $data = $data->orderBy('id', 'DESC')->get();
        } else {
            $data = Transfer::with('from_warehouse','to_warehouse','branch')
                                ->where('to_warehouse_id',Auth::user()->warehouse_id);
            $data->whereBetween('transfer_date', array($login_year.'-01-01', $login_year.'-12-31'));
           // $data = $data->orderBy('id', 'DESC')->get();   
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereHas('to_warehouse', function ($query) use ($branch_arr) {
                    $query->whereIn('branch_id', $branch_arr);                      
                });
                //$data->whereIn('branch_id',$branch_arr);
            }
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $data->whereHas('to_warehouse', function ($query) use ($branch) {
                    $query->where('branch_id', $branch);                      
                });
                //$data->where('branch_id',$branch);
            }
        }
        $data = $data->orderBy('id', 'DESC')->get();
        return response(compact('data'), 200);
    }

    public function receiveTransfer($id)
    {
        $data = Transfer::with('products','branch')->find($id);

        $received_date = Carbon::now()->format('Y-m-d');
        $data->received_date = $received_date;
        $data->received_by = Auth::user()->id;
        $data->save();

        

        foreach($data->products as $product) {

        	//calculate quantity for product pre-defined UOM
        	$uom_relation = DB::table('product_selling_uom')
    			   			->select('relation')
    			   			->where('product_id',$product->pivot->product_id)
    			   			->where('uom_id',$product->pivot->uom_id)
    			   			->first();
    		if($uom_relation) {
    			$relation_val = $uom_relation->relation;
    		} else {
    			//for pre-defined product uom
    			$relation_val = 1;
    		}

            //add products in transition table=> transfer_type = in (for received warehouse)
            $obj = new ProductTransition;
            $obj->product_id            = $product->pivot->product_id;
            $obj->transition_type       = "in";
            $obj->transition_transfer_id   = $id;
            $obj->transition_product_pivot_id   = $product->pivot->id;
            $obj->branch_id  = Auth::user()->branch_id;
            $obj->warehouse_id          = $data->to_warehouse_id; // for Main Warehouse Entry
            $obj->transition_date       = $received_date;
            $obj->transition_product_uom_id        = $product->pivot->uom_id;
            $obj->transition_product_quantity      = $product->pivot->product_quantity;
            $obj->product_uom_id        = $product->uom_id;
            $obj->product_quantity      = $product->pivot->product_quantity * $relation_val;
            $obj->created_by = Auth::user()->id;
            $obj->updated_by = Auth::user()->id;
            $obj->save();
        }

        return response(compact('data'), 200);
    }

    //get max id from transfers table
    public function getMaxId() {
    	$max_id = Transfer::max('id');
    	return compact('max_id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
        //auto generate invoice no;
        $max_id = Transfer::withTrashed()->max('id');
        if($max_id) {
            $max_id = $max_id + 1;
        } else {
            $max_id = 1;
        }

        $transfer_no = str_pad($max_id,5,"0",STR_PAD_LEFT); 

        $transfer = new Transfer;
        $transfer->transfer_no = $transfer_no;
        $transfer->transfer_date = $request->transfer_date;
        $transfer->branch_id = Auth::user()->branch_id;
        $transfer->from_warehouse_id = Auth::user()->warehouse_id;
        $transfer->to_warehouse_id = $request->to_warehouse;
        $transfer->created_by = Auth::user()->id;
        $transfer->updated_by = Auth::user()->id;        
        $transfer->save();

        $transfer_id = $transfer->id;

        
        for($i=0; $i<count($request->product); $i++) {

        	//get product pre-defined UOM
        	$product_result = Product::select('uom_id')->find($request->product[$i]);
        	$main_uom_id = $product_result->uom_id;

            //add product into pivot table
            $pivot = $transfer->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i]]);  

            //get last pivot insert id
            $last_row=DB::table('product_transfer')->orderBy('id', 'DESC')->first();

            $pivot_id = $last_row->id; 

            //calculate quantity for product pre-defined UOM
        	$uom_relation = DB::table('product_selling_uom')
    			   			->select('relation')
    			   			->where('product_id',$request->product[$i])
    			   			->where('uom_id',$request->uom[$i])
    			   			->first();
    		if($uom_relation) {
    			$relation_val = $uom_relation->relation;
    		} else {
    			//for pre-defined product uom
    			$relation_val = 1;
    		}
            $cost_price=$this->getCostPrice($request->product[$i])->product_cost_price;
            $store_cost_price=Product::find($request->product[$i]);
            if($cost_price==0){
                $cost_price=$store_cost_price->purchase_price;
            }
            //add products in transition table=> transfer_type = out (for transfer warehouse)
            $obj = new ProductTransition;
            $obj->product_id            = $request->product[$i];
            $obj->transition_type       = "out";
            $obj->transition_transfer_id   = $transfer_id;
            $obj->transition_product_pivot_id   = $pivot_id;
            $obj->branch_id  = Auth::user()->branch_id;
            $obj->warehouse_id          = Auth::user()->warehouse_id; // for Main Warehouse Entry
            $obj->transition_date       = $request->transfer_date;
            $obj->transition_product_uom_id        = $request->uom[$i];
            $obj->transition_product_quantity      = $request->qty[$i];
            $obj->cost_price            = $cost_price * $request->qty[$i];
            $obj->product_uom_id        = $main_uom_id;
            $obj->product_quantity      = $request->qty[$i] * $relation_val;
            $obj->created_by = Auth::user()->id;
            $obj->updated_by = Auth::user()->id;
            $obj->save();         
        }

        $status = "success";
        DB::commit();
            return compact('status');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
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
        DB::beginTransaction();
        try {
        $transfer = Transfer::find($id);
        $transfer->transfer_date = $request->transfer_date;
        $transfer->from_warehouse_id = Auth::user()->warehouse_id;
        $transfer->to_warehouse_id = $request->to_warehouse;
        //$transfer->created_by = Auth::user()->id;
        $transfer->updated_by = Auth::user()->id;        
        $transfer->save();
        
        $ex_pivot_arr = $request->ex_product_pivot;

        //remove id in pivot that are removed in Transfer Form
        $results =array_diff($ex_pivot_arr,$request->product_pivot); //get id that are not in request pivot array
        foreach($results as $key => $val) {
            //delete in pivot
            DB::table('product_transfer')
                ->where('id', $val)
                ->delete();
            //delete in transition
            DB::table('product_transitions')
                ->where('transition_product_pivot_id', $val)
                ->where('transition_transfer_id', $id)
                ->delete();
        }

        //update in product pivot table
        for($i=0; $i<count($request->product); $i++) {

            //check product already exist or not
            if($request->product_pivot[$i] != '0' && in_array($request->product_pivot[$i], $ex_pivot_arr)) {
                //update existing product in pivot and transition tables
                DB::table('product_transfer')
                    ->where('id', $request->product_pivot[$i])
                    ->update(array('uom_id' => $request->uom[$i],'product_quantity' => $request->qty[$i]));

                //get product pre-defined UOM
	        	$product_result = Product::select('uom_id')->find($request->product[$i]);
	        	$main_uom_id = $product_result->uom_id;

	        	//calculate quantity for product pre-defined UOM
	        	$uom_relation = DB::table('product_selling_uom')
	    			   			->select('relation')
	    			   			->where('product_id',$request->product[$i])
	    			   			->where('uom_id',$request->uom[$i])
	    			   			->first();
	    		if($uom_relation) {
	    			$relation_val = $uom_relation->relation;
	    		} else {
	    			//for pre-defined product uom
	    			$relation_val = 1;
	    		}
	    		$product_qty = $request->qty[$i] * $relation_val;
                $p=Product::find($request->product[$i]);
                $cost_price=$p->cost_price;
                DB::table('product_transitions')
                    ->where('transition_product_pivot_id', $request->product_pivot[$i])
                    ->where('transition_transfer_id', $id)
                    ->update(array('cost_price'=>$cost_price *$request->qty[$i],'product_uom_id' => $main_uom_id, 'product_quantity' => $product_qty, 'transition_product_uom_id' => $request->uom[$i], 'transition_product_quantity' => $request->qty[$i]));
            } else {
                //add product into pivot table if not exist
                //get product pre-defined UOM
	        	$product_result = Product::select('uom_id')->find($request->product[$i]);
	        	$main_uom_id = $product_result->uom_id;

	            //add product into pivot table
	            $pivot = $transfer->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i]]);  

	            //get last pivot insert id
	            $last_row=DB::table('product_transfer')->orderBy('id', 'DESC')->first();

	            $pivot_id = $last_row->id; 

	            //calculate quantity for product pre-defined UOM
	        	$uom_relation = DB::table('product_selling_uom')
	    			   			->select('relation')
	    			   			->where('product_id',$request->product[$i])
	    			   			->where('uom_id',$request->uom[$i])
	    			   			->first();
	    		if($uom_relation) {
	    			$relation_val = $uom_relation->relation;
	    		} else {
	    			//for pre-defined product uom
	    			$relation_val = 1;
                }
                $store_cost_price=Product::find($request->product[$i]);
                $cost_price=$store_cost_price->cost_price;

	    		//add products in transition table=> transfer_type = out (for transfer warehouse)
	            $obj = new ProductTransition;
	            $obj->product_id            = $request->product[$i];
	            $obj->transition_type       = "out";
	            $obj->transition_transfer_id   = $id;
	            $obj->transition_product_pivot_id   = $pivot_id;
                $obj->branch_id  = Auth::user()->branch_id;
	            $obj->warehouse_id          = Auth::user()->warehouse_id; // for Main Warehouse Entry
	            $obj->transition_date       = $request->transfer_date;
	            $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $request->qty[$i];
                $obj->cost_price            = $cost_price * $request->qty[$i];

	            $obj->product_uom_id        = $main_uom_id;
	            $obj->product_quantity      = $request->qty[$i] * $relation_val;
	           // $obj->created_by = Auth::user()->id;
	            $obj->updated_by = Auth::user()->id;
	            $obj->save();
            }
        }

        $status = "success";
        DB::commit();
        return compact('status');
    } catch (\Throwable $e) {
        dd($e->getMessage());
        DB::rollback();
        $status = "fail";
        return compact('status');
        throw $e;
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
        $transfer = Transfer::with('products','from_warehouse','to_warehouse','products.uom','products.selling_uoms','branch')->find($id);
        return compact('transfer');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transfer = Transfer::find($id);
        $transfer->products()->detach();
        //delete in transition
        DB::table('product_transitions')
            ->where('transition_transfer_id', $id)
            ->delete();
        $transfer->delete();
        return response(['message' => 'delete successful']);
    }
}
