<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sales Order Print</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>

    <style>
        /* Header (Company Name & Logo) */
        .rr-header {
            font-size: 1.3rem;
            /* Adjust between 14–18pt */
            font-weight: bold;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        .headerDetails{
            padding-bottom: 10px; 
            /* border-bottom: 2px solid #000 !important; */
        }

        .tablefooterDiv{
            /* border-top: 2px solid #000 !important; */
        }

        .so-info * {
            font-size: 10px;
            border: 1px solid #2e2e2e !important;
            table-layout: fixed; /* Ensures the columns respect assigned widths */
            width: 100%; /* Makes table responsive */
            height: 25px !important; /* Ensure at least 2 lines */
            white-space: normal;
            word-wrap: break-word;
            overflow: hidden;
            line-height: 10px;
        }

        .so-footer-info * {
            width: 8rem;
            font-size: 10px;
            border: none;
            height: 5px !important; /* Ensure at least 2 lines */
            overflow: hidden;
        }

        .so-footer-info tbody tr td {
            height: 5px !important; /* Ensure at least 2 lines */
            padding: 5px 0;
        }

        .so-footer-info tbody tr td:last-child {
            border-bottom: 1px solid #000;
        }

        .so-info tbody tr td{
            padding: .15rem .25rem;
        }

        .so-info thead tr:first-child {
            text-align: center;
            text-decoration: underline;
            padding: .15rem .25rem;
            height: 20px !important;
        }

        .footerText {
            width: 100%;
            text-align: center;
            background: white;
            padding: 10px 0;
            font-size: 12px;
            display: flex;
            flex-direction: column;
        }

        

        @media print {
            @page {
                margin: 0; /* Removes default browser print margins */
            }
            body {
                margin: 0;
                padding: 0 20px;
            }

            .header, .table-container {
                page-break-before: always;
            }
            hr {
                border: 1.5px solid black !important;
            }

            .footerTextLast {
                /* position: absolute; */
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                background: white;
                padding: 10px 0;
                font-size: 12px;
            }


            /* .billToDiv, .shipToDiv{
                border: 1.5px solid #696969;
                padding: 10px;
            } */

            .headBody{
                width: 100%;
                margin-top: 20px;
                text-align: center;
            }
            .headBody th {
                text-align: center;
                font-size: 9px;
            }

            .headBody td {
                padding: 5px;
                font-size: 10px;
                text-align: center;
                border: 1px solid #000000 !important;
                white-space: nowrap;
            }
        }
    </style>

    <body>
        @php
            $maxRowsPerPage = 17;  
            $rowCount = 0; 
            $totalPages = ceil(count($report->soitems)/$maxRowsPerPage);
            $pageNumber = 1;
            $totalAmount = 0;
        @endphp
        @foreach ($report->soitems as $index => $item)
            @if ($rowCount % $maxRowsPerPage == 0)
                @if ($rowCount > 0)
                    <div style="page-break-before: always;"></div>
                    @php
                        $pageNumber++;
                    @endphp
                @endif

                <header class="px-2 py-1">
                    <div class="d-flex flex-column mb-3">
                        <div class="p-2 pt-3">
                            <img src="https://jobslin.com/storage/logow/ph/FAST/fast-unimerchants-inc-1722319497.webp" alt="Description" width="80" height="30">
                        </div>
                        <div class="d-flex flex-row justify-content-between">
                            <div class="p-2">
                                <p class="m-0" style="font-size: 14px; font-weight:700">FAST DISTRIBUTION CORPORATION</p>
                                <p class="m-0" style="font-size: 9px;">H Abellana Street, Canduman, Mandaue City, Cebu, 6014</p>
                                <p class="m-0" style="font-size: 9px;">Tel. No. (032) 343-7888</p>
                                <p class="m-0" style="font-size: 9px;">Business Style: Wholesale and Retail Distribution Services</p>
                                <p class="m-0" style="font-size: 9px;"> VAT REG. TIN 485-010-749-00006</p>
                            </div>
                            <div >
                                <h3 class="fw-semibold">SALES ORDER</h3>
                                <div class="text-end">
                                    <p class="m-0" style="font-size: 9px;">Date: <span id="todaysDate">{{ $report->date }}</span></p>
                                    <p class="m-0" style="font-size: 9px;">Sales Order # <span id="SalesOrder">{{ $report->SalesOrder }}</span></p>
                                    <p class="m-0" style="font-size: 9px;">Order Date: <span id="OrderDate">{{ $report->OrderDate }}</span></p>
                                    <p class="m-0" style="font-size: 9px;">Ship Date: <span id="ReqShipDate">{{ $report->ReqShipDate }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around">
                        <div class="billToDiv">
                            <p class="m-0 fw-semibold" style="font-size: 14px;">Bill To:</p>
                            <hr class="m-0">
                            <p class="m-0" style="font-size: 11px;"><span id="billToCustomerName">{{ $report->CustomerName }}</span></p>
                            <p class="m-0" style="font-size: 11px;"><span id="billToShipAddress1">{{ $report->ShipAddress1 }}</span></p>
                            <p class="m-0" style="font-size: 11px;"><span id="billToCustomerContactNum">{{ $report->CustomerContactNum }}</span></p>
                        </div>
                        <div class="shipToDiv">
                            <p class="m-0 fw-semibold" style="font-size: 14px;">Ship To:</p>
                            <hr class="m-0">
                            <p class="m-0" style="font-size: 11px;"><span id="shipToCustomerName">{{ $report->CustomerName }}</span></p>
                            <p class="m-0" style="font-size: 11px;"><span id="shipToShipAddress1">{{ $report->ShipAddress1 }}</span></p>
                            <p class="m-0" style="font-size: 11px;"><span id="shipToCustomerContactNum">{{ $report->CustomerContactNum }}</span></p>
                        </div>
                    </div>
                    <div class="row headerDetails">
                        <div class="col-6">
                            <table class="headBody">
                                <thead>
                                    <tr>
                                        <th style="width:20%;">P.O. No.</th>
                                        <th style="width:30%;">SALES PERSON</th>
                                        <th style="width:20%;">PAYMENT</th>
                                        <th style="width:30%;">DELIVERY DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $report->CustomerPoNumber }}</td>
                                        <td>{{ $report->Salesperson }}</td>
                                        <td>{{ $report->Payment }}</td>
                                        <td>{{ $report->DeliveryDate }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="headBody">
                                <thead>
                                    <tr>
                                        <th style="width:30%;">SHIPPING METHOD</th>
                                        <th style="width:20%;">TERMS</th>
                                        <th style="width:20%;">SHIP VIA</th>
                                        <th style="width:30%;">SHIPPING DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>Land</td>
                                        <td>{{ $report->ReqShipDate }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </header>
                <table class="table so-info">
                    <thead>
                        <tr>
                            <th class="text-center"style="width: 5%;">No.</th>
                            <th class="text-center"style="width: 10%;">StockCode</th>
                            <th class="text-center"style="width: 10%;">Quantity</th>
                            <th class="text-center" style="width: 45%;">Description</th>
                            <th class="text-center" style="width: 15%;">UnitPrice</th>
                            <th class="text-center" style="width: 15%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif  
            <tr>
                <td class="text-center" style="width: 5%;">{{ $index+1 }}</td>  
                <td class="text-center" style="width: 10%;">{{ $item->MStockCode ?? '' }}</td>  
                <td class="text-center" style="width: 10%;">{{ $item->MOrderQty ?? '' }}</td>  
                <td class="text-start" style="width: 45%;">{{ $item->MStockDes ?? '' }}</td>  
                <td class="text-end" style="width: 15%;">{{ $item->MUnitCost ?? '' }}</td>  
                <td class="text-end" style="width: 15%;">{{ $item->MStockSubTotal ?? '' }}</td>  
            </tr>
            @php
                // $totalAmount += floatval($item->Gross);
                $rowCount++; // Increment row count
            @endphp
            @if ($rowCount % $maxRowsPerPage == 0)
                    </tbody>
                </table>
                <span style="font-size: 9px;">*Report continues on the next page...</span>
                @if ($pageNumber != $totalPages )
                    <div class="footerText" style="margin-top: 180px">
                        <span style="font-size: 8px">'THIS IS A SYSTEM-GENERATED DOCUMENT.'</span>
                        @php
                            echo "<div style='text-align: center; font-size: 7px'>Page {$pageNumber} of {$totalPages}</div>";
                        @endphp
                    </div>  
                @endif
            @endif
        @endforeach

        @if ($rowCount % $maxRowsPerPage != 0)
                </tbody>
            </table>
            
        @endif

        @if ($pageNumber == $totalPages)
            <div class="footerDiv d-flex justify-content-between">
                <span style="font-size:10px;">Remarks / Instructions:</span>
                <div class=" tablefooterDiv">
                    <table class="table so-footer-info">
                        <tbody>
                            <tr>
                                <td class="text-end fw-semibold" style="width: 60%;">Gross Sales</td>  
                                <td class="text-end" style="width: 40%; padding-right:5px;">30,146.46</td>  
                            </tr>
                            <tr> 
                                <td class="text-end fw-semibold" style="width: 60%;">VAT Amount</td>  
                                <td class="text-end" style="width: 40%; padding-right:5px;">3,229.98</td>  
                            </tr>
                            <tr>
                                <td class="text-end fw-semibold" style="width: 60%;">Net Sales</td>  
                                <td class="text-end" style="width: 40%; padding-right:5px;">26,916.48</td>  
                            </tr>
                            <tr>
                                <td class="text-end fw-semibold" style="width: 60%;">Subtotal</td>  
                                <td class="text-end" style="width: 40%; padding-right:5px;">26,916.48</td>  
                            </tr>
                            <tr>
                                <td class="text-end fw-semibold" style="width: 60%;">VAT Added</td>  
                                <td class="text-end" style="width: 40%; padding-right:5px;">3,229.98</td>  
                            </tr>
                            <tr>
                                <td class="text-end fw-bold" style="width: 60;">Total Amount Payable</td>  
                                <td class="text-end fw-bold" style="width: 40%; padding-right:5px;">30,146.46</td>  
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @php
                $remainingRows = (($maxRowsPerPage*$pageNumber)-$rowCount)-2; 
                $maxMargin = $remainingRows*28;
                echo '<div style="height: '.$maxMargin.'px !important"></div>';
            @endphp

            <div class="footerText" style="margin-top: 20px">
                <span style="font-size: 8px">'THIS IS A SYSTEM-GENERATED DOCUMENT.'</span>
                @php
                    echo "<div style='text-align: center; font-size: 7px'>Page {$pageNumber} of {$totalPages}</div>";
                @endphp
            </div>  
        @endif   
    </body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    {{-- <script src="{{ asset('assets/js/printing/rrPrintJS.js') }}"></script> --}}


    <script>
        window.onload = function() {
            window.print();

            // Close the tab after printing or if the user cancels
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</html>