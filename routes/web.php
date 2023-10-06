<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('reports/structured-stock-report', 'ReportController@structuredStockReport');
Route::get('reports/full-stock-report', 'ReportController@fullStockReport');
Route::get('reports/negative-stock-report', 'ReportController@negativeStockReport');
Route::get('reports/sales-day-report/{id}', 'ReportController@salesDayReports');
Route::get('reports/today-sales-report', 'ReportController@salesTodayReport');
Route::get('reports/month-report/{id}', 'ReportController@monthReport');
Route::get('reports/adjustment-report/{sid}/{eid}', 'ReportController@adjustmentReport');


Route::get('/{vue_capture?}',function () {
	    return view('welcome');
	})->where('vue_capture', '[\/\w\.-]*');

