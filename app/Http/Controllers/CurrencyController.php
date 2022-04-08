<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Currency;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $limit = 30;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 
        $data = Currency::orderBy('name', 'ASC')->paginate($limit);
        return response(compact('data'), 200);
    }

    public function allCurrency()
    {
        $data = Currency::where('is_active',1)->orderBy('name', 'ASC')->get();
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
        $currency = Currency::find($id);
        return compact('currency');
    }

    public function updateStatus($id, $status)
    {
        $data = Currency::find($id);
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

            $obj = new Currency;
            $obj->name = $request->name;
            $obj->sign = $request->sign;
            $obj->created_by = Auth::user()->id;
            $obj->save();

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
}
