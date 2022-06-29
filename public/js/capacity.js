let capacity = document.getElementById('capacity');
let fakecapacity = document.getElementById('fakecapacity');
let unit = document.getElementById('unit');

fakecapacity.onkeyup = function () {
    switch (unit.value) {
        case "kg":
            capacity.value = fakecapacity.value;
            break;
        case "barrel":
            capacity.value = fakecapacity.value * 185;
            break;
        case "galon":
            capacity.value = fakecapacity.value * 17.8;
            break;
    }
}

unit.addEventListener('change', function(){
    capacity.value = '';
    fakecapacity.value = '';
});