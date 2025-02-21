<?php

namespace App\Http\Controllers\api\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RRController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $dataset = [];

            for ($j = 1; $j <= 10; $j++) {
                $data = [
                    'title' => 'RR Printing ' . $j,
                    'dateToday' => now()->format('Y-m-d'),
                    'distName'=> 'FUI Shell',
                    'SupplierCode'=> 'VE-P000' . rand(10, 99),
                    'SupplierName'=> 'Shell Pilipinas Corporation',
                    'Address'=> 'Fort Bonifacio 1635 Taguig City NCR, Fourth District Philippines',
                    'SupplierTIN'=> '000-164-757-00000',
                    'RRNo'=> '16000007' . rand(10, 99),
                    'Date'=> 'Nov. 18, 2024',
                    'Reference'=> 'DN-512545212' . rand(400000000, 999999999),
                    'Status'=> 'Closed',
                    'Status2'=> 'Original', 
                    'PreparedBy'=> 'Marvin Navarro', 
                    'CheckedBy'=> 'Jhunrey Lucero', 
                    'ApprovedBy'=> 'Jhun Woogie Arrabis', 
                    'items' => []
                ];
            
                for ($i = 1; $i <=rand(10, 19); $i++) {
                    $data['items'][] = [
                        'SKU' => rand(100000000, 999999999),
                        'Description' => 'Sample Item Description' . $i,
                        'Quantity' => rand(10, 500),
                        'UOM' => ['CS', 'PC', 'IB'][array_rand(['CS', 'PC', 'IB'])],
                        'WhsCode'=> 'V' . rand(100, 999) . 'M' . rand(0, 9),
                        'UnitPrice' => round(rand(1000, 5000) + (rand(0, 99) / 100), 2),
                        'NetVat' => round(rand(5000, 500000) + (rand(0, 99) / 100), 2),
                        'Vat' => round(rand(500, 50000) + (rand(0, 99) / 100), 2),
                        'Gross' => round(rand(10000, 600000) + (rand(0, 99) / 100), 2)
                    ];
                }

                array_push($dataset,$data);
            }

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
            // $dataHeader = tblInvRRHeader::where('RRNo', $RRNum)->firstOrFail();
            // $dataDetails = tblInvRRDetails::where('RRNo', $RRNum)->get();
            // $dataHeader->items = $dataDetails;

            $data = [];

            $response = [
                'message' => 'Specific Receiving Report retrieved successfully',
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
}
