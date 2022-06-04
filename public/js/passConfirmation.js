var pwtag = document.getElementById("userpass");
var pwconftag = document.getElementById("userpass_confirmation");

pwtag.addEventListener("keyup", function () {
    var pattern = this.value;
    pwconftag.setAttribute('pattern',pattern);
});