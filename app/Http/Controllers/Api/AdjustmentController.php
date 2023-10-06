<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Adjustment;
use App\Model\AdjustmentProduct;
use App\Model\Stock;
use App\Model\Payment;
use App\Model\Product;

class AdjustmentController extends Controller
{
    public function index()
    {
        return Adjustment::withCount('adjustment_product')
        ->whereDate('created_at', date('Y-m-d', time()))
        ->orderBy('id', 'desc')
        ->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sub_total' => 'required',
            'grand_total' => 'required',
        ]);

        $Adjustment = Adjustment::create([
            'added_by' => $request['admin_id'],
            'sub_total' => $request['sub_total'],
            'grand_total' => $request['grand_total'],
        ]);
        $Adjustment_id = $Adjustment->id;

        foreach ($request['product_detail__id'] as $key => $detail) {
            if($detail):

                AdjustmentProduct::create([
                        'adjustment_id' =>$Adjustment_id,
                        'product_id' => $detail,
                        'price' => $request['product_detail__selling_cost'][$key],
                        'stock_id' => $request['product_detail__stockid'][$key],
                        'qty' => $request['product_detail__qty'][$key],
                ]);

                $stock = Stock::findOrFail($request['product_detail__stockid'][$key]);
                $qty = $stock['stock'] - $request['product_detail__qty'][$key];
                $stock->update(['stock' => $qty]);

            endif;
        }

        return ['id' => $Adjustment_id];
    }
}
