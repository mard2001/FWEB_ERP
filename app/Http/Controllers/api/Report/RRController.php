<?php

namespace App\Http\Controllers\api\Report;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Orders\PO;
use Illuminate\Http\Request;
use App\Models\ReceivingRHeader;
use App\Models\ReceivingRDetails;
use App\Services\ProductCalculator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class RRController extends Controller
{

    protected $productController;

    public function __construct(ProductCalculator $productController)
    {
        $this->productController = $productController;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            
            $dataset = [];
            $dataset = ReceivingRHeader::select('RRNo', 'Reference', 'RRDATE', 'Status', 'RECEIVEDBY', 'PO_NUMBER', 'Total')
                ->with([
                    'poincluded' => function ($query) {
                        $query->selectRaw('PONumber, TRIM(SupplierCode) as SupplierCode') // Fetch only required columns from PO
                            ->with(['posupplier' => function ($supplierQuery) {
                                $supplierQuery->select('SupplierCode', 'SupplierName', 'CompleteAddress'); // Fetch required supplier details
                            }]);
                    }
                ])
                ->get();

            // for ($j = 1; $j <= 10; $j++) {
            //     $data = [
            //         'title' => 'RR Printing ' . $j,
            //         'dateToday' => now()->format('Y-m-d'),
            //         'distName'=> 'FUI Shell',
            //         'SupplierCode'=> 'VE-P000' . rand(10, 99),
            //         'SupplierName'=> 'Shell Pilipinas Corporation',
            //         'Address'=> 'Fort Bonifacio 1635 Taguig City NCR, Fourth District Philippines',
            //         'SupplierTIN'=> '000-164-757-00000',
            //         'RRNo'=> '16000007' . rand(10, 99),
            //         'Date'=> 'Nov. 18, 2024',
            //         'Reference'=> 'DN-512545212' . rand(400000000, 999999999),
            //         'Status'=> 'Closed',
            //         'Status2'=> 'Original', 
            //         'PreparedBy'=> 'Marvin Navarro', 
            //         'CheckedBy'=> 'Jhunrey Lucero', 
            //         'ApprovedBy'=> 'Jhun Woogie Arrabis', 
            //         'items' => []
            //     ];
            
            //     for ($i = 1; $i <=rand(10, 19); $i++) {
            //         $data['items'][] = [
            //             'SKU' => rand(100000000, 999999999),
            //             'Description' => 'Sample Item Description' . $i,
            //             'Quantity' => rand(10, 500),
            //             'UOM' => ['CS', 'PC', 'IB'][array_rand(['CS', 'PC', 'IB'])],
            //             'WhsCode'=> 'V' . rand(100, 999) . 'M' . rand(0, 9),
            //             'UnitPrice' => round(rand(1000, 5000) + (rand(0, 99) / 100), 2),
            //             'NetVat' => round(rand(5000, 500000) + (rand(0, 99) / 100), 2),
            //             'Vat' => round(rand(500, 50000) + (rand(0, 99) / 100), 2),
            //             'Gross' => round(rand(10000, 600000) + (rand(0, 99) / 100), 2)
            //         ];
            //     }

            //     array_push($dataset,$data);
            // }

            if (count($dataset) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Receiving Report Data found',
                ], 200);  // HTTP 404 Not Found
            }

            return response()->json([
                'success' => true,
                'message' => 'Receiving Reports retrieved successfully',
                'data' => $dataset
            ], 200);  // HTTP 200 OK
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($RRNum)
    {
        try {

            $data = ReceivingRHeader::select('RRNo', 'Reference', 'RRDATE', 'Status', 'RECEIVEDBY', 'PO_NUMBER', 'Total')
                ->with([
                    'rrdetails' => function ($query) {
                        $query->with(['product' => function ($productQuery) {
                            $productQuery->whereIn('StockCode', function ($subQuery) {
                                $subQuery->selectRaw("CAST(SKU AS VARCHAR)") // Ensure SKU is treated as VARCHAR
                                    ->from('tblInvRRDetails');
                            });
                        }]);
                    },
                    'poincluded' => function ($query) {
                        $query->selectRaw('PONumber, TRIM(SupplierCode) as SupplierCode') // Fetch only required columns from PO
                            ->with(['posupplier' => function ($supplierQuery) {
                                $supplierQuery->select('SupplierCode', 'SupplierName', 'CompleteAddress'); // Fetch required supplier details
                            }]);
                    }
                ])
                ->where('RRno', $RRNum)
                ->get()
                ->map(function ($header) {
                    foreach ($header->rrdetails as $detail) {
                        if ($detail->product) {
                            // Call the convertProductToLargesttUnit method
                            $uoms = array_map('strval', [
                                $detail->product->StockUom, $detail->product->AlternateUom, $detail->product->OtherUom
                            ]);
                            
                            $detail->convertedQuantity = app(ProductCalculator::class)->convertProductToLargesttUnit(
                                $uoms, 
                                $detail->Quantity, 
                                $detail->product->ConvFactAltUom, 
                                $detail->product->ConvFactOthUom
                            );
                        }
                    }
                    return $header;
                });


            if (count($data) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receiving Report not found',
                ], 404);
            }
            return response()->json([
                'message' => 'Receiving Report retrieved successfully',
                'data' => $data,
                'success' => true,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);  // HTTP 500 Internal Server Error
        }
    }

    public function showv2($RRNum)
    {
        try {
            $perPOData = ReceivingRDetails::select('PO_NUMBER', 'RRNo')
            ->with('rrheader') 
            ->where('RRNo', $RRNum)
            ->distinct()
            ->get();

            foreach ($perPOData as $PO) {
                $supCode = PO::where('PONumber', $PO->PO_NUMBER)->value('SupplierCode');
                $PO->POSupplierCode = $supCode; 

                $supplierDetails = Supplier::select('SupplierName','SupplierType','CompleteAddress')
                    ->where('SupplierCode', $supCode)
                    ->first(); 

                if ($supplierDetails) {
                    $PO->SupplierName = $supplierDetails->SupplierName;
                    $PO->SupplierType = $supplierDetails->SupplierType;
                    $PO->CompleteAddress = $supplierDetails->CompleteAddress;
                    
                }

                $RRItems = ReceivingRDetails::select('SKU','Quantity','UOM','WhsCode','UnitPrice','NetVat','Vat','Gross')
                    ->where('RRNo', $RRNum)
                    ->where('PO_Number',$PO->PO_NUMBER)
                    ->get();

                $PO->RRItems = $RRItems;
                
                foreach ($PO->RRItems as $productItem) {
                    $ProdDetails = Product::select('Description','StockUom','AlternateUom','OtherUom','ConvFactAltUom','ConvMulDiv','ConvFactOthUom')
                        ->where('StockCode', $productItem->SKU)
                        ->first();
                    if ($ProdDetails) {
                        // Add product details as key-value pairs to the current productItem
                        $productItem->Description = $ProdDetails->Description;
                        $productItem->StockUom = $ProdDetails->StockUom;
                        $productItem->AlternateUom = $ProdDetails->AlternateUom;
                        $productItem->OtherUom = $ProdDetails->OtherUom;
                        $productItem->ConvFactAltUom = $ProdDetails->ConvFactAltUom;
                        $productItem->ConvMulDiv = $ProdDetails->ConvMulDiv;
                        $productItem->ConvFactOthUom = $ProdDetails->ConvFactOthUom;
                    }
                }
            }

            if (count($perPOData) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receiving Report not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Receiving Report retrieved successfully',
                'data' => $perPOData,
                'success' => true,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);  // HTTP 500 Internal Server Error
        }
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
        //
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


    public function setRRNum(Request $request) {
        // Session::put('RRNum', $request->RRNum);
        Cache::put('RRNum', $request->RRNum, now()->addMinutes(1)); 

        $RRNum = Cache::get('RRNum');
        return response()->json([
            'success' => true,
            'originalData' => $request->RRNum,
            'sessionData' => $RRNum
        ]);
    }

    public function printPage()
    {
        try{
            $RRNum = Cache::get('RRNum');
            if($RRNum != null){
                $data = ReceivingRHeader::select('RRNo', 'Reference', 'RRDATE', 'Status', 'RECEIVEDBY', 'PO_NUMBER', 'Total')
                    ->with([
                        'rrdetails' => function ($query) {
                            $query->with(['product' => function ($productQuery) {
                                $productQuery->whereIn('StockCode', function ($subQuery) {
                                    $subQuery->selectRaw("CAST(SKU AS VARCHAR)") // Ensure SKU is treated as VARCHAR
                                        ->from('tblInvRRDetails');
                                });
                            }]);
                        },
                        'poincluded' => function ($query) {
                            $query->selectRaw('PONumber, TRIM(SupplierCode) as SupplierCode') // Fetch only required columns from PO
                                ->with(['posupplier' => function ($supplierQuery) {
                                    $supplierQuery->select('SupplierCode', 'SupplierName', 'CompleteAddress'); // Fetch required supplier details
                                }]);
                        }
                    ])
                    ->where('RRno', $RRNum)
                    ->first();
                    // Check if data is found before modifying it
                    if ($data) {
                        tap($data, function ($header) {
                            foreach ($header->rrdetails as $detail) {
                                if ($detail->product) {
                                    $uoms = array_map('strval', [
                                        $detail->product->StockUom, $detail->product->AlternateUom, $detail->product->OtherUom
                                    ]);

                                    $detail->convertedQuantity = app(ProductCalculator::class)->convertProductToLargesttUnit(
                                        $uoms,
                                        $detail->Quantity,
                                        $detail->product->ConvFactAltUom,
                                        $detail->product->ConvFactOthUom
                                    );
                                }
                            }
                        });
                    }

                return view('Pages.Printing.RR_printing', ['report' => $data]);
            };

            return view('Pages.receiving_report_page');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);  // HTTP 500 Internal Server Error
        }

        
    }

}
