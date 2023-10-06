<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'adjustments';

    protected $fillable = [
        'added_by', 'sub_total', 'grand_total'
    ];

    public function adjustment_product()
    {
        return $this->hasMany('App\Model\AdjustmentProduct', 'adjustment_id');
    }
}
