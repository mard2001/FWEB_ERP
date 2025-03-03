var MainTH, selectedMain;
var globalApi = "https://spc.sfa.w-itsolutions.com/";
var fileCtrTotal = 0;
var insertion = 0;
var jsonArr = [];

const dataTableCustomBtn = `<div class="main-content buttons w-100 overflow-auto d-flex align-items-center px-2" style="font-size: 12px;">
                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1 actionBtn" id="csvDLBtn">
                                    <div class="btnImg me-2" id="dlImg">
                                    </div>
                                    <span>Download Template</span>
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

    await datatables.loadRRData();
    await initVS.liteDataVS();

    $("#rrTable").on("click", "tbody tr", async function () {
        const selectedRRCode = $(this).attr('id');
        await ajax('api/report/v2/rr/' + selectedRRCode, 'GET', null, (response) => { // Success callback
            if (response.success == 1) {
                RRModal.viewMode(response.data);
                selectedMain = response.data[0];
                // var tempRes = jsonArr.filter(item => item.RRNo == selectedRRCode)
                // RRModal.viewMode(tempRes[0]);
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

    $('#rrPrintPage').on('click', async function () {
        console.log('printClicked');
        sessionStorage.setItem('printingRRCode', selectedMain.RRNo);
        // window.open('/print/rr/'+selectedMain.RRNo, '_blank');
        
        $.ajax({
            url: "/api/redirect",
            type: "POST",
            data: { RRNum: selectedMain.RRNo },
            success: function(response) {
                if (response.success) {
                    console.log(response)
                    window.open('/print/rr', '_blank'); // Open without RRNum in URL
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });

    $('#csvDLBtn').on('click', function () {
        downloadToCSV(jsonArr);
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
    loadRRData: async () => {
        const prodData = await ajax('api/report/v2/rr', 'GET', null, (response) => { // Success callback
            jsonArr = response.data;
            datatables.initRRDatatable(response);
        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });
    },

    initRRDatatable: (response) => {
        if (response.success) {
            if (MainTH) {
                MainTH.clear().draw();
                MainTH.rows.add(response.data).draw();
            } else {
                MainTH = $('#rrTable').DataTable({
                    data: response.data,
                    layout: {
                        topStart: function () {
                            return $(dataTableCustomBtn);
                        }
                    },
                    columns: [
                        { data: 'poincluded.posupplier.SupplierCode',  title: 'Supplier Code' },
                        { data: 'poincluded.posupplier.SupplierName',  title: 'Supplier Name' },
                        // { data: 'SupplierTIN',  title: 'Supplier TIN' },
                        { data: 'poincluded.posupplier.CompleteAddress',  title: 'Supplier Address' },
                        { data: 'RRNo',  title: 'RR No.' },
                        { data: 'Total', title: 'Total',  
                            render: function(data, type, row) {
                                if (!data || isNaN(data)) return '0';
                                return parseFloat(data) !== 0 ? parseFloat(data).toLocaleString('en-US') : '0';
                            }
                        },
                        { data: 'RRDATE',  title: 'Date',
                            render: function(data, type, row) {
                                if (!data) return ''; 
                
                                let date = new Date(data);
                                return date.toLocaleDateString('en-US', {
                                    year: 'numeric', month: '2-digit', day: '2-digit'
                                });
                            } 
                        },
                        { data: 'Reference',  title: 'Reference'},
                        { data: 'Status',  title: 'Status',
                            render: function(data, type, row) {
                                return data == 1 ? "<span style='color:#22bb33;'>Active</span>" : "<span style='color:#bb2124;'>Deleted</span>";
                            } 
                        },
                        { data: 'RECEIVEDBY',  title: 'Received By' },
                    ],
                    columnDefs: [
                        { className: "text-start", targets: [ 0, 1, 2, 6 ] },
                        { className: "text-center", targets: [ 3, 7 ] },
                        { className: "text-end", targets: [ 4 ] },
                    ],
                    scrollCollapse: true,
                    scrollY: '100%',
                    scrollX: '100%',
                    "createdRow": function (row, data) {
                        $(row).attr('id', data.RRNo);
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
}

const RRModal = {
    hide: () => {
        $('#rrMainModal').modal('hide');
    },
    show: () => {
        $('#rrMainModal').modal('show');
    },
    fill: async (RRModalData) => {
        var total = 0;
        var tbody = $(".rrTbody");
        tbody.empty();
        // $('#StockCode').val(RRModal.SupplierCode);
        $('.supCode').html(RRModalData.poincluded.SupplierCode);
        $('.supName').html(RRModalData.poincluded.posupplier.SupplierName);
        $('.supTin').html('---');
        $('.supAdd').html(RRModalData.poincluded.posupplier.CompleteAddress);
        $('.rrNo').html(RRModalData.RRNo);
        $('.date').html(RRModalData.RRDATE);
        $('.reference').html(RRModalData.Reference);
        $('.status').html(RRModalData.Status == 1 ? "Active" : "Deleted");

        // <td class="text-center">${parseFloat(item["convertedQuantity"]).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        // <td class="text-center">${item["convertedUOM"]}</td>

        (RRModalData.rrdetails).forEach((item, index) => {
            total += parseFloat(item["Gross"]) || 0;
            // Feb, 24 = Typo naming in Description Column, tblInvRRDetails table;
            var tr = `
                <tr>
                    <th scope="row" class="text-center">${index + 1}</th>
                    <td>${item["SKU"]}</td>
                    <td>${item.product["Description"]}</td>
                    <td class="text-center">${parseFloat(item.convertedQuantity["convertedToLargestUnit"]).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-center">${item.convertedQuantity["uom"]}</td>
                    <td>${item["WhsCode"]}</td>
                    <td class="text-end">${parseFloat(item["UnitPrice"]).toLocaleString('en-US')}</td>
                    <td class="text-end">${parseFloat(item["NetVat"]).toLocaleString('en-US')}</td>
                    <td class="text-end">${parseFloat(item["Vat"]).toLocaleString('en-US')}</td>
                    <td class="text-end">${parseFloat(item["Gross"]).toLocaleString('en-US')}</td>
                </tr>`;
            
            tbody.append(tr);
        });

        var tr = `
                <tr>
                    <th scope="row" class="text-center" style="border-bottom: 0px !important;"></th>
                    <td style="border-bottom: 0px !important;"></td>
                    <td style="border-bottom: 0px !important;"></td>
                    <td class="text-center" style="border-bottom: 0px !important;"></td>
                    <td class="text-center" style="border-bottom: 0px !important;"></td>
                    <td style="border-bottom: 0px !important;"></td>
                    <td class="text-end" style="border-bottom: 0px !important;"></td>
                    <td class="text-end" style="border-bottom: 0px !important;"></td>
                    <td class="text-end fw-semibold" style="border-bottom: 2px solid #000 !important;">TOTAL:</td>
                    <td class="text-end" style="border-bottom: 2px solid #000 !important;">${total.toLocaleString('en-US')}</td>
                </tr>`;
            
            tbody.append(tr); // Append row to table
    },
    viewMode: async (RRData) => {
        RRModal.fill(RRData[0]);
        RRModal.show();
    },
}

function downloadToCSV(jsonArr){
    console.log('clicked');
    const csvData = Papa.unparse(jsonArr); // Convert JSON to CSV
    var today = new Date().toISOString().split('T')[0];

    // Create a blob and trigger download
    const blob = new Blob([csvData], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `ReceivingReports_${today}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

}

const initVS = {
    liteDataVS: async () => {
        // Initialize VirtualSelect for ship via
        VirtualSelect.init({
            ele: '#filterPOVS',                   // Attach to the element
            options: [
                // { label: "", value: null },
                // { label: "Active", value: 1 },
                // { label: "Deleted", value: 0 },

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