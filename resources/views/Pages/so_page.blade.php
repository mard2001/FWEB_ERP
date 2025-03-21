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

</style>

<x-so_modal>
    <x-slot:form_fields>
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
    </x-slot:form_fields>
</x-so_modal>

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