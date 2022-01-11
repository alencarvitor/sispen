<?php 
session_start();

$fk_Estado = $_POST['fk_Estado'];
$fk_Regional = $_POST['fk_Regional'];
$fk_Cidade = $_POST['fk_Cidade'];
$fk_Unidade = $_POST['fk_Unidade'];
$bloco = $_POST['bloco'];
$numero = $_POST['numero'];
$andar = $_POST['andar'];
  

  $cad = "INSERT INTO cela(
							  fk_Estado,
							  fk_Regional, 
							  fk_Cidade, 
							  fk_Unidade, 
							  bloco, 
							  numero, 
							  andar) 
				VALUES(
							'$fk_Estado'
							'$fk_Regional',
							'$fk_Cidade',
							'$fk_Unidade',
							'$bloco',
							'$numero',
							'$andar')";

$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção9
	if( mysqli_insert_id($conn)){
		header('Location: ../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Cela.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}


							?>