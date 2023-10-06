<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;

class SaleController extends Controller
{
    public function index()
    {
        return Sale::select('sales.*',  'customers.mobile', 'customers.name as customer_name', 'payments.id as pay_id', 'payments.pay_status as pay_status')
        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
        ->leftjoin('payments', 'payments.sale_id', '=', 'sales.id')
        ->withCount('sale_products')
        ->where('sales.inv_date', date('Y-m-d', time()))
        ->orderBy('id', 'desc')
        ->get();
    }

    public function saleLedger(Request $request)
    {
        $sales = Sale::select('sales.*',  'customers.mobile', 'customers.name as customer_name', 'payments.id as pay_id', 'payments.pay_status as pay_status')
        ->join('customers', 'customers.id', '=', 'sales.customer_id')
        ->join('payments', 'payments.sale_id', '=', 'sales.id')
        ->withCount('sale_products')
        ->where('sales.inv_date', date('Y-m-d', strtotime($request['inv_date'])))
        ->orderBy('id', 'desc')
        ->get();
        $total = Sale::select('sales.*',  'customers.mobile', 'customers.name as customer_name', 'payments.id as pay_id', 'payments.pay_status as pay_status')
        ->join('customers', 'customers.id', '=', 'sales.customer_id')
        ->join('payments', 'payments.sale_id', '=', 'sales.id')
        ->withCount('sale_products')
        ->where('sales.inv_date', date('Y-m-d', strtotime($request['inv_date'])))
        ->orderBy('id', 'desc')
        ->sum('sales.grand_total');
        return ['sales' => $sales, 'total' => $total];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'nullable|numeric|digits:10',
            'sub_total' => 'required',
            'grand_total' => 'required',
        ]);

        if($request['mobile']):
            $check = $this->__customerExist($request['mobile']);

            if($check):
                $customer_id = $check->id;
            else:
                $customer =  Customer::create([
                    'name' => ucwords($request['name']),
                    'mobile' => $request['mobile'],
                    'promotions' => 1,
                ]);
                $customer_id = $customer->id;
            endif;
        else:
            $customer_id = 6;
        endif;

        if($request['type'] == 1):  $invoiceNo = $this->invoiceGenerator();
        else: $invoiceNo = 0;  endif;

        if($request['inv_date']): $inv_date = date('Y-m-d', strtotime($request['inv_date']));
        else: $inv_date = date('Y-m-d', time()); endif;

        $sale = Sale::create([
            'customer_id' => $customer_id,
            'added_by' => Auth::user()->id,
            'type' => $request['type'],
            'invoice_number' => $invoiceNo,
            'sub_total' => $request['sub_total'],
            'discount_desc' => $request['discount_desc'],
            'discount_percent' => $request['discount_percent'],
            'discount_amount' => $request['discount_amount'],
            'total' => $request['sub_total'] - $request['discount_amount'],
            'tax_percent' => $request['tax_percent'],
            'tax_amount' => $request['tax_amount'],
            'grand_total' => $request['grand_total'],
            'inv_date' => $inv_date
        ]);
        $sale_id = $sale->id;

        if($request['payment_type'] == 2): $payamount = $request['grand_total']; $spayamount = 0; $pstatus = 1;
        else: $payamount = $request['paid_amount']; $spayamount = $request['grand_total']-$request['paid_amount']; $pstatus = 0; endif;

        Payment::create([
            'payment_type' => $request['payment_type'],
            'sale_id' => $sale_id,
            'firstpay_mode' => $request['payment_mode'],
            'firstpay_amount' => $payamount,
            'secondpay_amount' => $spayamount,
            'firstpay_date' => $inv_date,
            'firstpay_receiver' =>  Auth::user()->id,
            'firstpay_desc' => $request['payment_desc'],
            'pay_status' => $pstatus,
        ]);

        foreach ($request['product_detail__id'] as $key => $detail) {
            if($detail):

                SaleProduct::create([
                        'sale_id' =>$sale_id,
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

        return ['id' => $sale_id];
    }

    public function show($id)
    {
        return Sale::select('sales.*',  'customers.mobile', 'customers.name as customer_name', 'payments.id as pay_id', 'payments.pay_status as pay_status', 'payment_type', 'firstpay_mode', 'firstpay_amount', 'firstpay_date', 'firstpay_receiver',  'secondpay_mode', 'secondpay_amount', 'secondpay_date', 'secondpay_receiver', 'secondpay_desc', 'firstpay_desc')
                    ->join('customers', 'customers.id', '=', 'sales.customer_id')
                    ->join('payments', 'payments.sale_id', '=', 'sales.id')
                    ->where('sales.id', $id)
                    ->get()->first();
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        return ['message' => 'record deleted'];
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $sales = Sale::where(function($query) use ($search){
                $query->where('invoice_number','LIKE',"%$search%")->orwhere('grand_total','LIKE',"%$search%");
            })->withCount('sale_products')->paginate(10);
        }
        else{
           $sales = Sale::withCount('sale_products')->latest()->paginate(10);
        }
        return $sales;
    }

    public function searchSaleInvoice(Request $request)
    {
        $conditions = [];
        if(is_numeric($request['invoice_number'])): $conditions['sales.id'] = $request['invoice_number'];
        else: $conditions['sales.invoice_number'] = $request['invoice_number'];
        endif;
        return Sale::select('sales.*',  'customers.mobile', 'customers.name as customer_name', 'payments.id as pay_id', 'payments.pay_status as pay_status')
        ->join('customers', 'customers.id', '=', 'sales.customer_id')
        ->join('payments', 'payments.sale_id', '=', 'sales.id')
        ->withCount('sale_products')
        ->where($conditions)
        ->orderBy('id', 'desc')
        ->get();
    }

    private function invoiceGenerator()
    {
        $sale = Sale::where('type', 1)->latest()->get()->first();
        if($sale):
            $linv = substr($sale->invoice_number, 1);
            $counter = intval($linv)+1;
        else: $counter = 1; endif;
        $number = 'I'.str_pad($counter, 4, "0",STR_PAD_LEFT);
        return $number;
    }

    public function switchInvoice($sid = null)
    {
        $sale = Sale::findOrFail($sid);
        if($sale->type == 1):
            $data['type'] = 0;
            $data['invoice_number'] = 0;
            $data['tax_percent'] = 0;
            $data['tax_amount'] = 0;
            $data['grand_total'] = $sale->total;
            //$data['inv_date'] = date('Y-m-d', time());
            $sale->update($data);
        else:
            $data['type'] = 1;
            $data['invoice_number'] = $this->invoiceGenerator();
            $data['tax_percent'] = 18;
            $data['tax_amount'] = ($sale->total * 18)/100;
            $data['grand_total'] = $sale->total - $sale->tax_amount;
            //$data['inv_date'] = date('Y-m-d', time());
            $sale->update($data);
        endif;
        return ['status' => 'success'];
    }

    public function getReturnableData($sid = null) {
        $sale = Sale::where('id', $sid)->with('sale_products')->get()->first();
        $return['sale_id'] = $sale->id;
        $return['customer_id'] = $sale->customer_id;
        $return['type'] = $sale->type;
        $count = [];
        foreach($sale['sale_products'] as $key => $product):
            $counter[] = $key+1;
            $return['product_detail__id'][$key+1] = $product['product_id'];
            $return['product_detail__selling_cost'][$key+1] = $product['price'];
            $return['product_detail__name'][$key+1] = $this->__getProductName($product['product_id']);
            $return['product_detail__barcode'][$key+1] =  $this->__getBarcode($product['stock_id']);
            $return['product_detail__stockid'][$key+1] = $product['stock_id'];
            $return['product_detail__total'][$key+1] = 0;
            $return['product_detail__qty'][$key+1] = 0;
            $return['product_detail__oqty'][$key+1] = $product['qty'];
        endforeach;
        $return['sub_total'] ='0';
        $return['discount_desc'] = $sale->discount_desc;
        $return['discount_percent'] =$sale->discount_percent;
        $return['discount_amount'] ='';
        if($sale->type == 1):
            $return['tax_percent'] ='3';
            $return['tax_percent1'] ='1.5%';
            $return['tax_percent2'] ='1.5%';
        else:
            $return['tax_percent'] ='0';
            $return['tax_percent1'] ='0';
            $return['tax_percent2'] = '0';
        endif;
        $return['tax_amount'] ='0';
        $return['grand_total'] ='0';
        $return['payment_desc'] ='';
        return ['sale' => $return, 'counter' => $counter];
    }

    private function __getProductName($pid = null)
    {
        $product = Product::findOrFail($pid);
        return strtoupper($product->name);
    }

    private function __getBarcode($sid = null)
    {
        $product = Stock::findOrFail($sid);
        return strtoupper($product->barcode);
    }

    private function __customerExist($mobile = null)
    {
        return Customer::where('mobile', $mobile)->get()->first();
    }

    public function dayReport(Request $request)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Item Code', 'Barcode', 'Price', 'Received Stock', 'Available Stock', 'Adjustment');
        fputcsv($output, $columns);

        $data = Stock::select('stocks.*', 'products.name')
                        ->join('products', 'products.id', '=', 'stocks.product_id')->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->name, $index->barcode, $index->selling_cost, $index->received_stock, $index->stock, $index->adjustment ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function updateUser(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'nullable|numeric|digits:10',
            'customer_name' => 'required',
            'id' => 'required',
        ]);

        if($request['mobile']):
            $check = $this->__customerExist($request['mobile']);

            if($check):
                $check->update(['name' => $request['customer_name']]);
                $customer_id = $check->id;
            else:
                $customer =  Customer::create([
                    'name' => ucwords($request['customer_name']),
                    'mobile' => $request['mobile'],
                    'promotions' => 1,
                ]);
                $customer_id = $customer->id;
            endif;

            $record = Sale::where('id', $request['id'])->get()->first();
            $record->update(['customer_id' => $customer_id]);
        endif;
        return ['success'];
    }
}
