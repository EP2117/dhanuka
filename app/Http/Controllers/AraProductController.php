<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AraProduct;
use App\Imports\AraProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use DB;

class AraProductController extends Controller
{
    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new AraProductImport();
        Excel::import($import,$path);
        return ["message" => "success"];
    }

    public function allBrands()
    {
        $data = DB::table('ara_products')->distinct()->get(['ara_product_brand']);
        return response(compact('data'), 200);
    }

    public function allCategories()
    {
        $data = DB::table('ara_products')->distinct()->get(['ara_product_category']);
        return response(compact('data'), 200);
    }
}
