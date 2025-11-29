const carousel = document.getElementById("carousel");
const cards = [...carousel.children];
const style = getComputedStyle(carousel);
const gap = parseInt(style.gap);
const cardWidth = cards[0].offsetWidth + gap;

// Duplicate all items to make a "long loop"
carousel.innerHTML += carousel.innerHTML;

let position = 0;

function smoothSlide() {
    position += 1; // speed (px per frame) — bisa dikecilkan utk lebih halus

    // Reset jika lewat panjang asli
    if (position >= cards.length * cardWidth) {
        position = 0;
    }

    carousel.style.transform = `translateX(-${position}px)`;

    requestAnimationFrame(smoothSlide);
}

smoothSlide();
