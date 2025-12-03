const toTopBtn = document.getElementById('toTop');
const scrollIndicator = document.getElementById('scrollIndicator');
let isScrolling = false;

window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercentage = (scrollTop / scrollHeight) * 100;

    scrollIndicator.style.width = scrollPercentage + '%';

    if (scrollTop > 300) {
        toTopBtn.classList.add('show');
    } else {
        toTopBtn.classList.remove('show');
    }
});

toTopBtn.addEventListener('click', () => {
    toTopBtn.classList.add('scrolling');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

    setTimeout(() => {
        toTopBtn.classList.remove('scrolling');
    }, 1000);
});