<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     protected $fillable = [
        'name','code','publish'
    ];

    public function products()
    {
        return $this->hasMany('App\Model\Product', 'category_id');
    }
}
