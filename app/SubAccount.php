<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubAccount extends Model
{
    protected $fillable=['sub_account_name','account_group_id','account_head_id','account_type_id','created_at','created_by','updated_at','updated_by','is_active'];
    protected $table='sub_accounts';
    protected $with=['account_head','account_type'];
    public function account_head(){
        return $this->belongsTo(AccountHead::class,'account_head_id','id');
    }
    public function account_type(){
        return $this->belongsTo(AccountType::class,'account_type_id','id');
    } 
    public function account_group(){
        return $this->belongsTo(AccountGroup::class,'account_group_id','id');
    }  
}
