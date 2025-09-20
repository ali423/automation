// Select invoice and factor elements
const customerInvoice = document.querySelector("#invoice-customer");
const documentationInvoice = document.querySelector("#invoice-documentation");
const warehouseInvoice = document.querySelector("#invoice-warehouse");
const finvoice = document.querySelector("#finvoice");
const factorBtn2 = document.querySelector(".factorbtn2");
const finvoice2 = document.querySelector("#finvoice2");
const finvoiceTejarat = document.querySelector("#finvoice-tejarat");
const tejaratBtn = document.querySelector(".tejaratbtn");

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
        finvoice2,
        finvoiceTejarat,
    ];

    // Disable all elements
    invoices.forEach((invoice) => {
        if (invoice) {
            invoice.classList.remove("showprint", "print-active");
            invoice.classList.add("d-none");
            if (
                invoice === finvoice ||
                invoice === finvoice2 ||
                invoice === finvoiceTejarat
            ) {
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
        finvoice2,
        finvoiceTejarat,
    ];
    invoices.forEach((invoice) => {
        if (invoice) {
            invoice.classList.remove("showprint", "print-active");
            invoice.classList.add("d-none");
            if (
                invoice === finvoice ||
                invoice === finvoice2 ||
                invoice === finvoiceTejarat
            ) {
                invoice.classList.add("hideprint");
            }
        }
    });
};

// Event listener for customer invoice button
customerBtn.addEventListener("click", function () {
    showInvoice(customerInvoice);
    // Set document title for better filename when saving/printing
    const originalTitle = document.title;
    const customerName = document.querySelector('input[name="customer"]')?.value || 'نامشخص';
    const sellerName = document.querySelector('input[placeholder*="فروشنده"]')?.value || '';
    const requestNumber = document.querySelector('input[name="request_number"]')?.value || '';
    const requestDate = document.querySelector('input[name="created_at"]')?.value || '';
    const entityName = customerName !== 'نامشخص' ? customerName : sellerName || 'نامشخص';
    document.title = `حواله مشتری - ${entityName} - ${requestDate} - ${requestNumber}`;
    window.print();
    // Restore original title after print
    setTimeout(() => { document.title = originalTitle; }, 1000);
});

// Event listener for documentation invoice button
documentationBtn.addEventListener("click", function () {
    showInvoice(documentationInvoice);
    // Set document title for better filename when saving/printing
    const originalTitle = document.title;
    const customerName = document.querySelector('input[name="customer"]')?.value || 'نامشخص';
    const sellerName = document.querySelector('input[placeholder*="فروشنده"]')?.value || '';
    const requestNumber = document.querySelector('input[name="request_number"]')?.value || '';
    const requestDate = document.querySelector('input[name="created_at"]')?.value || '';
    const entityName = customerName !== 'نامشخص' ? customerName : sellerName || 'نامشخص';
    document.title = `حواله حسابداری - ${entityName} - ${requestDate} - ${requestNumber}`;
    window.print();
    // Restore original title after print
    setTimeout(() => { document.title = originalTitle; }, 1000);
});

// Event listener for warehouse invoice button
warehouseBtn.addEventListener("click", function () {
    showInvoice(warehouseInvoice);
    // Set document title for better filename when saving/printing
    const originalTitle = document.title;
    const customerName = document.querySelector('input[name="customer"]')?.value || 'نامشخص';
    const sellerName = document.querySelector('input[placeholder*="فروشنده"]')?.value || '';
    const requestNumber = document.querySelector('input[name="request_number"]')?.value || '';
    const requestDate = document.querySelector('input[name="created_at"]')?.value || '';
    const entityName = customerName !== 'نامشخص' ? customerName : sellerName || 'نامشخص';
    document.title = `حواله بارگیری - ${entityName} - ${requestDate} - ${requestNumber}`;
    window.print();
    // Restore original title after print
    setTimeout(() => { document.title = originalTitle; }, 1000);
});

// Event listener for factor button
factorBtn.addEventListener("click", function () {
    showInvoice(finvoice);
    // Set document title for better filename when saving/printing
    const originalTitle = document.title;
    const customerName = document.querySelector('input[name="customer"]')?.value || 'نامشخص';
    const sellerName = document.querySelector('input[placeholder*="فروشنده"]')?.value || '';
    const requestNumber = document.querySelector('input[name="request_number"]')?.value || '';
    const requestDate = document.querySelector('input[name="created_at"]')?.value || '';
    const entityName = customerName !== 'نامشخص' ? customerName : sellerName || 'نامشخص';
    document.title = `فاکتور - ${entityName} - ${requestDate} - ${requestNumber}`;
    window.print();
    // Restore original title after print
    setTimeout(() => { document.title = originalTitle; }, 1000);
});

// Event listener for factor button 2
factorBtn2.addEventListener("click", function () {
    showInvoice(finvoice2);
    // Set document title for better filename when saving/printing
    const originalTitle = document.title;
    const customerName = document.querySelector('input[name="customer"]')?.value || 'نامشخص';
    const sellerName = document.querySelector('input[placeholder*="فروشنده"]')?.value || '';
    const requestNumber = document.querySelector('input[name="request_number"]')?.value || '';
    const requestDate = document.querySelector('input[name="created_at"]')?.value || '';
    const entityName = customerName !== 'نامشخص' ? customerName : sellerName || 'نامشخص';
    document.title = `فاکتور - ${entityName} - ${requestDate} - ${requestNumber}`;
    window.print();
    // Restore original title after print
    setTimeout(() => { document.title = originalTitle; }, 1000);
});

// Event listener for tejarat button
if (tejaratBtn && finvoiceTejarat) {
    tejaratBtn.addEventListener("click", function () {
        showInvoice(finvoiceTejarat);
        // Set document title for better filename when saving/printing
        const originalTitle = document.title;
        const customerName = document.querySelector('input[name="customer"]')?.value || 'نامشخص';
        const sellerName = document.querySelector('input[placeholder*="فروشنده"]')?.value || '';
        const requestNumber = document.querySelector('input[name="request_number"]')?.value || '';
        const requestDate = document.querySelector('input[name="created_at"]')?.value || '';
        const entityName = customerName !== 'نامشخص' ? customerName : sellerName || 'نامشخص';
        document.title = `نسخه سامانه تجارت - ${entityName} - ${requestDate} - ${requestNumber}`;
        window.print();
        // Restore original title after print
        setTimeout(() => { document.title = originalTitle; }, 1000);
    });
}

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
