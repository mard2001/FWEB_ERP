<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purhcase Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<style>
    /* Header (Company Name & Logo) */
    .po-header {
        font-size: 1.8rem;
        /* Adjust between 14–18pt */
        font-weight: bold;
        text-transform: uppercase;
        font-family: Arial, Helvetica, sans-serif;
    }


    /* Purchase Order Title (e.g., "PURCHASE ORDER") */
    .po-title {
        font-size: 18pt;
        /* Adjust between 16–20pt */
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
    }

    /* Section Headings (e.g., "Vendor Details", "Ship To", "PO Number") */
    .po-section-heading {
        font-size: 12px;
        /* Adjust between 10–12pt */
        font-weight: bold;
        
    }
</style>

<header class="d-flex justify-content-between pt-2 px-2">
    <div>
        <div style="font-size: 18px; font-weight: 600">[Company Name]</div>
        <p class="m-0">[Street Address]</p>
        <p class="m-0">[City, ST ZIP]</p>
        <p class="m-0">Phone: (000) 000-0000</p>
    </div>


    <p class="text-nowrap text-primary text-center po-header">PURCHASE ORDER</p>
</header>

<body>

    <div class="d-flex justify-content-between flex-wrap mt-2 fs12">
        <div class="my-sm-2 bg-info bg-opacity-25" style="width: 38%;">
            <div class="bg-primary p-1 d-flex align-items-center text-white" style="font-size: 14px;">
                VENDOR
            </div>


        </div>

        <div class="my-sm-2 ResMWidth">
            <div class="bg-primary p-1 d-flex align-items-center text-white" style="font-size: 14px;">
                SHIP TO
            </div>

            <div id="shippedToName" name="shippedToName" required class="form-control bg-white p-0 border-0">
                Shipper Name
            </div>

            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white">Contact:</span>
                <input type="text" disabled id="shippedToContactName" required name="SupplierContactName" class="form-control bg-white" placeholder="Contact Name">
            </div>
            <label id="shippedToContactName-error" class="error d-block" for="shippedToContactName"></label>

            <textarea class="form-control px-2" id="shippedToAddress" required placeholder="Shipped To Address" rows="2" style="resize: none;"></textarea>
            <label id="shippedToAddress-error" class="error d-block" for="shippedToAddress"></label>

            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white">Phone:</span>
                <input type="text" disabled id="shippedToPhone" required name="shippedToPhone" class="form-control bg-white" placeholder="Phone">
            </div>
            <label id="shippedToPhone-error" class="error d-block" for="shippedToPhone"></label>

        </div>

    </div>

</body>

</html>