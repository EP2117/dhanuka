<?php

namespace App\Exports;

use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\User;
use DB;

class ProductExport implements FromView, WithTitle
{
    protected $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
        $data = Product::select([
                  'products.*',
                  'uoms.uom_name',
                  'brands.brand_name',
                  'categories.category_name'
                ]);
        
        $data->leftjoin('uoms', 'uoms.id','products.uom_id');
        $data->leftjoin('brands', 'brands.id','products.brand_id');
        $data->leftjoin('categories', 'categories.id','products.category_id');

        if($request->brand_id != "") {
            $data->where('brand_id',  $request->brand_id);
        }

        if($request->category_id != "") {
            $data->where('category_id',  $request->category_id);
        }

        if($request->product_code != "") {
            $data->where('product_code',  'LIKE', '%'.$request->product_code.'%');
        }

        if($request->product_name != "") {
            $data->where('product_name',  'LIKE', '%'.$request->product_name.'%');
        }

        if($request->status != "") {
            $data->where('is_active', $request->status);
        }

        if($request->order == "") {
            $order = "ASC";
        } else {
            $order = $request->order;
        }
        if($request->sort_by != "") {
            if($request->sort_by == "name") {
                $data->orderBy('product_name', $order);
            }
            else if($request->sort_by == "code") {
                $data->orderBy('product_code', $order);
            }
            else if($request->sort_by == "brand") {
                $data->orderBy('brands.brand_name', $order);
            }
            else if($request->sort_by == "category") {
                $data->orderBy('categories.category_name', $order);
            }
            else if($request->sort_by == "uom") {
                $data->orderBy('uoms.uom_name', $order);
            }
            else {}
        } else {
            $data = $data->orderBy('products.id', 'DESC');  
        }
        $data->with('selling_uoms');
        $data = $data->get();


        return view('exports.productExport', [
            'data' => $data,
            'request' => $request,
        ]);
    }

    public function title(): string
    {
        return 'Master Product Export';
    }
}

