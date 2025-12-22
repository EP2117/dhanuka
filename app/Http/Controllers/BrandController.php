<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Imports\BrandImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Brand;

class BrandController extends Controller
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
        $data = Brand::select(['id', 'brand_name','is_active']);

        if($request->brand_name != "") {
            $data->where('brand_name', 'LIKE', '%'.$request->brand_name.'%');
        }

        if($request->status != "") {
            $data->where('is_active', $request->status);
        }

        $data = $data->orderBy('brand_name', 'ASC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allBrands()
    {
        $data = Brand::select(['id', 'brand_name'])->where('is_active',1)->orderBy('id', 'DESC')->get();
        return response(compact('data'), 200);
    }

    public function filterBrands()
    {
        $access_brands = array();
        
        $data = Brand::select(['id', 'brand_name']);

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            foreach(Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }

            $data->whereIn('id',$access_brands);
        }
        $data->where('is_active',1);
                    
        $data = $data->orderBy('id', 'DESC')->get();
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
        $brand = Brand::find($id);
        return compact('brand');
    }

    public function updateStatus($id, $status)
    {
        $data = Brand::find($id);
        $active = $status == "active" ? '1' : '0';
        $data->is_active = $active;
        $data->save();
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
        try {
            $this->validate($request, [
                'brand_name' => 'required|max:255|unique:brands',
            ]);

            $brand = new Brand;
            $brand->brand_name = $request->brand_name;
            $brand->created_by = Auth::user()->id;
            $brand->save();

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'brand_name' => 'required|max:255|unique:brands,brand_name,'.$id,
            ]);

            $brand = Brand::find($id);
            $brand->brand_name      = $request->brand_name;
            $brand->updated_at = time();
            $brand->updated_by = Auth::user()->id;
            $brand->save();
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

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new BrandImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }
}
