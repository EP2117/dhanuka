<?php

namespace App\Imports;

use App\State;
use App\Country;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class StateImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new State([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        $user_id = Auth::user()->id;
        $j = 0;
        foreach ($rows as $row) 
        {
            
            if($j > 0) {
                $state = State::where('state_name', $row[0])->first();
                if(!$state) {

                    //get country id
                    $country = Country::where('country_name', $row[1])->first();
                    if($country) {
                        $country_id = $country->id;
                    } else {
                        //Insert into country table
                        $country = new Country;
                        $country->country_name = $row[1];
                        $country->created_by = $user_id;
                        $country->updated_by = $user_id;
                        $country->save();
                        $country_id = $country->id;
                    }

                    $obj = new State;
                    $obj->state_name = $row[0];
                    $obj->country_id = $country_id;
                    $obj->created_by = $user_id;
                    $obj->updated_by = $user_id;
                    $obj->save();
                }
            }

            $j++;
        }
    }
}
