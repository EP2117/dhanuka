<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;
use App\Country;
use App\Imports\StateImport;
use Maatwebsite\Excel\Facades\Excel;

class StateController extends Controller
{
    public function index()
    {
        $data = State::orderBy('state_name', 'ASC')->get();
        return response(compact('data'), 200);
    }

	public function stateByCountry($id)
    {
        $data = State::orderBy('state_name', 'DESC')
                ->where('country_id',$id)
                ->get();
        return response(compact('data'), 200);
    }

    public function import() 
    {
        $path1 = request()->file('file')->store('temp'); 
        $path=storage_path('app').'/'.$path1; 

        $import = new StateImport();
        Excel::import($import,$path);
        //return duplicate chessi no count
        return ["message" => "success"];
    }
}
