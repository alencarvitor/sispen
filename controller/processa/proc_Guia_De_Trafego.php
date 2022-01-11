<?php 
session_start();
unset ($_SESSION);
include_once('../../conexao.php');
//ENcapsulamento de dados do Formulario
$fk_Servidor = 1 ;/*$_SESSION['id'];*/
$motorista = $_POST['motorista'];
$cpf = preg_replace('/[^0-9]/', '', $_POST['documento']); 
$viatura = intval($_POST['viatura']);
$km_Saida = intval($_POST['km_Saida']);
$destino = $_POST['destino'];
$km_Entrada = 0 ;
$hora_Entrada = '00:00:00';
$hora = date('d/m/Y \à\s H:i:s');

//Fim  Encapsulamento de formulario
//encapsulamento de dados para sessão caso haja erro de cadastro 

$_SESSION['id'] = $fk_Servidor;
$_SESSION['motorista'] = $motorista;
$_SESSION['cpf'] = $cpf;
$_SESSION['viatura'] = $viatura;
$_SESSION['km_Saida'] = $km_Saida;
$_SESSION['destino'] = $destino;

//Fim  Encapsulamento de sessao		
//inicio da inserção de dados no banco.
$cad =  "INSERT INTO guia_de_trafego (fk_Servidor, nome_Motorista, cpf_Motorista, fk_Viatura, km_Saida, km_Entrada, hora_Entrada, hora_Saida, destino)
		             VALUES('$fk_Servidor', '$motorista', '$cpf', '$viatura', '$km_Saida', '$km_Entrada', '$hora_Entrada', '$hora', '$destino')";
$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
//inicio da validação de inserção

if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Guia_De_trafego.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}
  ?>