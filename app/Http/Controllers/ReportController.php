<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Purchase;
use App\Model\Sale;
use App\Model\SaleProduct;
use App\Model\Returns;
use App\Model\ReturnProduct;
use App\Model\Stock;
use App\Model\Adjustment;
use App\Model\AdjustmentProduct;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function fetchReport()
    {
        return ['msg' => 'success'];
    }

    public function fetchCustomReport()
    {
        return ['msg' => 'success'];
    }

    public function export()
    {
        return Excel::download(new SalesExport('01-02-2019', 1), 'sales.xlsx');
    }

    public function fullStockReport()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Item Code', 'Barcode', 'Price', 'Received Stock', 'Available Stock', 'Adjustment', 'Total');
        fputcsv($output, $columns);

        $data = Stock::select('stocks.*', 'products.name')
                        ->join('products', 'products.id', '=', 'stocks.product_id')->get();
        $total = 0;
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->name, $index->barcode, $index->selling_cost, $index->received_stock, $index->stock, $index->adjustment, $index->selling_cost*$index->stock ));

            $total = $total + ($index->selling_cost*$index->stock);
         }
         $columns = array(' ', ' ', '', '', '', 'Total', 'Stock value', $total);
         fputcsv($output, $columns);
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function negativeStockReport()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Item Code', 'Barcode', 'Price', 'Received Stock', 'Available Stock', 'Adjustment');
        fputcsv($output, $columns);

        $data = Stock::select('stocks.*', 'products.name')
                        ->join('products', 'products.id', '=', 'stocks.product_id')
                        ->where('stocks.stock', '<', 0)->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->name, $index->barcode, $index->selling_cost, $index->received_stock, $index->stock, $index->adjustment ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="negative-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function structuredStockReport()
    {
        $stocks = Stock::where('stock', '>=', 1)->orderBy('barcode', 'asc')->count();
        $counter = number_format($stocks/5);

        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('Barcode', 'Stock','Barcode', 'Stock','Barcode', 'Stock','Barcode', 'Stock','Barcode', 'Stock');
        fputcsv($output, $columns);

        for ($i=0; $i <= $counter ; $i++) {
            $array = [];
            $data = Stock::where('stock', '>=', 1)->orderBy('stocks.id', 'asc')->skip($i*5)->limit(5)->get();
            foreach($data as $key => $index)
            {
                $array[] = $index->barcode;
                $array[] = $index->stock;
            }
            fputcsv($output, $array);
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="structured-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function salesDayReport(Request $request)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $column = array('-', 'Date', date('d-M-Y', time()), 'Sales Today', '-', '-', '-');
        fputcsv($output, $column);
        $columns = array('S.No.', 'Order No', 'Product', 'Barcode', 'Cost', 'Qty', 'Total');
        fputcsv($output, $columns);

        $data = SaleProduct::select('sales.id', 'products.name', 'stocks.barcode', 'sale_products.price', 'sale_products.qty')
                        ->join('products', 'products.id', '=', 'sale_products.product_id')
                        ->join('stocks', 'stocks.id', '=', 'sale_products.stock_id')
                        ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                        ->where('sales.inv_date', date('y-m-d', time()))
                        ->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->id, $index->name, $index->barcode, $index->price, $index->qty, $index->price*$index->qty ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="day-sales-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function salesTodayReport()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $column = array('-', 'Date', date('d-M-Y', time()), 'Sales Today', '-', '-', '-');
        fputcsv($output, $column);
        $columns = array('S.No.', 'Order No', 'Product', 'Barcode', 'Cost', 'Qty', 'Total');
        fputcsv($output, $columns);

        $data = SaleProduct::select('sales.id', 'products.name', 'stocks.barcode', 'sale_products.price', 'sale_products.qty')
                        ->join('products', 'products.id', '=', 'sale_products.product_id')
                        ->join('stocks', 'stocks.id', '=', 'sale_products.stock_id')
                        ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                        ->where('sales.inv_date', date('y-m-d', time()))
                        ->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->id, $index->name, $index->barcode, $index->price, $index->qty, $index->price*$index->qty ));
         }
        $sub = Sale::where('sales.inv_date', date('y-m-d', time()))
                        ->sum('sub_total');
         $tax = Sale::where('sales.inv_date', date('y-m-d', time()))
                        ->sum('tax_amount');
        $discount = Sale::where('sales.inv_date', date('y-m-d', time()))
                        ->sum('discount_amount');
        $total = Sale::where('sales.inv_date', date('y-m-d', time()))
                        ->sum('grand_total');
        $column0 = array('-', '-', '-', '-', '-', 'Sub Total', $sub);
        fputcsv($output, $column0);
        $column1 = array('-', '-', '-', '-', '-', 'Total Discount', $discount);
        fputcsv($output, $column1);
        $column2 = array('-', '-', '-', '-', '-', 'Total Tax', $tax);
        fputcsv($output, $column2);
        $column3 = array('-', '-', '-', '-', '-', 'Grand Total', $total);
        fputcsv($output, $column3);

        $column = array('-', '-', '-', 'Return Today', '-', '-', '-');
        fputcsv($output, $column);
        $columns = array('S.No.', 'Return No', 'Product', 'Barcode', 'Cost', 'Qty', 'Total');
        fputcsv($output, $columns);

        $data = ReturnProduct::select('returns.id', 'products.name', 'stocks.barcode', 'return_products.price', 'return_products.qty')
                        ->join('products', 'products.id', '=', 'return_products.product_id')
                        ->join('stocks', 'stocks.id', '=', 'return_products.stock_id')
                        ->join('returns', 'returns.id', '=', 'return_products.return_id')
                        ->where('returns.rev_date', date('y-m-d', time()))
                        ->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->id, $index->name, $index->barcode, $index->price, $index->qty, $index->price*$index->qty ));
         }
        $sub = Returns::where('returns.rev_date', date('y-m-d', time()))
                        ->sum('sub_total');
         $tax = Returns::where('returns.rev_date', date('y-m-d', time()))
                        ->sum('tax_amount');
        $discount = Returns::where('returns.rev_date', date('y-m-d', time()))
                        ->sum('discount_amount');
        $rtotal = Returns::where('returns.rev_date', date('y-m-d', time()))
                        ->sum('grand_total');
        $column0 = array('-', '-', '-', '-', '-', 'Sub Total', $sub);
        fputcsv($output, $column0);
        $column1 = array('-', '-', '-', '-', '-', 'Total Discount', $discount);
        fputcsv($output, $column1);
        $column2 = array('-', '-', '-', '-', '-', 'Total Tax', $tax);
        fputcsv($output, $column2);
        $column3 = array('-', '-', '-', '-', '-', 'Grand Total', $rtotal);
        fputcsv($output, $column3);
        $column5 = array('----', '-----', '----', '----', '---', '----', '---');
        fputcsv($output, $column5);
        $column4 = array('----', '-----', '----', '----', '---', 'Total Receiving',$total - $rtotal);
        fputcsv($output, $column4);

         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="day-sales-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function salesDayReports($udate = null)
    {
        if($udate): $date = date('Y-m-d', strtotime($udate));
        else: $date = date('y-m-d', time()); endif;
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $column = array('-', 'Date', date('d-M-Y', strtotime($udate)), 'Sales Report', '-', '-', '-');
        fputcsv($output, $column);
        $columns = array('S.No.', 'Estimate/Invoice No', 'Sub Total', 'Discount', 'Tax', 'Total');
        fputcsv($output, $columns);

        $data = Sale::where('sales.inv_date', $date)->get();
        foreach($data as $key => $index)
        {
            if($index->type == 1): $inv = $index->invoice_number;
            else: $inv = '#'.$index->id; endif;
            fputcsv($output, array(++$key, $inv, $index->sub_total, $index->discount_amount, $index->tax_amount, $index->grand_total));
        }

        $total = Sale::where('sales.inv_date',  $date)
                        ->sum('grand_total');
        $column3 = array('-', '-', '-', '-', 'Grand Total', $total);
        fputcsv($output, $column3);

        $column = array('-', '-', '-', 'Return Report', '-', '-', '-');
        fputcsv($output, $column);
        $columns = array('S.No.', 'Return No', 'Sub Total', 'Discount', 'Tax', 'Total');
        fputcsv($output, $columns);

        $data = Returns::where('returns.rev_date', $date)
                        ->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->id, $index->sub_total, $index->discount_amount, $index->tax_amount, $index->grand_total));
         }

         $rtotal = Returns::where('returns.rev_date', date('y-m-d', time()))
         ->sum('grand_total');

         $column4 = array('-', '-', '-', '-', 'Grand Total', $rtotal);
        fputcsv($output, $column4);
        $column5 = array('----', '-----', '----', '----', '----', '---');
        fputcsv($output, $column5);
        $column6 = array('----', '-----', '----', '----', 'Total Receiving', $total - $rtotal);
        fputcsv($output, $column6);

         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="day-sales-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function adjustmentReport($sdate = null, $edate = null)
    {
        if($sdate): $sdate = date('Y-m-d', strtotime($sdate)); endif;
        if($edate): $edate = date('Y-m-d', strtotime($edate)); endif;

        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $column = array('-', '-', '-', 'Adjustments Report', '-', date('d-m-Y', strtotime($sdate)), date('d-m-Y', strtotime($edate)));
        fputcsv($output, $column);
        $columns = array('S.No.', 'Adjustment No', 'Adjustment Date', 'Product', 'Batch No', 'Cost', 'Qty', 'Total');
        fputcsv($output, $columns);
        $total = 0; $tqty = 0;
        $data = AdjustmentProduct::with(['product', 'stock'])->whereDate('created_at','>=',$sdate)->whereDate('created_at','<=',$edate)->get();
        foreach($data as $key => $index)
        {
            fputcsv($output, array(++$key, $index->adjustment_id, date('d-m-Y', strtotime($index->created_at)), $index->product->name, $index->stock->barcode, $index->stock->selling_cost, $index->qty, $index->stock->selling_cost*$index->qty));
            $total = $total + $index->stock->selling_cost*$index->qty;
            $tqty = $tqty + $index->qty;
        }

        $column3 = array('-', '-', '-', '-', '', 'Grand Total', $tqty, $total);
        fputcsv($output, $column3);

         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="adjustment-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function monthReport($umonth = null)
    {
        if($umonth): $um = explode('-', $umonth); $m = $um[1]; $y = $um[0];
        else: $m = date('m', time()); $y = date('Y', time()); endif;
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        fputcsv($output, array('', 'Month', date('M-Y', strtotime($y.'-'.$m.'-01')), ''));
        $columns = array('S.No.', 'Date', 'Estimate/Invocie No', 'Sub Total', 'Discount', 'Tax', 'Total');
        fputcsv($output, $columns);
        $loop = date('t', strtotime($umonth));
		$count = 0;
        for ($i=1; $i <= $loop ; $i++) {
        	$data = Sale::whereDay('inv_date', $i)
        				->whereMonth('inv_date', $m)
                        ->whereYear('inv_date', $y)->get();
            foreach($data as $key => $index)
            {
                if($index->type == 1): $inv = $index->invoice_number;
                else: $inv = '#'.$index->id; endif;
                fputcsv($output, array(++$count, date('d-M-Y', strtotime($index->inv_date)), $inv, $index->sub_total, $index->discount_amount, $index->tax_amount, $index->grand_total));
            }
        }
        $total = Sale::whereMonth('inv_date', $m)
                        ->whereYear('inv_date', $y)
                        ->sum('grand_total');
        $column3 = array('-', '-', '-', '-', '-', 'Grand Total', $total);
        fputcsv($output, $column3);

        $column = array('-', '-', '-', '-', 'Return Report', '-', '-', '-');
        fputcsv($output, $column);
        $columns = array('S.No.','Date', 'Return No', 'Sub Total', 'Discount', 'Tax', 'Total');
        fputcsv($output, $columns);

        $count = 0;
        for ($i=1; $i <= $loop ; $i++) {
        	$rdata = Returns::whereDay('rev_date', $i)
           				->whereMonth('rev_date', $m)
                        ->whereYear('rev_date', $y)->get();
            foreach($rdata as $key => $index)
            {
                fputcsv($output, array(++$count, date('d-M-Y', strtotime($index->rev_date)), $index->id, $index->sub_total, $index->discount_amount, $index->tax_amount, $index->grand_total));
            }
        }

        $rtotal = Returns::whereMonth('rev_date', $m)
                        ->whereYear('rev_date', $y)->sum('grand_total');

        $column4 = array('-', '-', '-', '-', '-', 'Grand Total', $rtotal);
        fputcsv($output, $column4);
        $column5 = array('----', '-----', '----', '----', '----', '----', '---');
        fputcsv($output, $column5);
        $column6 = array('----', '-----', '----', '----','----', 'Total Receiving', $total - $rtotal);
        fputcsv($output, $column6);

        //fputcsv($output, array('Total Sales', $stotal, 'Total Return', $rtotal));
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="invoice-monthly-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }
}
