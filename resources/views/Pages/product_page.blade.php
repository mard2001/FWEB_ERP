@extends('Layout.layout')

@section('html_title')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Product Maintenance</title>
@endsection

@section('title_header')
    <x-header title="Product Maintenance" />
@endsection

@section('table')
    <x-table id="ProductTable">
        <x-slot:td>
            <td class="col">StockCode</td>
            <td class="col">Description</td>
            <td class="col">LongDesc</td>
            <td class="col">AlternateKey1</td>
            <td class="col">StockUom</td>
            <td class="col">AlternateUom</td>
            <td class="col">OtherUom</td>
            <td class="col">ConvFactAltUom</td>
            <td class="col">ConvFactOthUom</td>
            <td class="col">Mass</td>
            <td class="col">Volume</td>
            <td class="col">ProductClass</td>
            <td class="col">WarehouseToUse</td>
        </x-slot:td>
    </x-table>
@endsection

@section('modal')
    <x-prod_modal>
        <x-slot:form_fields>
            <div class="row h-100">
                <div class="col-sm-12 col-md-4 mt-1">
                    <input type="file" id="imageHolder" style="display:none;" accept="image/*">

                    <div class="col mt-1 d-flex justify-content-center align-items-center px-3 py-2" style="height: 300px;">
                        <div class="container h-100 w-75 my-3 p-2 d-flex justify-content-center align-items-center" style="border: 4px dashed rgba(45, 45, 45, 0.5); position: relative;">
                            <img id="prdImg" class="border-0 p-2 h-auto w-100" style="max-width: 200px; max-height: 250px; object-fit: cover;  cursor: pointer;" src="./uploads/upload.png" alt="">
                        </div>
                    </div>

                    <div class="w-100 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-sm btn-primary text-white" id="uploadImage" type="file">Choose Image</button>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8 mt-1">
                    <div class="col mt-2">
                        <label for="StockCode">Product Stock Code</label>
                        <input disabled type="text" id="StockCode" name="StockCode" class="form-control bg-white" required placeholder="Stockcode">
                    </div>
                    <div class="col mt-2">
                        <label for="priceWithVat">Product Price</label>
                        <input disabled type="number" id="priceWithVat" name="priceWithVat" class="form-control bg-white" required placeholder="Price">
                    </div>
                    <div class="col mt-2">
                        <label for="Description">Product Description</label>
                        <input disabled type="text" id="Description" name="Description" class="form-control bg-white" required placeholder="Description">
                    </div>
                    <div class="col mt-2">
                        <label for="AlternateKey1">AlternateKey1</label>
                        <input disabled type="text" id="AlternateKey1" name="AlternateKey1" class="form-control bg-white" required placeholder="Brand">
                    </div>
                    <div class="col mt-2">
                        <label for="StockUom">UOM</label>
                        <input disabled type="text" id="StockUom" name="StockUom" class="form-control bg-white" required placeholder="UOM">
                    </div>
                    <div class="row">
                        <div class="col mt-2">
                            <label for="AlternateUom">AlternateUom</label>
                            <input disabled type="text" id="AlternateUom" name="AlternateUom" class="form-control bg-white" required placeholder="Alternate Uom">
                        </div>
                        <div class="col mt-2">
                            <label for="ConvFactAltUom">ConvFactAltUom</label>
                            <input disabled type="text" id="ConvFactAltUom" name="ConvFactAltUom" class="form-control bg-white" required placeholder="ConvFactAltUom">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mt-2">
                            <label for="OtherUom">OtherUom</label>
                            <input disabled type="text" id="OtherUom" name="OtherUom" class="form-control bg-white" required placeholder="OtherUom">
                        </div>
                        <div class="col mt-2">
                            <label for="ConvFactOthUom">ConvFactOthUom</label>
                            <input disabled type="text" id="ConvFactOthUom" name="ConvFactOthUom" class="form-control bg-white" required placeholder="ConvFactOthUom">
                        </div>
                    </div>
                    <div class="col mt-2">
                        <label for="Mass">Mass</label>
                        <input disabled type="text" id="Mass" name="Mass" class="form-control bg-white" required placeholder="Mass">
                    </div>
                    <div class="col mt-2">
                        <label for="Volume">Volume</label>
                        <input disabled type="text" id="Volume" name="Volume" class="form-control bg-white" required placeholder="Volume">
                    </div>
                    <div class="col mt-2">
                        <label for="ProductClass">ProductClass</label>
                        <input disabled type="text" id="ProductClass" name="ProductClass" class="form-control bg-white" required placeholder="ProductClass">
                    </div>
                    <div class="col mt-2">
                        <label for="WarehouseToUse">WarehouseToUse</label>
                        <input disabled type="text" id="WarehouseToUse" name="WarehouseToUse" class="form-control bg-white" required placeholder="WarehouseToUse">
                    </div>
                </div>
            </div>
        </x-slot:form_fields>
    </x-prod_modal>
@endsection

@section('pagejs')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<script type="text/javascript" src="{{ asset('assets/js/vendor/virtual-select.min.js') }}"></script>
<script src="{{ asset('assets/js/maintenance_uploader/product-v2.js') }}"></script>
@endsection