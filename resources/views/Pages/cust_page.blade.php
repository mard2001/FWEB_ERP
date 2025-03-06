@extends('Layout.layout')

@section('html_title')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://unpkg.com/read-excel-file@5.x/bundle/read-excel-file.min.js"></script>
    <title>Customer Maintenance</title>
@endsection 

@section('title_header')
    <x-header title="Customer Maintenance" />
@endsection

@section('table')
<x-table id="customerTable">
    <x-slot:td>
        <td class="col">customerID</td>
        <td class="col">custCode</td>
        <td class="col">custName</td>
        <td class="col">contactPerson</td>
        <td class="col">contactCellNumber</td>
        <td class="col">custType</td>
        <td class="col">address</td>
        <td class="col">frequencyCategory</td>
        <td class="col">mcpDay</td>
        <td class="col">mcpSchedule</td>
        <td class="col">mdCode</td>
        <td class="col">priceCode</td>
    </x-slot:td>
</x-table>
@endsection

@section('modal')
    <x-cust_modal>
        <x-slot:form_fields>
            <div class="row h-100">
                <div class="row">
                    <span style="font-size:12px;" class="text-secondary">Salesman Assigned:</span>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="mdCode">MdCode</label>
                            <div id="VSmdCode" name="filter" style="width: 200px" class="form-control bg-white p-0 mx-1 needField">MdCode</div>
                            <input disabled type="text" id="mdCode" name="mdCode" class="form-control bg-white" required placeholder="mdCode">
                        </div> 
                    </div>
                    <div class="col-9">
                        <div class="mb-3">
                            <label for="Salesman">Salesman Name</label>
                            <input disabled type="text" id="Salesman" name="Salesman" class="form-control bg-white" required placeholder="Salesman">
                        </div>    
                    </div>
                </div>
                <hr>
                <div class="row">
                    <span style="font-size:12px;" class="text-secondary">Customer Details:</span>
                    <div class="col-4 customerIDDIV">
                        <div class="mb-3">
                            <label for="customerID">Customer ID</label>
                            <input disabled type="text" id="customerID" name="customerID" class="form-control bg-white" required placeholder="customerID">
                        </div>
                    </div>
                    <div class="col-8 customerIDDIV">
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="custCode">CustCode</label>
                            <input disabled type="text" id="custCode" name="custCode" class="form-control bg-white needField" required placeholder="custCode">
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="mb-3">
                            <label for="custName">Name</label>
                            <input disabled type="text" id="custName" name="custName" class="form-control bg-white needField" required placeholder="custName">
                        </div>    
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="contactPerson">Contact Person</label>
                            <input disabled type="text" id="contactPerson" name="contactPerson" class="form-control bg-white" required placeholder="contactPerson">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="contactCellNumber">Contact Cell Number</label>
                            <input disabled type="text" id="contactCellNumber" name="contactCellNumber" class="form-control bg-white" required placeholder="contactCellNumber">
                        </div>    
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="contactLandline">Contact Landline</label>
                            <input disabled type="text" id="contactLandline" name="contactLandline" class="form-control bg-white" required placeholder="contactLandline">
                        </div>    
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="address">Address</label>
                            <input disabled type="text" id="address" name="address" class="form-control bg-white needField" required placeholder="address">
                        </div>    
                    </div>
                    {{-- <div class="col-4">
                        <div class="mb-3">
                            <label for="region">Region</label>
                            <input disabled type="text" id="region" name="region" class="form-control bg-white" required placeholder="region">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="province">Province</label>
                            <input disabled type="text" id="province" name="province" class="form-control bg-white" required placeholder="province">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="municipality">Municipality</label>
                            <input disabled type="text" id="municipality" name="municipality" class="form-control bg-white" required placeholder="municipality">
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mb-3">
                            <label for="barangay">Barangay</label>
                            <input disabled type="text" id="barangay" name="barangay" class="form-control bg-white" required placeholder="barangay">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="CityMunCode">CityMunCode</label>
                            <input disabled type="text" id="CityMunCode" name="CityMunCode" class="form-control bg-white" required placeholder="CityMunCode">
                        </div>
                    </div> --}}
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="frequencyCategory">Frequency</label>
                            <input disabled type="text" id="frequencyCategory" name="frequencyCategory" class="form-control bg-white needField" required placeholder="frequencyCategory" onkeypress="return /[0-9]/.test(event.key)">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="mcpDay">MCP Day</label>
                            <input disabled type="text" id="mcpDay" name="mcpDay" class="form-control bg-white needField" required placeholder="mcpDay" onkeypress="return /[0-9]/.test(event.key)">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="mcpSchedule">MCP Schedule</label>
                            <input disabled type="text" id="mcpSchedule" name="mcpSchedule" class="form-control bg-white needField" required placeholder="mcpSchedule">
                        </div>
                    </div>
                    {{-- <div class="col-12">
                        <div class="mb-3">
                            <label for="geolocation">Geolocation</label>
                            <input disabled type="text" id="geolocation" name="geolocation" class="form-control bg-white" required placeholder="geolocation">
                        </div>    
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="lastPurchase">last Purchase</label>
                            <input disabled type="text" id="lastPurchase" name="lastPurchase" class="form-control bg-white" required placeholder="lastPurchase">
                        </div>    
                    </div> --}}
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="priceCode">Price Code</label>
                            <input disabled type="text" id="priceCode" name="priceCode" class="form-control bg-white needField" required placeholder="priceCode" onkeypress="return /[0-9]/.test(event.key)" maxlength="2">
                        </div>    
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="custType">Customer Type</label>
                            <input disabled type="text" id="custType" name="custType" class="form-control bg-white needField" required placeholder="custType">
                        </div>    
                    </div>
                </div>
            </div>
        </x-slot:form_fields>
    </x-cust_modal>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js" integrity="sha512-dfX5uYVXzyU8+KHqj8bjo7UkOdg18PaOtpa48djpNbZHwExddghZ+ZmzWT06R5v6NSk3ZUfsH6FNEDepLx9hPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/js/maintenance_uploader/customer-v2.js') }}"></script>
@endsection