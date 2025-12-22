<?php

namespace App\Imports;

use App\AraSale;
use App\AraProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
use DB;
//use Maatwebsite\Excel\Concerns\ToModel;

class AraSaleProductImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AraSaleProduct([
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
                $sale = AraSale::where('ara_invoice_no', $row[0])->first();
                if($sale) {
                    $sale_id = $sale->id;
                    $product = AraProduct::where('ara_product_code', $row[1])->first();  
                    if($product) {
                        $product_id = $product->id;
                        DB::table('ara_product_sale')->insert(
                            [
                                'ara_sale_id' => $sale_id, 
                                'ara_product_id' => $product_id, 
                                'quantity' => $row[3],
                                'rate' => $row[4],
                                'total_amount' => $row[5]
                            ]
                        );
                    }
                    
                }
            }

            $j++;
        }
    }
}
