<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Purchase;

class PurchaseController extends Controller
{
    public function index()
    {
        return response()->json(Purchase::withCount('stocks')->latest()->paginate(12));
    }

    
    public function show($id)
    {
        return Purchase::where('id', $id)->withCount('stocks')->get()->first();
    }

    public function searchedShipment(Request $request)
    {
        $barcode = $request->barcode;
        return Purchase::whereHas('stocks', function ($query)  use ($barcode) {
                                    $query->where('barcode', $barcode);
        })->withCount('stocks')->latest()->paginate(12);
    }
}
