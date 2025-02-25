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

<x-table id="rrTable">
    <x-slot:td>
        <td class="col">supCode</td>
        <td class="col">supName</td>
        {{-- <td class="col">supTIN</td> --}}
        <td class="col">supAdd</td>
        <td class="col">rrNo</td>
        <td class="col">Total</td>
        <td class="col">rrDate</td>
        <td class="col">rrRef</td>
        <td class="col">rrStat1</td>
        <td class="col">prepared</td>
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

<x-rr_modal>
    <x-slot:form_fields>
        {{-- <h2 class="text-center mb-5">Receiving Report</h2> --}}
        
        <div class="row g-4">
            <div class="col">
                <table>
                    <tbody>
                        <tr>
                            <td></td>
                            <th></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Supplier Code:</td>
                            <th class="px-2"><span class="supCode" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Supplier Name:</td>
                            <th class="px-2"><span class="supName" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Supplier TIN:</td>
                            <th class="px-2"><span class="supTin" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Address:</td>
                            <th class="px-2"><span class="supAdd" style="font-weight: 550"></span></th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col ">
                <table>
                    <tbody>
                        <tr>
                            <td></td>
                            <th></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">RR No.:</td>
                            <th class="px-2"><span class="rrNo" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Date:</td>
                            <th class="px-2"><span class="date" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Reference:</td>
                            <th class="px-2"><span class="reference" style="font-weight: 550"></span></th>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">Status:</td>
                            <th class="px-2"><span class="status" style="font-weight: 550"></span></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <table class="table" style="font-size: 13px">
            <thead>
                <tr>
                    <th scope="col" class="text-center">No.</th>
                    <th scope="col" class="text-center">Item</th>
                    <th scope="col" class="text-center">Description</th>
                    <th scope="col" class="text-center">Quantity</th>
                    <th scope="col" class="text-center">OuM</th>
                    <th scope="col" class="text-center">WhsCode</th>
                    <th scope="col" class="text-center">Unit Price</th>
                    <th scope="col" class="text-center">Net of Vat</th>
                    <th scope="col" class="text-center">Vat</th>
                    <th scope="col" class="text-center">Gross</th>
                </tr>
            </thead>
            <tbody class="rrTbody">
            </tbody>
        </table>
    </x-slot:form_fields>
</x-rr_modal>

@endsection

@section('pagejs')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<script type="text/javascript" src="{{ asset('assets/js/vendor/virtual-select.min.js') }}"></script>

<script src="{{ asset('assets/js/maintenance_uploader/rr.js') }}"></script>
{{-- <script>
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
</script> --}}

@endsection