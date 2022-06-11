<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Imports\ProductImport;
use App\Imports\ProductMinQtyImport;
use App\Exports\ProductExport;
use App\Http\Traits\Report\GetReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Product;
use Carbon\Carbon;
use DB;
use PDF;
class ProductController extends Controller
{
    use GetReport;
	public function index(Request $request)
    {
        $limit = 30;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }

       	//$data = Product::with('uom','brand','category');
        //$data->select('products.*','uoms.uom_name','brands.brand_name','categories.category_name');

        $data = Product::with('photos')->select([
                  'products.*',
                  'uoms.uom_name',
                  'brands.brand_name',
                  'categories.category_name',
                ]);

        $data->leftjoin('uoms', 'uoms.id','products.uom_id');
        $data->leftjoin('brands', 'brands.id','products.brand_id');
        $data->leftjoin('categories', 'categories.id','products.category_id');
    //     $data->leftjoin(DB::raw("(SELECT product_id,AVG(price) as avg_valuation
    //     FROM product_purchase 
    //    GROUP BY product_purchase.product_id
    //    ) as pp"),function($join){
    //        $join->on("pp.product_id","=","products.id");
    //    });

        if($request->brand_id != "") {
            $data->where('products.brand_id',  $request->brand_id);
        }

        if($request->category_id != "") {
            $data->where('category_id',  $request->category_id);
        }

        if($request->product_code != "") {
            $data->where('product_code',  'LIKE', '%'.$request->product_code.'%');
        }

        if($request->product_name != "") {
            $data->where('product_name',  'LIKE', '%'.$request->product_name.'%');
        }

        if($request->status != "") {
            $data->where('is_active', $request->status);
        }

        if($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if($request->sort_by != "") {
            if($request->sort_by == "name") {
                $data->orderBy('product_name', $order);
            }
            else if($request->sort_by == "code") {
                $data->orderBy('product_code', $order);
            }
            else if($request->sort_by == "brand") {
                $data->orderBy('brands.brand_name', $order);
            }
            else if($request->sort_by == "category") {
                $data->orderBy('categories.category_name', $order);
            }
            else if($request->sort_by == "uom") {
                $data->orderBy('uoms.uom_name', $order);
            }
            else {}
        } else {
            $data = $data->orderBy('products.id', 'DESC');
        }
        $data->with('selling_uoms');
        $data = $data->paginate($limit);
        return response(compact('data'), 200);
    }

    public function getProducts() {

        $where = "warehouse_id = 1";//for main warehouse
        $data = DB::table("products")

                ->select(DB::raw("products.product_name as name, products.id as product_id,products.minimum_qty, products.selling_price, CONCAT(products.product_name, ' - ', products.product_code, ' - ', categories.category_name) as product_name,products.product_price,products.uom_id,uoms.uom_name,(CASE WHEN pt.in_count IS NOT NULL THEN pt.in_count ELSE 0 END) as in_count,(CASE WHEN pt.out_count IS NOT NULL THEN pt.out_count ELSE 0 END) as out_count,(CASE WHEN pt.direct_sale_qty IS NOT NULL THEN pt.direct_sale_qty ELSE 0 END) as direct_sale_qty, (CASE WHEN pt.transfer_qty IS NOT NULL THEN pt.transfer_qty ELSE 0 END) as transfer_qty, (CASE WHEN pt.revise_sale_qty IS NOT NULL THEN pt.revise_sale_qty ELSE 0 END) as revise_sale_qty"))

                ->leftjoin(DB::raw("(SELECT product_id, warehouse_id, transition_date,

                            SUM(CASE  WHEN transition_type = 'in' THEN product_quantity  ELSE 0 END)  as in_count, SUM(CASE  WHEN transition_type = 'out' THEN product_quantity  ELSE 0 END)  as out_count, SUM(CASE  WHEN transition_type = 'out' AND transition_sale_id IS NOT NULL AND transition_approval_id IS NULL THEN product_quantity  ELSE 0 END)  as direct_sale_qty, SUM(CASE  WHEN transition_type = 'out' AND transition_transfer_id IS NOT NULL THEN product_quantity  ELSE 0 END)  as transfer_qty, SUM(CASE  WHEN transition_type = 'out' AND transition_approval_id IS NOT NULL AND transition_sale_id IS NOT NULL AND is_revise IS NOT NULL THEN product_quantity  ELSE 0 END)  as revise_sale_qty

                            FROM product_transitions Where ".$where."

                            GROUP BY product_transitions.product_id

                            ) as pt"),function($join){

                            $join->on("pt.product_id","=","products.id");

                        })

                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id')

                ->where('products.is_active', 1)

                ->orderBy("products.product_name")

                ->get();

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
        $product = Product::with('photos','selling_uoms','uom')->find($id);
        $pp=DB::table('product_purchase')->where('product_id',$id)->get();
        $cost_price=$this->getCostPrice($id)->product_cost_price;
        //ep
        if($cost_price == 0) {
            $cost_price = $product->purchase_price;
        } 
        //end ep
        return compact('product','cost_price');
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
	            'product_code' => 'max:255|unique:products',
	        ]);
            $latest = Product::orderBy('id','desc')->first();
            $date= str_replace("-", "", Carbon::today()->format('d-m-Y'));
            $product_code=$this->getCode($request);
//            $latest = Product::orderBy('id','desc')->first();
////            dd($latest);
//            $random_code = "P".str_pad((int)$latest->id + 1,8,"0",STR_PAD_LEFT);
//            $product_code=$request->product_code_type=='manual' ? $request->product_code: $random_code ;
            $product = new Product;
	        $product->product_name      = $request->product_name;
            $product->product_code      = $product_code;
            $product->product_code_type = $request->product_code_type;
	        $product->brand_id          = $request->brand_id;
	        $product->category_id       = $request->category_id;
	        $product->uom_id            = $request->uom_id;
            $product->selling_price    = $request->selling_price;
            $product->purchase_price    = $request->purchase_price;
            $product->minimum_qty       = $request->min_qty;
            $product->reorder_level       = $request->reorder_level;
            $product->is_active = 1;
	        $product->created_by        = Auth::user()->id;
	        $product->updated_by        = Auth::user()->id;
	        $product->save();
            $product_id = $product->id;
//	        $relation_arr = $request->uom_relations; //array value(key is uom_id, value is related uom value)
//            $price_arr = $request->uom_prices; //array value(key is uom_id, value is related uom selling price)
//            $per_price_arr = $request->uom_per_prices; //array value(key is uom_id, value is related warehouse uom per price)
//
//            $retail1_price_arr = $request->uom_retail1_prices;
//            $retail2_price_arr = $request->uom_retail2_prices;
//            $wholesale_price_arr = $request->uom_wholesale_prices;
//            $purchase_price_arr = $request->uom_purchase_prices;
//
//	        for($i=0; $i<count($request->selected_selling_uom); $i++) {
//	           	$key = $request->selected_selling_uom[$i];
//	           	$relation = $relation_arr[$key];
//                $selling_price = $price_arr[$key];
//                $uom_per_price = $per_price_arr[$key];
//	            $product->selling_uoms()->attach($request->selected_selling_uom[$i],['relation' => $relation, 'retail1_price' => $retail1_price_arr[$key],  'retail2_price' => $retail2_price_arr[$key], 'wholesale_price' => $wholesale_price_arr[$key],'warehouse_uom_purchase_price' => $purchase_price_arr[$key]]);
//	        }
	        $status = "success";
	        return compact('status','product_id');
    	}
    	catch (ValidationException $exception) {
    		return response()->json([
    			'status' => 'error',
    			'message' => 'Error',
    			'errors' => json_encode($exception->errors()),
    		], 422);
    	}
    }
    public function getCode($request){
        $code=$request->product_code;
        $date= str_replace("-", "", Carbon::today()->format('d-m-Y'));
        if($code==null){
//            automatic
            $latest=Product::orderBy('id','desc')->where('product_code_type','automatic')->first();
            if($latest!=null){
                $last_invoice=$latest->product_code;
                $num=substr($last_invoice,-5);
//                    dd($num);
            }else{
                $num=0;
            }
            $product_code = "P".str_pad((int)$num+1,5,"0",STR_PAD_LEFT);
        }else{
            $product_code=$request->product_code;
        }
        return $product_code;
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
//        dd(count($request->selected_selling_uom));
//        dd($request->all());
        try {
            $this->validate($request, [
                'product_code' => 'max:255|unique:products,product_code,'.$id,
            ]);
//            $latest = Product::orderBy('id','desc')->first();
//            $random_code = "P".str_pad((int)$latest->id + 1,8,"0",STR_PAD_LEFT);
//            $product_code=$request->product_code_type=='manual' ? $request->product_code: $random_code;
            $product = Product::find($id);
            $product->product_name      = $request->product_name;
            $product->product_code_type      = $request->product_code_type;
            $product->product_code      = $request->product_code;
            $product->brand_id          = $request->brand_id;
            $product->uom_id            = $request->uom_id;
            $product->category_id       = $request->category_id;
            $product->minimum_qty       = $request->min_qty;
            $product->selling_price    = $request->selling_price;
            $product->purchase_price    = $request->purchase_price;
            $product->reorder_level       = $request->reorder_level;
            $product->updated_at = time();
            $product->updated_by = Auth::user()->id;
            $product->save();
//            $product->selling_uoms()->detach();
//            //add updated relations
//            $relation_arr = $request->uom_relations; //array value(key is uom_id, value is related uom value)
//            $price_arr = $request->uom_prices; //array value(key is uom_id, value is related uom selling price)
//            $per_price_arr = $request->uom_per_prices; //array value(key is uom_id, value is related uom per warehouse uom price)
//
//            $retail1_price_arr = $request->uom_retail1_prices;
//            $retail2_price_arr = $request->uom_retail2_prices;
//            $wholesale_price_arr = $request->uom_wholesale_prices;
//            $purchase_price_arr = $request->uom_purchase_prices;
////            for($i=0; $i<count($request->selected_selling_uom); $i++) {
//            foreach($request->selected_selling_uom as $key=>$s){
////                $key = $request->selected_selling_uom[$i];
//                $relation = $relation_arr[$s];
////                $selling_price = $price_arr[$key];
////                $uom_per_price = $per_price_arr[$key];
//                $product->selling_uoms()->attach($s,['relation' => $relation, 'retail1_price' => $retail1_price_arr[$s],  'retail2_price' => $retail2_price_arr[$s], 'wholesale_price' => $wholesale_price_arr[$s],'warehouse_uom_purchase_price' => $purchase_price_arr[$s]]);
//            }
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

    public function allProducts()
    {
        $data = Product::with('uom')->where('is_active',1)->get();
        return response(compact('data'), 200);
    }

    public function getSellingUomByProductId($product_id)
    {
        $s_uoms = Product::with('selling_uoms')->find($product_id);
        return response(compact('s_uoms'), 200);
    }

    public function updateStatus($id, $status)
    {
        $data = Product::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response(['message' => 'delete successful']);
    }

    public function import()
    {
        $path1 = request()->file('file')->store('temp');
        $path=storage_path('app').'/'.$path1;
        $import = new ProductImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }

    public function qtyImport()
    {
        $path1 = request()->file('file')->store('temp');
        $path=storage_path('app').'/'.$path1;

        $import = new ProductMinQtyImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }

    public function exportProduct(Request $request)
    {
        $export = new ProductExport($request);
        $fileName = 'product_export_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function exportProductPdf(Request $request)
    {
        $data = Product::select([
                  'products.*',
                  'uoms.uom_name',
                  'brands.brand_name',
                  'categories.category_name'
                ]);
        
        $data->leftjoin('uoms', 'uoms.id','products.uom_id');
        $data->leftjoin('brands', 'brands.id','products.brand_id');
        $data->leftjoin('categories', 'categories.id','products.category_id');

        if($request->brand_id != "") {
            $data->where('brand_id',  $request->brand_id);
        }

        if($request->category_id != "") {
            $data->where('category_id',  $request->category_id);
        }

        if($request->product_code != "") {
            $data->where('product_code',  'LIKE', '%'.$request->product_code.'%');
        }

        if($request->product_name != "") {
            $data->where('product_name',  'LIKE', '%'.$request->product_name.'%');
        }

        if($request->status != "") {
            $data->where('is_active', $request->status);
        }

        if($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if($request->sort_by != "") {
            if($request->sort_by == "name") {
                $data->orderBy('product_name', $order);
            }
            else if($request->sort_by == "code") {
                $data->orderBy('product_code', $order);
            }
            else if($request->sort_by == "brand") {
                $data->orderBy('brands.brand_name', $order);
            }
            else if($request->sort_by == "category") {
                $data->orderBy('categories.category_name', $order);
            }
            else if($request->sort_by == "uom") {
                $data->orderBy('uoms.uom_name', $order);
            }
            else {}
        } else {
            $data = $data->orderBy('products.id', 'DESC');  
        }
        $data->with('selling_uoms');
        $data = $data->get();
        $pdf = PDF::loadView('exports.product_pdf', compact('data'));
        $pdf->setPaper('a4' , 'portrait');
       // $output = $pdf->output();

      /*  return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
        ]);*/

        return $pdf->output();
    }
}
