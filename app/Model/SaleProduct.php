<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{

    protected $table = 'sale_products';

    protected $fillable = [
        'sale_id', 'product_id', 'stock_id', 'qty', 'price', 'return_status' ,'return_reason'
    ];

    public function product()
    {
        return $this->belongsTo('App\Model\Product');
    }

    public function stock()
    {
        return $this->belongsTo('App\Model\Stock');
    }

    public function sale()
    {
        return $this->belongsTo('App\Model\Sale');
    }
}
