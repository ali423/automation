// printable invoice titles
let customer = document.querySelector('.customer');
let doc = document.querySelector('.documentation');
let ware = document.querySelector('.warehouse');

// print buttons
let customerbtn = document.querySelector('.customerbtn');
let docbtn = document.querySelector('.documentationbtn');
let warebtn = document.querySelector('.warehousebtn');

customerbtn.addEventListener('click', function(){
    if(!(doc.classList.contains('d-none'))){
        doc.classList.add('d-none');
    }else if(!(ware.classList.contains('d-none'))){
        ware.classList.add('d-none');
    }

    customer.classList.remove('d-none')
    window.print();
})
docbtn.addEventListener('click', function(){
    if(!(customer.classList.contains('d-none'))){
        customer.classList.add('d-none');
    }else if(!(ware.classList.contains('d-none'))){
        ware.classList.add('d-none');
    }
    
    doc.classList.remove('d-none')
    window.print();
})
warebtn.addEventListener('click', function(){
    if(!(doc.classList.contains('d-none'))){
        doc.classList.add('d-none');
    }else if(!(customer.classList.contains('d-none'))){
        customer.classList.add('d-none');
    }
    
    ware.classList.remove('d-none')
    window.print();
})

var table = document.querySelectorAll('th[scope="row"]');
for(var i=0;i<table.length;i++){
    table[i].innerHTML = i+1;
} 