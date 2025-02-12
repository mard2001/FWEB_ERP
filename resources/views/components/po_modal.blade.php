<!-- Full Page Modal -->
<!-- <div class="modal fade" id="editXmlDataModal" tabindex="-1" aria-labelledby="fullPageModalLabel" aria-hidden="true"> -->
<div class="modal fade" id="editXmlDataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-0">
                <p class="text-nowrap text-primary text-center mx-auto my-0" style="font-size: 2rem; font-weight: bold;">PURCHASE ORDER</p>
            </div>
            <div class="modal-body pt-0">
                <form id="modalFields">
                    {{ $form_fields }}
                </form>
            </div>
            <div class="modal-footer py-1 d-flex justify-content-between" id="delprint">
                <div>
                    <button type="button" class="btn btn-sm btn-danger" id="deleteBtn">Delete</button>
                    <button type="button" class="btn btn-sm btn-primary" id="rePrintPage">Print purchase order</button>

                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-primary text-white" id="confirmPO">Confrim PO</button>
                    <button type="button" class="btn btn-sm btn-primary text-white" id="saveBtn">Save details</button>
                    <button type="button" class="btn btn-sm btn-info text-white" id="editBtn">Edit details</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>