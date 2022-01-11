<?php
session_start();
include_once('../conexao.php');
echo "Olá, ".$_SESSION['nome_Servidor]'];
if (isset($_SESSION['nome_Servidor'])) {
	echo "Olá, ".$_SESSION['nome_Servidor]'];
}





?>