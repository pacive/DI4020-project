const PROJECT_ROOT = "/~andalf20/project";

function login() {
  let json = {};
  json.username = document.getElementById("username").value;
  json.password = document.getElementById("password").value;

  let xhr = new XMLHttpRequest();
  xhr.open("POST", PROJECT_ROOT + "/api/auth.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(JSON.stringify(json));
  xhr.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 204) {
        window.location.assign(PROJECT_ROOT);
      } else {
        let elem = document.createElement("p");
        let text = document.createTextNode("Invalid username and password");
        elem.appendChild(text);
        document.getElementById("login").appendChild(elem);
      }
    }
  }
}

window.onload = function() {
  document.getElementById("submit").addEventListener("click", login);
  document.addEventListener("keypress", function(ev) { ev.key == "Enter" ? login() : null; });
}