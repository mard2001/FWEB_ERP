<?php

namespace App\Http\Controllers\api\Report;

use App\Http\Controllers\Controller;
use App\Models\SOMaster;
use Illuminate\Http\Request;

class SOMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = SOMaster::select(
                'SalesOrder',
                'NextDetailLine',
                'OrderStatus',
                'DocumentType',
                'Customer',
                'CustomerName',
                'Salesperson',
                'CustomerPoNumber',
                'OrderDate',
                'EntrySystemDate',
                'ReqShipDate',
                'DateLastDocPrt',
                'InvoiceCount',
                'Branch',
                'Warehouse',
                'ShipAddress1',
                'ShipToGpsLat',
                'ShipToGpsLong',
            )->get();
            
            if (count($data) == 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'No Sales Orders Data found',
                ], 200);  // HTTP 404 Not Found
            }

            return response()->json([
                'success' => true,
                'message' => 'Sales Orders Data retrieved successfully',
                'data' => $data
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
    public function show($salesorderID)
    {
        try {
            $salesorderID = (string) $salesorderID; 

            $data = SOMaster::select('SalesOrder', 'NextDetailLine', 'OrderStatus', 'DocumentType', 'Customer', 'CustomerName', 'Salesperson', 'CustomerPoNumber', 'OrderDate', 'EntrySystemDate', 'ReqShipDate', 'DateLastDocPrt', 'InvoiceCount', 'Branch', 'Warehouse', 'ShipAddress1', 'ShipToGpsLat', 'ShipToGpsLong')
                                ->where('SalesOrder', $salesorderID)
                                ->first();

            if ($data) {
                $details = $data->sodetails()->get()->toArray(); 
                $data->details = $details;

                return response()->json([
                    'success' => true,
                    'message' => 'Data Retrieved Successfully',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No Data Found'
                ]);
            }


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
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
}
