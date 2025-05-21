<?php
// Arquivo de logout
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
