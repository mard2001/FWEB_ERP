var MainTH, selectedMain;
var globalApi = "http://127.0.0.1:8000/";
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
    // await initVS.liteDataVS();

    $("#rrTable").on("click", "tbody tr", async function () {
        const selectedRRCode = $(this).attr('id');
        await ajax('api/report/v2/rr/' + selectedRRCode, 'GET', null, (response) => { // Success callback
            console.log(response);
            if (response.success == 1) {
                // RRModal.viewMode(response.data);
                // selectedMain = response.data;
                var tempRes = jsonArr.filter(item => item.RRNo == selectedRRCode)
                RRModal.viewMode(tempRes[0]);
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
        window.open("http://127.0.0.1:8000/print/rr/testing", "_blank");
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
                        { data: 'SupplierCode',  title: 'Supplier Code' },
                        { data: 'SupplierName',  title: 'Supplier Name' },
                        { data: 'SupplierTIN',  title: 'Supplier TIN' },
                        { data: 'Address',  title: 'Supplier Address' },
                        { data: 'RRNo',  title: 'RR No.' },
                        { data: 'Date',  title: 'RR Date' },
                        { data: 'Reference',  title: 'RR Reference' },
                        { data: 'Status',  title: 'RR Status' },
                        { data: 'PreparedBy',  title: 'Prepared By' },
                        // { data: 'Total' },
                    ],
                    columnDefs: [
                        // { className: "text-start", targets: [ ] },
                        { className: "text-center", targets: [2,4,7] },
                        // { className: "text-end", targets: [ ] },
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

                        // Select the label element and replace it with a div
                        // $('.dt-search label').replaceWith(function () {
                        //     return $('<div>', {
                        //         html: $(this).html(),
                        //         id: $(this).attr('id'),
                        //         class: $(this).attr('class')
                        //     });
                        // });
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
        // $('#StockCode').val(RRModal.StockCode);
        $('.supCode').html(RRModalData.SupplierCode);
        $('.supName').html(RRModalData.SupplierName);
        $('.supTin').html(RRModalData.SupplierTIN);
        $('.supAdd').html(RRModalData.Address);
        $('.rrNo').html(RRModalData.RRNo);
        $('.date').html(RRModalData.Date);
        $('.reference').html(RRModalData.Reference);
        $('.status').html(RRModalData.Status);

        (RRModalData.items).forEach((item, index) => {
            total += item["Gross"];
            var tr = `
                <tr>
                    <th scope="row" class="text-center">${index + 1}</th>
                    <td>${item["SKU"]}</td>
                    <td>${item["Description"]}</td>
                    <td class="text-center">${item["Quantity"]}</td>
                    <td class="text-center">${item["UOM"]}</td>
                    <td>${item["WhsCode"]}</td>
                    <td class="text-end">${item["UnitPrice"].toLocaleString('en-US')}</td>
                    <td class="text-end">${item["NetVat"].toLocaleString('en-US')}</td>
                    <td class="text-end">${item["Vat"].toLocaleString('en-US')}</td>
                    <td class="text-end">${item["Gross"].toLocaleString('en-US')}</td>
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
        RRModal.fill(RRData);
        RRModal.show();
    },
}