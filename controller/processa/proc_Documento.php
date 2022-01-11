<?php 
session_start();
unset($_SESSION['msg']);

include("../../conexao.php");


$fk_Servidor = 0;
$fk_Servidor_Destinatario = preg_replace('/[^0-9]/', '', $_POST['cpf']);
$fk_Tipo_Documento = intval($_POST['tipo_Documento']);
$numero_Sei = $_POST['sei'];
$hora = date('d/m/Y \à\s H:i:s');


if(isset($_FILES['arquivo'])){
    $extensao = strtolower(substr($_FILES['arquivo']['name'], -4));  //pega a extensao do arquivo

    $novo_nome = md5(time()) . $extensao;  //define o nome do arquivo

    $diretorio = "../../banco/documento/doc-"; //define o diretorio para onde enviaremos o arquivo

    move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.$novo_nome); //efetua o upload

  
    $cad = "INSERT INTO documento (fk_Servidor, fk_Servidor_Destinatario, fk_Tipo_Documento, nome_Documento, numero_Sei, created)

    	 VALUES('$fk_Servidor','$fk_Servidor_Destinatario', '$fk_Tipo_Documento','$novo_nome', '$numero_Sei', '$hora')";
    
	$exe_cadastro = mysqli_query($conn,	$cad);

 }
  	if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Documento.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}
  ?>