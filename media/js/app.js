const PROJECT_ROOT = "/~andalf20/project";

function createDevicesList() {
  getAll("devices", (devices) => {
    devices.forEach(device => {
      let para = document.createElement("p");
      let nameElem = document.createElement("span");
      let nameText = document.createTextNode(device.name + ": ");
      nameElem.appendChild(nameText);
      let statusElem = document.createElement("span");
      let statusText = document.createTextNode(device.status);
      statusElem.appendChild(statusText);
      para.appendChild(nameElem);
      para.appendChild(statusElem);
      para.id = "device-" + device.id;
      para.addEventListener("click", (ev) => { setStatus(device.id, statusElem.innerHTML == "ON" ? "OFF" : "ON"); });
      document.body.appendChild(para);
    });  
  });
}

function getAll(endpoint, callback) {
  let url = PROJECT_ROOT + "/api/" + endpoint + ".php";
  doGet(url, callback);
}

function doGet(url, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", url, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.send();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        callback(JSON.parse(this.response));
      } else {
        return null;
      }
    }
  }
}

function doPost(url, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Accept", "application/json");
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(body);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        callback(JSON.parse(this.response));
      } else if (this.status == 204) {
        callback();
      }
    }
  }
}

function setStatus(deviceId, status) {
  let data = { "id": deviceId, "status": status };
  let url = PROJECT_ROOT + "/api/status.php"
  doPost(url, JSON.stringify(data), () => {
    console.log("setStatus Callback (" + deviceId + ", " + status + ")");
    let para = document.getElementById("device-" + deviceId);
    para.lastChild.innerHTML = status;
  });
}

window.onload = () => {
  createDevicesList();
}