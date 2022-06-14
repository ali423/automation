if (document.getElementById("type").value == 'product') {

    document.querySelector("#sales_price").classList.remove('d-none');
    document.querySelector("#product_formul").classList.remove('d-none');
    document.querySelector("#purchase_price").classList.add('d-none');

} else if (document.getElementById("type").value == 'material') {

    document.querySelector("#purchase_price").classList.remove('d-none');
    document.querySelector("#sales_price").classList.add('d-none');
    document.querySelector("#product_formul").classList.add('d-none');

} else {

    document.querySelector("#sales_price").classList.add('d-none');
    document.querySelector("#purchase_price").classList.add('d-none');
    document.querySelector("#product_formul").classList.add('d-none');

}

document.getElementById("type").onchange = function () {
    var value = document.getElementById("type").value;

    if (value == 'product') {

        document.querySelector("#sales_price").classList.remove('d-none');
        document.querySelector("#product_formul").classList.remove('d-none');
        document.querySelector("#purchase_price").classList.add('d-none');

        document.querySelector("#purchase_price input").setAttribute('disabled', '');
        document.querySelector("#sales_price input").removeAttribute('disabled');
        document.querySelector("#product_formul input").removeAttribute('disabled');
        document.querySelector("#product_formul select").removeAttribute('disabled');

    } else if (value == 'material') {

        document.querySelector("#purchase_price").classList.remove('d-none');
        document.querySelector("#sales_price").classList.add('d-none');
        document.querySelector("#product_formul").classList.add('d-none');

        document.querySelector("#purchase_price input").removeAttribute('disabled');
        document.querySelector("#sales_price input").setAttribute('disabled', '');
        document.querySelector("#product_formul input").setAttribute('disabled', '');
        document.querySelector("#product_formul select").setAttribute('disabled', '');

        document.querySelector("#newRow").innerHTML = "";

    } else {

        document.querySelector("#purchase_price").classList.add('d-none');
        document.querySelector("#sales_price").classList.add('d-none');
        document.querySelector("#product_formul").classList.add('d-none');

        document.querySelector("#purchase_price input").setAttribute('disabled', '');
        document.querySelector("#sales_price input").setAttribute('disabled', '');
        document.querySelector("#product_formul input").setAttribute('disabled', '');
        document.querySelector("#product_formul select").setAttribute('disabled', '');

        document.querySelector("#newRow").innerHTML = "";
    }
};

function percentage(e){
    if (e.value < 0) e.value = 0;
    if (e.value > 100) e.value = 100;
}