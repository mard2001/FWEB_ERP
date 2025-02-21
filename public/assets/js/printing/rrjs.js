$(document).ready(function () {
    console.log("Here in RRJS");
    
    function getData(){
        let data = {
            title: "RR Printing",
            date: new Date().toISOString().split("T")[0], // Current date in Y-m-d format
            distName: "FUI Shell",
            supCode: "VE-P0002",
            supName: "Shell Pilipinas Corporation",
            supAdd: "Fort Bonifacio 1635 Taguig City NCR, Fourth District Philippines",
            supTIN: "000-164-757-00000",
            rrNo: "1600000711",
            rrDate: "Nov. 18, 2024",
            rrRef: "DN-512545212",
            rrStat1: "Closed",
            rrStat2: "Original",
            prepared: "Marvin Navarro",
            checked: "Jhunrey Lucero",
            approved: "Jhun Woogie Arrabis",
            items: []
        };
    
        // Generate 40 items dynamically
        for (let i = 1; i <= 40; i++) {
            data.items.push({
                itemCode: Math.floor(Math.random() * (999999999 - 100000000 + 1)) + 100000000,
                itemDesc: `Sample Item Description ${i}`,
                itemQty: Math.floor(Math.random() * (500 - 10 + 1)) + 10,
                itemOum: ["CS", "PC", "IB"][Math.floor(Math.random() * 3)],
                itemWhsCode: `V${Math.floor(Math.random() * (999 - 100 + 1)) + 100}M${Math.floor(Math.random() * 10)}`,
                itemUnitPrice: (Math.random() * (5000 - 1000) + 1000).toFixed(2),
                netVat: (Math.random() * (500000 - 5000) + 5000).toFixed(2),
                vat: (Math.random() * (50000 - 500) + 500).toFixed(2),
                gross: (Math.random() * (600000 - 10000) + 10000).toFixed(2)
            });
        }
        console.log(data); // Check output in console    
    }

});