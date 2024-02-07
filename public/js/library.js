const easterEggCombination = ['b', 'e', 'a', 'u', 'j', 'o', 'i', 'n'];

let userInput = [];

function playSound() {
    const audio = new Audio('../img/beaujoin.mp3');
    audio.addEventListener('ended', function() {
        this.currentTime = 0;
        this.play();
    }, false);
    audio.play();
}

function createExplosion() {
    const explosionImage = document.createElement('img');
    explosionImage.src = '../img/beaujoin.png';
    explosionImage.alt = 'Mamoot';
    explosionImage.style.width = '20%';
    explosionImage.style.height = 'auto';
    explosionImage.classList.add('explosion'); // Ajouter la classe pour l'animation

    const randomX = Math.random() * window.innerWidth;
    const randomY = Math.random() * window.innerHeight;
    explosionImage.style.left = `${randomX}px`;
    explosionImage.style.top = `${randomY}px`;

    document.body.appendChild(explosionImage);

    explosionImage.addEventListener('animationend', () => {
        explosionImage.remove();
    });
}

function handleEasterEgg() {
    document.addEventListener('keydown', function (event) {
        userInput.push(event.key.toLowerCase());

        if (userInput.join('') === easterEggCombination.join('')) {
            playSound();
            setInterval(createExplosion, 25);

            setTimeout(() => {
                userInput = [];
            }, 2500);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    handleEasterEgg();
});
