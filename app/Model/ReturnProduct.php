<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    

    protected $table = 'return_products';

    protected $fillable = [
        'return_id', 'product_id', 'stock_id', 'qty', 'price', 'return_status' ,'return_reason'
    ];

    public function product()
    {
        return $this->belongsTo('App\Model\Product');
    }

    public function stock()
    {
        return $this->belongsTo('App\Model\Stock');
    }

    public function returns()
    {
        return $this->belongsTo('App\Model\Return');
    }
}
