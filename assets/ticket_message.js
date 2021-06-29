const io = require("socket.io-client");
const moment = require('moment');
const form = document.getElementById('ticket_message_form');
const api = form.dataset.api;
const token = document.getElementById('ticket_message__token').value;
const PHPSESSID = form.dataset.token;
const listChat = document.getElementById('listChat');
const scrollDown = () => listChat.scrollTop = listChat.scrollHeight;
scrollDown();
const socket = io.connect(`ws://${api}`, {
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
  const currentUser = listChat.dataset.user;
  const created_at = moment(new Date()).format('DD-MM-YYYY H:m:s');
  const data = {
    'from_user': {
      'id': currentUser
    },
    'message': message,
    'created_at': created_at
  }
  addMessage(data);
  document.getElementById('ticket_message_content').value = "";
})
socket.on('message', data => addMessage(data));

const addMessage = data => {
  const currentUser = listChat.dataset.user;
  const li = document.createElement('li');
  li.classList.add(...['d-flex', 'mb-4', `${data.from_user.id == currentUser ? 'justify-content-end' : 'justify-content-start'}`, `${data.from_user.id == currentUser ? 'text-right' : 'text-left'}`])
  const col = document.createElement('div');
  col.classList.add(...['col-md-6', 'chat-body', 'white', 'p-3', 'ml-2', 'z-depth-1']);
  const header = document.createElement('header');
  header.classList.add('header');
  const img = document.createElement('img');
  img.src = 'https://mdbootstrap.com/img/Photos/Avatars/avatar-6.jpg';
  img.classList.add(...['avatar', 'rounded-circle', 'mr-2', 'z-depth-1']);
  img.alt = 'Avatar';
  const strong = document.createElement('strong');
  strong.classList.add('primary-front');
  strong.innerHTML = data.from_user.id == currentUser ? 'Moi' : data.from_user.username;
  const small = document.createElement('small');
  small.classList.add(...['pull-right', 'text-muted'])
  const i = document.createElement('i');
  i.classList.add(...['far', 'fa-clock']);
  const date = document.createElement('small');
  date.classList.add(...['ml-2'])
  date.innerHTML = data.created_at;
  const p = document.createElement('p');
  p.classList.add('mb-0');
  p.innerHTML = data.message;
  li.append(col);
  col.append(header);
  header.append(img);
  header.append(strong);
  header.append(small);
  small.append(i);
  small.append(date);
  col.append(p);
  listChat.append(li);
  scrollDown();
}