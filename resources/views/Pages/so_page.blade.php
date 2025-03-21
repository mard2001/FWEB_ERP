@extends('Layout.layout')

@section('html_title')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Sales Order</title>
@endsection

@section('title_header')
    <x-header title="Sales Order" />
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
        font-size: 15px;
    }

    .dtDetailssearchInput{
        font-size: 10px;
    }

    .dtDetailssearchLabel{
        background-color: #33336F;
        color: #FFF !important;
        font-size: 10.5px !important;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

</style>

<x-table id="soTable">
    <x-slot:td>
        <td class="SalesOrder">SalesOrder</td>
        <td class="OrderStatus">OrderStatus</td>
        <td class="DocumentType">DocumentType</td>
        <td class="Customer">Customer</td>
        <td class="CustomerName">CustomerName</td>
        <td class="CustomerPoNumber">CustomerPoNumber</td>
        <td class="OrderDate">OrderDate</td>
        <td class="Branch">Branch</td>
        <td class="Warehouse">Warehouse</td>
        <td class="ShipAddress1">ShipAddress1</td>
        <td class="ShipToGpsLat">ShipToGpsLat</td>
        <td class="shipToGpsLong">shipToGpsLong</td>
    </x-slot:td>
</x-table>
@endsection




@section('modal')

<style>
    #editXmlDataModal .modal-dialog {
        width: 70vw !important;
        /* Set width to 90% of viewport width */
        max-width: none !important;
        /* Remove any max-width constraints */
    }

    #editXmlDataModal .modal-content {
        margin: auto !important;
        /* Center the modal content */
    }

    .soTableHeader tbody tr:hover{
        background: transparent
    }

    #SODetails th {
        white-space: nowrap; /* Prevents text from wrapping */
    }

    #itemTables_wrapper #dt-search-1{
        height: 10px;
    }

</style>

<x-so_modal>
    <x-slot:form_fields>
        <div class="row g-4">
            <div class="col-6" style="display: none">
                <div class="mt-2 fs12">
                    <div class="my-sm-2 ResMWidth">
                        <div class="bg-primary p-1 d-flex align-items-center text-white" style="font-size: 14px;">
                            VENDOR
                        </div>
                        <div id="vendorName" name="vendorName" class="form-control bg-white p-0 border-0">
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
                </div>
            </div>
            <div class="col-6">
                <div class=" mt-2 fs12">
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
                    <button type="button" class="btn btn-primary btn-sm text-white mx-1" id="addItems">Add Item</button>
                    <!-- <button type="button" class="btn btn-danger btn-sm text-white mx-1" id="itemDelete" disabled>Delete Item</button> -->
                </div>
            </div>

            <x-sub_table id="itemTables" class="fs12 table-bordered">
                <x-slot:td>
                    <td class="col">StockCode</td>
                    <td class="col">Description</td>
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
        </div>

        <div class="row mt-2 mx-0">
            <table class="table table-bordered fs12">
                <tbody>
                    <tr>
                        <td class="col-9 text-center bg-info"> Comments or Special Instructions</td>
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

    {{-- <x-slot:form_fields>
        <div class="row g-4">
            <div class="col">
                <table class="soTableHeader" style="font-size: 14px">
                    <tbody>
                        <tr>
                            <td style="white-space: nowrap;">Branch:</td>
                            <th class="px-2"><span id="Branch" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Warehouse:</td>
                            <th class="px-2"><span id="Warehouse" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Customer ID:</td>
                            <th class="px-2"><span id="Customer" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Customer Name:</td>
                            <th class="px-2"><span id="CustomerName" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Address:</td>
                            <th class="px-2"><span id="ShipAddress1" style="font-weight: 550"></span></th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col ">
                <table class="soTableHeader" style="font-size: 14px">
                    <tbody>
                        <tr>
                            <td style="white-space: nowrap;">Sales Order:</td>
                            <th class="px-2"><span id="SalesOrder" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Order Status:</td>
                            <th class="px-2"><span id="OrderStatus" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Order Date:</td>
                            <th class="px-2"><span id="OrderDate" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Req. Ship Date:</td>
                            <th class="px-2"><span id="ReqShipDate" style="font-weight: 550"></span></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <table class="table" style="font-size: 12px; width: 100%;" id="SODetails"></table>
    </x-slot:form_fields> --}}
</x-so_modal>

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
                                    <div id="StockCode" name="StockCode" class="form-control bg-white p-0 w-100">
                                        <span class="loader d-flex align-self-center" style="height: 15px; width: 15px"></span>
                                    </div>
                                </div>
                                <div class="px-1 py-0 w-100">
                                    <label for="Decription">Decription</label>
                                    <input disabled type="text" id="Decription" name="Decription" class="form-control bg-white rounded-0" required placeholder="Decription">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="px-1 py-0 w-50 rounded-0">
                                        <label for="PricePerUnit">Price Per Unit</label>
                                        <input disabled type="text" id="PricePerUnit" name="PricePerUnit" class="form-control bg-white rounded-0" permit-fs required placeholder="Price Per Unit" readonly>
                                    </div>
                                    <div class="px-1 py-0 w-50">
                                        <label for="TotalPrice">Total Price</label>
                                        <input disabled type="text" id="TotalPrice" name="TotalPrice" class="form-control bg-white rounded-0" required placeholder="Total Price" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="row mx-1 UOMField" id="CSDiv">
                                    <div class="px-1 py-0 col">
                                        <label for="CSQuantity">CS Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text w-25 rounded-0">CS</span>
                                            <input disabled type="number" id="CSQuantity" name="CSQuantity" class="form-control bg-white rounded-0" required min="0">
                                            <div class="w-25 d-flex justify-content-evenly align-items-center">
                                                <i class="text-danger fa-solid fa-minus"></i>
                                                <i class="text-primary fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                        <label id="CSQuantity-error" class="error px-1" for="CSQuantity"></label>
                                    </div>
                                </div>

                                <div class="row mx-1 UOMField" id="IBDiv">
                                    <div class="px-1 py-0 col">
                                        <label for="IBQuantity">IB Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text w-25 rounded-0">IB</span>
                                            <input disabled type="number" id="IBQuantity" name="IBQuantity" class="form-control bg-white rounded-0" required min="0">
                                            <div class="w-25 d-flex justify-content-evenly align-items-center">
                                                <i class="text-danger fa-solid fa-minus"></i>
                                                <i class="text-primary fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <label id="IBQuantity-error" class="error px-1" for="IBQuantity"></label>

                                </div>

                                <div class="row mx-1 UOMField" id="PCDiv">
                                    <div class="px-1 py-0 col">
                                        <label for="PCQuantity">PC Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text w-25 rounded-0">PC</span>
                                            <input disabled type="number" id="PCQuantity" name="PCQuantity" class="form-control bg-white rounded-0" required min="0">
                                            <div class="w-25 d-flex justify-content-evenly align-items-center">
                                                <i class="text-danger fa-solid fa-minus"></i>
                                                <i class="text-primary fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                        <label id="PCQuantity-error" class="error px-1" for="PCQuantity"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm text-white" id="itemSave">Save Item</button>
                <button type="button" class="btn btn-info btn-sm text-white" id="itemEdit">Edit Item</button>
                <button type="button" class="btn btn-secondary btn-sm" id="itemCloseBtn">Close</button>
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

                            <div id="Region" name="Region" class="form-control bg-white p-0 w-100 mt-2 rounded-0">
                                <input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="Region" readonly>
                            </div>

                            <div id="Province" name="Province" class="form-control bg-white p-0 w-100 mt-2 rounded-0">
                                <input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="Province" readonly>
                            </div>

                            <div id="CityMunicipality" name="CityMunicipality" class="form-control bg-white p-0 w-100 mt-2 rounded-0">
                                <input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="City / Municipality" readonly>
                            </div>

                            <div id="Barangay" name="Barangay" class="form-control bg-white p-0 w-100 mt-2 rounded-0">
                                <input disabled type="text" class="form-control bg-white rounded-0"
                                    required placeholder="Barangay" readonly>
                            </div>
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

<div class="modal fade modal-lg" id="uploadCsv">
    <div class="modal-dialog">
        <div class="modal-content w-100">
            <div class="modal-body h-100">
                <form>
                    <div class="row h-100">
                        <div id="uploaderDiv">
                            <div class="upload-container">
                                <input class="form-control p-2" type="file" id="formFileMultiple" multiple>
                            </div>
                            <div id="uploadStatus" class="upload-status">
                                <div class="d-flex">
                                    <div class="col-10">
                                        <span style="font-size: 16px;">Uploaded files (<span id="totalFiles"
                                                class="text-primary">0</span></span>)
                                    </div>
                                    <div style="font-size: 14px;" class="col-2 text-end px-3">
                                        <span id="totalUploadSuccess">0</span>
                                        /
                                        <span id="totalFile">0</span>
                                    </div>
                                </div>
                                <hr class="my-1">

                                <div id="fileListDiv" class="p-1">
                                    <table class="table fs-6">
                                        <tbody id="fileListTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button id="uploadBtn2" class="btn btn-primary px-4">Upload</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('pagejs')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<script type="text/javascript" src="{{ asset('assets/js/vendor/virtual-select.min.js') }}"></script>

<script src="{{ asset('assets/js/maintenance_uploader/so.js') }}"></script>

@endsection