<?php

namespace App\Imports;

use App\Category;
use App\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
//use Maatwebsite\Excel\Concerns\ToModel;

class CategoryImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Category([
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

                //first check brand already exist or not
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
                $category = Category::where('category_name', $row[0])->where('brand_id',$brand_id)->first();
                if(!$category) {
                    $obj = new Category;
                    $obj->category_name = $row[0];
                    $obj->brand_id = $brand_id;
                    $obj->created_by = $user_id;
                    $obj->updated_by = $user_id;
                    $obj->save();
                }
            }

            $j++;
        }
    }
}
