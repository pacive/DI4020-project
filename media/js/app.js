const PROJECT_ROOT = "/~andalf20/project/";
const API_BASE = PROJECT_ROOT + "api/";

function createDevicesList() {
  JSON.parse(sessionStorage.rooms).forEach(room => {
    document.body.appendChild(createRoomElement(room));
  });  
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
      if (this.status == 200) {
        callback(this.response);
      } else {
        return null;
      }
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
      if (this.status == 200) {
        callback(this.response);
      } else if (this.status == 204) {
        callback();
      }
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
      if (this.status == 200) {
        callback(this.response);
      } else if (this.status == 204) {
        callback();
      }
    }
  }
}

function doDelete(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("DELETE", uri, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.send();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        callback(this.response);
      } else if (this.status == 204) {
        callback();
      }
    }
  }
}

function setStatus(deviceId, status) {
  let data = { "id": deviceId, "status": status };
  let uri = API_BASE + "status.php"
  doPost(uri, JSON.stringify(data), () => {
    console.log("setStatus Callback (" + deviceId + ", " + status + ")");
    let p = document.getElementById("device-" + deviceId);
    p.getElementsByTagName("input")[0].value = status;
  });
}

function apiExplorer() {
  let method = document.getElementById("method").value;
  let uri = API_BASE + document.getElementById("endpoint").value + ".php";
  let query = document.getElementById("query").value;
  let body = document.getElementById("body").value;

  let xhr = new XMLHttpRequest();
  xhr.open(method, uri + query, true);
  xhr.setRequestHeader("Accept", "application/json");
  var start = new Date();
  xhr.send(body);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      var time = new Date() - start;
      document.getElementById("result").innerHTML = this.status + " " + this.statusText + 
      " (" + time + " ms)"
      if (this.getResponseHeader("Content-Type") == "application/json") {
        document.getElementById("result").innerHTML += "\r\n\r\n" + JSON.stringify(JSON.parse(this.response), null, 2);
      } else {
        document.getElementById("result").innerHTML += "\r\n\r\n" + this.response;
      }
    }
  }
}

window.onload = () => {
  if (document.location.pathname == PROJECT_ROOT || document.location.pathname == PROJECT_ROOT + "index.html") {
    getAll("rooms", "?includeDevices=true", (data) => {
    sessionStorage.setItem('rooms', data);
      createDevicesList();
    });
  } else if (document.location.pathname == PROJECT_ROOT + "apiexplorer.html") {
    document.getElementById("submit").addEventListener("click", apiExplorer);
    document.addEventListener("keypress", function(ev) { 
      if (ev.key == "Enter" && !ev.shiftKey) {
        apiExplorer();
      }
    });
  }
}