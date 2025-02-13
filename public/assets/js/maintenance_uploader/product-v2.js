console.log("FROM V2");
var MainTH, selectedMain;
var globalApi = "http://127.0.0.1:8000/";

const dataTableCustomBtn = `<div class="main-content buttons w-100 overflow-auto d-flex align-items-center px-2" style="font-size: 12px;">
                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1" id="addBtn">
                                    <div class="btnImg me-2" id="addImg">
                                    </div>
                                    <span>Add new</span>
                                </div>

                                <div class="btn d-flex justify-content-around px-2 align-items-center me-1 actionBtn" id="csvShowBtn">
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

    await datatables.loadProdData();
    await initVS.liteDataVS();

    $('#addBtn').on('click', async function () {
        ProdModal.enable(true);
        ProdModal.clear();
        
        $('#prodMainModal').modal('show');

        $('#deleteProdBtn').hide();
        $('#rePrintPage').hide();
        $('#addProdBtn').show();
        $('#confirmProd').hide();
        $('#editProdBtn').hide();
    });

    $("#addProdBtn").on("click", function () {
        if (ProdModal.isValid()) {
            console.log('clicked');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to add this Product?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: "Yes, Add",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        ProdModal.ProdSave();
                    }
                });
        } else{
            console.log('invalid');
        }

    });

    $("#editProdBtn").on("click", async function () {
        if ($(this).text().toLocaleLowerCase() == 'edit details') {
            ProdModal.enable(true);
            $(this).text('Save changes').removeClass('btn-info').addClass('btn-primary');
            $('#deleteProdBtn').text('Cancel');
            $('#rePrintPage').hide();
            $('#confirmProd').hide();

        } else {
            //save update
            if (ProdModal.isValid()) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    showDenyButton: true,
                    confirmButtonText: "Yes, Update",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        var $selectedStockCode = $('#StockCode').val();
                        await ajax('api/v2/product/' + $selectedStockCode, 'POST', JSON.stringify({
                            data: ProdModal.getData(),
                            _method: "PUT"
                        }), (response) => { // Success callback

                            if (response.success) {
                                $(this).text('Edit details').removeClass('btn-primary').addClass('btn-info');
                                $('#deleteProdBtn').text('Delete');

                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success"
                                });

                                $('#confirmPO').hide();
                                ProdModal.hide();
                                datatables.loadProdData();

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

    $("#deleteProdBtn").on("click", async function () {
        if ($(this).text().toLowerCase() == 'cancel') {

            $(this).text('Delete');

            $('#editProdBtn').removeClass('btn-primary').addClass('btn-info');
            $('#editProdBtn').text('Edit details');

            ProdModal.fill(selectedMain);
            ProdModal.enable(false);
            $('#confirmProd').hide();
            $('#rePrintPage').show();

            // ItemsTH.column(6).visible(false);

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
                    var $selectedStockCode = $('#StockCode').val();
                    ajax('api/v2/product/' + $selectedStockCode, 'POST', JSON.stringify({ 
                        _method: 'DELETE' 
                    }), (response) => { // Success callback
                        
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success"
                            });

                            datatables.loadProdData();
                            ProdModal.hide();

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

    $("#ProductTable").on("click", "tbody tr", async function () {
        // selectedMain = ajaxMainData.find(item => item.id == $(this).attr('id'));
        const selectedStockCode = $(this).attr('id');
        await ajax('api/v2/product/' + selectedStockCode, 'GET', null, (response) => { // Success callback

            if (response.success == 1) {
                ProdModal.viewMode(response.data);
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
    loadProdData: async () => {
        const prodData = await ajax('api/v2/product', 'GET', null, (response) => { // Success callback
            console.log(response);
            datatables.initProdDatatable(response);
            // ajaxMainData = response.data;
        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });

    },

    initProdDatatable: (response) => {
        if (response.success) {
            if (MainTH) {
                MainTH.clear().draw();
                MainTH.rows.add(response.data).draw();
            } else {
                MainTH = $('#ProductTable').DataTable({
                    data: response.data,
                    layout: {
                        topStart: function () {
                            return $(dataTableCustomBtn);
                        }
                    },
                    columns: [
                        { data: 'StockCode' },
                        { data: 'Description' },
                        { data: 'LongDesc' },
                        { data: 'AlternateKey1' },
                        // { data: 'AlternateKey2' },
                        // { data: 'EccUser' },
                        { data: 'StockUom' },
                        { data: 'AlternateUom' },
                        { data: 'OtherUom' },
                        { data: 'ConvFactAltUom' },
                        // { data: 'ConvMulDiv' },
                        { data: 'ConvFactOthUom' },
                        // { data: 'MulDiv' },
                        { data: 'Mass' },
                        { data: 'Volume' },
                        // { data: 'Decimals' },
                        // { data: 'PriceCategory' },
                        // { data: 'PriceMethod' },
                        // { data: 'Supplier' },
                        // { data: 'CycleCount' },
                        { data: 'ProductClass' },
                        // { data: 'TaxCode' },
                        // { data: 'OtherTaxCode' },
                        // { data: 'ListPriceCode' },
                        // { data: 'SerialMethod' },
                        // { data: 'InterfaceFlag' },
                        // { data: 'KitType' },
                        // { data: 'LowLevelCode' },
                        // { data: 'Buyer' },
                        // { data: 'Planner' },
                        // { data: 'TraceableType' },
                        // { data: 'MpsFlag' },
                        // { data: 'BulkIssueFlag' },
                        // { data: 'AbcClass' },
                        // { data: 'LeadTime' },
                        // { data: 'StockMovementReq' },
                        // { data: 'ClearingFlag' },
                        // { data: 'SupercessionDate' },
                        // { data: 'AbcAnalysisReq' },
                        // { data: 'AbcCostingReq' },
                        // { data: 'CostUom' },
                        // { data: 'MinPricePct' },
                        // { data: 'LabourCost' },
                        // { data: 'MaterialCost' },
                        // { data: 'FixOverhead' },
                        // { data: 'VariableOverhead' },
                        // { data: 'PartCategory' },
                        // { data: 'DrawOfficeNum' },
                        { data: 'WarehouseToUse' },
                        // { data: 'BuyingRule' },
                        // { data: 'SpecificGravity' },
                        // { data: 'ImplosionNum' },
                        // { data: 'Ebq' },
                        // { data: 'ComponentCount' },
                        // { data: 'FixTimePeriod' },
                        // { data: 'PanSize' },
                        // { data: 'DockToStock' },
                        // { data: 'OutputMassFlag' },
                        // { data: 'ShelfLife' },
                        // { data: 'Version' },
                        // { data: 'Release' },
                        // { data: 'DemandTimeFence' },
                        // { data: 'MakeToOrderFlag' },
                        // { data: 'ManufLeadTime' },
                        // { data: 'GrossReqRule' },
                        // { data: 'PercentageYield' },
                        // { data: 'AbcPreProd' },
                        // { data: 'AbcManufacturing' },
                        // { data: 'AbcSales' },
                        // { data: 'AbcCumPreProd' },
                        // { data: 'AbcCumManuf' },
                        // { data: 'WipCtlGlCode' },
                        // { data: 'ResourceCode' },
                        // { data: 'GstTaxCode' },
                        // { data: 'PrcInclGst' },
                        // { data: 'SerEntryAtSale' },
                        // { data: 'StpSelection' },
                        // { data: 'UserField1' },
                        // { data: 'UserField2' },
                        // { data: 'UserField3' },
                        // { data: 'UserField4' },
                        // { data: 'UserField5' },
                        // { data: 'TariffCode' },
                        // { data: 'SupplementaryUnit' },
                        // { data: 'EbqPan' },
                        // { data: 'StdLandedCost' },
                        // { data: 'LctRequired' },
                        // { data: 'StdLctRoute' },
                        // { data: 'IssMultLotsFlag' },
                        // { data: 'InclInStrValid' },
                        // { data: 'StdLabCostsBill' },
                        // { data: 'PhantomIfComp' },
                        // { data: 'CountryOfOrigin' },
                        // { data: 'StockOnHold' },
                        // { data: 'StockOnHoldReason' },
                        // { data: 'EccFlag' },
                        // { data: 'StockAndAltUm' },
                        // { data: 'AltUnitChar' },
                        // { data: 'JobsOnHold' },
                        // { data: 'JobHoldAllocs' },
                        // { data: 'PurchOnHold' },
                        // { data: 'SalesOnHold' },
                        // { data: 'MaintOnHold' },
                        // { data: 'BatchBill' },
                        // { data: 'BlanketPoExists' },
                        // { data: 'CallOffBpoExists' },
                        // { data: 'DistWarehouseToUse' },
                        // { data: 'JobClassification' },
                        // { data: 'SubContractCost' },
                        // { data: 'DateStkAdded' },
                        // { data: 'InspectionFlag' },
                        // { data: 'SerialPrefix' },
                        // { data: 'SerialSuffix' },
                        // { data: 'ReturnableItem' },
                        // { data: 'ProductGroup' },
                        // { data: 'PriceType' },
                        // { data: 'Basis' },
                        // { data: 'ManualCostFlag' },
                        // { data: 'ManufactureUom' },
                        // { data: 'ConvFactMuM' },
                        // { data: 'ManMulDiv' },
                        // { data: 'LookAheadWin' },
                        // { data: 'LoadingFactor' },
                        // { data: 'SupplUnitCode' },
                        // { data: 'StorageSecurity' },
                        // { data: 'StorageHazard' },
                        // { data: 'StorageCondition' },
                        // { data: 'ProductShelfLife' },
                        // { data: 'InternalShelfLife' },
                        // { data: 'AltMethodFlag' },
                        // { data: 'AltSisoFlag' },
                        // { data: 'AltReductionFlag' },
                        // { data: 'WithTaxExpenseType' },
                        // { data: 'UsesPrefSupplier' },
                        // { data: 'PrdRecallFlag' },
                        // { data: 'OnHoldReason' },
                        // { data: 'SpecificGravity6' },
                        // { data: 'SuppUnitFactor' },
                        // { data: 'SuppUnitsMulDiv' },
                        // { data: 'QmInspectionReq' },
                    ],
                    columnDefs: [
                        // { className: "text-start", targets: [5, 6] },
                        { className: "text-center", targets: [4, 5, 6, 12] },
                        { className: "text-end", targets: [7, 8, 9, 10] },
                    ],
                    scrollCollapse: true,
                    scrollY: '100%',
                    scrollX: '100%',
                    "createdRow": function (row, data) {
                        $(row).attr('id', data.StockCode);
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

            // return respond.status_response == 1 ? true : false;
        }
    },
}

const ProdModal = {
    isValid: () => {
        return $('#modalFields').valid();
    },
    hide: () => {
        $('#prodMainModal').modal('hide');
    },
    show: () => {
        $('#prodMainModal').modal('show');
    },
    clear: () => {
        $('#modalFields input[type="text"]').val('');
        $('#modalFields input[type="number"]').val('');
        $('#modalFields textarea').val('');

    },
    enable: (enable) => {
        $('#modalFields input[type="text"]').prop('disabled', !enable);
        $('#modalFields input[type="number"]').prop('disabled', !enable);

    },
    fill: async (ProdData) => {
        $('#StockCode').val(ProdData.StockCode);
        $('#priceWithVat').val(ProdData.priceWithVat);
        $('#Description').val(ProdData.Description);
        $('#AlternateKey1').val(ProdData.AlternateKey1);
        $('#StockUom').val(ProdData.StockUom);
        $('#AlternateUom').val(ProdData.AlternateUom);
        $('#OtherUom').val(ProdData.OtherUom);
        $('#ConvFactAltUom').val(ProdData.ConvFactAltUom);
        $('#ConvFactOthUom').val(ProdData.ConvFactOthUom);
        $('#Mass').val(ProdData.Mass);
        $('#Volume').val(ProdData.Volume);
        $('#ProductClass').val(ProdData.ProductClass);
        $('#WarehouseToUse').val(ProdData.WarehouseToUse);

    },
    viewMode: async (ProdData) => {
        ProdModal.fill(ProdData);
        $('#deleteProdBtn').show();
        $('#addProdBtn').hide();
        $('#editProdBtn').show();
        $("#editProdBtn").text('Edit details').removeClass('btn-primary').addClass('btn-info');
        $('#confirmProd').hide();
        $('#deleteProdBtn').text('Delete');
        $('#rePrintPage').show();

        ProdModal.enable(false);
        ProdModal.show();
    },
    ProdSave: async () => {

        let ProdData = ProdModal.getData();
        console.log(ProdData);

        await ajax('api/v2/product', 'POST', JSON.stringify({ data: ProdData }), (response) => { // Success callback
            if (response.success) {

                datatables.loadProdData();
                ProdModal.hide();

                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
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

        var user = JSON.parse(localStorage.getItem('user'));
        var data = {
            StockCode: $('#StockCode').val(),
            Description: $('#Description').val(),
            LongDesc: $('#Description').val(),
            AlternateKey1: $('#AlternateKey1').val(),
            StockUom: $('#StockUom').val(),
            AlternateUom: $('#AlternateUom').val(),
            OtherUom: $('#OtherUom').val(),
            ConvFactAltUom: $('#ConvFactAltUom').val(),
            ConvMulDiv: "D",
            ConvFactOthUom: $('#ConvFactOthUom').val(),
            Mass: $('#Mass').val(),
            Volume: $('#Volume').val(),
            ProductClass: $('#ProductClass').val(),
            WarehouseToUse: $('#WarehouseToUse').val(),
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
    }
}

