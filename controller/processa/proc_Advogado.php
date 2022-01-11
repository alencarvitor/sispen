<?php 
session_start();
include_once('../../conexao.php');

//Encapsulamento de dados do Formulario

$nome_Advogado = $_POST['nome'];
$oab_Advogado =  preg_replace('/[^0-9]/', '', $_POST['oab']);
$uf_Oab_Advogado =  preg_replace('/[^0-9]/', '', $_POST['uf_Oab']);
$rg_Advogado =  preg_replace('/[^0-9]/', '', $_POST['rg']);
$orgao_Expeditor_Advogado = $_POST['orgao_Expeditor'];
$uf_Rg_Advogado =  preg_replace('/[^0-9]/', '', $_POST['uf_Rg']);
$cpf_Advogado =  preg_replace('/[^0-9]/', '', $_POST['cpf']);
$telefone_Pref_Advogado =  preg_replace('/[^0-9]/', '', $_POST['telefone_Preferencial']);
$telefone_Rec_Advogado =  preg_replace('/[^0-9]/', '', $_POST['telefone_Recado']);
$email_Advogado = $_POST['email'];
$senha_Advogado = $_POST['senha'];
$hora = date('d/m/Y \à\s H:i:s');
$observacao_Advogado = '';
//Fim  Encapsulamento de formulario
// conversão de senha para criptografia.
$senha_Advogado = md5($senha);
//fim criptografia.
//inicio da inserção de dados no banco.
$oab_Advogado = intval($oab_Advogado);
$uf_Oab_Advogado = intval($uf_Oab_Advogado);
$rg_Advogado = intval($rg_Advogado);
$uf_Rg_Advogado = intval($uf_Rg_Advogado);
$cpf_Advogado = intval($cpf_Advogado);
$telefone_Pref_Advogado = intval($telefone_Pref_Advogado);
$telefone_Rec_Advogado = intval($telefone_Rec_Advogado);


  $cad = "INSERT INTO advogado (
  nome_Advogado, 
  oab_Advogado, 
  uf_Oab_Advogado, 
  rg_Advogado, 
  uf_Rg_Advogado, 
  orgao_Expeditor_Advogado, 
  cpf_Advogado, 
  telefone_Pref_Advogado, 
  telefone_Rec_Advogado, 
  email_Advogado, 
  senha_Advogado, 
  observacao_Advogado, 
  created)

	VALUES(
	'$nome_Advogado', 
	'$oab_Advogado', 
	'$uf_Oab_Advogado',
	'$rg_Advogado',
	'$uf_Rg_Advogado', 
	'$orgao_Expeditor_Advogado', 
	'$cpf_Advogado', 
	'$telefone_Pref_Advogado', 
	'$telefone_Rec_Advogado', 
	'$email_Advogado', 
	'$senha_Advogado', 
	'$observacao_Advogado', 
	'$hora')";

	$exe_cadastro = mysqli_query($conn,	$cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção
	if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Advogado.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}
