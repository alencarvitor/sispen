<?php 
session_start();

include("functions.php");
   if(!isOnline())
      header(location: 'index.php');
  ?>