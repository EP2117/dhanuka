<?php

namespace App\Http\Controllers;

// use DB;
use PDF;
use Session;
use App\Sale;
use App\User;
use App\Order;
use App\Product;
use App\Customer;
use App\SaleAdvance;
use Carbon\Carbon;
use App\AccountTransition;
use App\ProductTransition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\DailySaleExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\Report\GetReport;
use App\Exports\DailySaleProductExport;
use App\Exports\SaleAnalystExport;
use App\Http\Traits\AccountReport\Ledger;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class SaleController extends Controller
{
    use GetReport;
    use Ledger;
    public function index(Request $request)
    {
        $login_year = Session::get('loginYear');

        if (Auth::user()->role->role_name == "admin" || Auth::user()->role->role_name == "system") {
            $data = Sale::with('products', 'products.uom', 'warehouse', 'customer', 'products.selling_uoms', 'branch');
            $data->whereBetween('invoice_date', array($login_year . '-01-01', $login_year . '-12-31'));
            $data = $data->orderBy('id', 'DESC')->get();
        } else {
            if (Auth::user()->role->role_name == "office_order_user") {
                //get order user's order id
                $orders = DB::table('orders')
                    ->where('created_by', Auth::user()->id)
                    ->pluck('id')->toArray();
                //get specific order invoics
                $data = Sale::with('products', 'products.uom', 'warehouse', 'customer', 'products.selling_uoms', 'branch')
                    ->where('warehouse_id', Auth::user()->warehouse_id);
                $data->whereBetween('invoice_date', array($login_year . '-01-01', $login_year . '-12-31'));
                $data->whereIn('order_id', $orders);
                $data = $data->orderBy('id', 'DESC')->get();
            } else {
                $data = Sale::with('products', 'products.uom', 'warehouse', 'customer', 'products.selling_uoms', 'branch')
                    ->where('warehouse_id', Auth::user()->warehouse_id);
                $data->whereBetween('invoice_date', array($login_year . '-01-01', $login_year . '-12-31'));
                $data = $data->orderBy('id', 'DESC')->get();
            }
        }

        return response(compact('data'), 200);
    }

    //get max id from sale table
    public function getMaxId()
    {
        $max_id = Sale::max('id');
        return compact('max_id');
    }

    //get sales by sale type (van or office sale)
    public function getBySaleType(Request $request)
    {
        $login_year = Session::get('loginYear');
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }

        $now = Carbon::now()->format('Y-m-d');

        $yesterday = Carbon::yesterday()->format('Y-m-d');

        if (Auth::user()->role->role_name == "office_order_user") {
            //get order user's order id
            $orders = DB::table('orders')
                ->where('created_by', Auth::user()->id)
                ->pluck('id')->toArray();
            //get specific order invoics
            $data = Sale::with('currency','order','sale_man','products','collections','products.uom', 'warehouse','customer','products.selling_uoms','deliveries','branch')
                        ->where('warehouse_id',Auth::user()->warehouse_id)
                        ->where('sale_type', $request->sale_type)->where('is_opening',0);
            //$data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
            $data->whereBetween('invoice_date', array($yesterday, $now));
            $data->whereIn('order_id', $orders);

            if ($request->invoice_no != "") {
                $data->where('invoice_no', $request->invoice_no);
            }

            if ($request->invoice_type != "") {
                if ($request->invoice_type == "direct") {
                    $data->whereNull('order_id');
                } else {
                    $data->whereNotNull('order_id');
                }
            }

            if ($request->delivery_approve != "") {
                if ($request->delivery_approve == 'true') {
                    $data->where('delivery_approve', 1);
                } else {
                    $data->where('delivery_approve', 0);
                }
            }

            if ($request->from_date != '' && $request->to_date != '') {
                $data->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            } else if ($request->from_date != '') {
                $data->whereDate('invoice_date', '>=', $request->from_date);
            } else if ($request->to_date != '') {
                $data->whereDate('invoice_date', '<=', $request->to_date);
            } else {
            }

            if ($request->customer_id != "") {
                $data->where('customer_id', $request->customer_id);
            }

            if (isset($request->branch_id) && $request->branch_id != "") {
                $data->where('branch_id', $request->branch_id);
            }

            if ($request->sale_man_id != "") {
                $data->where('office_sale_man_id', $request->sale_man_id);
            }
            //for Country Head and Admin roles (can access multiple branch)
            if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
                $branches = Auth::user()->branches;
                $branch_arr = array();
                foreach ($branches as $branch) {
                    array_push($branch_arr, $branch->id);
                }
                $data->whereIn('branch_id', $branch_arr);
            } else {
                //other roles can access only one branch
                if (Auth::user()->role->id != 1) { //system can access all branches
                    $branch = Auth::user()->branch_id;
                    $data->where('branch_id', $branch);
                }
            }

            $data = $data->orderBy('id', 'DESC')->paginate($limit);
        } else {
            $data = Sale::with('currency','order','sale_man','products','collections','products.uom', 'warehouse','customer','products.selling_uoms','deliveries','branch')
                    ->where('sale_type', $request->sale_type)->where('is_opening',0);

            if ($request->invoice_no == "" && $request->invoice_type == "" && $request->delivery_approve == "" && $request->from_date == "" && $request->to_date == "" && $request->customer_id  == "" && $request->warehouse_id  == "" && $request->office_sale_man_id == "" && $request->ref_no == ""  && $request->sale_man_id == "") {
                $data->whereBetween('invoice_date', array($yesterday, $now));
            }
            if ($request->sale_man_id != "") {
                $data->where('office_sale_man_id', $request->sale_man_id);
            }
            if ($request->office_sale_man_id != "") {
                $data->where('created_by', $request->office_sale_man_id);
            }
            if ($request->invoice_no != "") {
                $data->where('invoice_no', $request->invoice_no);
            }
            if ($request->state_id != "") {
                $data->whereHas('customer', function ($q) use ($request) {
                    $q->where('state_id', $request->state_id);
                });
            }
            if ($request->from_date != '' && $request->to_date != '') {
                $data->whereBetween('invoice_date', array($request->from_date, $request->to_date));
            } else if ($request->from_date != '') {
                $data->whereDate('invoice_date', '>=', $request->from_date);
            } else if ($request->to_date != '') {
                $data->whereDate('invoice_date', '<=', $request->to_date);
            } else {
                //$data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));
                //$data->whereBetween('invoice_date', array($yesterday, $now));
            }

            if ($request->customer_id != "") {
                $data->where('customer_id', $request->customer_id);
            }

            if (isset($request->branch_id) && $request->branch_id != "") {
                $data->where('branch_id', $request->branch_id);
            }

            if ($request->invoice_type != "") {
                if ($request->invoice_type == "direct") {
                    $data->whereNull('order_id');
                } else {
                    $data->whereNotNull('order_id');
                }
            }

            if ($request->delivery_approve != "") {
                if ($request->delivery_approve == 'true') {
                    $data->where('delivery_approve', 1);
                } else {
                    $data->where('delivery_approve', 0);
                }
            }

            if (Auth::user()->role->role_name == "Country Head") {
                //for Country Head User
                $access_users = array();
                $office_sale_man_arr = array();
                foreach (Auth::user()->country_head_children as $ls) {
                    array_push($access_users, $ls->id);
                    $ls_query = User::with('local_supervisor_children')->find($ls->id);
                    foreach ($ls_query->local_supervisor_children as $sm) {
                        array_push($access_users, $sm->id);
                    }
                }

                foreach (Auth::user()->office_sale_mans as $osm) {
                    array_push($office_sale_man_arr, $osm->id);
                }

                //get order user's order id
                $orders = DB::table('orders')
                    ->whereIn('created_by', $access_users)
                    ->pluck('id')->toArray();

                //$data->whereIn('order_id',$orders);
                $data->where(function ($query) use ($orders, $office_sale_man_arr) {
                    $query->whereIn('order_id', $orders)
                        ->orWhereIn('office_sale_man_id', $office_sale_man_arr);
                });
            }

            if (Auth::user()->role->role_name == "Local Supervisor") {
                //for Local Supervisor user
                $ls_access_users = array();
                array_push($ls_access_users, Auth::user()->id);
                foreach (Auth::user()->local_supervisor_children as $sm) {
                    array_push($ls_access_users, $sm->id);
                }

                //get order user's order id
                $orders = DB::table('orders')
                    ->whereIn('created_by', $ls_access_users)
                    ->pluck('id')->toArray();

                $data->whereIn('order_id', $orders);
            }

            if (Auth::user()->role->role_name == "delivery") {
                $data->where('delivery_approve', 1);
            }


            //$data->whereBetween('invoice_date', array($login_year.'-01-01', $login_year.'-12-31'));

            //for Country Head and Admin roles (can access multiple branch)
            if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
                if (Auth::user()->role->id != 1) { //system can access all branches
                    $branches = Auth::user()->branches;
                    $branch_arr = array();
                    foreach ($branches as $branch) {
                        array_push($branch_arr, $branch->id);
                    }
                    $data->whereIn('branch_id', $branch_arr);
                }
            } else {
                //other roles can access only one branch
                if (Auth::user()->role->id != 1) { //system can access all branches
                    $branch = Auth::user()->branch_id;
                    $data->where('branch_id', $branch);
                }
            }
            $data = $data->orderBy('id', 'DESC')->paginate($limit);
        }

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
        /**if(!empty($request->reference_no) && $request->duplicate_ref_no == false) {
            $validatedData = $request->validate([
                'reference_no' => 'max:255|unique:sales',
            ]);
        }**/
        DB::beginTransaction();
        try {
            $sale = new Sale;
            //auto generate invoice no;
            $max_id = Sale::where('sale_type', $request->sale_type)->max('id');
            if ($max_id) {
                $max_id = $max_id + 1;
            } else {
                $max_id = 1;
            }
            $invoice_no = "SI" . str_pad($max_id, 5, "0", STR_PAD_LEFT);
            $sale->invoice_no = $invoice_no;
            $sale->invoice_type = $request->invoice_type;
            $sale->branch_id = Auth::user()->branch_id;
            $sale->reference_no = $request->reference_no;
            $sale->invoice_date = $request->invoice_date;
            $sale->warehouse_id = Auth::user()->warehouse_id;
            $sale->customer_id = $request->customer_id;
            //$sale->delivery_approve = 0;
            $sale->office_sale_man_id = $request->office_sale_man_id;
            if ($request->sale_order == true) {
                $sale->order_id = $request->order_id;
            }
            $sale->pay_amount = $request->pay_amount;
            $sale->sale_type  = $request->sale_type;
            $sale->total_amount = $request->sub_total;
            $sale->cash_discount = $request->cash_discount;
            $sale->net_total = $request->net_total;
            $sale->tax = $request->tax;
            $sale->tax_amount = $request->tax_amount;
            $sale->balance_amount = $request->balance_amount;
            // if($request->cash_discount!=null || $request->cash_discount!= 0){
            //     $discount_allowed_sub_account_id=config('global.discount_allowed');     /*sub account_id for Discount allowed*/
            // }else{
            //     $discount_allowed_sub_account_id=null;     /*sub account_id for Discount allowed*/
            // }
            if ($request->payment_type == 'credit') {
                $sub_account_id = config('global.sale_advance');                 /*sub account_id for sale advance*/
                // $discount_allowed_sub_account_id=config('global.discount_allowed');     /*sub account_id for Discount allowed*/
                if ($request->pay_amount != 0) {
                    $amount = $request->pay_amount;
                }
                $sale->payment_type = 'credit';
                $sale->due_date = $request->due_date;
                $sale->credit_day = $request->credit_day;
            } else {
                $sub_account_id = config('global.cash_sale');     /*sub account_id for sale Account*/
                $sale_common_account_id = config('global.cash_sale');     /*sub account_id for cash sale */
                $amount = $request->pay_amount;
                $sale->payment_type = 'cash';
            }

            $sale->currency_id = $request->currency_id;
            if ($request->currency_id != 1) {
                $sale->currency_rate = $request->currency_rate;
                $sale->pay_amount_fx = $request->pay_amount_fx == '' ? 0 : $request->pay_amount_fx;
                $sale->total_amount_fx = $request->sub_total_fx == '' ? 0 : $request->sub_total_fx;
                $sale->cash_discount_fx = $request->cash_discount_fx == '' ? 0 : $request->cash_discount_fx;
                $sale->net_total_fx = $request->net_total_fx == '' ? 0 : $request->net_total_fx;
                $sale->tax_fx = $request->tax_fx == '' ? 0 : $request->tax_fx;
                $sale->tax_amount_fx = $request->tax_amount_fx == '' ? 0 : $request->tax_amount_fx;
                $sale->balance_amount_fx = $request->balance_amount_fx  == '' ? 0 : $request->balance_amount_fx;;
            } else {
                $sale->currency_rate = 0;
                $sale->pay_amount_fx = 0;
                $sale->total_amount_fx = 0;
                $sale->cash_discount_fx = 0;
                $sale->net_total_fx = 0;
                $sale->tax_fx = 0;
                $sale->tax_amount_fx = 0;
                $sale->balance_amount_fx = 0;
            }
            $sale->created_by = Auth::user()->id;
            $sale->updated_by = Auth::user()->id;
            $sale->save();

            $description = $sale->invoice_no . ", Date " . $sale->invoice_date . " by " . $sale->customer->cus_name;
            /* Cash Book  for sale*/
            if ($sale) {
                //get customer's sale advance
                $customer_advance = 0;
                $customer_advance_fx = 0;
                if($request->currency_id == 1) {
                    $advance = DB::table("sale_advances")
                                ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                                ->where('customer_id','=',$request->customer_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $customer_advance = $in - $out;
                    }

                    if($customer_advance == 0 || ($customer_advance > 0 && $request->pay_amount == 0)) {
                        // cashbook
                        if($request->customer_advance == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'transition_date' => $sale->invoice_date,
                                    'customer_id' => $sale->customer_id,
                                    'sale_id' => $sale->id,
                                    'status'=>'sale',
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'debit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                           $this->storeSaleInLedger($sale);
                            // end ledger
                        } else {
                            AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side 
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                        }
                    }
                    else if(($customer_advance > $request->pay_amount) || $customer_advance == $request->pay_amount) {
                        AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side 
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            $obj = new SaleAdvance;
                            $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->customer_id = $request->customer_id;
                            $obj->sale_id = $sale->id;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($customer_advance < $request->pay_amount) {
                        $paid_amt = $request->pay_amount - $customer_advance;
                        // add in cashbook
                            
                            AccountTransition::create([
                                'sub_account_id' => $sub_account_id,
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'status'=>'sale',
                                'vochur_no'=>$invoice_no,
                                'description'=>$description,
                                'is_cashbook' => 1,
                                'debit' => $paid_amt,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);
                            //add  in ledger

                            AccountTransition::create([
                                'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                // 'debit' => $sale->net_total,
                                // change for account side 
                                //'credit' => $paid_amt,
                                'debit' => $paid_amt,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            AccountTransition::create([
                                //'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                        $obj = new SaleAdvance;
                        $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->customer_id = $request->customer_id;
                        $obj->sale_id = $sale->id;
                        $obj->amount = $customer_advance;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
              }//End mmk
              else {
                //start Foreign Currency
                $advance = DB::table("sale_advances")

                                ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='in' THEN amount_fx ELSE 0 END)  as advance_amount_in_fx, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='out' THEN amount_fx ELSE 0 END)  as advance_amount_out_fx"))
                                ->where('customer_id','=',$request->customer_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $customer_advance = $in - $out;

                        $in_fx = $advance->advance_amount_in_fx == NULL ? 0 : $advance->advance_amount_in_fx;
                        $out_fx = $advance->advance_amount_out_fx == NULL ? 0 : $advance->advance_amount_out_fx;
                        $customer_advance_fx = $in_fx - $out_fx;
                    }

                    if($customer_advance_fx == 0 || ($customer_advance_fx > 0 && $request->pay_amount_fx == 0)) {
                        // cashbook
                        if($request->customer_advance_fx == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount_fx != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'transition_date' => $sale->invoice_date,
                                    'customer_id' => $sale->customer_id,
                                    'sale_id' => $sale->id,
                                    'status'=>'sale',
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'debit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                           $this->storeSaleInLedger($sale);
                            // end ledger
                        } else {

                            AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side 
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                        }
                    }
                    else if(($customer_advance_fx > $request->pay_amount_fx) || $customer_advance_fx == $request->pay_amount_fx) 
                    {
                        AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side 
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            //Start => sale advance amount first in first out process and calculate gain/loss
                            $s_adv = DB::table("sale_advances")

                                ->select(DB::raw("sale_advances.*"))
                                ->where('customer_id','=',$request->customer_id)
                                ->where('balance','!=',0)
                                ->orderBy('advance_date', 'ASC')                                
                                ->get();
                            if(!empty($s_adv)) {
                                $payAmount = $request->pay_amount_fx;
                                $payAmountFx = 0;
                                foreach($s_adv as $sa) {
                                    if($payAmount != 0) {
                                        if($sa->balance < $payAmount) {

                                            $sale->advances()->attach($sa->id,['amount_fx' => $sa->balance]);
                                            $payAmountFx = $payAmountFx + $sa->balance;
                                            DB::table('sale_advances')
                                                ->where('id', $sa->id)
                                                ->update(array('balance' => 0));
                                            if($sa->currency_rate < $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($sa->currency_rate - $request->currency_rate) * $sa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($sa->currency_rate > $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $sa->currency_rate) * $sa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = $payAmount - $sa->balance;
                                        } else {
                                            // pa->balance >= $payAmount  process
                                            $sale->advances()->attach($sa->id,['amount_fx' => $payAmount]);
                                            $payAmountFx = $payAmountFx + $payAmount;
                                            DB::table('sale_advances')
                                                ->where('id', $sa->id)
                                                ->update(array('balance' => $sa->balance - $payAmount));
                                            if($sa->currency_rate < $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($sa->currency_rate - $request->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($sa->currency_rate > $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $sa->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = 0;
                                            // end =>  pa->balance >= $payAmount  process
                                        }

                                    } else {
                                        break;
                                    }
                                }
                            }
                            //End => sale advance amount first in first out process and calculate gain/loss

                            $obj = new SaleAdvance;
                            $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->customer_id = $request->customer_id;
                            $obj->sale_id = $sale->id;
                            $obj->currency_rate = $request->currency_rate;
                            $obj->amount_fx = $payAmountFx;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($customer_advance_fx < $request->pay_amount_fx) {
                        $paid_amt = $request->pay_amount - ($customer_advance_fx * $request->currency_rate);
                        // add in cashbook
                            
                            AccountTransition::create([
                                'sub_account_id' => $sub_account_id,
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'status'=>'sale',
                                'vochur_no'=>$invoice_no,
                                'description'=>$description,
                                'is_cashbook' => 1,
                                'debit' => $paid_amt,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            //add  in ledger

                            AccountTransition::create([
                                'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                // 'debit' => $sale->net_total,
                                // change for account side 
                                //'credit' => $paid_amt,
                                'debit' => $paid_amt,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            AccountTransition::create([
                                //'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                        //Start => sale advance amount first in first out process and calculate gain/loss
                        $s_adv = DB::table("sale_advances")

                            ->select(DB::raw("sale_advances.*"))
                            ->where('customer_id','=',$request->customer_id)
                            ->where('balance','!=',0)
                            ->orderBy('advance_date', 'ASC')                                
                            ->get();
                        if(!empty($s_adv)) {
                            $payAmount = $request->pay_amount_fx;
                            $payAmountFx = $customer_advance_fx;
                            foreach($s_adv as $sa) {
                                if($payAmount > 0 && $payAmount != 0) {
                                    if($sa->balance < $payAmount) {
                                        $sale->advances()->attach($sa->id,['amount_fx' => $sa->balance]);
                                        //$payAmountFx = $payAmountFx + $sa->balance;
                                        DB::table('sale_advances')
                                            ->where('id', $sa->id)
                                            ->update(array('balance' => 0));
                                        if($sa->currency_rate < $request->currency_rate) {
                                            //loss
                                            $loss_amt = ($sa->currency_rate - $request->currency_rate) * $sa->balance;
                                            AccountTransition::create([
                                                'sub_account_id' => 80,
                                                'transition_date' => $request->invoice_date,
                                                'sale_id' => $sale->id,
                                                'sale_advance_id' => $sa->id,
                                                'customer_id'=>$sa->customer_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Loss Amount',
                                                'vochur_no'=>$sa->advance_no,
                                                'debit' => $loss_amt,
                                                'status'=>'loss',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]); 
                                        }
                                        if($sa->currency_rate > $request->currency_rate) {
                                            //gain
                                            $gain_amt = ($request->currency_rate - $sa->currency_rate) * $sa->balance;
                                            AccountTransition::create([
                                                'sub_account_id' => 79,
                                                'transition_date' => $request->invoice_date,
                                                'sale_id' => $sale->id,
                                                'sale_advance_id' => $sa->id,
                                                'customer_id'=>$sa->customer_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Gain Amount',
                                                'vochur_no'=>$sa->advance_no,
                                                'credit' => $gain_amt,
                                                'status'=>'gain',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }
                                        $payAmount = $payAmount - $sa->balance;
                                    } else {
                                        // pa->balance >= $payAmount  process
                                        $sale->advances()->attach($sa->id,['amount_fx' => $payAmount]);
                                        //$payAmountFx = $payAmountFx + $payAmount;
                                        DB::table('sale_advances')
                                            ->where('id', $sa->id)
                                            ->update(array('balance' => $sa->balance - $payAmount));
                                        if($sa->currency_rate < $request->currency_rate) {
                                            //loss
                                            $loss_amt = ($sa->currency_rate - $request->currency_rate) * $payAmount;
                                            AccountTransition::create([
                                                'sub_account_id' => 80,
                                                'transition_date' => $request->invoice_date,
                                                'sale_id' => $sale->id,
                                                'sale_advance_id' => $sa->id,
                                                'customer_id'=>$sa->customer_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Loss Amount',
                                                'vochur_no'=>$sa->advance_no,
                                                'debit' => $loss_amt,
                                                'status'=>'loss',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]); 
                                        }
                                        if($sa->currency_rate > $request->currency_rate) {
                                            //gain
                                            $gain_amt = ($request->currency_rate - $sa->currency_rate) * $payAmount;
                                            AccountTransition::create([
                                                'sub_account_id' => 79,
                                                'transition_date' => $request->invoice_date,
                                                'sale_id' => $sale->id,
                                                'sale_advance_id' => $sa->id,
                                                'customer_id'=>$sa->customer_id,
                                                'is_cashbook' => 0,
                                                'description'=>'Gain Amount',
                                                'vochur_no'=>$sa->advance_no,
                                                'credit' => $gain_amt,
                                                'status'=>'gain',
                                                'created_by' => Auth::user()->id,
                                                'updated_by' => Auth::user()->id,
                                            ]);
                                        }
                                        $payAmount = 0;
                                        // end =>  pa->balance >= $payAmount  process
                                    }

                                } else {
                                    break;
                                }
                            }
                        }
                        //End => sale advance amount first in first out process and calculate gain/loss

                        $obj = new SaleAdvance;
                        $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->customer_id = $request->customer_id;
                        $obj->sale_id = $sale->id;
                        $obj->currency_id = $request->currency_rate;
                        $obj->amount_fx = $payAmountFx;
                        $obj->amount = $customer_advance_fx * $request->currency_rate;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
                //End Foreign Currency
              }
            }
            $sale_id = $sale->id;
            for ($i = 0; $i < count($request->product); $i++) {
                //get product pre-defined UOM
                $product_result = Product::select('uom_id')->find($request->product[$i]);
                $main_uom_id = $product_result->uom_id;
                //add product into pivot table
                /**$pivot = $sale->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'price' => $request->unit_price[$i], 'price_variant' => $request->price_variants[$i], 'total_amount' => $request->total_amount[$i]]);**/

                if ($request->currency_id != 1) {
                    $rate_fx = $request->rate_fx[$i] == '' ? 0 :  $request->rate_fx[$i];
                    $actual_rate_fx = $request->actual_rate_fx[$i] == '' ? 0 :  $request->actual_rate_fx[$i];
                    $discount_fx = $request->discount_fx[$i] == '' ? 0 :  $request->discount_fx[$i];
                    $other_discount_fx = $request->other_discount_fx[$i] == '' ? 0 :  $request->other_discount_fx[$i];
                    $total_amount_fx = $request->total_amount_fx[$i] == '' ? 0 :  $request->total_amount_fx[$i];
                } else {
                    $rate_fx = 0;
                    $actual_rate_fx = 0;
                    $discount_fx = 0;
                    $other_discount_fx = 0;
                    $total_amount_fx = 0;
                }

                if ($request->sale_order == true) {
                    $pivot = $sale->products()->attach($request->product[$i], ['order_product_pivot_id' => $request->order_product_id[$i], 'uom_id' => $request->uom[$i], 'ctn' => $request->ctn[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'rate_fx' => $rate_fx, 'actual_rate' => $request->actual_rate[$i], 'actual_rate_fx' => $actual_rate_fx, 'discount' => $request->discount[$i], 'discount_fx' => $discount_fx, 'other_discount' => $request->other_discount[$i], 'other_discount_fx' => $other_discount_fx, 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx, 'is_foc' => $request->is_foc[$i]]);
                } else {
                    $pivot = $sale->products()->attach($request->product[$i], ['uom_id' => $request->uom[$i], 'ctn' => $request->ctn[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'rate_fx' => $rate_fx, 'actual_rate' => $request->actual_rate[$i], 'actual_rate_fx' => $actual_rate_fx, 'discount' => $request->discount[$i], 'discount_fx' => $discount_fx, 'other_discount' => $request->other_discount[$i], 'other_discount_fx' => $other_discount_fx, 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx, 'is_foc' => $request->is_foc[$i]]);
                }

                //get last pivot insert id
                $last_row = DB::table('product_sale')->orderBy('id', 'DESC')->first();

                $pivot_id = $last_row->id;

                if ($request->sale_order == true) {
                    //update quantiy in product_order table
                    $accepted_qty = 0;
                    $po_result = DB::table('product_order')
                        ->select('accepted_quantity')
                        ->where('id', $request->order_product_id[$i])
                        ->first();
                    if ($po_result->accepted_quantity == NULL) {
                        $accepted_qty = $request->qty[$i];
                    } else {
                        $accepted_qty = $po_result->accepted_quantity + $request->qty[$i];
                    }


                    DB::table('product_order')
                        ->where('id', $request->order_product_id[$i])
                        ->update(array('accepted_quantity' => $accepted_qty));

                    //Check order status is done or not
                    $chk_order = DB::table("product_order")

                        ->select(DB::raw("SUM(CASE  WHEN product_quantity IS NOT NULL THEN product_quantity  ELSE 0 END)  as product_qty, SUM(CASE  WHEN accepted_quantity IS NOT NULL THEN accepted_quantity  ELSE 0 END)  as accepted_qty"))
                        ->where('order_id', '=', $request->order_id)
                        ->groupBy('order_id')
                        ->first();
                    //update order status
                    $order = Order::find($request->order_id);
                    if ($chk_order->product_qty == $chk_order->accepted_qty) {
                        $status = "Done";
                        //change status in order table;
                        $order->order_status = $status;
                        $order->save();
                    }
                }

                //calculate quantity for product pre-defined UOM
                $uom_relation = DB::table('product_selling_uom')
                    ->select('relation')
                    ->where('product_id', $request->product[$i])
                    ->where('uom_id', $request->uom[$i])
                    ->first();
                if ($uom_relation) {
                    $relation_val = $uom_relation->relation;
                } else {
                    //for pre-defined product uom
                    $relation_val = 1;
                }
                // $pp=DB::table('product_purchase')->where('product_id',$request->product[$i])->get();
                // $q=$m=0;
                $cost_price = $this->getCostPrice($request->product[$i])->product_cost_price;
                // dd($cost_price);
                $store_cost_price = Product::find($request->product[$i]);
                if ($cost_price == 0) {
                    $cost_price = $store_cost_price->purchase_price;
                }
                $store_cost_price->cost_price = $cost_price;
                $store_cost_price->save();
                //add products in transition table=> transition_type = out (for sold out)
                $obj = new ProductTransition;
                $obj->product_id            = $request->product[$i];
                $obj->transition_type       = "out";
                $obj->transition_sale_id   = $sale_id;
                $obj->transition_product_pivot_id   = $pivot_id;
                $obj->branch_id  = Auth::user()->branch_id;
                $obj->warehouse_id          = Auth::user()->warehouse_id;
                $obj->transition_date       = $request->invoice_date;
                $obj->cost_price            = $cost_price * $request->qty[$i] * $relation_val;
                $obj->transition_product_uom_id        = $request->uom[$i];
                $obj->transition_product_quantity      = $request->qty[$i];
                $obj->product_uom_id        = $main_uom_id;
                $obj->product_quantity      = $request->qty[$i] * $relation_val;
                $obj->created_by = Auth::user()->id;
                $obj->updated_by = Auth::user()->id;
                $obj->save();
            }
            $status = "success";
            $sale_id = $sale->id;
            DB::commit();
            return compact('status', 'sale_id');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
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
        /**if(!empty($request->reference_no) && $request->duplicate_ref_no == false) {
            $validatedData = $request->validate([
                'reference_no' => 'max:255|unique:sales,reference_no,'.$id,
            ]);
        }**/
        DB::beginTransaction();

        try {
            $sale = Sale::find($id);
            $old_sub_account_id = $sale->payment_type == 'credit' ? config('global.sale_advance') : config('global.cash_sale');
            if ($sale->payment_type == 'cash') {
                $old_cash_sale_account_id = config('global.cash_sale');
                $old_discount_allowed_account_id = config('global.discount_allowed');
            }
            $sale->invoice_no = $request->invoice_no;
            $sale->invoice_type = $request->invoice_type;
            $sale->customer_id = $request->customer_id;
            $sale->branch_id = Auth::user()->branch_id;
            $sale->reference_no = $request->reference_no;
            $sale->invoice_date = $request->invoice_date;
            //$sale->warehouse_id = Auth::user()->warehouse_id;
            $sale->customer_id = $request->customer_id;
            //$sale->delivery_approve = 0;
            $sale->office_sale_man_id = $request->office_sale_man_id;

            if ($request->sale_order == true) {
                $sale->order_id = $request->order_id;
            }

            $sale->pay_amount = $request->pay_amount;
            $sale->sale_type  = $request->sale_type;

            $sale->total_amount = $request->sub_total;
            $sale->cash_discount = $request->cash_discount;
            $sale->net_total = $request->net_total;
            $sale->tax = $request->tax;
            $sale->tax_amount = $request->tax_amount;
            $sale->balance_amount = $request->balance_amount;

            if ($request->payment_type == 'credit') {
                $sub_account_id = config('global.sale_advance');     /*sub account_id for sale advance*/
                if ($request->pay_amount != 0) {
                    $amount = $request->pay_amount;
                }
                $sale->payment_type = 'credit';
                $sale->due_date = $request->due_date;
                $sale->credit_day = $request->credit_day;
            } else {
                $sub_account_id = config('global.cash_sale');     /*sub account_id for cash sale*/
                // $cash_sale_account_id=config('global.cash_sale');     /*sub account_id for cash sale */
                $amount = $request->pay_amount;
                $sale->payment_type = 'cash';
            }

            $sale->currency_id = $request->currency_id;
            if ($request->currency_id != 1) {
                $sale->currency_rate = $request->currency_rate;
                $sale->pay_amount_fx = $request->pay_amount_fx == '' ? 0 : $request->pay_amount_fx;
                $sale->total_amount_fx = $request->sub_total_fx == '' ? 0 : $request->sub_total_fx;
                $sale->cash_discount_fx = $request->cash_discount_fx == '' ? 0 : $request->cash_discount_fx;
                $sale->net_total_fx = $request->net_total_fx == '' ? 0 : $request->net_total_fx;
                $sale->tax_fx = $request->tax_fx == '' ? 0 : $request->tax_fx;
                $sale->tax_amount_fx = $request->tax_amount_fx == '' ? 0 : $request->tax_amount_fx;
                $sale->balance_amount_fx = $request->balance_amount_fx  == '' ? 0 : $request->balance_amount_fx;;
            } else {
                $sale->currency_rate = 0;
                $sale->pay_amount_fx = 0;
                $sale->total_amount_fx = 0;
                $sale->cash_discount_fx = 0;
                $sale->net_total_fx = 0;
                $sale->tax_fx = 0;
                $sale->tax_amount_fx = 0;
                $sale->balance_amount_fx = 0;
            }

            $sale->updated_at = time();
            $sale->updated_by = Auth::user()->id;
            $sale->save();

            $warehouse_id = $sale->warehouse_id;

            $cus=Customer::find($request->customer_id);
            $description=$sale->invoice_no.", Date ".$sale->invoice_date." by " .$cus->cus_name;
            if($sale){
                AccountTransition::where('sale_id',$id)
                                        ->where(function($query) {
                                                $query->where('status','sale')
                                                      ->orWhere('status','gain')
                                                      ->orWhere('status','loss');
                                            })
                                        ->delete();

                SaleAdvance::where('sale_id',$id)
                                ->where('advance_type','out')
                                ->delete();
                if($request->currency_id == 1) {
                   /**AccountTransition::where('sale_id',$id)
                                        ->where('status','sale')
                                        ->delete(); **/
                    
                    //get customer's sale advance
                    $customer_advance = 0;
                    $customer_advance_fx = 0;
                    $advance = DB::table("sale_advances")

                                ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out"))
                                ->where('customer_id','=',$request->customer_id)
                                ->first();
                    if(!empty($advance)) {
                        $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                        $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                        $customer_advance = $in - $out;
                    }

                    if($customer_advance == 0 || ($customer_advance > 0 && $request->pay_amount == 0)) {
                        // cashbook
                        if($request->customer_advance == 0) {
                            if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount != 0)) {
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'transition_date' => $sale->invoice_date,
                                    'sale_id' => $sale->id,
                                    'status'=>'sale',
                                    'vochur_no'=>$request->invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'debit' => $amount,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                            // end cashbook 

                            // for ledger 
                           $this->storeSaleInLedger($sale);
                            // end ledger
                        } else {

                            AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side 
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                        }
                    }
                    else if(($customer_advance > $request->pay_amount) || $customer_advance == $request->pay_amount) {
                        AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side 
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            $obj = new SaleAdvance;
                            $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->customer_id = $request->customer_id;
                            $obj->sale_id = $sale->id;
                            $obj->amount = $request->pay_amount;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                        
                    }
                    else if($customer_advance < $request->pay_amount) {
                        $paid_amt = $request->pay_amount - $customer_advance;
                        // add in cashbook
                            AccountTransition::create([
                                'sub_account_id' => $sub_account_id,
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'status'=>'sale',
                                'vochur_no'=>$sale->invoice_no,
                                'description'=>$description,
                                'is_cashbook' => 1,
                                'debit' => $paid_amt,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);
                            //add  in ledger

                            AccountTransition::create([
                                'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                // 'debit' => $sale->net_total,
                                // change for account side 
                                //'credit' => $paid_amt,
                                'debit' => $paid_amt,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                            AccountTransition::create([
                                'sub_account_id' => config('global.sale'),
                                'transition_date' => $sale->invoice_date,
                                'sale_id' => $sale->id,
                                'customer_id' => $sale->customer_id,
                                'vochur_no' => $sale->invoice_no,
                                'description' => '',
                                'is_cashbook' => 0,
                                'status' => 'sale',
                                //'debit' => $sale->net_total,
                                'credit' => $sale->net_total + $sale->tax_amount,
                                // change for account side
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);

                        $obj = new SaleAdvance;
                        $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                        if($max_id) {
                            $max_id = $max_id + 1;
                        }else {
                            $max_id = 1;
                        }
                        $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                        $obj->advance_no = $advance_no;
                        $obj->advance_date = $request->invoice_date;
                        $obj->customer_id = $request->customer_id;
                        $obj->sale_id = $sale->id;
                        $obj->amount = $customer_advance;
                        $obj->advance_type = 'out';
                        $obj->created_by = Auth::user()->id;
                        $obj->save();
                        
                    } else {

                    }
                } else {
                    //For Foreign Currency
                    //Reset advance and remove in purchase_advance_links
                    $old_advance = DB::table("sale_advance_links")

                                ->select(DB::raw("sale_advance_links.*, sale_advances.balance,sale_advances.id"))
                                ->leftjoin('sale_advances', 'sale_advances.id', '=', 'sale_advance_links.advance_id')
                                ->where('sale_advance_links.sale_id','=',$sale->id)
                                ->get();
                    if(!empty($old_advance)) {
                        foreach($old_advance as $a) {
                            $balance = $a->balance + $a->amount_fx;
                            DB::table('sale_advances')
                                ->where('id', $a->id)
                                ->update(array('balance' => $balance));   
                        }
                    }

                    $sale->advances()->detach();
                    //start Foreign Currency
                    $advance = DB::table("sale_advances")

                                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='in' THEN amount_fx ELSE 0 END)  as advance_amount_in_fx, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='out' THEN amount_fx ELSE 0 END)  as advance_amount_out_fx"))
                                    ->where('customer_id','=',$request->customer_id)
                                    ->first();
                        if(!empty($advance)) {
                            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
                            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
                            $customer_advance = $in - $out;

                            $in_fx = $advance->advance_amount_in_fx == NULL ? 0 : $advance->advance_amount_in_fx;
                            $out_fx = $advance->advance_amount_out_fx == NULL ? 0 : $advance->advance_amount_out_fx;
                            $customer_advance_fx = $in_fx - $out_fx;
                        }

                        if($customer_advance_fx == 0 || ($customer_advance_fx > 0 && $request->pay_amount_fx == 0)) {
                            // cashbook
                            if($request->customer_advance_fx == 0) {
                                if ($request->payment_type == 'cash' || ($request->payment_type == 'credit' && $request->pay_amount_fx != 0)) {
                                    AccountTransition::create([
                                        'sub_account_id' => $sub_account_id,
                                        'transition_date' => $sale->invoice_date,
                                        'customer_id' => $sale->customer_id,
                                        'sale_id' => $sale->id,
                                        'status'=>'sale',
                                        'vochur_no'=>$invoice_no,
                                        'description'=>$description,
                                        'is_cashbook' => 1,
                                        'debit' => $amount,
                                        'created_by' => Auth::user()->id,
                                        'updated_by' => Auth::user()->id,
                                    ]);
                                }
                                // end cashbook 

                                // for ledger 
                               $this->storeSaleInLedger($sale);
                                // end ledger
                            } else {

                                AccountTransition::create([
                                    'sub_account_id' => config('global.sale'),
                                    'transition_date' => $sale->invoice_date,
                                    'sale_id' => $sale->id,
                                    'customer_id' => $sale->customer_id,
                                    'vochur_no' => $sale->invoice_no,
                                    'description' => '',
                                    'is_cashbook' => 0,
                                    'status' => 'sale',
                                    //'debit' => $sale->net_total,
                                    'credit' => $sale->net_total + $sale->tax_amount,
                                    // change for account side 
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            }
                        }
                        else if(($customer_advance_fx > $request->pay_amount_fx) || $customer_advance_fx == $request->pay_amount_fx) {
                            AccountTransition::create([
                                    'sub_account_id' => config('global.sale'),
                                    'transition_date' => $sale->invoice_date,
                                    'sale_id' => $sale->id,
                                    'customer_id' => $sale->customer_id,
                                    'vochur_no' => $sale->invoice_no,
                                    'description' => '',
                                    'is_cashbook' => 0,
                                    'status' => 'sale',
                                    //'debit' => $sale->net_total,
                                    'credit' => $sale->net_total + $sale->tax_amount,
                                    // change for account side 
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                //Start => sale advance amount first in first out process and calculate gain/loss
                                $s_adv = DB::table("sale_advances")

                                    ->select(DB::raw("sale_advances.*"))
                                    ->where('customer_id','=',$request->customer_id)
                                    ->where('balance','!=',0)
                                    ->orderBy('advance_date', 'ASC')                                
                                    ->get();
                                if(!empty($s_adv)) {
                                    $payAmount = $request->pay_amount_fx;
                                    $payAmountFx = 0;
                                    foreach($s_adv as $sa) {
                                        if($payAmount != 0) {
                                            if($sa->balance < $payAmount) {

                                                $sale->advances()->attach($sa->id,['amount_fx' => $sa->balance]);
                                                $payAmountFx = $payAmountFx + $sa->balance;
                                                DB::table('sale_advances')
                                                    ->where('id', $sa->id)
                                                    ->update(array('balance' => 0));
                                                if($sa->currency_rate < $request->currency_rate) {
                                                    //loss
                                                    $loss_amt = ($sa->currency_rate - $request->currency_rate) * $sa->balance;
                                                    AccountTransition::create([
                                                        'sub_account_id' => 80,
                                                        'transition_date' => $request->invoice_date,
                                                        'sale_id' => $sale->id,
                                                        'sale_advance_id' => $sa->id,
                                                        'customer_id'=>$sa->customer_id,
                                                        'is_cashbook' => 0,
                                                        'description'=>'Loss Amount',
                                                        'vochur_no'=>$sa->advance_no,
                                                        'debit' => $loss_amt,
                                                        'status'=>'loss',
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]); 
                                                }
                                                if($sa->currency_rate > $request->currency_rate) {
                                                    //gain
                                                    $gain_amt = ($request->currency_rate - $sa->currency_rate) * $sa->balance;
                                                    AccountTransition::create([
                                                        'sub_account_id' => 79,
                                                        'transition_date' => $request->invoice_date,
                                                        'sale_id' => $sale->id,
                                                        'sale_advance_id' => $sa->id,
                                                        'customer_id'=>$sa->customer_id,
                                                        'is_cashbook' => 0,
                                                        'description'=>'Gain Amount',
                                                        'vochur_no'=>$sa->advance_no,
                                                        'credit' => $gain_amt,
                                                        'status'=>'gain',
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]);
                                                }
                                                $payAmount = $payAmount - $sa->balance;
                                            } else {
                                                // pa->balance >= $payAmount  process
                                                $sale->advances()->attach($sa->id,['amount_fx' => $payAmount]);
                                                $payAmountFx = $payAmountFx + $payAmount;
                                                DB::table('sale_advances')
                                                    ->where('id', $sa->id)
                                                    ->update(array('balance' => $sa->balance - $payAmount));
                                                if($sa->currency_rate < $request->currency_rate) {
                                                    //loss
                                                    $loss_amt = ($sa->currency_rate - $request->currency_rate) * $payAmount;
                                                    AccountTransition::create([
                                                        'sub_account_id' => 80,
                                                        'transition_date' => $request->invoice_date,
                                                        'sale_id' => $sale->id,
                                                        'sale_advance_id' => $sa->id,
                                                        'customer_id'=>$sa->customer_id,
                                                        'is_cashbook' => 0,
                                                        'description'=>'Loss Amount',
                                                        'vochur_no'=>$sa->advance_no,
                                                        'debit' => $loss_amt,
                                                        'status'=>'loss',
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]); 
                                                }
                                                if($sa->currency_rate > $request->currency_rate) {
                                                    //gain
                                                    $gain_amt = ($request->currency_rate - $sa->currency_rate) * $payAmount;
                                                    AccountTransition::create([
                                                        'sub_account_id' => 79,
                                                        'transition_date' => $request->invoice_date,
                                                        'sale_id' => $sale->id,
                                                        'sale_advance_id' => $sa->id,
                                                        'customer_id'=>$sa->customer_id,
                                                        'is_cashbook' => 0,
                                                        'description'=>'Gain Amount',
                                                        'vochur_no'=>$sa->advance_no,
                                                        'credit' => $gain_amt,
                                                        'status'=>'gain',
                                                        'created_by' => Auth::user()->id,
                                                        'updated_by' => Auth::user()->id,
                                                    ]);
                                                }
                                                $payAmount = 0;
                                                // end =>  pa->balance >= $payAmount  process
                                            }

                                        } else {
                                            break;
                                        }
                                    }
                                }
                                //End => sale advance amount first in first out process and calculate gain/loss

                                $obj = new SaleAdvance;
                                $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                                if($max_id) {
                                    $max_id = $max_id + 1;
                                }else {
                                    $max_id = 1;
                                }
                                $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                                $obj->advance_no = $advance_no;
                                $obj->advance_date = $request->invoice_date;
                                $obj->customer_id = $request->customer_id;
                                $obj->sale_id = $sale->id;
                                $obj->currency_rate = $request->currency_rate;
                                $obj->amount_fx = $payAmountFx;
                                $obj->amount = $request->pay_amount;
                                $obj->advance_type = 'out';
                                $obj->created_by = Auth::user()->id;
                                $obj->save();
                            
                        }
                        else if($customer_advance_fx < $request->pay_amount_fx) {
                            $paid_amt = $request->pay_amount - ($customer_advance_fx * $request->currency_rate);
                            // add in cashbook
                                
                                AccountTransition::create([
                                    'sub_account_id' => $sub_account_id,
                                    'transition_date' => $sale->invoice_date,
                                    'sale_id' => $sale->id,
                                    'customer_id' => $sale->customer_id,
                                    'status'=>'sale',
                                    'vochur_no'=>$invoice_no,
                                    'description'=>$description,
                                    'is_cashbook' => 1,
                                    'debit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                //add  in ledger

                                AccountTransition::create([
                                    'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                    'transition_date' => $sale->invoice_date,
                                    'sale_id' => $sale->id,
                                    'customer_id' => $sale->customer_id,
                                    'vochur_no' => $sale->invoice_no,
                                    'description' => '',
                                    'is_cashbook' => 0,
                                    'status' => 'sale',
                                    // 'debit' => $sale->net_total,
                                    // change for account side 
                                    //'credit' => $paid_amt,
                                    'debit' => $paid_amt,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                AccountTransition::create([
                                    //'sub_account_id' => $sale->payment_type == 'cash' ? $this->cash_sale : $this->sale_advance,
                                    'sub_account_id' => config('global.sale'),
                                    'transition_date' => $sale->invoice_date,
                                    'sale_id' => $sale->id,
                                    'customer_id' => $sale->customer_id,
                                    'vochur_no' => $sale->invoice_no,
                                    'description' => '',
                                    'is_cashbook' => 0,
                                    'status' => 'sale',
                                    //'debit' => $sale->net_total,
                                    'credit' => $sale->net_total + $sale->tax_amount,
                                    // change for account side
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);

                            //Start => sale advance amount first in first out process and calculate gain/loss
                            $s_adv = DB::table("sale_advances")

                                ->select(DB::raw("sale_advances.*"))
                                ->where('customer_id','=',$request->customer_id)
                                ->where('balance','!=',0)
                                ->orderBy('advance_date', 'ASC')                                
                                ->get();
                            if(!empty($s_adv)) {
                                $payAmount = $request->pay_amount_fx;
                                $payAmountFx = $customer_advance_fx;
                                foreach($s_adv as $sa) {
                                    if($payAmount > 0 && $payAmount != 0) {
                                        if($sa->balance < $payAmount) {
                                            $sale->advances()->attach($sa->id,['amount_fx' => $sa->balance]);
                                            //$payAmountFx = $payAmountFx + $sa->balance;
                                            DB::table('sale_advances')
                                                ->where('id', $sa->id)
                                                ->update(array('balance' => 0));
                                            if($sa->currency_rate < $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($sa->currency_rate - $request->currency_rate) * $sa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($sa->currency_rate > $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $sa->currency_rate) * $sa->balance;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = $payAmount - $sa->balance;
                                        } else {
                                            // pa->balance >= $payAmount  process
                                            $sale->advances()->attach($sa->id,['amount_fx' => $payAmount]);
                                            //$payAmountFx = $payAmountFx + $payAmount;
                                            DB::table('sale_advances')
                                                ->where('id', $sa->id)
                                                ->update(array('balance' => $sa->balance - $payAmount));
                                            if($sa->currency_rate < $request->currency_rate) {
                                                //loss
                                                $loss_amt = ($sa->currency_rate - $request->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 80,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Loss Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'debit' => $loss_amt,
                                                    'status'=>'loss',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]); 
                                            }
                                            if($sa->currency_rate > $request->currency_rate) {
                                                //gain
                                                $gain_amt = ($request->currency_rate - $sa->currency_rate) * $payAmount;
                                                AccountTransition::create([
                                                    'sub_account_id' => 79,
                                                    'transition_date' => $request->invoice_date,
                                                    'sale_id' => $sale->id,
                                                    'sale_advance_id' => $sa->id,
                                                    'customer_id'=>$sa->customer_id,
                                                    'is_cashbook' => 0,
                                                    'description'=>'Gain Amount',
                                                    'vochur_no'=>$sa->advance_no,
                                                    'credit' => $gain_amt,
                                                    'status'=>'gain',
                                                    'created_by' => Auth::user()->id,
                                                    'updated_by' => Auth::user()->id,
                                                ]);
                                            }
                                            $payAmount = 0;
                                            // end =>  pa->balance >= $payAmount  process
                                        }

                                    } else {
                                        break;
                                    }
                                }
                            }
                            //End => sale advance amount first in first out process and calculate gain/loss

                            $obj = new SaleAdvance;
                            $max_id = SaleAdvance::where('advance_type','out')->max('advance_no');
                            if($max_id) {
                                $max_id = $max_id + 1;
                            }else {
                                $max_id = 1;
                            }
                            $advance_no = str_pad($max_id,5,"0",STR_PAD_LEFT);

                            $obj->advance_no = $advance_no;
                            $obj->advance_date = $request->invoice_date;
                            $obj->customer_id = $request->customer_id;
                            $obj->sale_id = $sale->id;
                            $obj->amount_fx = $payAmountFx;
                            $obj->currency_rate = $request->currency_rate;
                            $obj->amount = $customer_advance_fx * $request->currency_rate;
                            $obj->advance_type = 'out';
                            $obj->created_by = Auth::user()->id;
                            $obj->save();
                            
                        } else {

                        }
                    //End Foreign Currency
                }
                /***if($request->payment_type =='cash' || ($request->payment_type=='credit' && $request->pay_amount!=0)) {
                    $update_cashbook=AccountTransition::where('sale_id',$id)->where('sub_account_id',$old_sub_account_id)->where('is_cashbook',1)->delete();

                       AccountTransition::create([
                            'sub_account_id' => $sub_account_id,
                            'transition_date' => $sale->invoice_date,
                            'sale_id' => $sale->id,
                            'is_cashbook' => 1,
                            'debit' => $amount,
                            'status'=>'sale',
                            'description'=>$description,
                            'vochur_no'=>$request->invoice_no,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ]);
                        $this->updateSaleInLedger($sale);

                }elseif($request->payment_type=='credit' && $request->pay_amount==0){
                    AccountTransition::where('sale_id',$id)
                    ->where('sub_account_id',$old_sub_account_id)
                    ->delete();
                    $this->updateSaleInLedger($sale);
                }****/
            }
            $ex_pivot_arr = $request->ex_product_pivot;

            //remove id in pivot that are removed in sale Form
            $results = array_diff($ex_pivot_arr, $request->product_pivot); //get id that are not in request pivot array
            foreach ($results as $key => $val) {
                //delete in pivot
                DB::table('product_sale')
                    ->where('id', $val)
                    ->delete();
                //delete in transition
                DB::table('product_transitions')
                    ->where('transition_product_pivot_id', $val)
                    ->where('transition_sale_id', $id)
                    ->delete();
            }
            //update in product pivot table
            for ($i = 0; $i < count($request->product); $i++) {

                if ($request->currency_id != 1) {
                    $rate_fx = $request->rate_fx[$i] == '' ? 0 :  $request->rate_fx[$i];
                    $actual_rate_fx = $request->actual_rate_fx[$i] == '' ? 0 :  $request->actual_rate_fx[$i];
                    $discount_fx = $request->discount_fx[$i] == '' ? 0 :  $request->discount_fx[$i];
                    $other_discount_fx = $request->other_discount_fx[$i] == '' ? 0 :  $request->other_discount_fx[$i];
                    $total_amount_fx = $request->total_amount_fx[$i] == '' ? 0 :  $request->total_amount_fx[$i];
                } else {
                    $rate_fx = 0;
                    $actual_rate_fx = 0;
                    $discount_fx = 0;
                    $other_discount_fx = 0;
                    $total_amount_fx = 0;
                }

                //check product already exist or not
                if ($request->product_pivot[$i] != '0' && in_array($request->product_pivot[$i], $ex_pivot_arr)) {
                    //update existing product in pivot and transition tables
                    DB::table('product_sale')
                        ->where('id', $request->product_pivot[$i])
                        ->update(array('uom_id' => $request->uom[$i], 'ctn' => $request->ctn[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'rate_fx' => $rate_fx, 'actual_rate' => $request->actual_rate[$i], 'actual_rate_fx' => $actual_rate_fx, 'discount' => $request->discount[$i], 'discount_fx' => $discount_fx, 'other_discount' => $request->other_discount[$i], 'other_discount_fx' => $other_discount_fx, 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx, 'is_foc' => $request->is_foc[$i]));

                    //get product pre-defined UOM
                    $product_result = Product::select('uom_id')->find($request->product[$i]);
                    $main_uom_id = $product_result->uom_id;
                    //calculate quantity for product pre-defined UOM
                    $uom_relation = DB::table('product_selling_uom')
                        ->select('relation')
                        ->where('product_id', $request->product[$i])
                        ->where('uom_id', $request->uom[$i])
                        ->first();
                    if ($uom_relation) {
                        $relation_val = $uom_relation->relation;
                    } else {
                        //for pre-defined product uom
                        $relation_val = 1;
                    }
                    $product_qty = $request->qty[$i] * $relation_val;
                    $p = Product::find($request->product[$i]);
                    $cost_price = $p->cost_price;
                    DB::table('product_transitions')
                        ->where('transition_product_pivot_id', $request->product_pivot[$i])
                        ->where('transition_sale_id', $id)
                        ->update(array('cost_price' => $cost_price * $request->qty[$i] * $relation_val, 'product_uom_id' => $main_uom_id, 'product_quantity' => $product_qty, 'transition_product_uom_id' => $request->uom[$i], 'transition_date' => $request->invoice_date, 'transition_product_quantity' => $request->qty[$i], 'warehouse_id' => $warehouse_id));
                } else {
                    //add product into pivot table if not exist

                    //get product pre-defined UOM
                    $product_result = Product::select('uom_id')->find($request->product[$i]);
                    $main_uom_id = $product_result->uom_id;
                    //add product into pivot table
                    /*$pivot = $sale->products()->attach($request->product[$i],['uom_id' => $request->uom[$i], 'product_quantity' => $request->qty[$i], 'price' => $request->unit_price[$i], 'price_variant' => $request->price_variants[$i], 'total_amount' => $request->total_amount[$i]]);*/

                    $pivot = $sale->products()->attach($request->product[$i], ['uom_id' => $request->uom[$i], 'ctn' => $request->ctn[$i], 'product_quantity' => $request->qty[$i], 'rate' => $request->rate[$i], 'rate_fx' => $rate_fx, 'actual_rate' => $request->actual_rate[$i], 'actual_rate_fx' => $actual_rate_fx, 'discount' => $request->discount[$i], 'discount_fx' => $discount_fx, 'other_discount' => $request->other_discount[$i], 'other_discount_fx' => $other_discount_fx, 'total_amount' => $request->total_amount[$i], 'total_amount_fx' => $total_amount_fx, 'is_foc' => $request->is_foc[$i]]);

                    //get last pivot insert id
                    $last_row = DB::table('product_sale')->orderBy('id', 'DESC')->first();

                    $pivot_id = $last_row->id;

                    //calculate quantity for product pre-defined UOM
                    $uom_relation = DB::table('product_selling_uom')
                        ->select('relation')
                        ->where('product_id', $request->product[$i])
                        ->where('uom_id', $request->uom[$i])
                        ->first();
                    if ($uom_relation) {
                        $relation_val = $uom_relation->relation;
                    } else {
                        //for pre-defined product uom
                        $relation_val = 1;
                    }
                    // $cost_price=$this->getCostPrice($request->product[$i])->product_cost_price;
                    /* $store_cost_price=Product::find($request->product[$i]);
                    $cost_price=$store_cost_price->cost_price;*/
                    $cost_price = $this->getCostPrice($request->product[$i])->product_cost_price;
                    $store_cost_price = Product::find($request->product[$i]);
                    //$store_cost_price->cost_price=$cost_price;
                    //$store_cost_price->save();
                    if ($cost_price == 0) {
                        $cost_price = $store_cost_price->purchase_price;
                    }
                    $store_cost_price->cost_price = $cost_price;
                    $store_cost_price->save();
                    //add products in transition table=> transfer_type = out (for sold out)
                    $obj = new ProductTransition;
                    $obj->product_id            = $request->product[$i];
                    $obj->transition_type       = "out";
                    $obj->transition_sale_id   = $id;
                    $obj->transition_product_pivot_id   = $pivot_id;
                    $obj->branch_id  = Auth::user()->branch_id;
                    $obj->warehouse_id          = $warehouse_id;
                    $obj->transition_date       = $request->invoice_date;
                    $obj->transition_product_uom_id = $request->uom[$i];
                    $obj->cost_price            = $cost_price * $request->qty[$i] * $relation_val;
                    $obj->transition_product_quantity  = $request->qty[$i];
                    $obj->product_uom_id        = $main_uom_id;
                    $obj->product_quantity      = $request->qty[$i] * $relation_val;
                    $obj->created_by = Auth::user()->id;
                    $obj->updated_by = Auth::user()->id;
                    $obj->save();
                }
            }

            $status = "success";
            DB::commit();
            return compact('status');
        } catch (\Throwable $e) {
            DB::rollback();
            $status = $e->getMessage();
            return compact('status');
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $access_brands = array();
        $sale = Sale::with('currency', 'products', 'collections', 'deliveries', 'warehouse', 'customer', 'products.brand', 'products.category', 'products.uom', 'products.selling_uoms', 'order', 'sale_man', 'branch')->find($id);
        if (Auth::user()->role->id == 6) {
            //for Country Head User
            foreach (Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }
        }

        $customer_id = $sale->customer_id;

        $previous_balance = 0;
        $return_amount = 0;
        /**$chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount ELSE 0 END)  as previous_balance"))
            ->where('customer_id','=',$customer_id)
            ->where('id', '!=', $id)
            ->groupBy('customer_id')
            ->first();
        if($chk_balance) {
            $previous_balance  = $chk_balance->previous_balance;
        }*/
        $previous_balance = 0;
        $chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN collection_amount IS NOT NULL THEN collection_amount  ELSE 0 END)  as total_collection_amount, SUM(CASE  WHEN discount IS NOT NULL THEN discount  ELSE 0 END)  as total_discount, SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as total_balance"))
            ->where('customer_id', '=', $customer_id)
            ->where('id', '!=', $id)
            ->where('payment_type', '=', 'credit')
            ->groupBy('customer_id')
            ->first();
        if ($chk_balance) {
            //$previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount + $chk_balance->total_discount);
            $previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount);
        }

        $chk_return = DB::table("sale_returns")

            ->select(DB::raw("SUM(sale_returns.total_payment_amount)  as return_payment"))
            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->where('sale_returns.customer_id', '=', $customer_id)
            ->where('sales.payment_type', '!=', 'cash')
            ->where('sales.id', '!=', $id)
            ->groupBy('sale_returns.customer_id')
            ->first();
        if ($chk_return) {
            $return_amount = $chk_return->return_payment;
        }

        $cus_return_amount = 0;
        $chk_cus_return = DB::table("return_invoices")

            ->select(DB::raw("SUM(return_invoices.return_amount)  as cus_return_amount"))
            ->leftjoin('customer_returns', 'customer_returns.id', '=', 'return_invoices.customer_return_id')
            ->where('customer_returns.customer_id', '=', $customer_id)
            ->where('return_invoices.sale_id', '!=', $id)
            ->groupBy('customer_returns.customer_id')
            ->first();
        if ($chk_cus_return) {
            $cus_return_amount = $chk_cus_return->cus_return_amount;
        }

        $previous_balance = ($previous_balance - $return_amount) - $cus_return_amount;
        return compact('sale', 'access_brands', 'previous_balance');
    }

    public function getCustomerPreviousBalance($id)
    {

        $previous_balance = 0;
        $return_amount = 0;
        /**$chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount ELSE 0 END)  as previous_balance"))
            ->where('customer_id','=',$id)
            ->groupBy('customer_id')
            ->first();
        if($chk_balance) {
            $previous_balance  = $chk_balance->previous_balance;
        }**/
        $chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN collection_amount IS NOT NULL THEN collection_amount  ELSE 0 END)  as total_collection_amount, SUM(CASE  WHEN discount IS NOT NULL THEN discount  ELSE 0 END)  as total_discount, SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as total_balance"))
            ->where('customer_id', '=', $id)
            ->where('payment_type', '=', 'credit')
            ->groupBy('customer_id')
            ->first();
        if ($chk_balance) {
            //$previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount + $chk_balance->total_discount);
            $previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount);
        }

        $chk_return = DB::table("sale_returns")
            ->select(DB::raw("SUM(sale_returns.total_payment_amount) as return_payment"))
            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->where('sale_returns.customer_id', '=', $id)
            ->where('sales.payment_type', '!=', 'cash')
            ->groupBy('sale_returns.customer_id')
            ->first();

        if ($chk_return) {
            $return_amount = $chk_return->return_payment;
        }

        $cus_return_amount = 0;
        $chk_cus_return = DB::table("return_invoices")

            ->select(DB::raw("SUM(return_invoices.return_amount)  as cus_return_amount"))
            ->leftjoin('customer_returns', 'customer_returns.id', '=', 'return_invoices.customer_return_id')
            ->where('customer_returns.customer_id', '=', $id)
            ->groupBy('customer_returns.customer_id')
            ->first();
        if ($chk_cus_return) {
            $cus_return_amount = $chk_cus_return->cus_return_amount;
        }

        $previous_balance = ($previous_balance - $return_amount) - $cus_return_amount;

        //get customer's sale advance
        $customer_advance = 0;
        $customer_advance_fx = 0;
        $advance = DB::table("sale_advances")
                    ->select(DB::raw("SUM(CASE  WHEN amount IS NOT NULL AND advance_type='in' THEN amount ELSE 0 END)  as advance_amount_in, SUM(CASE  WHEN amount IS NOT NULL AND advance_type='out' THEN amount ELSE 0 END)  as advance_amount_out,SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='in' THEN amount_fx ELSE 0 END)  as advance_amount_in_fx, SUM(CASE  WHEN amount_fx IS NOT NULL AND advance_type='out' THEN amount_fx ELSE 0 END)  as advance_amount_out_fx,currency_id"))
                    ->where('customer_id','=',$id)
                    ->first();
        if(!empty($advance)) {
            $in = $advance->advance_amount_in == NULL ? 0 : $advance->advance_amount_in;
            $out = $advance->advance_amount_out == NULL ? 0 : $advance->advance_amount_out;
            $customer_advance = $in - $out;

            $in_fx = $advance->advance_amount_in_fx == NULL ? 0 : $advance->advance_amount_in_fx;
            $out_fx = $advance->advance_amount_out_fx == NULL ? 0 : $advance->advance_amount_out_fx;
            $customer_advance_fx = $in_fx - $out_fx;

            if($advance->currency_id != 1) {
                $customer_advance = $customer_advance_fx;
            } 

            $currency_type = $advance->currency_id;
        }
        return compact('previous_balance','customer_advance','currency_type');
    }

    //Daily Sale Report
    public function getDailySaleReport(Request $request)
    {

        $sales =    Sale::with('customer', 'order', 'sale_man', 'customer.township', 'customer.customer_type', 'warehouse', 'branch');


        if ($request->invoice_no != "") {
            $sales->where('invoice_no', $request->invoice_no);
        }

        if ($request->ref_no != "") {
            $sales->where('reference_no', $request->ref_no);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach ($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $sales->whereIn('branch_id', $branch_arr);
        } else {
            //other roles can access only one branch
            if (Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $sales->where('branch_id', $branch);
            }
        }
        if ($request->state_id != "") {
            $sales->whereHas('customer', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }
        if (isset($request->branch_id) && $request->branch_id != "") {
            $sales->where('branch_id', $request->branch_id);
        }

        if ($request->from_date != '' && $request->to_date != '') {
            $sales->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $sales->whereDate('invoice_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $sales->whereDate('invoice_date', '<=', $request->to_date);
        } else {
        }

        if ($request->warehouse_id != "") {
            $sales->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->customer_id != "") {
            $sales->where('customer_id', $request->customer_id);
        }

        if ($request->cus_type != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('customer_type_id', $request->cus_type);
            });
        }

        if ($request->township_id != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('township_id', $request->township_id);
            });
        }

        if ($request->sale_man_id != "") {
            $sales->where('office_sale_man_id', $request->sale_man_id);
        }

        /**if($request->office_sale_man_id != "") {
            $sales->where('office_sale_man_id', $request->office_sale_man_id);
        }**/

        if (Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            $office_sale_man_arr = array();
            foreach (Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach ($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);
                }
            }

            foreach (Auth::user()->office_sale_mans as $osm) {
                array_push($office_sale_man_arr, $osm->id);
            }


            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $access_users)
                ->pluck('id')->toArray();

            // $sales->whereIn('order_id',$orders);
            $sales->where(function ($query) use ($orders, $office_sale_man_arr) {
                $query->whereIn('order_id', $orders)
                    ->orWhereIn('office_sale_man_id', $office_sale_man_arr);
            });
        }

        if (Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach (Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $ls_access_users)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if (Auth::user()->role->id == 4) {
            //for office order user
            //get order user's order id
            $orders = DB::table('orders')
                ->where('created_by', Auth::user()->id)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if ($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if ($request->sort_by != "") {
            if ($request->sort_by == "invoice_no") {
                $sales->orderBy('invoice_no', $order);
            } else {
            }
        } else {
            $sales->orderBy('invoice_date', 'DESC');
        }

        //$data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        $data = $sales->get();

        return response(compact('data'), 200);
    }

    public function exportDailySaleReport(Request $request)
    {
        $export = new DailySaleExport($request);
        $fileName = 'daily_sale_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function exportDailySaleReportPdf(Request $request)
    {

        // $data = ['title' => ''];
        // $pdf = PDF::loadView('invoice_print', $data);
         $sales =    Sale::with('customer','order','sale_man','customer.township','customer.customer_type','warehouse','branch');


        if ($request->invoice_no != "") {
            $sales->where('invoice_no', $request->invoice_no);
        }

        if ($request->ref_no != "") {
            $sales->where('reference_no', $request->ref_no);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach ($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $sales->whereIn('branch_id', $branch_arr);
        } else {
            //other roles can access only one branch
            if (Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $sales->where('branch_id', $branch);
            }
        }

        if (isset($request->branch_id) && $request->branch_id != "") {
            $sales->where('branch_id', $request->branch_id);
        }

        if ($request->from_date != '' && $request->to_date != '') {
            $sales->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $sales->whereDate('invoice_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $sales->whereDate('invoice_date', '<=', $request->to_date);
        } else {
        }

        if ($request->warehouse_id != "") {
            $sales->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->customer_id != "") {
            $sales->where('customer_id', $request->customer_id);
        }

        if ($request->cus_type != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('customer_type_id', $request->cus_type);
            });
        }

        if ($request->township_id != "") {
            $sales->whereHas('customer', function ($query) use ($request) {
                $query->where('township_id', $request->township_id);
            });
        }

        /***if($request->sale_man_id != "") {
            $sales->whereHas('order', function ($query) use ($request) {
                $query->where('sale_man_id', $request->sale_man_id);
            });
        }****/

        if ($request->sale_man_id != "") {
            $sales->where('office_sale_man_id', $request->sale_man_id);
        }

        if (Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            $office_sale_man_arr = array();
            foreach (Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach ($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);
                }
            }

            foreach (Auth::user()->office_sale_mans as $osm) {
                array_push($office_sale_man_arr, $osm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $access_users)
                ->pluck('id')->toArray();

            // $sales->whereIn('order_id',$orders);
            $sales->where(function ($query) use ($orders, $office_sale_man_arr) {
                $query->whereIn('order_id', $orders)
                    ->orWhereIn('office_sale_man_id', $office_sale_man_arr);
            });
        }

        if (Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach (Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $ls_access_users)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if (Auth::user()->role->id == 4) {
            //for office order user
            //get order user's order id
            $orders = DB::table('orders')
                ->where('created_by', Auth::user()->id)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if ($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if ($request->sort_by != "") {
            if ($request->sort_by == "invoice_no") {
                $sales->orderBy('invoice_no', $order);
            } else {
            }
        } else {
            $sales->orderBy('invoice_date', 'DESC');
        }

        //$data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        $data = $sales->get();

        //$data    =  $sales->orderBy('invoice_date', 'DESC')->get();

        $pdf = PDF::loadView('exports.dailySaleRptPdf', compact('data'));
        $pdf->setPaper('a4', 'portrait');
        // $output = $pdf->output();

        /*  return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
        ]);*/

        return $pdf->output();
    }


    //Daily Sale Product Wise Report
    public function getDailySaleProductReportssss(Request $request)
    {
        ini_set('memory_limit', '512M');

        $sales =    Sale::with('products', 'order', 'order.sale_man', 'customer', 'warehouse', 'products.selling_uoms', 'products.uom', 'office_sale_man', 'branch');


        if ($request->invoice_no != "") {
            $sales->where('invoice_no', $request->invoice_no);
        }

        if ($request->from_date != '' && $request->to_date != '') {
            $sales->whereBetween('invoice_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $sales->whereDate('invoice_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $sales->whereDate('invoice_date', '<=', $request->to_date);
        } else {
        }

        if ($request->warehouse_id != "") {
            $sales->where('warehouse_id', $request->warehouse_id);
        }

        if (isset($request->branch_id) && $request->branch_id != "") {
            $sales->where('branch_id', $request->branch_id);
        }

        if ($request->customer_id != "") {
            $sales->where('customer_id', $request->customer_id);
        }

        if ($request->product_name != "") {
            $sales->whereHas('products', function ($query) use ($request) {
                //$query->where('product_name', 'LIKE', "%$request->product_name%");
                $binds = array(strtolower($request->product_name));
                $query->whereRaw('lower(product_name) like lower(?)', ["%{$request->product_name}%"]);
            });
        }

        if ($request->brand_id != "") {
            $sales->whereHas('products', function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            });
        }

        if ($request->sale_man_id != "") {
            $sales->whereHas('order', function ($query) use ($request) {
                $query->where('sale_man_id', $request->sale_man_id);
            });
        }

        if ($request->office_sale_man_id != "") {
            $sales->where('office_sale_man_id', $request->office_sale_man_id);
        }

        if (Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            $office_sale_man_arr = array();
            foreach (Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach ($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);
                }
            }

            foreach (Auth::user()->office_sale_mans as $osm) {
                array_push($office_sale_man_arr, $osm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $access_users)
                ->pluck('id')->toArray();

            // $sales->whereIn('order_id',$orders);
            $sales->where(function ($query) use ($orders, $office_sale_man_arr) {
                $query->whereIn('order_id', $orders)
                    ->orWhereIn('office_sale_man_id', $office_sale_man_arr);
            });
        }

        if (Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach (Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $ls_access_users)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if (Auth::user()->role->id == 4) {
            //for office order user
            //get order user's order id
            $orders = DB::table('orders')
                ->where('created_by', Auth::user()->id)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if ($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if ($request->sort_by != "") {
            if ($request->sort_by == "invoice_no") {
                $sales->orderBy('invoice_no', $order);
            } else {
            }
        } else {
            $sales->orderBy('invoice_date', 'DESC');
        }

        // $data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        $data = $sales->get();

        return response(compact('data'), 200);
    }

    public function getDailySaleProductReport(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 240);

        //$sales =    Sale::with('products','order','order.sale_man','customer','warehouse','products.selling_uoms','products.uom','office_sale_man');
        $sales = DB::table("product_sale")

            ->select(DB::raw("product_sale.*, sales.invoice_date, sales.invoice_no, sales.order_id, sales.branch_id, sales.warehouse_id, sales.customer_id, sales.office_sale_man_id, products.product_code, products.product_name, products.brand_id, brands.brand_name, customers.cus_name, uoms.uom_name, sale_men.sale_man,orders.sale_man_id,branches.branch_name"))

            ->leftjoin('sales', 'sales.id', '=', 'product_sale.sale_id')

            ->leftjoin('orders', 'orders.id', '=', 'sales.order_id')

            ->leftjoin('products', 'products.id', '=', 'product_sale.product_id')

            ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

            ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')

            ->leftjoin('uoms', 'uoms.id', '=', 'product_sale.uom_id')

            ->leftjoin('sale_men', 'sale_men.id', '=', 'sales.office_sale_man_id')

            //->leftjoin('users as u1', 'u1.id', '=', 'sales.office_sale_man_id')

            //->leftjoin('users as u2', 'u2.id', '=', 'orders.sale_man_id')

            ->leftjoin('branches', 'branches.id', '=', 'sales.branch_id');

        if ($request->invoice_no != "") {
            $sales->where('sales.invoice_no', $request->invoice_no);
        }

        if ($request->from_date != '' && $request->to_date != '') {
            $sales->whereBetween('sales.invoice_date', array($request->from_date, $request->to_date));
        } else if ($request->from_date != '') {
            $sales->whereDate('sales.invoice_date', '>=', $request->from_date);
        } else if ($request->to_date != '') {
            $sales->whereDate('sales.invoice_date', '<=', $request->to_date);
        } else {
        }

        if ($request->warehouse_id != "") {
            $sales->where('sales.warehouse_id', $request->warehouse_id);
        }

        if (isset($request->branch_id) && $request->branch_id != "") {
            $sales->where('sales.branch_id', $request->branch_id);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach ($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $sales->whereIn('sales.branch_id', $branch_arr);
        } else {
            //other roles can access only one branch
            if (Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $sales->where('sales.branch_id', $branch);
            }
        }
        if ($request->customer_id != "") {
            $sales->where('sales.customer_id', $request->customer_id);
        }

        if ($request->product_name != "") {
            //$products->where('products.product_name', 'LIKE', "%$request->product_name%");
            $binds = array(strtolower($request->product_name));
            $sales->whereRaw('lower(products.product_name) like lower(?)', ["%{$request->product_name}%"]);
        }

        /*if($request->brand_id != "") {
            $sales->whereHas('products', function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            });
        }*/
        if ($request->brand_id != "") {
            $sales->where('products.brand_id', $request->brand_id);
        } else {
            if (Auth::user()->role->id == 6) {
                //for Country Head User
                $access_brands = array();
                foreach (Auth::user()->brands as $brand) {
                    array_push($access_brands, $brand->id);
                }

                $sales->whereIn('products.brand_id', $access_brands);
            }
        }

        /*if($request->sale_man_id != "") {
            $sales->whereHas('order', function ($query) use ($request) {
                $query->where('sale_man_id', $request->sale_man_id);
            });
        }*/
        /**if($request->sale_man_id != "") {
            $sales->where('orders.sale_man_id', $request->sale_man_id);
        }**/

        if ($request->sale_man_id != "") {
            $sales->where('sales.office_sale_man_id', $request->sale_man_id);
        }

        if ($request->category_id != "") {
            $sales->where('products.category_id', $request->category_id);
        }

        if (Auth::user()->role->id == 6) {
            //for Country Head User
            $access_users = array();
            $office_sale_man_arr = array();
            foreach (Auth::user()->country_head_children as $ls) {
                array_push($access_users, $ls->id);
                $ls_query = User::with('local_supervisor_children')->find($ls->id);
                foreach ($ls_query->local_supervisor_children as $sm) {
                    array_push($access_users, $sm->id);
                }
            }

            foreach (Auth::user()->office_sale_mans as $osm) {
                array_push($office_sale_man_arr, $osm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $access_users)
                ->pluck('id')->toArray();

            // $sales->whereIn('order_id',$orders);
            $sales->where(function ($query) use ($orders, $office_sale_man_arr) {
                $query->whereIn('order_id', $orders)
                    ->orWhereIn('office_sale_man_id', $office_sale_man_arr);
            });
        }

        if (Auth::user()->role->id == 7) {
            //for Local Supervisor user
            $ls_access_users = array();
            array_push($ls_access_users, Auth::user()->id);
            foreach (Auth::user()->local_supervisor_children as $sm) {
                array_push($ls_access_users, $sm->id);
            }

            //get order user's order id
            $orders = DB::table('orders')
                ->whereIn('created_by', $ls_access_users)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if (Auth::user()->role->id == 4) {
            //for office order user
            //get order user's order id
            $orders = DB::table('orders')
                ->where('created_by', Auth::user()->id)
                ->pluck('id')->toArray();

            $sales->whereIn('order_id', $orders);
        }

        if ($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if ($request->sort_by != "") {
            if ($request->sort_by == "invoice_no") {
                $sales->orderBy('sales.invoice_no', $order);
                $sales->orderBy('sales.id', 'DESC');
            } else {
            }
        } else {
            $sales->orderBy('sales.id', 'DESC');
        }

        // $data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        $data = $sales->get();
        $sale_arr = $data->pluck('sale_id')->toArray();
        //$r_count = count(array_keys($sale_arr, (int)394)); dd($r_count);

        /*$access_brands = array();

        if(Auth::user()->role->id == 6) {
            //for Country Head User
            foreach(Auth::user()->brands as $brand) {
                array_push($access_brands, $brand->id);
            }
        }

        $brands = DB::table('brands')
                    ->whereIn('id',$access_brands)
                    ->pluck('id')->toArray();*/

        $total = 0;
        $i = 1;
        $html = '';
        /***foreach($data as $sale) {
                $html .= '<tr><td class="text-right"></td><td>'.$sale->invoice_no.'</td><td>'.$sale->invoice_date.'</td>';
                $html .= '<td class="mm-txt">'.$sale->branch_name.'</td>';
                $html .= '<td class="mm-txt">'.$sale->cus_name.'</td>';
                $html .= '<td class="mm-txt">'.$sale->sale_man.'</td>';
                //$html .= '<td class="mm-txt">'.$sale->office_sale_man.'</td>';
                $html .= '<td>'.$sale->product_code.'</td>';
                $html .= '<td>'.$sale->product_name.'</td>';
                $html .= '<td>'.$sale->product_quantity.'</td>';
                $html .= '<td>'.$sale->uom_name.'</td>';
                if($sale->is_foc == 0) {
                    if(!empty($sale->other_discount)) {
                        $other_discount = ($sale->other_discount/100) * $sale->actual_rate;
                    } else {
                        $other_discount = 0;
                    }
                    $price = $sale->actual_rate - $other_discount;
                    $html .= '<td class="text-right">'.$price.'</td>';
                }
                else {
                    $html .= '<td>FOC</td>';
                }

                $html .='<td class="text-right">'.$sale->total_amount.'</td>';
                $html .= '</tr>';

                if($sale->is_foc == 0){
                    $total = $total + $sale->total_amount;
                }

                $i++;

        }

        $html .= '<tr><td colspan ="11" style="text-align: right;">Total</td><td class="text-right">'.number_format($total).'</td></tr>'; ***/
        $n = 0;
        $sp_total = 0;
        foreach ($data as $k => $sale) {
            $html .= '<tr>';
            if ($k == 0 || $sale->sale_id != $data[$k - 1]->sale_id) {
                $sp_total = 0;
                $n = $n + 1;
                $r_count = count(array_keys($sale_arr, (int)$sale->sale_id));
                $html .= '<td class="text-right" rowspan="' . $r_count . '" style="vertical-align:middle">' . $n . '</td><td rowspan="' . $r_count . '" style="vertical-align:middle">' . $sale->invoice_no . '</td><td rowspan="' . $r_count . '" style="vertical-align:middle">' . $sale->invoice_date . '</td>';
                $html .= '<td class="mm-txt" rowspan="' . $r_count . '" style="min-width:150px;vertical-align:middle;text-align:center;">' . $sale->branch_name . '</td>';
                $html .= '<td class="mm-txt" rowspan="' . $r_count . '" style="min-width:150px;vertical-align:middle; text-align:center;">' . $sale->cus_name . '</td>';
                $html .= '<td class="mm-txt" rowspan="' . $r_count . '" style="min-width:150px;border-right:solid 1px #ccc !important;vertical-align:middle;text-align:center;">' . $sale->sale_man . '</td>';
            }
            //$html .= '<td class="mm-txt">'.$sale->office_sale_man.'</td>';
            $html .= '<td>' . $sale->product_code . '</td>';
            $html .= '<td style="text-align:center;min-width:150px">' . $sale->product_name . '</td>';
            $html .= '<td>' . $sale->product_quantity . '</td>';
            $html .= '<td>' . $sale->uom_name . '</td>';
            if ($sale->is_foc == 0) {
                if (!empty($sale->other_discount)) {
                    $other_discount = ($sale->other_discount / 100) * $sale->actual_rate;
                } else {
                    $other_discount = 0;
                }
                $price = $sale->actual_rate - $other_discount;
                $html .= '<td class="text-right">' . $price . '</td>';
            } else {
                $html .= '<td class="text-right">FOC</td>';
            }

            $html .= '<td class="text-right">' . $sale->total_amount . '</td>';
            $html .= '</tr>';

            if ($sale->is_foc == 0) {
                $total = $total + $sale->total_amount;
                $sp_total = $sp_total + $sale->total_amount;
            }

            $i++;

            if (count($data) == 1 || $k == count($data) - 1 || (isset($data[$k + 1]) && $sale->sale_id != $data[$k + 1]->sale_id)) {
                $html .= '<tr><td colspan ="11" style="text-align: right;">Total</td><td class="text-right">' . number_format($sp_total) . '</td></tr>';
            }
        }


        $html .= '<tr><td colspan ="11" style="text-align: right;">Sub-Total</td><td class="text-right">' . number_format($total) . '</td></tr>';

        return response(compact('html'), 200);
    }

    public function exportDailySaleProductReport(Request $request)
    {
        $export = new DailySaleProductExport($request);
        $fileName = 'daily_sale_product_wise_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function generateInvoicePDF($sale_id)
    {

        // $data = ['title' => ''];
        //$pdf = PDF::loadView('invoice_print', $data);

        $sale = Sale::with('products', 'sale_man', 'warehouse', 'customer', 'products.uom', 'products.selling_uoms')->find($sale_id);

        //get customer previous balance
        /**$previous_balance = 0;
        $chk_balance = DB::table("sales")

                    ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount - return_amount ELSE 0 END)  as previous_balance"))
                    ->where('customer_id','=',$sale->customer_id)
                    ->where('id','!=',$sale_id)
                    ->groupBy('customer_id')
                    ->first();
        if($chk_balance) {
             $previous_balance = $chk_balance->previous_balance;
        }**/

        $previous_balance = 0;
        $return_amount = 0;
        /**$chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount ELSE 0 END)  as previous_balance"))
            ->where('customer_id','=',$sale->customer_id)
            ->where('id', '!=', $sale_id)
            ->groupBy('customer_id')
            ->first();
        if($chk_balance) {
            $previous_balance  = $chk_balance->previous_balance;
        }**/
        $chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN collection_amount IS NOT NULL THEN collection_amount  ELSE 0 END)  as total_collection_amount, SUM(CASE  WHEN discount IS NOT NULL THEN discount  ELSE 0 END)  as total_discount, SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as total_balance"))
            ->where('customer_id', '=', $sale->customer_id)
            ->where('id', '!=', $sale_id)
            ->where('payment_type', '=', 'credit')
            ->groupBy('customer_id')
            ->first();
        if ($chk_balance) {
            //$previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount + $chk_balance->total_discount);
            $previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount);
        }

        $chk_return = DB::table("sale_returns")

            ->select(DB::raw("SUM(sale_returns.total_payment_amount)  as return_payment"))
            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->where('sale_returns.customer_id', '=', $sale->customer_id)
            ->where('sales.payment_type', '!=', 'cash')
            ->where('sales.id', '!=', $sale_id)
            ->groupBy('sale_returns.customer_id')
            ->first();
        if ($chk_return) {
            $return_amount = $chk_return->return_payment;
        }

        $cus_return_amount = 0;
        $chk_cus_return = DB::table("return_invoices")

            ->select(DB::raw("SUM(return_invoices.return_amount)  as cus_return_amount"))
            ->leftjoin('customer_returns', 'customer_returns.id', '=', 'return_invoices.customer_return_id')
            ->where('customer_returns.customer_id', '=', $sale->customer_id)
            ->where('return_invoices.sale_id', '!=', $sale_id)
            ->groupBy('customer_returns.customer_id')
            ->first();
        if ($chk_cus_return) {
            $cus_return_amount = $chk_cus_return->cus_return_amount;
        }

        $previous_balance = ($previous_balance - $return_amount) - $cus_return_amount;

        // $previous_balance = $previous_balance - $return_amount;

        return view('exports.invoice_print', compact('sale', 'previous_balance'));

        $pdf = PDF::loadView('exports.invoice_print', compact('sale', 'previous_balance'));
        // $pdf->setPaper('a5' , 'portrait');
        $pdf->setPaper(array(0, 0, 709, 1042));
        $output = $pdf->output();

        return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
           'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
        ]);

        //return $pdf->stream();

    }

    public function generateInvoiceCurrencyPDF($sale_id)
    {

       // $data = ['title' => ''];
       //$pdf = PDF::loadView('invoice_print', $data);

        $sale = Sale::with('currency','products','sale_man','warehouse','customer','products.uom','products.selling_uoms')->find($sale_id);

        //get customer previous balance
        /**$previous_balance = 0;
        $chk_balance = DB::table("sales")

                    ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount - return_amount ELSE 0 END)  as previous_balance"))
                    ->where('customer_id','=',$sale->customer_id)
                    ->where('id','!=',$sale_id)
                    ->groupBy('customer_id')
                    ->first();
        if($chk_balance) {
             $previous_balance = $chk_balance->previous_balance;
        }**/

        $previous_balance = 0;
        $return_amount = 0;
        /**$chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount ELSE 0 END)  as previous_balance"))
            ->where('customer_id','=',$sale->customer_id)
            ->where('id', '!=', $sale_id)
            ->groupBy('customer_id')
            ->first();
        if($chk_balance) {
            $previous_balance  = $chk_balance->previous_balance;
        }**/
        $chk_balance = DB::table("sales")

                    ->select(DB::raw("SUM(CASE  WHEN collection_amount IS NOT NULL THEN collection_amount  ELSE 0 END)  as total_collection_amount, SUM(CASE  WHEN discount IS NOT NULL THEN discount  ELSE 0 END)  as total_discount, SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as total_balance"))
                    ->where('customer_id','=',$sale->customer_id)
                    ->where('id', '!=', $sale_id)
                    ->where('payment_type', '=', 'credit')
                    ->groupBy('customer_id')
                    ->first();
        if($chk_balance) {
            //$previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount + $chk_balance->total_discount);
            $previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount);
        }

        $chk_return = DB::table("sale_returns")

            ->select(DB::raw("SUM(sale_returns.total_payment_amount)  as return_payment"))
            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->where('sale_returns.customer_id','=',$sale->customer_id)
            ->where('sales.payment_type','!=','cash')
            ->where('sales.id','!=', $sale_id)
            ->groupBy('sale_returns.customer_id')
            ->first();
        if($chk_return) {
            $return_amount = $chk_return->return_payment;
        }

        $cus_return_amount = 0;
        $chk_cus_return = DB::table("return_invoices")

            ->select(DB::raw("SUM(return_invoices.return_amount)  as cus_return_amount"))
            ->leftjoin('customer_returns', 'customer_returns.id', '=', 'return_invoices.customer_return_id')
            ->where('customer_returns.customer_id','=',$sale->customer_id)
            ->where('return_invoices.sale_id','!=',$sale_id)
            ->groupBy('customer_returns.customer_id')
            ->first();
        if($chk_cus_return) {
            $cus_return_amount = $chk_cus_return->cus_return_amount;
        }

        $previous_balance = ($previous_balance - $return_amount) - $cus_return_amount;

       // $previous_balance = $previous_balance - $return_amount;
        $currency = $sale->currency->sign;

        return view('exports.invoice_currency_print', compact('sale','previous_balance','currency'));

        $pdf = PDF::loadView('exports.invoice_currency_print', compact('sale','previous_balance','currency'));
       // $pdf->setPaper('a5' , 'portrait');
        $pdf->setPaper(array(0,0,709,1042));
        $output = $pdf->output();

        return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
           'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
        ]);

        //return $pdf->stream();

    }

    public function getCreditSaleByCustomer($cus_id, Request $request)
    {
        /** if($request->branch_id && $request->branch_id != '') {
            $data = Sale::orderBy('invoice_date', 'ASC')
                    ->where('customer_id',$cus_id)
                    ->where('payment_type', 'credit')
                    ->where('branch_id', $request->branch_id)
                    ->whereRaw('(total_amount-(pay_amount + collection_amount)) > 0')
                    ->get();
        } else { $data = ''; }**/
        if (isset($request->currency_id) && $request->currency_id != "") {
            if ($request->currency_id == 1) {
                $data = Sale::with('collections')->orderBy('invoice_date', 'ASC')
                    ->select('*', DB::raw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) as sale_balance, IFNULL(tax_amount,0) as tax_amt,IFNULL(tax_amount,0) as tax_amt_mmk'))
                    ->where('customer_id', $cus_id)
                    ->where('currency_id', $request->currency_id)
                    ->where('payment_type', 'credit')
                    ->where('branch_id', $request->branch_id)
                    ->whereRaw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0')
                    ->get();
                foreach ($data as $key => $val) {
                    $gain_amt = 0;
                    $loss_amt = 0;
                    if (!empty($val->collections)) {
                        foreach ($val->collections as $c) {
                            $gain_amt += $c->pivot->gain_amount;
                            $loss_amt += abs($c->pivot->loss_amount);
                        }
                    }

                    $data[$key]->gain_amount = $gain_amt;
                    $data[$key]->loss_amount = $loss_amt;
                }
            } else {
                $data = Sale::with('collections')->orderBy('invoice_date', 'ASC')
                    ->select('*', DB::raw('((total_amount_fx + tax_amount_fx)-(IFNULL(cash_discount_fx,0) + pay_amount_fx + collection_amount_fx)) as sale_balance, IFNULL(tax_amount_fx,0) as tax_amt,IFNULL(tax_amount,0) as tax_amt_mmk'))
                    ->where('customer_id', $cus_id)
                    ->where('currency_id', $request->currency_id)
                    ->where('payment_type', 'credit')
                    ->where('branch_id', $request->branch_id)
                    ->whereRaw('((total_amount_fx + tax_amount_fx)-(IFNULL(cash_discount_fx,0) + pay_amount_fx + collection_amount_fx)) > 0')
                    ->get();
                foreach ($data as $key => $val) {
                    $gain_amt = 0;
                    $loss_amt = 0;
                    if (!empty($val->collections)) {
                        foreach ($val->collections as $c) {
                            $gain_amt += $c->pivot->gain_amount;
                            $loss_amt += abs($c->pivot->loss_amount);
                        }
                    }

                    $data[$key]->gain_amount = $gain_amt;
                    $data[$key]->loss_amount = $loss_amt;
                }
            }
        } else if (isset($request->sale_return)) {
            $data = Sale::orderBy('invoice_date', 'ASC')
                ->select('*', DB::raw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) as sale_balance, IFNULL(tax_amount,0) as tax_amt'))
                ->where('customer_id', $cus_id);
            if (isset($request->branch_id) && $request->branch_id != '') {
                $data->where('branch_id', $request->branch_id);
            }
            $data = $data->where('currency_id', 1)
                ->where('payment_type', 'credit')
                ->whereRaw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0')
                ->get();
        } else {
            $data = Sale::orderBy('invoice_date', 'ASC')
                ->select('*', DB::raw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) as sale_balance, IFNULL(tax_amount,0) as tax_amt,IFNULL(tax_amount,0) as tax_amt_mmk'))
                ->where('customer_id', $cus_id);
            if (isset($request->branch_id) && $request->branch_id != '') {
                $data->where('branch_id', $request->branch_id);
            }
            $data = $data->where('payment_type', 'credit')
                ->whereRaw('((total_amount + IFNULL(tax_amount,0))-(IFNULL(cash_discount,0) + pay_amount + collection_amount + return_amount + customer_return_amount)) > 0')
                ->get();
        }
        // dd($data);
        return response(compact('data'), 200);
    }

    public function getAllSaleByCustomer($cus_id, Request $request)
    {
        $data = Sale::orderBy('invoice_date', 'DESC')
            ->where('is_opening', '!=', 1)
            ->where('customer_id', $cus_id)
            ->where('currency_id', 1)
            ->get();
        // dd($data);

        /**$previous_balance = 0;
        $chk_balance = DB::table("sales")

                    ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount - return_amount ELSE 0 END)  as previous_balance"))
                    ->where('customer_id','=',$cus_id)
                    ->groupBy('customer_id')
                    ->first();
        if($chk_balance) {
             $previous_balance = $chk_balance->previous_balance;
        }***/

        $previous_balance = 0;
        $return_amount = 0;
        /**$chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount ELSE 0 END)  as previous_balance"))
            ->where('customer_id','=',$cus_id)
            ->groupBy('customer_id')
            ->first();
        if($chk_balance) {
            $previous_balance  = $chk_balance->previous_balance;
        }**/
        $previous_balance = 0;
        $chk_balance = DB::table("sales")

            ->select(DB::raw("SUM(CASE  WHEN collection_amount IS NOT NULL THEN collection_amount  ELSE 0 END)  as total_collection_amount, SUM(CASE  WHEN discount IS NOT NULL THEN discount  ELSE 0 END)  as total_discount, SUM(CASE  WHEN balance_amount IS NOT NULL THEN balance_amount  ELSE 0 END)  as total_balance"))
            ->where('customer_id', '=', $cus_id)
            ->where('payment_type', '=', 'credit')
            ->groupBy('customer_id')
            ->first();
        if ($chk_balance) {
            //$previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount + $chk_balance->total_discount);
            $previous_balance = $chk_balance->total_balance - ($chk_balance->total_collection_amount);
        }

        $chk_return = DB::table("sale_returns")

            ->select(DB::raw("SUM(sale_returns.total_payment_amount)  as return_payment"))
            ->leftjoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->where('sale_returns.customer_id', '=', $cus_id)
            ->where('sales.payment_type', '!=', 'cash')
            ->groupBy('sale_returns.customer_id')
            ->first();
        if ($chk_return) {
            $return_amount = $chk_return->return_payment;
        }


        $cus_return_amount = 0;
        $chk_cus_return = DB::table("return_invoices")

            ->select(DB::raw("SUM(return_invoices.return_amount)  as cus_return_amount"))
            ->leftjoin('customer_returns', 'customer_returns.id', '=', 'return_invoices.customer_return_id')
            ->where('customer_returns.customer_id', '=', $cus_id)
            ->groupBy('customer_returns.customer_id')
            ->first();
        if ($chk_cus_return) {
            $cus_return_amount = $chk_cus_return->cus_return_amount;
        }

        $previous_balance = ($previous_balance - $return_amount) - $cus_return_amount;

        return response(compact('data', 'previous_balance'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        if ($sale->order_id != NULL) {

            foreach ($sale->products as $product) {
                //update quantiy in product_order table
                $accepted_qty = $product->pivot->product_quantity;
                $po_result = DB::table('product_order')
                    ->select('accepted_quantity')
                    ->where('id', $product->pivot->order_product_pivot_id)
                    ->first();
                $accepted_qty = $po_result->accepted_quantity -  $accepted_qty;

                DB::table('product_order')
                    ->where('id', $product->pivot->order_product_pivot_id)
                    ->update(array('accepted_quantity' => $accepted_qty));

                //Check order status is done or not
                $chk_order = DB::table("product_order")

                    ->select(DB::raw("SUM(CASE  WHEN product_quantity IS NOT NULL THEN product_quantity  ELSE 0 END)  as product_qty, SUM(CASE  WHEN accepted_quantity IS NOT NULL THEN accepted_quantity  ELSE 0 END)  as accepted_qty"))
                    ->where('order_id', '=', $sale->order_id)
                    ->groupBy('order_id')
                    ->first();
                //update order status
                $order = Order::find($sale->order_id);
                if ($chk_order->product_qty == $chk_order->accepted_qty) {
                    $status = "Done";
                } else {
                    $status = "Draft";
                }
                //change status in order table;
                $order->order_status = $status;
                $order->save();
            }
        }

        $sale->products()->detach();

        //for foreign currency
        //Reset advance and remove in purchase_advance_links
        $old_advance = DB::table("sale_advance_links")

                    ->select(DB::raw("sale_advance_links.*, sale_advances.balance,sale_advances.id"))
                    ->leftjoin('sale_advances', 'sale_advances.id', '=', 'sale_advance_links.advance_id')
                    ->where('sale_advance_links.sale_id','=',$sale->id)
                    ->get();
        if(!empty($old_advance)) {
            foreach($old_advance as $a) {
                $balance = $a->balance + $a->amount_fx;
                DB::table('sale_advances')
                    ->where('id', $a->id)
                    ->update(array('balance' => $balance));   
            }
        }

        $sale->advances()->detach();

        AccountTransition::where('sale_id',$id)
                ->where(function($query) {
                        $query->where('status','sale')
                              ->orWhere('status','gain')
                              ->orWhere('status','loss');
                    })
                ->delete();

        /**AccountTransition::where([
            ['purchase_id',$id],
            ['status','purchase']
        ])->delete();**/

        DB::table('product_transitions')
            ->where('transition_sale_id', $id)
            ->delete();
        $sale->delete();
        if ($sale->payment_type == 'cash') {
            $sub_account_id = config('global.cash_sale');
        } else {
            $sub_account_id = config('global.sale_advance');
        }
        DB::table('sale_advances')
            ->where('sale_id', $id)
            ->delete();
        return response(['message' => 'delete successful']);
    }
    //update delivery approval in sales table
    public function deliveryApproval($sale_id, $status)
    {
        $sale = Sale::find($sale_id);
        if ($status == 'approve') {
            $sale->delivery_approve = 1;
        } else {
            $sale->delivery_approve = 0;
        }
        $sale->save();

        return response(['message' => 'success']);
    }

    //get revised sales for sale edit comparison report
    public function getReviseSales(Request $request)
    {
        $login_year = Session::get('loginYear');
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }

        //get specific order invoics
        $data = Sale::with('approval', 'approval.products', 'products', 'products.uom', 'approval.products.uom', 'customer', 'products.selling_uoms', 'approval.products.selling_uoms', 'branch', 'update_user')
            ->where('is_revise', 1);

        if ($request->invoice_no != "") {
            $data->where('invoice_no', $request->invoice_no);
        }

        if ($request->inv_from_date != '' && $request->inv_to_date != '') {
            $data->whereBetween('invoice_date', array($request->inv_from_date, $request->inv_to_date));
        } else if ($request->inv_from_date != '') {
            $data->whereDate('invoice_date', '>=', $request->inv_from_date);
        } else if ($request->inv_to_date != '') {
            $data->whereDate('invoice_date', '<=', $request->inv_to_date);
        } else {
        }

        if ($request->app_from_date != '' && $request->app_to_date != '') {
            // $data->whereBetween('invoice_date', array($request->inv_from_date, $request->inv_to_date));
            $data->whereHas('approval', function ($query) use ($request) {
                //$query->whereBetween('created_at', array($request->app_from_date, $request->app_to_date));
                $query->whereDate('created_at', '>=', $request->app_from_date);
                $query->whereDate('created_at', '<=', $request->app_to_date);
            });
        } else if ($request->app_from_date != '') {
            // $data->whereDate('invoice_date', '>=', $request->inv_from_date);
            $data->whereHas('approval', function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->app_from_date);
            });
        } else if ($request->app_to_date != '') {
            // $data->whereDate('invoice_date', '<=', $request->inv_to_date);
            $data->whereHas('approval', function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->app_to_date);
            });
        } else {
        }

        if ($request->customer_id != "") {
            $data->where('customer_id', $request->customer_id);
        }

        if ($request->reference_no != "") {
            $data->where('reference_no', $request->reference_no);
        }

        if ($request->warehouse_id != "") {
            $data->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->branch_id != "") {
            $data->where('branch_id', $request->branch_id);
        }

        //for Country Head and Admin roles (can access multiple branch)
        if (Auth::user()->role->id == 6 || Auth::user()->role->id == 2) {
            $branches = Auth::user()->branches;
            $branch_arr = array();
            foreach ($branches as $branch) {
                array_push($branch_arr, $branch->id);
            }
            $data->whereIn('branch_id', $branch_arr);
        } else {
            //other roles can access only one branch
            if (Auth::user()->role->id != 1) { //system can access all branches
                $branch = Auth::user()->branch_id;
                $data->where('branch_id', $branch);
            }
        }
        if ($request->approval_no != '') {
            // $data->whereBetween('invoice_date', array($request->inv_from_date, $request->inv_to_date));
            $data->whereHas('approval', function ($query) use ($request) {
                $query->where('approval_no', $request->approval_no);
            });
        }

        $data = $data->orderBy('id', 'DESC')->paginate($limit);

        return response(compact('data'), 200);
    }

    public function getSaleAnalyst(Request $request)
    {
        $where = " product_transitions.transition_sale_id IS NOT NULL";
        if($request->from_date != '' && $request->to_date != '')
        {
            $where .= " AND product_transitions.transition_date >= '".$request->from_date."' AND product_transitions.transition_date <= '".$request->to_date."'";
            //$data->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $where .= " AND product_transitions.transition_date >= '".$request->from_date."'";
            //$data->whereDate('product_transitions.transition_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $where .= " AND product_transitions.transition_date <= '".$request->to_date."'";
            //$data->whereDate('product_transitions.transition_date', '<=', $request->to_date);
        } else {}

        $data = DB::table("products")
        
                ->select(DB::raw("products.id, products.product_name,products.product_code, brands.brand_name, categories.category_name, uoms.uom_name, pt.total_amount, pt.total_quantity"))

                //->leftjoin('product_transitions', 'product_transitions.product_id', '=', 'products.id')

                //->leftjoin('product_sale', 'product_sale.id', '=', 'product_transitions.transition_product_pivot_id')
                ->leftjoin(DB::raw("(SELECT product_transitions.product_id, SUM(CASE  WHEN product_sale.total_amount IS NULL THEN 0  ELSE product_sale.total_amount END)  as total_amount, SUM(CASE  WHEN product_transitions.product_quantity IS NULL THEN 0  ELSE product_transitions.product_quantity END)  as total_quantity FROM product_transitions LEFT JOIN product_sale ON product_sale.id = product_transitions.transition_product_pivot_id Where ".$where." GROUP BY product_transitions.product_id

                            ) as pt"),function($join){

                            $join->on("pt.product_id","=","products.id");

                        }) 

               
                ->leftjoin('brands', 'brands.id', '=', 'products.brand_id')

                ->leftjoin('categories', 'categories.id', '=', 'products.category_id')

                ->leftjoin('uoms', 'uoms.id', '=', 'products.uom_id');

               // ->where("product_transitions.warehouse_id",Auth::user()->warehouse_id)

               // ->where('products.is_active',1)
        /***if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('product_transitions.transition_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('product_transitions.transition_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $data->whereDate('product_transitions.transition_date', '<=', $request->to_date);
        } else {}***/

        if($request->brand_id != "") {
            $data->where('products.brand_id', $request->brand_id);
        }

        if($request->category_id != "") {
            $data->where('products.category_id', $request->category_id);
        }

        if($request->product_code != '') {
            $data->where('products.product_code', 'LIKE', '%'.$request->product_code.'%');
        }

        if($request->product_name != '') {
            $data->where('products.product_name', 'LIKE', '%'.$request->product_name.'%');
        }

        $data    =  $data->orderBy('total_quantity', 'DESC')->groupBy("products.id")->get();

        $html = ''; $i=0;$total=0;$total_qty=0;
        foreach($data as $r) {
            $i++;
            $html .= '<tr>';
            $html .= '<td>'.$i.'</td>';
            $html .= '<td class="text-center">'.$r->brand_name.'</td>';
            $html .= '<td class="text-center">'.$r->category_name.'</td>';
            $html .= '<td class="text-center">'.$r->product_code.'</td>';
            $html .= '<td class="text-center">'.$r->product_name.'</td>';

            $html .= '<td class="text-right mm-txt">'.(int)$r->total_quantity.'</td>';
            $html .= '<td class="text-center mm-txt">'.$r->uom_name.'</td>';
            $html .= '<td class="text-right">'.number_format($r->total_amount).'</td>';
            $total += $r->total_amount;
            $total_qty += $r->total_quantity;
            $html .= '</tr>';

        } 
        if(!empty($data)) {
            $html .= '<tr><td colspan="5" class="text-right">Total</td><td class="text-right">'.number_format($total_qty).'</td><td></td><td class="text-right">'.number_format($total).'</td></tr>';

        }

        //return $html;
        return array($data,$html);
    }

    public function getSaleAnalystReport(Request $request)
    {
        list($data,$html) = $this->getSaleAnalyst($request);
        return response(compact('html'), 200);
    }

    public function exportSaleAnalystReport(Request $request)
    {
        list($data,$html) = $this->getSaleAnalyst($request);
        $export = new SaleAnalystExport($data,$request);
        $fileName = 'sale_analyst_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }

    public function exportSaleAnalystReportPdf(Request $request)
    {


       //$data    =  $sales->orderBy('invoice_date', 'DESC')->get();
        list($data,$html) = $this->getSaleAnalyst($request);

        //$data    =  $sales->orderBy('invoice_date', 'DESC')->get();

        $pdf = PDF::loadView('exports.sale_analyst_pdf', compact('data'));
        $pdf->setPaper('a4' , 'portrait');
       // $output = $pdf->output();

      /*  return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="sale_invoice.pdf"',
        ]);*/

        return $pdf->output();

    }

}
