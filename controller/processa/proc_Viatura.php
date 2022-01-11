<?php 
session_start();
include_once('../../conexao.php');
//Encapsulamento de dados do Formulario
$placa = $_POST['placa'];
$cor = $_POST['cor'];
$modelo = $_POST['modelo'];
$ano = $_POST['ano'];
$renavam = intval($_POST['renavam']);
$chassi = $_POST['chassi'];
$hodometro = intval($_POST['hodometro']);
$fk_Servidor = (int) 1;

//Fim  Encapsulamento de formulario

//encapsulamento de dados para sessão caso haja erro de cadastro 

$_SESSION['placa'] = $placa;
$_SESSION['cor'] = $cor;
$_SESSION['modelo'] = $modelo;
$_SESSION['ano'] = $ano;
$_SESSION['renavam'] = $renavam;
$_SESSION['chassi'] = $chassi;
$_SESSION['hodometro'] = $hodometro;

//Fim  Encapsulamento de sessao		

//inicio da inserção de dados no banco.

  		 $cad = "INSERT INTO viatura(
			   						fk_Servidor, 
   									placa, 
									cor, 
									modelo,
									ano, 
									renavam,
									chassi,
									hodametro_Total
   								)VALUES('$fk_Servidor',
   										'$placa',
										'$cor',
										'$modelo',
										'$ano',
										'$renavam',
										'$chassi',
										'$hodometro')";
									

$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção9
if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

 		header('Location: ../../view/cadastro/cad_Viatura.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
 									Contate O Suporte ou verifique Suas Informações!</p>";
 	}

   ?>


 