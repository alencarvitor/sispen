<?php 
session_start();
include_once('../../conexao.php');

$fk_Servidor = 1;	/*= $_SESSION['id_Servidor'];*/ 
$fk_Tipo_Acesso	= $_POST['tipo_Acesso'];
$fk_Tipo_Documento	= $_POST['tipo_Documento'];
$nome	= $_POST['nome'];
$documento	= $_POST['documento'];
$data = date("Y-m-d h:i:s"); 
$cad = "INSERT INTO acesso ( 
							 fk_Servidor,
							 fk_Tipo_Acesso,
							 fk_Tipo_Documento, 
							 nome, documento,
							 hora_Entrada
							) 
					VALUES ( 
							'$fk_Servidor', 
							'$fk_Tipo_Acesso', 
							'$fk_Tipo_Documento', 
							'$nome', 
							'$documento', 
							now()
							)";

$resultado_cadastro = mysqli_query($conn, $cad);
if( mysqli_insert_id($conn)){
	header('Location: ../../../index.php');
	$_SESSION['msg'] = "<p style color:green;>Acesso Registrado com Sucesso!</p>";
} else{

	header('Location: ../../view/cadastro/cad_Acesso.php');
	$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Registrar Acesso, Contate O Suporte!</p>";
}
