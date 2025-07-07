// Select invoice and factor elements
const customerInvoice = document.querySelector("#invoice-customer");
const documentationInvoice = document.querySelector("#invoice-documentation");
const warehouseInvoice = document.querySelector("#invoice-warehouse");
const finvoice = document.querySelector("#finvoice");

// Select print buttons
const customerBtn = document.querySelector(".customerbtn");
const documentationBtn = document.querySelector(".documentationbtn");
const warehouseBtn = document.querySelector(".warehousebtn");
const factorBtn = document.querySelector(".factorbtn");

// Function to manage display and print
function showInvoice(activeInvoice) {
    // List of all elements
    const invoices = [
        customerInvoice,
        documentationInvoice,
        warehouseInvoice,
        finvoice,
    ];

    // Disable all elements
    invoices.forEach((invoice) => {
        if (invoice) {
            invoice.classList.remove("showprint", "print-active");
            invoice.classList.add("d-none");
            if (invoice === finvoice) {
                invoice.classList.add("hideprint");
            }
        }
    });

    // Enable the selected element
    if (activeInvoice) {
        activeInvoice.classList.remove("d-none", "hideprint");
        activeInvoice.classList.add("showprint", "print-active");
    }
}

// Reset display after print
window.onafterprint = function () {
    const invoices = [
        customerInvoice,
        documentationInvoice,
        warehouseInvoice,
        finvoice,
    ];
    invoices.forEach((invoice) => {
        if (invoice) {
            invoice.classList.remove("showprint", "print-active");
            invoice.classList.add("d-none");
            if (invoice === finvoice) {
                invoice.classList.add("hideprint");
            }
        }
    });
};

// Event listener for customer invoice button
customerBtn.addEventListener("click", function () {
    showInvoice(customerInvoice);
    window.print();
});

// Event listener for documentation invoice button
documentationBtn.addEventListener("click", function () {
    showInvoice(documentationInvoice);
    window.print();
});

// Event listener for warehouse invoice button
warehouseBtn.addEventListener("click", function () {
    showInvoice(warehouseInvoice);
    window.print();
});

// Event listener for factor button
factorBtn.addEventListener("click", function () {
    showInvoice(finvoice);
    window.print();
});

// Set row numbers for invoice tables
document
    .querySelectorAll('.invoice table th[scope="row"]')
    .forEach((th, index) => {
        th.innerHTML = index + 1;
    });

// Set row numbers for factor table
document
    .querySelectorAll('#finvoice .factortable th[scope="row"]')
    .forEach((th, index) => {
        th.innerHTML = index + 1;
    });
