var amount = '';
if(document.getElementById("type").value=='product'){
    document.getElementById("amount").removeAttribute('disabled');
}else{
    document.getElementById("amount").setAttribute('disabled','');
}

document.getElementById("type").onchange = function () {
    var value = document.getElementById("type").value;

    if(value=='product'){
        document.getElementById("amount").removeAttribute('disabled');
        document.getElementById("amount").value = amount;
    }else{
        amount = document.getElementById("amount").value;
        document.getElementById("amount").value = '';
        document.getElementById("amount").setAttribute('disabled','');
    }
};