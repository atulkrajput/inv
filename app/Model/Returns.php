<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{

    protected $table = 'returns';

    protected $fillable = [
        'added_by', 'type', 'customer_id', 'sub_total', 'discount_desc', 'discount_percent','discount_amount', 'total', 'tax_percent','tax_amount','grand_total', 'rev_date'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Model\Customer');
    }

    public function sale()
    {
        return $this->hasOne('App\Model\Sale', 'revised', 'id')->with('payment');
    }

    public function payment()
    {
        return $this->hasOne('App\Model\Payment', 'return_id','id');
    }

    public function return_products()
    {
        return $this->hasMany('App\Model\ReturnProduct', 'return_id');
    }
}
