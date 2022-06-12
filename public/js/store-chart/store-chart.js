const currentElement = document.querySelectorAll('.current-size');
const currentSize = document.querySelector('.current-size').getAttribute('data-current');


currentElement.forEach(element=>{
    element.style.height=(element.getAttribute('data-current')+"%");
    element.innerHTML=(element.getAttribute('data-current')+"%");
})