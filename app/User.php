<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo('App\Branch')->select('id', 'branch_name');
    }

    public function branches()
    {
        return $this->belongsToMany('App\Branch', 'branch_user', 'user_id', 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse')->select('id', 'warehouse_name');
    }

    public function role()
    {
        return $this->belongsTo('App\Role')->select('id', 'role_name');
    }

    public function brands(){
        return $this->hasMany('App\Brand','country_head_id');
    }

    public function country_head_parent()
    {
        return $this->belongsTo('App\User', 'country_head_id');
    }

    public function country_head_children()
    {
        return $this->hasMany('App\User', 'country_head_id');
    }

    public function local_supervisor_parent()
    {
        return $this->belongsTo('App\User', 'local_supervisor_id');
    }

    public function local_supervisor_children()
    {
        return $this->hasMany('App\User', 'local_supervisor_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'sale_man_id');
    }

    public function country_heads()
    {
      return $this->belongsToMany('App\User','countryhead_officesaleman','office_sale_man_id','country_head_id');
    }

    public function office_sale_mans()
    {
      return $this->belongsToMany('App\User','countryhead_officesaleman','country_head_id','office_sale_man_id');
    }

    public function customer_logs(){
        return $this->hasMany('App\CustomerLog','added_by','id');
    }

}
