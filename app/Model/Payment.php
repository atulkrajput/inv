<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $table = 'payments';

    protected $fillable = [
                    'sale_id', 'purchase_id', 'return_id', 'payment_type', 'firstpay_mode', 'firstpay_amount', 'firstpay_date', 'firstpay_receiver', 'secondpay_mode', 'secondpay_amount', 'secondpay_date', 'secondpay_receiver', 'secondpay_desc', 'firstpay_desc', 'pay_status'
            ];

    public function sales()
    {
        return $this->belongsTo('App\Model\Sale');
    }

    public function purchases()
    {
        return $this->belongsTo('App\Model\Purchase');
    }

}
