@extends('Layout.layout')

@section('html_title')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<title>Receiving Report</title>
@endsection

@section('title_header')
<x-header title="Receiving Report" />
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
</style>

<x-table>
    <x-slot:td>
        <td class="col">SupplierCode</td>
        <td class="col">SupplierTIN</td>
        <td class="col">Address</td>
        <td class="col">RRNo</td>
        <td class="col">Date</td>
        <td class="col">Reference</td>
        <td class="col">Status</td>
        <td class="col">Total</td>
        <td class="col">PreparedBy</td>
        <td class="col">FileName</td>
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
</style>

<x-form_modal>
    <x-slot:form_fields>
        <div class="row h-100 fs15">
            <div class="col mt-1">

                <div class="col mt-2">
                    <div class="row">
                        <div class="col">
                            <label for="SupplierCode">Supplier Code</label>
                            <input disabled type="text" id="SupplierCode" name="SupplierCode" class="form-control bg-white"
                                required placeholder="Supplier Code">
                        </div>

                        <div class="col">
                            <label for="Reference">Reference</label>
                            <input disabled type="text" id="Reference" name="Reference" class="form-control bg-white"
                                required placeholder="Reference">
                        </div>
                    </div>
                </div>

                <div class="col mt-2">
                    <label for="SupplierName">Supplier Name</label>
                    <input disabled type="text" id="SupplierName" name="SupplierName" class="form-control bg-white"
                        required placeholder="Supplier Name" style="font-size: 14px"></input>
                </div>

                <div class="col mt-2">
                    <label for="Address">Address</label>
                    <textarea disabled id="Address" name="Address" class="form-control bg-white"
                        required placeholder="Address" rows="2" style="resize: none; font-size: 14px"></textarea>
                </div>

                <div class="col mt-2">
                    <div class="row">

                        <div class="col">
                            <label for="PreparedBy">Prepared By</label>
                            <input disabled type="text" id="PreparedBy" name="PreparedBy" class="form-control bg-white"
                                required placeholder="Prepared By" style="font-size: 14px"></input>
                        </div>

                        <div class="col">
                            <label for="PrintedBy">Printed By</label>
                            <input disabled type="text" id="PrintedBy" name="PrintedBy" class="form-control bg-white"
                                required placeholder="Printed By">
                        </div>

                    </div>
                </div>


            </div>

            <div class="col mt-1">

                <div class="col mt-2">
                    <div class="row">
                        <div class="col">
                            <label for="RRNo">RR Number</label>
                            <input disabled type="text" id="RRNo" name="RRNo" class="form-control bg-white"
                                required placeholder="RR Number">
                        </div>

                        <div class="col">
                            <label for="SupplierTIN">Supplier TIN</label>
                            <input disabled type="text" id="SupplierTIN" name="SupplierTIN" class="form-control bg-white"
                                required placeholder="SupplierTIN">
                        </div>
                    </div>
                </div>

                <div class="col mt-2">
                    <div class="row">
                        <div class="col">
                            <label for="Status">Status</label>
                            <input disabled type="text" id="Status" name="Status" class="form-control bg-white"
                                required placeholder="Status">
                        </div>

                        <div class="col">
                            <label for="Total">Total</label>
                            <input disabled type="text" id="Total" name="Total" class="form-control bg-white"
                                required placeholder="Total">
                        </div>
                    </div>
                </div>

                <div class="col mt-3">
                    <div class="row">
                        <div class="col">
                            <label for="CheckedBy">Checked By</label>
                            <input disabled type="text" id="CheckedBy" name="CheckedBy" class="form-control bg-white"
                                required placeholder="Checked By">
                        </div>

                        <div class="col">
                            <label for="ApprovedBy">Approved By</label>
                            <input disabled type="text" id="ApprovedBy" name="ApprovedBy" class="form-control bg-white"
                                required placeholder="Approved By">
                        </div>
                    </div>
                </div>

                <div class="col mt-3">
                    <label for="filename">PDF Filename</label>
                    <input disabled type="text" id="filename" name="filename" class="form-control bg-white"
                        required placeholder="PDF Filename">
                </div>




            </div>
        </div>

        <div class="row mt-3">
            <div class="d-flex justify-content-between align-items-center px-3">
                <div>
                    <label for="itemTables">Receiving Report Item List</label>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary btn-sm text-white mx-1" id="addItems">Add Item</button>
                    <button class="btn btn-danger btn-sm text-white mx-1" id="itemDelete" disabled>Delete Item</button>
                </div>
            </div>
            <x-sub_table id="itemTables">
                <x-slot:td>
                    <td class="col">SKU</td>
                    <td class="col">Decription</td>
                    <td class="col">Quantity</td>
                    <td class="col">UOM</td>
                    <td class="col">WhsCode</td>
                    <td class="col">UnitPrice</td>
                    <td class="col">NetVat</td>
                    <td class="col">Vat</td>
                    <td class="col">Gross</td>
                    <td class="col"></td>
                </x-slot:td>
            </x-sub_table>
        </div>
    </x-slot:form_fields>
</x-form_modal>

<div class="modal fade modal modal-lg text-dark" id="itemModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 h-100">
            <div class="modal-body" style="height: auto; max-height: 75vh;">
                <form id="itemModalFields">
                    <div class="row h-100 fs15">
                        <div class="col mt-1">

                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="SKU">SKU</label>
                                        <div disabled type="text" id="SKU" name="SKU" class="form-control bg-white p-0"
                                            required placeholder="Product Code"> </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="Decription">Decription</label>
                                        <input disabled type="text" id="Decription" name="Decription" class="form-control bg-white"
                                            required placeholder="Decription">
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="Quantity">Quantity</label>
                                        <input disabled type="number" id="Quantity" name="Quantity" class="form-control bg-white"
                                            required placeholder="Quantity">
                                    </div>

                                    <div class="col">
                                        <label for="UOM">UOM</label>
                                        <input disabled type="text" id="UOM" name="UOM" class="form-control bg-white"
                                            placeholder="UOM">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col mt-1">


                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="WhsCode">Warehouse Code</label>
                                        <input disabled type="text" id="WhsCode" name="WhsCode" class="form-control bg-white"
                                            required placeholder="Warehouse Code">
                                    </div>

                                    <div class="col">
                                        <label for="UnitPrice">Unit Price</label>
                                        <input disabled type="number" id="UnitPrice" name="UnitPrice" class="form-control bg-white"
                                            required placeholder="Unit Price">
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="row">

                                    <div class="col">
                                        <label for="NetVat">NetVat</label>
                                        <input disabled type="number" id="NetVat" name="NetVat" class="form-control bg-white"
                                            required placeholder="NetVat">
                                    </div>


                                    <div class="col">
                                        <label for="Vat">Vat</label>
                                        <input disabled type="number" id="Vat" name="Vat" class="form-control bg-white"
                                            required placeholder="Vat">
                                    </div>

                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="col">
                                    <label for="Gross">Gross</label>
                                    <input disabled type="number" id="Gross" name="Gross" class="form-control bg-white"
                                        required placeholder="Gross">
                                </div>

                            </div>


                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-info btn-info btn-sm text-white" id="itemSaveEdit">Edit
                    details</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal modal-lg text-dark" id="deliveriesModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 h-100">
            <div class="modal-body overflow-auto" style="height: auto; max-height: 75vh;">
                <form id="deliveriesModalFields">
                    <div class="row h-100 fs15">
                        <div class="col mt-1">

                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="SKU">SKU</label>
                                        <input disabled type="text" id="SKU" name="SKU" class="form-control bg-white"
                                            required placeholder="Product Code">
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="Decription">Decription</label>
                                        <input disabled type="text" id="Decription" name="Decription" class="form-control bg-white"
                                            required placeholder="Decription">
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="Quantity">Quantity</label>
                                        <input disabled type="number" id="Quantity" name="Quantity" class="form-control bg-white"
                                            required placeholder="Quantity">
                                    </div>

                                    <div class="col">
                                        <label for="UOM">UOM</label>
                                        <input disabled type="text" id="UOM" name="UOM" class="form-control bg-white"
                                            placeholder="UOM">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col mt-1">


                            <div class="col mt-2">
                                <div class="row">
                                    <div class="col">
                                        <label for="WhsCode">Warehouse Code</label>
                                        <input disabled type="text" id="WhsCode" name="WhsCode" class="form-control bg-white"
                                            required placeholder="Warehouse Code">
                                    </div>

                                    <div class="col">
                                        <label for="UnitPrice">Unit Price</label>
                                        <input disabled type="number" id="UnitPrice" name="UnitPrice" class="form-control bg-white"
                                            required placeholder="Unit Price">
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="row">

                                    <div class="col">
                                        <label for="NetVat">NetVat</label>
                                        <input disabled type="number" id="NetVat" name="NetVat" class="form-control bg-white"
                                            required placeholder="NetVat">
                                    </div>


                                    <div class="col">
                                        <label for="Vat">Vat</label>
                                        <input disabled type="number" id="Vat" name="Vat" class="form-control bg-white"
                                            required placeholder="Vat">
                                    </div>

                                </div>
                            </div>

                            <div class="col mt-2">
                                <div class="col">
                                    <label for="Gross">Gross</label>
                                    <input disabled type="number" id="Gross" name="Gross" class="form-control bg-white"
                                        required placeholder="Gross">
                                </div>

                            </div>


                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-info btn-info btn-sm text-white" id="deliveriesSaveEdit">Edit
                    details</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script> -->

<script src="{{ asset('assets/js/maintenance_uploader/receiving_report.js') }}"></script>
<script>
    $(document).ready(async function() {
        $("#uploadBtn").off("click", modalUploader);

        //modify the function
        modalUploader = async function() {

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
                    var response = await ajaxCall(1, fileFormData);

                    var iconResult = `<span class="mdi mdi-alert-circle text-danger resultIcon"></span>`;
                    var insertedResultColor = `text-danger`;

                    if (response.status_response == 1) {
                        iconResult = `<span class="mdi mdi-check-circle text-success resultIcon"></span>`
                        var incrementSuccess = parseInt($('#totalUploadSuccess').val(), 10) || 0; // Parse the value as an integer, default to 0 if NaN
                        incrementSuccess++;

                        $('#totalUploadSuccess').val(incrementSuccess);
                        $('#totalUploadSuccess').text(incrementSuccess);

                        insertedResultColor = 'text-success';


                    }
                    if (response.status_response == 2) {

                        iconResult = `<span class="mdi mdi-alert-circle text-warning resultIcon"></span>`
                        insertedResultColor = 'text-warning';
                    }

                    $("#fileStatus" + i).html(iconResult); // Use i here to update the correct element
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
                    icon: "error",
                    title: "Review files",
                    text: "Please select csv files only",
                });

            }

        };

        $("#uploadBtn").click(modalUploader);

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
            switch (method) {
                case 1: // BULK INSERT
                    apiMethod = 'POST';

                    break;
                case 2: // UPDATE DATA
                    apiMethod = 'POST';

                    break;
                case 3: // DELETE DATA
                    apiMethod = 'POST';

                    break;
                case 4: // GET SINGLE DATA VIA ID
                    apiMethod = 'GET';

                    break;
                case 5: // GET ALL DATA
                    apiMethod = 'GET';

                    break;
                case 6: // INSERT DATA
                    apiMethod = 'POST';

                    break;
            }


            return await $.ajax({
                url: globalApi + 'api/upload-po-pdf',
                type: apiMethod,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token')
                },
                processData: false, // Required for FormData
                contentType: false, // Required for FormData
                data: formData, // Convert the data to JSON format
                // data: xmlJson, // Convert the data to JSON format

                success: async function(response) {
                    console.log(response);

                    if (response.status_response != 1) {

                        //console.log(JSON.stringify(response, null, 2));
                        //console.log(response.extracted_text);



                    }

                    //console.log(response);
                    return response;

                },
                error: async function(xhr, status, error) {


                    // Swal.fire({
                    //     icon: "error",
                    //     title: "Api Error",
                    //     text: JSON.stringify({ xhr, status, error }, null, 2),
                    // });

                    console.log(xhr, status, error)

                    return xhr, status, error;
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