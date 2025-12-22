<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerType;
use App\Imports\CustomerTypeImport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerTypeController extends Controller
{
	public function index(Request $request)
    {
        $data = CustomerType::orderBy('id', 'DESC')->get();
        return response(compact('data'), 200);
    }

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new CustomerTypeImport();
        Excel::import($import,$path);
        return ["message" => "success"];
    }
}
