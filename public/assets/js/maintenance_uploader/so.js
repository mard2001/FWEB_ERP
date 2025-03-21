var MainTH, selectedMain;
var globalApi = "http://127.0.0.1:8000/";
var fileCtrTotal = 0;
var insertion = 0;
var jsonArr = [];
var detailsDatatable;
var isEditable = false;
var originalSelected = [];

const dataTableCustomBtn = `<div class="main-content buttons w-100 overflow-auto d-flex align-items-center px-2" style="font-size: 12px;">
                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1" id="addBtn">
                                    <div class="btnImg me-2" id="addImg">
                                    </div>
                                    <span>Add new</span>
                                </div>
                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1 actionBtn" id="csvDLBtn">
                                    <div class="btnImg me-2" id="dlImg">
                                    </div>
                                    <span>Download Report</span>
                                </div>
                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1 actionBtn" id="csvUploadShowBtn">
                                    <div class="btnImg me-2" id="ulImg">
                                    </div>
                                    <span>Upload Template</span>
                                </div>
                            </div>`;

// Set up CSRF token for AJAX
$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('api_token')
    },
});

// set up auth error redirect
$(document).ajaxError(function (event, jqXHR, ajaxSettings, thrownError) {
    if (jqXHR.status === 401) {
        // Redirect to the login page (or any other page)
        window.location.href = "/login"; // Replace with your desired URL
    }
});


$(document).ready(async function () {
    isTokenExist();
    GlobalUX();

    await datatables.loadSOData();
    await initVS.liteDataVS();
    datatables.initDetailsDatatable([]);

    $("#soTable").on("click", "tbody tr", async function () {
        Swal.fire({
            text: "Please wait... Preparing data...",
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,  
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
        const selectedSalesOrderID = $(this).attr('id');
        await ajax('api/sales-order/header/' + selectedSalesOrderID, 'GET', null, (response) => { 
            Swal.close();
            if (response.success == 1) {
                // console.log(response);
                SOModal.viewMode(response.data);
                selectedMain = response.data;
                // originalSelected = JSON.parse(JSON.stringify(response.data));

            } else {
                Swal.fire({
                    title: "Opppps..",
                    text: response.message,
                    icon: "error"
                });

            }

        }, (xhr, status, error) => { // Error callback
            if (xhr.responseJSON && xhr.responseJSON.message) {
                Swal.fire({
                    title: "Opppps..",
                    text: xhr.responseJSON.message,
                    icon: "error"
                });

            }
        });
    });

    $('#csvDLBtn').on('click', function () {
        downloadToCSV(jsonArr);
    });

    $("#csvUploadShowBtn").on("click", async function () {
        $('#uploadCsv').modal('show');
    });
});

function isTokenExist() {
    if (!localStorage.getItem('api_token')) {
        window.location.href = "/login";
    }
}

function GlobalUX() {
    //UI
    const hamBurger = document.querySelector(".btn-toggle");

    hamBurger.addEventListener("click", async function () {
        document.querySelector("#sidebar").classList.toggle("expand");

    });

    // Get the pathname part of the URL
    var path = window.location.pathname;
    // Split the path by "/" and get the last segment
    var lastSegment = path.substring(path.lastIndexOf('/') + 1);
    switch (lastSegment.toLocaleLowerCase()) {
        case 'product':
            returnSideBarItemBaseOnIndex(0);
            break;
        case 'salesman':
            returnSideBarItemBaseOnIndex(1);
            break;
        case 'customer':
            returnSideBarItemBaseOnIndex(2);
            break;
        case 'inventory':
            returnSideBarItemBaseOnIndex(3);
            break;
        case 'picklist':
            returnSideBarItemBaseOnIndex(4);
            break;
        case 'pamasterlist':
            returnSideBarItemBaseOnIndex(5);
            break;

        case 'patarget':
            returnSideBarItemBaseOnIndex(6);
            break;

        case 'invoices':
            returnSideBarItemBaseOnIndex(7);
            break;

        case 'purchase-order':
            returnSideBarItemBaseOnIndex(8);
            break;

        case 'receiving-report':
            returnSideBarItemBaseOnIndex(9);
            break;

        case 'sales-order':
            returnSideBarItemBaseOnIndex(10);
            break;

            function returnSideBarItemBaseOnIndex(i) {
                var sidebar = $('.sidebar-item').eq(i);
                sidebar.addClass('selectedlink');
                sidebar.find('span').addClass('selectedlinkSpan');
            }

    }
}

async function ajax(endpoint, method, data, successCallback = () => { }, errorCallback = () => { }) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: globalApi + endpoint,
            type: method,
            Accept: 'application/json',
            contentType: 'application/json',
            data: data,

            success: function (response) {
                successCallback(response);  // Trigger the success callback
                resolve(response);  // Resolve the promise with the response
            },
            error: function (xhr, status, error) {
                errorCallback(xhr, status, error);  // Trigger the error callback
                reject(error);  // Reject the promise with the error
            }
        });
    });
}

const datatables = {
    loadSOData: async () => {
        const SOHeaderData = await ajax('api/sales-order/header', 'GET', null, (response) => { // Success callback
            jsonArr = response.data;
            // console.log(response.data);
            datatables.initSODatatable(response);
        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });
    },

    initSODatatable: (response) => {
        if (response.success) {
            if (MainTH) {
                MainTH.clear().draw();
                MainTH.rows.add(response.data).draw();
            } else {
                MainTH = $('#soTable').DataTable({
                    data: response.data,
                    layout: {
                        topStart: function () {
                            return $(dataTableCustomBtn);
                        }
                    },
                    columns: [
                        { data: 'SalesOrder',  title: 'Sales Order' },
                        { data: 'OrderStatus',  title: 'Status' },
                        { data: 'DocumentType',  title: 'Document Type' },
                        { data: 'Customer',  title: 'Customer ID' },
                        { data: 'CustomerName',  title: 'Customer Name' },
                        { data: 'CustomerPoNumber',  title: 'PO Number' },
                        { data: 'OrderDate',  title: 'Order Date' },
                        { data: 'Branch',  title: 'Branch' },
                        { data: 'Warehouse',  title: 'Warehouse' },
                        { data: 'ShipAddress1',  title: 'Address' },
                        { data: 'ShipToGpsLat',  title: 'Latitude' },
                        { data: 'ShipToGpsLong',  title: 'Longitude' },
                    ],
                    columnDefs: [
                        { className: "text-start", targets: [ 3, 4, 5, 6, 9, 10, 11] },
                        { className: "text-center", targets: [ 1, 2, 7, 8  ] },
                        // { className: "text-end", targets: [ 4 ] },
                        { className: "text-nowrap", targets: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] },
                    ],
                    scrollCollapse: true,
                    scrollY: '100%',
                    scrollX: '100%',
                    "createdRow": function (row, data) {
                        $(row).attr('id', data.SalesOrder);
                    },

                    "pageLength": 15,
                    "lengthChange": false,

                    initComplete: function () {
                        $(this.api().table().container()).find('#dt-search-0').addClass('p-1 mx-0 dtsearchInput nofocus');
                        $(this.api().table().container()).find('.dt-search label').addClass('py-1 px-3 mx-0 dtsearchLabel');
                        $(this.api().table().container()).find('.dt-layout-row').addClass('px-4');
                        $(this.api().table().container()).find('.dt-layout-table').removeClass('px-4');
                        $(this.api().table().container()).find('.dt-scroll-body').addClass('rmvBorder');
                        $(this.api().table().container()).find('.dt-layout-table').addClass('btmdtborder');

                        const dtlayoutTE = $('.dt-layout-cell.dt-end').first();
                        dtlayoutTE.addClass('d-flex justify-content-end');
                        dtlayoutTE.prepend('<div id="filterPOVS" name="filter" style="width: 200px" class="form-control bg-white p-0 mx-1">Filter</div>');
                        $(this.api().table().container()).find('.dt-search').addClass('d-flex justify-content-end');
                        $('.loadingScreen').remove();
                        $('#dattableDiv').removeClass('opacity-0');
                    }
                });
            }
        }
    },

    initDetailsDatatable: (data) => {
        detailsDatatable = new DataTable('#SODetails', {
            data: data,
            layout: {
                // topStart: function () {
                //     return $(dataTableCustomBtn);
                // }
            },
            columns: [
                { data: 'MStockCode',  title: 'Stock Code' },
                { data: 'MStockDes',  title: 'Description' },
                { data: 'MPrice',  title: 'Price' },
                { data: 'MProductClass',  title: 'Product Class' },
                { data: 'MOrderQty',  title: 'Order Qty' },
                { data: 'MPriceCode',  title: 'Price Code' },
                { data: 'MStockQtyToShp',  title: 'Qty to Ship' },
                { data: 'MStockUnitMass',  title: 'Unit Mass' },
                { data: 'MStockUnitVol',  title: 'Unit Volume' },
            ],
            columnDefs: [
                { className: "text-start", targets: [ 0, 1 ] },
                { className: "text-center", targets: [ 3, 4, 5, 6, 7, 8 ] },
                { className: "text-end", targets: [ 2 ] },
                // { className: "text-nowrap", targets: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ] },
            ],
            scrollCollapse: true,
            // scrollY: '100%',
            // scrollX: '100%',
            "createdRow": function (row, data) {
                $(row).attr('id', data.MStockCode);
            },

            "pageLength": 10,
            "lengthChange": false,

            initComplete: function () {
                $(this.api().table().container()).find('#dt-search-1').addClass('p-1 mx-0 dtDetailssearchInput nofocus');
                $(this.api().table().container()).find('.dt-search label').addClass('py-1 px-3 mx-0 dtDetailssearchLabel');
                $(this.api().table().container()).find('#ICDetails_info').css({'font-size':'11px'});
                $(this.api().table().container()).find('.paging_full_numbers').css({'font-size':'10px'});
            }

        });

        // Make cells editable on click
        $('#ICDetails tbody').on('click', 'td', function() {
            if(isEditable){
                let column = $(this).index();
                let row = $(this).closest('tr').index();
                let cell = detailsDatatable.cell(this);
                let value = cell.data();
                let displayedValue = cell.render('display');

                // Replace cell content with an input field
                if(column == 4 || column == 3 || column == 2){
                    console.log(displayedValue);
                    if(displayedValue != '-'){
                        $(this).html(`<input type="text" value="${value}" class="edit-cell text-center ${column} ${row}" style="width: 30px;height: 20px;" oninput="this.value = this.value.replace(/[^0-9]/g, '')">`);
                        // Focus on the input field
                        $(this).find('input').focus();
                    }
                }
            }
        });

        $('#ICDetails tbody').on('blur', '.edit-cell', function() {
            let newValue = parseInt($(this).val());
            let cell = detailsDatatable.cell($(this).closest('td'));

            cell.data(newValue).draw();
        });
    }
}

const initVS = {
    liteDataVS: async () => {
        // Initialize VirtualSelect for ship via
        VirtualSelect.init({
            ele: '#filterPOVS',                   // Attach to the element
            options: [
                // { label: "", value: null },
                // { label: "", value: 1 },
                // { label: "", value: "2" },

            ], 
            multiple: true, 
            hideClearButton: true, 
            search: false,
            maxWidth: '100%', 
            additionalClasses: 'rounded',
            additionalDropboxClasses: 'rounded',
            additionalDropboxContainerClasses: 'rounded',
            additionalToggleButtonClasses: 'rounded',
        });
    }
}

const SOModal = {
    show: () => {
        $('#salesOrderMainModal').modal('show');
    },
    enable: (enable) => {
        $('#modalFields input[type="text"]').prop('disabled', !enable);
        $('#modalFields input[type="number"]').prop('disabled', !enable);
        $('#modalFields textarea').prop('disabled', !enable);
    },
    viewMode: async (SOData) => {
        SOModal.fill(SOData);
        $('#rePrintPage').show();
        SOModal.show();
    },
    fill: async (SODetails) => {
        console.log(SODetails);
        $('#Branch').html(SODetails.Branch);
        $('#Warehouse').html(SODetails.Warehouse);
        $('#Customer').html(SODetails.Customer);
        $('#CustomerName').html(SODetails.CustomerName);
        $('#ShipAddress1').html(SODetails.ShipAddress1);
        $('#SalesOrder').html(SODetails.SalesOrder);
        $('#OrderStatus').html(SODetails.OrderStatus);
        $('#OrderDate').html(SODetails.OrderDate);
        $('#ReqShipDate').html(SODetails.ReqShipDate);

        detailsDatatable.clear().rows.add(SODetails.details).draw();
    },
}

function downloadToCSV(jsonArr){
    const csvData = Papa.unparse(jsonArr); // Convert JSON to CSV
    var today = new Date().toISOString().split('T')[0];

    // Create a blob and trigger download
    const blob = new Blob([csvData], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `SalesOrder_${today}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

}


async function ajaxCall(method, formDataArray = null, id) {
    let formData = new FormData();
    formData.append('salesman', JSON.stringify(formDataArray));

    return await $.ajax({
        url: globalApi + 'api/maintenance/v2/salesperson/upload',
        type: method,
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('api_token')
        },
        processData: false, // Required for FormData
        contentType: false, // Required for FormData
        data: JSON.stringify(formDataArray), // Convert the data to JSON format

        success: async function(response) {
            insertion++;
            expectedtotalRows += response.totalFileLength;
            actualtotalRows += response.successful;

            iconResult = `<span class="mdi mdi-alert-circle text-danger resultIcon"></span>`;
            var insertedResultColor = `text-danger`;

            if (response.status_response == 1) {
                iconResult = `<span class="mdi mdi-check-circle text-success resultIcon"></span>`
                insertedResultColor = 'text-success';


            } else if (response.status_response == 2) {
                iconResult = `<span class="mdi mdi-alert-circle text-warning resultIcon"></span>`
                insertedResultColor = 'text-warning';
            }

            $('#totalUploadSuccess').text(insertion);
            $("#fileStatus" + id).html(iconResult); 
            $("#insertedStat" + id).html(`${response.successful} / ${response.totalFileLength}`).addClass(insertedResultColor);
            
            if(fileCtrTotal>0 && fileCtrTotal==insertion){
                console.log('1')
                if(expectedtotalRows>0 && expectedtotalRows == actualtotalRows){
                    Swal.fire({
                        title: "Success!",
                        text: 'All data successfully Inserted',
                        icon: "success"
                    });
                } else {
                    var unsucc = expectedtotalRows-actualtotalRows;
                    let message = `Some data could not be inserted. <br>Please review the uploaded CSV file.<br><strong>${unsucc}</strong> Salesman${unsucc > 1 ? 's' : ''} were not inserted.<br><br><br>${issueTable}`;

                    Swal.fire({
                        title: "Warning!",
                        html: message,
                        icon: "warning"
                    });
                }
            }
            datatables.loadSalesmanData();
        },
        error: async function(xhr, subTotal, error) {
            Swal.fire({
                icon: "error",
                title: "Api Error",
                text: xhr.responseJSON?.message || xhr.statusText,

            });
            return xhr, subTotal, error;
        }
    });
}

const uploadconfirmUpload = document.getElementById('uploadBtn2')
    .addEventListener('click', () => {
        var appendTable = '';
        insertion = 0;
        fileCtrTotal = 0;
        expectedtotalRows = 0;
        actualtotalRows = 0; 
        errorFile = false;
        // Get all the files selected in the file input
        var files = document.getElementById('formFileMultiple').files;

        $('#totalUploadSuccess').html(insertion);
        $('#totalFiles').html(files.length);
        $('#totalFile').html(files.length);
        fileCtrTotal = files.length;
        // Loop over each file and check the extension
        for(let i=0; i < files.length; i++){
            var fileExtension = files[i].name.split('.').pop().toLowerCase();

            appendTable += trNew(files[i].name, i);
            if(!['csv','xlsx'].includes(fileExtension)){
                setTimeout(function() {
                    iconResult = `<span class="mdi mdi-alpha-x-circle text-danger resultIcon"></span>`;
                    $("#fileStatus" + i).html(iconResult); 
                }, 100);
                errorFile = true;
            }

            $('#fileListTable').html(appendTable);
        }

        if(!errorFile){
            for(let i=0; i < files.length; i++){
                var fileExtension = files[i].name.split('.').pop().toLowerCase();

                appendTable += trNew(files[i].name, i);
                if (fileExtension === 'csv') {
                    // processCSVFile(files[i], i); // Process CSV
                    console.log('CSV file.')
                }
                else if(fileExtension === 'xlsx'){
                    // processExcelFile(files[i], i); // Process CSV
                    console.log('Excel file.')
                }
                // $('#fileListTable').html(appendTable);
            }
            $('#uploadBtn2').html('Upload');
        } else{
            Swal.fire({
                icon: "error",
                title: "Review files",
                text: "Please select .csv files only",
            });
            $('#uploadBtn2').html('Reupload');
        }
    });

function processCSVFile(file, ctr) {
    Papa.parse(file, {
        header: true,
        skipEmptyLines: true,
        complete: function(results) {
            ajaxCall('POST', results.data, ctr);
        }
    });
}

function processExcelFile(file, ctr) {
    readXlsxFile(file).then((rows) => {
        let keys = rows[0]; // First row contains the keys
        let result = rows.slice(1).map(row => {
            return keys.reduce((obj, key, index) => {
                obj[key] = row[index]; // Map key to corresponding value in row
                return obj;
            }, {});
        });
        ajaxCall('POST', result, ctr);
    });
}


function trNew(fileName, indexId) {
    return `<tr id="fileRow${indexId}">
            <td class="imgSizeContainer col-1">
                <span class="mdi mdi-file-document-outline"></span>
            </td>
            <td class = "col-9" style="padding-left: 0px;">
                <span>${fileName}</span>
            </td>
            <td id="insertedStat${indexId}" class="text-end col-2">    
            
            </td>
            <td id="fileStatus${indexId}" class="text-center col-1">       
                <span class="loader">                                    
                </span>              
            </td>
        </tr>`;
}