<?php
session_start();
include_once("../../conexao.php");
$btnLogin = filter_input(INPUT_POST, 'btnLogin', FILTER_SANITIZE_STRING);
if($btnLogin){
	$usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
	$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
	$senha = md5($senha);
	//echo "$usuario - $senha";
	if((!empty($usuario)) AND (!empty($senha))){
		//Gerar a senha criptografa
		//echo password_hash($senha, PASSWORD_DEFAULT);
		//Pesquisar o usuário no BD
		$result_usuario = "SELECT * FROM servidor WHERE email_Servidor='$usuario' LIMIT 1";
		$resultado_usuario = mysqli_query($conn, $result_usuario);
		if($resultado_usuario){
			$row_usuario = mysqli_fetch_assoc($resultado_usuario);
			if(($senha == $row_usuario['senha_Servidor'])){
				$_SESSION['id'] = $row_usuario['id'];
			
				$_SESSION['nome_Servidor'] = $row_usuario['nome_Servidor'];

				header("Location: ../../../index.php");
				
				}else{
					$_SESSION['msg'] = "Login e senha incorreto!";
					header("Location: login1.php");
			}
		}
	}else{
		$_SESSION['msg'] = "Login e senha incorreto!";
		header("Location: login2.php");
	}
}else{
	$_SESSION['msg'] = "Página não encontrada";
	header("Location: login3.php");
}
