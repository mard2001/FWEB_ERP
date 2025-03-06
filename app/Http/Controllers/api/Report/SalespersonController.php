<?php

namespace App\Http\Controllers\api\Report;

use App\Http\Controllers\Controller;
use App\Models\Salesperson;
use Illuminate\Http\Request;

class SalespersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = [];
            $data = Salesperson::get();

            if (count($data) == 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'No Customer Data found',
                    'data' => $data
                ], 200);  // HTTP 404 Not Found
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Salesman Data retrieved successfully',
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
        try {
            $data = $request->data;
            Salesperson::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'New Salesman created successfully',
            ], 200);  // HTTP 200 OK

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);  // HTTP 500 Internal Server Error
        }
    }

    public function storeBulk(Request $request)
    {   
        $allSalesperson = $request->json()->all();
        $inserted = 0;
        $notInserted = 0;
        try {
            foreach ($allSalesperson as $SalespersonData) {
                $SalespersonData = array_map('trim', $SalespersonData);
                $InsertSalesPerson = Salesperson::firstOrCreate(['Salesperson' => $SalespersonData['Salesperson']],$SalespersonData);
                if ($InsertSalesPerson->wasRecentlyCreated) {
                    $inserted++;
                } else {
                    $notInserted++;
                }
            }
            if($notInserted > 0){
                $retval = 2;
            }
            else{
                $retval = 1;
            }
            return response()->json([
                'success' => true,
                'status_response' => $retval,
                'message' => 'Customers created successfully',
                'successful' => $inserted,
                'unsuccessful' => $notInserted,
                'totalFileLength' => count($allSalesperson),
                'data' => $SalespersonData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status_response' => 0,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            //code...
            $data = Salesperson::where('EmployeeID',$id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salesman not found',
                ], 404);
            }
            return response()->json([
                'message' => 'Salesman Details retrieved successfully',
                'data' => $data,
                'success' => true,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
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
        try {
            $data = $request['data'];
            $found = Salesperson::where('EmployeeID', $id)->first();

            if (!$found) {
                $response = [
                    'response' => 'data not found',
                    'success' => false
                ];

                //break to reserve server resouces
                return response()->json($response);
            }
            // dd($data, $found);
            $found->update($data);
            return response()->json([
                'success' => true,
                'message' =>  "Salesman updated successfully!",
                "data"=> $found
            ]);

        } catch (\Exception $e) {

            $response = [
                'message' => $e->getMessage(),
                'success' => false
            ];
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            
            $data = Salesperson::where('EmployeeID', $id)->first();

            if (!$data) {
                $response = [
                    'message' => 'Salesman not found',
                    'success' => false
                ];

                return response()->json($response);
            }

            $data->delete();

            $response = [
                'message' => 'Salesman deleted successfully!',
                'success' => true,
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => $e->getMessage(),
                'success' => 0
            ];
        }

        return response()->json($response);
    }
}
