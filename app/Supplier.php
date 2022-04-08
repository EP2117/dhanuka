<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    protected $with=['state','township','country'];
    protected $fillable=['name','phone_number','supplier_code','country_id','state_id','township_id','supplier_billing_address','supplier_shipping_address','created_by','updated_by','is_active'];
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function township()
    {
        return $this->belongsTo(Township::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function purchase_advances(){
        return $this->hasMany('App\PurchaseAdvance');
    }

    public function landed_costings(){
        return $this->hasMany('App\LandedCosting');
    }
}
