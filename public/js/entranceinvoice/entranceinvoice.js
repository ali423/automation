// printable invoice titles
let customer = document.querySelector('.customer');
let doc = document.querySelector('.documentation');
let ware = document.querySelector('.warehouse');

// print buttons
let customerbtn = document.querySelector('.customerbtn');
let docbtn = document.querySelector('.documentationbtn');
let warebtn = document.querySelector('.warehousebtn');
let factorbtn = document.querySelector('.factorbtn');

//export import invoice
let invoice = document.querySelector('#invoice')

//factor invoice
let finvoice = document.querySelector('#finvoice')

customerbtn.addEventListener('click', function(){
    if(!(doc.classList.contains('d-none'))){
        doc.classList.add('d-none');
    }else if(!(ware.classList.contains('d-none'))){
        ware.classList.add('d-none');
    }
    
    if(!(invoice.classList.contains('showprint'))&&(finvoice.classList.contains('showprint'))){
        finvoice.classList.remove('showprint');
        if(!(finvoice.classList.contains('hideprint'))){
            finvoice.classList.add('hideprint');
        }
        if(invoice.classList.contains('hideprint')){
            invoice.classList.remove('hideprint');
        }
        invoice.classList.add('showprint');
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
    
    if(!(invoice.classList.contains('showprint'))&&(finvoice.classList.contains('showprint'))){
        finvoice.classList.remove('showprint');
        if(!(finvoice.classList.contains('hideprint'))){
            finvoice.classList.add('hideprint');
        }
        if(invoice.classList.contains('hideprint')){
            invoice.classList.remove('hideprint');
        }
        invoice.classList.add('showprint');
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
    
    if(!(invoice.classList.contains('showprint'))&&(finvoice.classList.contains('showprint'))){
        finvoice.classList.remove('showprint');
        if(!(finvoice.classList.contains('hideprint'))){
            finvoice.classList.add('hideprint');
        }
        if(invoice.classList.contains('hideprint')){
            invoice.classList.remove('hideprint');
        }
        invoice.classList.add('showprint');
    }
    ware.classList.remove('d-none')
    window.print();
})
factorbtn.addEventListener('click', function(){
    
    if(!(finvoice.classList.contains('showprint'))&&(invoice.classList.contains('showprint'))){
        invoice.classList.remove('showprint');
        if(!(invoice.classList.contains('hideprint'))){
            invoice.classList.add('hideprint');
        }
        if(finvoice.classList.contains('hideprint')){
            finvoice.classList.remove('hideprint');
        }
        finvoice.classList.add('showprint');
    }
    window.print();
})

var table = document.querySelectorAll('th[scope="row"]');
for(var i=0;i<table.length;i++){
    table[i].innerHTML = i+1;
} 

var rownubmer =  document.querySelectorAll('#rownumbers');
rownubmer.forEach((element,index) => {
    element.innerHTML = index+1;
});