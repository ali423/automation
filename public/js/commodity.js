if (document.getElementById("type").value == 'product') {

    document.querySelector("#sales_price").style.display = 'block';
    document.querySelector("#discount_percentage_group").style.display = 'block';
    document.querySelector("#product_formul").style.display = 'block';
    document.querySelector("#purchase_price").style.display = 'none';
    document.querySelector("#weight_per_unit_group").style.display = 'block';
    document.querySelector("#litrage_group").style.display = 'block';
    document.querySelector("#pieces_per_box_group").style.display = 'block';
    document.querySelector("#product_identifier_group").style.display = 'block';

} else if (document.getElementById("type").value == 'material') {

    document.querySelector("#purchase_price").style.display = 'block';
    document.querySelector("#sales_price").style.display = 'none';
    document.querySelector("#product_formul").style.display = 'none';
    document.querySelector("#weight_per_unit_group").style.display = 'none';
    document.querySelector("#litrage_group").style.display = 'none';
    document.querySelector("#discount_percentage_group").style.display = 'none';
    document.querySelector("#pieces_per_box_group").style.display = 'none';
    document.querySelector("#product_identifier_group").style.display = 'none';

} else {

    document.querySelector("#sales_price").style.display = 'none';
    document.querySelector("#purchase_price").style.display = 'none';
    document.querySelector("#product_formul").style.display = 'none';
    document.querySelector("#weight_per_unit_group").style.display = 'none';
    document.querySelector("#litrage_group").style.display = 'none';
    document.querySelector("#discount_percentage_group").style.display = 'none';
    document.querySelector("#pieces_per_box_group").style.display = 'none';
    document.querySelector("#product_identifier_group").style.display = 'none';

}

document.getElementById("type").onchange = function () {
    var value = document.getElementById("type").value;

    if (value == 'product') {

        document.querySelector("#sales_price").style.display = 'block';
        document.querySelector("#discount_percentage_group").style.display = 'block';
        document.querySelector("#product_formul").style.display = 'block';
        document.querySelector("#purchase_price").style.display = 'none';
        document.querySelector("#weight_per_unit_group").style.display = 'block';
        document.querySelector("#litrage_group").style.display = 'block';
        document.querySelector("#pieces_per_box_group").style.display = 'block';
        document.querySelector("#product_identifier_group").style.display = 'block';

        document.querySelector("#purchase_price input").setAttribute('disabled', '');
        document.querySelector("#sales_price input").removeAttribute('disabled');
        document.querySelector("#product_formul input").removeAttribute('disabled');
        document.querySelector("#product_formul select").removeAttribute('disabled');
        document.querySelector("#weight_per_unit").removeAttribute('disabled');

    } else if (value == 'material') {

        document.querySelector("#purchase_price").style.display = 'block';
        document.querySelector("#sales_price").style.display = 'none';
        document.querySelector("#product_formul").style.display = 'none';
        document.querySelector("#weight_per_unit_group").style.display = 'none';
        document.querySelector("#litrage_group").style.display = 'none';
        document.querySelector("#discount_percentage_group").style.display = 'none';
        document.querySelector("#pieces_per_box_group").style.display = 'none';
        document.querySelector("#product_identifier_group").style.display = 'none';

        document.querySelector("#purchase_price input").removeAttribute('disabled');
        document.querySelector("#sales_price input").setAttribute('disabled', '');
        document.querySelector("#product_formul input").setAttribute('disabled', '');
        document.querySelector("#product_formul select").setAttribute('disabled', '');
        document.querySelector("#weight_per_unit").setAttribute('disabled', '');

        document.querySelector("#newRow").innerHTML = "";

    } else {

        document.querySelector("#sales_price").style.display = 'none';
        document.querySelector("#purchase_price").style.display = 'none';
        document.querySelector("#product_formul").style.display = 'none';
        document.querySelector("#weight_per_unit_group").style.display = 'none';
        document.querySelector("#litrage_group").style.display = 'none';
        document.querySelector("#discount_percentage_group").style.display = 'none';
        document.querySelector("#pieces_per_box_group").style.display = 'none';
        document.querySelector("#product_identifier_group").style.display = 'none';

        document.querySelector("#purchase_price input").setAttribute('disabled', '');
        document.querySelector("#sales_price input").setAttribute('disabled', '');
        document.querySelector("#product_formul input").setAttribute('disabled', '');
        document.querySelector("#product_formul select").setAttribute('disabled', '');
        document.querySelector("#weight_per_unit").setAttribute('disabled', '');

        document.querySelector("#newRow").innerHTML = "";
    }
};

// Unit-based validation - ensure positive amounts
function validateAmount(e){
    if (e.value < 0) e.value = 0;
}