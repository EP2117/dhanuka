<?php

namespace App\Imports;

use App\Product;
use App\Brand;
use App\Category;
use App\Uom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToCollection
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
            
            if($j > 0) {
                $check = Product::where('product_code', $row[0])->first();
                if(!$check){
                    //check and get brand id
                    $brand = Brand::where('brand_name', $row[1])->first();
                    if($brand) {
                        $brand_id = $brand->id;
                    } else {
                        //Insert into brands table
                        $brand = new Brand;
                        $brand->brand_name = $row[1];
                        $brand->created_by = $user_id;
                        $brand->updated_by = $user_id;
                        $brand->save();
                        $brand_id = $brand->id;
                    }

                    //check and get category id
                    $cate = Category::where('category_name', $row[2])->first();
                    if($cate) {
                        $cate_id = $cate->id;
                    } else {
                        //Insert into categories table
                        $cate = new Category;
                        $cate->category_name = $row[2];
                        $cate->created_by = $user_id;
                        $cate->updated_by = $user_id;
                        $cate->save();
                        $cate_id = $cate->id;
                    }

                    //check and get warehouse pre-defined uom id
                    $wh_uom = Uom::where('uom_name', $row[4])->first();
                    if($wh_uom) {
                        $wh_uom_id = $wh_uom->id;
                    } else {
                        //Insert into uoms table
                        $uom = new Uom;
                        $uom->uom_name = $row[4];
                        $uom->created_by = $user_id;
                        $uom->updated_by = $user_id;
                        $uom->save();
                        $wh_uom_id = $uom->id;
                    }

                    //Insert into product table
                    $product = new Product;
                    $product->product_name      = $row[3];
                    $product->product_code      = $row[0];
                    $product->brand_id          = $brand_id;
                    $product->category_id       = $cate_id;
                    $product->uom_id            = $wh_uom_id;
                    //$product->product_price     = $request->product_price;
                    $product->created_by        = $user_id;
                    $product->updated_by        = $user_id;
                    $product->save();

                    //Product Selling UOM Relation
                    $suom_arr = explode(",",$row[5]);
                    $relation_arr = explode(",", $row[6]);

                    for($i=0; $i<count($suom_arr); $i++) {

                        //check and get selling uom id
                        $selling_uom = Uom::where('uom_name', $suom_arr[$i])->first();
                        if($selling_uom) {
                            $selling_uom_id = $selling_uom->id;
                        } else {
                            //Insert into uoms table
                            $uom = new Uom;
                            $uom->uom_name = $suom_arr[$i];
                            $uom->created_by = $user_id;
                            $uom->updated_by = $user_id;
                            $uom->save();
                            $selling_uom_id = $uom->id;
                        }

                        $product->selling_uoms()->attach($selling_uom_id,['relation' => $relation_arr[$i]]); 
                    }

                }
            }

            $j++;
        }
    }
}
