<?php 
session_start();
include_once('../../conexao.php');
//ENcapsulamento de dados do Formulario
$nome = $_POST['tipo'];
$carga_Horaria = $_POST['carga_Horaria'];
$regime = $_POST['regime'];
$fk_Servidor = 1;


// INSERT INTO 
$cad = "INSERT INTO escala ( fk_servidor, nome, carga_Horaria, regime) VALUES ('$fk_servidor', '$nome', '$carga_Horaria', '$regime');";
$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção
	if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Escala.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou Verifique Suas Informações!</p>";
	}
?>