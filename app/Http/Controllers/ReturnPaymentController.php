<?php

namespace App\Http\Controllers;

use Session;
use App\Sale;
use App\User;
use App\Product;
use App\Customer;
use App\SaleReturn;
use App\ReturnPayment;
use Carbon\Carbon;
use App\AccountTransition;
use App\ProductTransition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use DB;
use App\Http\Traits\Report\GetReport;
use App\Http\Traits\AccountReport\Ledger;
use App\Exports\SaleReturnPaymentExport;

class ReturnPaymentController extends Controller
{
	use GetReport;
    use Ledger;

    public function index(Request $request)
    {
        $limit = 15;
        if ($request->has('limit')) {
            $limit = $request->limit;
        } 

        $login_year = Session::get('loginYear');

        $data = ReturnPayment::with('sale_return','customer','branch');

        if($request->from_date != '' && $request->to_date != '')
        {            
            $data->whereBetween('return_payment_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('return_payment_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
             $data->whereDate('return_payment_date', '<=', $request->to_date);
        } else {
            $data->whereBetween('return_payment_date', array($login_year.'-01-01', $login_year.'-12-31'));
        }

        if(isset($request->payment_no) && $request->payment_no != "") {
            $data->where('return_payment_no','LIKE','%'.$request->payment_no.'%');
        }

        if(isset($request->return_no) && $request->return_no != "") {
            $data->whereHas('sale_return',function($q)use($request){
                $q->where('return_no', 'LIKE', '%'.$request->return_no.'%');
            });
        }

        if(isset($request->customer_id) && $request->customer_id != "") {
            $data->where('customer_id', $request->customer_id);
        }

        $data = $data->orderBy('id', 'DESC')->paginate($limit); 
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
        $payment = ReturnPayment::with('sale_return','customer','branch')->find($id);
        return compact('payment');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	DB::beginTransaction();
        try {
            $obj = new ReturnPayment;
            //auto generate invoice no;
            $max_id = ReturnPayment::max('id');
            if($max_id) {
                $max_id = $max_id + 1;
            }else {
                $max_id = 1;
            }
            $return_payment_no = "RP".str_pad($max_id,5,"0",STR_PAD_LEFT);
            $obj->return_payment_no = $return_payment_no;
            $obj->return_payment_date = $request->payment_date;
            $obj->branch_id = $request->branch_id;
            $obj->customer_id = $request->customer_id;
            $obj->return_payment_type = $request->payment_type;
            $obj->return_id = $request->return_id;
            $obj->total_amount = $request->return_amount;
            $obj->created_by = Auth::user()->id;
            $obj->updated_by = Auth::user()->id;
            $obj->save();

            $return_payment_id = $obj->id;

            //update total return payment amount in sale return table
            $sr_obj = SaleReturn::find($request->return_id);
            $sr_obj->total_payment_amount = $sr_obj->total_payment_amount + $request->return_amount;
            $sr_obj->save();

            //update in sale table
            if($sr_obj->return_method=="with invoice") {
                $sale=Sale::find($sr_obj->sale_id);
                if($sale->payment_type == 'credit') {
                    $sale->return_amount = $request->return_amount + $sale->return_amount;
                } else {
                    $sale->cash_return_amount = $request->return_amount + $sale->cash_return_amount;
                }
                $sale->save();
            } else {
                
            }

            //store in cashbook and ledger
            if($sr_obj->return_method == "with invoice" && $sale->payment_type == 'cash') {
            	//for cash invoice
            	$description=$obj->return_payment_no.", Date ".$request->payment_date." by " .$obj->customer->cus_name;
            	AccountTransition::create([
	                'sub_account_id' => 78,
	                'transition_date' => $request->payment_date,
	                'customer_id' => $request->customer_id,
	                //'sale_id' => $sale->id,
	                'return_id' => $request->return_id,
	                'return_payment_id' => $return_payment_id,
	                'status'=>'return_payment',
	                'vochur_no'=>$obj->return_payment_no,
	                'description'=>$description,
	                'is_cashbook' => 1,
	                'credit' => $request->return_amount,
	                'created_by' => Auth::user()->id,
	                'updated_by' => Auth::user()->id,
	            ]);

	            AccountTransition::create([
	                'sub_account_id' => 78,
	                'transition_date' => $request->payment_date,
	                'customer_id' => $request->customer_id,
	                //'sale_id' => $sale->id,
	                'return_id' => $request->return_id,
	                'return_payment_id' => $return_payment_id,
	                'status'=>'return_payment',
	                'vochur_no'=>$obj->return_payment_no,
	                'description'=>'',
	                'is_cashbook' => 0,
	                'credit' => $request->return_amount,
	                'created_by' => Auth::user()->id,
	                'updated_by' => Auth::user()->id,
	            ]);
            } else {
            	//for credit invoice and without invoice
                if($sr_obj->return_method == "with invoice") {
                	AccountTransition::create([
    	                'sub_account_id' => 78,
    	                'transition_date' => $request->payment_date,
    	                'customer_id' => $request->customer_id,
    	                //'sale_id' => $sale->id,
    	                'return_id' => $request->return_id,
    	                'return_payment_id' => $return_payment_id,
    	                'status'=>'return_payment',
    	                'vochur_no'=>$obj->return_payment_no,
    	                'description'=>'',
    	                'is_cashbook' => 0,
    	                'debit' => $request->return_amount,
    	                'created_by' => Auth::user()->id,
    	                'updated_by' => Auth::user()->id,
    	            ]);
                }
            }

        	$status = "success";
            DB::commit();
            return compact('status','return_payment_id');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
            
        }
      
    }

    public function update(Request $request, $id)
    {
    	DB::beginTransaction();
        try {
            $obj = ReturnPayment::find($id);

            $old_return_amount = $obj->total_amount;

            $obj->return_payment_date = $request->payment_date;
            $obj->branch_id = $request->branch_id;
            //$obj->customer_id = $request->customer_id;
            $obj->return_payment_type = $request->payment_type;
            //$obj->return_id = $request->return_id;
            $obj->total_amount = $request->return_amount;
            $obj->updated_by = Auth::user()->id;
            $obj->save();
           // AccountTransition::where('return_id',$id)->delete();
            //update total return payment amount in sale return table
            $sr_obj = SaleReturn::find($request->return_id);
            $sr_obj->total_payment_amount = ($sr_obj->total_payment_amount - $old_return_amount) + $request->return_amount;
            $sr_obj->save();

            //update in sale table
            if($sr_obj->return_method=="with invoice") {
                $sale=Sale::find($sr_obj->sale_id);
                if($sale->payment_type == 'credit') {
                    $sale->return_amount = $request->return_amount + ($sale->return_amount - $old_return_amount);
                } else {
                    $sale->cash_return_amount = $request->return_amount + ($sale->cash_return_amount - $old_return_amount);
                }
                $sale->save();
            } else {
                
            }

            //update in cashbook and ledger
            if($sr_obj->return_method == "with invoice" && $sale->payment_type == 'cash') {
            	//for cash invoice
            	$description=$obj->return_payment_no.", Date ".$request->payment_date." by " .$obj->customer->cus_name;
            	DB::table('account_transitions')
                            ->where('return_payment_id', $id)
                            ->where('is_cashbook', 1)
                            ->update(array('transition_date' => $request->payment_date,'description'=>$description,'credit' => $request->return_amount,'updated_by' => Auth::user()->id));

                DB::table('account_transitions')
                            ->where('return_payment_id', $id)
                            ->where('is_cashbook', 0)
                            ->update(array('transition_date' => $request->payment_date,'credit' => $request->return_amount,'updated_by' => Auth::user()->id));
            } else {
            	//for credit invoice and without invoice
            	DB::table('account_transitions')
                            ->where('return_payment_id', $id)
                            ->where('is_cashbook', 0)
                            ->update(array('transition_date' => $request->payment_date,'debit' => $request->return_amount,'updated_by' => Auth::user()->id));
            }

        	$status = "success";
            DB::commit();
            return compact('status','id');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            $status = "fail";
            return compact('status');
            throw $e;
            
        }
    	
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = ReturnPayment::find($id);
        $delete_return_amount = $payment->total_amount;
	    //update total return payment amount in sale return table
	    $sr_obj = SaleReturn::find($payment->return_id);
	    $sr_obj->total_payment_amount = $sr_obj->total_payment_amount - $delete_return_amount;
	    $sr_obj->save();

        if($sr_obj->return_method=="with invoice") {
            $sale=Sale::find($sr_obj->sale_id);
            if($sale->payment_type == 'credit') {
                $sale->return_amount = $sale->return_amount - $delete_return_amount;
            } else {
                $sale->cash_return_amount = $sale->cash_return_amount - $delete_return_amount;
            }
            $sale->save();
        }

	    AccountTransition::where('return_payment_id',$id)->delete();

        $payment->delete();

        return response(['message' => 'delete successful']);
    }

    public function getSaleReturnPayment(Request $request)
    {

        $data = ReturnPayment::with('sale_return','customer');
        if($request->return_date != '') {
            $data->whereHas('sale_return',function($q)use($request){
                    $q->whereDate('return_date', $request->return_date);
                });
        }
        if($request->return_no != '') {
            $data->whereHas('sale_return',function($q)use($request){
                    $q->where('return_no', 'LIKE', '%'.$request->return_no.'%');
                });
        }
        if($request->payment_no != '') {
            $data->where('return_payment_no', 'LIKE', '%'.$request->payment_no.'%');
        }
        if($request->customer_id != '') {
            $data->where('customer_id', $request->customer_id);
        }
        if($request->from_date != '' && $request->to_date != '')
        {
            $data->whereBetween('return_payment_date', array($request->from_date, $request->to_date));
        } else if($request->from_date != '') {
            $data->whereDate('return_payment_date', '>=', $request->from_date);

        }else if($request->to_date != '') {
            $data->whereDate('return_payment_date', '<=', $request->to_date);
        } else {}

        $data    =  $data->orderBy('return_payment_date', 'DESC')->get();

        $html = ''; $i=0;$total=0;
        foreach($data as $r) {
            $i++;
            $html .= '<tr>';
            $html .= '<td>'.$i.'</td>';
            $html .= '<td class="text-center">'.$r->sale_return->return_no.'</td>';
            $html .= '<td class="text-center">'.$r->sale_return->return_date.'</td>';

            $html .= '<td class="text-center">'.$r->return_payment_no.'</td>';
            $html .= '<td class="text-center">'.$r->return_payment_date.'</td>';

            $html .= '<td class="text-center mm-txt">'.$r->customer->cus_name.'</td>';
            $html .= '<td class="text-right">'.number_format($r->total_amount).'</td>';
            $total += $r->total_amount;
            $html .= '</tr>';

        } 
        if(!empty($data)) {
            $html .= '<tr><td colspan="6" class="text-right">Return Total</td><td class="text-right">'.number_format($total).'</td></tr>';

        }

        //return $html;
        return array($data,$html);
    }

    public function getSaleReturnPaymentReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturnPayment($request);
        return response(compact('html'), 200);
    }

    public function exportSaleReturnPaymentReport(Request $request)
    {
        list($data,$html) = $this->getSaleReturnPayment($request);
        $export = new SaleReturnPaymentExport($data,$request);
        $fileName = 'return_payment_report_'.Carbon::now()->format('Ymd').'.xlsx';

        return Excel::download($export, $fileName);
    }
}
