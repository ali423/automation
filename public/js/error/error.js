const errors = document.querySelectorAll('#error');
var length = errors.length;
var i;

for (i = 0; i < length; i++) {
  errors[i].classList.add('close');
}