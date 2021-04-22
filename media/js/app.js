INIT = {
  /*
   * Common functions that should be executed on every page
   */
  common: function() {
    // Add event listeners to menu open and close buttons
    document.getElementById('open')?.addEventListener('click', openBar);
    document.getElementById('close')?.addEventListener('click', closeBar);

    // Get all rooms from the api
    getAll("rooms", {includeDevices: true}).then(rooms => {
      // Save the rooms in the browser
      sessionStorage.setItem('rooms', JSON.stringify(rooms));
      // Create the rooms in the menu 
      rooms.forEach(room => {
        createRoomMenu(room);
      });  
      // Start listening for status updates
    }).then(startSse)
    .catch(error => console.log(error.statusText));
  },

  /*
   * index
   */
  index: function() {
    waitForRoomData().then(rooms => {
      rooms.forEach(room => { createArea(room) })
    });

    // Resize the areas so they always match the image size if it's changed, e.g if changing to portrait view on a phone
    window.addEventListener('resize', () => {
      let rooms = JSON.parse(sessionStorage.getItem('rooms'));
      rooms?.forEach(room => {
        let area = document.getElementById('room-area-' + room.id);
        area.coords = scaleCoordinates(room.coordinates);
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
    // Override default submit behaviour
    document.getElementById('login').addEventListener('submit', event => {
      event.preventDefault();
      login();
    });
  },

  /*
  * edit users 
  */
  editUsers: function() {
    getUsernames();
  },

  /*
  * add users
  */
  addUser: function() {
    document.getElementById('addUser').addEventListener('submit', event => {
      event.preventDefault(); //Förhindrar att det skickas på vanligt sätt
      submitForm('addUser');
    });
    },

    updateUser: function() {
      document.getElementById('updateUser').addEventListener('submit', event => {
        event.preventDefault(); //Förhindrar att det skickas på vanligt sätt
        submitForm('updateUser', 'put');
      });
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
    let popupDevice = document.getElementById("popup-device-" + data.id);
    let menuDevice = document.getElementById("menu-device-" + data.id);
    [popupDevice, menuDevice].forEach(deviceElem => {
      if (deviceElem !== null) {
        deviceElem.querySelector('input').checked = data.status == 'ON';
      }  
    });
  }
}

/*
 * Wait until the room data is stored in sessionStorage
 */
async function waitForRoomData() {
  return new Promise(resolve => {
    let rooms = sessionStorage.getItem('rooms');
    if (rooms !== null) {
     resolve(JSON.parse(rooms));
    } else {
      setTimeout(() => { resolve(waitForRoomData()); }, 500);
    }
  })
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
    let deviceElem = createDeviceElement(device);
    deviceElem.id = 'menu-device-' + device.id;
    devicesDiv.appendChild(deviceElem);
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
  area.id = 'room-area-' + room.id;
  area.addEventListener('click', event => {
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
  getAll('users').then(users => {
    let selectElement = document.getElementById('selectUsernames');
    selectElement.addEventListener('change', () => {
      let nameElement = document.getElementById('username');
      let adminElem = document.getElementById('isAdmin');
      getById('users', selectElement.value).then(user => {
        nameElement.value = user.name;
        adminElem.checked = user.admin;
      });
    });
    users.forEach(user => {
      let option = document.createElement('option');
      option.text = user.name;
      option.value = user.id;
      selectElement.add(option);
    });
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
  let data = { id: deviceId, status: status };
  let uri = "api/status.php"
  doPost(uri, data);
}

/*
 * Sends a form to the server as json
 */
async function submitForm(formId, method = null) {
  let form = document.getElementById(formId);
  let inputs = form.querySelectorAll('input, select, textarea');
  let data = {};

  inputs.forEach(input => {
    if (input.type == 'checkbox') {
      data[input.name] = input.checked;
    } else if (input.value != '' && input.name != '') {
      data[input.name] = input.value;
    }
  });

  if (method == null) {
    method = form.method;
  }

  switch (method) {
    case 'post':
      return doPost(form.action, data);
    case 'put':
      return doPut(form.action, data);
    default:
      return doGet(form.action, data);
  }
}

/*
 * Format the values from the login form as JSON and send to the auth endpoint
 */
function login() {
  submitForm('login').then(() => {
    var redirectUri = new URLSearchParams(window.location.search).get('redirectUri');
    if (redirectUri !== null && redirectUri !== '') {
      window.location.assign(redirectUri);
    } else {
      window.location.assign('index.php');
    }
  })
  .catch(() => {
    let p = document.getElementById("error");
    if (p === null || p === undefined) {
      p = document.createElement("p");
      p.id = "error";
      p.class = "error";
      document.getElementById("login").appendChild(p);
   }
   p.textContent = "Invalid username or password";
  });
}

  /*
   * Ajax helper functions
   */

/*
 * Get an entity from rooms/devices/roomtypes/devicetypes/users by its id
 */
async function getById(endpoint, id, params = {}) {
  let uri = 'api/' + endpoint + '.php';
  params.id = id;
  return doGet(uri, params).then(response => response.json());
}

/*
 * Get all rooms/devices/roomtypes/devicetypes/users
 */
async function getAll(endpoint, params = {}) {
  let uri = 'api/' + endpoint + '.php';
  return doGet(uri, params).then(response => response.json());
}

/*
 * Make a GET request
 */
async function doGet(uri, params = {}) {
  var req = { method: 'GET',
              headers: {'Accept': 'application/json'} }

  return doRequest(uri, req, params);
}

/*
 * Make a POST request
 */
async function doPost(uri, body, params = {}) {
  var req = { method: 'POST',
              headers: {'Content-Type': 'application/json',
                        'Accept': 'application/json'},
              body: JSON.stringify(body) }
  return doRequest(uri, req, params);
}

/*
 * Make a PUT request
 */
async function doPut(uri, body, params = {}) {
  var req = { method: 'PUT',
              headers: {'Content-Type': 'application/json',
                        'Accept': 'application/json'},
              body: JSON.stringify(body) }
  return doRequest(uri, req, params);
}

/*
 * Make a DELETE request
 */
async function doDelete(uri, params = {}) {
  var req = { method: 'DELETE' }
  return doRequest(uri, req, params);
}

/*
 * Make a request to the server
 */
async function doRequest(uri, req, params = {}) {
  if (Object.keys(params).length > 0) {
    let query = new URLSearchParams();
    Object.entries(params).forEach(entry => {
      query.append(entry[0], entry[1]);
    });
    uri += '?' + query.toString();
  }

  return fetch(uri, req).then(response => {
    if (response.ok) {
      return response;
    } else {
      throw response;
    }
  });
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
