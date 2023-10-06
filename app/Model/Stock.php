<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'product_id', 'stock', 'received_stock', 'barcode', 'image', 'batch_no', 'purchase_id', 'purchase_cost', 'selling_cost', 'adjustment', 'reason', 'readd'
    ];

    public function product()
    {
        return $this->belongsTo('App\Model\Product');
    }
}
