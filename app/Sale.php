<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model
{
    protected $with=['customer','sale_man','created_user'];
    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_sale', 'sale_id', 'product_id')->withPivot('id','uom_id','product_quantity','delivered_quantity','price','price_variant','total_amount','is_foc','rate','actual_rate','discount','other_discount','order_product_pivot_id');
    }
    public function approval()
    {
        return $this->belongsTo('App\OrderApproval', 'order_approval_id', 'id'); 
    }
    public function update_user()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id'); 
    }
    public function created_user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id'); 
    }
    public function collections()
    {
        return $this->belongsToMany('App\Collection', 'collection_sale', 'sale_id', 'collection_id')->withPivot('id','paid_amount','discount');
    }
    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_id', 'id')->select('id', 'warehouse_name'); 
    }
    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id', 'id'); 
    }
    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }
    public function order_approval()
    {
        return $this->belongsTo('App\OrderApproval', 'order_approval_id', 'id'); 
    }
    // public function office_sale_man()
    // {
    //     return $this->belongsTo('App\User', 'office_sale_man_id', 'id')->select('id', 'name'); 
    // }

    public function sale_man(){
        return $this->belongsTo('App\SaleMan','office_sale_man_id','id');
    }
    public function deliveries()
    {
        return $this->hasMany('App\SaleDelivery');
    }
}
