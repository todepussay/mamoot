//compte à rebours de 10 secondes
function countdown() {

    var count = 10;
    //si il n'y a seulement 2 joueurs on met le compte à rebours à 7
    if (document.getElementsByClassName("joueur").length == 2) {
        count = 7;
    }
    //si il n'y a seulement 1 joueur on met le compte à rebours à 4
    if (document.getElementsByClassName("joueur").length == 1) {
        count = 4;
    }
    if (document.getElementsByClassName("joueur").length == 0) {
        count = 0;
    }

    var counter = setInterval(timer, 1000);

    function timer() {
        if (count == 8) {
            document.getElementById("3").style.display = "block";
            third_place.play();
        } else if (count == 6) {
            document.getElementById("2").style.display = "block";
            second_place.play();
        } else if (count == 3) {
            document.getElementById("1").style.display = "block";
            first_place.play();
            //démarrage confetti
            document.getElementById("canvas").style.display = "block";
            confettiParticle();
            confetti.play();
        } else if (count == 0) {
            clearInterval(counter);

            //affichage des joueurs restants
            var elements = document.getElementsByClassName("joueur");
            for (var i = 0; i < elements.length; i++) {
                elements[i].style.display = "block";
            }
        }
        count--;
        if (count < 0) {
            clearInterval(counter);
        }
    }
}

// Déclaration des variables pour les sons
var third_place = new Audio('../son/third_place.mp3');
var second_place = new Audio('../son/second_place.mp3');
var first_place = new Audio('../son/first_place.mp3');
var confetti = new Audio('../son/confetti.mp3');

//var fireworks = new Audio('../son/fireworks.mp3');

// Démarrer le compte à rebours
countdown();




/*-------------------------------------------------*/
//CONFETTI

function confettiParticle() {
    let W = window.innerWidth;
    let H = window.innerHeight;
    const canvas = document.getElementById("canvas");
    const context = canvas.getContext("2d");
    const maxConfettis = 50;
    const particles = [];

    const possibleColors = [
        "DodgerBlue",
        "OliveDrab",
        "Gold",
        "Pink",
        "SlateBlue",
        "LightBlue",
        "Gold",
        "Violet",
        "PaleGreen",
        "SteelBlue",
        "SandyBrown",
        "Chocolate",
        "Crimson"
    ];

    function randomFromTo(from, to) {
        return Math.floor(Math.random() * (to - from + 1) + from);
    }

    function confettiParticle() {
        this.x = Math.random() * W; // x
        this.y = Math.random() * H - H; // y
        this.r = randomFromTo(11, 33); // radius
        this.d = Math.random() * maxConfettis + 11;
        this.color =
            possibleColors[Math.floor(Math.random() * possibleColors.length)];
        this.tilt = Math.floor(Math.random() * 33) - 11;
        this.tiltAngleIncremental = Math.random() * 0.07 + 0.05;
        this.tiltAngle = 0;

        this.draw = function () {
            context.beginPath();
            context.lineWidth = this.r / 2;
            context.strokeStyle = this.color;
            context.moveTo(this.x + this.tilt + this.r / 3, this.y);
            context.lineTo(this.x + this.tilt, this.y + this.tilt + this.r / 5);
            return context.stroke();
        };
    }

    function Draw() {
        const results = [];

        // Magical recursive functional love
        requestAnimationFrame(Draw);

        context.clearRect(0, 0, W, window.innerHeight);

        for (var i = 0; i < maxConfettis; i++) {
            results.push(particles[i].draw());
        }

        let particle = {};
        let remainingFlakes = 0;
        for (var i = 0; i < maxConfettis; i++) {
            particle = particles[i];

            particle.tiltAngle += particle.tiltAngleIncremental;
            particle.y += (Math.cos(particle.d) + 3 + particle.r / 2) / 2;
            particle.tilt = Math.sin(particle.tiltAngle - i / 3) * 15;

            if (particle.y <= H) remainingFlakes++;

            // If a confetti has fluttered out of view,
            // bring it back to above the viewport and let if re-fall.
            if (particle.x > W + 30 || particle.x < -30 || particle.y > H) {
                particle.x = Math.random() * W;
                particle.y = -30;
                particle.tilt = Math.floor(Math.random() * 10) - 20;
            }
        }

        return results;
    }

    window.addEventListener(
        "resize",
        function () {
            W = window.innerWidth;
            H = window.innerHeight;
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        },
        false
    );

// Push new confetti objects to `particles[]`
    for (var i = 0; i < maxConfettis; i++) {
        particles.push(new confettiParticle());
    }

// Initialize
    canvas.width = W;
    canvas.height = H;
    Draw();

}