const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const port = 5000;

app.use(cors({
    origin: "*",
    methods: ["GET", "POST"],
}));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

const server = http.createServer(app);
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
        rooms[id_user] = {
            id: socket.id,
            quiz: quiz,
            code: code
        };
        console.log("Création d'une nouvelle room");

        io.to(socket.id).emit("room", { quiz, code });
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

server.listen(port, () => console.log(`Listening on port ${port}`));