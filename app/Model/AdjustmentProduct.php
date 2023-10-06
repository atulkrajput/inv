<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class AdjustmentProduct extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'adjustment_products';

    protected $fillable = [
        'adjustment_id', 'product_id', 'stock_id', 'qty', 'price'
    ];

    public function adjustment()
    {
        return $this->belongsTo('App\Model\Adjustment');
    }

    public function product()
    {
        return $this->belongsTo('App\Model\Product');
    }

    public function stock()
    {
        return $this->belongsTo('App\Model\Stock');
    }
}
