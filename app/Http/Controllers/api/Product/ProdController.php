<?php

namespace App\Http\Controllers\api\Product;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProdController extends Controller
{
    public function index()
    {
        try {
            $purchaseOrders = Product::orderBy('StockCode','desc')
                                ->select(
                                    'StockCode',
                                    'Description',
                                    'LongDesc',
                                    'AlternateKey1',
                                    'StockUom',
                                    'AlternateUom',
                                    'OtherUom',
                                    'ConvFactAltUom',
                                    'ConvFactOthUom',
                                    'Mass',
                                    'Volume',
                                    'ProductClass',
                                    'WarehouseToUse',
                                )->get();

            if ($purchaseOrders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Product found',
                ], 200);  // HTTP 404 Not Found
            }

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $purchaseOrders
            ], 200);  // HTTP 200 OK
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
        
    }

    public function store(Request $request)
    {

        try {
            $data = $request->data;
            // return PO::create($data);;
            echo $data;
            // DB::transaction(function () use ($data) {
            //     $items = Arr::pull($data, 'Items');
            //     $po = Product::create($data);
            //     $po->POItems()->createMany($items);
            // });


            return response()->json([
                'success' => true,
                'message' => 'New Purchase Order created successfully',
            ], 200);  // HTTP 200 OK

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);  // HTTP 500 Internal Server Error
        }
    }

    public function show(string $stockCode)
    {
        $response = array();
        try {
            $data = Product::where('stockCode', $stockCode)->select(
                'StockCode',
                'Description',
                'LongDesc',
                'AlternateKey1',
                'StockUom',
                'AlternateUom',
                'OtherUom',
                'ConvFactAltUom',
                'ConvFactOthUom',
                'Mass',
                'Volume',
                'ProductClass',
                'WarehouseToUse',
            )->firstOrFail();

            $response = [
                'message' => 'Specific Product retrieved successfully',
                'data' => $data,
                'success' => true,
            ];
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);  // HTTP 200 OK
        }

        return response()->json($response);
    }
}
