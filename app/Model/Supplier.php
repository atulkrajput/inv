<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'contact_no', 'company_name', 'email', 'city', 'address', 'connection_source', 'status'
    ];

    public function purchase()
    {
        return $this->hasMany('App\Model\Purchase', 'supplier_id');
    }
}
 