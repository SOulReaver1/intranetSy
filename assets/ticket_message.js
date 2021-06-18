const io = require("socket.io-client");
const form = document.getElementById('ticket_message_form');
const api = form.dataset.api;
const token = document.getElementById('ticket_message__token').value;
const PHPSESSID = form.dataset.token;

const socket = io.connect(`ws://localhost:3000`, {
  auth: {
      token,
      PHPSESSID
  }
});

form.addEventListener('submit', e => {
  e.preventDefault();
  const message = document.getElementById('ticket_message_content').value;
  socket.emit('message', {
      url: window.location.pathname,
      message: message
  });
})
socket.on('message', data => {
  console.log(data);
});