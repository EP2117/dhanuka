<?php

namespace App\Imports;

use App\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class WarehouseImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Warehouse([
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
                $wh = Warehouse::where('warehouse_name', $row[0])->first();
                if(!$wh) {
                    $obj = new Warehouse;
                    $obj->warehouse_name = $row[0];
                    $obj->warehouse_type = 2;
                    $obj->created_by = $user_id;
                    $obj->updated_by = $user_id;
                    $obj->save();
                }
            }

            $j++;
        }
    }
}
