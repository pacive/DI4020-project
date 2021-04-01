const PROJECT_ROOT = "/~andalf20/project/";
const API_BASE = PROJECT_ROOT + "api/";

function openBar() {
  document.getElementById("sideBar").style.width = "20%";
}

function closeBar() {
  document.getElementById("sideBar").style.width = "0";
}

function createDevicesList(room = null) {
  let rooms = JSON.parse(sessionStorage.rooms)
  if (room !== null) {
    document.body.appendChild(createRoomElement(rooms[room]));
  } else {
    rooms.forEach(room => {
      document.body.appendChild(createRoomElement(room));
    });  
  }
}

function createRoomElement(room) {
  let div = document.createElement("div");
  let title = div.appendChild(document.createElement("h6"));
  title.appendChild(document.createTextNode(room.name));
  room.devices.forEach(device => {
    div.appendChild(createDeviceElement(device));
  });
  return div;
}

function createDeviceElement(device) {
  let p = document.createElement("p");
  p.id = "device-" + device.id;
  let nameElem = p.appendChild(document.createElement("span"));
  nameElem.appendChild(document.createTextNode(device.name + ": "));
  let button = p.appendChild(document.createElement("input"));
  button.type = "button"
  button.value = device.status;
  button.addEventListener("click", () => { setStatus(device.id, button.value == "ON" ? "OFF" : "ON"); });
  return p;
}

function getAll(endpoint, params = "", callback) {
  let uri = API_BASE + endpoint + ".php" + params;
  doGet(uri, callback);
}

function doGet(uri, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", uri, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.send();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      callback(this.status, this.response);
    }
  }
}

function doPost(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", uri, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(body);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      callback(this.status, this.response);
    }
  }
}

function doPut(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("PUT", uri, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(body);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      callback(this.status, this.response);
    }
  }
}

function doDelete(uri, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("DELETE", uri, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.send();
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

function startSse() {
  var events = new EventSource(API_BASE + "events.php");
  events.onmessage = (event) => {
    console.log(event.data);
    let data = JSON.parse(event.data);
    sessionStorage.setItem('device-' + data.id, data.status);
    let p = document.getElementById("device-" + data.id);
    p.getElementsByTagName("input")[0].value = data.status;
  }
}

function init() {
  getAll("rooms", "?includeDevices=true", (status, data) => {
    if (status = 200) {
      sessionStorage.setItem('rooms', data);
      startSse();
    }
  });
}

window.addEventListener('load' = () => {
  init();
  let loginForm = document.getElementById("login")
  if (loginForm !== null) {
    loginForm.getElementById('submit').addEventListener('click', login);
    document.addEventListener('keypress', function(ev) { ev.key == 'Enter' ? login() : null; });
  }
});