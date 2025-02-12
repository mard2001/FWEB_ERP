var MainTH, selectedMain;
var ItemsTH, selectedItems;
var globalApi = "https://spc.sfa.w-itsolutions.com/";
var ajaxMainData, ajaxItemsData;
var shippedToData, selecteddShippedTo;
var vendordata, selectedVendor;
var itemTmpSave = [];

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

    await datatables.loadPO();
    await initVS.liteDataVS();
    await initVS.bigDataVS();

    $("#POHeaderTable").on("click", "tbody tr", async function () {
        // selectedMain = ajaxMainData.find(item => item.id == $(this).attr('id'));
        const selectedPO = $(this).attr('id');

        await ajax('api/orders/po/' + selectedPO, 'GET', null, (response) => { // Success callback

            if (response.success == 1) {
                POModal.viewMode(response.data);
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


    $('#modalFields').on('hidden.bs.modal', function () {
        POModal.enable(false);
    });


    $("#itemDelete").on("click", async function () {

        Swal.fire({
            title: "Are you sure?",
            text: "Are you sure you want to delete all selected data? You won't be able to revert this!", // Use <br> for line breaks
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                let totalItemDeleted = 0;
                const promises = [];

                // Iterate over DataTable rows
                ItemsTH.rows().every(function () {
                    var row = this.node();
                    var checkbox = $(row).find('input.row-checkbox');

                    if (checkbox.is(':checked')) {
                        // Push the async function as a promise without executing it immediately
                        promises.push((async () => {

                            if (isStatSaveNew()) {
                                const rowdata = this.data();
                                itemTmpSave = itemTmpSave.filter(item => item.StockCode != rowdata.StockCode);
                                totalItemDeleted++;
                            } else {
                                await ajax('api/orders/po-items/' + row.id, 'POST', JSON.stringify({ _method: 'DELETE' }), (response) => { // Success callback
                                    totalItemDeleted++;
                                }, (xhr, status, error) => { // Error callback
                                    console.error('Error:', error);
                                });
                            }
                        })()); // Executes the function immediately
                    }
                });

                // Wait for all promises to complete
                await Promise.all(promises);

                // Now, show success message after deletion is truly completed
                Swal.fire({
                    title: "Deleted!",
                    text: `Total ${totalItemDeleted} items have been deleted.`,
                    icon: "success",
                    confirmButtonColor: "#3085d6"
                });

                $(this).prop('disabled', true);

                if (isStatSaveNew()) {
                    datatables.initPOItemsDatatable(itemTmpSave);
                    calculateCost();
                } else {
                    await datatables.loadItems(selectedMain.PONumber);
                    calculateCost();

                }
            }

        });

    });

    $("#itemTables").on("click", "tbody tr", function (event) {

        // Find the checkbox inside the row
        var checkbox = $(this).find('.form-check-input');

        // Check if the clicked element is the checkbox itself
        if (!$(event.target).is(checkbox)) {
            // Toggle the checkbox state

            if (ItemsTH && ItemsTH.data().any()) {

                // var data = $('#saveEdit').text().toLowerCase() == 'save' ? tmpItemList : itemAjaxData
                // itemSelectedData = data.find(item => item.id == $(this).attr('id'));

                // POItemsModal.show();
                // $('#itemSaveEdit').text('Edit details').addClass('btn-info').removeClass('btn-primary');

                // $('#itemModalFields input[type="text"]').prop('disabled', true);
                // $('#itemModalFields input[type="number"]').prop('disabled', true);

                // elementAndColumn.forEach(element => {
                //     document.querySelector(element.element).disable();
                // });

                // if (document.querySelector('#UOMDropDown')?.virtualSelect) {
                //     document.querySelector('#UOMDropDown').disable();
                // }

                // itemModalFillField();

                // $('#PricePerUnit').val(itemSelectedData.PricePerUnit);
            }


        } else {
            $(this).toggleClass('rowSelected');

            let isAnyChecked = false;

            // Loop through each row in the DataTable
            ItemsTH.rows().every(function () {
                var row = this.node();
                var checkbox = $(row).find('input.row-checkbox');

                if (checkbox.is(':checked')) {
                    isAnyChecked = true; // At least one checkbox is checked
                    return false; // Exit the loop early since we found a checked checkbox
                }
            });

            $('#itemDelete').prop('disabled', !isAnyChecked);
        }


    });


    $(document).on('click', '.itemDeleteIcon', async function () {

        // console.log($(this).parent());
        const row = $(this).closest('tr');

        if (isStatSaveNew()) {
            const skuCode = row.find('td:first'); // Get the first <td>
            itemTmpSave = itemTmpSave.filter(item => item.StockCode != skuCode.text());

            datatables.initPOItemsDatatable(itemTmpSave);
            calculateCost();

        } else {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete all selected data? You won't be able to revert this!", // Use <br> for line breaks
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(async (result) => {
                if (result.isConfirmed) {

                    const itemId = row.attr('id');
                    await ajax('api/orders/po-items/' + itemId, 'POST', JSON.stringify({ _method: 'DELETE' }), async (response) => { // Success callback
                        // Now, show success message after deletion is truly completed
                        if (response.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#3085d6"
                            });

                            await datatables.loadItems(selectedMain.PONumber);
                            calculateCost();
                        } else {
                            Swal.fire({
                                title: "Opppps..",
                                text: response.message,
                                icon: "error"
                            });
                        }

                    }, (xhr, status, error) => { // Error callback
                        Swal.fire({
                            title: "Opppps..",
                            text: xhr.responseJSON.message,
                            icon: "error"
                        });
                    });
                }

            });


        }



    });

    $(document).on('click', '#addBtn', async function () {
        POModal.enable(true);
        POModal.clear();
        
        $('#editXmlDataModal').modal('show');

        $('#deleteBtn').hide();
        $('#rePrintPage').hide();
        $('#saveBtn').show();
        $('#editBtn').hide();
        itemTmpSave = [];
        selectedMain = null;

        datatables.initPOItemsDatatable(null);
        $('#confirmPO').hide();
        $('#addItems').show();
        ItemsTH.column(6).visible(true);


    });

    $("#confirmPO").on("click", async function () {

        Swal.fire({
            title: "Confirm Purchase Order",
            text: "Are you sure you want to proceed with this purchase order? This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, proceed with the order!"
        }).then(async (result) => {
            if (result.isConfirmed) {

                await ajax('api/orders/po-confirm/' + selectedMain.id, 'POST', null, (response) => { // Success callback
                    if (response.success) {

                        datatables.loadPO();
                        POModal.hide();

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
            }
        });


    });

    $("#deleteBtn").on("click", async function () {
        if ($(this).text().toLowerCase() == 'cancel') {

            $(this).text('Delete');

            $('#editBtn').removeClass('btn-primary').addClass('btn-info');
            $('#editBtn').text('Edit details');

            POModal.fill(selectedMain);
            POModal.enable(false);
            $('#confirmPO').show();
            ItemsTH.column(6).visible(false);


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
                    ajax('api/orders/po/' + selectedMain.id, 'POST', JSON.stringify({ _method: 'DELETE' }), (response) => { // Success callback
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success"
                            });

                            datatables.loadPO();
                            POModal.hide();

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

    // Set up a MutationObserver to watch for changes in the container's visibility
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === 'style') {
                const isVisible = $('#editXmlDataModal').is(':visible');
                if (isVisible && ItemsTH) {
                    ItemsTH.columns.adjust();
                    ItemsTH.draw();

                }
            }
        });
    });

    // Start observing the container for attribute changes
    observer.observe(document.getElementById('editXmlDataModal'), {
        attributes: true // Configure it to listen to attribute changes
    });

    $("#addItems").on("click", function () {
        POItemsModal.show();
        POItemsModal.clear();
        POItemsModal.enable(true);

        $('#itemEdit').hide();
        $('#itemSave').show();
    });

    $("#editBtn").on("click", async function () {
        if ($(this).text().toLocaleLowerCase() == 'edit details') {
            POModal.enable(true);
            $(this).text('Save changes').removeClass('btn-info').addClass('btn-primary');
            $('#deleteBtn').text('Cancel');
            $('#confirmPO').hide();

            //set the selected vendor to the to be edit vendor
            const modalCurrentVendor = $('#vendorName').val();
            selectedVendor = vendordata.find(item => item.id = modalCurrentVendor);
            ItemsTH.column(6).visible(true);
            $('#addItems').show();



        } else {
            //save update
            if (POModal.isValid()) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    showDenyButton: true,
                    confirmButtonText: "Yes, Update",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {

                        await ajax('api/orders/po/' + selectedMain.id, 'POST', JSON.stringify({
                            data: POModal.getData(),
                            _method: "PUT"
                        }), (response) => { // Success callback

                            if (response.success) {
                                // datatables.loadItems(selectedMain.PONumber);
                                $(this).text('Edit details').removeClass('btn-primary').addClass('btn-info');
                                $('#deleteBtn').text('Delete');

                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success"
                                });

                                $('#confirmPO').show();
                                POModal.hide();
                                datatables.loadPO();

                                ItemsTH.column(6).visible(false);


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


    $("#Quantity").on("input", function () {
        autoCalculateTotalPrice();
    });

    $("#itemSave").on("click", function () {

        if (POItemsModal.isValid() && $('#TotalPrice').val() && +$('#TotalPrice').val() > 0) {


            const currentItems = ItemsTH.rows().data().toArray();
            const getItem = POItemsModal.getData();
            const isAlreadyExist = currentItems.some(item => item.StockCode == getItem.StockCode);

            if (isAlreadyExist) {
                Swal.fire({
                    title: "Item exist",
                    text: "Item exist already in the order list cannot saved.",
                    icon: "warning"
                });
                return;
            }

            //check if saving
            if (isStatSaveNew()) {

                getItem.PRD_INDEX = itemTmpSave ? itemTmpSave.length + 1 : 1;
                POItemsModal.itemTmpSave(getItem);
                calculateCost();

            } else {

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to add this data?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: "Yes, Add",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        await POItemsModal.itemAPISave(getItem);

                    }
                });


            }
        }

    });

    $("#saveBtn").on("click", function () {

        if (POModal.isValid()) {

            if (itemTmpSave.length < 1) {
                Swal.fire({
                    title: 'No items',
                    text: 'Please review your order. No items have been added for purchase.',
                    icon: 'error',
                });

            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to add this data?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: "Yes, Add",
                    denyButtonText: `Cancel`
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        POModal.POSave();

                    }
                });
            }



        }

    });


});

function autoCalculateTotalPrice() {
    $('#TotalPrice').val((+$('#Quantity').val() || 0) * (+$('#PricePerUnit').val() || 0).toFixed(2));
}

function calculateCost() {
    const taxCost = 0;
    const shippingCost = 0;
    const othersCost = 0;
    const grandTotal = ItemsTH.rows().data().toArray().reduce((sum, item) => sum + parseFloat(item.TotalPrice), 0);

    $('#taxCost').text(formatMoney(taxCost));
    $('#shippingCost').text(formatMoney(shippingCost));
    $('#othersCost').text(formatMoney(othersCost));
    $('#grandTotal').text(formatMoney(grandTotal));
    $('#subTotal').text(formatMoney(grandTotal));
}


const initVS = {
    liteDataVS: async () => {
        // Initialize VirtualSelect for ship via
        VirtualSelect.init({
            ele: '#shipVia',                   // Attach to the element
            options: [
                { label: "Road Delivery", value: "road_delivery" },
                { label: "Air Freight", value: "air_freight" }
            ],                                 // Provide options
            maxWidth: '100%',                  // Set maxWidth
            multiple: false,                   // Enable multiselect
            hideClearButton: true,             // Hide clear button
            disabledOptions: ['air_freight'],
            selectedValue: 'road_delivery',    // Preselect (must match `value`)
        });

        // Initialize VirtualSelect for ship via
        VirtualSelect.init({
            ele: '#filterPOVS',                   // Attach to the element
            options: [
                { label: "Pending PO", value: null },
                { label: "Confirmed PO", value: 1 },
                { label: "other status PO", value: "air_freight" },

            ],                                 // Provide options
            multiple: true,                   // Enable multiselect
            hideClearButton: true,             // Hide clear button
            search: false,
            maxWidth: '100%',                  // Set maxWidth
            additionalClasses: 'rounded',
            additionalDropboxClasses: 'rounded',
            additionalDropboxContainerClasses: 'rounded',
            additionalToggleButtonClasses: 'rounded',
            // selectedValue: 'road_delivery',    // Preselect (must match `value`)
        });

        //load vendors
        await ajax('api/vendors', 'GET', null, (response) => { // Success callback
            // shippedToData = response.data;
            vendordata = response.data;
            const newData = vendordata.map(item => {
                // Create a new object with the existing properties and the new column
                return {
                    description: item.CompleteAddress,
                    value: item.cID, // Spread the existing properties
                    label: item.SupplierName, // Copy the value from sourceKey to targetKey
                };
            });

            // Check if the VirtualSelect instance exists before destroying
            if (document.querySelector('#vendorName')?.virtualSelect) {
                document.querySelector('#vendorName').destroy();
            }

            VirtualSelect.init({
                ele: '#vendorName',
                options: newData,
                markSearchResults: true,
                maxWidth: '100%',
                search: true,
                autofocus: true,
                hasOptionDescription: true,
                noSearchResultsText: `<div class="w-100 d-flex justify-content-around align-items-center mt-2">
                                                <div class="w-auto text-center">
                                                     No result found. Add new?
                                                </div>
                                                <div class="w-auto">
                                                    <button id="CustomerNoDataFoundBtn" type="button" class="btn btn-primary btn-sm">Add new</button>
                                                </div>
                                            </div>`,

            });

            $('#vendorName').on('afterClose', function () {
                if (this.value) {
                    var findVendor = vendordata.find(item => item.cID == this.value);

                    $('#VendorContactName').val(findVendor.ContactPerson);
                    $('#vendorAddress').val(findVendor.CompleteAddress);

                    var mobileContact = findVendor.ContactNo = /^9\d{9}$/.test(findVendor.ContactNo) ? findVendor.ContactNo.replace(/^9/, "09") : findVendor.ContactNo;

                    selectedVendor = findVendor;

                    $('#vendorPhone').val(mobileContact);

                    if (this.value && $('#shippedToName').value) {
                        $('#addItems').prop("disabled", false);
                    }

                    $('#shippingTerms').val(findVendor.TermsCode);


                }

            });

            $('#vendorName').on('reset', function () {
                $('#VendorContactName').val('');
                $('#vendorAddress').val('');
                $('#vendorPhone').val('');
                selectedVendor = null;
            });

        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });

        //shippedToData
        await ajax('api/supplier-shipped-to', 'GET', null, (response) => { // Success callback
            shippedToData = response.data;

            const newData = response.data.map(item => {
                // Create a new object with the existing properties and the new column
                return {
                    value: item.cID, // Spread the existing properties
                    label: item.CompleteAddress, // Copy the value from sourceKey to targetKey
                };
            });

            // Check if the VirtualSelect instance exists before destroying
            if (document.querySelector('#shippedToName')?.virtualSelect) {
                document.querySelector('#shippedToName').destroy();
            }

            VirtualSelect.init({
                ele: '#shippedToName',
                options: newData,
                markSearchResults: true,
                maxWidth: '100%',
                search: true,
                autofocus: true,
                hasOptionDescription: true,
                noSearchResultsText: `<div class="w-100 d-flex justify-content-around align-items-center mt-2">
                                    <div class="w-auto text-center">
                                         No result found. Add new?
                                    </div>
                                    <div class="w-auto">
                                        <button id="ShipperNoDataFoundBtn" type="button" class="btn btn-primary btn-sm">Add new</button>
                                    </div>
                                </div>`,

            });

            $('#shippedToName').on('afterClose', function () {
                if (this.value) {
                    var findSupplier = response.data.find(item => item.cID == this.value);

                    $('#shippedToContactName').val(findSupplier.ContactPerson.trim());
                    $('#shippedToAddress').val(findSupplier.CompleteAddress.trim());

                    var mobileContact = findSupplier.ContactNo = /^9\d{9}$/.test(findSupplier.ContactNo) ? findSupplier.ContactNo.replace(/^9/, "09") : findSupplier.ContactNo;

                    $('#shippedToPhone').val(mobileContact);

                    if (this.value && document.querySelector('#vendorName').value) {
                        $('#addItems').prop("disabled", false);
                    }

                    console.log(this.value);
                }

            });

            $('#shippedToName').on('reset', function () {
                $('#shippedToContactName').val('');
                $('#shippedToAddress').val('');
                $('#shippedToPhone').val('');
            });

        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });
    },

    bigDataVS: async () => {
        await ajax('api/product', 'GET', null, (response) => { // Success callback
            const products = response.data;

            const newData = products.map(item => {
                // Create a new object with the existing properties and the new column
                return {
                    description: item.Description,
                    value: item.StockCode, // Spread the existing properties
                    label: item.StockCode, // Copy the value from sourceKey to targetKey
                };
            });

            // Check if the VirtualSelect instance exists before destroying
            if (document.querySelector('#StockCode')?.virtualSelect) {
                document.querySelector('#StockCode').destroy();
            }

            // Initialize VirtualSelect
            VirtualSelect.init({
                ele: '#StockCode',        // Attach to the element
                options: newData,                 // Provide options
                maxWidth: '100%',                 // Set maxWidth
                autofocus: true,
                search: true,
                hasOptionDescription: true,

            });

            $('#StockCode').on('afterClose', async function () {
                if (this.value) {
                    const stockCode = this.value;
                    var findProduct = products.find(item => item.StockCode == stockCode);
                    $('#Decription').val(findProduct.Description);

                    let priceCode;

                    if (isStatSaveNew()) {
                        priceCode = selectedVendor.PriceCode.trim();
                    } else {

                        var findSupplier = vendordata.find(item => item.SupplierName == selectedMain.SupplierName);
                        priceCode = findSupplier.PriceCode.trim();
                    }

                    const getPriceBody = {
                        stockCode: stockCode,
                        priceCode: priceCode
                    };

                    await ajax('api/getProductPrice', 'GET', getPriceBody, (response) => { // Success callback

                        if (response.status_response == 1) {
                            $('#PricePerUnit').val(response.response.UNITPRICE);

                        } else {
                            console.error('Error:', response);

                        }

                    }, (xhr, status, error) => { // Error callback
                        console.error('Error:', error);
                    });

                    //UOM Dorpdown
                    var uomColumn = ["StockUom", "AlternateUom", "OtherUom"];

                    // Create a new object with the existing properties and the new column
                    var uoms = uomColumn.map(item => {
                        return {
                            value: findProduct[item], // Spread the existing properties
                            label: findProduct[item], // Copy the value from sourceKey to targetKey
                        };
                    });


                    // Remove duplicates from `newData` based on the `value` property
                    uoms = uoms.filter((item, index, self) =>
                        index === self.findIndex(other => other.value === item.value)
                    );


                    const UOMHtml = (data) => {
                        return ``;
                    }

                    $('.UOMField').addClass('d-none');
                    uoms.forEach(item => {
                        $(`#${item.value}Div`).removeClass('d-none');
                    });




                    // // Initialize VirtualSelect
                    // VirtualSelect.init({
                    //     ele: '#UOMDropDown',             // Attach to the element
                    //     options: uoms,                 // Provide options
                    //     maxWidth: '100%',                 // Set maxWidth
                    //     multiple: false,                   // Enable multiselect
                    //     hideClearButton: true,            // Hide clear button
                    //     selectedValue: [uoms[0]?.value], // Preselect (must match `value`)
                    // });

                    // document.querySelector('#UOMDropDown').enable();
                    autoCalculateTotalPrice();

                }

            });


            $('#StockCode').on('reset', function () {
                $('#Decription').val('');
                $('#PricePerUnit').val('');
            });

        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });
    }

}

const POModal = {
    isValid: () => {
        return $('#modalFields').valid();
    },
    hide: () => {
        $('#editXmlDataModal').modal('hide');
    },
    show: () => {
        $('#editXmlDataModal').modal('show');
    },
    fill: async (POData) => {
        const findVendor = vendordata.find(item => item.SupplierCode.trim() == POData.SupplierCode.trim());
        selectedVendor = findVendor;
        document.querySelector('#vendorName').setValue(findVendor.cID)
        $('#VendorContactName').val(findVendor.ContactPerson);
        $('#vendorAddress').val(findVendor.CompleteAddress);
        $('#vendorPhone').val(findVendor.ContactNo);

        $('#shippedToContactName').val(POData.contactPerson);
        $('#shippedToAddress').val(POData.deliveryAddress);
        $('#shippedToName').val(POData.deliveryAddress);
        document.querySelector('#shippedToName').setValue(POData.deliveryAddress)

        $('#shippedToPhone').val(POData.contactNumber);
        $('#deliveryMethod').val(POData.deliveryMethod);

        $('#requisitioner').val(POData.EncoderID);
        $('#fob').val(POData.FOB);
        $('#subTotal').text(formatMoney(POData.subTotal));
        $('#totalDiscount').val(formatMoney(POData.totalDiscount));
        $('#others').val(formatMoney(POData.othersCost));
        $('#grandTotal').text(formatMoney(POData.totalCost));
        $('#poComment').val(POData.SpecialInstruction);
        $('#taxCost').text(formatMoney(POData.totalTax));
        $('#shippingTerms').val(POData.TermsCode);
    },
    clear: () => {
        $('#modalFields input[type="text"]').val('');
        $('#modalFields input[type="number"]').val('');
        $('#modalFields textarea').val('');

        if (document.querySelector('#vendorName')?.virtualSelect) {
            document.querySelector('#vendorName').reset();

        }

        if (document.querySelector('#shippedToName')?.virtualSelect) {
            document.querySelector('#shippedToName').reset();

        }

        $('#subTotal').text(formatMoney(0));
        $('#taxCost').text(formatMoney(0));
        $('#totalItemsLabel').text('0');
        $('#grandTotal').text(formatMoney(0));

    },
    enable: (enable) => {
        $('#modalFields input[type="text"]').prop('disabled', !enable);
        $('#modalFields input[type="number"]').prop('disabled', !enable);
        $('#modalFields textarea').prop('disabled', !enable);
        $("#itemTables").find('input[type="checkbox"]').prop('disabled', !enable);

        $('#addItems').prop('disabled', !enable);

        if (enable) {
            document.querySelector('#vendorName').enable();
            document.querySelector('#shippedToName').enable();
            document.querySelector('#shipVia').enable();

        } else {
            document.querySelector('#vendorName').disable();
            document.querySelector('#shippedToName').disable();
            document.querySelector('#shipVia').disable();

        }



    },
    getData: () => {

        var user = JSON.parse(localStorage.getItem('user'));
        var data = {
            PODate: moment().format('YYYY-MM-DD'),
            SupplierCode: selectedVendor.SupplierCode.trim(),
            SupplierName: selectedVendor.SupplierName,
            productType: selectedVendor.SupplierType,
            FOB: $('#fob').val(),
            deliveryAddress: $('#shippedToAddress').val(),
            contactPerson: $('#shippedToContactName').val(),
            contactNumber: $('#shippedToPhone').val(),
            deliveryMethod: $('#shipVia').val(),
            totalDiscount: 0,
            totalTax: parseMoney($('#taxCost').text()),
            SpecialInstruction: $('#poComment').val(),
            EncoderID: $('#requisitioner').val(),
            orderPlacer: user.name,
            orderPlacerEmail: user.email,
            subTotal: parseMoney($('#subTotal').text()),
            TermsCode: $('#shippingTerms').val(),
            totalCost: parseMoney($('#grandTotal').text())
        }

        return data;
    },
    viewMode: async (POData) => {
        POModal.fill(POData);
        datatables.initPOItemsDatatable(POData.p_o_items)
        $('#deleteBtn').show();
        $('#saveBtn').hide();
        $('#editBtn').show();
        $("#editBtn").text('Edit details').removeClass('btn-primary').addClass('btn-info');
        ItemsTH.column(6).visible(false);
        $('#addItems').hide();

        if (POData.POStatus == null) {
            $('#confirmPO').show();
            $('#itemBtns').show();
            // ItemsTH.column(6).visible(true);
            $('#rePrintPage').hide();
            

        } else {
            $('#itemBtns').hide();
            // ItemsTH.column(6).visible(false);
            $('#confirmPO').hide();
            $('#editBtn').hide();
            $('#deleteBtn').hide();
            $('#rePrintPage').show();

        }

        POModal.enable(false);
        POModal.show();
    },
    POSave: async () => {

        let POData = POModal.getData();
        POData.Items = itemTmpSave.map((item, index) => ({
            ...item,        // ✅ Spread all properties from item
            PRD_INDEX: index + 1 // ✅ Add the new index property
        }));

        await ajax('api/orders/po', 'POST', JSON.stringify({ data: POData }), (response) => { // Success callback
            if (response.success) {

                datatables.loadPO();
                POModal.hide();

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

    }
}

const POItemsModal = {
    isValid: () => {
        return $('#itemModalFields').valid();
    },
    hide: () => {
        $('#itemModal').modal('hide');
    },
    show: () => {
        $('#itemModal').modal('show');
    },
    fill: (itemData) => {
        $('#Decription').val(itemData.Decription);
        $('#Quantity').val(itemData.Quantity);
        $('#UOM').val(itemData.UOM);
        $('#ItemVolume').val(itemData.ItemVolume);
        $('#ItemWeight').val(itemData.ItemWeight);
        $('#TotalPrice').val(itemData.TotalPrice);
        $('#PricePerUnit').val(itemData.PricePerUnit);
        document.querySelector('#StockCode').setValue(itemData.StockCode)
    },
    clear: () => {
        $('#itemModalFields input[type="text"]').val('');
        $('#itemModalFields input[type="number"]').val('');
        $('#itemModalFields textarea').val('');

        if (document.querySelector('#UOMDropDown')?.virtualSelect) {
            document.querySelector('#UOMDropDown').reset();
        }

        if (document.querySelector('#StockCode')?.virtualSelect) {
            document.querySelector('#StockCode').reset();
        }

    },
    enable: (enable) => {
        $('#itemModalFields input[type="text"]').prop('disabled', !enable);
        $('#itemModalFields input[type="number"]').prop('disabled', !enable);
        $('#itemModalFields textarea').prop('disabled', !enable);
    },
    getData: () => {

        return {
            StockCode: $('#StockCode').val(),
            Decription: $('#Decription').val(),
            UOM: $('#UOMDropDown').val(),
            Quantity: $('#Quantity').val(),
            PricePerUnit: $('#PricePerUnit').val(),
            TotalPrice: $('#TotalPrice').val(),

        }
    },
    itemTmpSave: (getItem) => {
        itemTmpSave.unshift(getItem);
        datatables.initPOItemsDatatable(itemTmpSave);
        POItemsModal.hide();

    },
    itemAPISave: async (item) => {

        item.PONumber = selectedMain.PONumber;
        item.PRD_INDEX = ItemsTH.rows().data().toArray().length + 1;

        await ajax('api/orders/po-items', 'POST', JSON.stringify({ data: item }), (response) => { // Success callback
            if (response.success) {
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
                });

                POItemsModal.hide();
                datatables.loadItems(item.PONumber);
                calculateCost();

            }

        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });
    }
}

const datatables = {
    loadPO: async () => {
        const poData = await ajax('api/orders/po', 'GET', null, (response) => { // Success callback
            datatables.initPODatatable(response);
            ajaxMainData = response.data;
        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });

    },
    loadItems: async (PONumber) => {

        const poItems = await ajax('api/orders/po-items/search-items/' + PONumber, 'GET', null, (response) => { // Success callback
            ajaxItemsData = response.data;
            datatables.initPOItemsDatatable(ajaxItemsData);

        }, (xhr, status, error) => { // Error callback
            console.error('Error:', error);
        });


    },
    initPODatatable: (response) => {
        if (response.success) {
            if (MainTH) {
                MainTH.clear().draw();
                MainTH.rows.add(response.data).draw();
            } else {
                MainTH = $('#POHeaderTable').DataTable({
                    data: response.data,
                    layout: {
                        topStart: function () {
                            return $(dataTableCustomBtn);
                        }
                    },
                    columns: [
                        { data: 'OrderNumber' },
                        { data: 'PONumber' },
                        {
                            data: 'POStatus',
                            render: function (data, type, row) {
                                return data != null ? 'Confirmed' : 'Pending';
                            }
                        },
                        { data: 'SupplierName' },
                        { data: 'PODate' },
                        { data: 'orderPlacer' },
                        {
                            data: 'totalDiscount',
                            render: function (data, type, row) {
                                return formatMoney(data);
                            }
                        },
                        {
                            data: 'totalCost',
                            render: function (data, type, row) {
                                return formatMoney(data);
                            }
                        },
                    ],
                    columnDefs: [
                        { className: "text-end", targets: [5, 6] },
                    ],
                    scrollCollapse: true,
                    scrollY: '100%',
                    scrollX: '100%',
                    "createdRow": function (row, data) {
                        $(row).attr('id', data.id);
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
                        $('.dt-search label').replaceWith(function () {
                            return $('<div>', {
                                html: $(this).html(),
                                id: $(this).attr('id'),
                                class: $(this).attr('class')
                            });
                        });
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
    initPOItemsDatatable: (datas) => {
        if (ItemsTH) {
            ItemsTH.clear().draw();
            datas && ItemsTH.rows.add(datas).draw();
        } else {
            ItemsTH = $('#itemTables').DataTable({
                data: datas,
                columns: [
                    { data: 'StockCode' },
                    { data: 'Decription' },
                    { data: 'Quantity' },
                    { data: 'UOM' },
                    {
                        data: 'PricePerUnit',
                        render: function (data, type, row) {
                            return formatMoney(data);
                        }
                    },
                    {
                        data: 'TotalPrice',
                        render: function (data, type, row) {
                            return formatMoney(data);
                        }
                    },
                    {
                        data: null, // Placeholder for checkbox
                        render: function (data, type, row) {
                            // return '<div class="form-check d-flex justify-content-center align-items-center"><input type="checkbox" class="form-check-input row-checkbox cursor-pointer hover:bg-light" data-id="' + row.id + '"></div>';

                            return ` <div class="d-flex actIcon">
                                        <div class="w-50 d-flex justify-content-center">
                                            <i class="fa-regular fa-pen-to-square fa-lg text-primary m-auto "></i>
                                        </div>
                                        <div class="w-50 d-flex justify-content-center itemDeleteIcon"(${data.id})">
                                            <i class="fa-solid fa-trash fa-lg text-danger m-auto"></i>
                                        </div>
                                    </div>`

                            return `<div class="w-100 d-flex justify-content-around actIcon">
                                        <i class="fa-regular fa-pen-to-square fa-lg text-primary"></i>
                                        <i class="fa-solid fa-trash fa-lg text-danger"></i>
                                    </div>`;
                        },
                        orderable: false, // Prevent sorting on the checkbox column
                        searchable: false,  // Disable search on the checkbox column
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            // Add class to the parent <td> element dynamically
                            $(cell).addClass('nhover');
                        }
                    },

                ],
                columnDefs: [
                    { className: "text-center", targets: [0, 2, 3] },
                    { className: "text-end", targets: [4, 5] },
                ],
                searching: false,
                scrollCollapse: true,
                responsive: true, // Enable responsive modeoWidth: true, // Enable auto-width calculation
                scrollY: '100%',
                scrollX: '100%',
                "createdRow": function (row, data) {
                    $(row).attr('id', data.id);
                },
                "lengthChange": false,  // Hides the per page dropdown
                "info": false,          // Hides the bottom text (like "Showing x to y of z entries")
                "paging": false,        // Hides the pagination controls (Next, Previous, etc.)
            });

        }

        $('#totalItemsLabel').text(datas ? datas.length : 0);
    },

    deleteItem: (itemId) => {


        async function bulkDelete() {
            let totalItemDeleted = 0;
            const promises = [];

            // Iterate over DataTable rows
            ItemsTH.rows().every(function () {
                var row = this.node();
                var checkbox = $(row).find('input.row-checkbox');

                if (checkbox.is(':checked')) {
                    // Push the async function as a promise without executing it immediately
                    promises.push((async () => {

                        if (isStatSaveNew()) {
                            const rowdata = this.data();
                            itemTmpSave = itemTmpSave.filter(item => item.StockCode != rowdata.StockCode);
                            totalItemDeleted++;
                        } else {
                            await ajax('api/orders/po-items/' + row.id, 'POST', JSON.stringify({ _method: 'DELETE' }), (response) => { // Success callback
                                totalItemDeleted++;
                            }, (xhr, status, error) => { // Error callback
                                console.error('Error:', error);
                            });
                        }
                    })()); // Executes the function immediately
                }
            });

            // Wait for all promises to complete
            await Promise.all(promises);

            // Now, show success message after deletion is truly completed
            Swal.fire({
                title: "Deleted!",
                text: `Total ${totalItemDeleted} items have been deleted.`,
                icon: "success",
                confirmButtonColor: "#3085d6"
            });

            $(this).prop('disabled', true);

            if (isStatSaveNew()) {
                datatables.initPOItemsDatatable(itemTmpSave);
            } else {
                await datatables.loadItems(selectedMain.PONumber);
            }

            calculateCost();
        }
    }

}

function testIconClick(id) {
    alert('click with id' + id);
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

function isTokenExist() {
    if (!localStorage.getItem('api_token')) {
        window.location.href = "/login";
    }
}

function getDBCon() {
    return localStorage.getItem('dbcon');
}

function isDBConfig() {
    var retrievedUser = getDBCon();

    if (retrievedUser) {
        retrievedUser = JSON.parse(retrievedUser);
    } else {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-primary"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "No Database Config Detected",
            text: "Database operations require proper settings. Set up the configuration to continue.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Set DBConfig",
            cancelButtonText: "Load Default",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = globalApi + 'dbconfig';
                window.NavigationPreloadManager;

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                var dbaccount = {
                    "company": '',
                    "driver": "sqlsrv",
                    "host": '66.42.43.247',
                    "port": '8055',
                    "database": 'FASTERP',
                    "username": 'fastsfa',
                    "password": 'default',
                    "machineIdKey": "default"
                }

                localStorage.setItem('dbcon', JSON.stringify(dbaccount));
                location.reload(true);

            }
        });

        return;
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

const isStatSaveNew = () => {
    return $("#saveBtn").is(":visible");
}

function formatMoney(amount, locale = 'en-PH', currency = 'PHP') {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function parseMoney(formattedAmount) {
    return parseFloat(formattedAmount.replace(/[^0-9.-]+/g, ''));
}






