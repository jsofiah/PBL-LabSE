const carousel = document.getElementById("carousel");
const style = getComputedStyle(carousel);
const gap = parseInt(style.gap);
const cardWidth = carousel.children[0].offsetWidth + gap;

let offset = 0;

function autoSlide() {
    const totalCards = carousel.children.length;
    const maxOffset = (totalCards * cardWidth) - window.innerWidth + 120;

    if (offset >= maxOffset) {
        offset = 0;
    } else {
        offset += cardWidth;
    }

    carousel.style.transform = `translateX(-${offset}px)`;
}

setInterval(autoSlide, 3000);