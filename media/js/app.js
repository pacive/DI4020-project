INIT = {
  /*
   * Common functions that should be executed on every page
   */
  common: function() {
    // Add event listeners to menu open and close buttons
    document.getElementById('open')?.addEventListener('click', openBar);
    document.getElementById('close')?.addEventListener('click', closeBar);    
  },

  /*
   * index
   */
  index: function() {
    // Get all rooms from the api
    getAll("rooms", "?includeDevices=true", (status, data) => {
      if (status == 200) {
        // Save the rooms in the browser
        sessionStorage.setItem('rooms', data);
        let rooms = JSON.parse(data);
        // Create an area for each room in the map 
        rooms.forEach(room => {
          createArea(room);
          createRoomMenu(room);
        });
        // Start listening for status updates
        startSse();
      }
    });

    // Resize the areas so they always match the image size if it's changed, e.g if changing to portrait view on a phone
    window.addEventListener('resize', () => {
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
  },

  /*
   * login
   */
  login: function() {      
    // Add event listeners to login form if at the login page
    let loginForm = document.getElementById("login");
    loginForm.querySelector('#submit').addEventListener('click', login);
    document.addEventListener('keypress', function(ev) { ev.key == 'Enter' ? login() : null; });
  }
}

/*
 * Call inititalization functions on page load
 */
window.addEventListener('load', () => {
  INIT.common();
  var initFunctions = document.body.dataset.init?.split(' ');
  initFunctions?.forEach(func => {
    if (INIT[func] !== undefined) {
      INIT[func]();
    }
  })  
});

/*
 * Start subscribing to events from server and updates element
 */
function startSse() {
  var events = new EventSource("api/events.php");
  events.onmessage = (event) => {
    console.log(event.data);
    let data = JSON.parse(event.data);
    sessionStorage.setItem('device-' + data.id, data.status);
    let p = document.getElementById("popup-device-" + data.id);
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
  document.getElementById("sideBar").classList.remove('closed');
  document.getElementById("sideBar").classList.add('open');
}
/*
 * close the menu
 */
function closeBar() {
  document.getElementById("sideBar").classList.remove('open');
  document.getElementById("sideBar").classList.add('closed');
}

/* create the rooms and devices in the sidemenu */
function createRoomMenu(room) {
  let roomDiv = document.createElement('div');
  let roomName = document.createElement('p');
  roomName.classList.add('roombtn');
  roomName.id = 'menu-room-' + room.id;
  roomName.innerHTML = room.name;
  let menuDiv = document.getElementById('menu');
  let devicesDiv = document.createElement('div');
  devicesDiv.classList.add('dropdown_content');
  devicesDiv.id = "dropdown-" + room.id;
  roomName.addEventListener('click', () => {
    open_closeDropdown(devicesDiv.id) });
  room.devices.forEach(device => {
    devicesDiv.appendChild(createDeviceElement(device));
  });
  roomDiv.appendChild(roomName);
  roomDiv.appendChild(devicesDiv);
  menuDiv.prepend(roomDiv);
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
  let roomCenter = calculateCenter(scaleCoordinates(room.coordinates));

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
    let deviceElem = createDeviceElement(device);
    deviceElem.id = 'popup-device-' + device.id;
    div.appendChild(deviceElem);
  });
}

/*
 * Create an element for a device to be added to the device list
 */
function createDeviceElement(device) {
  let p = document.createElement("p");
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


/* function for bringing out the devices when clicking on a room in menu */


function open_closeDropdown(id) {
  let dropdownDiv = document.getElementById(id);
  if (dropdownDiv.style.display == "none" || dropdownDiv.style.display == "") {
    dropdownDiv.style.display = "block";
  } else if (dropdownDiv.style.display == "block") {
    dropdownDiv.style.display = "none";
  };
}

/* getting all users, to edit them  */

function getUsernames() {
  getAll('users', '', (status, data) => {
    if (status === 200) {
      var users = JSON.parse(data);
      // users är nu en array av user-objekt
      let selectElement = document.getElementById('selectUsernames');
      users.forEach(user => {
        let option = document.createElement('option');
        option.text = user.name;
        option.value = user.id;
        selectElement.add(option);
      });
    }
  });
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
  let uri = "api/status.php"
  doPost(uri, JSON.stringify(data), () => { return; });
}

/*
 * Format the values from the login form as JSON and send to the auth endpoint
 */
function login() {
  let json = { username: document.getElementById("username").value, password: document.getElementById("password").value };
  let uri = "api/auth.php";
  
  doPost(uri, JSON.stringify(json), (status) => {
    if (status == 204) {
      var redirectUri = new URLSearchParams(window.location.search).get('redirectUri');
      if (redirectUri !== null && redirectUri !== '') {
        window.location.assign(redirectUri);
      } else {
        window.location.assign('index.php');
      }
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
  let uri = 'api/' + endpoint + '.php' + params;
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


/* delete user from form */

function deleteUser() {
  let theForm = document.getElementById('selectUsernames');
  let deleteUserId = theForm.value;
  doDelete('users.php?id=' + deleteUserId);
}
