<?php

namespace App\Imports;

use App\AraProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class AraProductImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AraProduct([
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
                $product = AraProduct::where('ara_product_code', $row[0])->first();
                if(!$product) {

                    $obj= new AraProduct;
                    $obj->ara_product_code = $row[0];
                    $obj->ara_product_name = $row[1];
                    $obj->ara_product_brand = $row[2];
                    $obj->ara_product_category = $row[3];
                    $obj->save();
                }
            }

            $j++;
        }
    }
}
