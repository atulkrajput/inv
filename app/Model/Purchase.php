<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{

   protected $table = 'purchases';

    protected $fillable = [
        'added_by', 'invoice_number', 'supplier_id', 'sub_total', 'discount_desc', 'discount_percent', 'discount_amount', 'tax_percent', 'tax_amount', 'grand_total', 'shipment_number', 'purchase_date', 'revised', 'revised_amount'];

    public function suppliers()
    {
        return $this->belongsTo('App\Model\Supplier');
    }

    public function payments()
    {
        return $this->hasOne('App\Model\Payment');
    }

    public function stocks()
    {
        return $this->hasMany('App\Model\Stock', 'purchase_id');
    }
}
