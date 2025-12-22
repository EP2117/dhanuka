<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $data = Country::orderBy('country_name', 'ASC')->get();
        return response(compact('data'), 200);
    }
}
