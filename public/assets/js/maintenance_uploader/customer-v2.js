var MainTH, selectedMain;
var globalApi = "http://127.0.0.1:8000/";
var fileCtrTotal = 0;
var insertion = 0;
var jsonArr = [];
var salesmanData = [];
console.log('CUSTOMER V2')

const dataTableCustomBtn = `<div class="main-content buttons w-100 overflow-auto d-flex align-items-center px-2" style="font-size: 12px;">
                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1" id="addBtn">
                                    <div class="btnImg me-2" id="addImg">
                                    </div>
                                    <span>Add new</span>
                                </div>

                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1 actionBtn" id="csvDLBtn">
                                    <div class="btnImg me-2" id="dlImg">
                                    </div>
                                    <span>Download Template</span>
                                </div>

                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1 actionBtn" id="csvUploadShowBtn">
                                    <div class="btnImg me-2" id="ulImg">
                                    </div>
                                    <span>Upload Template</span>
                                </div>
                            </div>`;
let issueTable = `
        <div class='mx-auto' style="font-size:14px">
            <strong>Possible Issues:</strong>
            <div class="mx-3">
                <span> *Duplication of custCode.</span><br>
                <span> *One or more fields contain invalid data.</span>
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

    await datatables.loadCustomerData();
    await initVS.liteDataVS();
    await initVS.salesmanVS();

    $("#customerTable").on("click", "tbody tr", async function () {
        const selectedCustCode = $(this).attr('id');
        $('#modalFields #mdCode').show();
        $('#VSmdCode').hide();
        $('.customerIDDIV').show();

        await ajax('api/maintenance/v2/customer/' + selectedCustCode, 'GET', null, (response) => { // Success callback

            if (response.success == 1) {
                CustomerModal.viewMode(response.data);
                selectedMain = response.data;
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

    $('#addBtn').on('click', async function () {
        CustomerModal.enable(true);
        CustomerModal.clear();
        
        $('#customerMainModal').modal('show');

        $('#deleteCustBtn').hide();
        $('#rePrintPage').hide();
        $('#addCustBtn').show();
        $('#confirmCust').hide();
        $('#editCustBtn').hide();
        $('#modalFields #mdCode').hide();
        $('#VSmdCode').show();
        $('.customerIDDIV').hide();
    });

    $("#addCustBtn").on("click", function () {
        if (CustomerModal.isValid()) {
            console.log('clicked');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to add this Customer?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: "Yes, Add",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        CustomerModal.CustomerSave();
                    }
                });
        } else{
            console.log('invalid');
        }

    });

    $("#csvUploadShowBtn").on("click", async function () {
        $('#uploadCsv').modal('show');
    });

    $('#csvDLBtn').on('click', function () {
        downloadToCSV(jsonArr);
    });

    $("#deleteCustBtn").on("click", async function () {
        if ($(this).text().toLowerCase() == 'cancel') {
            $(this).text('Delete');
            $('#editCustBtn').removeClass('btn-primary').addClass('btn-info');
            $('#editCustBtn').text('Edit details');

            CustomerModal.fill(selectedMain);
            CustomerModal.enable(false);
            $('#confirmCust').hide();
        } else {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    var selectedCustID = $('#customerID').val();
                    // console.log(selectedCustID)
                    ajax('api/maintenance/v2/customer/' + selectedCustID, 'POST', JSON.stringify({ 
                        _method: 'DELETE' 
                    }), (response) => { // Success callback
                        
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                allowOutsideClick: false,
                                allowEscapeKey: false,  
                                allowEnterKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire({
                                        text: "Please wait... reloading data...",
                                        timerProgressBar: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,  
                                        allowEnterKey: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        },
                                    });
                                    CustomerModal.hide();
                                    datatables.loadCustomerData();
                                }
                            });
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
                }
            });
        }
    });

    $("#editCustBtn").on("click", async function () {
        if ($(this).text().toLocaleLowerCase() == 'edit details') {
            CustomerModal.enable(true);
            $('#mdCode').prop('disabled', true);
            $('#customerID').prop('disabled', true);
            $(this).text('Save changes').removeClass('btn-info').addClass('btn-primary');
            $('#deleteCustBtn').text('Cancel');
            $('#rePrintPage').hide();
            $('#confirmCust').hide();

        } else {
            //save update
            if (CustomerModal.isValid()) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    showDenyButton: true,
                    confirmButtonText: "Yes, Update",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        var selectedCustID = $('#customerID').val();
                        var mdCode = $('#customerID').val();
                        const customers = CustomerModal.getData();

                        await ajax('api/maintenance/v2/customer/' + selectedCustID, 'POST', JSON.stringify({
                            data: {...customers, mdCode:selectedMain.mdCode},
                            _method: "PUT"
                        }), (response) => { // Success callback
                            if (response.success) {
                                $(this).text('Edit details').removeClass('btn-primary').addClass('btn-info');
                                $('#deleteCustBtn').text('Delete');

                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,  
                                    allowEnterKey: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#confirmPO').hide();
                                        CustomerModal.hide();
        
                                        Swal.fire({
                                            text: "Please wait... reloading data...",
                                            timerProgressBar: true,
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,  
                                            allowEnterKey: false,
                                            didOpen: () => {
                                                Swal.showLoading();
                                            },
                                        });
        
                                        datatables.loadCustomerData();
                                    }
                                });
                                // ItemsTH.column(6).visible(false);
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

                    }
                });
            }
        }
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
    loadCustomerData: async () => {
        const CustomerData = await ajax('api/maintenance/v2/customer', 'GET', null, (response) => { // Success callback
            jsonArr = response.data;
            // console.log(response.data);
            datatables.initCustomerDatatable(response);
            Swal.close();
        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });
    },
    initCustomerDatatable: (response) => {
        if (response.success) {
            if (MainTH) {
                MainTH.clear().draw();
                MainTH.rows.add(response.data).draw();
            } else {
                MainTH = $('#customerTable').DataTable({
                    data: response.data,
                    layout: {
                        topStart: function () {
                            return $(dataTableCustomBtn);
                        }
                    },
                    columns: [
                        { data: 'customerID',  title: 'Customer ID' },
                        { data: 'custCode',  title: 'Customer Code' },
                        { data: 'custName',  title: 'Customer' },
                        { data: 'contactPerson',  title: 'Contact Person' },
                        { data: 'contactCellNumber',  title: 'Contact #' },
                        { data: 'custType',  title: 'Type' },
                        { data: 'address',  title: 'Address' },
                        { data: 'frequencyCategory',  title: 'Frequency Category' },
                        { data: 'mcpDay',  title: 'MCP Day' },
                        { data: 'mcpSchedule',  title: 'MCP Schedule' },
                        { data: 'mdCode',  title: 'MdCode' },
                        { data: 'priceCode',  title: 'priceCode' },
                    ],
                    columnDefs: [
                        { className: "text-start", targets: [ 0, 1, 2, 3, 4, 6, 9 ] },
                        { className: "text-center", targets: [ 5, 7, 8, 10, 11 ] },
                        // { className: "text-end", targets: [ 4 ] },
                        { className: "text-nowrap", targets: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ] },
                    ],
                    scrollCollapse: true,
                    scrollY: '100%',
                    scrollX: '100%',
                    "createdRow": function (row, data) {
                        $(row).attr('id', data.custCode);
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
};

const CustomerModal = {
    isValid: () => {
        return $('#modalFields').valid();
    },
    hide: () => {
        $('#customerMainModal').modal('hide');
    },
    show: () => {
        $('#customerMainModal').modal('show');
    },
    clear: () => {
        $('#modalFields input[type="text"]').val('');
        $('#modalFields input[type="number"]').val('');
        $('#modalFields textarea').val('');

    },
    enable: (enable) => {
        $('#modalFields input[type="text"]').prop('disabled', !enable);
        $('#modalFields input[type="number"]').prop('disabled', !enable);
        $('#modalFields textarea').prop('disabled', !enable);
        $('#modalFields #Salesman').prop('disabled', true);
    },
    viewMode: async (custData) => {
        CustomerModal.fill(custData);
        $('#deleteCustBtn').show();
        $('#addCustBtn').hide();
        $('#editCustBtn').show();
        $("#editCustBtn").text('Edit details').removeClass('btn-primary').addClass('btn-info');
        $('#confirmCust').hide();
        $('#deleteCustBtn').text('Delete');
        $('#rePrintPage').hide();

        CustomerModal.enable(false);
        CustomerModal.show();
    },
    fill: async (custData) => {
        $('#mdCode').val(custData.mdCode);
        $('#Salesman').val(custData.salesman.mdName);
        $('#customerID').val(custData.customerID);
        $('#custCode').val(custData.custCode);
        $('#custName').val(custData.custName);
        $('#contactPerson').val(custData.contactPerson);
        $('#contactCellNumber').val(custData.contactCellNumber);
        $('#contactLandline').val(custData.contactLandline);
        $('#address').val(custData.address);
        // $('#region').val(custData.region);
        // $('#province').val(custData.province);
        // $('#municipality').val(custData.municipality);
        // $('#barangay').val(custData.barangay);
        // $('#CityMunCode').val(custData.CityMunCode);
        $('#frequencyCategory').val(custData.frequencyCategory);
        $('#mcpDay').val(custData.mcpDay);
        $('#mcpSchedule').val(custData.mcpSchedule);
        // $('#geolocation').val(custData.geolocation);
        // $('#lastPurchase').val(custData.lastPurchase);
        $('#priceCode').val(custData.priceCode);
        $('#custType').val(custData.custType);
    },
    CustomerSave: async () => {
        let custData = CustomerModal.getData();
        console.log(custData);
        await ajax('api/maintenance/v2/customer', 'POST', JSON.stringify({ data: custData }), (response) => { // Success callback
            if (response.success) {
                datatables.loadCustomerData();
                CustomerModal.hide();

                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
                });

            }else if(response.success == 409){
                Swal.fire({
                    title: "error",
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

    },
    getData: () => {
        var data = {
            mdCode: $('#VSmdCode').val(),
            custCode: $('#custCode').val(),
            custName: $('#custName').val(),
            contactPerson: $('#contactPerson').val(),
            contactCellNumber: $('#contactCellNumber').val(),
            contactLandline: $('#contactLandline').val(),
            address: $('#address').val(),
            // region: $('#region').val(),
            // province: $('#province').val(),
            // municipality: $('#municipality').val(),
            // barangay: $('#barangay').val(),
            // CityMunCode: $('#CityMunCode').val(),
            frequencyCategory: $('#frequencyCategory').val(),
            mcpDay: $('#mcpDay').val(),
            mcpSchedule: $('#mcpSchedule').val(),
            // geolocation: $('#geolocation').val(),
            // lastPurchase: $('#lastPurchase').val(),
            priceCode: $('#priceCode').val(),
            custType: $('#custType').val(),
        }
        return data;
    },
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

    },

    salesmanVS: async () => {
        await ajax('api/maintenance/v2/salesman', 'GET', null, (response) => { // Success callback
            salesmanData = response.data;

            const SMDataVS = response.data.map(item => {
                return {
                    value: item.mdCode, 
                    label: item.mdCode,
                };
            });

            if (document.querySelector('#VSmdCode')?.virtualSelect) {
                document.querySelector('#VSmdCode').destroy();
            }

            VirtualSelect.init({
                ele: '#VSmdCode',
                options: SMDataVS, 
                multiple: false, 
                hideClearButton: false, 
                search: true,
                maxWidth: '100%', 
                additionalClasses: 'rounded',
                additionalDropboxClasses: 'rounded',
                additionalDropboxContainerClasses: 'rounded',
                additionalToggleButtonClasses: 'rounded',
            });

            $('#VSmdCode').on('afterClose', function () {
                if (this.value) {
                    var selected = salesmanData.filter(salesman => salesman.mdCode == this.value);
                    $('#Salesman').val(selected[0].mdName);
                }
            });

            $('#VSmdCode').on('change', function () {
                let selectedValue = this.virtualSelect.getValue();
            
                if (!selectedValue) {
                    $('#Salesman').val('');
                }
            });
            
        });
    }
}

async function ajaxCall(method, formDataArray = null, id) {
    let formData = new FormData();
    formData.append('customers', JSON.stringify(formDataArray));

    return await $.ajax({
        url: globalApi + 'api/maintenance/v2/customer/upload',
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
                console.log('warning')
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
                    let message = `Some data could not be inserted. <br>Please review the uploaded CSV file.<br><strong>${unsucc}</strong> customer${unsucc > 1 ? 's' : ''} were not inserted.<br><br><br>${issueTable}`;

                    Swal.fire({
                        title: "Warning!",
                        html: message,
                        icon: "warning"
                    });
                }
            }
            datatables.loadCustomerData();
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
                    processCSVFile(files[i], i); // Process CSV
                    console.log('CSV file.')
                }
                else if(fileExtension === 'xlsx'){
                    processExcelFile(files[i], i); // Process XLXS
                    console.log('Excel file.')
                }
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

function downloadToCSV(jsonArr){
    const csvData = Papa.unparse(jsonArr); // Convert JSON to CSV
    var today = new Date().toISOString().split('T')[0];

    // Create a blob and trigger download
    const blob = new Blob([csvData], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `CustomerMaintenance_${today}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

}








