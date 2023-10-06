<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Adjustment;
use App\Model\AdjustmentProduct;
use App\Model\Stock;
use Illuminate\Http\Request;

class AdjustmentProductController extends Controller
{
    public function show($id)
    {
        return AdjustmentProduct::select('adjustment_products.*', 'name', 'model_no','products.image','adjustments.grand_total','barcode', 'batch_no')
                    ->join('stocks', 'stocks.id', '=', 'adjustment_products.stock_id')
                    ->join('products', 'products.id', '=', 'adjustment_products.product_id')
                    ->join('adjustments', 'adjustments.id', '=', 'adjustment_products.adjustment_id')
                    ->where('adjustment_products.adjustment_id', $id)
                    ->get();
    }
}
