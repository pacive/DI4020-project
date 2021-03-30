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
        if (confirm("Logged in! Click OK to proceed")) {
          window.location.assign(PROJECT_ROOT);
        }
      } else {
        alert(this.responseText);
      }
    }
  }
}

document.getElementById("submit").addEventListener("click", login);