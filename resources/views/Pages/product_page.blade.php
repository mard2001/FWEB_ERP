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

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadCsv">
  Launch demo modal
</button>


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
                <button id="uploadBtn" class="btn btn-primary px-4">Upload</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@section('pagejs')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<script type="text/javascript" src="{{ asset('assets/js/vendor/virtual-select.min.js') }}"></script>
<script src="{{ asset('assets/js/maintenance_uploader/product-v2.js') }}"></script>

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