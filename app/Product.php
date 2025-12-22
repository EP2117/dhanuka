<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $with=['product_transition','uom'];
    public function product_transition(){
        return $this->hasMany(ProductTransition::class);
    }
    public function selling_uoms()
    {
        return $this->belongsToMany('App\Uom', 'product_selling_uom')->withPivot('relation','product_price','retail1_price','retail2_price','wholesale_price','per_warehouse_uom_price','warehouse_uom_purchase_price');
    }

	//relationship for warehouse uom
	public function uom()
    {
        return $this->belongsTo('App\Uom');
    }

    public function photos()
    {
        return $this->hasMany('App\ProductPhoto');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function mainwarehouse_entries()
    {
        return $this->belongsToMany('App\MainwarehouseEntry', 'product_mainwarehouse_entry', 'product_id', 'entry_id')->withPivot('id','uom_id','product_quantity');
    }

    public function transfers()
    {
        return $this->belongsToMany('App\Transfer', 'product_transfer', 'product_id', 'transfer_id')->withPivot('id','uom_id','product_quantity','cost_price');
    }

    public function sales()
    {
        return $this->belongsToMany('App\Sale', 'product_sale', 'product_id', 'sale_id')->withPivot('id','uom_id','ctn','product_quantity','delivered_quantity','return_quantity','price','price_fx','price_variant','total_amount','total_amount_fx','is_foc','rate','rate_fx','actual_rate','actual_rate_fx','discount','discount_fx','other_discount','other_discount_fx','order_product_pivot_id');
    }

    public function returns()
    {
        return $this->belongsToMany('App\SaleReturn', 'product_return', 'product_id', 'return_id')->withPivot('id','sale_product_pivot_id','uom_id','product_quantity','price','price_variant','total_amount','is_foc');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order', 'product_order', 'product_id', 'order_id')->withPivot('id','uom_id','product_quantity','approved_quantity','price','price_variant','total_amount','is_foc');
    }

    public function order_approvals()
    {
        return $this->belongsToMany('App\OrderApproval', 'order_approval_product', 'product_id', 'approval_id')->withPivot('id','order_id','uom_id','approval_qty','price','price_variant','total_amount','is_foc');
    }
}
