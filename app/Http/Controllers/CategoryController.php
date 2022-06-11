<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Category;
use DB;

class CategoryController extends Controller
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
        $data = Category::with('brand');

        if($request->category_name != "") {
            $data->where('category_name', 'LIKE', '%'.$request->category_name.'%');
        }

        if($request->status != "") {
            $data->where('is_active', $request->status);
        }

        $data = $data->orderBy('category_name', 'ASC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allCategories()
    {
        $data = Category::with('brand')->where('is_active',1)->orderBy('category_name', 'ASC')->get();
        return response(compact('data'), 200);
    }

    public function getCategoriesByBrand($brand_id)
    {
        $data = Category::with('brand')->where('brand_id',$brand_id)->where('is_active',1)->orderBy('category_name', 'ASC')->get();
        return response(compact('data'), 200);
    }

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new CategoryImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::with('brand')->find($id);
        return compact('category');
    }

    public function updateStatus($id, $status)
    {
        $data = Category::find($id);
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
                'category_name' => 'required|max:255',
            ]);

            $chk_sql = DB::table('categories')
                                ->where('brand_id',$request->brand_id)
                                ->where('category_name',$request->category_name)
                                ->get();
            if(count($chk_sql) > 0) {
                $status = "exist";
            } else {

                $category = new Category;
                $category->category_name = $request->category_name;
                $category->brand_id = $request->brand_id;
                $category->created_by = Auth::user()->id;
                $category->save();

                $status = "success";
            }
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
                'category_name' => 'required|max:255',
            ]);

            $chk_sql = DB::table('categories')
                                ->where('brand_id',$request->brand_id)
                                ->where('category_name',$request->category_name)
                                ->where('id','!=', $id)
                                ->get();
            if(count($chk_sql) > 0) {
                $status = "exist";
            } else {

                $category = Category::find($id);
                $category->brand_id = $request->brand_id;
                $category->category_name = $request->category_name;
                $category->updated_at = time();
                $category->updated_by = Auth::user()->id;
                $category->save();
                $status = "success";
            }
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
}
