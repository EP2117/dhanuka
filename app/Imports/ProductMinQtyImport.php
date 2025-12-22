<?php

namespace App\Imports;

use App\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class ProductMinQtyImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
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
                $product = Product::where('product_code', $row[0])->first();
                if($product) {
                    $product->minimum_qty = $row[1];
                    $product->percentage_qty = $row[2];
                    $product->save();
                }
                    
            }

            $j++;
        }
    }
}
