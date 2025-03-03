<?php

use App\Http\Controllers\api\Report\CountController;
use App\Http\Controllers\api\Report\RRController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanAndMoveFilesController;
use Laravel\Sanctum\Http\Middleware\CheckForAnyToken;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\AuthenticateWithCookie;

//helper xD
function page_view($view)
{
    return view('Pages.' . $view);
}


// Route::get('/', function () {
//     return view('welcome');
// });


//Route::middleware(['auth:sanctum', 'redirectTo:/login'])->group(function () {});

Route::get('/pamasterlist', function () {
    return page_view('pamasterlist_page');
})->name('pamasterlist');

Route::get('/product', function () {
    return page_view('product_page');
})->name('product');

Route::get('/inventory', function () {
    // return page_view('inventory_page');
    return page_view('invCount_page');
})->name('inventory');

Route::get('/salesman', function () {
    return page_view('salesman_page');
})->name('salesman');

Route::get('/customer', function () {
    return page_view('customer_page');
})->name('customer');

Route::get('/dbconfig', function () {
    return page_view('dbconfig_page');
})->name('dbconfig');

Route::get('/picklist', function () {
    return page_view('picklist_page');
})->name('picklist');

Route::get('/testpage', function () {
    return page_view('inventory_test');
})->name('dbconfig');


Route::get('/patarget', function () {
    return page_view('patarget_page');
})->name('patarget');



Route::get('/invoices', function () {
    return page_view('invoices_page');
})->name('invoices');

Route::get('/receiving-report', function () {
    return page_view('receiving_report_page');
})->name('receiving-report');

Route::get('/purchase-order', function () {
    return page_view('purchase_order_page');
})->name('purchase-order');

Route::get('/register', function () {
    return page_view('register');
})->name('register');

Route::get('/uploader', function () {
    return page_view('uploader_page');
})->name('uploader');

Route::get('/login', function () {
    return page_view('login');
})->name('login');

Route::get('/print', function () {
    return page_view('PurchaseOrder-PDF');
})->name('print');

// PRINTING
Route::get('/print/rr/testing', function () {
    $data = [
        'title' => 'RR Printing',
        'date' => now()->format('Y-m-d'),
        'distName'=> 'FUI Shell',
        'supCode'=> 'VE-P0002',
        'supName'=> 'Shell Pilipinas Corporation',
        'supAdd'=> 'Fort Bonifacio 1635 Taguig City NCR, Fourth District Philippines Fort Bonifacio 1635 Taguig City NCR, Fourth District Philippines',
        'supTIN'=> '000-164-757-00000',
        'rrNo'=> '1600000711',
        'rrDate'=> 'Nov. 18, 2024',
        'rrRef'=> 'DN-512545212',
        'rrStat1'=> 'Closed',
        'rrStat2'=> 'Original', 
        'prepared'=> 'Marvin Navarro', 
        'checked'=> 'Jhunrey Lucero', 
        'approved'=> 'Jhun Woogie Arrabis', 
        'items' => []
    ];

    // Generate 40 items dynamically
    for ($i = 1; $i <=19; $i++) {
        $data['items'][] = [
            'itemCode' => rand(100000000, 999999999),
            'itemDesc' => 'Sample Item Description' . $i,
            'itemQty' => rand(10, 500),
            'itemOum' => ['CS', 'PC', 'IB'][array_rand(['CS', 'PC', 'IB'])],
            'itemWhsCode'=> 'V' . rand(100, 999) . 'M' . rand(0, 9),
            'itemUnitPrice' => round(rand(1000, 5000) + (rand(0, 99) / 100), 2),
            'netVat' => round(rand(5000, 500000) + (rand(0, 99) / 100), 2),
            'vat' => round(rand(500, 50000) + (rand(0, 99) / 100), 2),
            'gross' => round(rand(10000, 600000) + (rand(0, 99) / 100), 2)
        ];
    }

     // Convert array to object
     $report = json_decode(json_encode($data));

    return view('Pages.Printing.RR_printing_test', compact('report'));
})->name('printrrtest');

Route::get('/print/rr/', [RRController::class, 'printPage'])->name('web.print');

Route::get('/print/countsheet/testing', function () {
    $data = [
        'distributor' => 'Fast Distribution Corporation',
        'branch' => 'CEBU Branch',
        'warehouse'=> 'M1',
        'date'=> now()->format('Y-m-d'),
        'counted'=> 'Jhunrey Lucero', 
        'confirmed'=> 'Jhun Woogie Arrabis', 
        'items' => []
    ];

    // Generate 40 items dynamically
    for ($i = 1; $i <=50; $i++) {
        $data['items'][] = [
            'stockCode' => rand(10000000, 99999999),
            'itemDesc' => 'Sample Item Description' . $i,
            'cases' => rand(10, 500),
            'ib' => rand(10, 999),
            'piece'=> rand(1, 999),
        ];
    }

     // Convert array to object
     $report = json_decode(json_encode($data));

    return view('Pages.Printing.CountSheet_printing', compact('report'));
})->name('printcountSheet');

Route::get('/print/countsheet/manual', [CountController::class, 'printManualPage'])->name('printManualPage');

Route::get('/countsheet', function () {
    return page_view('invCount_page');
})->name('countsheet');

Route::get('/layout', function () {
    return page_view('layout');
})->name('layout');


// Route::get('/job/start', [ScanAndMoveFilesController::class, 'startJob'])->name('job.start');
// Route::get('/job/stop', [ScanAndMoveFilesController::class, 'stopJob'])->name('job.stop');
// Route::get('/job/status', [ScanAndMoveFilesController::class, 'getJobStatus'])->name('job.status');
