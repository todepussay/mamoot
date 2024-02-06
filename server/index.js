const express = require('express');
const http = require("https");
const socketIo = require('socket.io');
const bodyParser = require('body-parser');
const cors = require('cors');
const fs = require("fs");

const app = express();
const port = 5000;

app.use(cors({
    origin: "*",
    methods: ["GET", "POST"],
}));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

const credentials = {
    key: fs.readFileSync('privkey.pem'),
    cert: fs.readFileSync('fullchain.pem')
};

const server = http.createServer(credentials, app);
const io = socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
    }
});

let rooms = {};

io.on('connection', (socket) => {

    socket.on('disconnecting', (data) => {
        delete rooms[data.userId];
    });

    socket.on("createdRoom", ({ id_user, quiz }) => {
        let code = createCode();

        let quiz_player = [];

        for(let i = 0; i < quiz.questions.length; i++){
            let question = quiz.questions[i];

            if(question.vraifaux){
                quiz_player.push({
                    temps: question.temps,
                    type: "vraifaux"
                });
            } else if(question.curseur){
                quiz_player.push({
                    temps: question.temps,
                    type: "curseur",
                    minimumCurseur: question.minimum_curseur,
                    maximumCurseur: question.maximum_curseur,
                    interval: question.interval_curseur
                });
            } else {
                quiz_player.push({
                    temps: question.temps,
                    type: "quiz",
                    number: question.reponses.length
                })
            }
        }

        rooms[id_user] = {
            id: socket.id,
            quiz: quiz,
            quiz_player: quiz_player,
            code: code,
            status: "pending",
            players: [],
            is_saved: false
        };

        console.log(`Création d'une nouvelle room par ${id_user}`);

        io.to(socket.id).emit("room", { quiz_reponse: quiz, code: code });
    })

    socket.on("connectedUser", ({ username, code }) => {

        let quiz;
        for(let room in rooms){
            if(rooms[room].code === code){
                quiz = rooms[room].quiz_player;
                rooms[room].players.push({
                    id: socket.id,
                    username: username,
                    score: 0,
                    bonne_reponse: []
                });

                io.to(rooms[room].id).emit("newConnected", {
                    id: socket.id,
                    username: username,
                    quiz: quiz
                });
            }
        }

    })

    socket.on("disconnectingUser", ({ code }) => {

        for(let room in rooms){
            if(rooms[room].code === code){
                for(let i = 0; i < rooms[room].players.length; i++){
                    if(rooms[room].players[i].id === socket.id){
                        rooms[room].players.splice(i, 1);

                        io.to(rooms[room].id).emit("newDisconnected", {
                            id: socket.id
                        });
                        break;
                    }
                }
            }
        }

    })

    socket.on("kickedPlayer", ({ id, code }) => {

        for(let room in rooms){
            if(rooms[room].code === code){
                for(let i = 0; i < rooms[room].players.length; i++){
                    if(rooms[room].players[i].id === id){
                        rooms[room].players.splice(i, 1);
                        io.to(id).emit("kicked");
                        break;
                    }
                }
            }
        }

    })

    socket.on("startGame", ({ id }) => {

        rooms[id].status = "progress";

        for(let i = 0; i < rooms[id].players.length; i++){
            io.to(rooms[id].players[i].id).emit("start", {
                id: id,
                quiz_player: rooms[id].quiz_player
            });
        }
    })

    socket.on("skipQuestion", ({ id }) => {
        for(let i = 0; i < rooms[id].players.length; i++){
            io.to(rooms[id].players[i].id).emit("skip");
        }
    })

    socket.on("reponse", ({ current_question, reponse, id, time }) => {

        if(reponse !== null) {
            let good = false;

            if(rooms[id].quiz.questions[current_question].vraifaux){
                if(rooms[id].quiz.questions[current_question].valeurvraifaux === reponse){
                    good = true;
                }
            } else if(rooms[id].quiz.questions[current_question].curseur){
                if(reponse >= rooms[id].quiz.questions[current_question].minimum_valide && reponse <= rooms[id].quiz.questions[current_question].maximum_valide){
                    good = true;
                }
            } else {
                if (rooms[id].quiz.questions[current_question].reponses[reponse].good) {
                    good = true;
                }
            }

            if(good){
                let score = Math.round(100 * (time / rooms[id].quiz.questions[current_question].temps));
                for(let i = 0; i < rooms[id].players.length; i++){
                    if(rooms[id].players[i].id === socket.id){
                        rooms[id].players[i].score += score;
                        rooms[id].players[i].bonne_reponse.push(current_question);
                    }
                }
            }

        }
    })

    socket.on("afficherResultat", ({ id, current_question }) => {

        let resultat = [];

        for(let i = 0; i < rooms[id].players.length; i++){
            resultat.push({
                id_user: rooms[id].players[i].id,
                username: rooms[id].players[i].username,
                score: rooms[id].players[i].score,
                good: rooms[id].players[i].bonne_reponse.slice(-1)[0] === current_question
            });
        }

        resultat.sort((a, b) => {
            // Trie par score décroissant
            if (b.score !== a.score) {
                return b.score - a.score;
            }

            // Si les scores sont égaux, trie par ordre alphabétique du nom d'utilisateur
            return a.username.localeCompare(b.username);
        });

        for(let i = 0; i < resultat.length; i++){
            io.to(resultat[i].id_user).emit("resultatJoueur", {
                position: i + 1,
                derriere: i === 0 ? null : resultat[i-1].username,
                score: resultat[i].score
            })
        }

    })

    socket.on("nextQuestion", ({ id }) => {

        for(let i = 0; i < rooms[id].players.length; i++){
            io.to(rooms[id].players[i].id).emit("next");
        }

    })

});

function createCode(){

    while (true){
        var code = '';
        var good = true;
        for (var i = 0; i < 6; i++) {
            var digit = Math.floor(1 + Math.random() * 9);
            code += digit;
        }

        for(let i = 0; i < rooms.length; i++){
            if (rooms[i].code === code){
                good = false;
            }
        }

        if(good){
            return code;
        }

    }
}

app.post('/party_exist', (req, res) => {
    let code = req.body.code;

    for(let room in rooms){
        if(code === rooms[room].code){
            if(rooms[room].status === "pending"){
                return res.send({ exist: true });
            } else {
                return res.send({ exist: false, message: "La partie est déjà en cours"});
            }
        }
    }

    return res.send({ exist: false, message: "La partie n'existe pas" });
})

app.post("/get_resultat", (req, res) => {
    const { id, current_question } = req.body;

    let bonne_reponse = 0;

    for(let i = 0; i < rooms[id].players.length; i++){
        if(rooms[id].players[i].bonne_reponse.slice(-1)[0] === current_question){
            bonne_reponse += 1;
        }
    }

    res.send({ bonne_reponse: bonne_reponse })

})

app.post("/get_resultat_finish", (req, res) => {
    const { id } = req.body;

    let quiz = rooms[id];

    quiz.players.sort((a, b) => {
        // Trie par score décroissant
        if (b.score !== a.score) {
            return b.score - a.score;
        }

        // Si les scores sont égaux, trie par ordre alphabétique du nom d'utilisateur
        return a.username.localeCompare(b.username);
    });

    for(let i = 0; i <  quiz.players.length; i++){
        io.to(quiz.players[i].id).emit("endGame");
    }

    res.send({ resultat: quiz });
})

server.listen(port, () => console.log(`Listening on port ${port}`));