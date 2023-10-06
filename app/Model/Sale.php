<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'added_by', 'type', 'invoice_number', 'customer_id', 'sub_total', 'discount_desc', 'discount_percent','discount_amount', 'total', 'tax_percent','tax_amount','grand_total','payment_desc','payment_mode','revised','revised_amount', 'inv_date'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Model\Customer');
    }

    public function payment()
    {
        return $this->hasOne('App\Model\Payment', 'sale_id','id');
    }

    public function sale_products()
    {
        return $this->hasMany('App\Model\SaleProduct', 'sale_id');
    }
}
