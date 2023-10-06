<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Newstock extends Model
{

    protected $fillable = [
        'stock', 'barcode', 'cost'
    ];

   
}
