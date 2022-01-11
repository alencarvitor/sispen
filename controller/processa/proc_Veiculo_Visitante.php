<?php 
session_start();
//Encapsulamento de dados do Formulario
$fk_Servidor = $_SESSION['id'];
$nome = $_POST['nome'];
$documento = $_POST['documento'];
$tipo_documento = $_POST['tipo_documento'];
$placa = $_POST['placa'];
$cor = $_POST['cor'];
$modelo = $_POST['modelo'];
//Fim  Encapsulamento de formulario

//encapsulamento de dados para sessão caso haja erro de cadastro 


$_SESSION['nome'] = $nome;
$_SESSION['documento'] = $documento;
$_SESSION['tipo_documento'] = $tipo_documento;
$_SESSION['placa'] = $placa;
$_SESSION['cor'] = $cor;
$_SESSION['modelo'] = $modelo;
//Fim  Encapsulamento de sessao		

//inicio da inserção de dados no banco.

  		 $cad = "INSERT INTO veiculo_visitante( 
  									fk_Servidor,
  									nome,
  									tipo_documento,   					
  									documento,
   									placa, 
									cor, 
									modelo,		
   								)VALUES( 
   										'$fk_Servidor'
   										'$nome',
   										'$tipo_documento',					
										'$documento',
   										'$placa'
										'$cor',
										'$modelo',
										)";

$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção9
	if( mysqli_insert_id($conn)){
		header('Location: ../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Veiculo_Visitante.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}

  ?>