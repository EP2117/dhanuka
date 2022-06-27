<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountHead extends Model
{
    protected $table='account_head';
    protected $with=['financial_type1','financial_type2'];
    protected $fillable=['name','financial_type1_id','financial_type2_id','created_at','created_by','updated_at','updated_by','is_active'];
    public function financial_type1(){
        return $this->belongsTo(FinancialType::class,'financial_type1_id','id');
    }
    public function financial_type2(){
        return $this->belongsTo(FinancialType::class,'financial_type2_id','id');
    }
    // public function sub_accounts(){
    //     return $this->hasMany(SubAccount::class,'sub_account_id');
    // }

    public function sub_accounts(){
        return $this->hasMany(SubAccount::class);
    }
}
