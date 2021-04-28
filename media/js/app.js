var SmartHome = {
  INIT: {
    /*
    * Common functions that should be executed on every page
    */
    common: {
      initialize: function() {
        // Add event listeners to menu open and close buttons
        document.getElementById('open')?.addEventListener('click', openBar);
        document.getElementById('close')?.addEventListener('click', closeBar);
        let forms = document.getElementsByTagName('form');
        for (let i = 0; i < forms.length; i++) {
          forms[i].addEventListener('submit', e => e.preventDefault())
        }
      },
      start: function() {
        // Get all rooms from the api
        getAll("rooms", {includeDevices: true}).then(rooms => {
          // Save the rooms in the browser
          sessionStorage.setItem('rooms', JSON.stringify(rooms));
          // Create the rooms in the menu
          rooms.forEach(createRoomMenu);
          // Start listening for status updates
        }).then(startSse)
        .catch(error => console.log(error));
      }
    },

    /*
    * index
    */
    index: function() {
      SmartHome.apiListeners.add(['rooms'], 'createArea', createArea);

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
      document.getElementById('login').addEventListener('submit', login);
    },

    /*
    * add users
    */
    addUser: function() {
      document.getElementById('addUser').addEventListener('submit', () => {
        submitForm('addUser').then(newUser => {
          console.log(newUser);
          let para = document.getElementById('userAdded');
          para.innerHTML = newUser.name + " is added";
        });
      });
    },

    /*
    * edit users
    */
    updateUser: function() {
      getUsernames();
      document.getElementById('updateUser').addEventListener('submit', () => {
        submitForm('updateUser', 'put').then(user => {
          console.log(user);
          let para = document.getElementById('userUpdated');
          para.innerHTML = user.name + " is updated";
        });
      });
      document.getElementById('selectUsernames').addEventListener('change', event => {
        let id = event.target.value;
        let nameElement = document.getElementById('username');
        let adminElem = document.getElementById('isAdmin');
        if (id == 0) {
          nameElement.value = '';
          adminElem.checked = false;
        } else {
          getById('users', selectElement.value).then(user => {
            nameElement.value = user.name;
            adminElem.checked = user.admin;
          });
        }
      });  
    },

      /* add device */
    addDevice: function() {
      getRooms();
      getDeviceTypes();
      document.getElementById('addDevice').addEventListener('submit', () => {
        submitForm('addDevice').then(newDevice => {
          console.log(newDevice);
          let para = document.getElementById('deviceAdded');
          para.innerHTML = newDevice.name + " is added";
        });
      });
    },

   /*
    * edit devices
    */
    updateDevice: function() {
      getDevices();
      getDeviceTypes();
      getRooms();
      document.getElementById('updateDevice').addEventListener('submit', () => {
        submitForm('updateDevice', 'put').then(device => {
          console.log(device);
          let para = document.getElementById('deviceUpdated');
          para.innerHTML = device.name + " is updated";
        });
      });
      document.getElementById('selectDevices').addEventListener('change', event => {
        let id = event.target.value;
        let nameElement = document.getElementById('username');
        let typeElem = document.getElementById('getTypeIds');
        let roomElem = document.getElementById('getRooms');
        if (id == 0) {
          nameElement.value = '';
          typeElem.value = 0;
          roomElem.value = 0;
        } else {
          getById('devices', selectElement.value).then(device => {
            nameElement.value = device.name;
            typeElem.value = device.typeId;
            roomElem.value = device.roomId;
          });
        }
      });
    }
  },

  apiListeners: {
    listeners: new Map(),
    add: function(namespace, key, callback) {
      let ns = this.listeners;
      namespace.forEach(key => {
        ns.has(key) || ns.set(key, new Map());
        ns = ns.get(key);
      });
      ns.has('_listeners') || ns.set('_listeners', new Map());
      ns.get('_listeners').set(key, callback);
    },
    remove: function(key, ns = this.listeners) {
      ns.forEach((v, k) => {
        if (k == '_listeners') {
          v.delete(key);
        } else {
          this.remove(key, v);
        }
      });
    },
    notify: function(namespace, data) {
      if (Array.isArray(data)) {
        data.forEach(item => {
          this.notify(namespace, item);
        });
        return;
      }

      ['id', 'event'].forEach(key => {
        if (data.hasOwnProperty(key)) {
          namespace.push(data[key]);
        }
      });

      this.dig(namespace, listeners => {
        listeners.forEach(fn => {
          fn(data);
        });
      });
    },
    dig: function(namespace, fn = null) {
      let ns = this.listeners;
      for (let key of namespace) {
        if (!ns.has(key)) {
          return undefined;
        }
        ns = ns.get(key);
        if (fn && ns.has('_listeners')) {
          fn(ns.get('_listeners'));
        }
      }
      return ns.get('_listeners');
    }
  }
}

/*
 * Call inititalization functions on page load
 */
window.addEventListener('load', () => {
  SmartHome.INIT.common.initialize();
  var initFunctions = document.body.dataset.init?.split(' ');
  initFunctions?.forEach(func => {
    if (SmartHome.INIT[func] !== undefined) {
      SmartHome.INIT[func]();
    }
  });
  SmartHome.INIT.common.start();
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
    SmartHome.apiListeners.notify(['status'], data);
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
  let menuDiv = document.getElementById('menu');
  let roomDiv = menuDiv.appendChild(document.createElement('div'));
  let roomName = roomDiv.appendChild(document.createElement('p'));
  let devicesDiv = roomDiv.appendChild(document.createElement('div'));
  roomName.classList.add('roombtn');
  roomName.id = 'menu-room-' + room.id;
  roomName.textContent = room.name;
  devicesDiv.classList.add('dropdown_content');
  devicesDiv.id = "dropdown-" + room.id;
  roomName.addEventListener('click', () => {
    open_closeDropdown(devicesDiv) });
  room.devices.forEach(device => {
    let deviceElem = devicesDiv.appendChild(createDeviceElement(device));
    deviceElem.id = 'menu-device-' + device.id;
    SmartHome.apiListeners.add(['devices', device.id, 'updated'], deviceElem, data => {
      let newDiv = document.getElementById("dropdown-" + data.roomId);
      if (newDiv != deviceElem.parentElement) {
        newDiv.appendChild(deviceElem);
      }
    });
  });
}


/*
 * Create an area for the image map with the coordinates of a room
 * and add a click event listener to display the room popup
 */
function createArea(room) {
  let area = document.getElementById('room-area-' + room.id);
  if (area == null) {
    area = document.createElement('area');
    area.shape = 'poly';
    area.tabIndex = room.id;
    area.id = 'room-area-' + room.id;
    area.addEventListener('click', event => {
      showRoomPopUp(room, event.offsetX, event.offsetY);
    });
    document.getElementById('blueprint').appendChild(area);
  }
  area.coords = scaleCoordinates(room.coordinates).toString();
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
    SmartHome.apiListeners.remove(first);
    div.removeChild(first);
  }
  room.devices.forEach(device => {
    let deviceElem = div.appendChild(createDeviceElement(device));
    deviceElem.id = 'popup-device-' + device.id;
  });
}

/*
 * Create an element for a device to be added to the device list
 */
function createDeviceElement(device) {
  let p = document.createElement("p");
  let nameElem = p.appendChild(document.createElement("span"));
  let statusDisplay = p.appendChild(createStatusDisplayElement(device, device.typeName == 'Sensor'));
  
  nameElem.textContent = device.name + ": ";
  SmartHome.apiListeners.add(['devices', device.id, 'deleted'], p, () => {
    SmartHome.apiListeners.remove(nameElem);
    SmartHome.apiListeners.remove(statusDisplay);
    SmartHome.apiListeners.remove(p);
    p.remove();
  });
  SmartHome.apiListeners.add(['devices', device.id, 'updated'], nameElem, data => {
    nameElem.textContent = data.name + ": ";
  });
  return p;
}

function createStatusDisplayElement(device, readOnly = false) {
  let elem;
  if (readOnly) {
    elem = document.createElement('span');
    elem.textContent = device.status;
    SmartHome.apiListeners.add(['status', device.id], elem, status => {
      elem.textContent = status.status;
    });
  } else {
    elem = document.createElement("input");
    elem.type = 'checkbox';
    elem.checked = getStatus(device.id) == 'ON';
    elem.addEventListener('change', () => { setStatus(device.id, elem.checked ? "ON" : "OFF"); });
    SmartHome.apiListeners.add(['status', device.id], elem, status => {
      elem.checked = status.status == 'ON';
    });
  }
  elem.classList.add('status')
  return elem;
}

/* function for bringing out the devices when clicking on a room in menu */


function open_closeDropdown(dropdownDiv) {
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
    users.forEach(user => {
      let option = document.createElement('option');
      option.text = user.name;
      option.value = user.id;
      selectElement.add(option);
      SmartHome.apiListeners.add(['users', user.id, 'updated'], option, data => {
        option.text = data.name;
      });
      SmartHome.apiListeners.add(['users', user.id, 'deleted'], option, () => {
        SmartHome.apiListeners.remove(option);
        option.remove();
      });
    });
  });
}

/* getting all devices, to edit them  */

function getDevices() {
  getAll('devices').then(devices => {
    let selectElement = document.getElementById('selectDevices');
    devices.forEach(device => {
      let option = document.createElement('option');
      option.text = device.name + ' (' + device.roomName + ')';
      option.value = device.id;
      selectElement.add(option);
      SmartHome.apiListeners.add(['devices', device.id, 'updated'], option, data => {
        option.text = data.name + ' (' + data.roomName + ')';
      });
      SmartHome.apiListeners.add(['devices', device.id, 'deleted'], option, () => {
        SmartHome.apiListeners.remove(option);
        option.remove();
      });
    });
  });
}

function getDeviceTypes() {
  getAll('devicetypes').then(types => {
    let selectElement = document.getElementById('getTypeIds');
    types.forEach(type => {
      let option = document.createElement('option');
      option.text = type.name;
      option.value = type.id;
      selectElement.add(option);
    });
  });
}

function getRooms() {
  getAll('rooms').then(rooms => {
    let selectElement = document.getElementById('getRooms');
    rooms.forEach(room => {
      let option = document.createElement('option');
      option.text = room.name;
      option.value = room.id;
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

  switch (method || form.getAttribute('method').toLowerCase()) {
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
    window.location.assign(redirectUri || 'index.php');
  })
  .catch(() => {
    let p = document.getElementById("error");
    if (!p) {
      p = document.getElementById("login").appendChild(document.createElement("p"));
      p.id = "error";
      p.class = "error";
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
  return doGet(uri, params);
}

/*
 * Get all rooms/devices/roomtypes/devicetypes/users
 */
async function getAll(endpoint, params = {}) {
  let uri = 'api/' + endpoint + '.php';
  return doGet(uri, params)
}

/*
 * Make a GET request
 */
async function doGet(uri, params = {}) {
  var req = { method: 'GET' }

  return doRequest(uri, req, params);
}

/*
 * Make a POST request
 */
async function doPost(uri, body, params = {}) {
  var req = { method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify(body) }
  return doRequest(uri, req, params);
}

/*
 * Make a PUT request
 */
async function doPut(uri, body, params = {}) {
  var req = { method: 'PUT',
              headers: {'Content-Type': 'application/json'},
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
  let endpoint;
  if (match = uri.match(/api\/(.*)\.php/)) {
    endpoint = match[1];
  }
  if (Object.keys(params).length > 0) {
    let query = new URLSearchParams();
    Object.entries(params).forEach(entry => {
      query.append(entry[0], entry[1]);
    });
    uri += '?' + query.toString();
  }
  req.headers ||= {};
  req.headers['Accept'] = 'application/json';

  return fetch(uri, req).then(response => {
    return response.ok ? Promise.resolve(response) : Promise.reject(response);
  }).then(async response => {
    if (response.headers.get('Content-Type').includes('application/json')) {
      data = await response.json();
      if (endpoint) {
        SmartHome.apiListeners.notify([endpoint], data);
      }
      return data;
    } else {
      return response.text();
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
  let selectedUser = document.getElementById('selectUsernames');
  let deleteUserId = selectedUser.value;
  doDelete('users.php?id=' + deleteUserId);
}

/* delete device from form */

function deleteDevice() {
  let selectedDevice = document.getElementById('selectDevices');
  let deleteDeviceId = selectedDevice.value;
  doDelete('devices.php?id=' + deleteDeviceId);
}