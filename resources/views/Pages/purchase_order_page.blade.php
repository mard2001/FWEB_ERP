@extends('Layout.layout')

@section('html_title')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<title>Purchase Order</title>
@endsection

@section('title_header')
<x-header title="Purchase Order" />
@endsection

@section('table')
<style>
    .secBtns .selected {
        background-color: rgba(23, 162, 184, 0.10);
        border-bottom: 2px solid #0275d8;
    }

    .secBtns button {
        border-bottom: 2px solid transparent;
        border-top: 1px solid transparent;
        border-left: 1px solid transparent;
        border-right: 1px solid transparent;
    }

    .secBtns button:hover {
        background-color: rgba(23, 162, 184, 0.10);
        border-bottom: 2px solid #0275d8;
        border-top: 0.5px solid #0275d8;
        border-left: 0.5px solid #0275d8;
        border-right: 0.5px solid #0275d8;
    }

    .autocompleteHover:hover {
        background-color: #3B71CA;
        cursor: pointer;
    }

    .ui-autocomplete {
        z-index: 9999 !important;
    }

    .fs15 * {
        font-size: 15px !important;
    }

    .fs12 * {
        font-size: 12px !important;
        border-radius: 0 !important;
    }

    .fs12 .actIcon * {
        font-size: 20px !important;
    }

    .ResMWidth {
        width: 300px;
    }

    /* @media (min-width: 768px) {
        .ResMWidth {
            width: 30%;
        }

        .fs12 * {
            font-size: 15px !important;
            border-radius: 0 !important;
        }
    } */

    /* Extra small devices (phones, 0px - 575px) */
    @media (max-width: 575px) {
        .ResMWidth {
            width: 100%;
        }

        .fs12 * {
            font-size: 12px !important;
            border-radius: 0 !important;
        }
    }

    /* Small devices (phones, 576px - 767px) */
    @media (min-width: 576px) and (max-width: 767px) {
        .ResMWidth {
            width: 45%;
        }

        .fs12 * {
            font-size: 13px !important;
            border-radius: 0 !important;
        }
    }

    /* Medium devices (tablets, 768px - 991px) */
    @media (min-width: 768px) and (max-width: 991px) {
        .ResMWidth {
            width: 40%;
        }

        .fs12 * {
            font-size: 13px !important;
            border-radius: 0 !important;
        }
    }

    /* Large devices (desktops, 992px - 1199px) */
    @media (min-width: 992px) and (max-width: 1199px) {
        .ResMWidth {
            width: 35%;
        }

        .fs12 * {
            font-size: 14px !important;
            border-radius: 0 !important;
        }
    }

    /* Extra large devices (large desktops, 1200px and up) */
    @media (min-width: 1200px) {
        .ResMWidth {
            width: 30%;
        }

        .fs12 * {
            font-size: 14px !important;
            border-radius: 0 !important;
        }
    }

    @media (max-width: 992px) {
        .custom-modal-fullscreen {
            width: 100%;
            height: 100%;
            margin: 0;
            max-width: 100%;
        }

        .custom-modal-fullscreen .modal-content {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
    }
</style>

<x-table id="POHeaderTable">
    <x-slot:td>
        <td class="col">OrderNumber</td>
        <td class="col">PONumber</td>
        <td class="col">Status</td>
        <td class="col">POAccount</td>
        <td class="col">PODate</td>
        <td class="col">OrderPlacer</td>
        <td class="col">Discount</td>
        <td class="col">TotalCost</td>

    </x-slot:td>
</x-table>

@endsection

@section('modal')
<x-po_modal>
    <x-slot:form_fields>
        <div class="row justify-content-between d-none">

            <div class="col-5">
                <textarea class="form-control" id="exampleFormControlTextarea1 rounded-0" placeholder="Address" rows="2" style="resize: none;"></textarea>
                <input type="text" disabled id="mobile" name="mobile"
                    class="form-control form-control-sm bg-white rounded-0" required placeholder="Mobile">
            </div>

            <div class="col-5 text-end">
                <input type="text" disabled id="date" name="date" readonly
                    class="form-control form-control-sm bg-white rounded-0" required placeholder="Date">

                <input type="text" disabled id="poNumber" name="poNumber"
                    class="form-control form-control-sm bg-white rounded-0" required placeholder="PO #">
            </div>
        </div>

        <div class="d-flex justify-content-between flex-wrap mt-2 fs12">
            <div class="my-sm-2 ResMWidth">
                <div class="bg-primary p-1 d-flex align-items-center text-white" style="font-size: 14px;">
                    VENDOR
                </div>
                <div id="vendorName" name="vendorName" required class="form-control bg-white p-0 border-0">
                    Vendor Name
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">Contact:</span>
                    <input type="text" disabled id="VendorContactName" name="VendorContactName" readonly required class="form-control bg-white" placeholder="Contact Name">
                </div>
                <label id="VendorContactName-error" class="error d-block" for="VendorContactName"></label>

                <textarea class="form-control px-2" id="vendorAddress" readonly required placeholder="Vendor Address" rows="2" style="resize: none;"></textarea>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">Phone:</span>
                    <input type="text" disabled id="vendorPhone" name="vendorPhone" required readonly class="form-control bg-white" placeholder="Phone">
                </div>
                <label id="vendorPhone-error" class="error d-block" for="vendorPhone"></label>

            </div>

            <div class="my-sm-2 ResMWidth">
                <div class="bg-primary p-1 d-flex align-items-center text-white" style="font-size: 14px;">
                    SHIP TO
                </div>

                <div id="shippedToName" name="shippedToName" required class="form-control bg-white p-0 border-0">
                    Shipper Name
                </div>

                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">Contact:</span>
                    <input type="text" disabled id="shippedToContactName" required name="SupplierContactName" class="form-control bg-white" placeholder="Contact Name">
                </div>
                <label id="shippedToContactName-error" class="error d-block" for="shippedToContactName"></label>

                <textarea class="form-control px-2" id="shippedToAddress" required placeholder="Shipped To Address" rows="2" style="resize: none;"></textarea>
                <label id="shippedToAddress-error" class="error d-block" for="shippedToAddress"></label>

                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">Phone:</span>
                    <input type="text" disabled id="shippedToPhone" required name="shippedToPhone" class="form-control bg-white" placeholder="Phone">
                </div>
                <label id="shippedToPhone-error" class="error d-block" for="shippedToPhone"></label>

            </div>

        </div>

        <div class="row d-flex flex-wrap mt-1 fs12">
            <div class="col pe-0 text-center text-white">
                <label for="requisitioner" class="w-100 border-0 bg-primary py-1">REQUISITIONER</label>
                <input type="text" disabled id="requisitioner" name="requisitioner"
                    class="form-control form-control-sm bg-white py-2 rounded-0" required>

            </div>
            <div class="col px-0 text-center text-white">
                <label for="shipVia" class="w-100 border-0 bg-primary py-1">SHIP VIA</label>


                <div id="shipVia" name="shipVia" class="form-control bg-white p-0 border-0">
                    Shipper Name
                </div>

            </div>

            <div class="col px-0 text-center text-white">
                <label for="fob" class="w-100 border-0 bg-primary py-1">F.O.B.</label>
                <input type="text" disabled id="fob" name="fob"
                    class="form-control form-control-sm bg-white py-2 rounded-0" required>
            </div>

            <div class="col ps-0 text-center text-white">
                <label for="shippingTerms" class="w-100 border-0 bg-primary py-1 rounded-0">SHIPPING TERMS</label>
                <input type="text" disabled id="shippingTerms" name="shippingTerms"
                    class="form-control form-control-sm bg-white py-2" required>
            </div>
        </div>

        <div class="row mt-2">
            <div class="d-flex align-items-center px-2 fs12">

                <div>
                    <button type="button" class="btn btn-primary btn-sm text-white mx-1" disabled id="addItems">Add Item</button>
                    <!-- <button type="button" class="btn btn-danger btn-sm text-white mx-1" id="itemDelete" disabled>Delete Item</button> -->
                </div>
            </div>

            <x-sub_table id="itemTables" class="fs12 table-bordered">
                <x-slot:td>
                    <td class="col">StockCode</td>
                    <td class="col">Decription</td>
                    <td class="col">Quantity</td>
                    <td class="col">UOM</td>
                    <td class="col">Unit Price</td>
                    <td class="col">Total Price</td>
                    <!-- <td class="col">Action</td> -->
                    <td class="col text-center">
                        Action
                    </td>

                </x-slot:td>
            </x-sub_table>

            <!-- <div class="d-flex justify-content-between w-100 h-100 fs12 px-4">
                <div class="col-7">
                    <div class="row me-0 w-100 bg-info text-center py-1">
                        <div class="col text-center">
                            Comments or Special Instructions
                        </div>
                    </div>
                    <div class="row me-0">
                        <textarea class="form-control px-2 h-100 w-100" id="poComment" rows="4" style="resize: none;"></textarea>
                    </div>
                </div>

                <div class="col-5">
                    <div class="row">
                        <div class="col-5 px-0">
                            <span class="input-group-text bg-white px-2 py-1">SUBTOTAL:</span>
                        </div>

                        <div class="col-7 px-0">
                            <input type="text" disabled id="subTotal" name="subTotal" class="form-control bg-white px-2 py-1 text-end" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5 px-0">
                            <span class="input-group-text bg-white px-2 py-1">TAX:</span>
                        </div>

                        <div class="col-7 px-0">
                            <input type="text" disabled id="taxCost" name="taxCost" class="form-control bg-white px-2 py-1 text-end" readonly>
                        </div>
                    </div>

                    <div class="row d-none">
                        <div class="col-5 px-0">
                            <span class="input-group-text bg-white px-2 py-1">SHIPPING:</span>
                        </div>

                        <div class="col-7 px-0">
                            <input type="text" disabled id="shippingCost" name="shippingCost" class="form-control bg-white px-2 py-1 text-end" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-5 px-0">
                            <span class="input-group-text bg-white px-2 py-1">OTHER:</span>
                        </div>

                        <div class="col-7 px-0">
                            <input type="text" disabled id="others" name="others" class="form-control bg-white px-2 py-1 text-end" readonly>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-5 px-0">
                            <span class="input-group-text bg-white px-2 py-1" style="font-weight: bold;">TOTAL:</span>
                        </div>

                        <div class="col-7 px-0">
                            <input type="text" disabled id="grandTotal" name="grandTotal" class="form-control bg-white px-2 py-1 text-end" readonly
                                style="font-weight: bold;">
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="row mt-2 mx-0">
            <table class="table table-bordered fs12">
                <tbody>
                    <tr>
                        <td class="col-9 text-center bg-info"> Comments or Special Instructions
                        </td>
                        <td class="col">SUB TOTAL: </td>
                        <td id="subTotal" class="col text-end"></td>
                    </tr>
                    <tr>
                        <td rowspan="3" class="p-0">
                            <textarea class="form-control px-2 h-100 w-100" id="poComment" rows="5" style="resize: none;"></textarea>

                        </td>
                        <td>TAX: </td>
                        <td id="taxCost" class="text-end"></td>
                    </tr>
                    <tr class="d-none">
                        <td>OTHER: </td>
                        <td id="others" class="text-end"></td>
                    </tr>
                    <tr>
                        <td>TOTAL ITEM: </td>
                        <td id="totalItemsLabel" class="text-end"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">TOTAL: </td>
                        <td id="grandTotal" style="font-weight: bold;" class="text-end"></td>
                    </tr>
                </tbody>

            </table>

        </div>

    </x-slot:form_fields>
</x-po_modal>

<div class="modal fade modal modal-lg text-dark" id="itemModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 h-100">
            <div class="modal-body" style="height: auto; max-height: 75vh;">
                <form id="itemModalFields">
                    <div class="row h-100">
                        <div class="d-flex justify-content-between">
                            <div class="col">
                                <div class="px-1 py-0 w-100">

                                    <label for="StockCode">StockCode</label>

                                    <div id="StockCode" name="StockCode" class="form-control bg-white p-0 w-100"
                                        required placeholder="StockCode">
                                        <span class="loader d-flex align-self-center" style="height: 15px; width: 15px"></span>
                                    </div>
                                </div>

                                <div class="px-1 py-0 w-100">

                                    <label for="Decription">Decription</label>
                                    <input disabled type="text" id="Decription" name="Decription" class="form-control bg-white rounded-0"
                                        required placeholder="Decription">

                                </div>

                                <div class="d-flex justify-content-between">
                                    <div class="px-1 py-0 w-50 rounded-0">
                                        <label for="PricePerUnit">Price Per Unit</label>
                                        <input disabled type="text" id="PricePerUnit" name="PricePerUnit" class="form-control bg-white rounded-0"
                                            permit-fs required placeholder="Price Per Unit" readonly>

                                    </div>

                                    <div class="px-1 py-0 w-50">
                                        <label for="TotalPrice">Total Price</label>
                                        <input disabled type="text" id="TotalPrice" name="TotalPrice" class="form-control bg-white rounded-0"
                                            required placeholder="Total Price" readonly>
                                    </div>
                                </div>

                            </div>

                            <div class="col">
                                <div class="row mx-1 UOMField" id="CSDiv">
                                    <div class="px-1 py-0 col">
                                        <label for="CSQuantity">CS Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text w-25 rounded-0">CS</span>
                                            <input disabled type="number" id="CSQuantity" name="CSQuantity" class="form-control bg-white rounded-0"
                                                required>
                                        </div>
                                        <label id="CSQuantity-error" class="error px-1" for="CSQuantity"></label>
                                    </div>
                                </div>

                                <div class="row mx-1 UOMField" id="IBDiv">
                                    <div class="px-1 py-0 col">
                                        <label for="IBQuantity">IB Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text w-25 rounded-0">IB</span>
                                            <input disabled type="number" id="IBQuantity" name="IBQuantity" class="form-control bg-white rounded-0"
                                                required>
                                        </div>
                                    </div>
                                    <label id="IBQuantity-error" class="error px-1" for="IBQuantity"></label>

                                </div>

                                <div class="row mx-1 UOMField" id="PCDiv">
                                    <div class="px-1 py-0 col">
                                        <label for="PCQuantity">PC Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text w-25 rounded-0">PC</span>
                                            <input disabled type="number" id="PCQuantity" name="PCQuantity" class="form-control bg-white rounded-0"
                                                required>
                                        </div>
                                        <label id="PCQuantity-error" class="error px-1" for="PCQuantity"></label>

                                    </div>
                                </div>


                            </div>
                            <!-- <div class="col">
                                <div class="row px-2 UOMField" id="CSDiv">
                                    <div class="col-5"></div>
                                    <div class="px-1 py-0 w-50" style="border-radius: 0;">

                                                                            <div class="form-control bg-white rounded-0">CS</div>

                                    </div>

                                    <div class="px-1 py-0 w-50">
                                        <label for="Quantity">Quantity</label>
                                        <input type="number" id="CSQuantity" name="Quantity" class="form-control bg-white"
                                            required placeholder="Quantity" style="border-radius: 0;">
                                    </div>
                                </div>

                                <div class="row px-2 UOMField" id="IBDiv">
                                    <div class="px-1 py-0 w-50" style="border-radius: 0;">

                                        
                                        <div class="form-control bg-white rounded-0"
                                            required placeholder="UOMDropDown"> <input disabled type="number" class="form-control bg-white border-0"
                                                required placeholder="IB" style="border-radius: 0;" readonly></div>

                                    </div>

                                    <div class="px-1 py-0 w-50">
                                        <label for="IB">Quantity</label>
                                        <input type="number" id="IBQuantity" name="Quantity" class="form-control bg-white"
                                            required placeholder="Quantity" style="border-radius: 0;">
                                    </div>
                                </div>


                                <div class="row px-2 UOMField" id="PCDiv">
                                    <div class="px-1 py-0 w-50" style="border-radius: 0;">

                                        
                                        <div class="form-control bg-white rounded-0"
                                            required placeholder="UOMDropDown"> <input disabled type="number" class="form-control bg-white border-0"
                                                required placeholder="PC" style="border-radius: 0;" readonly></div>

                                    </div>

                                    <div class="px-1 py-0 w-50">
                                        <label for="Quantity">Quantity</label>
                                        <input type="number" id="PCQuantity" name="Quantity" class="form-control bg-white"
                                            required placeholder="Quantity" style="border-radius: 0;">
                                    </div>
                                </div>


                            </div> -->

                        </div>
                        <!-- 
                        <div class="d-flex justify-content-between">
                            <div class="d-flex justify-content-between px-3 px-sm-0 w-50">

                            </div>


                            <div class="d-flex w-50 justify-content-between px-2">

                            </div>
                        </div> -->
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm text-white" id="itemSave">Save Item</button>
                <button type="button" class="btn btn-info btn-sm text-white" id="itemEdit">Edit Item</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal modal-lg text-dark" id="newVendorModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 h-100">
            <div class="modal-body" style="height: auto; max-height: 75vh;">
                <form id="newVendorForm">
                    <div class="row">
                        <div class="col d-flex flex-column">

                            <input type="text" id="SupplierType" name="SupplierType" class="form-control bg-white mt-2 rounded-0"
                                required placeholder="Supplier Type">


                            <input type="text" id="SupplierName" name="SupplierName" class="form-control bg-white mt-2 rounded-0"
                                required placeholder="Supplier Name">

                            <input type="text" id="TermsCode" name="TermsCode" class="form-control bg-white mt-2 rounded-0"
                                required placeholder="Terms Code">


                            <input type="text" id="ContactPerson" name="ContactPerson" class="form-control bg-white mt-2 rounded-0"
                                required placeholder="Contact Person">

                            <input type="text" id="ContactNo" name="ContactNo" class="form-control bg-white mt-2 rounded-0"
                                required placeholder="ContactNo">

                        </div>
                        <div class="col d-flex flex-column">

                            <div id="Region" name="Region" class="form-control bg-white p-0 w-100 mt-2 rounded-0"
                                required placeholder="Region"><input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="Region" readonly></div>

                            <div id="Province" name="Province" class="form-control bg-white p-0 w-100 mt-2 rounded-0"
                                required placeholder="Province"><input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="Province" readonly> </div>

                            <div id="CityMunicipality" name="CityMunicipality" class="form-control bg-white p-0 w-100 mt-2 rounded-0"
                                required placeholder="City"><input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="City / Municipality" readonly> </div>

                            <div id="Barangay" name="Barangay" class="form-control bg-white p-0 w-100 mt-2 rounded-0"
                                required placeholder="Barangay"> <input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="Barangay" readonly> </div>


                            <textarea class="form-control mt-2 rounded-0" id="NVCompleteAddress" placeholder="Address" rows="2" style="resize: none;"></textarea>



                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm text-white px-3" id="newVendorSaveBtn">Save</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<x-slot:uploader_modal>

</x-slot:uploader_modal>
@endsection

@section('pagejs')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<script type="text/javascript" src="{{ asset('assets/js/vendor/virtual-select.min.js') }}"></script>
<script type="module" src="{{ asset('assets/js/PH_Address/virtualSelectAddresses.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/maintenance_uploader/purchase_order-v2.js') }}"></script>

<script>
    $(document).ready(async function() {
        $("#uploadBtn").on("click", async function() {
            //validate all files if csv file and to fileList
            var appendTable = '';
            var file_data = $('#formFileMultiple').prop('files');

            var pdfOnly = true;
            for (var i = 0; i < file_data.length; i++) {

                appendTable += trNew(file_data[i].name, i);
                var fileExtension = file_data[i].name.split('.').pop().toLowerCase();

                if (!fileExtension == 'pdf') {
                    pdfOnly = false;
                    break;
                }
            }

            if (pdfOnly && file_data.length > 0) {

                $('#fileListTable').html(appendTable);
                $('#totalFiles').html(file_data.length);
                $('#totalFile').html(file_data.length);


                for (var i = 0; i < file_data.length; i++) {

                    var fileFormData = new FormData();
                    fileFormData.append('pdf_file', file_data[i]);

                    var convertedtoString = await readpdf(file_data[i]);
                    var retrievedUser = JSON.parse(localStorage.getItem('dbcon'));

                    fileFormData.append('conn', JSON.stringify(retrievedUser));
                    fileFormData.append('extractedString', convertedtoString);


                    // Call async API function and process response
                    var response = await ajaxCall('POST', fileFormData);

                    var iconResult = `<span class="mdi mdi-alert-circle text-danger resultIcon"></span>`;
                    var insertedResultColor = `text-danger`;

                    if (response.subTotal_response == 1) {
                        iconResult = `<span class="mdi mdi-check-circle text-success resultIcon"></span>`
                        var incrementSuccess = parseInt($('#totalUploadSuccess').val(), 10) || 0; // Parse the value as an integer, default to 0 if NaN
                        incrementSuccess++;

                        $('#totalUploadSuccess').val(incrementSuccess);
                        $('#totalUploadSuccess').text(incrementSuccess);

                        insertedResultColor = 'text-success';


                    }
                    if (response.subTotal_response == 2) {

                        iconResult = `<span class="mdi mdi-alert-circle text-warning resultIcon"></span>`
                        insertedResultColor = 'text-warning';
                    }

                    $("#filesubTotal" + i).html(iconResult); // Use i here to update the correct element
                    // $("#insertedStat" + i).html(`${response.total_inserted} / ${response.tatal_entry}`).addClass(insertedResultColor);

                    if (i == file_data.length - 1) {
                        $('#formFileMultiple').val('');

                        var allResultIcon = $('#fileListTable').find('.resultIcon');
                        var swal = {
                            title: "Success!",
                            text: 'All data successfully Inserted',
                            icon: "success"
                        };

                        allResultIcon.each(function(index, element) {
                            // console.log($(element).attr('class'));

                            if (!$(element).hasClass('text-success')) {
                                console.log('fail ' + $(element).attr('class'));

                                swal = {
                                    title: "Warning!",
                                    text: 'Not all data inserted.\nReview uploaded pdf content',
                                    icon: "warning"
                                };

                                return false;
                            } else {
                                console.log('passed ' + $(element).attr('class'));

                            }

                        });


                        Swal.fire(swal);
                        getAllXmlData();


                    }

                }

            } else {
                Swal.fire({
                    icon: "error d-block",
                    title: "Review files",
                    text: "Please select csv files only",
                });

            }
        });


        async function readpdf(file) {

            try {
                const fileReader = new FileReader();

                const typedArray = await new Promise((resolve, reject) => {
                    fileReader.onload = () => resolve(new Uint8Array(fileReader.result));
                    fileReader.onerror = reject;
                    fileReader.readAsArrayBuffer(file);
                });

                const pdf = await pdfjsLib.getDocument(typedArray).promise;
                let pdfText = '';

                for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                    const page = await pdf.getPage(pageNumber);
                    const textContent = await page.getTextContent();

                    textContent.items.forEach(textItem => {
                        if (textItem.str.trim()) { // Skip empty strings
                            pdfText += textItem.str + '\n';
                        }
                    });
                }

                return pdfText;
            } catch (error) {
                console.error('Error occurred:', error);
                return '';
            }

        }

        async function ajaxCall(method, formData = null) {

            return await $.ajax({
                url: globalApi + 'api/upload-po-pdf',
                type: method,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token')
                },
                processData: false, // Required for FormData
                contentType: false, // Required for FormData
                data: formData, // Convert the data to JSON format
                // data: xmlJson, // Convert the data to JSON format

                success: async function(response) {
                    console.log(response);

                    if (response.subTotal_response != 1) {

                        //console.log(JSON.stringify(response, null, 2));
                        //console.log(response.extracted_text);



                    }

                    //console.log(response);
                    return response;

                },
                error: async function(xhr, subTotal, error) {


                    Swal.fire({
                        icon: "error d-block",
                        title: "Api Error",
                        text: xhr.responseJSON?.message || xhr.statusText,

                    });

                    console.log(xhr, subTotal, error)

                    return xhr, subTotal, error;
                }
            });
        }

        async function loadPdfText(pdfUrl) {
            try {
                const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
                let pdfText = '';

                for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                    const page = await pdf.getPage(pageNumber);
                    const textContent = await page.getTextContent();

                    textContent.items.forEach(textItem => {
                        if (textItem.str.charCodeAt(0) !== 32) {
                            pdfText += textItem.str + '\n';
                        }
                    });
                }

                //console.log(pdfText);
                return pdfText;
            } catch (error) {
                console.error('Error occurred:', error);
                return false;
            }
        }




    });
</script>

@endsection