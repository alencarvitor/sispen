<?php 
session_start();    
include_once('../../conexao.php');

//Encapsulamento de dados do Formulario
$nome = $_POST['nome'];

$_SESSION['nome'] = $nome;

$cad = "INSERT INTO cargo(nome) VALUES('$nome')";
$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção
	if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Cargo.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}
?>