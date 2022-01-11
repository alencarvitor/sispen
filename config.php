<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
<script src="http://digitalbush.com/files/jquery/maskedinput/rc3/jquery.maskedinput.js" type="text/javascript"></script>
<?php
session_start();

function isOnline(){
     session_start();
       if(isset($_SESSION['id']))
           true;
      return false;
  }
?>

