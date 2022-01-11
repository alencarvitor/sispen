<?php 
session_start();
include_once('../../conexao.php');

//Encapsulamento de dados do Formulario
$fk_Servidor = 1;
$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$estado = intval($_POST['id_categoria']);
$cidade = intval($_POST['id_sub_categoria']);
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$fk_Diretor = 0;
$fk_Regional = 0;

 //Fim  Encapsulamento de formulario

//encapsulamento de dados para sessão caso haja erro de cadastro 

$_SESSION['nome'] = $nome;
$_SESSION['endereco'] = $endereco;
$_SESSION['id_categoria'] = $estado;
$_SESSION['id_sub_categoria'] = $cidade;
$_SESSION['email'] = $email;
$_SESSION['telefone'] = $telefone;
//Fim  Encapsulamento de sessao				 

//inicio da inserção de dados no banco.

$cad = " INSERT INTO unidade (fk_Servidor, fk_Servidor_Diretor, fk_Regional, fk_Estado, fk_Cidade, nome, endereco, email, telefone)
		       VALUES('$fk_Servidor', '$fk_Diretor', '$fk_Regional', '$estado', '$cidade', '$nome', '$endereco', '$email', '$telefone')";
$exe_cadastro = mysqli_query($conn,	$cad);									
//fim da execução query de inserção de dados
//inicio da validação de inserção
if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Unidade_Prisional.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}



 ?>