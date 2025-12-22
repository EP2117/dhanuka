<?php

namespace App\Imports;

use App\Township;
use App\State;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class TownshipImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Township([
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

                //get state id
                $state = State::where('state_name', $row[1])->first();
                if($state) {
                    $state_id = $state->id;
                } else {
                    //Insert into states table
                    $state = new State;
                    $state->state_name = $row[1];
                    $state->country_id = 1;
                    $state->created_by = $user_id;
                    $state->updated_by = $user_id;
                    $state->save();
                    $state_id = $state->id;
                }

                $tsp = Township::where('township_name', $row[0])->first();
                if(!$tsp) {
                    $obj = new Township;
                    $obj->township_name = $row[0];
                    $obj->state_id = $state_id;
                    $obj->created_by = $user_id;
                    $obj->updated_by = $user_id;
                    $obj->save();
                }
            }

            $j++;
        }
    }
}
