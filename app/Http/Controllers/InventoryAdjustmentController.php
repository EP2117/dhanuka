<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductTransition;
use App\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Report\GetReport;
use Illuminate\Support\Facades\Session;

class InventoryAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use GetReport;
    public function index(Request $request)
    {
        // $data=InventoryAdjustment::paginate(30);
        // return compact('data');
        // $limit = 30;
        // if ($request->has('limit')) {
        //     $limit = $request->limit;
        // }

        $login_year = Session::get('loginYear');

       	$data = InventoryAdjustment::with('products','warehouse','branch');

        if($request->invoice_no != "") {
            $data->where('invoice_no',  $request->invoice_no);
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
                $branch = Auth::user()->branch_id;
                $data->where('branch_id',$branch);
            }
        }

        if($request->from_date != '' && $request->to_date != '')
        {            
            $data->whereBetween('adjustment_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('adjustment_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $data->whereDate('adjustment_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('adjustment_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if($request->product_name != "") {
            $data->whereHas('products', function ($query) use ($request) {
                $binds = array(strtolower($request->product_name));
                $query->whereRaw('lower(product_name) like lower(?)', ["%{$request->product_name}%"]);
            });   
        }

        $data = $data->orderBy('adjustment_date', 'DESC')->paginate(30);

        return response(compact('data'), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'reference_no' => 'max:255|unique:inventory_adjustment',
            ]);
        }
        DB::beginTransaction();
        try {
        $max_id = InventoryAdjustment::max('id');
            if($max_id) {
                $max_id = $max_id + 1;
            } else {
                $max_id = 1;
            }
            $adjustment_no = "IA".str_pad($max_id,5,"0",STR_PAD_LEFT);
           $adjustment = new InventoryAdjustment();
        $adjustment->reference_no = $request->reference_no;
        $adjustment->adjustment_date = $request->adjustment_date;
        $adjustment->warehouse_id = Auth::user()->warehouse_id;
        $adjustment->branch_id = Auth::user()->branch_id;
        $adjustment->created_by = Auth::user()->id;
        $adjustment->updated_by = Auth::user()->id;        
        $adjustment->invoice_no = $adjustment_no;
        $adjustment->save();
        // dd($adjustment);
        $adjustment_id = $adjustment->id;

    
        for($i=0; $i<count($request->product); $i++) {
            if($request->add_qty[$i]==null){
                $qty=$request->less_qty[$i];
                $type="out";
            }else{
                $qty=$request->add_qty[$i];
                $type="in";

            }

            //add product into pivot table
            $pivot = $adjustment->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'add_qty' =>$request->add_qty[$i],'less_qty' =>$request->less_qty[$i]]);

            //get last pivot insert id
            $last_row=DB::table('product_inventory_adjustment')->orderBy('id', 'DESC')->first();

            $pivot_id = $last_row->id;
            $cost_price=$this->getCostPrice($request->product[$i])->product_cost_price;
             $store_cost_price=Product::find($request->product[$i]);
             if($cost_price==0){
                 $cost_price=$store_cost_price->purchase_price;
             }
             $store_cost_price->cost_price=$cost_price;
             $store_cost_price->save();
            //add products in transition table
            $obj = new ProductTransition();
            $obj->product_id 			= $request->product[$i];
            $obj->transition_type   	= $type;
            $obj->transition_adjustment_id 	= $adjustment_id;
            $obj->transition_product_pivot_id   = $pivot_id;
            $obj->branch_id = Auth::user()->branch_id;
            //$obj->warehouse_id			= 1; // for Main Warehouse Entry
            $obj->warehouse_id = Auth::user()->warehouse_id;
            $obj->transition_date 		= $request->adjustment_date;
            $obj->transition_product_uom_id        = $request->uom[$i];
            $obj->transition_product_quantity      = $qty;
            $obj->cost_price            = $cost_price * $qty;
            $obj->product_uom_id 		= $request->uom[$i];
            $obj->product_quantity 		= $qty;
            $obj->created_by = Auth::user()->id;
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            // dd($obj);
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

    public function edit($id)
    {
        $adjustment=InventoryAdjustment::find($id);
        return compact('adjustment');
        // $inven
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
        // dd($request->all());

        // dd($request->all());
        DB::beginTransaction();
        try {
        if(!empty($request->reference_no)) {
            $validatedData = $request->validate([
                'reference_no' => 'max:255|unique:inventory_adjustment,reference_no,'.$id,
            ]);
        }
        $adjustment = InventoryAdjustment::find($id);;
        $adjustment->adjustment_date = $request->adjustment_date;
        $adjustment->reference_no = $request->reference_no;
        $adjustment->updated_by = Auth::user()->id;
        $adjustment->save();
        $adjustment_id = $id;
        $ex_pivot_arr = $request->ex_product_pivot;

        //remove id in pivot that are removed in form
        $results =array_diff($ex_pivot_arr,$request->product_pivot); //get id that are not in request pivot array
        foreach($results as $key => $val) {
            //delete in pivot
            DB::table('product_inventory_adjustment')
                ->where('id', $val)
                ->delete();
            //delete in transition
            DB::table('product_transitions')
                ->where('transition_product_pivot_id', $val)
                ->where('transition_adjustment_id', $id)
                ->delete();
        }

        //update in product pivot table
        for($i=0; $i<count($request->product); $i++) {
            if($request->add_qty[$i]==null){
                $qty=$request->less_qty[$i];
                $type="out";
            }else{
                $qty=$request->add_qty[$i];
                $type="in";

            }
            //check product already exist or not
            if($request->product_pivot[$i] != '0' && in_array($request->product_pivot[$i], $ex_pivot_arr)) {
                //update existing product in pivot and transition tables
                DB::table('product_inventory_adjustment')
                    ->where('id', $request->product_pivot[$i])
                    ->update([
                        'add_qty' => $request->add_qty[$i],'less_qty'=>$request->less_qty[$i]
                    ]);
                    // if($request->add_qty[$i]==null){
                    //     $qty=$request->less_qty[$i];
                    //     $type="out";
                    // }else{
                    //     $qty=$request->add_qty[$i];
                    //     $type="in";

                    // }
                    $p=Product::find($request->product[$i]);
                    $cost_price=$p->cost_price;
                    // dd($qty);
                DB::table('product_transitions')
                    ->where('transition_product_pivot_id', $request->product_pivot[$i])
                    ->where('transition_adjustment_id', $id)
                    ->update(array('cost_price'=>$cost_price *$qty,'product_quantity' => $qty,'transition_type'=>$type, 'transition_product_quantity' => $qty));
            } else {
                //add product into pivot table if not exist
                $pivot = $adjustment->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'add_qty' => $request->add_qty[$i],'less_qty' => $request->less_qty[$i]]);
                //get last pivot insert id
                $last_row=DB::table('product_inventory_adjustment')->orderBy('id', 'DESC')->first();
                $pivot_id = $last_row->id;
                $store_cost_price=Product::find($request->product[$i]);
                $cost_price=$store_cost_price->cost_price;
                //add products in transition table
                $obj = new ProductTransition;
                $obj->product_id            = $request->product[$i];
                $obj->transition_type       = $type;
                $obj->transition_adjustment_id   = $adjustment_id;
                $obj->transition_product_pivot_id   = $pivot_id;
                $obj->branch_id = Auth::user()->branch_id;
                //$obj->warehouse_id          = 1; // for Main Warehouse Entry
                $obj->warehouse_id = Auth::user()->warehouse_id;
                $obj->transition_date       = $request->adjustment_date;
                $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $qty;
                $obj->cost_price=$cost_price;
                $obj->product_uom_id        = $request->uom[$i];
                $obj->product_quantity      = $qty;
                $obj->created_by = Auth::user()->id;
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $adjustment = InventoryAdjustment::find($id);
        //remove relations
        $adjustment->products()->detach();        

        //delete in transition table
        DB::table('product_transitions')
                    ->where('transition_adjustment_id', $id)
                    ->delete();
        //delete entry
        $adjustment->delete();

        return response(['message' => 'delete successful']);
    }
}
