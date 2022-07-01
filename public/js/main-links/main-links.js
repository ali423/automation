
let mainLinks = document.getElementById('main-links');
let link = document.querySelectorAll('#main-links .link');
let options = document.querySelectorAll('#users-links .card-body');

for (let i = 0; i < link.length; i++) {
    link[i].addEventListener('click', function () {
        for (let i = 0; i < options.length; i++) {
            if(!(options[i].classList.contains('d-none'))){options[i].classList.add('d-none')}
        }
        let mainLink = document.querySelectorAll('.main-link');
        for (let i = 0; i < mainLink.length; i++) {
            mainLink[i].classList.remove('main-link');
        }
        document.getElementById(this.getAttribute('data-link')).classList.remove('d-none');
        this.closest('.height-card').classList.add('main-link')
    })
}