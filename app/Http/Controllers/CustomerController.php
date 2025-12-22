<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Category;
use App\CustomerLog;
use App\Imports\CustomerImport;
use App\Exports\CustomerExport;
use App\Exports\CustomerWiseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Session;
use Storage;
use Image;
use File;
use PDF;

class CustomerController extends Controller
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
        $data = Customer::select([
                  'customers.*',
                  'customer_types.customer_type_name',
                  'countries.country_name',
                  'states.state_name',
                  'townships.township_name'
                ]);
        $data->leftjoin('customer_types', 'customer_types.id','customers.customer_type_id');
        $data->leftjoin('countries', 'countries.id','customers.country_id');
        $data->leftjoin('states', 'states.id','customers.state_id');
        $data->leftjoin('townships', 'townships.id','customers.township_id');
        if($request->cus_name != "") {
            $data->where('cus_name', 'LIKE', '%'.$request->cus_name.'%');
        }

        if($request->cus_code != "") {
            $data->where('cus_code', $request->cus_code);
        }

        if($request->cus_type != "") {
            $data->where('customer_type_id', $request->cus_type);
        }

        if($request->township_id != "") {
            $data->where('township_id', $request->township_id);
        }

        if($request->state_id != "") {
            $data->where('customers.state_id', $request->state_id);
        }

        if($request->country_id != "") {
            $data->where('country_id', $request->country_id);
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
                $data->orderBy('cus_name', $order);
            }
            else if($request->sort_by == "code") {
                $data->orderBy('cus_code', $order);
            }
            else if($request->sort_by == "phone") {
                $data->orderBy('cus_phone', $order);
            }
            else if($request->sort_by == "cus_type") {
                $data->orderBy('customer_types.customer_type_name', $order);
            }
            else if($request->sort_by == "address") {
                $data->orderBy('townships.township_name', $order);
                $data->orderBy('states.state_name', $order);
                $data->orderBy('countries.country_name', $order);
            }
            else {}
        } else {
            $data = $data->orderBy('customers.id', 'DESC');
        }
        $data = $data->paginate($limit);

        //$data = $data->orderBy('id', 'DESC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allCustomers()
    {
        $data = Customer::orderBy('cus_name', 'ASC')->where('is_active',1)->get();
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
        $customer = Customer::with('categories','products')->find($id);
        $categories = Category::with('brand')->orderBy('category_name', 'ASC')->get();

        $where = "warehouse_id = 1";//for main warehouse
        $products = DB::table("products")

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
        return compact('customer','categories','products');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $obj = new Customer;
        //auto generate customer code;
        $max_id = Customer::withTrashed()->max('id');
        if($max_id) {
            $max_id = $max_id + 1;
        } else {
            $max_id = 1;
        }
        $cus_code = "C".str_pad($max_id,5,"0",STR_PAD_LEFT);
        $obj->cus_code = $cus_code;
        $obj->cus_name = $request->cus_name;
        $obj->customer_type_id = $request->cus_type;
        $obj->country_id    = $request->country_id;
        $obj->state_id      = $request->state_id;
        $obj->township_id   = $request->township_id;
        $obj->cus_phone  = $request->cus_phone;
        $obj->cus_billing_address  = $request->billing_address;
        $obj->cus_shipping_address = $request->shipping_address;
        $obj->is_active = 1;
        $obj->created_by = Auth::user()->id;
        $obj->updated_by = Auth::user()->id;
        $is_lock = 0;
        $lock_remark = NULL;
        $lock_approve = NULL;
        if(!empty($request->is_lock)) {
            $is_lock = $request->is_lock == 'lock' ? 1 : 0;
            $obj->is_lock = $is_lock;
            $lock_remark = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $request->lock_remark)));
            $lock_approve = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $request->lock_approve)));
            $obj->lock_remark  = $lock_remark;
            $obj->lock_approve = $lock_approve;
        } else {
            $obj->is_lock = NULL;
            $obj->lock_remark = NULL;
            $obj->lock_approve = NULL;
        }

        $obj->save();

        for($i=0; $i<count($request->categories); $i++) {
            $obj->categories()->attach($request->categories[$i]);
        }

        for($i=0; $i<count($request->products); $i++) {
            $obj->products()->attach($request->products[$i]);
        }

        if(!empty($request->is_lock)) {
            $log = new CustomerLog;
            $log->customer_id = $obj->id;
            $log->is_lock = $is_lock;
            $log->remark = $lock_remark;
            $log->approved_by = $lock_approve;
            $log->added_by = Auth::user()->id;
            $log->added_time = Carbon::now();
            $log->save();
        }

        $cus_id = $obj->id;

        $status = "success";
        return compact('status','cus_id');
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

        $obj = Customer::find($id);

        $old_is_lock = $obj->is_lock;
        $old_lock_remark = $obj->lock_remark;
        $old_lock_approve = $obj->lock_approve;

        $obj->cus_name = $request->cus_name;
        $obj->customer_type_id = $request->cus_type;
        $obj->country_id    = $request->country_id;
        $obj->state_id      = $request->state_id;
        $obj->township_id   = $request->township_id;
        $obj->cus_phone  = $request->cus_phone;
        $obj->cus_billing_address  = $request->billing_address;
        $obj->cus_shipping_address = $request->shipping_address;
        $obj->updated_by = Auth::user()->id;
        $is_lock = 0;
        $lock_remark = NULL;
        $lock_approve = NULL;
        if(!empty($request->is_lock)) {
            $is_lock = $request->is_lock == 'lock' ? 1 : 0;
            $obj->is_lock = $is_lock;
            $lock_remark = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $request->lock_remark)));
            $lock_approve = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $request->lock_approve)));
            $obj->lock_remark  = $lock_remark;
            $obj->lock_approve = $lock_approve;
        } else {
            $obj->is_lock = NULL;
            $obj->lock_remark = NULL;
            $obj->lock_approve = NULL;
        }

        $obj->save(); 

        $obj->categories()->detach();

        for($i=0; $i<count($request->categories); $i++) {
            $obj->categories()->attach($request->categories[$i]);
        }

        $obj->products()->detach();

        for($i=0; $i<count($request->products); $i++) {
            $obj->products()->attach($request->products[$i]);
        }

        if(!empty($request->is_lock) && ($old_is_lock !==  $obj->is_lock || $old_lock_remark != $obj->lock_remark || $old_lock_approve != $obj->lock_approve)) {
            $log = new CustomerLog;
            $log->customer_id = $obj->id;
            $log->is_lock = $is_lock;
            $log->remark = $lock_remark;
            $log->approved_by = $lock_approve;
            $log->added_by = Auth::user()->id;
            $log->added_time = Carbon::now();
            $log->save();
        }
        $cus_id = $obj->id;
        $status = "success";
        return compact('status','cus_id');

    }

    public function import()
    {
        $path1 = request()->file('file')->store('temp');
        $path=storage_path('app').'/'.$path1;

        $import = new CustomerImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }

    public function updateStatus($id, $status)
    {
        $data = Customer::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
        return response(compact('data'), 200);
    }

    public function searchCustomers(Request $request)
    {
        if($request->term && $request->term != '') {
            $data = Customer::with('township')
                            ->where('is_active',1)
                            ->whereRaw('lower(cus_name) like lower(?)', ["%{$request->term}%"])
                            ->orderBy('cus_name', 'ASC')->get();
            return response()->json($data);
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
        $obj = Customer::find($id);
        $obj->delete();
        return response(['message' => 'delete successful']);
    }

    public function exportCustomer(Request $request)
    {
        $export = new CustomerExport($request);
        $fileName = 'customer_export_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function addPhoto(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);
        //ini_set('post_max_size', '64M');
        //ini_set('upload_max_filesize', '64M');
        $cus_id = $request->hid_cus_id;
        $obj = Customer::find($cus_id);
        $photos = '';
        $file = $request->file('file');
        //dd($file->getClientOriginalName());
       /** $filename = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $onlyName = explode('.'.$ext,$filename);
        $imagename = $onlyName[0]."_".date('YmdHis')."_".$obj->id.".".$ext;
        $destinationPath = public_path('member_families');
        //$filename = $file->getClientOriginalName();
        $file->move($destinationPath, $imagename);

        $family   = new MemberFamily;
        $family->member_id = $obj->id;
        $family->file   = $imagename;
        $family->created_by = Auth::user()->id;
        $family->save();**/

        $file_name = $file->getClientOriginalName();
        $ext = strtolower(substr(strrchr($file_name, "."), 1));
        $onlyName = explode('.'.$ext,$file_name); 

        $newname = $onlyName[0]."_".date('YmdHis')."_".$cus_id.".".$ext;

        $img = Image::make($file->getRealPath());

        $destinationPath = storage_path('app/public/image/customer');
       // $destinationPath = '/home/god/img/';
        // save file as jpg with medium quality
        $img->save($destinationPath.'/'.$newname, 60);

        //Storage::disk('image')->put('/delivery/'.$newname, file_get_contents($file));

        if(empty($obj->photo)){
            $photos = $newname;
        } else {
            $photos = $obj->photo.','.$newname;
        }

        $obj->photo = $photos;
        $obj->save();
        $status = "success";
        return compact('status');
    }

    public function deletePhoto($name,$id)
    {
        $obj = Customer::find($id);
        $photos = explode(',',$obj->photo);
        if (($key = array_search($name, $photos)) !== false) {
            unset($photos[$key]);

            $exist = Storage::disk('image')->exists('/customer/'.$name);
            if ($exist) {
                try {
                    File::delete('storage/image/customer/' . $name);
                } catch (Exception $e) {
                    return  response($e->getMessage());
                }
            }
        }
        if(count($photos) == 0) {
            $obj->photo = NULL;
        } else {
            $obj->photo = implode(',', $photos);
        }
        
        $obj->save();
        $status = "success";
        return compact('status');
    }

    public function getCustomerWiseList(Request $request) {
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 240);
         $data = DB::table("states")
                    ->select(DB::raw("cus_category.*, townships.id as tsp_id, townships.township_name, states.state_name,cus_product.product_name,cus_product.product_id"))

                    ->leftjoin('townships', 'townships.state_id', '=', 'states.id') 

                    ->leftjoin(DB::raw("(SELECT customers.*, GROUP_CONCAT(cc.category_name) as category_name, GROUP_CONCAT(cc.category_id) as category_id, GROUP_CONCAT(cc.cat_product_id SEPARATOR '_') as cat_product_id FROM customers LEFT JOIN (SELECT cate.category_name, cate.cat_product_id, category_customer.customer_id, category_customer.category_id FROM category_customer LEFT JOIN (SELECT categories.*, GROUP_CONCAT(products.id) as cat_product_id FROM categories LEFT JOIN products ON products.category_id = categories.id GROUP BY products.category_id) as cate ON category_customer.category_id = cate.id) as cc ON cc.customer_id = customers.id GROUP BY customers.id) as cus_category"),function($join){
                            $join->on("cus_category.township_id","=","townships.id");
                        })
                    //->leftjoin('states', 'states.id', '=', 'cus_category.state_id')
                    ->leftjoin(DB::raw("(SELECT product_customer.customer_id, GROUP_CONCAT(products.product_name) as product_name, GROUP_CONCAT(products.id) as product_id FROM product_customer LEFT JOIN products ON product_customer.product_id = products.id GROUP BY product_customer.customer_id) as cus_product"),function($join){
                            $join->on("cus_product.customer_id","=","cus_category.id");
                        });

        if($request->customer_id != "") {
            $data->where('cus_category.id',$request->customer_id);
        }

        if($request->cus_code != "") {
            $data->where('cus_category.cus_code','LIKE','%'.$request->cus_code.'%');
        }

        /**if($request->category_id != "") {
            $cat_id = (int)$request->category_id;
            $data->where(function($query) use ($cat_id) {
                        $query->where('cus_category.category_id','LIKE',$cat_id.',')
                              ->orWhere('cus_category.category_id','LIKE','%'.$cat_id.'%')
                              ->orWhere('cus_category.category_id','LIKE',','.$cat_id);
                    });
        }**/
        if($request->township_id != "") {
            $data->where('townships.id',$request->township_id);
        }

        if($request->state_id != "") {
            $data->where('states.id',$request->state_id);
        }

        if(!empty($request->categories)){
            $c_arr = explode(',',$request->categories);

             if(!empty($request->products)){
                $p_arr = explode(',',$request->products);
                foreach($p_arr as $pk=>$pv) {
                    $p_id = (int)$pv;
                    $pc_id_data =  DB::table("products")
                        ->where('id',$p_id)                        
                        ->first();
                    $c_id = $pc_id_data->category_id;
                    array_push($c_arr, $c_id);
                }
            }

            $data->where(function($query) use ($c_arr) {
                    foreach($c_arr as $ck=>$cv) {
                        $cat_id = (int)$cv;
                        $query->where('cus_category.category_id','LIKE',$cat_id.',')
                              ->orWhere('cus_category.category_id','LIKE','%'.$cat_id.'%')
                              ->orWhere('cus_category.category_id','LIKE',','.$cat_id);

                        
                    }

                });
        }

        if(!empty($request->products)){
            $p_arr = explode(',',$request->products);            

            $data->where(function($query) use ($p_arr) {
                    foreach($p_arr as $pk=>$pv) {
                        $p_id = (int)$pv;
                        $pc_id_data =  DB::table("products")
                            ->where('id',$p_id)                        
                            ->first();
                        $c_id = $pc_id_data->category_id;
                        $query->where('cus_product.product_id','LIKE',$p_id.',')
                              ->orWhere('cus_product.product_id','LIKE','%'.$p_id.'%')
                              ->orWhere('cus_product.product_id','LIKE',','.$p_id)
                              ->orwhere('cus_category.category_id','LIKE',$c_id.',')
                              ->orWhere('cus_category.category_id','LIKE','%'.$c_id.'%')
                              ->orWhere('cus_category.category_id','LIKE',','.$c_id);
                    }
                });

            if(!empty($request->categories)){
                $c_arr = explode(',',$request->categories);
                foreach($c_arr as $ck=>$cv) {
                    $cat_id = (int)$cv;
                    $data->orWhere('cus_category.category_id','LIKE',$cat_id.',')
                          ->orWhere('cus_category.category_id','LIKE','%'.$cat_id.'%')
                          ->orWhere('cus_category.category_id','LIKE',','.$cat_id);
                }
            }
        }

        $data = $data->whereNotNull('states.state_name');
        //$data = $data->whereNotNull('townships.township_name');
        $data = $data->orderBy('states.state_name')->orderBy('townships.township_name')->get();
        //dd($data);
        return $data;
    }

    public function getCustomerWiseReport(Request $request)
    {
        $data = $this->getCustomerWiseList($request);
        return response(compact('data'), 200);
    }

    public function exportCustomerWiseReport(Request $request)
    {
        $data = $this->getCustomerWiseList($request);;
        $export = new CustomerWiseExport($data,$request);
        $fileName = 'customer_wise_contact_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function exportCustomerWiseReportPdf(Request $request)
    {
        $data = $this->getCustomerWiseList($request);
        $pdf = PDF::loadView('exports.customer_wise_pdf', compact('data','request'));
        $pdf->setPaper('a4' , 'portrait');
       // $output = $pdf->output();

      /*  return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
        ]);*/

        return $pdf->output();
    }
}
