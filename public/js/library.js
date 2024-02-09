const easterEggCombination = ['b', 'e', 'a', 'u', 'j', 'o', 'i', 'n'];
let userInput = [];
let explosionInterval = null;
let easterEggActive = false;

function playSound() {
    const audio = new Audio(`../img/beaujoin.mp3`);
    audio.play();
}

function createExplosion() {
    if (!easterEggActive) return;

    const explosionImage = document.createElement('img');
    explosionImage.src = '../img/beaujoin.png';
    explosionImage.alt = 'Mamoot';
    explosionImage.style.width = '20%';
    explosionImage.style.height = 'auto';
    explosionImage.style.position = 'absolute';
    explosionImage.classList.add('explosion');

    const randomX = Math.random() * window.innerWidth;
    const randomY = Math.random() * window.innerHeight;
    explosionImage.style.left = `${randomX}px`;
    explosionImage.style.top = `${randomY}px`;

    document.body.appendChild(explosionImage);

    explosionImage.addEventListener('animationend', () => {
        explosionImage.remove();
    });
}

function toggleEasterEgg() {
    easterEggActive = !easterEggActive; // Bascule l'état actif/inactif des explosions

    if (easterEggActive) {
        playSound();
        explosionInterval = setInterval(createExplosion, 25);
    } else {
        clearInterval(explosionInterval);
    }
}

function handleEasterEgg() {
    document.addEventListener('keydown', function (event) {
        userInput.push(event.key.toLowerCase());
        userInput = userInput.slice(-easterEggCombination.length);

        if (userInput.join('') === easterEggCombination.join('')) {
            toggleEasterEgg();
            userInput = [];
        }
    });
}


document.addEventListener('DOMContentLoaded', function () {
    handleEasterEgg();
});
