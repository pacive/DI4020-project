const PROJECT_ROOT = "/~andalf20/project/";
const API_BASE = PROJECT_ROOT + "api/";


/*
 * Call init() function after page has finished loading
 */
window.addEventListener('load', () => {
  init();
});

/*
 * Initialize the page by downloading data, adding event listeners,
 * and creating elements
 */
function init() {
  // Get all rooms from the api
  getAll("rooms", "?includeDevices=true", (status, data) => {
    if (status == 200) {
      // Save the rooms in the browser
      sessionStorage.setItem('rooms', data);
      let rooms = JSON.parse(data);
      // Create an area for each room in the map
      rooms.forEach(room => {
        createArea(room);
      });
      // Start listening for status updates
      startSse();
    }
  });

  // Resize the areas so they always match the image size if it's changed, e.g if changing to portrait view on a phone
  window.addEventListener('resize', (e) => {
    let mapElem = document.querySelector('.image map');
    while (first = mapElem.firstChild) {
      mapElem.removeChild(first);
    }
    let rooms = JSON.parse(sessionStorage.getItem('rooms'));
    rooms.forEach(room => {
      createArea(room);
    });
  });

  // Add event listener to popup close button
  document.getElementById('closepopup')?.addEventListener('click', () => {
    document.getElementById('roompopup').style.visibility = 'hidden';
  });  

  // Add event listeners to login form if at the login page
  let loginForm = document.getElementById("login");
  if (loginForm !== null) {
    loginForm.querySelector('#submit').addEventListener('click', login);
    document.addEventListener('keypress', function(ev) { ev.key == 'Enter' ? login() : null; });
  }

  // Add event listeners to menu open and close buttons
  document.getElementById('open')?.addEventListener('click', openBar);
  document.getElementById('close')?.addEventListener('click', closeBar);
}

/*
 * Start subscribing to events from server and updates element
 */
function startSse() {
  var events = new EventSource(API_BASE + "events.php");
  events.onmessage = (event) => {
    console.log(event.data);
    let data = JSON.parse(event.data);
    sessionStorage.setItem('device-' + data.id, data.status);
    let p = document.getElementById("device-" + data.id);
    if (p !== null) {
      p.querySelector('input').checked = data.status == 'ON';
    }
  }
}

  /*
   * DOM manipulation
   */

/*
 * Open the menu
 */
function openBar() {
  if (window.matchMedia('(orientation:portrait)').matches) {
    document.getElementById("sideBar").style.width = "40%";
  } else {
    document.getElementById("sideBar").style.width = "20%";
  }
}

/*
 * close the menu
 */
function closeBar() {
  document.getElementById("sideBar").style.width = "0%";
}

/*
 * Create an area for the image map with the coordinates of a room
 * and add a click event listener to display the room popup
 */
function createArea(room) {
  let area = document.createElement('area');
  area.shape = 'poly';
  area.coords = scaleCoordinates(room.coordinates).toString();
  area.tabIndex = room.id;
  area.addEventListener('click', (event) => {
    showRoomPopUp(room, event.offsetX, event.offsetY);
  });
  document.getElementById('blueprint').appendChild(area);
}

/*
 * Prepare the room popup and calculate the correct position on the image
 * to display it
 */
function showRoomPopUp(room, mouseX, mouseY) {
  let roomPopup = document.getElementById('roompopup');
  populateRoomPopup(room);

  let image = document.querySelector('.image img');
  let roomCenter = calculateCenter(room.coordinates);

  // If the popup is rotated the coordinates need to be adjusted a bit
  let offset = window.matchMedia('(orientation:portrait)').matches ? (roomPopup.offsetWidth - roomPopup.offsetHeight) / 2 : 0;
  
  let posX = roomCenter[0] < (image.offsetWidth / 2) ?
              Math.max(0 - offset, mouseX + offset - roomPopup.offsetWidth) :
              Math.min(mouseX - offset, image.offsetWidth - roomPopup.offsetWidth + offset);
  let posY = roomCenter[1] < (image.offsetHeight / 2) ?
              Math.max(0 + offset, mouseY - offset - roomPopup.offsetHeight) :
              Math.min(mouseY + offset, image.offsetHeight - roomPopup.offsetHeight - offset);

  roomPopup.style.left = posX + 'px';
  roomPopup.style.top = posY + 'px';
  roomPopup.style.visibility = 'visible';
}

/*
 * Fill in the roompopup div with room name and devices
 */
function populateRoomPopup(room) {
  let title = document.getElementById('roomname');
  title.textContent = room.name;

  let div = document.getElementById('devicelist');
  while (first = div.firstChild) {
    div.removeChild(first);
  }
  room.devices.forEach(device => {
    div.appendChild(createDeviceElement(device));
  });
}

/*
 * Create an element for a device to be added to the device list
 */
function createDeviceElement(device) {
  let p = document.createElement("p");
  p.id = "device-" + device.id;
  let nameElem = p.appendChild(document.createElement("span"));
  nameElem.textContent = device.name + ": ";
  if (device.typeName == 'Sensor') {
    let statusText = p.appendChild(document.createElement('span'));
    statusText.classList.add('status')
    statusText.textContent = device.status;
  } else {
    let toggle = p.appendChild(document.createElement("input"));
    toggle.classList.add('status')
    toggle.type = 'checkbox';
    toggle.checked = getStatus(device.id) == 'ON';
    toggle.addEventListener('change', () => { setStatus(device.id, toggle.checked ? "ON" : "OFF"); });
  }
  return p;
}

  /*
   * Math calculations
   */

/*
 * Transform the coordinates if the displayed image is not in the original size
 */
function scaleCoordinates(array) {
  var img = document.querySelector('.image img')
  var scaleFactor = img.offsetWidth / img.naturalWidth;
  var scaled = array.map(pair => {
    return pair.map(c => {
      return c * scaleFactor;
    })
  })
  return scaled;
}

/*
 * Calculate the center point of a set of coordinates
 */
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

  /*
   * Server communication
   */

/*
 * Send device update to server
 */
function setStatus(deviceId, status) {
  let data = { "id": deviceId, "status": status };
  let uri = API_BASE + "status.php"
  doPost(uri, JSON.stringify(data), () => { return; });
}

/*
 * Format the values from the login form as JSON and send to the auth endpoint
 */
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
        p.appendChild(document.createTextNode("Invalid username or password"));
        document.getElementById("login").appendChild(p);
      }
    }
  });
}

  /*
   * Ajax helper functions
   */

/*
 * Get all rooms/devices/roomtypes/devicetypes/users
 */
function getAll(endpoint, params = "", callback) {
  let uri = API_BASE + endpoint + ".php" + params;
  doGet(uri, callback);
}

/*
 * Make a GET request
 */
function doGet(uri, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", uri, true);
  doRequest(xhr, callback);
}

/*
 * Make a POST request
 */
function doPost(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", uri, true);
  xhr.setRequestHeader("Content-Type", "application/json");
  doRequest(xhr, callback, body);
}

/*
 * Make a PUT request
 */
function doPut(uri, body, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("PUT", uri, true);
  xhr.setRequestHeader("Content-Type", "application/json");
  doRequest(xhr, callback, body);
}

/*
 * Make a DELETE request
 */
function doDelete(uri, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open("DELETE", uri, true);
  doRequest(xhr, callback);
}

/*
 * Send request and execute callback function after response
 */
function doRequest(xhr, callback, body = null) {
  xhr.setRequestHeader("Accept", "application/json");
  xhr.send(body);
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      callback(this.status, this.response);
    }
  }
}

  /*
   * Other helper functions
   */

/*
 * Get status for a specified device from browser storage
 */
function getStatus(deviceId) {
  return sessionStorage.getItem('device-' + deviceId);
}
