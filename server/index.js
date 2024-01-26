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

let room = [];

io.on('connection', (socket) => {

    socket.on("welcome", (message) => {
        console.log(message);
    })

    socket.on("createdRoom", ({ id_user, quiz, questions }) => {
        console.log(id_user, quiz, questions);
    })

});

server.listen(port, () => console.log(`Listening on port ${port}`));