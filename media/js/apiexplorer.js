const PROJECT_ROOT = "/~andalf20/project/";
const API_BASE = PROJECT_ROOT + "api/";

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

window.addEventListener('load', () => {
  document.getElementById("submit").addEventListener("click", apiExplorer);
  document.addEventListener("keypress", function(ev) { 
    if (ev.key == "Enter" && !ev.shiftKey) {
      apiExplorer();
    }
  });
});