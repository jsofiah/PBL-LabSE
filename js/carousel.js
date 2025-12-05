const carousel = document.getElementById("carousel");
const cards = [...carousel.children];
const style = getComputedStyle(carousel);
const gap = parseInt(style.gap);
const cardWidth = cards[0].offsetWidth + gap;

carousel.innerHTML += carousel.innerHTML;

let position = 0;
let isPaused = false;
let animationId = null;

function smoothSlide() {
    if (!isPaused) {
        position += 0.8;

        if (position >= cards.length * cardWidth) {
            position = 0;
        }

        carousel.style.transform = `translateX(-${position}px)`;
    }

    animationId = requestAnimationFrame(smoothSlide);
}

const carouselWrapper = document.querySelector('.carousel-wrapper');

carouselWrapper.addEventListener('mouseenter', () => {
    isPaused = true;
    carousel.style.transition = 'transform 0.3s ease';
});

carouselWrapper.addEventListener('mouseleave', () => {
    isPaused = false;
    carousel.style.transition = 'none';
});

document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        isPaused = true;
    });
    
    card.addEventListener('mouseleave', () => {
        isPaused = false;
    });
});

smoothSlide();

document.addEventListener('keydown', (e) => {
    if (e.key === ' ' || e.key === 'Spacebar') {
        e.preventDefault();
        isPaused = !isPaused;
    }
});