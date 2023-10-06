<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'category_id', 'model_no', 'image', 'description'
    ];

    public function category()
    {
        return $this->belongsTo('App\Model\Category');
    }

    public function stocks()
    {
        return $this->hasMany('App\Model\Stock', 'product_id');
    }
}
 