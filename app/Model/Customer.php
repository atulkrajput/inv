<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
     protected $fillable = [
        'name', 'mobile', 'email', 'promotions', 'address','reg_type'
    ];

    public function sale()
    {
        return $this->hasMany('App\Model\Sale', 'customer_id');
    }
}
 