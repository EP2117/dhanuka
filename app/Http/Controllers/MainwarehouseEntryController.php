<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\MainwarehouseEntry;
use App\ProductTransition;
use App\Product;
use DB;
use Session;

class MainwarehouseEntryController extends Controller
{
	public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }

        $login_year = Session::get('loginYear');

       	$data = MainwarehouseEntry::with('products','warehouse','branch');

        if($request->entry_no != "") {
            $data->where('entry_no',  $request->entry_no);
        }

        if($request->reference_no != "") {
            $data->where('reference_no',  $request->reference_no);
        }

        if($request->branch_id != "") {
            $data->where('branch_id',  $request->branch_id);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if(Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $data->whereIn('branch_id',$branch_arr);
        } else {
            //other roles can access only one branch
            if(Auth::user()->role->id != 1) { //system can access all branches
                /*$branch = Auth::user()->branch_id;
                $data->where('branch_id',$branch);*/
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id',$branch_arr);
            }
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $data->whereBetween('entry_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('entry_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $data->whereDate('entry_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('entry_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if($request->product_name != "") {
            $data->whereHas('products', function ($query) use ($request) {
                $binds = array(strtolower($request->product_name));
                $query->whereRaw('lower(product_name) like lower(?)', ["%{$request->product_name}%"]);
            });   
        }

        $data = $data->orderBy('entry_date', 'DESC')->paginate($limit);

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
        $entry = MainwarehouseEntry::with('products','warehouse','products.uom','branch')->find($id);
        return compact('entry');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->reference_no)) {
            $validatedData = $request->validate([
                'reference_no' => 'max:255|unique:mainwarehouse_entries',
            ]);
        }
        foreach (Auth::user()->branches as $k => $b) {
            if ($k == 0) {
                $branch_id = $b->id;
            }
        }
       	$entry = new MainwarehouseEntry;
        $entry->entry_date = $request->entry_date;
        $entry->warehouse_id = Auth::user()->warehouse_id;
        $entry->branch_id = $branch_id;
        $entry->created_by = Auth::user()->id;
        $entry->updated_by = Auth::user()->id;        
        $entry->save();

        $entry->entry_no = str_pad($entry->id,5,"0",STR_PAD_LEFT);
        $entry->reference_no = $request->reference_no;
        $entry->save();

        $entry_id = $entry->id;

        
        for($i=0; $i<count($request->product); $i++) {

            //add product into pivot table
            $pivot = $entry->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i]]);

            //get last pivot insert id
            $last_row=DB::table('product_mainwarehouse_entry')->orderBy('id', 'DESC')->first();

            $pivot_id = $last_row->id;


            //add products in transition table
        	$obj = new ProductTransition;
        	$obj->product_id 			= $request->product[$i];
        	$obj->transition_type   	= "in";
        	$obj->transition_entry_id 	= $entry_id;
            $obj->transition_product_pivot_id   = $pivot_id;
            $obj->branch_id = $branch_id;
        	//$obj->warehouse_id			= 1; // for Main Warehouse Entry
            $obj->warehouse_id = Auth::user()->warehouse_id;
        	$obj->transition_date 		= $request->entry_date;
            $obj->transition_product_uom_id        = $request->uom[$i];
            $obj->transition_product_quantity      = $request->qty[$i];
        	$obj->product_uom_id 		= $request->uom[$i];
        	$obj->product_quantity 		= $request->qty[$i];
        	$obj->created_by = Auth::user()->id;
        	$obj->updated_by = Auth::user()->id;
        	$obj->save();
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
        /*try {
            $this->validate($request, [
                'entry_date' => 'required',
                'reference_no' => 'max:255|unique:sales,reference_no,'.$id,
            ]);*/

            if(!empty($request->reference_no)) {
                $validatedData = $request->validate([
                    'reference_no' => 'max:255|unique:mainwarehouse_entries,reference_no,'.$id,
                ]);
            }

            foreach (Auth::user()->branches as $k => $b) {
                if ($k == 0) {
                    $branch_id = $b->id;
                }
            }

            $entry = MainwarehouseEntry::find($id);;
            $entry->entry_date = $request->entry_date;
            $entry->reference_no = $request->reference_no;
            $entry->updated_by = Auth::user()->id;
            $entry->save();

            $entry_id = $id;

            $ex_pivot_arr = $request->ex_product_pivot;

            //remove id in pivot that are removed in form
            $results =array_diff($ex_pivot_arr,$request->product_pivot); //get id that are not in request pivot array
            foreach($results as $key => $val) {
                //delete in pivot
                DB::table('product_mainwarehouse_entry')
                    ->where('id', $val)
                    ->delete();
                //delete in transition
                DB::table('product_transitions')
                    ->where('transition_product_pivot_id', $val)
                    ->where('transition_entry_id', $id)
                    ->delete();
            }

            //update in product pivot table
            for($i=0; $i<count($request->product); $i++) {

                //check product already exist or not
                if($request->product_pivot[$i] != '0' && in_array($request->product_pivot[$i], $ex_pivot_arr)) {
                    //update existing product in pivot and transition tables
                    DB::table('product_mainwarehouse_entry')
                        ->where('id', $request->product_pivot[$i])
                        ->update(array('product_quantity' => $request->qty[$i]));

                    DB::table('product_transitions')
                        ->where('transition_product_pivot_id', $request->product_pivot[$i])
                        ->where('transition_entry_id', $id)
                        ->update(array('product_quantity' => $request->qty[$i], 'transition_date' => $request->entry_date, 'transition_product_quantity' => $request->qty[$i]));
                } else {

                    //add product into pivot table if not exist
                    $pivot = $entry->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i]]);

                    //get last pivot insert id
                    $last_row=DB::table('product_mainwarehouse_entry')->orderBy('id', 'DESC')->first();

                    $pivot_id = $last_row->id;


                    //add products in transition table
                    $obj = new ProductTransition;
                    $obj->product_id            = $request->product[$i];
                    $obj->transition_type       = "in";
                    $obj->transition_entry_id   = $entry_id;
                    $obj->transition_product_pivot_id   = $pivot_id;
                    $obj->branch_id = $branch_id;
                    //$obj->warehouse_id          = 1; // for Main Warehouse Entry
                    $obj->warehouse_id = Auth::user()->warehouse_id;
                    $obj->transition_date       = $request->entry_date;
                    $obj->transition_product_uom_id        = $request->uom[$i];
                    $obj->transition_product_quantity      = $request->qty[$i];
                    $obj->product_uom_id        = $request->uom[$i];
                    $obj->product_quantity      = $request->qty[$i];
                    $obj->created_by = Auth::user()->id;
                    $obj->updated_by = Auth::user()->id;
                    $obj->save();
                }
            }

            $status = "success";
            return compact('status');
       /* }
        catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error',
                'errors' => json_encode($exception->errors()),
            ], 422);
        }*/

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry = MainwarehouseEntry::find($id);
        //remove relations
        $entry->products()->detach();        

        //delete in transition table
        DB::table('product_transitions')
                    ->where('transition_entry_id', $id)
                    ->delete();
        //delete entry
        $entry->delete();

        return response(['message' => 'delete successful']);
    }
}
