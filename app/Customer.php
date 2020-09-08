<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
	use SoftDeletes;

    public function sales(){
        return $this->hasMany('App\Sale')->select('id','customer_id');
    }

    public function collections(){
        return $this->hasMany('App\Collection')->select('id','customer_id');
    }

    public function ara_sales(){
        return $this->hasMany('App\AraSale')->select('id','customer_id');
    }

    public function customer_type()
    {
        return $this->belongsTo('App\CustomerType')->select('id', 'customer_type_name');
    }

    public function state()
    {
        return $this->belongsTo('App\State')->select('id', 'state_name');
    }

    public function township()
    {
        return $this->belongsTo('App\Township')->select('id', 'township_name');
    }

    public function country()
    {
        return $this->belongsTo('App\Country')->select('id', 'country_name');
    }
}
