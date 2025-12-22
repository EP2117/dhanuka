<?php

namespace App\Imports;

use App\Customer;
use App\Township;
use App\State;
use App\CustomerType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Customer([
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
                $customer = Customer::where('cus_code', $row[0])->first();
                if(!$customer) {
                    //get state id
                    $state = State::where('state_name', $row[5])->first();
                    if($state) {
                        $state_id = $state->id;
                    } else {
                        //Insert into states table
                        $state = new State;
                        $state->state_name = $row[5];
                        $state->country_id = 1;
                        $state->created_by = $user_id;
                        $state->updated_by = $user_id;
                        $state->save();
                        $state_id = $state->id;
                    }

                    //get township id
                    $tsp = Township::where('township_name', $row[6])->first();
                    if($tsp) {
                        $township_id = $tsp->id;
                    } else {
                        //Insert into townships table
                        $tsp = new Township;
                        $tsp->township_name = $row[6];
                        $tsp->created_by = $user_id;
                        $tsp->updated_by = $user_id;
                        $tsp->save();
                        $township_id = $tsp->id;
                    }

                    //get customer type id
                    $type = CustomerType::where('customer_type_name', $row[7])->first();
                    if($type) {
                        $cus_type = $type->id;
                    } else {
                        $type = new CustomerType;
                        $type->customer_type_name = $row[7];
                        $type->created_by = $user_id;
                        $type->updated_by = $user_id;
                        $type->save();
                        $cus_type = $type->id;
                    }

                    //Insert into customer table
                    $obj = new Customer;
                    $obj->cus_code = $row[0];
                    $obj->cus_name = $row[1];
                    $obj->cus_phone = $row[2]; 
                    $obj->customer_type_id = $cus_type;
                    $obj->country_id = 1;
                    $obj->state_id = $state_id;                                       
                    $obj->township_id = $township_id;
                    $obj->cus_billing_address = $row[3];
                    $obj->cus_shipping_address = $row[4];
                    $obj->created_by = $user_id;
                    $obj->updated_by = $user_id;
                    $obj->save();
                }
            }

            $j++;
        }
    }
}
