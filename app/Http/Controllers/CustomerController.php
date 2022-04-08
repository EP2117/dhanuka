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
        $customer = Customer::with('categories')->find($id);
        $categories = Category::with('brand')->orderBy('category_name', 'ASC')->get();
        return compact('customer','categories');
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

         $data = DB::table("townships")
                    ->select(DB::raw("cus_category.*, townships.township_name"))
                    ->leftjoin(DB::raw("(SELECT customers.*, GROUP_CONCAT(cc.category_name) as category_name, GROUP_CONCAT(cc.category_id) as category_id FROM customers LEFT JOIN (SELECT categories.category_name, category_customer.customer_id, category_customer.category_id FROM category_customer LEFT JOIN categories ON category_customer.category_id = categories.id) as cc ON cc.customer_id = customers.id GROUP BY customers.id) as cus_category"),function($join){
                            $join->on("cus_category.township_id","=","townships.id");
                        });

        if($request->customer_id != "") {
            $data->where('cus_category.id',$request->customer_id);
        }

        if($request->cus_code != "") {
            $data->where('cus_category.cus_code','LIKE','%'.$request->cus_code.'%');
        }

        if($request->category_id != "") {
            $cat_id = (int)$request->category_id;
            $data->where(function($query) use ($cat_id) {
                        $query->where('cus_category.category_id','LIKE',$cat_id.',')
                              ->orWhere('cus_category.category_id','LIKE','%'.$cat_id.'%')
                              ->orWhere('cus_category.category_id','LIKE',','.$cat_id);
                    });
        }

        $data = $data->orderBy('townships.township_name')->get();
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
}
