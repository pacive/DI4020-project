var SmartHome = {
  config: {
    rooms: {},
    devices: {},
    users: {},
    devicetypes: {},
    roomtypes: {}
  },
  INIT: {
    /*
    * Common functions that should be executed on every page
    */
    common: {
      initialize: function() {
        // Add event listeners to menu open and close buttons
        document.getElementById('open').addEventListener('click', openBar);
        document.getElementById('close').addEventListener('click', closeBar);
        let forms = document.getElementsByTagName('form');
        for (let i = 0; i < forms.length; i++) {
          forms[i].addEventListener('submit', e => e.preventDefault(), true);
        }
        ['rooms', 'devices', 'users', 'devicetypes', 'roomtypes'].forEach(endpoint => {
          onGet(endpoint, endpoint + 'Cache', entity => {
            SmartHome.config[endpoint][entity.id] || (SmartHome.config[endpoint][entity.id] = {});
            Object.assign(SmartHome.config[endpoint][entity.id], entity);
          });  
        });
        onGet('status', 'statusCache', status => {
          SmartHome.config.devices[status.id] || (SmartHome.config.devices[status.id] = {});
          SmartHome.config.devices[status.id].status = status.status;
        });  
      },
      start: function() {
        // Get all rooms from the api
        getAll('rooms', {includeDevices: true}).then(rooms => {
          // Create the rooms in the menu
          rooms.forEach(room => {
            createRoomMenu(room);
            room.devices.forEach(device => {
              SmartHome.apiListeners.notify(['devices', device.id], device);
            });
          });
        }).then(() => {
          // Start listening for status updates
          startSse('api/events.php', event => {
            console.log(event.data);
            let data = JSON.parse(event.data);
            SmartHome.apiListeners.notify(['status'], data);        
          });
        }).catch(console.log);
      }
    },

    /*
    * index
    */
    index: function() {
      onGet('rooms', 'createArea', createArea);

      // Resize the areas so they always match the image size if it's changed, e.g if changing to portrait view on a phone
      window.addEventListener('resize', () => {
        Object.values(SmartHome.config.rooms).forEach(room => {
          let area = document.getElementById('room-area-' + room.id);
          area.coords = scaleCoordinates(room.coordinates);
        });
      });

      // Add event listener to popup close button
      document.getElementById('closepopup').addEventListener('click', () => {
        document.getElementById('roompopup').style.visibility = 'hidden';
      });
    },

    /*
    * login
    */
    login: function() {
      document.getElementById('login').addEventListener('submit', login);
      return { preventStart: true }
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
      document.getElementById('selectUsername').addEventListener('change', event => {
        let id = event.target.value;
        let nameElement = document.getElementById('username');
        let adminElem = document.getElementById('isAdmin');
        if (id == 0) {
          nameElement.value = '';
          adminElem.checked = false;
        } else {
          nameElement.value = SmartHome.config.users[id].name;
          adminElem.checked = SmartHome.config.users[id].admin;
        }
      });  
      document.getElementById('delete').addEventListener('click', deleteUser);
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
      document.getElementById('selectDevice').addEventListener('change', event => {
        let id = event.target.value;
        let nameElement = document.getElementById('name');
        let typeElem = document.getElementById('getTypeIds');
        let roomElem = document.getElementById('selectRoom');
        if (id == 0) {
          nameElement.value = '';
          typeElem.value = 0;
          roomElem.value = 0;
        } else {
          nameElement.value = SmartHome.config.devices[id].name;
          typeElem.value = SmartHome.config.devices[id].typeId;
          roomElem.value = SmartHome.config.devices[id].roomId;
        }
      });
      document.getElementById('delete').addEventListener('click', deleteDevice);
    },

    /* add room */      
    addRoom: function() {
      getRoomTypes();
      document.getElementById('addRoom').addEventListener('submit', () => {
        submitForm('addRoom').then(newRoom => {
          console.log(newRoom);
          let para = document.getElementById('roomAdded');
          para.innerHTML = newRoom.name + " is added";
          para.parentNode.appendChild(createElem('a', {href: 'draw_room.php?id=' + newRoom.id}, 'Add room to floorplan'));
        });
      });
    },

    /* add room type */
    addRoomType: function() {
      document.getElementById('newRoomTypeForm').addEventListener('submit', () => {
        submitForm('newRoomTypeForm');
        document.location="add_room.php"
      });
    },

    /* edit room */
    updateRoom: function() {
      getRooms();
      getRoomTypes(); 
      document.getElementById('updateRoom').addEventListener('submit', () => {
        submitForm('updateRoom', 'put').then(room => {
          console.log(room);
          let para = document.getElementById('roomUpdated');
          para.innerHTML = room.name + " is updated";
        });
      });
      document.getElementById('selectRoom').addEventListener('change', event => {
        let id = event.target.value;
        let nameElement = document.getElementById('roomName');
        let typeElem = document.getElementById('roomTypes');
        let drawRoomLink = document.getElementById('drawRoomLink');
        if (id == 0) {
          nameElement.value = '';
          typeElem.value = 0;
          drawRoomLink.href = '#';
          drawRoomLink.style.display = 'none';
        } else {
          nameElement.value = SmartHome.config.rooms[id].name;
          typeElem.value = SmartHome.config.rooms[id].typeId;
          drawRoomLink.href = 'draw_room.php?id=' + id;
          drawRoomLink.style.display = 'inline';
        }
      });
      document.getElementById('delete').addEventListener('click', deleteRoom);
    },

    /* edit room type*/
    updateRoomType: function() {
      getRoomTypes(); 
      document.getElementById('updateRoomType').addEventListener('submit', () => {
        submitForm('updateRoomType', 'put').then(roomType => {
          console.log(roomType);
          let para = document.getElementById('updateRoomType');
          para.innerHTML = roomType.name + " is updated";
        });
      });
      document.getElementById('roomTypes').addEventListener('change', event => {
        let id = event.target.value;
        let nameElement = document.getElementById('roomTypeName');
        if (id == 0) {
          nameElement.value = '';
        } else {
          nameElement.value = SmartHome.config.roomtypes[id].name;
        }
      });
      document.getElementById('delete').addEventListener('click', deleteRoomType);
    },

    /*
     * Statistics
     */
    statistics: function() {
      SmartHome.statistics.initialize();
      // Get data
      doGet('api/statistics.php').then(data => {
        SmartHome.statistics.updateChart(data.browsers);
        SmartHome.statistics.updateTable(data.ipAddresses);
      });

      document.getElementById('selectPeriod').addEventListener('change', e => {
        doGet('api/statistics.php', {period: e.target.value }).then(data => {
          SmartHome.statistics.updateChart(data.browsers);
          SmartHome.statistics.updateTable(data.ipAddresses);
        });  
      });
    },

    /*
     * Log
     */
    log: function() {
      let logWindow = document.getElementById('log');
      startSse('api/accesslog.php?initialSize=50', event => {  
        let data = JSON.parse(event.data);
        let line = logWindow.insertBefore(createElem('p'), logWindow.firstChild);
        line.textContent = `${data.time} ${data.requestType} ${data.page} (${data.responseCode}), user: ${data.user}`;
      });
      setTimeout(() => {
        onGet('status', 'log', data => {
          let device = SmartHome.config.devices[data.id];
          let line = logWindow.insertBefore(createElem('p'), logWindow.firstChild);
          line.textContent = `${data.time} ${device.name} (${device.roomName}) is ${data.status}`;
        });
      }, 2000);
    },
    /*
     * Draw room
     */
    drawRoom: function() {
      let roomId = new URLSearchParams(window.location.search).get('id');
      SmartHome.drawRoom.initialize();
      onGet(['rooms', parseInt(roomId)], 'drawRoom', room => {
        SmartHome.drawRoom.setCorners(room.coordinates);
      });
      document.getElementById('save').addEventListener('click', () => {
        let data = { id: roomId, coordinates: SmartHome.drawRoom.getCorners() };
        doPut('api/rooms.php', data).then(room => {
          document.getElementById('roomUpdated').textContent = room.name + ' updated!';
        });
      });
      document.getElementById('reset').addEventListener('click', () => {
        SmartHome.drawRoom.reset();
      });
      document.getElementById('undo').addEventListener('click', () => {
        SmartHome.drawRoom.undo();
      });
    },

    apiExplorer: function() {
      document.getElementById('apiexplorer').addEventListener('submit', apiExplorer);
      document.getElementById('reset').addEventListener('click', () => {
        document.getElementById('result').innerHTML = '';
      })
    }
  },

  /*
   * Pub/sub framework for api requests
   */
  apiListeners: {
    listeners: new Map(),
    /*
     * Add listener for specified namespace
     */
    add: function(namespace, key, callback) {
      let ns = this.listeners;
      namespace.forEach(key => {
        ns.has(key) || ns.set(key, new Map());
        ns = ns.get(key);
      });
      ns.has('_listeners') || ns.set('_listeners', new Map());
      ns.get('_listeners').set(key, callback);
    },

    /*
     * Remove listener from all namespaces
     */
    remove: function(key, ns = this.listeners) {
      ns.forEach((v, k) => {
        if (k == '_listeners') {
          v.delete(key);
        } else {
          this.remove(key, v);
        }
      });
    },

    /*
     * Notify all listeners in the specified namespace hierarchy
     */
    notify: function(namespace, data) {
      if (Array.isArray(data)) {
        data.forEach(item => {
          this.notify([...namespace], item);
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

    /*
     * Recurse down the namespace hierarchy to return all listeners at the last level
     * optionally apply the specified function to all listeners in the namespace path
     */
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
  },

  statistics: (function() {
    var canvas = document.getElementById('browser-chart'),
    ipTable = document.getElementById('ip-addresses'),
    //Chart config
    broserChartConfig = {
      type: 'doughnut',
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          },
          title: {
            display: true,
            text: 'Browsers'
          }
        }
      },
      data: {
        labels: [],
        datasets: [{
          label: 'Browsers',
          data: [],
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ]
        }]
      }
    },
    chart = null;

    var initialize = function() {
      chart = new Chart(canvas, broserChartConfig);
    }

    var updateChart = function(data) {
      chart.data.labels = [];
      chart.data.datasets[0].data = [];

      data.forEach(entry => {
        chart.data.labels.push(entry.userAgent);
        chart.data.datasets[0].data.push(parseInt(entry.visits));
      });

      chart.update();
    }

    var updateTable = function(data) {
      let tBody = ipTable.tBodies[0]
      while (firstChild = tBody.firstChild) {
        firstChild.remove();
      }
      data.forEach(entry => {
        let tr = tBody.appendChild(createElem('tr'));
        tr.appendChild(createElem('td', {}, entry.ipAddress));
        tr.appendChild(createElem('td', {}, entry.visits));
        tr.appendChild(createElem('td', {}, entry.lastVisit));
      });
    }

    return {
      initialize: initialize,
      updateChart: updateChart,
      updateTable: updateTable
    }
  }()),

  /*
   * Functions for drawing rooms
   */
  drawRoom: (function() {

    var corners = [],
    cornerHandles = [],
    canvas = null,
    ctx = null,
    draggedElement = null;

    /*
     * Initialize the drawing area, with the optionally provided
     * corner coordinates
     */
    var initialize = function(coordinates = []) {
      corners = coordinates;
      canvas = document.getElementById('draw-room');
      ctx = canvas.getContext('2d');
      
      // Adapt the canvas size to the image
      let background = new Image();
      background.addEventListener('load', () => {
        canvas.width = background.width;
        canvas.height = background.height;
        ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
        draw();
        ctx.save();
      });
      background.src = 'media/images/blueprint.png';

      // Add event listeners for mouse events
      canvas.addEventListener('mousedown', event => {
        draggedElement = getTargetCorner(event.layerX, event.layerY);
        if (draggedElement === null) {
          addCorner(event.layerX, event.layerY);
          draggedElement = corners.length - 1;
        }
        window.requestAnimationFrame(draw);
      });

      canvas.addEventListener('mousemove', event => {
        if (draggedElement != null) {
          corners[draggedElement] = snapCorner(event.layerX, event.layerY, draggedElement);
        }
      });

      canvas.addEventListener('mouseup', () => {
        draggedElement = null;
      });
    }

    var setCorners = function(coords) {
      corners = coords;
      draw();
    }

    /*
     * Add a corner to the room
     */
    var addCorner = function(x, y) {
      corners.push(snapCorner(x, y));
    }

    /*
     * Check if corner approximately lines up with previous corners and snap to
     * the axis
     */
    var snapCorner = function(x, y, excludeCorner = null) {
      let coords = [x, y];
      for (let i = 0; i < corners.length; i++) {
        if (i == excludeCorner) {
          continue;
        }
        for (let d = 0; d < 2; d++) {
          if (Math.abs(coords[d] - corners[i][d]) < 10) {
            coords[d] = corners[i][d];
          }  
        }
      }
      return coords;
    }

    /*
     * Draw the corners and path
     */
    var draw = function() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      cornerHandles = [];
      if (corners.length > 0) {
        let roomPath = new Path2D();
        roomPath.moveTo(...corners[0]);
        cornerHandles[0] = new Path2D();
        cornerHandles[0].arc(...corners[0], 5, 0, Math.PI * 2);
        ctx.stroke(cornerHandles[0]);
        for (let i = 1; i < corners.length; i++) {
          roomPath.lineTo(...corners[i]);
          cornerHandles[i] = new Path2D();
          cornerHandles[i].arc(...corners[i], 5, 0, Math.PI * 2);
          ctx.stroke(cornerHandles[i]);
        }
        roomPath.closePath();
        ctx.stroke(roomPath);
        ctx.fill(roomPath);
        if (draggedElement != null) {
          window.requestAnimationFrame(draw);
        }
      }
    }

    /*
     * Clear the drawing area
     */
    var reset = function() {
      corners = [];
      draw();
    }

    /*
     * Undo the last placed corner
     */
    var undo = function() {
      corners.pop();
      draw();
    }

    /*
     * Get the corner at the specified position
     */
    var getTargetCorner = function(x, y) {
      for (let i = cornerHandles.length; i--;) {
        if (ctx.isPointInPath(cornerHandles[i], x, y)) {
          return i;
        }
      }
      return null;
    }

    var getCorners = function() {
      return corners;
    }

    return {
      initialize: initialize,
      setCorners: setCorners,
      reset: reset,
      undo: undo,
      getCorners: getCorners
    };
  }())
}

/*
 * Call inititalization functions on page load
 */
window.addEventListener('load', () => {
  SmartHome.INIT.common.initialize();
  var initData = document.body.dataset.init;
  var initFunctions = initData ? initData.split(' ') : [];
  initFunctions.forEach(func => {
    if (SmartHome.INIT[func] !== undefined) {
      let config = SmartHome.INIT[func]();
      Object.assign(SmartHome.config, config);
    }
  });
  if (!SmartHome.config.preventStart) {
    SmartHome.INIT.common.start();
  }
});

/*
 * Start subscribing to events from server and updates element
 */
function startSse(uri, callback) {
  var events = new EventSource(uri);
  events.onmessage = callback
  events.onerror = e => {
    testConnection().then(status => {
      if (status == 403) {
        window.location.assign('login.php?redirectUri=' + window.location.pathname);
      }
    });
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
  let roomDiv = menuDiv.appendChild(createElem('div'));
  let roomName = roomDiv.appendChild(createElem('p', {class: 'roombtn'}, room.name));
  let devicesDiv = roomDiv.appendChild(createElem('div', {id: "dropdown-" + room.id, class: 'dropdown_content'}));
  roomName.addEventListener('click', () => {
    open_closeDropdown(devicesDiv) });
  onUpdate('rooms', room.id, roomName, data => {
    roomName.textContent = data.name;
  });
  onDelete('rooms', room.id, roomDiv, () => {
    roomDiv.remove();
  });
  if (room.devices) {
    room.devices.forEach(device => {
      let deviceElem = devicesDiv.appendChild(createDeviceElement(device));
      onUpdate('devices', device.id, deviceElem, data => {
        let newDiv = document.getElementById("dropdown-" + data.roomId);
        if (newDiv != deviceElem.parentElement) {
          newDiv.appendChild(deviceElem);
        }
      });
    });
  }
}

/* create option elements */
function createOptionElement(value, text) {
  let option = document.createElement('option');
  option.value = value;
  option.text = text;
  return option;
}

/*
 * Create an area for the image map with the coordinates of a room
 * and add a click event listener to display the room popup
 */
function createArea(room) {
  let area = document.getElementById('room-area-' + room.id);
  if (area == null) {
    if (room.coordinates.length == 0) { return; }
    area = document.getElementById('blueprint').appendChild(createElem('area', {id: 'room-area-' + room.id, shape: 'poly'}));
    area.addEventListener('click', event => {
      showRoomPopUp(room, event.clientX, event.clientY);
    });
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
  let imageRect = image.getBoundingClientRect();
  let roomCenter = calculateCenter(scaleCoordinates(room.coordinates));
  mouseX -= imageRect.left;
  mouseY -= imageRect.top;
  // If the popup is rotated the coordinates need to be adjusted a bit
  let offset = 0;
  if (window.matchMedia('(orientation:portrait)').matches) {
    let tmp = mouseX;
    mouseX = imageRect.height - mouseY;
    mouseY = tmp;
    offset = (roomPopup.offsetWidth - roomPopup.offsetHeight) / 2;
  }

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
    removeListener(first);
    first.remove();
  }
  room.devices.forEach(device => {
    div.appendChild(createDeviceElement(device));
  });
}

/*
 * Create an element for a device to be added to the device list
 */
function createDeviceElement(device, attrs = {}) {
  let p = createElem('p', attrs);
  let nameElem = p.appendChild(createElem('span', {}, device.name + ": "));
  let statusDisplay = p.appendChild(createStatusDisplayElement(device, device.typeName == 'Sensor'));
  
  onDelete('devices', device.id, p, () => {
    removeListener(nameElem);
    removeListener(statusDisplay);
    p.remove();
  });
  onUpdate('devices', device.id, nameElem, data => {
    nameElem.textContent = data.name + ": ";
  });
  return p;
}

function createStatusDisplayElement(device, readOnly = false) {
  let elem;
  if (readOnly) {
    elem = createElem('span', {class: 'status'}, device.status);
    onStatusUpdate(device.id, elem, status => {
      elem.textContent = status.status;
    });
  } else {
    elem = createToggle(device.id, getStatus(device.id) == 'ON');
  }
  return elem;
}

function createToggle(deviceId, checked = false) {
  let label = createElem('label', {class: 'toggle status'});
  let checkbox = label.appendChild(createElem("input", {type: 'checkbox'}));
  checkbox.checked = checked
  checkbox.addEventListener('change', () => { setStatus(deviceId, checkbox.checked ? "ON" : "OFF"); });
  onStatusUpdate(deviceId, checkbox, status => {
    checkbox.checked = status.status == 'ON';
  });
  label.appendChild(createElem('span', {class: 'slider'}));
  return label;  
}

/* function for bringing out the devices when clicking on a room in menu */

function open_closeDropdown(dropdownDiv) {
  let isOpen = dropdownDiv.offsetHeight > 0;
  for (openRoom of document.querySelectorAll('.dropdown_content')) {
    openRoom.style.height = '0px';
  }
  if (!isOpen) {
    dropdownDiv.style.height = dropdownDiv.scrollHeight + 'px';
  }
}

/* getting all users, to edit them  */

function getUsernames() {
  getAll('users').then(users => {
    let selectElement = document.getElementById('selectUsername');
    users.forEach(user => {
      var option = createOptionElement(user.id, user.name);
      selectElement.add(option);
      onUpdate('users', user.id, option, data => {
        option.text = data.name;
      });
      onDelete('users', user.id, option, () => {
        option.remove();
      });
    });
  });
}

/* getting all devices, to edit them  */

function getDevices() {
  getAll('devices').then(devices => {
    let selectElement = document.getElementById('selectDevice');
    devices.forEach(device => {
      let option = createOptionElement(device.id, device.name + ' (' + device.roomName + ')');
      selectElement.add(option);
      onUpdate('devices', device.id, option, data => {
        option.text = data.name + ' (' + data.roomName + ')';
      });
      onDelete('devices', device.id, option, () => {
        option.remove();
      });
    });
  });
}

function getDeviceTypes() {
  getAll('devicetypes').then(types => {
    let selectElement = document.getElementById('getTypeIds');
    types.forEach(type => {
      selectElement.add(createOptionElement(type.id, type.name));
    });
  });
}

/* get all rooms, for editing or adding devices */

function getRooms() {
  getAll('rooms').then(rooms => {
    let selectElement = document.getElementById('selectRoom');
    rooms.forEach(room => {
      let option = createOptionElement(room.id, room.name);
      selectElement.add(option);
      onUpdate('rooms', room.id, option, data => {
        option.text = data.name;
      });
      onDelete('rooms', room.id, option, () => {
        option.remove();
      });
    });
  });
}

/* get roomTypes, for adding new rooms */

function getRoomTypes() {
  getAll('roomtypes').then(types => {
    let selectElement = document.getElementById('roomTypes');
    types.forEach(type => {
      selectElement.add(createOptionElement(type.id, type.name));
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
      p = document.getElementById("login").appendChild(createElem('p', {id: 'error', class: 'error'}));
   }
   p.textContent = "Invalid username or password";
  });
}

async function testConnection() {
  var req = { method: 'HEAD', headers: { 'Connection': 'close' }}
  return fetch('api/events.php', req).then(response => response.status);
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
  req.headers || (req.headers = {});
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
  let device = SmartHome.config.devices[deviceId]
  return device ? device.status : undefined;
}

/*
 * Specify a callback to execute when an entity is retrieved from the server
 */
function onGet(endpoint, identifier, callback) {
  let ns = Array.isArray(endpoint) ? endpoint : [endpoint];
  SmartHome.apiListeners.add(ns, identifier, callback);
}

/*
 * Specify a callback to execute when an entity is updated after a PUT request
 */
function onUpdate(endpoint, id, element, callback) {
  SmartHome.apiListeners.add([endpoint, id, 'updated'], element, callback);
}

/*
 * Specify a callback to execute when an entity is deleted after a DELETE request
 */
function onDelete(endpoint, id, element, callback) {
  let fn = (data) => {
    SmartHome.apiListeners.remove(element);
    callback(data);
  }
  SmartHome.apiListeners.add([endpoint, id, 'deleted'], element, fn);
}

/*
 * Specify a callback to execute when a device status update is recieved
 */
function onStatusUpdate(deviceId, element, callback) {
  SmartHome.apiListeners.add(['status', deviceId], element, callback);
}

/*
 * Remove a listener
 */
function removeListener(element) {
  SmartHome.apiListeners.remove(element);
}

function createElem(type, attrs = {}, text = null) {
  let elem = document.createElement(type);
  Object.entries(attrs).forEach(([attr, value]) =>  { elem.setAttribute(attr, value); });
  text && (elem.textContent = text);
  return elem;
}

/* delete user from form */

function deleteUser() {
  let selectedUser = document.getElementById('selectUsername');
  let deleteUserId = selectedUser.value;
  let deleteUserName = selectedUser.options[selectedUser.selectedIndex].text;
  if (confirm('Are you sure you want to delete ' + deleteUserName + '?')) {
  doDelete('api/users.php?id=' + deleteUserId);
  let para = document.getElementById('userUpdated');
  para.innerHTML =  deleteUserName+ " is deleted";
  };
}

/* delete device from form */

function deleteDevice() {
  let selectedDevice = document.getElementById('selectDevice');
  let deleteDeviceId = selectedDevice.value;
  let deleteDeviceName = selectedDevice.options[selectedDevice.selectedIndex].text;
  if (confirm('Are you sure you want to delete ' + deleteDeviceName + '?')) {
  doDelete('api/devices.php?id=' + deleteDeviceId);
  let para = document.getElementById('deviceUpdated');
  para.innerHTML =  deleteDeviceName + " is deleted";
  };
}

/* delete room */
function deleteRoom() {
  let selectedRoom = document.getElementById('selectRoom');
  let deleteRoomId = selectedRoom.value;
  let deleteRoomName = selectedRoom.options[selectedRoom.selectedIndex].text;
  if (confirm('Are you sure you want to delete ' + deleteRoomName + '?')) {
    doDelete('api/rooms.php?id=' + deleteRoomId);
    let para = document.getElementById('roomUpdated');
    para.innerHTML =  deleteRoomName + " is deleted";
  };
}

/* delete room types */
function deleteRoomType() {
  let selectedRoomType = document.getElementById('roomTypes');
  let deleteRoomTypeId = selectedRoomType.value;
  let deleteRoomTypeName = selectedRoomType.options[selectedRoomType.selectedIndex].text;
  if (confirm('Are you sure you want to delete ' + deleteRoomTypeName + '?')) {
    doDelete('api/roomtypes.php?id=' + deleteRoomTypeId);
    let para = document.getElementById('roomTypeUpdated');
  let deleteRoomTypeName = selectedRoomType.options[selectedRoomType.selectedIndex].text;
    para.innerHTML = deleteRoomTypeName + " is deleted";
  };
}

/*
 * Function for Api explorer
 */
function apiExplorer() {
  let uri = 'api/' + document.getElementById("endpoint").value + ".php";
  let query = document.getElementById("query").value;
  let req = {
    method: document.getElementById("method").value,
    headers: {"Accept": "application/json",
              "Content-Type": "application/json" }
  };
  
  if (req.method == 'POST' || req.method == 'PUT') {
    req.body = document.getElementById("body").value
  }
  var start = new Date();
  fetch(uri + query, req).then(response => {
    var time = new Date() - start;
    document.getElementById("result").innerHTML = response.status + " " + response.statusText + 
    " (" + time + " ms)";
    if (response.headers.get("Content-Type") == "application/json") {      
      return response.json().then(data => {
        return JSON.stringify(data, null, 2);
      })
    } else {
      return response.text()
    }
  }).then(data => {
    document.getElementById("result").innerHTML += "\r\n\r\n" + data;
  })
}