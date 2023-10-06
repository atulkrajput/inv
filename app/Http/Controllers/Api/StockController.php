<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Stock;
use App\Model\Purchase;
use App\Model\SaleProduct;
use App\Model\ReturnProduct;
use App\Model\Product;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function getPurchaseStocks($pid = null)
    {
        return Stock::select('stocks.*', 'products.name', 'products.model_no', 'categories.name as category')
                ->join('products', 'products.id', '=', 'stocks.product_id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('stocks.purchase_id', $pid)
                ->get();
    }

    public function revise(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'newstock' => 'required|numeric'
        ]);

            $record = Stock::where('id', $request['id'])->get()->first();
            if($record->readd):
                $data = json_decode($record->readd);
                $newdata[] = ['date' => date('Y-m-d', strtotime($request['date'])),
                        'stock' => $request['newstock'],
                        'old_recevied' => $record->received_stock,
                        'available_stock' => $record->stock];
                $data = array_merge($data, $newdata);
            else:
                $data[] = ['date' => date('Y-m-d', strtotime($request['date'])),
                        'stock' => $request['newstock'],
                        'old_recevied' => $record->received_stock,
                        'available_stock' => $record->stock];
            endif;
            $stock = $record->received_stock + $request['newstock'];
            $aval = $record->stock + $request['newstock'];
            Stock::where('id', $request['id'])->update([
                                        'received_stock' => $stock,
                                        'stock' => $aval,
                                        'readd' => json_encode($data),
                                    ]);
        return ['message' => 'updating data'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Stock::select('stocks.*', 'products.name', 'products.model_no', 'categories.name as category')
        ->join('products', 'products.id', '=', 'stocks.product_id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->orderBy('id', 'desc')->paginate(12);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $this->validate($request, [
            'sub_total' => 'required',
       ]);
       $snumber = $this->__getShipmentNo();
       $purchase = Purchase::create([
                            //    'added_by' => auth('api')->user()->id,
                               'added_by' => $request['admin_id'],
                               'invoice_number' => $snumber,
                               'supplier_id' => 1,
                               'sub_total' => $request['sub_total'],
                               'grand_total' => $request['sub_total'],
                               'shipment_number' => $snumber,
                               'payment_type' => 2,
                               'purchase_date' => date('Y-m-d', time())
       ]);

       foreach ($request['product_detail__stock'] as $key => $detail) {
           if(!empty($detail)):
               $check = Product::where('model_no', $request['product_detail__name'][$key])->get()->first();
               if($check):
                   $product_id = $check->id;
               else:
                   $product = Product::create([
                                   'name' => strtoupper($request['product_detail__name'][$key]),
                                   'model_no' => strtoupper($request['product_detail__name'][$key]),
                                   'category_id' => $request['product_detail__category_id'][$key],
                   ]);
                   $product_id = $product->id;
               endif;
               $barcode = $this->__CreateBarcode();

               if(isset($request['product_detail__image'][$key])){
                   $photo = strtoupper($barcode).'.'.explode('/', explode(':', substr($request['product_detail__image'][$key], 0, strpos($request['product_detail__image'][$key], ';')))[1])[1];
                   \Image::make($request['product_detail__image'][$key])->save(public_path('backend/product/').$photo)->resize(300,300);
               }
               else{
                   $photo = '';
               }
               $newdata[] = ['date' =>  date('Y-m-d', time()),
                               'stock' => $detail,
                               'old_recevied' => 0,
                               'available_stock' => 0];
               Stock::create([
                       'purchase_id' =>$purchase['id'],
                       'product_id' =>  $product_id,
                       'stock' => $detail,
                       'received_stock' => $detail,
                       'barcode' => $barcode,
                       'image' => $photo,
                       'readd' => json_encode($newdata),
                       'selling_cost' => $request['product_detail__purchase_cost'][$key],
                       'purchase_cost' => $request['product_detail__purchase_cost'][$key]
                   ]);
                   $newdata = [];
           endif;
       }
       return ['message' => 'product created'];
    }

    private function __getShipmentNo()
    {
        $last = Purchase::orderBy('id', 'desc')->get()->first();
        if(substr($last->shipment_number, 0, 8) == date('Ymd', time())):
            $counter = intval(substr($last->shipment_number, 8, 2))+1;
            return date('Ymd', time()).str_pad($counter, 2, "0",STR_PAD_LEFT);
        else:
            return date('Ymd', time()).'01';
        endif;
    }

    private function __CreateBarcode()
    {
        $record = Stock::orderBy('id', 'desc')->get()->first();
        $p1 = substr($record->barcode, 1, 2);
        $y1 = date('y', time());
        if($p1 == $y1){
            $linv = substr($record->barcode, 3);
            $counter = intval($linv)+1;
        } else { $counter = 1; }
        $number = 'E'.date('y', time()).str_pad($counter, 4, "0",STR_PAD_LEFT);
        return $number;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $stocks = Stock::select('stocks.*', 'products.name', 'products.model_no', 'categories.name as category')
            ->where(function($query) use ($search){
                $query->where('products.name','LIKE',"%$search%")->orwhere('stocks.barcode','LIKE',"%$search%")->orwhere('categories.name','LIKE',"%$search%")->orwhere('stocks.batch_no','LIKE',"%$search%");
            })
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->orderBy('barcode', 'desc')->paginate(12);
        }
        else{
           $stocks = Stock::select('stocks.*', 'products.name', 'products.model_no', 'categories.name as category')
                        ->join('products', 'products.id', '=', 'stocks.product_id')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->orderBy('barcode', 'desc')->paginate(12);
        }
        return $stocks;
    }

    public function searchStock(Request $request)
    {
        $conditions = [];
        if($request['category_id']): $conditions['categories.id'] = $request['category_id']; endif;
        if($request['product_code']): $conditions['products.model_no'] = $request['product_code']; endif;
        if($request['barcode']): $conditions['stocks.barcode'] = $request['barcode']; endif;

        if(count($conditions) == 0): return [];
        else:
            $stocks = Stock::select('stocks.*', 'products.name', 'products.model_no', 'categories.name as category')
                        ->join('products', 'products.id', '=', 'stocks.product_id')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->orderBy('id', 'desc')
                        ->where($conditions)->get();
        endif;
        return $stocks;
    }

    public function stockLedger(Request $request)
    {
        $stock = []; $sales = []; $returns = [];
        $stock = Stock::select('stocks.*', 'products.name', 'products.model_no', 'categories.name as category', 'purchases.shipment_number')
                    ->join('products', 'products.id', '=', 'stocks.product_id')
                    ->join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->orderBy('id', 'desc')
                    ->where('stocks.barcode', $request->barcode)->get()->first();
        if($stock):
            $stock->revised = json_decode($stock->readd);
            $sales = SaleProduct::select('sale_products.*', 'sales.invoice_number', 'sales.type', 'sales.inv_date', 'customers.name')
                        ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                        ->join('customers', 'sales.customer_id', '=', 'customers.id')
                        ->orderBy('sales.inv_date', 'desc')
                        ->where('sale_products.stock_id', $stock->id)->get();
            $returns = ReturnProduct::select('return_products.*', 'returns.type', 'returns.rev_date', 'customers.name', 'sales.id as sale_id', 'sales.invoice_number')
                        ->join('returns', 'returns.id', '=', 'return_products.return_id')
                        ->join('customers', 'returns.customer_id', '=', 'customers.id')
                        ->join('sales', 'sales.revised', '=', 'returns.id')
                        ->orderBy('returns.rev_date', 'desc')
                        ->where('return_products.stock_id', $stock->id)->get();
        endif;
        return ['stock' =>  $stock, 'sales' => $sales, 'returns' => $returns];
    }

    public function findByBarcode()
    {
        if($search = \Request::get('q')) {
            $stock = Stock::select('stocks.*', 'products.name')
                            ->join('products', 'products.id', '=', 'stocks.product_id')
                            ->where(function($query) use ($search){
                                    $query->where('barcode','=',$search)->orWhere('batch_no','=',$search)->orWhere('products.name','=',$search);
                            })->get();
        }
        else{
           $stock = [];
        }
        return $stock;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Stock::select('stocks.*', 'products.name')
        ->join('products', 'products.id', '=', 'stocks.product_id')
        ->where('stocks.id', $id)
        ->get()->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->type == 1):
            $this->validate($request, [
                'selling_cost' => 'required',
                'received_stock' => 'required'
            ]);

            $record = Stock::where('id', $request['id'])->get()->first();

            $diff = $request->received_stock - $record->received_stock;
            Stock::where('id', $request['id'])->update([
                                        'received_stock' => $record->received_stock + $diff,
                                        'stock' => $record->stock + $diff,
                                        'selling_cost' => $request->selling_cost,
                                        'purchase_cost' => $request->purchase_cost,
                                    ]);

        elseif($request->type == 2):
            $this->validate($request, [
                'adjustment' => 'required',
                'reason' => 'required'
            ]);

            $record = Stock::where('id', $request['id'])->get()->first();

            Stock::where('id', $request['id'])->update([
                                        'adjustment' => $request->adjustment,
                                        'stock' => $record->stock - $request->adjustment,
                                        'reason' => $request->reason,
                                    ]);
        else:
            $this->validate($request, [
                'image' => 'nullable'
            ]);

            if($request->image){
                $photo = strtolower($request['barcode']).'.'.explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
                \Image::make($request->image)->save(public_path('backend/product/').$photo)->resize(300,300);
                Stock::where('barcode', $request['barcode'])->update(['image' => $photo ]);
            }
        endif;
        return ['message' => 'updating data'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
