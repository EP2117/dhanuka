<?php

namespace App\Imports;

use App\AraSale;
use App\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class AraSaleImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AraSale([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        $user_id = Auth::user()->id;
        $j = 0;
        foreach ($rows as $row) 
        {
            //Insert into uoms table
            if($j > 0) {
                $customer = Customer::where('cus_code', $row[3])->first();
                if($customer) {
                    $sale = AraSale::where('ara_invoice_no', $row[0])->first();
                    if(!$sale) {
                        $obj= new AraSale;
                        $obj->ara_invoice_no = $row[0];
                        $dates = explode("/", $row[1]);
                        $obj->ara_invoice_date = $dates[2].'-'.$dates[1].'-'.$dates[0];
                        $obj->customer_id = $customer->id;
                        $obj->sale_man = $row[4];
                        $obj->total_amount = $row[5];
                        $obj->created_by = $user_id;
                        $obj->updated_by = $user_id;
                        $obj->save();
                    }
                } else {
                   /*$obj= new AraSale;
                        $obj->ara_invoice_no = 'cus_code_'.$row[3];
                        $dates = "2021-12-12";
                        $obj->ara_invoice_date = $dates;
                        $obj->customer_id = 1;
                        $obj->sale_man = 0;
                        $obj->total_amount = 0;
                        $obj->created_by = $user_id;
                        $obj->updated_by = $user_id;
                        $obj->save();*/ 
                }
            }

            $j++;
        }
    }
}
