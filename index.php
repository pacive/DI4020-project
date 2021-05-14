<?php 
  $init = 'index';
  require_once('template.php');
?>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!--CDN link to jQuery-->
  <script src="<https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js>"></script>
</head>
<body>
  

<!--Link to custom js (script.js)-->
 <script src="./media/js/modal.js"></script>


<!--MODAL -->
<div class='modal'>
      <div class='popup'>
          <span class="close" id='close'>&times;</span>

          <div>
            <h1>Welcome home!</h1>

            <p>Use the main menu is located in the top left corner to interract with your home, or click directly on the rooms in floor plan bellow.</p>
            <p>/^_^\</p>
          </div>
      </div>
  </div>
</body>


<div class="main-content">
<div id="image" class ="image">
  <img src="media/images/blueprint.png" alt="Layout sketch" usemap="#blueprint"/>
  <map id="blueprint" name="blueprint">
  </map>
  <div id="roompopup" class="roompopup">
    <h6 id="roomname">roomname</h6><span id="closepopup">X</span>
    <div id="devicelist"></div>
  </div>
</div>
</div>