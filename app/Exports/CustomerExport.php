<?php

namespace App\Exports;

use App\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class CustomerExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
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

        $data = $data->get();


        return view('exports.customerExport', [
            'data' => $data,
            'request' => $request,
        ]);
    }

    public function title(): string
    {
        return 'Master Customer Export';
    }
}
