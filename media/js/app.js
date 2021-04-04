const PROJECT_ROOT = "/~andalf20/project/";
const API_BASE = PROJECT_ROOT + "api/";

function openBar() {
  document.getElementById("sideBar").style.width = "20%";
}

function closeBar() {
  document.getElementById("sideBar").style.width = "0";
}

function showRoomPopUp(room) {
  let imageDiv = document.getElementById('image');

  let existing = document.getElementById('roompopup');
  existing !== null && imageDiv.removeChild(existing);

  let roomElem = imageDiv.appendChild(createRoomElement(room));

  let image = imageDiv.getElementsByTagName('img')[0];
  let roomCenter = calculateCenter(room.coordinates);

  roomElem.style.left = roomCenter[0] + (roomCenter[0] < (image.offsetWidth / 2) ? -roomElem.offsetWidth : 0) + 'px';
  roomElem.style.top = roomCenter[1] + (roomCenter[1] < (image.offsetHeight / 2) ? -roomElem.offsetHeight : 0) + 'px';
  roomElem.style.visibility = 'visible';
}

function createArea(room) {
  let area = document.createElement('area');
  area.shape = 'poly';
  area.coords = room.coordinates.toString();
  area.tabIndex = room.id;
  area.addEventListener('click', () => {
    showRoomPopUp(room);
  });
  document.getElementById('blueprint').appendChild(area);
}

function createRoomElement(room) {
  let div = document.createElement("div");
  div.id = 'roompopup';
  div.setAttribute('class', 'roompopup');
  let close = div.appendChild(document.createElement("span"));
  close.appendChild(document.createTextNode('X'));
  close.addEventListener('click', () => {
    document.body.removeChild(div);
  });
  let title = div.appendChild(document.createElement("h6"));
  title.appendChild(document.createTextNode(room.name));
  room.devices.forEach(device => {
    div.appendChild(createDeviceElement(device));
  });
  div.style.visibility ='hidden';
  return div;
}

function createDeviceElement(device) {
  let p = document.createElement("p");
  p.id = "device-" + device.id;
  let nameElem = p.appendChild(document.createElement("span"));
  nameElem.appendChild(document.createTextNode(device.name + ": "));
  if (device.typeName == 'Sensor') {
    let statusText = p.appendChild(document.createElement('span'));
    statusText.appendChild(document.createTextNode(device.status));
  } else {
    let toggle = p.appendChild(document.createElement("input"));
    toggle.type = 'checkbox';
    toggle.checked = getStatus(device.id) == 'ON';
    toggle.addEventListener('change', () => { setStatus(device.id, toggle.checked ? "ON" : "OFF"); });
  }
  return p;
}

function getAll(endpoint, params = "", callback) {
  let uri = API_BASE + endpoint + ".php" + params;
  doGet(uri, callback);
}

function doGet(uri, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", uri, true);
  doRequest(xhr, callback);
}

function doPost(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", uri, true);
  xhr.setRequestHeader("Content-Type", "application/json");
  doRequest(xhr, callback, body);
}

function doPut(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("PUT", uri, true);
  xhr.setRequestHeader("Content-Type", "application/json");
  doRequest(xhr, callback, body);
}

function doDelete(uri, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("DELETE", uri, true);
  doRequest(xhr, callback);
}

function doRequest(xhr, callback, body = null) {
  xhr.setRequestHeader("Accept", "application/json");
  xhr.send(body);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      callback(this.status, this.response);
    }
  }
}

function setStatus(deviceId, status) {
  let data = { "id": deviceId, "status": status };
  let uri = API_BASE + "status.php"
  doPost(uri, JSON.stringify(data), () => { return; });
}

function getStatus(deviceId) {
  return sessionStorage.getItem('device-' + deviceId);
}

function login() {
  let json = { username: document.getElementById("username").value, password: document.getElementById("password").value };
  let uri = PROJECT_ROOT + "/api/auth.php";
  
  doPost(uri, JSON.stringify(json), (status) => {
    if (status == 204) {
      window.location.assign(PROJECT_ROOT);
    } else {
      let p = document.getElementById("error");
      if (p === null || p === undefined) {
        p = document.createElement("p");
        p.id = "error";
        p.class = "error";
        p.appendChild(document.createTextNode("Invalid username and password"));
        document.getElementById("login").appendChild(p);
      }
    }
  });
}

function calculateCenter(coordinates) {
  let center = [];
  if (coordinates.length < 4) {
    for (let i = 0; i < 2; i++) {
      center[i] = coordinates.reduce((total, current) => {
        return total + current[i];
      }, 0) / coordinates.length;
    }
  } else {
    for (let i = 0; i < 2; i++) {
      min = coordinates.reduce((min, current) => {
        return current[i] < min ? current[i] : min;
      }, Number.MAX_VALUE);
      max = coordinates.reduce((max, current) => {
        return current[i] > max ? current[i] : max;
      }, Number.MIN_VALUE);
      center[i] = (min + max) / 2;
    }
  }
  return center;
}

function startSse() {
  var events = new EventSource(API_BASE + "events.php");
  events.onmessage = (event) => {
    console.log(event.data);
    let data = JSON.parse(event.data);
    sessionStorage.setItem('device-' + data.id, data.status);
    let p = document.getElementById("device-" + data.id);
    if (p !== null) {
      p.getElementsByTagName("input")[0].value = data.status;
    }
  }
}

function init() {
  getAll("rooms", "?includeDevices=true", (status, data) => {
    if (status == 200) {
      sessionStorage.setItem('rooms', data);
      let rooms = JSON.parse(data);
      rooms.forEach(room => {
        createArea(room);
      })
      startSse();
    }
  });
  let loginForm = document.getElementById("login");
  if (loginForm !== null) {
    loginForm.querySelector('#submit').addEventListener('click', login);
    document.addEventListener('keypress', function(ev) { ev.key == 'Enter' ? login() : null; });
  }
  document.getElementById('open').addEventListener('click', openBar);
  document.getElementById('close').addEventListener('click', closeBar);
}

window.addEventListener('load', () => {
  init();
});