<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductPhoto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Session;
use Storage;
use Image;
use File;

class ProductPhotoController extends Controller
{
	public function show($id)
    {
    	$data = ProductPhoto::find($id);
    	$photo = $data->photo;
    	return compact('photo');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $product_id = $request->hid_product_id;
        $obj = new ProductPhoto;
        $file = $request->file('file');

        $file_name = $file->getClientOriginalName();
        $ext = strtolower(substr(strrchr($file_name, "."), 1));
        $onlyName = explode('.'.$ext,$file_name); 

        $newname = 'product_'.$onlyName[0]."_".date('YmdHis')."_".$product_id.".".$ext;

        $img = Image::make($file->getRealPath());

        $img_width = $img->width();
        if($img_width > 500) {
            $img->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $destinationPath = storage_path('app/public/image/customer');
        // save file as jpg with medium quality
        $img->save($destinationPath.'/'.$newname, 60);        
        $base64_encode_str = (string) Image::make($destinationPath.'/'.$newname)->encode('data-url');
        unlink($destinationPath.'/'.$newname);

        $obj->product_id = $product_id;
        $obj->photo = $base64_encode_str;
        $obj->save();
        $status = "success";
        return compact('status');
    }

    public function destroy($id)
    {
    	$data = ProductPhoto::find($id);
    	$data->delete();
    	return response(['status' => 'success']);
    }
}
