const PROJECT_ROOT = "/~andalf20/project/";
const API_BASE = PROJECT_ROOT + "api/";

function apiExplorer() {
  let uri = API_BASE + document.getElementById("endpoint").value + ".php";
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

window.addEventListener('load', () => {
  document.getElementById("submit").addEventListener("click", apiExplorer);
  document.addEventListener("keypress", function(ev) { 
    if (ev.key == "Enter" && !ev.shiftKey) {
      apiExplorer();
    }
  });
});