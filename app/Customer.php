<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $with=['state'];
    public function sales(){
        return $this->hasMany('App\Sale')->select('id','customer_id');
    }
    public function categories()
    {
        return $this->belongsToMany('App\Category', 'category_customer', 'customer_id', 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_customer', 'customer_id', 'product_id');
    }

    public function returns(){
        return $this->hasMany('App\SaleReturn')->select('id','customer_id');
    }

    public function collections(){
        return $this->hasMany('App\Collection')->select('id','customer_id');
    }

    public function return_payments(){
        return $this->hasMany('App\ReturnPayment')->select('id','customer_id');
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

    public function sale_advances(){
        return $this->hasMany('App\SaleAdvance')->select('id','customer_id');
    }

    public function customer_logs(){
        return $this->hasMany('App\CustomerLog');
    }
}
