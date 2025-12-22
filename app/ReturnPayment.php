<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnPayment extends Model
{
	public function sale_return(){
        return $this->belongsTo('App\SaleReturn','return_id','id');
    }

    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }
 
    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }
}
